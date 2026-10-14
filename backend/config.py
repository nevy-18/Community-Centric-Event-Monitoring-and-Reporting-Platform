import os
from pathlib import Path
from dotenv import load_dotenv

# Locate and load the root .env file
BASE_DIR = Path(__file__).resolve().parent.parent
env_path = BASE_DIR / '.env'
if env_path.exists():
    load_dotenv(dotenv_path=env_path)
else:
    load_dotenv()

class Config:
    SECRET_KEY = os.getenv('SECRET_KEY', 'ytr-super-secret-key-2026-development')
    
    # Database Settings
    DB_HOST = os.getenv('DB_HOST', 'localhost')
    DB_PORT = int(os.getenv('DB_PORT', 3306))
    DB_NAME = os.getenv('DB_NAME', 'ytr_event_monitoring')
    DB_USER = os.getenv('DB_USER', 'root')
    DB_PASS = os.getenv('DB_PASS', '')
    
    # App Settings
    APP_NAME = os.getenv('APP_NAME', 'Youth on the Rock Platform')
    APP_ENV = os.getenv('APP_ENV', 'development')
    PORT = int(os.getenv('BACKEND_PORT', 5000))
