from flask import Blueprint, request, session
from backend.db import get_db
from backend.auth_helpers import api_response, api_error
from datetime import date, timedelta

notifications_bp = Blueprint('notifications', __name__, url_prefix='/api/notifications')

@notifications_bp.route('/summary', methods=['GET'])
def get_notification_summary():
    today = date.today()
    soon_limit = today + timedelta(days=5)

    alerts = []

    user_id = request.headers.get('X-User-Id') or request.args.get('user_id') or session.get('user_id')
    user_email = request.headers.get('X-User-Email') or session.get('user_email')

    with get_db() as conn:
        with conn.cursor() as cur:
            user = None
            if user_id:
                try:
                    cur.execute("SELECT id, role, chapter_id, email, full_name FROM users WHERE id = %s", (int(user_id),))
                    user = cur.fetchone()
                except (ValueError, TypeError):
                    user = None

            if not user and user_email:
                cur.execute("SELECT id, role, chapter_id, email, full_name FROM users WHERE LOWER(email) = %s LIMIT 1", (str(user_email).lower(),))
                user = cur.fetchone()

            user_role = user.get('role') if user else None
            user_chapter_id = user.get('chapter_id') if user else None

            # 1. Fetch targeted direct notifications from `notifications` table
            unread_count = 0
            if user:
                cur.execute("""
                    SELECT id, user_id, created_by, title, message, related_type, related_id, target_url, is_read, created_at
                    FROM notifications
                    WHERE user_id = %s
                    ORDER BY created_at DESC
                    LIMIT 20
                """, (user['id'],))
                db_notifs = cur.fetchall()

                for n in db_notifs:
                    is_read = bool(n.get('is_read'))
                    if not is_read:
                        unread_count += 1

                    rel_type = n.get('related_type') or 'reminder'
                    badge = 'REMINDER' if rel_type == 'event_report' else 'NOTIFICATION'
                    badge_cls = 'badge overdue' if rel_type == 'event_report' else 'badge timer'
                    target_link = n.get('target_url') or ('ChapterReport.php' if user_role == 'chapter_admin' else 'Report.php')

                    alerts.append({
                        'id': n['id'],
                        'type': rel_type,
                        'title': n['title'],
                        'message': n['message'],
                        'badge': badge,
                        'badge_class': badge_cls,
                        'link': target_link,
                        'is_read': is_read,
                        'created_at': str(n.get('created_at'))
                    })

            # 2. System Alerts based on Role
            if user_role == 'chapter_admin' and user_chapter_id:
                # Chapter admin: Only see milestones for their chapter
                cur.execute("""
                    SELECT id, title, due_date, status
                    FROM events
                    WHERE chapter_id = %s
                      AND due_date IS NOT NULL
                      AND due_date < %s
                      AND status NOT IN ('completed', 'cancelled')
                    ORDER BY due_date ASC
                    LIMIT 5
                """, (user_chapter_id, today))
                overdue_events = cur.fetchall()

                for ev in overdue_events:
                    unread_count += 1
                    title = ev.get('title', 'Event')
                    due = ev.get('due_date', '')
                    alerts.append({
                        'type': 'overdue',
                        'title': 'Overdue Milestone',
                        'message': f'"{title}" deadline was on {due}.',
                        'badge': 'OVERDUE',
                        'badge_class': 'badge overdue',
                        'link': 'ChapterProposal.php',
                        'event_id': ev['id']
                    })

                cur.execute("""
                    SELECT id, title, due_date, status
                    FROM events
                    WHERE chapter_id = %s
                      AND due_date IS NOT NULL
                      AND due_date >= %s
                      AND due_date <= %s
                      AND status NOT IN ('completed', 'cancelled')
                    ORDER BY due_date ASC
                    LIMIT 5
                """, (user_chapter_id, today, soon_limit))
                soon_events = cur.fetchall()

                for ev in soon_events:
                    days_left = (ev['due_date'] - today).days
                    unread_count += 1
                    title = ev.get('title', 'Event')
                    due = ev.get('due_date', '')
                    alerts.append({
                        'type': 'due_soon',
                        'title': 'Milestone Approaching',
                        'message': f'"{title}" is due in {days_left} day(s) on {due}.',
                        'badge': 'DUE SOON',
                        'badge_class': 'badge due-soon',
                        'link': 'ChapterProposal.php',
                        'event_id': ev['id']
                    })

            else:
                # National Admin or guest overview
                # Check Overdue Events
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
                    unread_count += 1
                    title = ev.get('title', 'Event')
                    due = ev.get('due_date', '')
                    alerts.append({
                        'type': 'overdue',
                        'title': 'Overdue Event Milestone',
                        'message': f'"{title}" deadline was on {due}.',
                        'badge': 'OVERDUE',
                        'badge_class': 'badge overdue',
                        'link': 'Proposal.php',
                        'event_id': ev['id']
                    })

                # Check Upcoming Due Dates
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
                    unread_count += 1
                    title = ev.get('title', 'Event')
                    due = ev.get('due_date', '')
                    alerts.append({
                        'type': 'due_soon',
                        'title': 'Milestone Approaching',
                        'message': f'"{title}" is due in {days_left} day(s) on {due}.',
                        'badge': 'DUE SOON',
                        'badge_class': 'badge due-soon',
                        'link': 'Proposal.php',
                        'event_id': ev['id']
                    })

                # Check Pending Account Requests
                cur.execute("SELECT COUNT(*) AS c FROM users WHERE account_status = 'pending'")
                pending_accounts = cur.fetchone()['c']
                if pending_accounts > 0:
                    unread_count += 1
                    alerts.append({
                        'type': 'pending_account',
                        'title': 'Pending Account Requests',
                        'message': f"{pending_accounts} new coordinator application(s) awaiting approval.",
                        'badge': 'ACTION REQUIRED',
                        'badge_class': 'badge pending',
                        'link': 'Manage.php'
                    })

                # Check Pending Event Proposals
                cur.execute("SELECT COUNT(*) AS c FROM events WHERE status = 'pending_approval'")
                pending_proposals = cur.fetchone()['c']
                if pending_proposals > 0:
                    unread_count += 1
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
        'unread_count': unread_count,
        'alerts': alerts
    })


@notifications_bp.route('/<int:notif_id>/read', methods=['PUT', 'POST'])
def mark_notification_read(notif_id):
    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("UPDATE notifications SET is_read = 1, read_at = NOW() WHERE id = %s", (notif_id,))
    return api_response({'id': notif_id}, "Notification marked as read.")


@notifications_bp.route('/read-all', methods=['PUT', 'POST'])
def mark_all_notifications_read():
    user_id = request.headers.get('X-User-Id') or session.get('user_id')
    user_email = request.headers.get('X-User-Email') or session.get('user_email')
    with get_db() as conn:
        with conn.cursor() as cur:
            if user_id:
                try:
                    cur.execute("UPDATE notifications SET is_read = 1, read_at = NOW() WHERE user_id = %s", (int(user_id),))
                except (ValueError, TypeError):
                    pass
            elif user_email:
                cur.execute("""
                    UPDATE notifications n
                    JOIN users u ON u.id = n.user_id
                    SET n.is_read = 1, n.read_at = NOW()
                    WHERE LOWER(u.email) = %s
                """, (str(user_email).lower(),))
    return api_response(None, "All notifications marked as read.")
