from flask import Blueprint, request
from backend.db import get_db, serialize_rows, serialize_row
from backend.auth_helpers import api_response, api_error

dashboard_bp = Blueprint('dashboard', __name__, url_prefix='/api/dashboard')

@dashboard_bp.route('/overview', methods=['GET'])
def get_dashboard_overview():
    with get_db() as conn:
        with conn.cursor() as cur:
            # 1. Pending Chapters Report: Approved events that do not yet have an event_report
            cur.execute("""
                SELECT COUNT(*) AS c 
                FROM events e
                LEFT JOIN event_reports er ON er.event_id = e.id
                WHERE e.status IN ('approved', 'published', 'completed')
                  AND er.id IS NULL
            """)
            pending_reports = cur.fetchone()['c']

            # 2. Total Reports Submitted
            cur.execute("SELECT COUNT(*) AS c FROM event_reports")
            total_reports = cur.fetchone()['c']

            # 3. Partner Chapters Count
            cur.execute("SELECT COUNT(*) AS c FROM chapters WHERE status = 'active'")
            partner_chapters = cur.fetchone()['c']

            # 4. Chapters Due Reports: events past due_date (or start_datetime) without report
            cur.execute("""
                SELECT COUNT(*) AS c
                FROM events e
                LEFT JOIN event_reports er ON er.event_id = e.id
                WHERE e.status IN ('approved', 'published', 'completed')
                  AND er.id IS NULL
                  AND (
                    (e.due_date IS NOT NULL AND e.due_date < CURDATE())
                    OR (e.start_datetime IS NOT NULL AND e.start_datetime < NOW())
                  )
            """)
            due_reports = cur.fetchone()['c']

            # 5. Partner Chapters Breakdown Table
            cur.execute("""
                SELECT 
                    c.id, c.chapter_name, c.chapter_code, c.region, c.status,
                    COUNT(DISTINCT e.id) AS total_events,
                    COUNT(DISTINCT CASE WHEN e.status IN ('approved', 'published', 'completed') THEN e.id END) AS conducted_events,
                    COUNT(DISTINCT er.id) AS reports_submitted,
                    GREATEST(0, COUNT(DISTINCT CASE WHEN e.status IN ('approved', 'published') THEN e.id END) - COUNT(DISTINCT er.id)) AS pending_reports
                FROM chapters c
                LEFT JOIN events e ON e.chapter_id = c.id
                LEFT JOIN event_reports er ON er.event_id = e.id
                GROUP BY c.id, c.chapter_name, c.chapter_code, c.region, c.status
                ORDER BY c.chapter_name ASC
            """)
            chapters = cur.fetchall()

    return api_response({
        'metrics': {
            'pending_chapters_report': pending_reports,
            'total_reports_submitted': total_reports,
            'partner_chapters_count': partner_chapters,
            'chapters_due_reports': due_reports
        },
        'chapters': serialize_rows(chapters)
    })
