"""
Database Migration: Add regional_director columns to chapters and signature_image to users
Usage: python backend/migrate_signatures.py
"""
import sys
from pathlib import Path

ROOT_DIR = Path(__file__).resolve().parent.parent
if str(ROOT_DIR) not in sys.path:
    sys.path.insert(0, str(ROOT_DIR))

from backend.db import get_db

def run_migration():
    print(">> Running migration for Regional Directors and National Signatures...")
    with get_db() as conn:
        with conn.cursor() as cur:
            # 1. Add regional_director columns to chapters if missing
            cur.execute("SHOW COLUMNS FROM chapters LIKE 'regional_director_name'")
            if not cur.fetchone():
                cur.execute("""
                    ALTER TABLE chapters
                    ADD COLUMN regional_director_id BIGINT(20) UNSIGNED NULL,
                    ADD COLUMN regional_director_name VARCHAR(150) NULL
                """)
                print("[PASS] Added regional_director_id and regional_director_name to chapters table.")
            else:
                print("[INFO] chapters already contains regional_director_name.")

            # 2. Add signature_image to users if missing
            cur.execute("SHOW COLUMNS FROM users LIKE 'signature_image'")
            if not cur.fetchone():
                cur.execute("ALTER TABLE users ADD COLUMN signature_image LONGTEXT NULL")
                print("[PASS] Added signature_image to users table.")
            else:
                print("[INFO] users already contains signature_image.")

            # 3. Seed default Regional Directors for active chapters
            cur.execute("UPDATE chapters SET regional_director_name = 'Elena Reyes' WHERE chapter_code IN ('YTR-LAG', 'YTR-CAV') AND (regional_director_name IS NULL OR regional_director_name = '')")
            cur.execute("UPDATE chapters SET regional_director_name = 'Admin Host' WHERE chapter_code IN ('YTR-NCR', 'YTR-UMAK') AND (regional_director_name IS NULL OR regional_director_name = '')")
            cur.execute("UPDATE chapters SET regional_director_name = 'Carlos Santos' WHERE chapter_code = 'YTR-PAM' AND (regional_director_name IS NULL OR regional_director_name = '')")
            cur.execute("UPDATE chapters SET regional_director_name = 'Mateo Hernandez' WHERE chapter_code = 'YTR-BAT' AND (regional_director_name IS NULL OR regional_director_name = '')")

            cur.execute("SELECT id, chapter_code, chapter_name, regional_director_name FROM chapters")
            print(">> Current Chapters and Assigned Regional Directors:")
            for ch in cur.fetchall():
                print(f"   [{ch['chapter_code']}] {ch['chapter_name']} -> {ch['regional_director_name']}")

    print("[SUCCESS] Migration completed successfully.")

if __name__ == '__main__':
    run_migration()
