#!/usr/bin/env python3
"""
Youth on the Rock (YTR) - Python Backend Runner
Usage: python run_backend.py
"""
import sys
from pathlib import Path

ROOT_DIR = Path(__file__).resolve().parent
if str(ROOT_DIR) not in sys.path:
    sys.path.insert(0, str(ROOT_DIR))

from backend.app import app
from backend.config import Config

if __name__ == '__main__':
    port = Config.PORT
    print("============================================================")
    print(">> Youth on the Rock (YTR) Python Backend Server")
    print(f">> Local URL: http://127.0.0.1:{port}")
    print(f">> Health Check: http://127.0.0.1:{port}/api/health")
    print(f">> Database: {Config.DB_NAME}@{Config.DB_HOST}")
    print("============================================================")
    app.run(host='0.0.0.0', port=port, debug=True)
