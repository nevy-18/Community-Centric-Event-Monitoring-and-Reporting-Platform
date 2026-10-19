"""
Migration Script: Add attachment_path column to events table
Allows storing uploaded proposal soft copy document / PDF / presentation / budget sheet.
"""
import os
import sys

PROJECT_ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
if PROJECT_ROOT not in sys.path:
    sys.path.insert(0, PROJECT_ROOT)

from backend.db import get_db

def migrate():
    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("SHOW COLUMNS FROM events LIKE 'attachment_path'")
            if not cur.fetchone():
                print("[MIGRATING] Adding attachment_path column to events table...")
                cur.execute("ALTER TABLE events ADD COLUMN attachment_path VARCHAR(255) NULL AFTER due_date")
                conn.commit()
                print("[SUCCESS] Column attachment_path added to events table.")
            else:
                print("[SKIP] Column attachment_path already exists on events table.")

if __name__ == '__main__':
    migrate()
