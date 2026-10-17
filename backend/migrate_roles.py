import sys
from pathlib import Path

ROOT_DIR = Path(__file__).resolve().parent.parent
if str(ROOT_DIR) not in sys.path:
    sys.path.insert(0, str(ROOT_DIR))

from backend.db import get_db

def migrate_users():
    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("SHOW COLUMNS FROM users LIKE 'position'")
            if not cur.fetchone():
                cur.execute("ALTER TABLE users ADD COLUMN position VARCHAR(100) NULL AFTER role")
                print("[PASS] Added 'position' column to users table.")

            # Assign positions based on email/role
            assignments = [
                ('admin@ytr.org', 'Executive Director'),
                ('adminhost@gmail.com', 'Regional Director (National)'),
                ('joan.dela@gmail.com', 'Chapter Coordinator'),
                ('mark.bautista@gmail.com', 'Chapter President'),
                ('elena.reyes@gmail.com', 'Regional Director (Chapter)'),
                ('carlos.santos@gmail.com', 'Chapter Coordinator'),
                ('munozrayvhen@gmail.com', 'Chapter Coordinator'),
            ]
            for email, pos in assignments:
                cur.execute("UPDATE users SET position = %s WHERE email = %s", (pos, email))

            # Default any remaining users
            cur.execute("UPDATE users SET position = 'National Youth Officer' WHERE role = 'national_admin' AND (position IS NULL OR position = '')")
            cur.execute("UPDATE users SET position = 'Chapter Coordinator' WHERE role = 'chapter_admin' AND (position IS NULL OR position = '')")

            cur.execute("SELECT id, full_name, email, role, position FROM users")
            rows = cur.fetchall()
            print(">> Updated users:")
            for r in rows:
                print(f"   {r['id']}: {r['full_name']} ({r['email']}) -> {r['role']} | {r['position']}")

if __name__ == '__main__':
    migrate_users()
