@echo off
title YTR Python Backend Server (Port 5000)
cd /d "%~dp0"
echo ============================================================
echo Starting Youth on the Rock (YTR) Python Backend...
echo Local URL: http://127.0.0.1:5000
echo ============================================================
python run_backend.py
pause
