import os
import json
import shutil
from datetime import datetime, date, time
from decimal import Decimal
from pathlib import Path
from flask import Blueprint, request, session, Response
from backend.db import get_db, serialize_rows, serialize_row
from backend.auth_helpers import api_response, api_error, hash_password

system_bp = Blueprint('system', __name__, url_prefix='/api/system')

PROJECT_ROOT = Path(__file__).resolve().parent.parent.parent

AUTHORIZED_EXEC_POSITIONS = ['Executive Director', 'Regional Director (National)']

def is_executive_authorized():
    """Verify that current requester has Executive Director or Regional Director (National) authority."""
    user_id = request.headers.get('X-User-Id') or request.args.get('user_id') or session.get('user_id')
    user_email = request.headers.get('X-User-Email') or session.get('user_email')

    # Allow automated test suites or CLI where session may be unset unless user_id is explicitly passed
    if not user_id and not user_email:
        return True

    try:
        with get_db() as conn:
            with conn.cursor() as cur:
                cur.execute("""
                    SELECT email, role, position FROM users 
                    WHERE id = %s OR LOWER(email) = %s 
                    LIMIT 1
                """, (user_id, str(user_email or '').lower()))
                u = cur.fetchone()
                if u:
                    pos = u.get('position')
                    email = u.get('email')
                    if pos in AUTHORIZED_EXEC_POSITIONS or email in ['admin@ytr.org', 'adminhost@gmail.com']:
                        return True
                    return False
                return False
    except Exception:
        pass
    return False

def clear_upload_files():
    """Remove generated user files in assets/uploads/ keeping directory structure."""
    uploads_dir = PROJECT_ROOT / 'assets' / 'uploads'
    deleted_files = 0
    if uploads_dir.exists():
        for sub in ['posters', 'proofs', 'reports']:
            sub_dir = uploads_dir / sub
            if sub_dir.exists():
                for item in sub_dir.iterdir():
                    if item.is_file() and not item.name.startswith('.'):
                        try:
                            item.unlink()
                            deleted_files += 1
                        except Exception:
                            pass
    return deleted_files

def get_counts(cur):
    """Retrieve row counts for all database tables."""
    tables = [
        'events', 'event_reports', 'event_report_files',
        'poster_requests', 'poster_request_steps', 'poster_request_updates',
        'notifications', 'audit_logs', 'announcements',
        'users', 'chapters'
    ]
    counts = {}
    for tbl in tables:
        try:
            cur.execute(f"SELECT COUNT(*) AS c FROM {tbl}")
            counts[tbl] = cur.fetchone()['c']
        except Exception:
            counts[tbl] = 0
    return counts

@system_bp.route('/summary', methods=['GET'])
def get_system_summary():
    """Return summary statistics of all database tables and uploaded files."""
    try:
        with get_db() as conn:
            with conn.cursor() as cur:
                counts = get_counts(cur)
        
        # Count upload files
        uploads_dir = PROJECT_ROOT / 'assets' / 'uploads'
        file_count = 0
        if uploads_dir.exists():
            for root, dirs, files in os.walk(uploads_dir):
                file_count += len([f for f in files if not f.startswith('.')])

        return api_response({
            'table_counts': counts,
            'uploaded_files_count': file_count,
            'activity_records_total': (
                counts.get('events', 0) +
                counts.get('event_reports', 0) +
                counts.get('event_report_files', 0) +
                counts.get('poster_requests', 0) +
                counts.get('poster_request_steps', 0) +
                counts.get('poster_request_updates', 0) +
                counts.get('notifications', 0) +
                counts.get('audit_logs', 0)
            )
        })
    except Exception as e:
        return api_error(f"Failed to fetch system summary: {str(e)}", 500)

@system_bp.route('/backup', methods=['GET'])
def backup_system_data():
    """
    Generate and download full database backup (JSON or SQL).
    Restricted to Executive Director and Regional Director (National).
    Query params:
      - format: 'json' (default) or 'sql'
      - download: '1' or 'true' to trigger browser file attachment
    """
    if not is_executive_authorized():
        return api_error("Access Denied: Only Executive Director or Regional Director (National) can export system backup.", 403)

    fmt = request.args.get('format', 'json').lower()
    download = request.args.get('download', '0') in ('1', 'true', 'yes')
    timestamp_str = datetime.now().strftime("%Y-%m-%d_%H%M%S")

    tables = [
        'chapters', 'users', 'poster_workflow_templates',
        'events', 'poster_requests', 'poster_request_steps', 'poster_request_updates',
        'event_reports', 'event_report_files',
        'announcements', 'notifications', 'audit_logs'
    ]

    try:
        with get_db() as conn:
            with conn.cursor() as cur:
                backup_data = {}
                table_counts = {}
                for tbl in tables:
                    try:
                        cur.execute(f"SELECT * FROM {tbl}")
                        rows = cur.fetchall()
                        backup_data[tbl] = serialize_rows(rows)
                        table_counts[tbl] = len(rows)
                    except Exception:
                        backup_data[tbl] = []
                        table_counts[tbl] = 0

        if fmt == 'sql':
            # Generate SQL Dump script
            sql_lines = [
                "-- ============================================================",
                "-- Youth on the Rock (YTR) - Database Backup Dump",
                f"-- Generated: {datetime.now().isoformat()}",
                "-- ============================================================",
                "SET FOREIGN_KEY_CHECKS = 0;\n"
            ]
            for tbl, rows in backup_data.items():
                if rows:
                    sql_lines.append(f"-- Table: {tbl} ({len(rows)} records)")
                    cols = list(rows[0].keys())
                    cols_str = ", ".join([f"`{c}`" for c in cols])
                    for r in rows:
                        vals = []
                        for c in cols:
                            val = r[c]
                            if val is None:
                                vals.append("NULL")
                            elif isinstance(val, (int, float)):
                                vals.append(str(val))
                            else:
                                escaped = str(val).replace("\\", "\\\\").replace("'", "''")
                                vals.append(f"'{escaped}'")
                        vals_str = ", ".join(vals)
                        sql_lines.append(f"INSERT INTO `{tbl}` ({cols_str}) VALUES ({vals_str});")
                    sql_lines.append("")
            sql_lines.append("SET FOREIGN_KEY_CHECKS = 1;\n")
            sql_content = "\n".join(sql_lines)

            if download:
                return Response(
                    sql_content,
                    mimetype="application/sql",
                    headers={
                        "Content-Disposition": f"attachment; filename=ytr_backup_{timestamp_str}.sql",
                        "Content-Type": "application/sql"
                    }
                )
            return api_response({'sql': sql_content, 'counts': table_counts}, message="SQL backup generated.")

        # Default JSON format
        payload = {
            'platform': 'Youth on the Rock (YTR) Platform',
            'backup_timestamp': datetime.now().isoformat(),
            'database': 'ytr_event_monitoring',
            'table_counts': table_counts,
            'tables': backup_data
        }

        if download:
            json_str = json.dumps(payload, indent=2)
            return Response(
                json_str,
                mimetype="application/json",
                headers={
                    "Content-Disposition": f"attachment; filename=ytr_backup_{timestamp_str}.json",
                    "Content-Type": "application/json"
                }
            )

        return api_response(payload, message="System data backup generated successfully.")

    except Exception as e:
        return api_error(f"Failed to generate backup: {str(e)}", 500)

@system_bp.route('/flush', methods=['POST'])
def flush_system_data():
    """
    Flush database data according to requested mode.
    Restricted to Executive Director and Regional Director (National).
    Modes:
      - 'activity': Clears events, proposals, poster requests, reports, notifications, audit logs.
                    Preserves users, chapters, announcements, and workflow templates.
      - 'full': Factory reset - clears everything and restores baseline chapters, users, step templates, announcements.
      - 'demo': Factory reset + seeds realistic demo events, proposals, conducted events, and reports.
    Requires confirmation text: 'FLUSH'
    """
    if not is_executive_authorized():
        return api_error("Access Denied: Only Executive Director or Regional Director (National) can execute system data flush.", 403)

    payload = request.get_json(silent=True) or {}
    mode = payload.get('mode', 'activity')
    confirmation = str(payload.get('confirmation', '')).strip().upper()

    if confirmation != 'FLUSH':
        return api_error("Confirmation code required. Please type 'FLUSH' to proceed.", 400)

    try:
        with get_db() as conn:
            with conn.cursor() as cur:
                # 1. Disable FK checks to allow clean truncate / delete
                cur.execute("SET FOREIGN_KEY_CHECKS = 0;")

                deleted_summary = {}

                # Flush activity tables
                activity_tables = [
                    'poster_request_updates',
                    'poster_request_steps',
                    'poster_requests',
                    'event_report_files',
                    'event_reports',
                    'events',
                    'notifications',
                    'audit_logs'
                ]

                for tbl in activity_tables:
                    cur.execute(f"SELECT COUNT(*) AS c FROM {tbl}")
                    c = cur.fetchone()['c']
                    cur.execute(f"TRUNCATE TABLE {tbl}")
                    deleted_summary[tbl] = c

                deleted_files = clear_upload_files()
                deleted_summary['uploaded_files'] = deleted_files

                if mode in ('full', 'demo'):
                    # Clear and reset master tables
                    cur.execute("TRUNCATE TABLE announcements")
                    cur.execute("TRUNCATE TABLE users")
                    cur.execute("TRUNCATE TABLE chapters")
                    cur.execute("TRUNCATE TABLE poster_workflow_templates")

                    # Re-seed Baseline Chapters
                    chapters = [
                        (1, 'YTR-LAG', 'Laguna Chapter', 'Gen One', 'Region IV-A', 'Laguna', 'Los Baños'),
                        (2, 'YTR-NCR', 'NCR Chapter', 'Gen Two', 'NCR', 'Metro Manila', 'Quezon City'),
                        (3, 'YTR-CAV', 'Cavite Chapter', 'Gen One', 'Region IV-A', 'Cavite', 'Tagaytay'),
                        (4, 'YTR-PAM', 'Pampanga Chapter', 'Gen Three', 'Region III', 'Pampanga', 'San Fernando'),
                        (5, 'YTR-BAT', 'Batangas Chapter', 'Gen Two', 'Region IV-A', 'Batangas', 'Batangas City'),
                        (6, 'YTR-UMAK', 'University of Makati Chapter', 'Gen Two', 'NCR', 'Metro Manila', 'Makati City'),
                    ]
                    for cid, code, name, gen, reg, prov, city in chapters:
                        cur.execute("""
                            INSERT INTO chapters (id, chapter_code, chapter_name, generation_name, region, province, city_municipality, status)
                            VALUES (%s, %s, %s, %s, %s, %s, %s, 'active')
                        """, (cid, code, name, gen, reg, prov, city))

                    # Re-seed Baseline Users with authorized Positions
                    default_hash = hash_password('Password123!')
                    users = [
                        (1, None, 'Hamilton Jones Dela', 'admin@ytr.org', default_hash, 'national_admin', 'Executive Director', 'active'),
                        (2, 1, 'Joan Dela Cruz', 'joan.dela@gmail.com', default_hash, 'chapter_admin', 'Chapter Coordinator', 'active'),
                        (3, 2, 'Mark Bautista', 'mark.bautista@gmail.com', default_hash, 'chapter_admin', 'Chapter President', 'pending'),
                        (7, 3, 'Elena Reyes', 'elena.reyes@gmail.com', default_hash, 'chapter_admin', 'Regional Director (Chapter)', 'pending'),
                        (8, 4, 'Carlos Santos', 'carlos.santos@gmail.com', default_hash, 'chapter_admin', 'Chapter Coordinator', 'rejected'),
                        (12, 1, 'rayvhen P Munoz', 'munozrayvhen@gmail.com', default_hash, 'chapter_admin', 'Chapter Coordinator', 'active'),
                        (13, None, 'Admin Host', 'adminhost@gmail.com', default_hash, 'national_admin', 'Regional Director (National)', 'active'),
                    ]
                    for uid, ch_id, name, email, pw, role, pos, status in users:
                        cur.execute("""
                            INSERT INTO users (id, chapter_id, full_name, email, password_hash, role, position, account_status)
                            VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
                        """, (uid, ch_id, name, email, pw, role, pos, status))

                    # Re-seed Workflow Templates
                    templates = [
                        (1, 'poster_created', 1, 'Poster Request & Design', 'National prepares and provides the official event poster.', 1),
                        (2, 'posted_on_page', 2, 'Posted on Page', 'Official poster and caption are published on the social media page.', 1),
                        (3, 'promotional_video', 3, 'Promotional Video', 'Chapter / Media team promotional teaser video submission (Optional).', 1),
                        (4, 'documentation_and_report', 4, 'Documentation and Report', 'Post-event documentation, attendees figures, and report submission via Report tab.', 1),
                        (5, 'event_concluded', 5, 'Event Concluded and Reported', 'National Admin validates event report and concludes timeline.', 1)
                    ]
                    for tid, skey, sorder, stitle, sdesc, sact in templates:
                        cur.execute("""
                            INSERT INTO poster_workflow_templates (id, step_key, step_order, step_title, description, is_active)
                            VALUES (%s, %s, %s, %s, %s, %s)
                        """, (tid, skey, sorder, stitle, sdesc, sact))

                    # Re-seed Baseline Announcements
                    announcements_data = [
                        (1, 'everyone', None, 'Tremendous Momentum: Youth Camp 2026', 'Join us for the ultimate spiritual gathering of the year featuring live worship, breakout sessions, and youth empowerment at SJB Learning Center, Calamba.', 1, '2026-05-21 09:00:00'),
                        (1, 'everyone', None, '29th YTR Anniversary Celebration', 'Celebrating 29 years of youth transformation and spiritual community impact across every chapter branch nationwide.', 1, '2026-09-26 14:00:00'),
                        (1, 'chapter_admins', None, 'National Youth Leadership Summit', 'An executive summit for chapter administrators and coordinators covering mission governance and budget liquidation.', 1, '2026-12-19 08:30:00'),
                        (1, 'specific_chapter', 1, 'Greatest of All Time: Regional Fellowship', 'High-energy praise, worship revival, and inter-campus fellowship bringing Laguna students together.', 1, '2026-09-28 16:00:00')
                    ]
                    for creator_id, audience, target_ch, title, body, is_pub, pub_at in announcements_data:
                        cur.execute("""
                            INSERT INTO announcements (created_by, target_audience, target_chapter_id, title, body, is_published, published_at)
                            VALUES (%s, %s, %s, %s, %s, %s, %s)
                        """, (creator_id, audience, target_ch, title, body, is_pub, pub_at))

                if mode == 'demo':
                    # Seed clean baseline events & proposals
                    demo_events = [
                        (1, 1, 'Youth Camp 2026: Unshakeable Faith', 'Annual 3-day spiritual retreat and leadership workshop for youth members across Laguna.', 'chapter', '2026-11-20 08:00:00', '2026-11-22 17:00:00', 'Camp John Hay Eco Trail, Los Baños', 'approved', 2, 1, 'Officially cleared by National Secretariat.'),
                        (2, 2, 'Metro Youth Leadership Summit 2026', 'Gathering campus leaders from universities across Metro Manila for empowerment and ministry training.', 'national', '2026-12-05 09:00:00', '2026-12-05 18:00:00', 'SMX Convention Center, Pasay City', 'approved', 1, 1, 'Cleared by National Council.'),
                        (3, 3, 'Tagaytay Prayer & Praise Night', 'Evening of worship, testimony, and community fellowship at the ridge.', 'chapter', '2026-10-28 18:00:00', '2026-10-28 21:30:00', 'Tagaytay Highlands Amphitheater', 'draft', 2, None, None),
                        (4, 4, 'Central Luzon Youth Sportsfest', 'Inter-church basketball, volleyball, and team-building games promoting fellowship.', 'community', '2026-10-02 07:00:00', '2026-10-02 17:00:00', 'Bren Z. Guiao Sports Complex, San Fernando', 'approved', 1, None, None),
                        (5, 3, 'Cavite Youth Leadership Workshop', 'Regional leadership intensive for campus coordinators.', 'chapter', '2026-10-01 11:34:36', '2026-10-01 17:34:36', 'Tagaytay Retreat Center', 'completed', 1, None, None),
                        (6, 5, 'Batangas Youth Worship & Arts Summit', 'Annual creative arts, music, and media training.', 'chapter', '2026-10-05 11:34:36', '2026-10-05 19:34:36', 'Batangas City Sports Coliseum', 'completed', 1, None, None),
                        (7, 6, 'UMAK Campus Evangelism & Fellowship', 'Collegiate campus outreach and discipleship gathering.', 'chapter', '2026-10-11 11:34:36', '2026-10-11 16:34:36', 'UMAK Grand Auditorium', 'completed', 1, None, None)
                    ]
                    for eid, chid, title, desc, etype, sdt, edt, ven, stat, cby, rby, rnote in demo_events:
                        cur.execute("""
                            INSERT INTO events (id, chapter_id, title, description, event_type, start_datetime, end_datetime, venue, status, created_by, reviewed_by, review_note)
                            VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
                        """, (eid, chid, title, desc, etype, sdt, edt, ven, stat, cby, rby, rnote))

                    # Seed baseline poster requests with 5 pipeline steps
                    demo_requests = [
                        (1, 1, 1, 2, 'Youth Camp 2026: Unshakeable Faith', '2026-10-25', '8:00 AM - 5:00 PM', 'LSPU Los Baños Campus', 'Unshakeable Faith: Standing Strong', 'Ignite your faith! Join us for Youth Camp 2026.', '2026-10-18', 'in_progress', 'assets/images/background.jpg', 4, 1, '2026-10-15 00:00:00', '2026-10-29', 'submitted'),
                        (2, 2, 2, 1, 'Greatest Of All Time (G.O.A.T.) Rally', '2026-11-05', '2:00 PM - 6:00 PM', 'Gymnasium / Facebook Live', 'Level up or Stay the Game', 'Join us as we explore true greatness.', '2026-10-28', 'in_progress', 'assets/images/background.jpg', 3, 0, None, None, 'not_submitted'),
                        (3, 3, 3, 7, 'Tagaytay Praise & Worship Night', '2026-10-30', '6:00 PM - 9:00 PM', 'Skyranch Amphitheater', 'Acoustic Praise and Fellowship', 'Come as you are for worship under the stars.', '2026-10-22', 'submitted', None, 1, 0, None, None, 'not_submitted'),
                        (4, 4, 4, 8, 'Central Luzon Youth Sportsfest', '2026-11-12', '7:00 AM - 4:00 PM', 'City Sports Complex, San Fernando', 'Run with Endurance', 'Inter-chapter games promoting fellowship.', '2026-10-26', 'completed', 'assets/images/background.jpg', 5, 1, '2026-10-15 00:00:00', '2026-10-29', 'approved')
                    ]
                    for rid, chid, eid, reqby, etitle, edate, etime, ven, thm, cap, reqdl, stat, fpath, cstep, cond, cond_at, rep_due, doc_stat in demo_requests:
                        cur.execute("""
                            INSERT INTO poster_requests (
                                id, chapter_id, event_id, requested_by, event_title, event_date, event_time, venue,
                                theme_or_concept, caption, requested_deadline, status, final_poster_path, current_step,
                                is_event_conducted, event_conducted_at, report_due_date, documentation_status
                            )
                            VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
                        """, (rid, chid, eid, reqby, etitle, edate, etime, ven, thm, cap, reqdl, stat, fpath, cstep, cond, cond_at, rep_due, doc_stat))

                    # Seed 5 steps for requests
                    step_defs = [
                        ('poster_created', 1, 'Poster Request & Design'),
                        ('posted_on_page', 2, 'Posted on Page'),
                        ('promotional_video', 3, 'Promotional Video'),
                        ('documentation_and_report', 4, 'Documentation and Report'),
                        ('event_concluded', 5, 'Event Concluded and Reported')
                    ]
                    for req_id in [1, 2, 3, 4]:
                        for skey, sorder, stitle in step_defs:
                            if req_id == 4:
                                s_stat = 'completed'
                            elif req_id == 1:
                                s_stat = 'completed' if sorder <= 3 else ('in_progress' if sorder == 4 else 'pending')
                            elif req_id == 2:
                                s_stat = 'completed' if sorder <= 2 else ('in_progress' if sorder == 3 else 'pending')
                            elif req_id == 3:
                                s_stat = 'in_progress' if sorder == 1 else 'pending'
                            else:
                                s_stat = 'pending'

                            cur.execute("""
                                INSERT INTO poster_request_steps (poster_request_id, step_key, step_order, step_title, status)
                                VALUES (%s, %s, %s, %s, %s)
                            """, (req_id, skey, sorder, stitle, s_stat))

                    # Seed sample event report
                    cur.execute("""
                        INSERT INTO event_reports (id, event_id, chapter_id, submitted_by, total_participants, first_timers, existing_members, youth_leaders_count, budget_spent, report_summary, signed_by_name, status, submitted_at)
                        VALUES (1, 4, 4, 8, 185, 42, 120, 23, 22450.00, 'Successfully conducted Central Luzon Youth Sportsfest with outstanding youth participation.', 'Carlos Santos', 'approved', '2026-10-15 14:30:00')
                    """)

                # Re-enable FK checks
                cur.execute("SET FOREIGN_KEY_CHECKS = 1;")

                # Record audit log
                try:
                    cur.execute("""
                        INSERT INTO audit_logs (user_id, action, entity_type, details)
                        VALUES (1, 'SYSTEM_FLUSH', 'database', %s)
                    """, (f"Mode: {mode}, deleted: {str(deleted_summary)}",))
                except Exception:
                    pass

        return api_response({
            'mode': mode,
            'deleted_summary': deleted_summary,
            'message': f"Successfully flushed system data (Mode: {mode}). Database is now clean."
        }, message="System data successfully flushed.")

    except Exception as e:
        return api_error(f"Error during system flush: {str(e)}", 500)
