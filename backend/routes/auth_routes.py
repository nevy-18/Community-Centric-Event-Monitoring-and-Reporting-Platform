from flask import Blueprint, request, session
from backend.db import get_db, serialize_row
from backend.auth_helpers import hash_password, verify_password, api_response, api_error
import re

auth_bp = Blueprint('auth', __name__, url_prefix='/api/auth')

EMAIL_REGEX = r'^[\w\.-]+@[\w\.-]+\.\w+$'

NATIONAL_ROLES = ['Chief Executive Operation', 'Executive Director', 'Regional Director (National)', 'National Youth Officer']
CHAPTER_ROLES = ['Chapter Coordinator', 'Chapter President', 'Regional Director (Chapter)']

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
                SELECT u.id, u.chapter_id, u.full_name, u.email, u.password_hash, u.role, u.position, u.account_status,
                       u.signature_image, u.profile_picture, c.chapter_name
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
            session['user_position'] = user.get('position')
            session['chapter_id'] = user['chapter_id']
            session['chapter_name'] = user['chapter_name']

            # Update last_login_at
            cur.execute("UPDATE users SET last_login_at = NOW() WHERE id = %s", (user['id'],))

    user_info = {
        'id': user['id'],
        'full_name': user['full_name'],
        'email': user['email'],
        'role': user['role'],
        'position': user.get('position') or ('Chief Executive Operation' if user['role'] == 'national_admin' else 'Chapter Coordinator'),
        'chapter_id': user['chapter_id'],
        'chapter_name': user['chapter_name'],
        'signature_image': user.get('signature_image'),
        'profile_picture': user.get('profile_picture')
    }

    redirect_url = 'ChapterDashboard.php' if user['role'] == 'chapter_admin' else 'Dashboard.php'

    return api_response({
        'user': user_info,
        'redirect': redirect_url
    }, 'Login successful.')

@auth_bp.route('/register', methods=['POST'])
def register():
    data = request.get_json(silent=True) or request.form.to_dict() or {}
    full_name = data.get('full_name', '').strip()
    email = data.get('email', '').strip().lower()
    password = data.get('password', '')
    raw_role = (data.get('requested_role') or data.get('role') or '').strip()
    raw_position = (data.get('position') or '').strip()
    chapter_id = data.get('chapter_id')

    if not full_name or not email or not password or not raw_role:
        return api_error('All required fields must be filled out.', 400)

    if not re.match(EMAIL_REGEX, email):
        return api_error('Please provide a valid email address.', 400)

    if len(password) < 8:
        return api_error('Password must contain at least 8 characters.', 400)

    # Determine Tier (role) and Specific Position
    if raw_role in NATIONAL_ROLES:
        db_role = 'national_admin'
        position = raw_role
        chapter_id = None
    elif raw_role in CHAPTER_ROLES:
        db_role = 'chapter_admin'
        position = raw_role
    elif raw_role == 'national_admin':
        db_role = 'national_admin'
        position = raw_position or 'Executive Director'
        chapter_id = None
    elif raw_role == 'chapter_admin':
        db_role = 'chapter_admin'
        position = raw_position or 'Chapter Coordinator'
    else:
        return api_error('Invalid role or position selected.', 400)

    if db_role == 'chapter_admin' and not chapter_id:
        return api_error('Please select a valid chapter for Chapter roles.', 400)

    with get_db() as conn:
        with conn.cursor() as cur:
            # Check duplicate email
            cur.execute("SELECT id FROM users WHERE LOWER(email) = %s LIMIT 1", (email,))
            if cur.fetchone():
                return api_error('An account with this email address already exists or is pending review.', 409)

            pw_hash = hash_password(password)

            cur.execute("""
                INSERT INTO users (chapter_id, full_name, email, password_hash, role, position, account_status, requested_at)
                VALUES (%s, %s, %s, %s, %s, %s, 'pending', NOW())
            """, (chapter_id, full_name, email, pw_hash, db_role, position))

            new_user_id = cur.lastrowid

    return api_response({
        'user_id': new_user_id,
        'role': db_role,
        'position': position
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
                SELECT u.id, u.chapter_id, u.full_name, u.email, u.role, u.position, u.account_status,
                       u.signature_image, u.profile_picture, c.chapter_name
                FROM users u
                LEFT JOIN chapters c ON c.id = u.chapter_id
                WHERE u.id = %s
                LIMIT 1
            """, (user_id,))
            user = cur.fetchone()

    if not user:
        return api_error('User not found.', 404)

    return api_response({'user': serialize_row(user)})

@auth_bp.route('/profile', methods=['GET', 'POST', 'PUT'])
def user_profile():
    if request.method == 'GET':
        user_id = request.args.get('user_id') or session.get('user_id')
        email = (request.args.get('email') or '').strip().lower()
        if not user_id and not email:
            return api_error('User identification required.', 400)

        with get_db() as conn:
            with conn.cursor() as cur:
                if user_id:
                    cur.execute("""
                        SELECT u.id, u.chapter_id, u.full_name, u.email, u.role, u.position, u.account_status,
                               u.signature_image, u.profile_picture, u.requested_at, c.chapter_name, c.chapter_code, c.region
                        FROM users u
                        LEFT JOIN chapters c ON c.id = u.chapter_id
                        WHERE u.id = %s
                        LIMIT 1
                    """, (user_id,))
                else:
                    cur.execute("""
                        SELECT u.id, u.chapter_id, u.full_name, u.email, u.role, u.position, u.account_status,
                               u.signature_image, u.profile_picture, u.requested_at, c.chapter_name, c.chapter_code, c.region
                        FROM users u
                        LEFT JOIN chapters c ON c.id = u.chapter_id
                        WHERE LOWER(u.email) = %s
                        LIMIT 1
                    """, (email,))
                user = cur.fetchone()

        if not user:
            return api_error('User not found.', 404)

        return api_response({'user': serialize_row(user)})

    # POST or PUT to update profile
    data = request.get_json(silent=True) or request.form.to_dict() or {}
    user_id = data.get('user_id') or session.get('user_id')
    email = (data.get('email') or '').strip().lower()

    if not user_id and not email:
        return api_error('User identification required.', 400)

    full_name = (data.get('full_name') or '').strip()
    position = (data.get('position') or '').strip()
    signature_image = data.get('signature_image')
    profile_picture = data.get('profile_picture')
    current_password = data.get('current_password', '')
    new_password = data.get('new_password', '')

    with get_db() as conn:
        with conn.cursor() as cur:
            if user_id:
                cur.execute("SELECT * FROM users WHERE id = %s LIMIT 1", (user_id,))
            else:
                cur.execute("SELECT * FROM users WHERE LOWER(email) = %s LIMIT 1", (email,))
            user = cur.fetchone()

            if not user:
                return api_error('User not found.', 404)

            target_id = user['id']

            # If changing password
            if new_password:
                if len(new_password) < 8:
                    return api_error('New password must be at least 8 characters long.', 400)
                if current_password and not verify_password(current_password, user['password_hash']):
                    return api_error('Current password is incorrect.', 400)
                new_hash = hash_password(new_password)
                cur.execute("UPDATE users SET password_hash = %s WHERE id = %s", (new_hash, target_id))

            # Update profile info
            updates = []
            params = []
            if full_name:
                updates.append("full_name = %s")
                params.append(full_name)
            if position:
                updates.append("position = %s")
                params.append(position)
            if signature_image is not None and signature_image != '':
                updates.append("signature_image = %s")
                params.append(signature_image)
            if profile_picture == 'REMOVE':
                updates.append("profile_picture = NULL")
            elif profile_picture:
                updates.append("profile_picture = %s")
                params.append(profile_picture)

            if updates:
                params.append(target_id)
                cur.execute(f"UPDATE users SET {', '.join(updates)} WHERE id = %s", tuple(params))

            # Fetch updated user
            cur.execute("""
                SELECT u.id, u.chapter_id, u.full_name, u.email, u.role, u.position, u.account_status,
                       u.signature_image, u.profile_picture, u.requested_at, c.chapter_name, c.chapter_code, c.region
                FROM users u
                LEFT JOIN chapters c ON c.id = u.chapter_id
                WHERE u.id = %s
                LIMIT 1
            """, (target_id,))
            updated_user = cur.fetchone()

    if session.get('user_id') == target_id:
        if full_name:
            session['user_name'] = full_name
        if position:
            session['user_position'] = position

    return api_response({'user': serialize_row(updated_user)}, 'Profile updated successfully.')


@auth_bp.route('/forgot-password', methods=['POST'])
def forgot_password():
    data = request.get_json(silent=True) or request.form.to_dict() or {}
    email = (data.get('email') or '').strip().lower()
    notes = (data.get('notes') or '').strip()

    if not email:
        return api_error('Email address is required.', 400)

    if not re.match(EMAIL_REGEX, email):
        return api_error('Please provide a valid email address.', 400)

    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("""
                SELECT u.id, u.full_name, u.email, u.role, u.position, c.chapter_name
                FROM users u
                LEFT JOIN chapters c ON c.id = u.chapter_id
                WHERE LOWER(u.email) = %s
                LIMIT 1
            """, (email,))
            user = cur.fetchone()

            if not user:
                return api_error('No registered account found with that email address. Please verify your email or request an account.', 404)

            # Check if there is already an active pending reset request
            cur.execute("""
                SELECT id FROM password_resets
                WHERE user_id = %s AND status = 'pending'
                ORDER BY id DESC LIMIT 1
            """, (user['id'],))
            existing_reset = cur.fetchone()

            if existing_reset:
                cur.execute("""
                    UPDATE password_resets
                    SET requested_at = NOW(), notes = %s
                    WHERE id = %s
                """, (notes or 'Password reset requested via login page.', existing_reset['id']))
            else:
                cur.execute("""
                    INSERT INTO password_resets (user_id, email, status, notes, requested_at)
                    VALUES (%s, %s, 'pending', %s, NOW())
                """, (user['id'], user['email'], notes or 'Password reset requested via login page.'))

            cur.execute("""
                UPDATE users
                SET password_reset_pending = 1,
                    password_reset_requested_at = NOW()
                WHERE id = %s
            """, (user['id'],))

            # Send Notification to National Admins
            notif_msg = f"{user['full_name']} ({user['chapter_name'] or 'National Admin'}) submitted a password reset request."
            cur.execute("""
                INSERT INTO notifications (user_id, title, message, related_type, related_id, target_url, is_read, created_at)
                SELECT id, 'Password Reset Requested', %s, 'account', %s, 'Manage.php', 0, NOW()
                FROM users
                WHERE role = 'national_admin' AND account_status = 'active'
            """, (notif_msg, user['id']))

    return api_response({
        'user_id': user['id'],
        'email': user['email'],
        'full_name': user['full_name']
    }, 'Password reset request submitted successfully. National Administration has been notified and can issue a temporary password for your account.')

