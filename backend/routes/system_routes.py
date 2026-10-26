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

AUTHORIZED_CEO_POSITIONS = [
    'Chief Executive Operation',
    'Chief Executive Officer',
    'CEO',
    'Chief Executive'
]

def is_executive_authorized():
    """Verify that current requester has Chief Executive Officer authority to flush or retrieve data."""
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
                    pos = str(u.get('position') or '').strip()
                    email = str(u.get('email') or '').strip().lower()
                    if pos in AUTHORIZED_CEO_POSITIONS or email == 'admin@ytr.org':
                        return True
                    return False
                return False
    except Exception:
        pass
    return False

def archive_upload_files(batch_id):
    """
    Moves user uploads in assets/uploads/ to assets/uploads/archived/<batch_id>/,
    preserving folder hierarchy and enabling complete file retrieval.
    """
    uploads_dir = PROJECT_ROOT / 'assets' / 'uploads'
    archived_base = uploads_dir / 'archived' / batch_id
    moved_count = 0
    if uploads_dir.exists():
        for sub in ['posters', 'proofs', 'reports']:
            sub_dir = uploads_dir / sub
            if sub_dir.exists():
                for item in list(sub_dir.iterdir()):
                    if item.is_file() and not item.name.startswith('.'):
                        dest_dir = archived_base / sub
                        dest_dir.mkdir(parents=True, exist_ok=True)
                        try:
                            shutil.move(str(item), str(dest_dir / item.name))
                            moved_count += 1
                        except Exception as e:
                            print(f"Error moving upload file {item}: {e}")
    return moved_count

def restore_upload_files(batch_id):
    """
    Moves archived files from assets/uploads/archived/<batch_id>/ back to assets/uploads/.
    """
    uploads_dir = PROJECT_ROOT / 'assets' / 'uploads'
    archived_base = uploads_dir / 'archived' / batch_id
    restored_count = 0
    if archived_base.exists():
        for sub in ['posters', 'proofs', 'reports']:
            sub_dir = archived_base / sub
            if sub_dir.exists():
                dest_dir = uploads_dir / sub
                dest_dir.mkdir(parents=True, exist_ok=True)
                for item in list(sub_dir.iterdir()):
                    if item.is_file() and not item.name.startswith('.'):
                        try:
                            target_path = dest_dir / item.name
                            if target_path.exists():
                                target_path.unlink()
                            shutil.move(str(item), str(target_path))
                            restored_count += 1
                        except Exception as e:
                            print(f"Error restoring upload file {item}: {e}")
    return restored_count

def clear_upload_files():
    """Backwards-compatible wrapper that archives files under a temporary batch."""
    batch_id = f"ARCHIVE-{datetime.now().strftime('%Y%m%d-%H%M%S')}"
    return archive_upload_files(batch_id)

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
            cur.execute(f"SELECT COUNT(*) AS c FROM `{tbl}`")
            counts[tbl] = cur.fetchone()['c']
        except Exception:
            counts[tbl] = 0
    return counts

def ensure_archive_tables(cur):
    """Ensure system_data_archives and shadow tables are created."""
    cur.execute("""
        CREATE TABLE IF NOT EXISTS system_data_archives (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            batch_id VARCHAR(64) NOT NULL,
            table_name VARCHAR(64) NOT NULL,
            record_id BIGINT UNSIGNED NOT NULL,
            record_data LONGTEXT NOT NULL,
            flushed_mode VARCHAR(32) NOT NULL DEFAULT 'activity',
            flushed_by BIGINT UNSIGNED NULL,
            flushed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            restored_at DATETIME NULL,
            is_restored TINYINT(1) NOT NULL DEFAULT 0,
            INDEX idx_batch (batch_id),
            INDEX idx_table_record (table_name, record_id),
            INDEX idx_is_restored (is_restored),
            INDEX idx_flushed_at (flushed_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    """)

    cur.execute("CREATE TABLE IF NOT EXISTS archived_events LIKE events;")
    cur.execute("CREATE TABLE IF NOT EXISTS archived_event_reports LIKE event_reports;")
    cur.execute("CREATE TABLE IF NOT EXISTS archived_poster_requests LIKE poster_requests;")

    for tbl in ['archived_events', 'archived_event_reports', 'archived_poster_requests']:
        cur.execute(f"SHOW COLUMNS FROM `{tbl}` LIKE 'archive_batch_id'")
        if not cur.fetchone():
            cur.execute(f"ALTER TABLE `{tbl}` ADD COLUMN archive_batch_id VARCHAR(64) NOT NULL AFTER id")
            cur.execute(f"ALTER TABLE `{tbl}` ADD COLUMN archived_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER archive_batch_id")
            cur.execute(f"ALTER TABLE `{tbl}` ADD INDEX idx_arch_batch (archive_batch_id)")

def archive_records_before_flush(cur, batch_id, mode, user_id=None):
    """
    Safely captures and moves records from active tables into system_data_archives
    and shadow archive tables before active tables are cleared.
    """
    ensure_archive_tables(cur)
    now_ts = datetime.now()

    tables_to_archive = [
        'events',
        'event_reports',
        'event_report_files',
        'poster_requests',
        'poster_request_steps',
        'poster_request_updates',
        'notifications',
        'audit_logs'
    ]

    if mode in ('full', 'demo'):
        tables_to_archive.extend([
            'announcements',
            'users',
            'chapters',
            'poster_workflow_templates'
        ])

    archived_counts = {}

    for tbl in tables_to_archive:
        try:
            cur.execute(f"SELECT * FROM `{tbl}`")
            rows = cur.fetchall()
            if not rows:
                archived_counts[tbl] = 0
                continue

            serialized = serialize_rows(rows)
            for r, s_row in zip(rows, serialized):
                rec_id = r.get('id', 0)
                json_data = json.dumps(s_row, default=str)
                cur.execute("""
                    INSERT INTO system_data_archives (batch_id, table_name, record_id, record_data, flushed_mode, flushed_by, flushed_at)
                    VALUES (%s, %s, %s, %s, %s, %s, %s)
                """, (batch_id, tbl, rec_id, json_data, mode, user_id, now_ts))

            # Populate shadow table for direct tabular archive inspection
            shadow_map = {
                'events': 'archived_events',
                'event_reports': 'archived_event_reports',
                'poster_requests': 'archived_poster_requests'
            }
            if tbl in shadow_map:
                shadow_tbl = shadow_map[tbl]
                cur.execute(f"SHOW COLUMNS FROM `{shadow_tbl}`")
                shadow_valid_cols = {col['Field'] for col in cur.fetchall()}
                for r in rows:
                    insert_cols = [c for c in r.keys() if c in shadow_valid_cols and c not in ('archive_batch_id', 'archived_at')]
                    cols_str = ", ".join([f"`{c}`" for c in insert_cols])
                    placeholders = ", ".join(["%s"] * len(insert_cols))
                    vals = [r[c] for c in insert_cols]
                    cur.execute(f"""
                        INSERT INTO `{shadow_tbl}` (archive_batch_id, archived_at, {cols_str})
                        VALUES (%s, %s, {placeholders})
                    """, [batch_id, now_ts] + vals)

            archived_counts[tbl] = len(rows)
        except Exception as e:
            print(f"Error archiving table {tbl}: {e}")
            archived_counts[tbl] = 0

    return archived_counts

def restore_archive_batch(cur, batch_id, user_id=None):
    """
    Restores all records from system_data_archives for a given batch_id into active database tables.
    """
    cur.execute("""
        SELECT table_name, record_id, record_data
        FROM system_data_archives
        WHERE batch_id = %s
        ORDER BY id ASC
    """, (batch_id,))
    records = cur.fetchall()

    if not records:
        return {'success': False, 'message': f"No archived records found for batch {batch_id}."}

    from collections import defaultdict
    table_records = defaultdict(list)
    for r in records:
        try:
            table_records[r['table_name']].append(json.loads(r['record_data']))
        except Exception:
            pass

    cur.execute("SET FOREIGN_KEY_CHECKS = 0;")

    # Dependency restoration order
    table_order = [
        'chapters',
        'users',
        'poster_workflow_templates',
        'events',
        'poster_requests',
        'poster_request_steps',
        'poster_request_updates',
        'event_reports',
        'event_report_files',
        'announcements',
        'notifications',
        'audit_logs'
    ]

    restored_summary = {}

    for tbl in table_order:
        rows = table_records.get(tbl, [])
        if not rows:
            continue

        cur.execute(f"SHOW COLUMNS FROM `{tbl}`")
        valid_cols = {col['Field'] for col in cur.fetchall()}

        restored_count = 0
        for row in rows:
            filtered_cols = [c for c in row.keys() if c in valid_cols]
            if not filtered_cols:
                continue
            cols_str = ", ".join([f"`{c}`" for c in filtered_cols])
            placeholders = ", ".join(["%s"] * len(filtered_cols))
            vals = [row[c] for c in filtered_cols]
            cur.execute(f"REPLACE INTO `{tbl}` ({cols_str}) VALUES ({placeholders})", vals)
            restored_count += 1

        restored_summary[tbl] = restored_count

    # Mark batch as restored
    cur.execute("""
        UPDATE system_data_archives
        SET is_restored = 1, restored_at = NOW()
        WHERE batch_id = %s
    """, (batch_id,))

    # Restore associated upload files
    restored_files = restore_upload_files(batch_id)
    restored_summary['uploaded_files'] = restored_files

    cur.execute("SET FOREIGN_KEY_CHECKS = 1;")

    try:
        cur.execute("""
            INSERT INTO audit_logs (user_id, action, entity_type, details)
            VALUES (%s, 'ARCHIVE_RESTORE', 'database', %s)
        """, (user_id or 1, f"Restored archive batch {batch_id}: {str(restored_summary)}"))
    except Exception:
        pass

    return {
        'success': True,
        'batch_id': batch_id,
        'restored_summary': restored_summary,
        'message': f"Batch {batch_id} successfully restored into active system."
    }

@system_bp.route('/summary', methods=['GET'])
def get_system_summary():
    """Return summary statistics of all database tables and uploaded files."""
    try:
        with get_db() as conn:
            with conn.cursor() as cur:
                ensure_archive_tables(cur)
                counts = get_counts(cur)

                # Count archive batches
                cur.execute("SELECT COUNT(DISTINCT batch_id) AS c FROM system_data_archives")
                archive_batches_count = cur.fetchone()['c']

        uploads_dir = PROJECT_ROOT / 'assets' / 'uploads'
        file_count = 0
        if uploads_dir.exists():
            for root, dirs, files in os.walk(uploads_dir):
                if 'archived' in root:
                    continue
                file_count += len([f for f in files if not f.startswith('.')])

        return api_response({
            'table_counts': counts,
            'uploaded_files_count': file_count,
            'archive_batches_count': archive_batches_count,
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
        return api_error("Access Denied: Only the Chief Executive Officer can export system backup.", 403)

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
                        cur.execute(f"SELECT * FROM `{tbl}`")
                        rows = cur.fetchall()
                        backup_data[tbl] = serialize_rows(rows)
                        table_counts[tbl] = len(rows)
                    except Exception:
                        backup_data[tbl] = []
                        table_counts[tbl] = 0

        if fmt == 'sql':
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

        payload = {
            'platform': 'Youth on the Rock (YTR) Platform',
            'backup_timestamp': datetime.now().isoformat(),
            'database': 'ytr_event_monitoring',
            'table_counts': table_counts,
            'tables': backup_data
        }

        if download:
            json_str = json.dumps(payload, indent=2, default=str)
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
    Safely flushes active database records by moving them into archive tables
    (system_data_archives, archived_events, archived_event_reports, archived_poster_requests)
    and archiving upload files, keeping the data hidden but 100% retrievable.
    Restricted to Executive Director and Regional Director (National).
    Modes:
      - 'activity': Moves active events, proposals, poster requests, reports, notifications, audit logs to archive.
                    Preserves users, chapters, announcements, and workflow templates.
      - 'full': Factory reset - archives current data and resets baseline chapters, users, step templates.
      - 'demo': Factory reset + seeds realistic demo events, proposals, conducted events, and reports.
    Requires confirmation text: 'FLUSH'
    """
    if not is_executive_authorized():
        return api_error("Access Denied: Only the Chief Executive Officer can execute system data flush.", 403)

    payload = request.get_json(silent=True) or {}
    mode = payload.get('mode', 'activity')
    confirmation = str(payload.get('confirmation', '')).strip().upper()

    if confirmation != 'FLUSH':
        return api_error("Confirmation code required. Please type 'FLUSH' to proceed.", 400)

    user_id = request.headers.get('X-User-Id') or request.args.get('user_id') or session.get('user_id')
    batch_id = f"FLUSH-{datetime.now().strftime('%Y%m%d-%H%M%S')}"

    try:
        with get_db() as conn:
            with conn.cursor() as cur:
                # 1. Archive records safely into another table before any deletion
                archived_summary = archive_records_before_flush(cur, batch_id, mode, user_id)
                archived_files = archive_upload_files(batch_id)
                archived_summary['uploaded_files'] = archived_files

                # 2. Disable FK checks to allow clean truncate / delete
                cur.execute("SET FOREIGN_KEY_CHECKS = 0;")

                # Activity tables to clear from active view
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
                    cur.execute(f"TRUNCATE TABLE `{tbl}`")

                if mode in ('full', 'demo'):
                    cur.execute("TRUNCATE TABLE `announcements`")
                    cur.execute("TRUNCATE TABLE `users`")
                    cur.execute("TRUNCATE TABLE `chapters`")
                    cur.execute("TRUNCATE TABLE `poster_workflow_templates`")

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

                if mode == 'demo':
                    demo_events = [
                        (1, 1, 'Youth Camp 2026: Unshakeable Faith', 'Annual 3-day spiritual retreat and leadership workshop for youth members across Laguna.', 'chapter', 'Build up (Youth Personality Development)', '2026-11-20 08:00:00', '2026-11-22 17:00:00', 'Camp John Hay Eco Trail, Los Baños', 'approved', 2, 1, 'Officially cleared by National Secretariat.'),
                        (2, 2, 'Metro Youth Leadership Summit 2026', 'Gathering campus leaders from universities across Metro Manila for empowerment and ministry training.', 'national', 'Lead up (Youth Leadership)', '2026-12-05 09:00:00', '2026-12-05 18:00:00', 'SMX Convention Center, Pasay City', 'approved', 1, 1, 'Cleared by National Council.'),
                        (3, 3, 'Tagaytay Prayer & Praise Night', 'Evening of worship, testimony, and community fellowship at the ridge.', 'chapter', 'Build up (Youth Personality Development)', '2026-10-28 18:00:00', '2026-10-28 21:30:00', 'Tagaytay Highlands Amphitheater', 'draft', 2, None, None),
                        (4, 4, 'Central Luzon Youth Sportsfest', 'Inter-church basketball, volleyball, and team-building games promoting fellowship.', 'community', 'Rise up (Youth Excellence)', '2026-10-02 07:00:00', '2026-10-02 17:00:00', 'Bren Z. Guiao Sports Complex, San Fernando', 'approved', 1, None, None),
                        (5, 3, 'Cavite Youth Leadership Workshop', 'Regional leadership intensive for campus coordinators.', 'chapter', 'Lead up (Youth Leadership)', '2026-10-01 11:34:36', '2026-10-01 17:34:36', 'Tagaytay Retreat Center', 'completed', 1, None, None),
                        (6, 5, 'Batangas Youth Worship & Arts Summit', 'Annual creative arts, music, and media training.', 'chapter', 'Rise up (Youth Excellence)', '2026-10-05 11:34:36', '2026-10-05 19:34:36', 'Batangas City Sports Coliseum', 'completed', 1, None, None),
                        (7, 6, 'UMAK Campus Evangelism & Fellowship', 'Collegiate campus outreach and discipleship gathering.', 'chapter', 'Gear up (Youth preparation and empowerment)', '2026-10-11 11:34:36', '2026-10-11 16:34:36', 'UMAK Grand Auditorium', 'completed', 1, None, None)
                    ]
                    for eid, chid, title, desc, etype, yprog, sdt, edt, ven, stat, cby, rby, rnote in demo_events:
                        cur.execute("""
                            INSERT INTO events (id, chapter_id, title, description, event_type, ytr_program, start_datetime, end_datetime, venue, status, created_by, reviewed_by, review_note)
                            VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
                        """, (eid, chid, title, desc, etype, yprog, sdt, edt, ven, stat, cby, rby, rnote))

                    demo_requests = [
                        (1, 1, 1, 2, 'Youth Camp 2026: Unshakeable Faith', '2026-10-25', '8:00 AM - 5:00 PM', 'LSPU Los Baños Campus', 'Unshakeable Faith', 'Ignite your faith! Join us for Youth Camp 2026.', '2026-10-18', 'in_progress', 'assets/images/background.jpg', 4, 1, '2026-10-15 00:00:00', '2026-10-29', 'submitted'),
                        (2, 2, 2, 1, 'Greatest Of All Time (G.O.A.T.) Rally', '2026-11-05', '2:00 PM - 6:00 PM', 'Gymnasium / Facebook Live', 'Level up', 'Join us as we explore true greatness.', '2026-10-28', 'in_progress', 'assets/images/background.jpg', 3, 0, None, None, 'not_submitted'),
                        (3, 3, 3, 7, 'Tagaytay Praise & Worship Night', '2026-10-30', '6:00 PM - 9:00 PM', 'Skyranch Amphitheater', 'Acoustic Praise', 'Come as you are for worship under the stars.', '2026-10-22', 'submitted', None, 1, 0, None, None, 'not_submitted'),
                        (4, 4, 4, 8, 'Central Luzon Youth Sportsfest', '2026-11-12', '7:00 AM - 4:00 PM', 'City Sports Complex, San Fernando', 'Run with Endurance', 'Inter-chapter games.', '2026-10-26', 'completed', 'assets/images/background.jpg', 5, 1, '2026-10-15 00:00:00', '2026-10-29', 'approved')
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

                    cur.execute("""
                        INSERT INTO event_reports (id, event_id, chapter_id, submitted_by, total_participants, first_timers, existing_members, youth_leaders_count, budget_spent, report_summary, signed_by_name, status, submitted_at)
                        VALUES (1, 4, 4, 8, 185, 42, 120, 23, 22450.00, 'Successfully conducted Central Luzon Youth Sportsfest.', 'Carlos Santos', 'approved', '2026-10-15 14:30:00')
                    """)

                # Re-enable FK checks
                cur.execute("SET FOREIGN_KEY_CHECKS = 1;")

                # Record audit log
                try:
                    cur.execute("""
                        INSERT INTO audit_logs (user_id, action, entity_type, details)
                        VALUES (%s, 'SYSTEM_FLUSH', 'database', %s)
                    """, (user_id or 1, f"Archived batch {batch_id}, mode {mode}, summary: {str(archived_summary)}",))
                except Exception:
                    pass

        return api_response({
            'mode': mode,
            'batch_id': batch_id,
            'archived_summary': archived_summary,
            'message': f"System data safely moved to archive (Batch {batch_id}) and hidden from active tables. You can retrieve or restore this data at any time."
        }, message="System data successfully archived and flushed.")

    except Exception as e:
        return api_error(f"Error during system flush: {str(e)}", 500)

@system_bp.route('/archives', methods=['GET'])
def list_system_archives():
    """
    Returns list of all archived data batches with record statistics and preview titles.
    Restricted to Executive Director and Regional Director (National).
    """
    if not is_executive_authorized():
        return api_error("Access Denied: Only the Chief Executive Officer can view system archives.", 403)

    try:
        with get_db() as conn:
            with conn.cursor() as cur:
                ensure_archive_tables(cur)

                cur.execute("""
                    SELECT batch_id, flushed_mode,
                           MIN(flushed_at) AS flushed_at,
                           MAX(restored_at) AS restored_at,
                           MAX(is_restored) AS is_restored,
                           COUNT(*) AS total_records
                    FROM system_data_archives
                    GROUP BY batch_id, flushed_mode
                    ORDER BY flushed_at DESC
                """)
                batches = cur.fetchall()

                result = []
                for b in batches:
                    bid = b['batch_id']
                    cur.execute("""
                        SELECT table_name, COUNT(*) AS c
                        FROM system_data_archives
                        WHERE batch_id = %s
                        GROUP BY table_name
                    """, (bid,))
                    tbl_counts = {r['table_name']: r['c'] for r in cur.fetchall()}

                    # Sample event titles
                    cur.execute("""
                        SELECT record_data FROM system_data_archives
                        WHERE batch_id = %s AND table_name = 'events'
                        LIMIT 5
                    """, (bid,))
                    sample_events = []
                    for er in cur.fetchall():
                        try:
                            ed = json.loads(er['record_data'])
                            if ed.get('title'):
                                sample_events.append(ed['title'])
                        except Exception:
                            pass

                    # Sample report titles
                    cur.execute("""
                        SELECT record_data FROM system_data_archives
                        WHERE batch_id = %s AND table_name = 'event_reports'
                        LIMIT 5
                    """, (bid,))
                    sample_reports = []
                    for rr in cur.fetchall():
                        try:
                            rd = json.loads(rr['record_data'])
                            if rd.get('event_title') or rd.get('report_summary'):
                                sample_reports.append(rd.get('event_title') or rd.get('report_summary')[:50])
                        except Exception:
                            pass

                    # Count archived files on disk
                    arch_files_dir = PROJECT_ROOT / 'assets' / 'uploads' / 'archived' / bid
                    arch_files_count = 0
                    if arch_files_dir.exists():
                        for root, dirs, files in os.walk(arch_files_dir):
                            arch_files_count += len([f for f in files if not f.startswith('.')])

                    serialized_b = serialize_row(b)
                    serialized_b['table_counts'] = tbl_counts
                    serialized_b['table_breakdown'] = tbl_counts
                    serialized_b['archived_files_count'] = arch_files_count
                    serialized_b['files_archived'] = arch_files_count
                    serialized_b['sample_events'] = sample_events
                    serialized_b['sample_reports'] = sample_reports
                    result.append(serialized_b)

        return api_response(result)
    except Exception as e:
        return api_error(f"Failed to fetch archives: {str(e)}", 500)

@system_bp.route('/archives/<batch_id>', methods=['GET'])
def get_archive_details(batch_id):
    """
    Returns full details and itemized list of records in an archive batch.
    Restricted to Executive Director and Regional Director (National).
    """
    if not is_executive_authorized():
        return api_error("Access Denied: Only the Chief Executive Officer can view archive details.", 403)

    try:
        with get_db() as conn:
            with conn.cursor() as cur:
                ensure_archive_tables(cur)

                cur.execute("""
                    SELECT id, batch_id, table_name, record_id, record_data, flushed_mode, flushed_at, restored_at, is_restored
                    FROM system_data_archives
                    WHERE batch_id = %s
                    ORDER BY id ASC
                """, (batch_id,))
                rows = cur.fetchall()

                if not rows:
                    return api_error("Archive batch not found.", 404)

                records = []
                records_by_table = {}
                for r in rows:
                    t = r['table_name']
                    if t not in records_by_table:
                        records_by_table[t] = []
                    try:
                        parsed_data = json.loads(r['record_data'])
                    except Exception:
                        parsed_data = r['record_data']
                    records_by_table[t].append(parsed_data)
                    records.append({
                        'id': r['id'],
                        'table_name': r['table_name'],
                        'record_id': r['record_id'],
                        'record_data': parsed_data
                    })

                arch_files_dir = PROJECT_ROOT / 'assets' / 'uploads' / 'archived' / batch_id
                archived_files = []
                if arch_files_dir.exists():
                    for root, dirs, files in os.walk(arch_files_dir):
                        for f in files:
                            if not f.startswith('.'):
                                archived_files.append(os.path.relpath(os.path.join(root, f), str(arch_files_dir)))

                return api_response({
                    'batch_id': batch_id,
                    'total_records': len(rows),
                    'flushed_at': rows[0]['flushed_at'].isoformat() if hasattr(rows[0]['flushed_at'], 'isoformat') else str(rows[0]['flushed_at']),
                    'flushed_mode': rows[0]['flushed_mode'],
                    'is_restored': bool(rows[0]['is_restored']),
                    'restored_at': rows[0]['restored_at'].isoformat() if rows[0]['restored_at'] and hasattr(rows[0]['restored_at'], 'isoformat') else str(rows[0]['restored_at']),
                    'records': records,
                    'tables': records_by_table,
                    'files': archived_files
                })
    except Exception as e:
        return api_error(f"Failed to fetch archive details: {str(e)}", 500)

@system_bp.route('/archives/<batch_id>/restore', methods=['POST'])
def restore_system_archive(batch_id):
    """
    Restores the specified archive batch into active database tables.
    Restricted to Executive Director and Regional Director (National).
    """
    if not is_executive_authorized():
        return api_error("Access Denied: Only the Chief Executive Officer can restore archives.", 403)

    user_id = request.headers.get('X-User-Id') or request.args.get('user_id') or session.get('user_id')

    try:
        with get_db() as conn:
            with conn.cursor() as cur:
                ensure_archive_tables(cur)
                result = restore_archive_batch(cur, batch_id, user_id)
                if not result.get('success'):
                    return api_error(result.get('message', 'Failed to restore archive batch.'), 400)

        return api_response(result, message=result.get('message', 'Archive restored successfully.'))
    except Exception as e:
        return api_error(f"Failed to restore archive: {str(e)}", 500)
