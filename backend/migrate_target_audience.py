"""
Migration Script: Add target_audience column to events and poster_requests tables.
Allows specifying target demographics (e.g., 'Open for All', 'BSIT Students', 'College / University Students', or custom).
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
            # 1. Update events table
            cur.execute("SHOW COLUMNS FROM events LIKE 'target_audience'")
            if not cur.fetchone():
                print("[MIGRATING] Adding target_audience column to events table...")
                cur.execute("ALTER TABLE events ADD COLUMN target_audience VARCHAR(150) NOT NULL DEFAULT 'Open for All' AFTER ytr_program")
                conn.commit()
                print("[SUCCESS] Column target_audience added to events table.")
            else:
                print("[SKIP] Column target_audience already exists on events table.")

            # 2. Update poster_requests table
            cur.execute("SHOW COLUMNS FROM poster_requests LIKE 'target_audience'")
            if not cur.fetchone():
                print("[MIGRATING] Adding target_audience column to poster_requests table...")
                cur.execute("ALTER TABLE poster_requests ADD COLUMN target_audience VARCHAR(150) NOT NULL DEFAULT 'Open for All' AFTER target_participants")
                conn.commit()
                print("[SUCCESS] Column target_audience added to poster_requests table.")
            else:
                print("[SKIP] Column target_audience already exists on poster_requests table.")

if __name__ == '__main__':
    migrate()
