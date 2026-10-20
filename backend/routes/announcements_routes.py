import json
from flask import Blueprint, request
from backend.db import get_db
from backend.auth_helpers import api_response, api_error

announcements_bp = Blueprint('announcements', __name__, url_prefix='/api/announcements')

def format_announcement_row(row):
    extra = {}
    if row.get('extra_details'):
        try:
            extra = json.loads(row['extra_details']) if isinstance(row['extra_details'], str) else row['extra_details']
        except Exception:
            extra = {}

    # Map category to match frontend expectation ('national', 'chapter')
    raw_cat = row.get('category') or 'national'
    cat = 'chapter' if raw_cat in ('chapter', 'specific_chapter') else 'national'

    image = row.get('poster_image') or extra.get('image') or 'assets/images/background.jpg'
    date_val = extra.get('date') or (row['published_at'].strftime('%b %d, %Y') if row.get('published_at') else 'Upcoming')

    return {
        'id': row['id'],
        'title': row['title'],
        'category': cat,
        'date': date_val,
        'time': extra.get('time') or '9:00 AM - 5:00 PM',
        'venue': extra.get('venue') or 'Venue TBD',
        'city': extra.get('city') or 'Philippines',
        'target': int(extra.get('target', 100)),
        'registered': int(extra.get('registered', 0)),
        'chapter': extra.get('chapter') or 'Youth on the Rock',
        'image': image,
        'excerpt': row.get('body') or '',
        'details': row.get('body') or '',
        'pm': extra.get('pm') or 'Event Lead',
        'creative_lead': extra.get('creative_lead') or 'Creative Lead',
        'tech_director': extra.get('tech_director') or 'Tech Director',
        'room': extra.get('room') or 'Main Hall',
        'parking': extra.get('parking') or 'General Parking',
        'wifi': extra.get('wifi') or 'YTR-Guest',
        'reg_link': extra.get('reg_link') or '',
        'contact_team': extra.get('contact_team') or 'YTR Secretariat',
        'contact_person': extra.get('contact_person') or 'National Admin'
    }

@announcements_bp.route('', methods=['GET'])
def get_announcements():
    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("""
                SELECT id, created_by, target_audience, category, title, body, 
                       poster_image, extra_details, is_published, published_at 
                FROM announcements
                WHERE is_published = 1
                ORDER BY published_at DESC, id DESC
            """)
            rows = cur.fetchall()

    announcements_list = [format_announcement_row(r) for r in rows]
    return api_response({'announcements': announcements_list}, 'Announcements retrieved successfully')

@announcements_bp.route('/<int:announcement_id>/photo', methods=['POST'])
def update_announcement_photo(announcement_id):
    """
    1-Click Announcement Poster Photo Update
    Immediately persists the replacement photo to Aiven MySQL so EVERY user,
    chapter, and visitor across the entire network sees the updated photo.
    """
    data = request.get_json(silent=True) or {}
    image_url = data.get('image')
    if not image_url:
        return api_error('Image payload is required.', 400)

    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("SELECT id, extra_details FROM announcements WHERE id = %s", (announcement_id,))
            row = cur.fetchone()
            if not row:
                return api_error('Announcement not found.', 404)

            # Update poster_image column
            cur.execute("""
                UPDATE announcements 
                SET poster_image = %s, updated_at = NOW() 
                WHERE id = %s
            """, (image_url, announcement_id))
            conn.commit()

    return api_response({
        'id': announcement_id,
        'image': image_url,
        'message': 'Announcement poster photo updated successfully across all chapters and devices!'
    })

@announcements_bp.route('/<int:announcement_id>', methods=['PUT'])
def update_announcement(announcement_id):
    """
    Full Announcement Details Update
    Persists title, body, category, dates, venue, and poster_image to cloud DB.
    """
    data = request.get_json(silent=True) or {}
    title = (data.get('title') or '').strip()
    if not title:
        return api_error('Title is required.', 400)

    details = data.get('details') or data.get('excerpt') or ''
    category = data.get('category') or 'national'
    poster_image = data.get('image')

    extra = {
        'date': data.get('date'),
        'time': data.get('time'),
        'venue': data.get('venue'),
        'city': data.get('city'),
        'target': data.get('target', 100),
        'registered': data.get('registered', 0),
        'chapter': data.get('chapter', 'Youth on the Rock'),
        'pm': data.get('pm'),
        'creative_lead': data.get('creative_lead'),
        'tech_director': data.get('tech_director'),
        'room': data.get('room'),
        'parking': data.get('parking'),
        'wifi': data.get('wifi'),
        'reg_link': data.get('reg_link'),
        'contact_team': data.get('contact_team'),
        'contact_person': data.get('contact_person')
    }

    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("SELECT id FROM announcements WHERE id = %s", (announcement_id,))
            if not cur.fetchone():
                return api_error('Announcement not found.', 404)

            updates = [
                "title = %s",
                "body = %s",
                "category = %s",
                "extra_details = %s",
                "updated_at = NOW()"
            ]
            params = [title, details, category, json.dumps(extra)]

            if poster_image:
                updates.append("poster_image = %s")
                params.append(poster_image)

            params.append(announcement_id)

            sql = f"UPDATE announcements SET {', '.join(updates)} WHERE id = %s"
            cur.execute(sql, tuple(params))
            conn.commit()

            cur.execute("""
                SELECT id, created_by, target_audience, category, title, body, 
                       poster_image, extra_details, is_published, published_at 
                FROM announcements WHERE id = %s
            """, (announcement_id,))
            updated_row = cur.fetchone()

    return api_response({
        'announcement': format_announcement_row(updated_row),
        'message': 'Announcement updated successfully across all chapters!'
    })

@announcements_bp.route('', methods=['POST'])
def create_announcement():
    """
    Publish a new announcement into cloud database
    """
    data = request.get_json(silent=True) or {}
    title = (data.get('title') or '').strip()
    if not title:
        return api_error('Title is required.', 400)

    details = data.get('details') or data.get('excerpt') or ''
    category = data.get('category') or 'national'
    poster_image = data.get('image') or 'assets/images/background.jpg'

    extra = {
        'date': data.get('date'),
        'time': data.get('time'),
        'venue': data.get('venue'),
        'city': data.get('city'),
        'target': data.get('target', 100),
        'registered': data.get('registered', 0),
        'chapter': data.get('chapter', 'Youth on the Rock'),
        'pm': data.get('pm'),
        'creative_lead': data.get('creative_lead'),
        'tech_director': data.get('tech_director'),
        'room': data.get('room'),
        'parking': data.get('parking'),
        'wifi': data.get('wifi'),
        'reg_link': data.get('reg_link'),
        'contact_team': data.get('contact_team'),
        'contact_person': data.get('contact_person')
    }

    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("""
                INSERT INTO announcements (
                    created_by, target_audience, category, title, body, 
                    poster_image, extra_details, is_published, published_at
                ) VALUES (1, %s, %s, %s, %s, %s, %s, 1, NOW())
            """, (
                'specific_chapter' if category == 'chapter' else 'everyone',
                category,
                title,
                details,
                poster_image,
                json.dumps(extra)
            ))
            new_id = cur.lastrowid
            conn.commit()

            cur.execute("""
                SELECT id, created_by, target_audience, category, title, body, 
                       poster_image, extra_details, is_published, published_at 
                FROM announcements WHERE id = %s
            """, (new_id,))
            new_row = cur.fetchone()

    return api_response({
        'announcement': format_announcement_row(new_row),
        'message': 'Announcement created and published successfully!'
    }, status_code=201)

@announcements_bp.route('/<int:announcement_id>', methods=['DELETE'])
def delete_announcement(announcement_id):
    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("DELETE FROM announcements WHERE id = %s", (announcement_id,))
            conn.commit()
    return api_response({'message': 'Announcement deleted successfully.'})
