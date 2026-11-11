from flask import Blueprint, jsonify, request, session
from datetime import datetime, date
from backend.db import get_db, serialize_rows, serialize_row
from backend.auth_helpers import api_response, api_error

chapters_bp = Blueprint('chapters', __name__, url_prefix='/api/chapters')

def compute_due_badge(due_date):
    if not due_date:
        return None
    today = date.today()
    if isinstance(due_date, str):
        try:
            d = datetime.strptime(due_date[:10], '%Y-%m-%d').date()
        except Exception:
            return None
    elif isinstance(due_date, datetime):
        d = due_date.date()
    elif isinstance(due_date, date):
        d = due_date
    else:
        return None

    diff = (d - today).days
    if diff < 0:
        return {'label': 'OVERDUE', 'cls': 'badge overdue'}
    elif diff <= 3:
        return {'label': f'DUE IN {diff}D', 'cls': 'badge due-soon'}
    return {'label': f'Due {d.strftime("%b %d")}', 'cls': 'badge'}

def compute_approval_timer(approved_at):
    if not approved_at:
        return None
    if isinstance(approved_at, str):
        try:
            dt = datetime.fromisoformat(approved_at.replace('Z', ''))
        except Exception:
            return None
    else:
        dt = approved_at
    delta = datetime.now() - dt
    days = delta.days
    hours = int(delta.total_seconds() // 3600)
    if days > 0:
        return {'label': f'Approved {days}d ago', 'cls': 'badge timer'}
    elif hours > 0:
        return {'label': f'Approved {hours}h ago', 'cls': 'badge timer'}
    mins = max(1, int(delta.total_seconds() // 60))
    return {'label': f'Approved {mins}m ago', 'cls': 'badge timer'}

@chapters_bp.route('', methods=['GET'])
def list_chapters():
    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("""
                SELECT id, chapter_code, chapter_name, generation_name, region, province, city_municipality, status,
                       regional_director_id, regional_director_name
                FROM chapters
                WHERE status = 'active' OR status IS NULL OR status = ''
                ORDER BY chapter_name ASC
            """)
            chapters = cur.fetchall()

    return api_response(serialize_rows(chapters))

@chapters_bp.route('', methods=['POST'])
def create_chapter():
    """Register a new partner chapter. Automatically available in all dropdowns."""
    data = request.get_json(silent=True) or request.form.to_dict() or {}
    chapter_name = data.get('chapter_name', '').strip()
    chapter_code = data.get('chapter_code', '').strip().upper()
    region = data.get('region', '').strip()
    province = data.get('province', '').strip()
    city_municipality = data.get('city_municipality', '').strip()
    generation_name = data.get('generation_name', 'Gen One').strip()

    if not chapter_name:
        return api_error('Chapter name is required.', 400)

    if not chapter_code:
        # Generate code from chapter name, e.g. "Rizal Chapter" -> "YTR-RIZ"
        clean = chapter_name.replace('Chapter', '').replace('chapter', '').strip()
        parts = clean.split()
        prefix = ''.join(p[:3] for p in parts).upper()[:6] if parts else 'CHP'
        chapter_code = f"YTR-{prefix}"

    with get_db() as conn:
        with conn.cursor() as cur:
            # Check unique chapter_name or chapter_code
            cur.execute("""
                SELECT id, chapter_name, chapter_code FROM chapters
                WHERE LOWER(chapter_name) = %s OR chapter_code = %s
                LIMIT 1
            """, (chapter_name.lower(), chapter_code))
            existing = cur.fetchone()
            if existing:
                return api_error(f"A chapter with name '{chapter_name}' or code '{chapter_code}' already exists.", 409)

            cur.execute("""
                INSERT INTO chapters (chapter_code, chapter_name, generation_name, region, province, city_municipality, status, created_at, updated_at)
                VALUES (%s, %s, %s, %s, %s, %s, 'active', NOW(), NOW())
            """, (chapter_code, chapter_name, generation_name, region or None, province or None, city_municipality or None))
            new_id = cur.lastrowid

            cur.execute("""
                SELECT id, chapter_code, chapter_name, generation_name, region, province, city_municipality, status,
                       regional_director_id, regional_director_name
                FROM chapters
                WHERE id = %s
            """, (new_id,))
            new_ch = cur.fetchone()

    return api_response(serialize_row(new_ch), f"Partner Chapter '{chapter_name}' added successfully.", 201)

AUTHORIZED_CEO_POSITIONS = [
    'Chief Executive Operation',
    'Chief Executive Officer',
    'CEO',
]

@chapters_bp.route('/<int:chapter_id>/regional-director', methods=['PUT', 'POST'])
def assign_regional_director(chapter_id):
    """Assign or update the Regional Director for a specific chapter. Restricted to Chief Executive Officer."""
    reviewer_id = request.headers.get('X-User-Id') or session.get('user_id')
    reviewer_email = request.headers.get('X-User-Email') or session.get('user_email')

    # Authority Check: Only Chief Executive Officer can assign Regional Director for chapters
    if reviewer_id or reviewer_email:
        with get_db() as conn:
            with conn.cursor() as cur:
                cur.execute("""
                    SELECT id, email, role, position FROM users 
                    WHERE id = %s OR LOWER(email) = %s 
                    LIMIT 1
                """, (reviewer_id, str(reviewer_email or '').lower()))
                rev = cur.fetchone()
                if not rev:
                    return api_error("Access Denied: Requester account not found.", 403)

                pos = str(rev.get('position') or '').strip()
                email = str(rev.get('email') or '').strip().lower()
                if pos not in AUTHORIZED_CEO_POSITIONS and email != 'admin@ytr.org':
                    return api_error("Access Denied: Only the Chief Executive Officer can assign Regional Directors for chapters.", 403)

    data = request.get_json(silent=True) or request.form.to_dict() or {}
    director_name = data.get('regional_director_name', '').strip()
    director_id = data.get('regional_director_id')

    if director_id:
        try:
            director_id = int(director_id)
        except (ValueError, TypeError):
            director_id = None

    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("SELECT id, chapter_name FROM chapters WHERE id = %s", (chapter_id,))
            ch = cur.fetchone()
            if not ch:
                return api_error('Chapter not found.', 404)

            # If director_id is provided but director_name is empty, fetch user's full_name
            if director_id and not director_name:
                cur.execute("SELECT full_name FROM users WHERE id = %s", (director_id,))
                u = cur.fetchone()
                if u:
                    director_name = u['full_name']

            cur.execute("""
                UPDATE chapters
                SET regional_director_id = %s,
                    regional_director_name = %s
                WHERE id = %s
            """, (director_id, director_name, chapter_id))

            cur.execute("SELECT * FROM chapters WHERE id = %s", (chapter_id,))
            updated_ch = cur.fetchone()

    return api_response(serialize_row(updated_ch), f'Regional Director for {ch["chapter_name"]} assigned successfully.')


@chapters_bp.route('/<int:chapter_id>', methods=['GET'])
def get_chapter(chapter_id):
    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("SELECT * FROM chapters WHERE id = %s", (chapter_id,))
            chapter = cur.fetchone()

    if not chapter:
        return api_error('Chapter not found.', 404)

    return api_response(serialize_row(chapter))

@chapters_bp.route('/<int:chapter_id>/workspace', methods=['GET'])
def get_chapter_workspace(chapter_id):
    with get_db() as conn:
        with conn.cursor() as cur:
            # 1. Chapter details
            cur.execute("SELECT * FROM chapters WHERE id = %s", (chapter_id,))
            chapter = cur.fetchone()
            if not chapter:
                return api_error('Chapter not found.', 404)

            # 2. Events belonging to this chapter with report info
            cur.execute("""
                SELECT e.*, 
                       er.id AS report_id, er.total_participants, er.first_timers, 
                       er.existing_members, er.youth_leaders_count, er.signed_by_name,
                       er.submitted_at AS report_submitted_at
                FROM events e
                LEFT JOIN event_reports er ON er.event_id = e.id
                WHERE e.chapter_id = %s
                ORDER BY e.created_at DESC
            """, (chapter_id,))
            events = cur.fetchall()

            # 3. Poster requests for this chapter
            cur.execute("""
                SELECT pr.*, u.full_name AS requester_name
                FROM poster_requests pr
                LEFT JOIN users u ON u.id = pr.requested_by
                WHERE pr.chapter_id = %s
                ORDER BY pr.submitted_at DESC
            """, (chapter_id,))
            posters = cur.fetchall()

            # 4. Announcements for this chapter
            cur.execute("""
                SELECT a.*, u.full_name AS author_name
                FROM announcements a
                LEFT JOIN users u ON u.id = a.created_by
                WHERE a.is_published = 1
                  AND (a.target_audience IN ('everyone', 'chapter_admins') OR a.target_chapter_id = %s)
                ORDER BY a.published_at DESC
                LIMIT 5
            """, (chapter_id,))
            announcements = cur.fetchall()

            # 5. Metrics
            active_events = sum(1 for e in events if e['status'] in ('approved', 'published', 'ongoing'))
            pending_proposals = sum(1 for e in events if e['status'] in ('pending_approval', 'draft'))
            submitted_reports = sum(1 for e in events if e.get('report_id'))
            pending_reports = sum(1 for e in events if e['status'] in ('approved', 'published', 'completed') and not e.get('report_id'))
            posters_in_progress = sum(1 for p in posters if p['status'] in ('submitted', 'under_review', 'in_progress'))
            
            # Turnout calculation
            target_sum = sum(e.get('target_turnout') or 0 for e in events if e.get('target_turnout'))
            actual_sum = sum(e.get('total_participants') or 0 for e in events if e.get('total_participants'))
            turnout_pct = round((actual_sum / target_sum * 100), 1) if target_sum > 0 else 100.0

    # Enrich events
    enriched_events = []
    due_soon_count = 0
    overdue_count = 0
    for ev in events:
        d = dict(ev)
        due_info = compute_due_badge(d.get('due_date'))
        d['due_badge'] = due_info
        d['approval_timer'] = compute_approval_timer(d.get('approved_at'))
        if due_info:
            if 'overdue' in due_info.get('cls', ''):
                overdue_count += 1
            elif 'due-soon' in due_info.get('cls', ''):
                due_soon_count += 1
        enriched_events.append(d)

    return api_response({
        'chapter': serialize_row(chapter),
        'metrics': {
            'active_events': active_events,
            'pending_proposals': pending_proposals,
            'pending_reports': pending_reports,
            'submitted_reports': submitted_reports,
            'due_soon_reports': due_soon_count,
            'overdue_reports': overdue_count,
            'posters_in_progress': posters_in_progress,
            'turnout_achievement_pct': turnout_pct,
            'total_target_turnout': target_sum,
            'total_actual_turnout': actual_sum
        },
        'events': serialize_rows(enriched_events),
        'poster_requests': serialize_rows(posters),
        'announcements': serialize_rows(announcements)
    })

@chapters_bp.route('/<int:chapter_id>/unreported-events', methods=['GET'])
def get_unreported_events(chapter_id):
    """Returns approved events for this chapter that still need a post-event report"""
    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("""
                SELECT e.id, e.title, e.event_type, e.ytr_program, e.start_datetime, e.end_datetime,
                       e.venue, e.target_turnout, e.budget_amount, e.due_date
                FROM events e
                LEFT JOIN event_reports er ON er.event_id = e.id
                WHERE e.chapter_id = %s
                  AND e.status IN ('approved', 'published', 'ongoing', 'completed')
                  AND er.id IS NULL
                ORDER BY e.start_datetime ASC
            """, (chapter_id,))
            events = cur.fetchall()

    return api_response(serialize_rows(events))


@chapters_bp.route('/<int:chapter_id>/remind', methods=['POST'])
def send_chapter_reminder(chapter_id):
    """
    Send an official post-event report reminder directly to Chapter Admin(s).
    Inserts a targeted notification into the `notifications` table and records an audit log.
    """
    data = request.get_json(silent=True) or request.form.to_dict() or {}
    message = (data.get('message') or '').strip()
    subject = (data.get('subject') or '').strip()
    event_id = data.get('event_id')

    if not message:
        return api_error("Reminder message cannot be empty.", 400)

    try:
        event_id = int(event_id) if event_id else None
    except (ValueError, TypeError):
        event_id = None

    sender_id = request.headers.get('X-User-Id') or session.get('user_id')
    sender_email = request.headers.get('X-User-Email') or session.get('user_email')

    with get_db() as conn:
        with conn.cursor() as cur:
            # 1. Verify chapter exists
            cur.execute("SELECT id, chapter_name, chapter_code FROM chapters WHERE id = %s", (chapter_id,))
            chapter = cur.fetchone()
            if not chapter:
                return api_error("Chapter not found.", 404)

            # 2. Resolve sender_id
            valid_sender_id = None
            if sender_id:
                try:
                    valid_sender_id = int(sender_id)
                except (ValueError, TypeError):
                    valid_sender_id = None

            if not valid_sender_id and sender_email:
                cur.execute("SELECT id FROM users WHERE LOWER(email) = %s LIMIT 1", (str(sender_email).lower(),))
                u = cur.fetchone()
                if u:
                    valid_sender_id = u['id']

            # 3. If event_id provided, fetch event title
            event_title = None
            if event_id:
                cur.execute("SELECT id, title FROM events WHERE id = %s", (event_id,))
                ev = cur.fetchone()
                if ev:
                    event_title = ev['title']

            if not subject:
                if event_title:
                    subject = f"Action Required: Submit Post-Event Report - {event_title}"
                else:
                    subject = f"Action Required: Post-Event Documentation ({chapter['chapter_name']})"

            # 4. Find all chapter admins / coordinators for this chapter
            cur.execute("""
                SELECT id, full_name, email, role, chapter_id
                FROM users
                WHERE chapter_id = %s
                  AND role IN ('chapter_admin', 'coordinator')
                  AND account_status = 'active'
            """, (chapter_id,))
            recipients = cur.fetchall()

            if not recipients:
                cur.execute("""
                    SELECT id, full_name, email, role, chapter_id
                    FROM users
                    WHERE chapter_id = %s
                      AND account_status = 'active'
                """, (chapter_id,))
                recipients = cur.fetchall()

            # 5. Insert notification for each recipient
            notif_ids = []
            for r in recipients:
                cur.execute("""
                    INSERT INTO notifications (user_id, created_by, title, message, related_type, related_id, target_url, is_read, created_at)
                    VALUES (%s, %s, %s, %s, 'event_report', %s, 'ChapterReport.php', 0, NOW())
                """, (r['id'], valid_sender_id, subject, message, event_id))
                notif_ids.append(cur.lastrowid)

            # 6. Insert audit log
            try:
                log_details = f"Report reminder sent to {len(recipients)} admin(s) for chapter '{chapter['chapter_name']}'"
                if event_title:
                    log_details += f" regarding event '{event_title}'"
                cur.execute("""
                    INSERT INTO audit_logs (user_id, action, entity_type, entity_id, details, created_at)
                    VALUES (%s, 'send_report_reminder', 'chapter', %s, %s, NOW())
                """, (valid_sender_id, chapter_id, log_details))
            except Exception as log_err:
                print(f"[WARN] Failed to insert audit log for reminder: {log_err}")

    recipient_info = [{'id': r['id'], 'name': r['full_name'], 'email': r['email']} for r in recipients]
    return api_response({
        'chapter_id': chapter_id,
        'chapter_name': chapter['chapter_name'],
        'event_id': event_id,
        'subject': subject,
        'recipients_count': len(recipients),
        'recipients': recipient_info,
        'notification_ids': notif_ids
    }, f"Reminder successfully delivered to {len(recipients)} chapter administrator(s).")
