"""
Youth on the Rock (YTR) - Database Flush and Reset Utility CLI
Usage:
    python backend/flush_data.py                  # Interactive prompt
    python backend/flush_data.py --mode activity   # Flush operational data only (keep users & chapters)
    python backend/flush_data.py --mode full       # Factory reset to baseline chapters & users
    python backend/flush_data.py --mode demo       # Reset and seed full demo dataset with sample events
    python backend/flush_data.py --yes             # Non-interactive confirmation
"""
import sys
import argparse
from pathlib import Path

ROOT_DIR = Path(__file__).resolve().parent.parent
if str(ROOT_DIR) not in sys.path:
    sys.path.insert(0, str(ROOT_DIR))

from backend.routes.system_routes import get_counts, clear_upload_files
from backend.db import get_db
from backend.auth_helpers import hash_password

def run_flush(mode='activity', verbose=True):
    if verbose:
        print("=" * 60)
        print(f">> YTR PLATFORM: SYSTEM DATA FLUSH (Mode: {mode.upper()})")
        print("=" * 60)

    with get_db() as conn:
        with conn.cursor() as cur:
            # 1. Print before stats
            before_counts = get_counts(cur)
            if verbose:
                print(">> Current database records:")
                for k, v in before_counts.items():
                    print(f"   - {k}: {v}")

            cur.execute("SET FOREIGN_KEY_CHECKS = 0;")

            # Activity tables
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
                cur.execute(f"TRUNCATE TABLE {tbl}")

            deleted_files = clear_upload_files()

            if mode in ('full', 'demo'):
                cur.execute("TRUNCATE TABLE announcements")
                cur.execute("TRUNCATE TABLE users")
                cur.execute("TRUNCATE TABLE chapters")
                cur.execute("TRUNCATE TABLE poster_workflow_templates")

                # Chapters
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

                # Users
                pw_hash = hash_password('Password123!')
                users = [
                    (1, None, 'Hamilton Jones Dela', 'admin@ytr.org', pw_hash, 'national_admin', 'active'),
                    (2, 1, 'Joan Dela Cruz', 'joan.dela@gmail.com', pw_hash, 'chapter_admin', 'active'),
                    (3, 2, 'Mark Bautista', 'mark.bautista@gmail.com', pw_hash, 'chapter_admin', 'pending'),
                    (7, 3, 'Elena Reyes', 'elena.reyes@gmail.com', pw_hash, 'chapter_admin', 'pending'),
                    (8, 4, 'Carlos Santos', 'carlos.santos@gmail.com', pw_hash, 'chapter_admin', 'rejected'),
                    (12, 1, 'rayvhen P Munoz', 'munozrayvhen@gmail.com', pw_hash, 'chapter_admin', 'active'),
                    (13, None, 'Admin Host', 'adminhost@gmail.com', pw_hash, 'national_admin', 'active'),
                ]
                for uid, ch_id, name, email, pw, role, status in users:
                    cur.execute("""
                        INSERT INTO users (id, chapter_id, full_name, email, password_hash, role, account_status)
                        VALUES (%s, %s, %s, %s, %s, %s, %s)
                    """, (uid, ch_id, name, email, pw, role, status))

                # Workflow templates
                templates = [
                    (1, 'poster_created', 1, 'Poster created', 'National prepares the event poster.', 1),
                    (2, 'waiting_for_caption', 2, 'Waiting for caption', 'The event caption is requested or being finalized.', 1),
                    (3, 'posted_on_page', 3, 'Posted on page', 'The event poster has been published on the page.', 1),
                    (4, 'promotional_video', 4, 'Promotional video', 'Promotional video preparation or publication.', 1),
                    (5, 'documentation_and_report', 5, 'Documentation and report', 'Event documentation and report are being prepared.', 1),
                    (6, 'event_conducted_and_reported', 6, 'Event conducted and reported', 'The event has taken place and its report has been submitted.', 1)
                ]
                for tid, skey, sorder, stitle, sdesc, sact in templates:
                    cur.execute("""
                        INSERT INTO poster_workflow_templates (id, step_key, step_order, step_title, description, is_active)
                        VALUES (%s, %s, %s, %s, %s, %s)
                    """, (tid, skey, sorder, stitle, sdesc, sact))

                # Announcements
                announcements = [
                    (1, 'everyone', None, 'Tremendous Momentum: Youth Camp 2026', 'Join us for spiritual gathering of the year at SJB Learning Center.', 1, '2026-05-21 09:00:00'),
                    (1, 'everyone', None, '29th YTR Anniversary Celebration', 'Celebrating 29 years of youth transformation nationwide.', 1, '2026-09-26 14:00:00'),
                    (1, 'chapter_admins', None, 'National Youth Leadership Summit', 'An executive summit for chapter administrators.', 1, '2026-12-19 08:30:00'),
                    (1, 'specific_chapter', 1, 'Greatest of All Time: Regional Fellowship', 'Praise, worship revival, and inter-campus fellowship.', 1, '2026-09-28 16:00:00')
                ]
                for cby, aud, tch, tit, bdy, ip, pa in announcements:
                    cur.execute("""
                        INSERT INTO announcements (created_by, target_audience, target_chapter_id, title, body, is_published, published_at)
                        VALUES (%s, %s, %s, %s, %s, %s, %s)
                    """, (cby, aud, tch, tit, bdy, ip, pa))

            if mode == 'demo':
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

                demo_requests = [
                    (1, 1, 1, 2, 'Youth Camp 2026: Unshakeable Faith', '2026-10-25', '8:00 AM - 5:00 PM', 'LSPU Los Baños Campus', 'Unshakeable Faith', 'Ignite your faith! Join us for Youth Camp 2026.', '2026-10-18', 'in_progress', 'assets/images/background.jpg', 4, 1, '2026-10-15 00:00:00', '2026-10-29', 'submitted'),
                    (2, 2, 2, 1, 'Greatest Of All Time (G.O.A.T.) Rally', '2026-11-05', '2:00 PM - 6:00 PM', 'Gymnasium / Facebook Live', 'Level up', 'Join us as we explore true greatness.', '2026-10-28', 'in_progress', 'assets/images/background.jpg', 3, 0, None, None, 'not_submitted'),
                    (3, 3, 3, 7, 'Tagaytay Praise & Worship Night', '2026-10-30', '6:00 PM - 9:00 PM', 'Skyranch Amphitheater', 'Acoustic Praise', 'Come as you are.', '2026-10-22', 'submitted', None, 1, 0, None, None, 'not_submitted'),
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
                    ('poster_created', 1, 'Poster created'),
                    ('waiting_for_caption', 2, 'Waiting for caption'),
                    ('posted_on_page', 3, 'Posted on page'),
                    ('promotional_video', 4, 'Promotional video'),
                    ('event_conducted_and_reported', 5, 'Event conducted & reported')
                ]
                for req_id in [1, 2, 3, 4]:
                    for skey, sorder, stitle in step_defs:
                        s_stat = 'completed' if req_id == 4 or (req_id == 1 and sorder <= 3) else ('in_progress' if (req_id == 1 and sorder == 4) or (req_id == 2 and sorder == 3) or (req_id == 3 and sorder == 1) else 'pending')
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
                print(f"[PASS] Cleared {deleted_files} uploaded file(s) from assets/uploads/")
                print(f"[PASS] Flush operation complete (Mode: {mode})!")
                after_counts = get_counts(cur)
                print(">> Post-flush database records:")
                for k, v in after_counts.items():
                    print(f"   - {k}: {v}")

def main():
    parser = argparse.ArgumentParser(description="Flush or reset YTR platform data.")
    parser.add_argument('--mode', choices=['activity', 'full', 'demo'], default='activity',
                        help="Flush mode: 'activity' (clear events/reports/requests), 'full' (factory reset), 'demo' (reset + seed sample events)")
    parser.add_argument('--yes', '-y', action='store_true',
                        help="Skip interactive confirmation")

    args = parser.parse_args()

    if not args.yes:
        print(f"WARNING: This will flush {args.mode.upper()} data from the database!")
        confirm = input("Type 'FLUSH' to proceed: ").strip()
        if confirm != 'FLUSH':
            print("Aborted.")
            sys.exit(1)

    run_flush(mode=args.mode, verbose=True)

if __name__ == '__main__':
    main()
