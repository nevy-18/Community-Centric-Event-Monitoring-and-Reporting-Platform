import os
import uuid
import logging
from pathlib import Path
from flask import Blueprint, request, session, send_file
from backend.db import get_db, serialize_rows, serialize_row
from backend.auth_helpers import api_response, api_error
from datetime import datetime, date

logger = logging.getLogger(__name__)

PROJECT_ROOT = Path(__file__).resolve().parent.parent.parent

proposals_bp = Blueprint('proposals', __name__, url_prefix='/api/proposals')

def compute_proposal_badges(p):
    """Calculate dynamic due date status and approval timer."""
    today = date.today()
    
    # 1. Due Date Calculation
    due_date = p.get('due_date')
    if due_date:
        if isinstance(due_date, str):
            try:
                due_date_obj = datetime.strptime(due_date[:10], '%Y-%m-%d').date()
            except ValueError:
                due_date_obj = None
        elif isinstance(due_date, (datetime, date)):
            due_date_obj = due_date if isinstance(due_date, date) else due_date.date()
        else:
            due_date_obj = None

        if due_date_obj:
            diff_days = (due_date_obj - today).days
            if diff_days < 0 and p.get('status') != 'completed':
                p['due_badge'] = {'type': 'overdue', 'text': f'Overdue by {abs(diff_days)}d', 'label': f'Overdue by {abs(diff_days)}d', 'cls': 'badge overdue', 'days': diff_days}
            elif diff_days == 0 and p.get('status') != 'completed':
                p['due_badge'] = {'type': 'due_soon', 'text': 'Due Today', 'label': 'Due Today', 'cls': 'badge due-soon', 'days': 0}
            elif 1 <= diff_days <= 5 and p.get('status') != 'completed':
                p['due_badge'] = {'type': 'due_soon', 'text': f'Due in {diff_days}d', 'label': f'Due in {diff_days}d', 'cls': 'badge due-soon', 'days': diff_days}
            else:
                p['due_badge'] = {'type': 'on_track', 'text': f'Due in {diff_days}d', 'label': f'Due in {diff_days}d', 'cls': 'badge timer', 'days': diff_days}
        else:
            p['due_badge'] = None
    else:
        p['due_badge'] = None

    # 2. Approval Timer Calculation
    approved_at = p.get('approved_at')
    if approved_at:
        if isinstance(approved_at, str):
            try:
                app_time = datetime.fromisoformat(approved_at)
            except ValueError:
                app_time = None
        else:
            app_time = approved_at

        if app_time:
            now = datetime.now()
            delta = now - app_time
            hours = int(delta.total_seconds() // 3600)
            days = delta.days
            if days > 0:
                timer_txt = f"Approved {days}d ago"
            elif hours > 0:
                timer_txt = f"Approved {hours}h ago"
            else:
                mins = max(1, int(delta.total_seconds() // 60))
                timer_txt = f"Approved {mins}m ago"
            p['approval_timer'] = {'label': timer_txt, 'text': timer_txt, 'cls': 'badge timer'}
        else:
            p['approval_timer'] = None
    else:
        p['approval_timer'] = None

    # 3. Threshold status
    budget_amt = float(p.get('budget_amount') or 0)
    budget_limit = float(p.get('budget_threshold') or 25000)
    p['budget_threshold_exceeded'] = budget_amt > budget_limit
    return p

@proposals_bp.route('/metrics', methods=['GET'])
def get_metrics():
    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("SELECT COUNT(*) AS c FROM events")
            total = cur.fetchone()['c']

            cur.execute("SELECT COUNT(*) AS c FROM events WHERE status = 'pending_approval'")
            pending = cur.fetchone()['c']

            cur.execute("SELECT COUNT(*) AS c FROM events WHERE status IN ('approved', 'published', 'ongoing', 'completed')")
            approved = cur.fetchone()['c']

            cur.execute("SELECT COUNT(*) AS c FROM events WHERE status = 'draft'")
            revision = cur.fetchone()['c']

    return api_response({
        'total_proposals': total,
        'pending_review': pending,
        'approved': approved,
        'needs_revision': revision
    })

@proposals_bp.route('', methods=['GET'])
def list_proposals():
    status_filter = request.args.get('status', 'all')
    chapter_filter = request.args.get('chapter_id', 'all')
    search = request.args.get('search', '').strip()

    sql = """
        SELECT e.id, e.chapter_id, e.title, e.description, e.event_type, e.ytr_program, e.target_audience,
               e.start_datetime, e.end_datetime, e.venue, e.status,
               e.created_at, e.review_note, e.target_turnout, e.budget_amount,
               e.budget_threshold, e.due_date, e.approved_at, e.attachment_path, e.drive_link,
               c.chapter_name, c.chapter_code,
               u.full_name AS requester_name
        FROM events e
        LEFT JOIN chapters c ON c.id = e.chapter_id
        LEFT JOIN users u ON u.id = e.created_by
        WHERE 1=1
    """
    params = []

    if status_filter == 'pending':
        sql += " AND e.status = 'pending_approval'"
    elif status_filter == 'approved':
        sql += " AND e.status IN ('approved', 'published', 'ongoing', 'completed')"
    elif status_filter == 'revision':
        sql += " AND e.status = 'draft'"

    if chapter_filter and chapter_filter != 'all':
        try:
            ch_int = int(chapter_filter)
            sql += " AND e.chapter_id = %s"
            params.append(ch_int)
        except (ValueError, TypeError):
            pass

    if search:
        sql += " AND (e.title LIKE %s OR e.venue LIKE %s OR c.chapter_name LIKE %s)"
        s = f"%{search}%"
        params.extend([s, s, s])

    sql += " ORDER BY e.created_at DESC, e.id DESC"

    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute(sql, tuple(params))
            proposals = cur.fetchall()

    serialized = serialize_rows(proposals)
    enriched = [compute_proposal_badges(p) for p in serialized]

    return api_response(enriched)

@proposals_bp.route('/<int:proposal_id>', methods=['GET'])
def get_proposal(proposal_id):
    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("""
                SELECT e.*, c.chapter_name, c.chapter_code, c.region,
                       u.full_name AS requester_name, u.email AS requester_email, u.position AS requester_position,
                       r.full_name AS reviewer_name, r.position AS reviewer_position
                FROM events e
                LEFT JOIN chapters c ON c.id = e.chapter_id
                LEFT JOIN users u ON u.id = e.created_by
                LEFT JOIN users r ON r.id = e.reviewed_by
                WHERE e.id = %s
                LIMIT 1
            """, (proposal_id,))
            proposal = cur.fetchone()

    if not proposal:
        return api_error('Proposal not found.', 404)

    serialized = serialize_row(proposal)
    enriched = compute_proposal_badges(serialized)

    return api_response(enriched)

@proposals_bp.route('/<int:proposal_id>/attachment', methods=['GET'])
def download_proposal_attachment(proposal_id):
    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("SELECT id, title, attachment_path FROM events WHERE id = %s", (proposal_id,))
            ev = cur.fetchone()
    if not ev or not ev.get('attachment_path'):
        return api_error('No proposal attachment found for this event.', 404)

    rel_path = ev['attachment_path'].lstrip('/\\')
    file_path = os.path.join(PROJECT_ROOT, rel_path)
    if not os.path.exists(file_path):
        return api_error('Attached proposal file is missing on server.', 404)

    return send_file(file_path, as_attachment=True)

@proposals_bp.route('', methods=['POST'])
def create_proposal():
    try:
        data = request.get_json(silent=True) or request.form.to_dict() or {}
        title = data.get('title', '').strip()
        raw_ch = data.get('chapter_id') or session.get('chapter_id')
        try:
            chapter_id = int(raw_ch) if raw_ch is not None and str(raw_ch).isdigit() else None
        except (ValueError, TypeError):
            chapter_id = None
        start_date = data.get('start_date') or data.get('start_datetime')
        end_date = data.get('end_date') or data.get('end_datetime') or start_date
        venue = data.get('venue', '').strip()
        description = data.get('description', '').strip()
        event_type = (data.get('event_type') or 'chapter').strip()
        ytr_program = data.get('ytr_program') or data.get('program') or 'Lead up (Youth Leadership)'
        target_audience = (data.get('target_audience') or 'Open for All').strip() or 'Open for All'

        try:
            target_turnout = int(data.get('target_turnout') or 100)
        except (ValueError, TypeError):
            target_turnout = 100

        try:
            budget_amount = float(data.get('budget_amount') or 0.0)
        except (ValueError, TypeError):
            budget_amount = 0.0

        try:
            budget_threshold = float(data.get('budget_threshold') or 25000.0)
        except (ValueError, TypeError):
            budget_threshold = 25000.0

        due_date = data.get('due_date') or start_date
        if due_date:
            due_date = str(due_date).split('T')[0].split(' ')[0]

        created_by = session.get('user_id')

        if not title or not start_date or not venue or not description:
            return api_error('Title, start date, venue, and description are required.', 400)

        # Process uploaded attachment file if present
        attachment_path = None
        file = request.files.get('attachment') or request.files.get('proposal_file') or request.files.get('proposal_doc')
        if file and file.filename != '':
            ext = os.path.splitext(file.filename)[1].lower()
            unique_fname = f"proposal_{uuid.uuid4().hex[:10]}{ext}"
            target_dir = os.path.join(PROJECT_ROOT, 'assets', 'uploads', 'proposals')
            os.makedirs(target_dir, exist_ok=True)
            save_path = os.path.join(target_dir, unique_fname)
            file.save(save_path)
            attachment_path = f"assets/uploads/proposals/{unique_fname}"
        elif data.get('attachment_path'):
            attachment_path = data.get('attachment_path')

        drive_link = (data.get('drive_link') or data.get('proposal_drive_link') or '').strip() or None

        def _format_dt(val, default_time='09:00:00'):
            if not val:
                return None
            val_str = str(val).replace('T', ' ')
            if len(val_str) == 10:
                return f"{val_str} {default_time}"
            return val_str

        formatted_start = _format_dt(start_date, '09:00:00')
        formatted_end = _format_dt(end_date, '17:00:00')

        with get_db() as conn:
            with conn.cursor() as cur:
                if not created_by:
                    cur.execute("SELECT id, chapter_id FROM users WHERE account_status = 'active' LIMIT 1")
                    first_user = cur.fetchone()
                    if first_user:
                        created_by = first_user['id']
                        if not chapter_id:
                            chapter_id = first_user['chapter_id']

                if not chapter_id:
                    cur.execute("SELECT id FROM chapters LIMIT 1")
                    ch = cur.fetchone()
                    chapter_id = ch['id'] if ch else None

                cur.execute("""
                    INSERT INTO events (chapter_id, title, description, event_type, ytr_program, target_audience, start_datetime, end_datetime, venue, target_turnout, budget_amount, budget_threshold, due_date, attachment_path, drive_link, status, created_by, created_at)
                    VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 'pending_approval', %s, NOW())
                """, (
                    chapter_id,
                    title,
                    description,
                    event_type,
                    ytr_program,
                    target_audience,
                    formatted_start,
                    formatted_end,
                    venue,
                    target_turnout,
                    budget_amount,
                    budget_threshold,
                    due_date,
                    attachment_path,
                    drive_link,
                    created_by
                ))
                new_id = cur.lastrowid

        return api_response({'event_id': new_id, 'attachment_path': attachment_path, 'drive_link': drive_link}, 'Event proposal submitted successfully and added to review queue!', 201)
    except Exception as e:
        logger.exception("Failed to create proposal: %s", str(e))
        return api_error(f"Failed to submit proposal: {str(e)}", 500)

@proposals_bp.route('/<int:proposal_id>/review', methods=['POST'])
def review_proposal(proposal_id):
    data = request.get_json(silent=True) or request.form.to_dict() or {}
    status = data.get('status')
    note = data.get('review_note', '').strip()
    reviewer_id = session.get('user_id')

    if status not in ['approved', 'draft', 'rejected']:
        return api_error('Valid status (approved, draft, rejected) is required.', 400)

    with get_db() as conn:
        with conn.cursor() as cur:
            if status == 'approved':
                cur.execute("""
                    UPDATE events
                    SET status = %s,
                        review_note = %s,
                        reviewed_by = %s,
                        approved_at = NOW(),
                        updated_at = NOW()
                    WHERE id = %s
                """, (status, note, reviewer_id, proposal_id))
            else:
                cur.execute("""
                    UPDATE events
                    SET status = %s,
                        review_note = %s,
                        reviewed_by = %s,
                        updated_at = NOW()
                    WHERE id = %s
                """, (status, note, reviewer_id, proposal_id))

    return api_response({}, f'Event proposal status updated to {status}.')
