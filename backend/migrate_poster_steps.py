import sys
from pathlib import Path

ROOT_DIR = Path(__file__).resolve().parent.parent
if str(ROOT_DIR) not in sys.path:
    sys.path.insert(0, str(ROOT_DIR))

from backend.db import get_db

def migrate_steps():
    with get_db() as conn:
        with conn.cursor() as cur:
            # 1. Update workflow templates
            cur.execute("DELETE FROM poster_workflow_templates")
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

            # 2. Reset steps for demo requests 1, 2, 3, 4
            step_defs = [
                ('poster_created', 1, 'Poster Request & Design'),
                ('posted_on_page', 2, 'Posted on Page'),
                ('promotional_video', 3, 'Promotional Video'),
                ('documentation_and_report', 4, 'Documentation and Report'),
                ('event_concluded', 5, 'Event Concluded and Reported')
            ]

            for req_id in [1, 2, 3, 4]:
                cur.execute("DELETE FROM poster_request_steps WHERE poster_request_id = %s", (req_id,))
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

            cur.execute("UPDATE poster_requests SET current_step = 1 WHERE id = 3")
            conn.commit()

    print("[PASS] Successfully migrated poster workflow templates and steps to 5 standard steps.")

if __name__ == '__main__':
    migrate_steps()
