from flask import Blueprint, request, session
from backend.db import get_db, serialize_rows, serialize_row
from backend.auth_helpers import api_response, api_error
from datetime import datetime

reports_bp = Blueprint('reports', __name__, url_prefix='/api/reports')

def enrich_report(r):
    """Compute turnout thresholds and formatted timestamps."""
    target = int(r.get('target_turnout') or 100)
    actual = int(r.get('total_participants') or 0)
    r['target_turnout'] = target
    r['turnout_percentage'] = round((actual / target) * 100, 1) if target > 0 else 0
    r['target_achieved'] = actual >= target
    return r

@reports_bp.route('/metrics', methods=['GET'])
def get_metrics():
    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("SELECT COUNT(*) AS c FROM events WHERE status IN ('approved', 'published', 'ongoing')")
            active = cur.fetchone()['c']

            cur.execute("SELECT COUNT(*) AS c FROM events WHERE status = 'completed'")
            completed = cur.fetchone()['c']

            cur.execute("SELECT COALESCE(SUM(first_timers), 0) AS c FROM event_reports WHERE status = 'approved'")
            first_timers = cur.fetchone()['c']

    return api_response({
        'total_active_events': active,
        'completed_events': completed,
        'first_timers': int(first_timers)
    })

@reports_bp.route('', methods=['GET'])
def list_reports():
    filter_type = request.args.get('filter', 'all')
    search = request.args.get('search', '').strip()

    sql = """
        SELECT r.id AS report_id, r.total_participants, r.first_timers, r.existing_members,
               r.youth_leaders_count, r.attendee_roster, r.e_signature, r.signed_by_name,
               r.signed_at, r.budget_spent, r.report_summary, r.highlights, r.challenges,
               r.recommendations, r.status AS report_status, r.submitted_at,
               e.id AS event_id, e.title AS event_title, e.start_datetime, e.end_datetime,
               e.venue, e.status AS event_status, e.target_turnout, e.budget_amount,
               c.id AS chapter_id, c.chapter_name, c.chapter_code,
               c.regional_director_name, c.regional_director_id,
               u.full_name AS submitted_by_name
        FROM events e
        LEFT JOIN event_reports r ON r.event_id = e.id
        LEFT JOIN chapters c ON c.id = e.chapter_id
        LEFT JOIN users u ON u.id = r.submitted_by
        WHERE 1=1
    """
    params = []

    if filter_type == 'completed':
        sql += " AND (r.status = 'approved' OR e.status = 'completed')"
    elif filter_type == 'pending':
        sql += " AND (r.status = 'submitted' OR (r.id IS NULL AND e.status IN ('approved', 'ongoing')))"

    if search:
        sql += " AND (e.title LIKE %s OR c.chapter_name LIKE %s)"
        s = f"%{search}%"
        params.extend([s, s])

    sql += " ORDER BY e.start_datetime DESC"

    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute(sql, tuple(params))
            reports = cur.fetchall()

    serialized = serialize_rows(reports)
    enriched = [enrich_report(rep) for rep in serialized]

    return api_response(enriched)

@reports_bp.route('/signatories', methods=['GET'])
def get_report_signatories():
    """Returns official executive signatories including Chief Executive Operation and signature."""
    with get_db() as conn:
        with conn.cursor() as cur:
            # Fetch chief executive / national admin
            cur.execute("""
                SELECT id, full_name, email, role, position, signature_image
                FROM users
                WHERE role = 'national_admin' AND account_status = 'active'
                ORDER BY CASE 
                    WHEN position LIKE '%Chief%' OR position LIKE '%Operation%' THEN 0
                    WHEN position LIKE '%Executive%' THEN 1
                    ELSE 2 
                END, id ASC
                LIMIT 1
            """)
            exec_user = cur.fetchone()

    exec_name = exec_user['full_name'] if exec_user else 'Hamilton Jones Dela'
    exec_pos = 'Chief Executive Operation'
    exec_sig = exec_user.get('signature_image') if exec_user else None

    return api_response({
        'chief_executive_name': exec_name,
        'chief_executive_title': exec_pos,
        'chief_executive_signature': exec_sig
    })

@reports_bp.route('/<int:report_id>', methods=['GET'])
def get_report(report_id):
    sql = """
        SELECT r.*, e.title AS event_title, e.start_datetime, e.end_datetime,
               e.venue, e.target_turnout, e.budget_amount,
               c.chapter_name, c.chapter_code,
               c.regional_director_name, c.regional_director_id,
               u.full_name AS submitter_name
        FROM event_reports r
        JOIN events e ON e.id = r.event_id
        LEFT JOIN chapters c ON c.id = r.chapter_id
        LEFT JOIN users u ON u.id = r.submitted_by
        WHERE r.id = %s
        LIMIT 1
    """
    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute(sql, (report_id,))
            rep = cur.fetchone()

    if not rep:
        return api_error('Report not found.', 404)

    return api_response(enrich_report(serialize_row(rep)))

@reports_bp.route('', methods=['POST'])
def submit_report():
    data = request.get_json(silent=True) or request.form.to_dict() or {}
    event_id = data.get('event_id')
    total_participants = int(data.get('total_participants') or 0)
    first_timers = int(data.get('first_timers') or 0)
    existing_members = int(data.get('existing_members') or 0)
    youth_leaders_count = int(data.get('youth_leaders_count') or 0)
    attendee_roster = data.get('attendee_roster', '').strip()
    e_signature = data.get('e_signature', '').strip()
    signed_by_name = data.get('signed_by_name', '').strip()
    budget_spent = float(data.get('budget_spent') or 0.0)
    report_summary = data.get('report_summary', '').strip()
    highlights = data.get('highlights', '').strip()
    challenges = data.get('challenges', '').strip()
    recommendations = data.get('recommendations', '').strip()
    submitted_by = data.get('submitted_by') or session.get('user_id')

    if not event_id:
        return api_error('Event ID is required.', 400)

    with get_db() as conn:
        with conn.cursor() as cur:
            # 1. Fetch event and verify existence
            cur.execute("SELECT id, chapter_id, title, status FROM events WHERE id = %s", (event_id,))
            ev = cur.fetchone()
            if not ev:
                return api_error('Event not found.', 404)
            
            event_chapter_id = ev['chapter_id']

            # 2. Enforce: ONLY chapter_admin can submit reports!
            if submitted_by:
                cur.execute("SELECT id, role, chapter_id, full_name FROM users WHERE id = %s", (submitted_by,))
                submitter = cur.fetchone()
                if not submitter:
                    return api_error('Submitting user account does not exist.', 404)
                
                # Reject National Admin submission
                if submitter['role'] == 'national_admin':
                    return api_error('Submission restricted: Only Chapter-based administrators can submit post-event reports. National Admins evaluate and audit submissions.', 403)
                
                if submitter['role'] != 'chapter_admin':
                    return api_error('Unauthorized: Only active Chapter Admins can submit post-event reports.', 403)
                
                if submitter['chapter_id'] and submitter['chapter_id'] != event_chapter_id:
                    return api_error('You can only submit event reports for events belonging to your assigned chapter.', 403)
                
                if not signed_by_name:
                    signed_by_name = submitter['full_name']
                chapter_id = submitter['chapter_id']
            else:
                # If no session/user passed explicitly, find active chapter_admin for this event's chapter
                cur.execute("SELECT id, full_name, chapter_id FROM users WHERE role = 'chapter_admin' AND chapter_id = %s AND account_status = 'active' LIMIT 1", (event_chapter_id,))
                default_chapter_admin = cur.fetchone()
                if default_chapter_admin:
                    submitted_by = default_chapter_admin['id']
                    if not signed_by_name:
                        signed_by_name = default_chapter_admin['full_name']
                    chapter_id = default_chapter_admin['chapter_id']
                else:
                    return api_error('Only Chapter-based administrators can submit reports. No authorized chapter admin found for this chapter.', 403)

            # Check if report already exists for this event
            cur.execute("SELECT id FROM event_reports WHERE event_id = %s LIMIT 1", (event_id,))
            existing = cur.fetchone()

            if existing:
                cur.execute("""
                    UPDATE event_reports
                    SET total_participants = %s,
                        first_timers = %s,
                        existing_members = %s,
                        youth_leaders_count = %s,
                        attendee_roster = %s,
                        e_signature = %s,
                        signed_by_name = %s,
                        signed_at = NOW(),
                        budget_spent = %s,
                        report_summary = %s,
                        highlights = %s,
                        challenges = %s,
                        recommendations = %s,
                        status = 'approved',
                        submitted_at = NOW(),
                        updated_at = NOW()
                    WHERE id = %s
                """, (
                    total_participants, first_timers, existing_members, youth_leaders_count,
                    attendee_roster, e_signature, signed_by_name, budget_spent,
                    report_summary, highlights, challenges, recommendations,
                    existing['id']
                ))
                rep_id = existing['id']
            else:
                cur.execute("""
                    INSERT INTO event_reports (
                        event_id, chapter_id, submitted_by, total_participants, first_timers,
                        existing_members, youth_leaders_count, attendee_roster, e_signature,
                        signed_by_name, signed_at, budget_spent, report_summary, highlights,
                        challenges, recommendations, status, submitted_at, created_at
                    ) VALUES (
                        %s, %s, %s, %s, %s,
                        %s, %s, %s, %s,
                        %s, NOW(), %s, %s, %s,
                        %s, %s, 'approved', NOW(), NOW()
                    )
                """, (
                    event_id, chapter_id, submitted_by, total_participants, first_timers,
                    existing_members, youth_leaders_count, attendee_roster, e_signature,
                    signed_by_name, budget_spent, report_summary, highlights,
                    challenges, recommendations
                ))
                rep_id = cur.lastrowid

            # Update event status to completed
            cur.execute("UPDATE events SET status = 'completed', updated_at = NOW() WHERE id = %s", (event_id,))

            # Synchronize linked poster request timeline: Chapter reported using the Report tab!
            cur.execute("""
                UPDATE poster_requests
                SET documentation_status = 'submitted',
                    is_event_conducted = 1,
                    attendees_count = %s,
                    first_timers_count = %s,
                    report_summary = %s,
                    documentation_submitted_at = NOW(),
                    current_step = 4,
                    updated_at = NOW()
                WHERE event_id = %s OR (chapter_id = %s AND event_title = %s)
            """, (total_participants, first_timers, report_summary, event_id, event_chapter_id, ev['title']))

            cur.execute("""
                UPDATE poster_request_steps prs
                JOIN poster_requests pr ON pr.id = prs.poster_request_id
                SET prs.status = 'in_progress',
                    prs.notes = 'Official event report submitted via Report tab. Awaiting National Admin validation.'
                WHERE (pr.event_id = %s OR (pr.chapter_id = %s AND pr.event_title = %s))
                  AND prs.step_order = 4
            """, (event_id, event_chapter_id, ev['title']))

            cur.execute("""
                INSERT INTO poster_request_updates
                (poster_request_id, updated_by, update_type, message, created_at)
                SELECT id, %s, 'step_updated', 'Official event report submitted via Report tab. Awaiting National Admin validation before timeline can conclude.', NOW()
                FROM poster_requests
                WHERE event_id = %s OR (chapter_id = %s AND event_title = %s)
            """, (submitted_by, event_id, event_chapter_id, ev['title']))

            conn.commit()

    return api_response({
        'report_id': rep_id
    }, 'Event report and attendance roster submitted and signed successfully!', 201)
