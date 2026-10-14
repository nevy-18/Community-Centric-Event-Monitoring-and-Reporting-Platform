from flask import Blueprint
from backend.db import get_db
from backend.auth_helpers import api_response
from datetime import date, timedelta

notifications_bp = Blueprint('notifications', __name__, url_prefix='/api/notifications')

@notifications_bp.route('/summary', methods=['GET'])
def get_notification_summary():
    today = date.today()
    soon_limit = today + timedelta(days=5)

    alerts = []

    with get_db() as conn:
        with conn.cursor() as cur:
            # 1. Check Overdue Events / Proposals
            cur.execute("""
                SELECT id, title, due_date, status
                FROM events
                WHERE due_date IS NOT NULL
                  AND due_date < %s
                  AND status NOT IN ('completed', 'cancelled')
                ORDER BY due_date ASC
                LIMIT 5
            """, (today,))
            overdue_events = cur.fetchall()

            for ev in overdue_events:
                alerts.append({
                    'type': 'overdue',
                    'title': 'Overdue Event Milestone',
                    'message': f"\"{ev['title']}\" deadline was on {ev['due_date']}.",
                    'badge': 'OVERDUE',
                    'badge_class': 'badge overdue',
                    'link': 'Proposal.php',
                    'event_id': ev['id']
                })

            # 2. Check Upcoming Due Dates (Due Soon)
            cur.execute("""
                SELECT id, title, due_date, status
                FROM events
                WHERE due_date IS NOT NULL
                  AND due_date >= %s
                  AND due_date <= %s
                  AND status NOT IN ('completed', 'cancelled')
                ORDER BY due_date ASC
                LIMIT 5
            """, (today, soon_limit))
            soon_events = cur.fetchall()

            for ev in soon_events:
                days_left = (ev['due_date'] - today).days
                alerts.append({
                    'type': 'due_soon',
                    'title': 'Milestone Approaching',
                    'message': f"\"{ev['title']}\" is due in {days_left} day(s) on {ev['due_date']}.",
                    'badge': 'DUE SOON',
                    'badge_class': 'badge due-soon',
                    'link': 'Proposal.php',
                    'event_id': ev['id']
                })

            # 3. Check Pending Account Requests
            cur.execute("SELECT COUNT(*) AS c FROM users WHERE account_status = 'pending'")
            pending_accounts = cur.fetchone()['c']
            if pending_accounts > 0:
                alerts.append({
                    'type': 'pending_account',
                    'title': 'Pending Account Requests',
                    'message': f"{pending_accounts} new coordinator application(s) awaiting approval.",
                    'badge': 'ACTION REQUIRED',
                    'badge_class': 'badge pending',
                    'link': 'Manage.php'
                })

            # 4. Check Pending Event Proposals
            cur.execute("SELECT COUNT(*) AS c FROM events WHERE status = 'pending_approval'")
            pending_proposals = cur.fetchone()['c']
            if pending_proposals > 0:
                alerts.append({
                    'type': 'pending_proposal',
                    'title': 'Proposals Awaiting Evaluation',
                    'message': f"{pending_proposals} chapter event proposal(s) waiting for National Admin review.",
                    'badge': 'PENDING REVIEW',
                    'badge_class': 'badge pending',
                    'link': 'Proposal.php'
                })

    return api_response({
        'total_alerts': len(alerts),
        'unread_count': len(alerts),
        'alerts': alerts
    })
