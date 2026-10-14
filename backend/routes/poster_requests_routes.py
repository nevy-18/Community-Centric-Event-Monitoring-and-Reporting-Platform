from flask import Blueprint, request
from datetime import datetime, date
from backend.db import get_db, serialize_rows, serialize_row
from backend.auth_helpers import api_response, api_error

poster_requests_bp = Blueprint('poster_requests', __name__, url_prefix='/api/poster-requests')

def compute_due_and_timer(r):
    deadline = r.get('requested_deadline')
    submitted_at = r.get('submitted_at')
    updated_at = r.get('updated_at')
    status = r.get('status')
    
    today = date.today()

    # Due date badge
    if deadline:
        if isinstance(deadline, str):
            try:
                deadline_d = datetime.strptime(deadline[:10], '%Y-%m-%d').date()
            except Exception:
                deadline_d = None
        elif isinstance(deadline, datetime):
            deadline_d = deadline.date()
        elif isinstance(deadline, date):
            deadline_d = deadline
        else:
            deadline_d = None

        if deadline_d:
            diff_days = (deadline_d - today).days
            if diff_days < 0:
                r['due_badge'] = {'label': 'OVERDUE', 'cls': 'badge overdue'}
            elif diff_days <= 3:
                r['due_badge'] = {'label': f'DUE IN {diff_days}D', 'cls': 'badge due-soon'}
            else:
                r['due_badge'] = {'label': f'Due {deadline_d.strftime("%b %d")}', 'cls': 'badge'}
        else:
            r['due_badge'] = None
    else:
        r['due_badge'] = None

    # Approval timer
    if status in ('in_progress', 'completed'):
        ref_time = updated_at or submitted_at
        if ref_time:
            if isinstance(ref_time, str):
                try:
                    ref_dt = datetime.fromisoformat(ref_time.replace('Z', ''))
                except Exception:
                    ref_dt = None
            else:
                ref_dt = ref_time

            if ref_dt:
                delta = datetime.now() - ref_dt
                hours = int(delta.total_seconds() // 3600)
                days = delta.days
                if days > 0:
                    timer_str = f"Approved {days}d ago"
                elif hours > 0:
                    timer_str = f"Approved {hours}h ago"
                else:
                    mins = max(1, int(delta.total_seconds() // 60))
                    timer_str = f"Approved {mins}m ago"
                r['approval_timer'] = {'label': timer_str, 'cls': 'badge timer'}
            else:
                r['approval_timer'] = None
        else:
            r['approval_timer'] = None
    else:
        r['approval_timer'] = None

    return r

@poster_requests_bp.route('/metrics', methods=['GET'])
def get_metrics():
    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("SELECT COUNT(*) AS c FROM events WHERE status IN ('approved', 'published', 'ongoing')")
            active = cur.fetchone()['c']

            cur.execute("SELECT COUNT(*) AS c FROM poster_requests WHERE status IN ('submitted', 'under_review')")
            pending = cur.fetchone()['c']

            cur.execute("SELECT COUNT(*) AS c FROM poster_requests WHERE status = 'in_progress'")
            awaiting = cur.fetchone()['c']

            cur.execute("SELECT COUNT(*) AS c FROM poster_requests WHERE status = 'completed'")
            concluded = cur.fetchone()['c']

    return api_response({
        'total_active_events': active,
        'pending_poster_request': pending,
        'awaiting_report': awaiting,
        'concluded_events': concluded
    })

@poster_requests_bp.route('', methods=['GET'])
def list_poster_requests():
    tab = request.args.get('tab', 'accepted')

    sql = """
        SELECT pr.*, c.chapter_name, c.chapter_code,
               u.full_name AS requester_name,
               a.full_name AS assignee_name
        FROM poster_requests pr
        LEFT JOIN chapters c ON c.id = pr.chapter_id
        LEFT JOIN users u ON u.id = pr.requested_by
        LEFT JOIN users a ON a.id = pr.assigned_to
        WHERE 1=1
    """
    params = []

    if tab == 'accepted':
        sql += " AND pr.status IN ('in_progress', 'completed')"
    elif tab == 'queue':
        sql += " AND pr.status IN ('submitted', 'under_review', 'revision_requested')"

    sql += " ORDER BY pr.submitted_at DESC"

    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute(sql, tuple(params))
            requests = cur.fetchall()

    enriched = [compute_due_and_timer(r) for r in requests]
    return api_response(serialize_rows(enriched))

@poster_requests_bp.route('/<int:request_id>/approve', methods=['POST'])
def approve_poster_request(request_id):
    data = request.get_json(silent=True) or {}
    review_note = data.get('review_note', 'Approved by National Admin')
    reviewer_id = data.get('reviewed_by', 1)

    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("SELECT id, status FROM poster_requests WHERE id = %s", (request_id,))
            req = cur.fetchone()
            if not req:
                return api_error('Poster request not found.', 404)

            cur.execute("""
                UPDATE poster_requests
                SET status = 'in_progress',
                    reviewed_by = %s,
                    review_note = %s,
                    updated_at = NOW()
                WHERE id = %s
            """, (reviewer_id, review_note, request_id))
            conn.commit()

    return api_response({'message': 'Poster request approved and moved to in_progress queue.'})

@poster_requests_bp.route('/<int:request_id>/reject', methods=['POST'])
def reject_poster_request(request_id):
    data = request.get_json(silent=True) or {}
    review_note = data.get('review_note', 'Poster request declined by reviewer')
    reviewer_id = data.get('reviewed_by', 1)

    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("SELECT id, status FROM poster_requests WHERE id = %s", (request_id,))
            req = cur.fetchone()
            if not req:
                return api_error('Poster request not found.', 404)

            cur.execute("""
                UPDATE poster_requests
                SET status = 'rejected',
                    reviewed_by = %s,
                    review_note = %s,
                    updated_at = NOW()
                WHERE id = %s
            """, (reviewer_id, review_note, request_id))
            conn.commit()

    return api_response({'message': 'Poster request rejected.'})

@poster_requests_bp.route('', methods=['POST'])
def create_poster_request():
    data = request.get_json(silent=True) or {}
    chapter_id = data.get('chapter_id')
    event_title = data.get('event_title')
    event_date = data.get('event_date')
    venue = data.get('venue', '')
    theme = data.get('theme_or_concept', '')
    deadline = data.get('requested_deadline')
    requested_by = data.get('requested_by', 1)
    notes = data.get('additional_notes', '')

    if not chapter_id or not event_title or not event_date:
        return api_error('chapter_id, event_title, and event_date are required.', 400)

    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("""
                INSERT INTO poster_requests
                (chapter_id, requested_by, event_title, event_date, venue, theme_or_concept, requested_deadline, additional_notes, status, submitted_at)
                VALUES (%s, %s, %s, %s, %s, %s, %s, %s, 'submitted', NOW())
            """, (chapter_id, requested_by, event_title, event_date, venue, theme, deadline, notes))
            conn.commit()
            new_id = cur.lastrowid

    return api_response({'message': 'Poster request submitted successfully.', 'request_id': new_id}, 201)
