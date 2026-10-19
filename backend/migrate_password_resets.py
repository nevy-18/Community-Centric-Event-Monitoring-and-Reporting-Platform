"""
Database Migration: Password Reset Requests and User Reset Columns
Usage: python backend/migrate_password_resets.py
"""
import sys
from pathlib import Path

ROOT_DIR = Path(__file__).resolve().parent.parent
if str(ROOT_DIR) not in sys.path:
    sys.path.insert(0, str(ROOT_DIR))

from backend.db import get_db

def run_migration():
    print(">> Running migration for Password Resets...")
    with get_db() as conn:
        with conn.cursor() as cur:
            # 1. Add reset columns to users table if missing
            cur.execute("SHOW COLUMNS FROM users LIKE 'password_reset_pending'")
            if not cur.fetchone():
                cur.execute("""
                    ALTER TABLE users
                    ADD COLUMN password_reset_pending TINYINT(1) DEFAULT 0,
                    ADD COLUMN password_reset_requested_at DATETIME NULL,
                    ADD COLUMN temporary_password VARCHAR(100) NULL,
                    ADD COLUMN password_reset_at DATETIME NULL
                """)
                print("[PASS] Added password_reset_pending, password_reset_requested_at, temporary_password, and password_reset_at to users.")
            else:
                print("[INFO] users table already contains password_reset_pending.")

            # 2. Create password_resets table if not exists
            cur.execute("""
                CREATE TABLE IF NOT EXISTS password_resets (
                    id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    user_id BIGINT(20) UNSIGNED NOT NULL,
                    email VARCHAR(190) NOT NULL,
                    status ENUM('pending', 'resolved', 'cancelled') DEFAULT 'pending',
                    requested_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    resolved_at DATETIME NULL,
                    resolved_by BIGINT(20) UNSIGNED NULL,
                    temporary_password VARCHAR(100) NULL,
                    notes TEXT NULL,
                    INDEX idx_user_id (user_id),
                    INDEX idx_status (status),
                    INDEX idx_email (email),
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            """)
            print("[PASS] Created or verified password_resets table.")

    print("[SUCCESS] Password resets migration completed successfully.")

if __name__ == '__main__':
    run_migration()
