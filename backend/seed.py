"""
Database Seeder written in Python for YTR Platform
Usage: python backend/seed.py
"""
import sys
from pathlib import Path

ROOT_DIR = Path(__file__).resolve().parent.parent
if str(ROOT_DIR) not in sys.path:
    sys.path.insert(0, str(ROOT_DIR))

from backend.db import get_db
from backend.auth_helpers import hash_password

def seed_database():
    print(">> Seeding YTR database via Python...")
    with get_db() as conn:
        with conn.cursor() as cur:
            # 1. Seed Chapters
            chapters = [
                ('YTR-LAG', 'Laguna Chapter', 'Gen One', 'Region IV-A', 'Laguna', 'Los Baños'),
                ('YTR-NCR', 'NCR Chapter', 'Gen Two', 'NCR', 'Metro Manila', 'Quezon City'),
                ('YTR-CAV', 'Cavite Chapter', 'Gen One', 'Region IV-A', 'Cavite', 'Tagaytay'),
                ('YTR-PAM', 'Pampanga Chapter', 'Gen Three', 'Region III', 'Pampanga', 'San Fernando'),
                ('YTR-BAT', 'Batangas Chapter', 'Gen Two', 'Region IV-A', 'Batangas', 'Batangas City'),
            ]

            for code, name, gen, reg, prov, city in chapters:
                cur.execute("""
                    INSERT INTO chapters (chapter_code, chapter_name, generation_name, region, province, city_municipality, status)
                    VALUES (%s, %s, %s, %s, %s, %s, 'active')
                    ON DUPLICATE KEY UPDATE chapter_name = VALUES(chapter_name)
                """, (code, name, gen, reg, prov, city))

            # Fetch Laguna ID
            cur.execute("SELECT id FROM chapters WHERE chapter_code = 'YTR-LAG'")
            laguna_id = cur.fetchone()['id']

            cur.execute("SELECT id FROM chapters WHERE chapter_code = 'YTR-NCR'")
            ncr_id = cur.fetchone()['id']

            # 2. Seed Users
            default_hash = hash_password('Password123!')
            users = [
                (None, 'Hamilton Jones Dela', 'admin@ytr.org', default_hash, 'national_admin', 'active'),
                (laguna_id, 'Joan Dela Cruz', 'joan.dela@gmail.com', default_hash, 'chapter_admin', 'active'),
                (ncr_id, 'Mark Bautista', 'mark.bautista@gmail.com', default_hash, 'chapter_admin', 'pending'),
            ]

            for ch_id, name, email, pw, role, status in users:
                cur.execute("""
                    INSERT INTO users (chapter_id, full_name, email, password_hash, role, account_status)
                    VALUES (%s, %s, %s, %s, %s, %s)
                    ON DUPLICATE KEY UPDATE full_name = VALUES(full_name), account_status = VALUES(account_status)
                """, (ch_id, name, email, pw, role, status))

            print("[PASS] Successfully seeded chapters and initial users!")

if __name__ == '__main__':
    seed_database()
