from flask import Blueprint, request, session
from backend.db import get_db, serialize_rows, serialize_row
from backend.auth_helpers import api_response, api_error

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

    return api_response({
        'total_accounts': total,
        'pending_requests': pending,
        'national_admins': national,
        'chapter_admins': chapter
    })

@accounts_bp.route('', methods=['GET'])
def list_accounts():
    filter_type = request.args.get('filter', 'all')
    search = request.args.get('search', '').strip()

    sql = """
        SELECT u.id, u.chapter_id, u.full_name, u.email, u.role, u.position, u.account_status,
               u.requested_at, u.reviewed_at, u.review_note,
               c.chapter_name, c.chapter_code
        FROM users u
        LEFT JOIN chapters c ON c.id = u.chapter_id
        WHERE 1=1
    """
    params = []

    if filter_type == 'pending':
        sql += " AND u.account_status = 'pending'"
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
