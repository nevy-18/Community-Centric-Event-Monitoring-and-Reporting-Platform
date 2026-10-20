import json
from flask import Blueprint, request
from backend.db import get_db
from backend.auth_helpers import api_response, api_error

announcements_bp = Blueprint('announcements', __name__, url_prefix='/api/announcements')

DEFAULT_ANNOUNCEMENTS = [
    {
        'id': 1,
        'title': 'Tremendous Momentum: Youth Camp 2026',
        'category': 'national',
        'date': 'May 21, 2026',
        'time': '9:00 AM - 5:00 PM',
        'venue': 'SJB Learning Center Canlalay',
        'city': 'Biñan, Laguna',
        'target': 100,
        'registered': 60,
        'chapter': 'Laguna Chapter',
        'image': 'assets/images/tremendous_momentum.png',
        'excerpt': 'Join us for the ultimate showcase of the year. The "Greatest of All Time" of Youth Tech Revolution will feature live algorithm optimization challenges, full-stack architecture deep dives, and an awards ceremony for the top chapter contributors.',
        'details': 'Join us for the ultimate showcase of the year. The "Greatest of All Time" of Youth Tech Revolution will feature live algorithm optimization challenges, full-stack architecture deep dives, and an awards ceremony for the top chapter contributors.',
        'pm': 'Alex R.',
        'creative_lead': 'Maria P.',
        'tech_director': 'John K.',
        'room': 'Auditorium A (2nd Floor)',
        'parking': 'Lot B, Level-1',
        'wifi': 'YTR-Guest / goat2026',
        'reg_link': 'https://forms.gle/YcZudZkgefs1dEEn7',
        'contact_team': 'YTR - Laguna Team',
        'contact_person': 'Secretary: Rayvhen P. Muñoz'
    },
    {
        'id': 2,
        'title': '29th YTR Anniversary Celebration',
        'category': 'national',
        'date': 'Sept 28, 2026',
        'time': '8:00 AM - 6:00 PM',
        'venue': 'SJB Learning Center Canlalay',
        'city': 'Biñan, Laguna',
        'target': 250,
        'registered': 180,
        'chapter': 'National Secretariat',
        'image': 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&w=500&q=80',
        'excerpt': 'Celebrating nearly three decades of passionate youth transformation, leadership empowerment, and nation-building across every Philippine province.',
        'details': 'Commemorating 29 years of faithful community impact. All chapters nationwide are invited to join the main thanksgiving fellowship and awards ceremony honoring outstanding youth leaders.',
        'pm': 'David C.',
        'creative_lead': 'Sarah L.',
        'tech_director': 'Michael T.',
        'room': 'Main Hall (Ground Floor)',
        'parking': 'Lot A & B',
        'wifi': 'YTR-Anniversary / ytr2026',
        'reg_link': 'https://forms.gle/YcZudZkgefs1dEEn7',
        'contact_team': 'National Secretariat Team',
        'contact_person': 'Coordinator: Hannah S. Gomez'
    },
    {
        'id': 3,
        'title': 'National Youth Leadership Summit',
        'category': 'national',
        'date': 'Dec 19-20, 2026',
        'time': '9:00 AM - 4:00 PM',
        'venue': 'SMX Convention Center, Pasay City',
        'city': 'Pasay City, Metro Manila',
        'target': 400,
        'registered': 320,
        'chapter': 'National HQ',
        'image': 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=500&q=80',
        'excerpt': 'An executive gathering of chapter administrators, youth pastoring councils, and ministry executives to strategize vision 2027 and annual mission milestones.',
        'details': 'Two-day national leadership conference covering strategic planning, digital event monitoring governance, budget threshold liquidation, and youth mobilization across chapters.',
        'pm': 'Jonathan E.',
        'creative_lead': 'Rachel B.',
        'tech_director': 'Kevin M.',
        'room': 'Function Room 3 & 4',
        'parking': 'SMX Multi-Level Parking',
        'wifi': 'SMX-Event / summit2026',
        'reg_link': 'https://forms.gle/YcZudZkgefs1dEEn7',
        'contact_team': 'National Leadership Council',
        'contact_person': 'Director: Joshua M. Santos'
    },
    {
        'id': 4,
        'title': 'Greatest of All Time: Regional Fellowship',
        'category': 'chapter',
        'date': 'Sept 28, 2026',
        'time': '1:00 PM - 5:00 PM',
        'venue': 'SJB Learning Center Canlalay',
        'city': 'Biñan, Laguna',
        'target': 120,
        'registered': 60,
        'chapter': 'Laguna Chapter',
        'image': 'https://images.unsplash.com/photo-1523580494863-6f3031224c94?auto=format&fit=crop&w=500&q=80',
        'excerpt': 'High-energy praise, worship revival, and inter-campus fellowship bringing university students together for inspiration and community outreach.',
        'details': 'Laguna Chapter host gathering featuring guest worship teams, creative arts presentations, and community outreach testimony nights.',
        'pm': 'Alex R.',
        'creative_lead': 'Maria P.',
        'tech_director': 'John K.',
        'room': 'Auditorium A (2nd Floor)',
        'parking': 'Lot B, Level-1',
        'wifi': 'YTR-Guest / goat2026',
        'reg_link': 'https://forms.gle/YcZudZkgefs1dEEn7',
        'contact_team': 'YTR - Laguna Team',
        'contact_person': 'Secretary: Rayvhen P. Muñoz'
    }
]

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

    def_item = next((d for d in DEFAULT_ANNOUNCEMENTS if d['id'] == row['id']), {})

    image = row.get('poster_image') or extra.get('image') or def_item.get('image') or 'assets/images/background.jpg'
    date_val = extra.get('date') or (row['published_at'].strftime('%b %d, %Y') if row.get('published_at') else def_item.get('date', 'Upcoming'))

    return {
        'id': row['id'],
        'title': row['title'],
        'category': cat,
        'date': date_val,
        'time': extra.get('time') or def_item.get('time', '9:00 AM - 5:00 PM'),
        'venue': extra.get('venue') or def_item.get('venue', 'Venue TBD'),
        'city': extra.get('city') or def_item.get('city', 'Philippines'),
        'target': int(extra.get('target', def_item.get('target', 100))),
        'registered': int(extra.get('registered', def_item.get('registered', 0))),
        'chapter': extra.get('chapter') or def_item.get('chapter', 'Youth on the Rock'),
        'image': image,
        'excerpt': row.get('body') or def_item.get('excerpt', ''),
        'details': row.get('body') or def_item.get('details', ''),
        'pm': extra.get('pm') or def_item.get('pm', 'Event Lead'),
        'creative_lead': extra.get('creative_lead') or def_item.get('creative_lead', 'Creative Lead'),
        'tech_director': extra.get('tech_director') or def_item.get('tech_director', 'Tech Director'),
        'room': extra.get('room') or def_item.get('room', 'Main Hall'),
        'parking': extra.get('parking') or def_item.get('parking', 'General Parking'),
        'wifi': extra.get('wifi') or def_item.get('wifi', 'YTR-Guest'),
        'reg_link': extra.get('reg_link') or def_item.get('reg_link', 'https://forms.gle/YcZudZkgefs1dEEn7'),
        'contact_team': extra.get('contact_team') or def_item.get('contact_team', 'YTR Secretariat'),
        'contact_person': extra.get('contact_person') or def_item.get('contact_person', 'National Admin')
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
                ORDER BY id ASC
            """)
            rows = cur.fetchall()

            # If table is empty, auto-seed defaults into DB so all platforms stay in sync
            if not rows:
                for d in DEFAULT_ANNOUNCEMENTS:
                    extra = {
                        'time': d['time'],
                        'venue': d['venue'],
                        'city': d['city'],
                        'target': d['target'],
                        'registered': d['registered'],
                        'chapter': d['chapter'],
                        'pm': d['pm'],
                        'creative_lead': d['creative_lead'],
                        'tech_director': d['tech_director'],
                        'room': d['room'],
                        'parking': d['parking'],
                        'wifi': d['wifi'],
                        'reg_link': d['reg_link'],
                        'contact_team': d['contact_team'],
                        'contact_person': d['contact_person']
                    }
                    cur.execute("""
                        INSERT INTO announcements (created_by, target_audience, category, title, body, poster_image, extra_details, is_published, published_at)
                        VALUES (1, %s, %s, %s, %s, %s, %s, 1, NOW())
                    """, (
                        'specific_chapter' if d['category'] == 'chapter' else 'everyone',
                        d['category'],
                        d['title'],
                        d['details'],
                        d['image'],
                        json.dumps(extra)
                    ))
                conn.commit()

                cur.execute("""
                    SELECT id, created_by, target_audience, category, title, body, 
                           poster_image, extra_details, is_published, published_at 
                    FROM announcements
                    WHERE is_published = 1
                    ORDER BY id ASC
                """)
                rows = cur.fetchall()

    announcements_list = [format_announcement_row(r) for r in rows]
    return api_response({'announcements': announcements_list})

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
