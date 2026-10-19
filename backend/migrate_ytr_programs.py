"""
Database Migration: Add ytr_program column to events table
Usage: python backend/migrate_ytr_programs.py
"""
import sys
from pathlib import Path

ROOT_DIR = Path(__file__).resolve().parent.parent
if str(ROOT_DIR) not in sys.path:
    sys.path.insert(0, str(ROOT_DIR))

from backend.db import get_db

def run_migration():
    print(">> Running migration for YTR Program Pillars on events table...")
    with get_db() as conn:
        with conn.cursor() as cur:
            # 1. Check if column exists
            cur.execute("SHOW COLUMNS FROM events LIKE 'ytr_program'")
            col = cur.fetchone()
            if not col:
                cur.execute("""
                    ALTER TABLE events
                    ADD COLUMN ytr_program VARCHAR(100) NOT NULL DEFAULT 'Lead up (Youth Leadership)'
                    AFTER event_type
                """)
                print("[PASS] Added ytr_program column to events table.")
            else:
                print("[INFO] events table already contains ytr_program column.")

            # 2. Update existing events to appropriate YTR programs
            program_mappings = {
                1: 'Build up (Youth Personality Development)',
                2: 'Lead up (Youth Leadership)',
                3: 'Build up (Youth Personality Development)',
                4: 'Rise up (Youth Excellence)',
                5: 'Lead up (Youth Leadership)',
                6: 'Rise up (Youth Excellence)',
                7: 'Gear up (Youth preparation and empowerment)',
            }
            for eid, prog in program_mappings.items():
                cur.execute("UPDATE events SET ytr_program = %s WHERE id = %s", (prog, eid))

            cur.execute("SELECT id, title, event_type, ytr_program FROM events")
            rows = cur.fetchall()
            print(f">> Updated {len(rows)} events with YTR Program pillars:")
            for r in rows:
                print(f"   [{r['id']}] {r['title']} -> {r.get('ytr_program')}")

    print("[SUCCESS] Migration for YTR Program completed successfully.")

if __name__ == '__main__':
    run_migration()
