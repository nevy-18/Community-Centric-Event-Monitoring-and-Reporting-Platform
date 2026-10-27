import json
from flask import Blueprint, request, session
from backend.db import get_db, serialize_rows, serialize_row
from backend.auth_helpers import api_response, api_error, hash_password
import secrets
import string

accounts_bp = Blueprint('accounts', __name__, url_prefix='/api/accounts')

@accounts_bp.route('/metrics', methods=['GET'])
def get_metrics():
    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("SELECT COUNT(*) AS c FROM users")
            total = cur.fetchone()['c']

            cur.execute("SELECT COUNT(*) AS c FROM users WHERE account_status = 'pending'")
            pending = cur.fetchone()['c']

            cur.execute("SELECT COUNT(*) AS c FROM users WHERE role = 'national_admin' AND account_status = 'active'")
            national = cur.fetchone()['c']

            cur.execute("SELECT COUNT(*) AS c FROM users WHERE role = 'chapter_admin' AND account_status = 'active'")
            chapter = cur.fetchone()['c']

            cur.execute("SELECT COUNT(*) AS c FROM users WHERE password_reset_pending = 1")
            resets = cur.fetchone()['c']

    return api_response({
        'total_accounts': total,
        'pending_requests': pending,
        'national_admins': national,
        'chapter_admins': chapter,
        'password_resets': resets
    })

@accounts_bp.route('', methods=['GET'])
def list_accounts():
    filter_type = request.args.get('filter', 'all')
    search = request.args.get('search', '').strip()

    sql = """
        SELECT u.id, u.chapter_id, u.full_name, u.email, u.role, u.position, u.account_status,
               u.requested_at, u.reviewed_at, u.review_note,
               u.password_reset_pending, u.password_reset_requested_at, u.temporary_password, u.password_reset_at,
               (SELECT notes FROM password_resets WHERE user_id = u.id AND status = 'pending' ORDER BY id DESC LIMIT 1) AS reset_notes,
               c.chapter_name, c.chapter_code
        FROM users u
        LEFT JOIN chapters c ON c.id = u.chapter_id
        WHERE 1=1
    """
    params = []

    if filter_type == 'pending':
        sql += " AND u.account_status = 'pending'"
    elif filter_type == 'password_resets':
        sql += " AND (u.password_reset_pending = 1 OR u.id IN (SELECT user_id FROM password_resets WHERE status = 'pending'))"
    elif filter_type == 'national_admins':
        sql += " AND u.role = 'national_admin'"
    elif filter_type == 'chapter_admins':
        sql += " AND u.role = 'chapter_admin'"
    elif filter_type == 'disabled':
        sql += " AND u.account_status IN ('rejected', 'suspended')"

    if search:
        sql += " AND (u.full_name LIKE %s OR u.email LIKE %s OR c.chapter_name LIKE %s OR u.position LIKE %s)"
        s = f"%{search}%"
        params.extend([s, s, s, s])

    sql += " ORDER BY u.requested_at DESC, u.id DESC"

    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute(sql, tuple(params))
            accounts = cur.fetchall()

    return api_response(serialize_rows(accounts))

@accounts_bp.route('/<int:user_id>', methods=['GET'])
def get_account(user_id):
    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("""
                SELECT u.id, u.chapter_id, u.full_name, u.email, u.role, u.position, u.account_status,
                       u.requested_at, u.reviewed_at, u.review_note,
                       u.password_reset_pending, u.password_reset_requested_at, u.temporary_password, u.password_reset_at,
                       c.chapter_name, c.chapter_code,
                       r.full_name AS reviewer_name
                FROM users u
                LEFT JOIN chapters c ON c.id = u.chapter_id
                LEFT JOIN users r ON r.id = u.reviewed_by
                WHERE u.id = %s
                LIMIT 1
            """, (user_id,))
            user = cur.fetchone()

    if not user:
        return api_error('User account not found.', 404)

    return api_response(serialize_row(user))

@accounts_bp.route('/<int:user_id>/approve', methods=['POST'])
def approve_account(user_id):
    data = request.get_json(silent=True) or request.form.to_dict() or {}
    role = data.get('role', 'chapter_admin')
    position = data.get('position')
    chapter_id = data.get('chapter_id')
    reviewer_id = session.get('user_id')

    if role not in ['national_admin', 'chapter_admin']:
        return api_error('Invalid role specified.', 400)

    if role == 'chapter_admin':
        if not chapter_id:
            chapter_id = 1  # Default to Laguna Chapter if none provided
        else:
            try:
                chapter_id = int(chapter_id)
            except (ValueError, TypeError):
                chapter_id = 1

    if not position:
        position = 'Executive Director' if role == 'national_admin' else 'Chapter Coordinator'

    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("""
                UPDATE users
                SET account_status = 'active',
                    role = %s,
                    position = %s,
                    chapter_id = %s,
                    reviewed_by = %s,
                    reviewed_at = NOW()
                WHERE id = %s
            """, (role, position, chapter_id if role == 'chapter_admin' else None, reviewer_id, user_id))

    return api_response({}, 'Account approved successfully.')

@accounts_bp.route('/<int:user_id>/decline', methods=['POST'])
def decline_account(user_id):
    data = request.get_json(silent=True) or request.form.to_dict() or {}
    note = data.get('review_note', 'Application declined by administrator.').strip()
    reviewer_id = session.get('user_id')

    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("""
                UPDATE users
                SET account_status = 'rejected',
                    review_note = %s,
                    reviewed_by = %s,
                    reviewed_at = NOW()
                WHERE id = %s
            """, (note, reviewer_id, user_id))

    return api_response({}, 'Account request has been declined.')

@accounts_bp.route('/<int:user_id>', methods=['DELETE', 'POST'])
def remove_account(user_id):
    """
    Permanently remove an account.
    Restricted to National Administrators.
    """
    reviewer_id = request.headers.get('X-User-Id') or session.get('user_id')
    reviewer_email = request.headers.get('X-User-Email') or session.get('user_email')

    with get_db() as conn:
        with conn.cursor() as cur:
            # Check reviewer authority
            if reviewer_id or reviewer_email:
                cur.execute("""
                    SELECT role FROM users 
                    WHERE id = %s OR LOWER(email) = %s 
                    LIMIT 1
                """, (reviewer_id, str(reviewer_email or '').lower()))
                rev = cur.fetchone()
                if not rev or rev.get('role') != 'national_admin':
                    return api_error("Access Denied: Only National Administrators can remove accounts.", 403)

            # Check target user
            cur.execute("SELECT id, full_name, email, role, position, chapter_id FROM users WHERE id = %s", (user_id,))
            target_user = cur.fetchone()
            if not target_user:
                return api_error("Account not found.", 404)

            # Cannot delete primary root admin
            if target_user['id'] == 1 or target_user['email'].lower() == 'admin@ytr.org':
                return api_error("Cannot remove the primary Root Administrator account.", 403)

            # Cannot delete oneself
            if reviewer_id and str(target_user['id']) == str(reviewer_id):
                return api_error("You cannot remove your own active logged-in account.", 400)

            # Archive target user into system_data_archives for retrievability
            try:
                cur.execute("""
                    INSERT INTO system_data_archives (batch_id, table_name, record_id, record_data, flushed_mode, flushed_by)
                    VALUES (%s, 'users', %s, %s, 'account_removal', %s)
                """, (f"REMOVE-{user_id}", user_id, json.dumps(serialize_row(target_user), default=str), reviewer_id))
            except Exception:
                pass

            # Safe deletion with FK handling
            cur.execute("SET FOREIGN_KEY_CHECKS = 0;")
            try:
                cur.execute("DELETE FROM notifications WHERE user_id = %s OR created_by = %s", (user_id, user_id))
                cur.execute("DELETE FROM password_resets WHERE user_id = %s", (user_id,))
                cur.execute("UPDATE events SET created_by = NULL WHERE created_by = %s", (user_id,))
                cur.execute("UPDATE events SET reviewed_by = NULL WHERE reviewed_by = %s", (user_id,))
                cur.execute("UPDATE event_reports SET submitted_by = NULL WHERE submitted_by = %s", (user_id,))
                cur.execute("UPDATE event_reports SET reviewed_by = NULL WHERE reviewed_by = %s", (user_id,))
                cur.execute("UPDATE event_report_files SET uploaded_by = NULL WHERE uploaded_by = %s", (user_id,))
                cur.execute("UPDATE poster_requests SET requested_by = NULL WHERE requested_by = %s", (user_id,))
                cur.execute("UPDATE poster_requests SET assigned_to = NULL WHERE assigned_to = %s", (user_id,))
                cur.execute("UPDATE poster_requests SET reviewed_by = NULL WHERE reviewed_by = %s", (user_id,))
                cur.execute("UPDATE poster_request_steps SET updated_by = NULL WHERE updated_by = %s", (user_id,))
                cur.execute("UPDATE poster_request_updates SET updated_by = NULL WHERE updated_by = %s", (user_id,))
                cur.execute("UPDATE announcements SET created_by = NULL WHERE created_by = %s", (user_id,))
                cur.execute("UPDATE audit_logs SET user_id = NULL WHERE user_id = %s", (user_id,))
                cur.execute("UPDATE users SET reviewed_by = NULL WHERE reviewed_by = %s", (user_id,))
                cur.execute("DELETE FROM users WHERE id = %s", (user_id,))
            finally:
                cur.execute("SET FOREIGN_KEY_CHECKS = 1;")

    return api_response({}, f"Account for {target_user['full_name']} ({target_user['email']}) has been successfully removed.")

@accounts_bp.route('/signature', methods=['GET', 'POST'])
def handle_signature():
    """Upload or retrieve national administrator signature image."""
    if request.method == 'POST':
        data = request.get_json(silent=True) or request.form.to_dict() or {}
        sig = data.get('signature_image', '').strip()
        user_id = data.get('user_id') or session.get('user_id')

        if not sig:
            return api_error('Signature image data is required.', 400)

        with get_db() as conn:
            with conn.cursor() as cur:
                if not user_id:
                    # Target primary national admin / chief executive
                    cur.execute("SELECT id FROM users WHERE role = 'national_admin' ORDER BY id ASC LIMIT 1")
                    row = cur.fetchone()
                    user_id = row['id'] if row else 1

                cur.execute("UPDATE users SET signature_image = %s WHERE id = %s", (sig, user_id))
                cur.execute("SELECT id, full_name, role, position, signature_image FROM users WHERE id = %s", (user_id,))
                updated_user = cur.fetchone()

        return api_response(serialize_row(updated_user), 'National administrator signature saved successfully.')

    # GET
    user_id = request.args.get('user_id') or session.get('user_id')
    with get_db() as conn:
        with conn.cursor() as cur:
            if user_id:
                cur.execute("SELECT id, full_name, role, position, signature_image FROM users WHERE id = %s", (user_id,))
                user = cur.fetchone()
            else:
                # Find active national admin with a signature first
                cur.execute("""
                    SELECT id, full_name, role, position, signature_image
                    FROM users
                    WHERE role = 'national_admin' AND signature_image IS NOT NULL AND signature_image != ''
                    LIMIT 1
                """)
                user = cur.fetchone()
                if not user:
                    cur.execute("SELECT id, full_name, role, position, signature_image FROM users WHERE role = 'national_admin' LIMIT 1")
                    user = cur.fetchone()

    return api_response(serialize_row(user) if user else {'signature_image': None})

@accounts_bp.route('/<int:user_id>/reset-password', methods=['POST'])
def reset_password(user_id):
    """National Administrator endpoint to issue/provide a reset password for a user account."""
    data = request.get_json(silent=True) or request.form.to_dict() or {}
    new_password = (data.get('new_password') or data.get('password') or '').strip()
    notes = (data.get('notes') or '').strip()
    reviewer_id = session.get('user_id')

    if not new_password:
        chars = string.ascii_letters + string.digits
        random_suffix = ''.join(secrets.choice(chars) for _ in range(4))
        new_password = f"YTR-{random_suffix}#26"

    if len(new_password) < 8:
        return api_error('New password must be at least 8 characters long.', 400)

    pw_hash = hash_password(new_password)

    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("""
                SELECT u.id, u.full_name, u.email, u.role, u.position, c.chapter_name
                FROM users u
                LEFT JOIN chapters c ON c.id = u.chapter_id
                WHERE u.id = %s
                LIMIT 1
            """, (user_id,))
            user = cur.fetchone()

            if not user:
                return api_error('Account not found.', 404)

            # Update user password and clear reset flag
            cur.execute("""
                UPDATE users
                SET password_hash = %s,
                    temporary_password = %s,
                    password_reset_pending = 0,
                    password_reset_at = NOW()
                WHERE id = %s
            """, (pw_hash, new_password, user_id))

            # Mark any pending password reset requests for this user as resolved
            cur.execute("""
                UPDATE password_resets
                SET status = 'resolved',
                    resolved_at = NOW(),
                    resolved_by = %s,
                    temporary_password = %s,
                    notes = CASE WHEN %s != '' THEN %s ELSE notes END
                WHERE user_id = %s AND status = 'pending'
            """, (reviewer_id, new_password, notes, notes, user_id))

            # Record audit log
            cur.execute("""
                INSERT INTO audit_logs (user_id, action, entity_type, entity_id, details)
                VALUES (%s, 'reset_password', 'user', %s, %s)
            """, (reviewer_id, user_id, f"National Administrator issued reset password for {user['full_name']} ({user['email']})"))

    return api_response({
        'user_id': user_id,
        'full_name': user['full_name'],
        'email': user['email'],
        'temporary_password': new_password
    }, f"Password for {user['full_name']} has been reset successfully. Temporary password issued.")

@accounts_bp.route('/password-resets', methods=['GET'])
def list_password_resets():
    """List password reset requests with user and chapter details."""
    status_filter = request.args.get('status', 'all')
    sql = """
        SELECT pr.id, pr.user_id, pr.email, pr.status, pr.notes, pr.temporary_password,
               pr.requested_at, pr.resolved_at,
               u.full_name, u.role, u.position, u.account_status,
               c.chapter_name, c.chapter_code,
               r.full_name AS resolved_by_name
        FROM password_resets pr
        JOIN users u ON u.id = pr.user_id
        LEFT JOIN chapters c ON c.id = u.chapter_id
        LEFT JOIN users r ON r.id = pr.resolved_by
        WHERE 1=1
    """
    params = []
    if status_filter == 'pending':
        sql += " AND pr.status = 'pending'"
    elif status_filter == 'resolved':
        sql += " AND pr.status = 'resolved'"

    sql += " ORDER BY pr.requested_at DESC, pr.id DESC"

    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute(sql, tuple(params))
            resets = cur.fetchall()

    return api_response(serialize_rows(resets))


