from flask import Blueprint, request
from datetime import datetime, date
from backend.db import get_db, serialize_rows, serialize_row
from backend.auth_helpers import api_response, api_error

poster_requests_bp = Blueprint('poster_requests', __name__, url_prefix='/api/poster-requests')

def compute_due_and_timer(r):
    deadline = r.get('requested_deadline')
    submitted_at = r.get('submitted_at')
    updated_at = r.get('updated_at')
    status = r.get('status')
    
    today = date.today()

    # Due date badge
    if deadline:
        if isinstance(deadline, str):
            try:
                deadline_d = datetime.strptime(deadline[:10], '%Y-%m-%d').date()
            except Exception:
                deadline_d = None
        elif isinstance(deadline, datetime):
            deadline_d = deadline.date()
        elif isinstance(deadline, date):
            deadline_d = deadline
        else:
            deadline_d = None

        if deadline_d:
            diff_days = (deadline_d - today).days
            if diff_days < 0:
                r['due_badge'] = {'label': 'OVERDUE', 'cls': 'badge overdue'}
            elif diff_days <= 3:
                r['due_badge'] = {'label': f'DUE IN {diff_days}D', 'cls': 'badge due-soon'}
            else:
                r['due_badge'] = {'label': f'Due {deadline_d.strftime("%b %d")}', 'cls': 'badge'}
        else:
            r['due_badge'] = None
    else:
        r['due_badge'] = None

    # Approval timer
    if status in ('in_progress', 'completed'):
        ref_time = updated_at or submitted_at
        if ref_time:
            if isinstance(ref_time, str):
                try:
                    ref_dt = datetime.fromisoformat(ref_time.replace('Z', ''))
                except Exception:
                    ref_dt = None
            else:
                ref_dt = ref_time

            if ref_dt:
                delta = datetime.now() - ref_dt
                hours = int(delta.total_seconds() // 3600)
                days = delta.days
                if days > 0:
                    timer_str = f"Approved {days}d ago"
                elif hours > 0:
                    timer_str = f"Approved {hours}h ago"
                else:
                    mins = max(1, int(delta.total_seconds() // 60))
                    timer_str = f"Approved {mins}m ago"
                r['approval_timer'] = {'label': timer_str, 'cls': 'badge timer'}
            else:
                r['approval_timer'] = None
        else:
            r['approval_timer'] = None
    else:
        r['approval_timer'] = None

    return r

@poster_requests_bp.route('/metrics', methods=['GET'])
def get_metrics():
    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("SELECT COUNT(*) AS c FROM events WHERE status IN ('approved', 'published', 'ongoing')")
            active = cur.fetchone()['c']

            cur.execute("SELECT COUNT(*) AS c FROM poster_requests WHERE status IN ('submitted', 'under_review')")
            pending = cur.fetchone()['c']

            cur.execute("SELECT COUNT(*) AS c FROM poster_requests WHERE status = 'in_progress'")
            awaiting = cur.fetchone()['c']

            cur.execute("SELECT COUNT(*) AS c FROM poster_requests WHERE status = 'completed'")
            concluded = cur.fetchone()['c']

    return api_response({
        'total_active_events': active,
        'pending_poster_request': pending,
        'awaiting_report': awaiting,
        'concluded_events': concluded
    })

@poster_requests_bp.route('', methods=['GET'])
def list_poster_requests():
    tab = request.args.get('tab', 'accepted')

    sql = """
        SELECT pr.*, c.chapter_name, c.chapter_code,
               u.full_name AS requester_name,
               a.full_name AS assignee_name
        FROM poster_requests pr
        LEFT JOIN chapters c ON c.id = pr.chapter_id
        LEFT JOIN users u ON u.id = pr.requested_by
        LEFT JOIN users a ON a.id = pr.assigned_to
        WHERE 1=1
    """
    params = []

    if tab == 'accepted':
        sql += " AND pr.status IN ('in_progress', 'completed')"
    elif tab == 'queue':
        sql += " AND pr.status IN ('submitted', 'under_review', 'revision_requested')"

    sql += " ORDER BY pr.submitted_at DESC"

    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute(sql, tuple(params))
            requests = cur.fetchall()

    enriched = [compute_due_and_timer(r) for r in requests]
    return api_response(serialize_rows(enriched))

@poster_requests_bp.route('/<int:request_id>/approve', methods=['POST'])
def approve_poster_request(request_id):
    data = request.get_json(silent=True) or {}
    review_note = data.get('review_note', 'Approved by National Admin')
    reviewer_id = data.get('reviewed_by', 1)

    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("SELECT id, status FROM poster_requests WHERE id = %s", (request_id,))
            req = cur.fetchone()
            if not req:
                return api_error('Poster request not found.', 404)

            cur.execute("""
                UPDATE poster_requests
                SET status = 'in_progress',
                    reviewed_by = %s,
                    review_note = %s,
                    updated_at = NOW()
                WHERE id = %s
            """, (reviewer_id, review_note, request_id))
            conn.commit()

    return api_response({'message': 'Poster request approved and moved to in_progress queue.'})

@poster_requests_bp.route('/<int:request_id>/reject', methods=['POST'])
def reject_poster_request(request_id):
    data = request.get_json(silent=True) or {}
    review_note = data.get('review_note', 'Poster request declined by reviewer')
    reviewer_id = data.get('reviewed_by', 1)

    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("SELECT id, status FROM poster_requests WHERE id = %s", (request_id,))
            req = cur.fetchone()
            if not req:
                return api_error('Poster request not found.', 404)

            cur.execute("""
                UPDATE poster_requests
                SET status = 'rejected',
                    reviewed_by = %s,
                    review_note = %s,
                    updated_at = NOW()
                WHERE id = %s
            """, (reviewer_id, review_note, request_id))
            conn.commit()

    return api_response({'message': 'Poster request rejected.'})

import os
import json
import time
from werkzeug.utils import secure_filename

DEFAULT_TIMELINE_STEPS = [
    {
        'step_key': 'poster_request',
        'step_order': 1,
        'step_title': 'Poster Request',
        'subtext': 'By Communications, Marketing & Creative Team',
        'default_status': 'pending'
    },
    {
        'step_key': 'posted_on_page',
        'step_order': 2,
        'step_title': 'Posted on Page',
        'subtext': 'Official publication on social media page with live engagement',
        'default_status': 'pending'
    },
    {
        'step_key': 'promotional_video',
        'step_order': 3,
        'step_title': 'Promotional Video',
        'subtext': 'Chapter / Media team promotional teaser clip submission',
        'default_status': 'pending'
    },
    {
        'step_key': 'documentation_and_report',
        'step_order': 4,
        'step_title': 'Documentation and Report',
        'subtext': 'Post-event documentation, attendees, and accomplishment report',
        'default_status': 'pending'
    },
    {
        'step_key': 'event_concluded',
        'step_order': 5,
        'step_title': 'Event Concluded and Reported',
        'subtext': 'Event concluded, all deliverables verified, and archived',
        'default_status': 'pending'
    }
]

def ensure_steps_exist(cur, request_id, req_status):
    cur.execute("SELECT COUNT(*) AS c FROM poster_request_steps WHERE poster_request_id = %s", (request_id,))
    cnt = cur.fetchone()['c']
    if cnt == 0:
        for s in DEFAULT_TIMELINE_STEPS:
            initial_status = 'pending'
            if req_status in ('in_progress', 'completed') and s['step_order'] == 1:
                initial_status = 'completed'
            elif req_status in ('in_progress',) and s['step_order'] == 2:
                initial_status = 'in_progress'
            elif req_status == 'completed':
                initial_status = 'completed'

            cur.execute("""
                INSERT INTO poster_request_steps
                (poster_request_id, step_key, step_order, step_title, status, notes, completed_at, created_at)
                VALUES (%s, %s, %s, %s, %s, %s, IF(%s = 'completed', NOW(), NULL), NOW())
            """, (request_id, s['step_key'], s['step_order'], s['step_title'], initial_status, s['subtext'], initial_status))

@poster_requests_bp.route('', methods=['POST'])
def create_poster_request():
    # Support both JSON and multipart/form-data
    if request.is_json:
        data = request.get_json(silent=True) or {}
    else:
        data = request.form.to_dict()

    chapter_id = data.get('chapter_id')
    event_title = data.get('event_title')
    event_date = data.get('event_date')
    event_time = data.get('event_time', '')
    venue = data.get('venue', '')
    theme = data.get('theme_or_concept', '')
    target_participants = data.get('target_participants', '')
    registration_link = data.get('registration_link', '')
    caption = data.get('caption', '')
    deadline = data.get('requested_deadline')
    requested_by = data.get('requested_by', 1)
    notes = data.get('additional_notes', '')

    if not chapter_id or not event_title or not event_date:
        return api_error('chapter_id, event_title, and event_date are required.', 400)

    reference_poster_path = 'assets/images/background.jpg'
    if 'reference_poster' in request.files:
        file = request.files['reference_poster']
        if file and file.filename:
            fname = secure_filename(file.filename)
            unique_fname = f"ref_{int(time.time())}_{fname}"
            os.makedirs('assets/uploads/posters', exist_ok=True)
            save_path = os.path.join('assets/uploads/posters', unique_fname)
            file.save(save_path)
            reference_poster_path = f"assets/uploads/posters/{unique_fname}"

    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("""
                INSERT INTO poster_requests
                (chapter_id, requested_by, event_title, event_date, event_time, venue, theme_or_concept,
                 target_participants, registration_link, caption, requested_deadline, additional_notes,
                 reference_poster_path, status, current_step, submitted_at)
                VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 'submitted', 1, NOW())
            """, (chapter_id, requested_by, event_title, event_date, event_time, venue, theme,
                  target_participants, registration_link, caption, deadline, notes, reference_poster_path))
            new_id = cur.lastrowid
            ensure_steps_exist(cur, new_id, 'submitted')
            conn.commit()

    return api_response({'message': 'Poster request submitted successfully.', 'request_id': new_id}, 201)

@poster_requests_bp.route('/<int:request_id>/timeline', methods=['GET'])
def get_poster_timeline(request_id):
    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("""
                SELECT pr.*, c.chapter_name, c.chapter_code,
                       u.full_name AS requester_name,
                       a.full_name AS assignee_name,
                       r.full_name AS reviewer_name
                FROM poster_requests pr
                LEFT JOIN chapters c ON c.id = pr.chapter_id
                LEFT JOIN users u ON u.id = pr.requested_by
                LEFT JOIN users a ON a.id = pr.assigned_to
                LEFT JOIN users r ON r.id = pr.reviewed_by
                WHERE pr.id = %s
            """, (request_id,))
            req = cur.fetchone()
            if not req:
                return api_error('Poster request not found.', 404)

            # Ensure steps exist
            ensure_steps_exist(cur, request_id, req['status'])
            conn.commit()

            # Fetch steps
            cur.execute("""
                SELECT * FROM poster_request_steps
                WHERE poster_request_id = %s
                ORDER BY step_order ASC
            """, (request_id,))
            steps = cur.fetchall()

            # Fetch updates / proofs
            cur.execute("""
                SELECT pru.*, u.full_name AS updater_name
                FROM poster_request_updates pru
                LEFT JOIN users u ON u.id = pru.updated_by
                WHERE pru.poster_request_id = %s
                ORDER BY pru.created_at DESC
            """, (request_id,))
            updates = cur.fetchall()

    enriched_req = compute_due_and_timer(req)
    return api_response({
        'request': serialize_row(enriched_req),
        'steps': serialize_rows(steps),
        'updates': serialize_rows(updates)
    })

@poster_requests_bp.route('/<int:request_id>/verify-approve', methods=['POST'])
def verify_approve_poster_request(request_id):
    data = request.get_json(silent=True) or {}
    checklist = data.get('checklist', [])
    review_note = data.get('review_note', 'Verified & Approved by National Admin')
    reviewer_id = data.get('reviewed_by', 1)

    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("SELECT id, status FROM poster_requests WHERE id = %s", (request_id,))
            req = cur.fetchone()
            if not req:
                return api_error('Poster request not found.', 404)

            ensure_steps_exist(cur, request_id, req['status'])

            checklist_json = json.dumps(checklist)
            cur.execute("""
                UPDATE poster_requests
                SET status = 'in_progress',
                    current_step = 2,
                    checklist_verified = %s,
                    reviewed_by = %s,
                    review_note = %s,
                    updated_at = NOW()
                WHERE id = %s
            """, (checklist_json, reviewer_id, review_note, request_id))

            # Mark step 1 completed, step 2 in_progress
            cur.execute("""
                UPDATE poster_request_steps
                SET status = 'completed',
                    completed_at = NOW(),
                    notes = %s,
                    updated_by = %s
                WHERE poster_request_id = %s AND step_order = 1
            """, (review_note, reviewer_id, request_id))

            cur.execute("""
                UPDATE poster_request_steps
                SET status = 'in_progress'
                WHERE poster_request_id = %s AND step_order = 2 AND status = 'pending'
            """, (request_id,))

            cur.execute("""
                INSERT INTO poster_request_updates
                (poster_request_id, updated_by, update_type, message, created_at)
                VALUES (%s, %s, 'status_changed', 'Pre-approval checklist verified. Request approved and moved to design production.', NOW())
            """, (request_id, reviewer_id))

            conn.commit()

    return api_response({
        'message': 'Poster request verified and approved. Moved to Accepted pipeline.',
        'status': 'in_progress'
    })

@poster_requests_bp.route('/<int:request_id>/timeline-step', methods=['POST'])
def update_timeline_step(request_id):
    if request.is_json:
        data = request.get_json(silent=True) or {}
    else:
        data = request.form.to_dict()

    step_order = int(data.get('step_order', 1))
    step_status = data.get('step_status', 'completed')
    notes = data.get('notes', '')
    is_final_poster = str(data.get('is_final_poster', 'false')).lower() in ('true', '1')
    updater_id = int(data.get('updated_by', 1))

    proof_file_path = None
    if 'proof_file' in request.files:
        file = request.files['proof_file']
        if file and file.filename:
            fname = secure_filename(file.filename)
            unique_fname = f"proof_{request_id}_s{step_order}_{int(time.time())}_{fname}"
            subfolder = 'posters' if is_final_poster else 'proofs'
            target_dir = os.path.join('assets', 'uploads', subfolder)
            os.makedirs(target_dir, exist_ok=True)
            save_path = os.path.join(target_dir, unique_fname)
            file.save(save_path)
            proof_file_path = f"assets/uploads/{subfolder}/{unique_fname}"
    elif data.get('proof_file_path'):
        proof_file_path = data.get('proof_file_path')

    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("SELECT id, status FROM poster_requests WHERE id = %s", (request_id,))
            req = cur.fetchone()
            if not req:
                return api_error('Poster request not found.', 404)

            ensure_steps_exist(cur, request_id, req['status'])

            # Update step record
            sql_update_step = """
                UPDATE poster_request_steps
                SET status = %s,
                    notes = COALESCE(NULLIF(%s, ''), notes),
                    proof_file_path = COALESCE(%s, proof_file_path),
                    updated_by = %s,
                    completed_at = IF(%s = 'completed', NOW(), completed_at)
                WHERE poster_request_id = %s AND step_order = %s
            """
            cur.execute(sql_update_step, (step_status, notes, proof_file_path, updater_id, step_status, request_id, step_order))

            # Update poster_requests if final poster or concluded
            if is_final_poster and proof_file_path:
                cur.execute("UPDATE poster_requests SET final_poster_path = %s WHERE id = %s", (proof_file_path, request_id))

            if step_order == 5 and step_status == 'completed':
                cur.execute("UPDATE poster_requests SET status = 'completed', updated_at = NOW() WHERE id = %s", (request_id,))
            elif step_status == 'completed' and step_order < 5:
                # Advance next step to in_progress if currently pending
                cur.execute("""
                    UPDATE poster_request_steps
                    SET status = 'in_progress'
                    WHERE poster_request_id = %s AND step_order = %s AND status = 'pending'
                """, (request_id, step_order + 1))
                cur.execute("UPDATE poster_requests SET current_step = %s, updated_at = NOW() WHERE id = %s", (step_order + 1, request_id))

            # Log update
            update_msg = notes or f"Timeline Step {step_order} updated to {step_status}."
            cur.execute("""
                INSERT INTO poster_request_updates
                (poster_request_id, updated_by, update_type, message, proof_file_path, created_at)
                VALUES (%s, %s, %s, %s, %s, NOW())
            """, (request_id, updater_id, 'proof_uploaded' if proof_file_path else 'step_updated', update_msg, proof_file_path))

            conn.commit()

    return api_response({
        'message': 'Timeline step and proof updated successfully.',
        'proof_file_path': proof_file_path
    })
