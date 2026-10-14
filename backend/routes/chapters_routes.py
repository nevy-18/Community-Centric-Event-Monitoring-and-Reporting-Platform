from flask import Blueprint, jsonify
from backend.db import get_db, serialize_rows, serialize_row
from backend.auth_helpers import api_response, api_error

chapters_bp = Blueprint('chapters', __name__, url_prefix='/api/chapters')

@chapters_bp.route('', methods=['GET'])
def list_chapters():
    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("""
                SELECT id, chapter_code, chapter_name, generation_name, region, province, city_municipality, status
                FROM chapters
                WHERE status = 'active'
                ORDER BY chapter_name ASC
            """)
            chapters = cur.fetchall()

    return api_response(serialize_rows(chapters))

@chapters_bp.route('/<int:chapter_id>', methods=['GET'])
def get_chapter(chapter_id):
    with get_db() as conn:
        with conn.cursor() as cur:
            cur.execute("SELECT * FROM chapters WHERE id = %s", (chapter_id,))
            chapter = cur.fetchone()

    if not chapter:
        return api_error('Chapter not found.', 404)

    return api_response(serialize_row(chapter))
