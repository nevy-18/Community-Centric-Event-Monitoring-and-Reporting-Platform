"""
Migration script to add drive_link to events table if not already present.
"""
from backend.db import get_db

def migrate():
    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("SHOW COLUMNS FROM events LIKE 'drive_link'")
            res = cur.fetchone()
            if not res:
                print("Adding drive_link column to events table...")
                cur.execute("ALTER TABLE events ADD COLUMN drive_link VARCHAR(1000) NULL AFTER attachment_path")
                conn.commit()
                print("Column drive_link added successfully!")
            else:
                print("Column drive_link already exists in events.")

if __name__ == '__main__':
    migrate()
