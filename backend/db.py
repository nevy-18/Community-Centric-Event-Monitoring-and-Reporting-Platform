import pymysql
import pymysql.cursors
from backend.config import Config
from contextlib import contextmanager
from datetime import datetime, date

def get_db_connection():
    """Establish and return a PyMySQL connection with dictionary cursor."""
    return pymysql.connect(
        host=Config.DB_HOST,
        port=Config.DB_PORT,
        user=Config.DB_USER,
        password=Config.DB_PASS,
        database=Config.DB_NAME,
        charset='utf8mb4',
        cursorclass=pymysql.cursors.DictCursor,
        autocommit=False
    )

@contextmanager
def get_db():
    """Context manager for safe database transactions and auto-closing."""
    conn = get_db_connection()
    try:
        yield conn
        conn.commit()
    except Exception:
        conn.rollback()
        raise
    finally:
        conn.close()

def serialize_row(row):
    """Serialize datetime and date objects in a dictionary to ISO format strings."""
    if not row:
        return row
    serialized = dict(row)
    for k, v in serialized.items():
        if isinstance(v, (datetime, date)):
            serialized[k] = v.isoformat()
    return serialized

def serialize_rows(rows):
    """Serialize a list of database rows."""
    return [serialize_row(r) for r in rows]
