from flask import Blueprint, request, session
from backend.db import get_db, serialize_row
from backend.auth_helpers import hash_password, verify_password, api_response, api_error
import re

auth_bp = Blueprint('auth', __name__, url_prefix='/api/auth')

EMAIL_REGEX = r'^[\w\.-]+@[\w\.-]+\.\w+$'

@auth_bp.route('/login', methods=['POST'])
def login():
    data = request.get_json(silent=True) or request.form.to_dict() or {}
    email = data.get('email', '').strip().lower()
    password = data.get('password', '')

    if not email or not password:
        return api_error('Email and password are required.', 400)

    if not re.match(EMAIL_REGEX, email):
        return api_error('Please provide a valid email address.', 400)

    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("""
                SELECT u.id, u.chapter_id, u.full_name, u.email, u.password_hash, u.role, u.account_status,
                       c.chapter_name
                FROM users u
                LEFT JOIN chapters c ON c.id = u.chapter_id
                WHERE LOWER(u.email) = %s
                LIMIT 1
            """, (email,))
            user = cur.fetchone()

            if not user or not verify_password(password, user['password_hash']):
                return api_error('Invalid email or password.', 401)

            if user['account_status'] == 'pending':
                return api_error('Your account request is still pending administrator review.', 403)

            if user['account_status'] == 'rejected':
                return api_error('Your account access request was declined. Please contact your administrator.', 403)

            if user['account_status'] == 'suspended':
                return api_error('Your account has been suspended. Please contact National Admin.', 403)

            # Store in session
            session['user_id'] = user['id']
            session['user_name'] = user['full_name']
            session['user_email'] = user['email']
            session['user_role'] = user['role']
            session['chapter_id'] = user['chapter_id']
            session['chapter_name'] = user['chapter_name']

            # Update last_login_at
            cur.execute("UPDATE users SET last_login_at = NOW() WHERE id = %s", (user['id'],))

    user_info = {
        'id': user['id'],
        'full_name': user['full_name'],
        'email': user['email'],
        'role': user['role'],
        'chapter_id': user['chapter_id'],
        'chapter_name': user['chapter_name']
    }

    return api_response({
        'user': user_info,
        'redirect': 'Dashboard.php'
    }, 'Login successful.')

@auth_bp.route('/register', methods=['POST'])
def register():
    data = request.get_json(silent=True) or request.form.to_dict() or {}
    full_name = data.get('full_name', '').strip()
    email = data.get('email', '').strip().lower()
    password = data.get('password', '')
    role = data.get('requested_role', '')
    chapter_id = data.get('chapter_id')

    if not full_name or not email or not password or not role:
        return api_error('All required fields must be filled out.', 400)

    if not re.match(EMAIL_REGEX, email):
        return api_error('Please provide a valid email address.', 400)

    if len(password) < 8:
        return api_error('Password must contain at least 8 characters.', 400)

    if role not in ['national_admin', 'chapter_admin']:
        return api_error('Invalid role requested.', 400)

    if role == 'chapter_admin' and not chapter_id:
        return api_error('Please select a valid chapter for Chapter Admin role.', 400)

    with get_db() as conn:
        with conn.cursor() as cur:
            # Check duplicate email
            cur.execute("SELECT id FROM users WHERE LOWER(email) = %s LIMIT 1", (email,))
            if cur.fetchone():
                return api_error('An account with this email address already exists or is pending review.', 409)

            pw_hash = hash_password(password)

            cur.execute("""
                INSERT INTO users (chapter_id, full_name, email, password_hash, role, account_status, requested_at)
                VALUES (%s, %s, %s, %s, %s, 'pending', NOW())
            """, (chapter_id if role == 'chapter_admin' else None, full_name, email, pw_hash, role))

            new_user_id = cur.lastrowid

    return api_response({
        'user_id': new_user_id
    }, 'Account request submitted successfully. It will be reviewed by an administrator.', 201)

@auth_bp.route('/logout', methods=['POST', 'GET'])
def logout():
    session.clear()
    return api_response({}, 'Logged out successfully.')

@auth_bp.route('/me', methods=['GET'])
def get_current_user():
    user_id = session.get('user_id')
    if not user_id:
        return api_error('Not authenticated.', 401)

    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("""
                SELECT u.id, u.chapter_id, u.full_name, u.email, u.role, u.account_status,
                       c.chapter_name
                FROM users u
                LEFT JOIN chapters c ON c.id = u.chapter_id
                WHERE u.id = %s
                LIMIT 1
            """, (user_id,))
            user = cur.fetchone()

    if not user:
        return api_error('User not found.', 404)

    return api_response({'user': serialize_row(user)})
