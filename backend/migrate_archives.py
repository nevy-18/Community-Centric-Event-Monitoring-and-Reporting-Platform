"""
Migration: Sets up archiving tables for non-destructive data flush and retrieval.
Creates:
  - system_data_archives (JSON ledger of all flushed records with batch_id, table_name, record_id, record_data)
  - archived_events (shadow table of events)
  - archived_event_reports (shadow table of event reports)
  - archived_poster_requests (shadow table of poster requests)
"""
import sys
from pathlib import Path

ROOT_DIR = Path(__file__).resolve().parent.parent
if str(ROOT_DIR) not in sys.path:
    sys.path.insert(0, str(ROOT_DIR))

from backend.db import get_db

def run_migration():
    print(">> Running Archive Migration...")
    with get_db() as conn:
        with conn.cursor() as cur:
            # 1. Primary Archive Ledger table
            cur.execute("""
                CREATE TABLE IF NOT EXISTS system_data_archives (
                    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    batch_id VARCHAR(64) NOT NULL,
                    table_name VARCHAR(64) NOT NULL,
                    record_id BIGINT UNSIGNED NOT NULL,
                    record_data LONGTEXT NOT NULL,
                    flushed_mode VARCHAR(32) NOT NULL DEFAULT 'activity',
                    flushed_by BIGINT UNSIGNED NULL,
                    flushed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    restored_at DATETIME NULL,
                    is_restored TINYINT(1) NOT NULL DEFAULT 0,
                    INDEX idx_batch (batch_id),
                    INDEX idx_table_record (table_name, record_id),
                    INDEX idx_is_restored (is_restored),
                    INDEX idx_flushed_at (flushed_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            """)
            print("  [x] Table system_data_archives verified.")

            # 2. Shadow tables
            cur.execute("CREATE TABLE IF NOT EXISTS archived_events LIKE events;")
            cur.execute("CREATE TABLE IF NOT EXISTS archived_event_reports LIKE event_reports;")
            cur.execute("CREATE TABLE IF NOT EXISTS archived_poster_requests LIKE poster_requests;")

            # 3. Add archive_batch_id and archived_at columns if not present
            for tbl in ['archived_events', 'archived_event_reports', 'archived_poster_requests']:
                cur.execute(f"SHOW COLUMNS FROM {tbl} LIKE 'archive_batch_id'")
                if not cur.fetchone():
                    cur.execute(f"ALTER TABLE {tbl} ADD COLUMN archive_batch_id VARCHAR(64) NOT NULL AFTER id")
                    cur.execute(f"ALTER TABLE {tbl} ADD COLUMN archived_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER archive_batch_id")
                    cur.execute(f"ALTER TABLE {tbl} ADD INDEX idx_arch_batch (archive_batch_id)")
                    print(f"  [x] Added archive tracking columns to {tbl}.")
                else:
                    print(f"  [x] Table {tbl} already has tracking columns.")

    print(">> Archive migration complete!")

if __name__ == '__main__':
    run_migration()
