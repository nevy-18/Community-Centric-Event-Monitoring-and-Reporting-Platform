from flask import Blueprint, jsonify, request
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
                SELECT id, chapter_code, chapter_name, generation_name, region, province, city_municipality, status
                FROM chapters
                WHERE status = 'active'
                ORDER BY chapter_name ASC
            """)
            chapters = cur.fetchall()

    return api_response(serialize_rows(chapters))

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
                SELECT e.id, e.title, e.event_type, e.start_datetime, e.end_datetime,
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
