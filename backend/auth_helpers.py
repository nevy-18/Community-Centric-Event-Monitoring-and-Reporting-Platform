import bcrypt
from flask import session, jsonify

def hash_password(password: str) -> str:
    """Generate a bcrypt password hash."""
    salt = bcrypt.gensalt(rounds=10)
    # Output hash as string
    hashed = bcrypt.hashpw(password.encode('utf-8'), salt)
    return hashed.decode('utf-8')

def verify_password(password: str, hashed_password: str) -> bool:
    """Verify password against bcrypt hash (supports standard $2b$ and PHP $2y$ prefix)."""
    if not hashed_password:
        return False
    # Normalize PHP $2y$ to $2b$ for python bcrypt
    normalized_hash = hashed_password.replace('$2y$', '$2b$').encode('utf-8')
    return bcrypt.checkpw(password.encode('utf-8'), normalized_hash)

def api_response(data=None, message="Success", status_code=200):
    """Standardized API response format."""
    payload = {
        "status": "success",
        "message": message,
        "data": data if data is not None else {}
    }
    return jsonify(payload), status_code

def api_error(message="An error occurred", status_code=400, errors=None):
    """Standardized API error response format."""
    payload = {
        "status": "error",
        "message": message
    }
    if errors is not None:
        payload["errors"] = errors
    return jsonify(payload), status_code
