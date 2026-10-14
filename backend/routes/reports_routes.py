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
               c.chapter_name, c.chapter_code,
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

@reports_bp.route('/<int:report_id>', methods=['GET'])
def get_report(report_id):
    sql = """
        SELECT r.*, e.title AS event_title, e.start_datetime, e.end_datetime,
               e.venue, e.target_turnout, e.budget_amount,
               c.chapter_name, c.chapter_code,
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
    chapter_id = data.get('chapter_id') or session.get('chapter_id')
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
    submitted_by = session.get('user_id')

    if not event_id:
        return api_error('Event ID is required.', 400)

    if not submitted_by:
        with get_db() as conn:
            with conn.cursor() as cur:
                cur.execute("SELECT id, chapter_id FROM users WHERE account_status = 'active' LIMIT 1")
                first = cur.fetchone()
                if first:
                    submitted_by = first['id']
                    if not chapter_id:
                        chapter_id = first['chapter_id']

    with get_db() as conn:
        with conn.cursor() as cur:
            if not chapter_id:
                cur.execute("SELECT chapter_id FROM events WHERE id = %s", (event_id,))
                ev = cur.fetchone()
                chapter_id = ev['chapter_id'] if ev else 1

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

    return api_response({
        'report_id': rep_id
    }, 'Event report and attendance roster submitted and signed successfully!', 201)
