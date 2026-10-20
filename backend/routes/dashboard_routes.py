from flask import Blueprint, request
from datetime import datetime, date, timedelta
from backend.db import get_db, serialize_rows, serialize_row
from backend.auth_helpers import api_response, api_error

dashboard_bp = Blueprint('dashboard', __name__, url_prefix='/api/dashboard')

@dashboard_bp.route('/overview', methods=['GET'])
def get_dashboard_overview():
    today = date.today()
    with get_db() as conn:
        with conn.cursor() as cur:
            # 1. Partner Chapters Count
            cur.execute("SELECT COUNT(*) AS c FROM chapters WHERE status = 'active'")
            partner_chapters = cur.fetchone()['c']

            # 2. Total Reports Submitted
            cur.execute("SELECT COUNT(*) AS c FROM event_reports")
            total_reports = cur.fetchone()['c']

            # 3. Query Conducted Events across chapters
            cur.execute("""
                SELECT 
                    e.id AS event_id,
                    e.chapter_id,
                    c.chapter_name,
                    c.chapter_code,
                    c.region,
                    e.title AS event_title,
                    e.ytr_program,
                    COALESCE(MAX(pr.event_conducted_at), e.start_datetime) AS conducted_datetime,
                    COALESCE(MAX(pr.event_date), DATE(e.start_datetime)) AS event_date,
                    e.status AS event_status,
                    e.venue,
                    MAX(er.id) AS report_id,
                    MAX(er.status) AS report_status,
                    MAX(er.submitted_at) AS report_submitted_at,
                    MAX(pr.id) AS poster_request_id,
                    MAX(pr.is_event_conducted) AS is_event_conducted,
                    MAX(pr.report_due_date) AS report_due_date,
                    MAX(pr.documentation_status) AS documentation_status
                FROM events e
                JOIN chapters c ON c.id = e.chapter_id
                LEFT JOIN event_reports er ON er.event_id = e.id
                LEFT JOIN poster_requests pr ON (pr.event_id = e.id OR (pr.chapter_id = e.chapter_id AND pr.event_title = e.title))
                WHERE e.status IN ('approved', 'published', 'completed')
                  AND (
                    (e.start_datetime IS NOT NULL AND e.start_datetime <= NOW())
                    OR (pr.is_event_conducted = 1)
                    OR (e.due_date IS NOT NULL)
                    OR (er.id IS NOT NULL)
                    OR (e.status = 'completed')
                  )
                GROUP BY e.id, e.chapter_id, c.chapter_name, c.chapter_code, c.region, e.title, e.ytr_program, e.start_datetime, e.status, e.venue
            """)
            conducted_rows = cur.fetchall()

            # Also include standalone poster requests marked conducted
            cur.execute("""
                SELECT 
                    NULL AS event_id,
                    pr.chapter_id,
                    c.chapter_name,
                    c.chapter_code,
                    c.region,
                    pr.event_title,
                    'Lead up (Youth Leadership)' AS ytr_program,
                    COALESCE(pr.event_conducted_at, pr.event_date) AS conducted_datetime,
                    pr.event_date,
                    pr.status AS event_status,
                    pr.venue,
                    er.id AS report_id,
                    er.status AS report_status,
                    er.submitted_at AS report_submitted_at,
                    pr.id AS poster_request_id,
                    pr.is_event_conducted,
                    pr.report_due_date,
                    pr.documentation_status
                FROM poster_requests pr
                JOIN chapters c ON c.id = pr.chapter_id
                LEFT JOIN event_reports er ON er.chapter_id = pr.chapter_id AND er.report_summary LIKE CONCAT('%', pr.event_title, '%')
                WHERE pr.is_event_conducted = 1
                  AND pr.event_id IS NULL
            """)
            standalone_pr_rows = cur.fetchall()

            all_event_titles = {r['event_title'] for r in conducted_rows}
            for pr_r in standalone_pr_rows:
                if pr_r['event_title'] not in all_event_titles:
                    conducted_rows.append(pr_r)
                    all_event_titles.add(pr_r['event_title'])

            conducted_events = []
            pending_count = 0
            overdue_count = 0
            due_soon_count = 0

            for r in conducted_rows:
                raw_dt = r.get('conducted_datetime') or r.get('event_date')
                if isinstance(raw_dt, datetime):
                    ev_date = raw_dt.date()
                elif isinstance(raw_dt, date):
                    ev_date = raw_dt
                else:
                    ev_date = today

                # 2-Week Deadline: 14 days after event date
                deadline = ev_date + timedelta(days=14)
                days_left = (deadline - today).days

                has_er = bool(r.get('report_id') and r.get('report_status'))
                doc_stat = r.get('documentation_status')
                is_submitted = has_er or doc_stat in ('submitted', 'approved')
                is_validated = (has_er and r.get('report_status') == 'approved') or doc_stat == 'approved'

                if is_validated:
                    status_key = 'approved'
                    status_label = 'Report Validated'
                    badge_cls = 'badge approved'
                    report_submitted_label = 'Submitted'
                    report_submitted_badge = 'badge approved'
                    needs_action = False
                elif is_submitted:
                    status_key = 'under_review'
                    status_label = 'Submitted (Under Review)'
                    badge_cls = 'badge blue'
                    report_submitted_label = 'Submitted'
                    report_submitted_badge = 'badge due-soon'
                    needs_action = True
                    pending_count += 1
                elif days_left < 0:
                    status_key = 'overdue'
                    status_label = f'Overdue by {abs(days_left)}d'
                    badge_cls = 'badge overdue'
                    report_submitted_label = 'Not Submitted'
                    report_submitted_badge = 'badge overdue'
                    needs_action = True
                    pending_count += 1
                    overdue_count += 1
                elif days_left <= 3:
                    status_key = 'due_soon'
                    status_label = f'Due Soon ({days_left}d left)'
                    badge_cls = 'badge due-soon'
                    report_submitted_label = 'Not Submitted'
                    report_submitted_badge = 'badge overdue'
                    needs_action = True
                    pending_count += 1
                    due_soon_count += 1
                else:
                    status_key = 'pending'
                    status_label = f'Pending ({days_left}d left)'
                    badge_cls = 'badge timer'
                    report_submitted_label = 'Not Submitted'
                    report_submitted_badge = 'badge overdue'
                    needs_action = True
                    pending_count += 1

                conducted_events.append({
                    'event_id': r.get('event_id'),
                    'poster_request_id': r.get('poster_request_id'),
                    'chapter_id': r.get('chapter_id'),
                    'chapter_name': r.get('chapter_name'),
                    'chapter_code': r.get('chapter_code'),
                    'region': r.get('region'),
                    'event_title': r.get('event_title'),
                    'ytr_program': r.get('ytr_program'),
                    'venue': r.get('venue'),
                    'event_date': ev_date.strftime('%Y-%m-%d'),
                    'event_date_formatted': ev_date.strftime('%b %d, %Y'),
                    'deadline': deadline.strftime('%Y-%m-%d'),
                    'deadline_formatted': deadline.strftime('%b %d, %Y'),
                    'days_left': days_left,
                    'is_report_submitted': is_submitted,
                    'is_validated': is_validated,
                    'report_submitted_label': report_submitted_label,
                    'report_submitted_badge': report_submitted_badge,
                    'report_id': r.get('report_id'),
                    'status_key': status_key,
                    'status_label': status_label,
                    'badge_cls': badge_cls,
                    'needs_action': needs_action
                })

            # Sort: pendings at the top, closest to deadline first
            conducted_events.sort(key=lambda x: (
                1 if x['is_report_submitted'] else 0,  # Not submitted (0) at top
                0 if x['days_left'] < 0 else 1,        # Overdue (0) first
                x['days_left']                         # Closest deadline first
            ))

            # 4. Partner Chapters Summary
            cur.execute("""
                SELECT 
                    c.id, c.chapter_name, c.chapter_code, c.region, c.status,
                    COUNT(DISTINCT e.id) AS total_events,
                    COUNT(DISTINCT CASE WHEN e.status IN ('approved', 'published', 'completed') THEN e.id END) AS conducted_events,
                    COUNT(DISTINCT er.id) AS reports_submitted,
                    GREATEST(0, COUNT(DISTINCT CASE WHEN e.status IN ('approved', 'published', 'completed') THEN e.id END) - COUNT(DISTINCT er.id)) AS pending_reports
                FROM chapters c
                LEFT JOIN events e ON e.chapter_id = c.id
                LEFT JOIN event_reports er ON er.event_id = e.id
                GROUP BY c.id, c.chapter_name, c.chapter_code, c.region, c.status
                ORDER BY c.chapter_name ASC
            """)
            chapters = cur.fetchall()

    return api_response({
        'metrics': {
            'pending_chapters_report': pending_count,
            'total_reports_submitted': total_reports,
            'partner_chapters_count': partner_chapters,
            'chapters_due_reports': overdue_count,
            'due_soon_reports': due_soon_count
        },
        'chapters': serialize_rows(chapters),
        'conducted_events': serialize_rows(conducted_events)
    })
