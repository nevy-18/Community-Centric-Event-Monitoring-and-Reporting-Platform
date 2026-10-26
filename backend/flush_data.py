"""
Youth on the Rock (YTR) - Database Flush, Archive and Retrieval Utility CLI
Usage:
    python backend/flush_data.py                  # Interactive flush prompt (auto-archives data)
    python backend/flush_data.py --mode activity   # Flush operational data (preserves in archive table)
    python backend/flush_data.py --mode full       # Factory reset to baseline chapters & users (archives prior data)
    python backend/flush_data.py --mode demo       # Reset and seed demo dataset (archives prior data)
    python backend/flush_data.py --list            # List all past archive batches
    python backend/flush_data.py --restore BATCH_ID# Restore archived batch into active database
    python backend/flush_data.py --yes             # Non-interactive confirmation
"""
import sys
import argparse
from datetime import datetime
from pathlib import Path

ROOT_DIR = Path(__file__).resolve().parent.parent
if str(ROOT_DIR) not in sys.path:
    sys.path.insert(0, str(ROOT_DIR))

from backend.routes.system_routes import (
    get_counts,
    archive_records_before_flush,
    archive_upload_files,
    restore_archive_batch,
    ensure_archive_tables
)
from backend.db import get_db
from backend.auth_helpers import hash_password

def list_archives():
    print("=" * 65)
    print(">> YTR SYSTEM ARCHIVES LEDGER")
    print("=" * 65)
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
            if not batches:
                print("No archive batches found in database.")
                return

            for b in batches:
                status = "[RESTORED]" if b['is_restored'] else "[ARCHIVED/HIDDEN]"
                print(f"Batch: {b['batch_id']} | Mode: {b['flushed_mode']} | Records: {b['total_records']} | Status: {status}")
                print(f"  Flushed at: {b['flushed_at']}")
                if b['restored_at']:
                    print(f"  Restored at: {b['restored_at']}")
                print("-" * 50)

def run_restore(batch_id):
    print("=" * 65)
    print(f">> RESTORING ARCHIVED BATCH: {batch_id}")
    print("=" * 65)
    with get_db() as conn:
        with conn.cursor() as cur:
            ensure_archive_tables(cur)
            res = restore_archive_batch(cur, batch_id, user_id=1)
            if res.get('success'):
                print(f"[SUCCESS] {res.get('message')}")
                print("Restored records summary:")
                for tbl, cnt in res.get('restored_summary', {}).items():
                    print(f"  - {tbl}: {cnt}")
            else:
                print(f"[ERROR] {res.get('message')}")

def run_flush(mode='activity', verbose=True):
    batch_id = f"FLUSH-{datetime.now().strftime('%Y%m%d-%H%M%S')}"

    if verbose:
        print("=" * 65)
        print(f">> YTR PLATFORM: NON-DESTRUCTIVE SYSTEM DATA FLUSH (Mode: {mode.upper()})")
        print(f">> Archive Batch ID: {batch_id}")
        print("=" * 65)

    with get_db() as conn:
        with conn.cursor() as cur:
            # 1. Print before stats
            before_counts = get_counts(cur)
            if verbose:
                print(">> Current database records:")
                for k, v in before_counts.items():
                    print(f"   - {k}: {v}")

            # 2. Archive active records safely into another table before any clearing
            archived_summary = archive_records_before_flush(cur, batch_id, mode, user_id=1)
            archived_files = archive_upload_files(batch_id)

            if verbose:
                print(f">> Safely archived {sum(archived_summary.values())} record(s) and {archived_files} file(s) into archive tables.")
                for tbl, cnt in archived_summary.items():
                    if cnt > 0:
                        print(f"   * {tbl}: {cnt} preserved")

            cur.execute("SET FOREIGN_KEY_CHECKS = 0;")

            # Activity tables to clear from active display
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

                # Chapters
                chapters = [
                    (1, 'YTR-LAG', 'Laguna Chapter', 'Gen One', 'Region IV-A', 'Laguna', 'Los Baños', 'Elena Reyes'),
                    (2, 'YTR-NCR', 'NCR Chapter', 'Gen Two', 'NCR', 'Metro Manila', 'Quezon City', 'Admin Host'),
                    (3, 'YTR-CAV', 'Cavite Chapter', 'Gen One', 'Region IV-A', 'Cavite', 'Tagaytay', 'Elena Reyes'),
                    (4, 'YTR-PAM', 'Pampanga Chapter', 'Gen Three', 'Region III', 'Pampanga', 'San Fernando', 'Carlos Santos'),
                    (5, 'YTR-BAT', 'Batangas Chapter', 'Gen Two', 'Region IV-A', 'Batangas', 'Batangas City', 'Mateo Hernandez'),
                    (6, 'YTR-UMAK', 'University of Makati Chapter', 'Gen Two', 'NCR', 'Metro Manila', 'Makati City', 'Admin Host'),
                ]
                for cid, code, name, gen, reg, prov, city, rd in chapters:
                    cur.execute("""
                        INSERT INTO chapters (id, chapter_code, chapter_name, generation_name, region, province, city_municipality, regional_director_name, status)
                        VALUES (%s, %s, %s, %s, %s, %s, %s, %s, 'active')
                    """, (cid, code, name, gen, reg, prov, city, rd))

                # Users
                pw_hash = hash_password('Password123!')
                users = [
                    (1, None, 'Hamilton Jones Dela', 'admin@ytr.org', pw_hash, 'national_admin', 'Executive Director', 'active'),
                    (2, 1, 'Joan Dela Cruz', 'joan.dela@gmail.com', pw_hash, 'chapter_admin', 'Chapter Coordinator', 'active'),
                    (3, 2, 'Mark Bautista', 'mark.bautista@gmail.com', pw_hash, 'chapter_admin', 'Chapter President', 'pending'),
                    (7, 3, 'Elena Reyes', 'elena.reyes@gmail.com', pw_hash, 'chapter_admin', 'Regional Director (Chapter)', 'pending'),
                    (8, 4, 'Carlos Santos', 'carlos.santos@gmail.com', pw_hash, 'chapter_admin', 'Chapter Coordinator', 'rejected'),
                    (12, 1, 'rayvhen P Munoz', 'munozrayvhen@gmail.com', pw_hash, 'chapter_admin', 'Chapter Coordinator', 'active'),
                    (13, None, 'Admin Host', 'adminhost@gmail.com', pw_hash, 'national_admin', 'Regional Director (National)', 'active'),
                ]
                for uid, ch_id, name, email, pw, role, pos, status in users:
                    cur.execute("""
                        INSERT INTO users (id, chapter_id, full_name, email, password_hash, role, position, account_status)
                        VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
                    """, (uid, ch_id, name, email, pw, role, pos, status))

                # Workflow templates
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

            cur.execute("SET FOREIGN_KEY_CHECKS = 1;")

            if verbose:
                print(f"[PASS] Active operational tables cleared (records safely preserved in archive {batch_id})!")
                print(f"[PASS] To retrieve this data, run: python backend/flush_data.py --restore {batch_id}")
                after_counts = get_counts(cur)
                print(">> Post-flush active database records:")
                for k, v in after_counts.items():
                    print(f"   - {k}: {v}")

def main():
    parser = argparse.ArgumentParser(description="Non-destructive Flush, Archive and Retrieval for YTR Platform Data.")
    parser.add_argument('--mode', choices=['activity', 'full', 'demo'], default='activity',
                        help="Flush mode: 'activity' (clear events/reports/requests), 'full' (factory reset), 'demo' (reset + seed sample events)")
    parser.add_argument('--list', action='store_true',
                        help="List all past archive batches")
    parser.add_argument('--restore', type=str, default=None,
                        help="Batch ID to restore into active database (e.g. FLUSH-20261027-013000)")
    parser.add_argument('--yes', '-y', action='store_true',
                        help="Skip interactive confirmation")

    args = parser.parse_args()

    if args.list:
        list_archives()
        return

    if args.restore:
        run_restore(args.restore)
        return

    if not args.yes:
        print(f"NOTICE: This will move active {args.mode.upper()} data to the archive tables and hide it from the active system.")
        print("Data is NOT destroyed and can be retrieved at any time.")
        confirm = input("Type 'FLUSH' to proceed: ").strip()
        if confirm != 'FLUSH':
            print("Aborted.")
            sys.exit(1)

    run_flush(mode=args.mode, verbose=True)

if __name__ == '__main__':
    main()
