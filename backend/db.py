import pymysql
import pymysql.cursors
from backend.config import Config
from contextlib import contextmanager
from datetime import datetime, date
from decimal import Decimal

def get_db_connection():
    """Establish and return a PyMySQL connection with dictionary cursor."""
    conn_params = {
        'host': Config.DB_HOST,
        'port': Config.DB_PORT,
        'user': Config.DB_USER,
        'password': Config.DB_PASS,
        'database': Config.DB_NAME,
        'charset': 'utf8mb4',
        'cursorclass': pymysql.cursors.DictCursor,
        'autocommit': False
    }
    if getattr(Config, 'DB_SSL', False):
        conn_params['ssl'] = {'check_hostname': False}
    conn = pymysql.connect(**conn_params)
    try:
        with conn.cursor() as cur:
            cur.execute("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))")
    except Exception:
        pass
    return conn

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
    """Serialize datetime, date, and Decimal objects in a dictionary."""
    if not row:
        return row
    serialized = dict(row)
    for k, v in serialized.items():
        if isinstance(v, (datetime, date)):
            serialized[k] = v.isoformat()
        elif isinstance(v, Decimal):
            serialized[k] = float(v)
    return serialized

def serialize_rows(rows):
    """Serialize a list of database rows."""
    return [serialize_row(r) for r in rows]
