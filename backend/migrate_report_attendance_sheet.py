"""
Migration Script: Add attendance_sheet_path column to event_reports table
Allows storing uploaded physical/scanned attendance sheet or attendee ledger document.
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
            cur.execute("SHOW COLUMNS FROM event_reports LIKE 'attendance_sheet_path'")
            if not cur.fetchone():
                print("[MIGRATING] Adding attendance_sheet_path column to event_reports table...")
                cur.execute("ALTER TABLE event_reports ADD COLUMN attendance_sheet_path VARCHAR(255) NULL AFTER attendee_roster")
                conn.commit()
                print("[SUCCESS] Column attendance_sheet_path added to event_reports table.")
            else:
                print("[SKIP] Column attendance_sheet_path already exists on event_reports table.")

if __name__ == '__main__':
    migrate()
