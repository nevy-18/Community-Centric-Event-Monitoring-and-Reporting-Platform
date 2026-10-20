"""
Migration Script: Add photos_drive_link column to event_reports table
Allows chapters to submit a Google Drive / Cloud photo album link for event documentation.
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
            cur.execute("SHOW COLUMNS FROM event_reports LIKE 'photos_drive_link'")
            if not cur.fetchone():
                print("[MIGRATING] Adding photos_drive_link column to event_reports table...")
                cur.execute("ALTER TABLE event_reports ADD COLUMN photos_drive_link VARCHAR(1000) NULL AFTER attendance_sheet_path")
                conn.commit()
                print("[SUCCESS] Column photos_drive_link added to event_reports table.")
            else:
                print("[SKIP] Column photos_drive_link already exists on event_reports table.")

if __name__ == '__main__':
    migrate()
