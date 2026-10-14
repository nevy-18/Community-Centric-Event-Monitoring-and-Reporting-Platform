# Youth on the Rock (YTR) - Community-Centric Event Monitoring and Reporting Platform

A centralized, community-centric event monitoring, proposal approval, and chapter reporting platform built for **Youth on the Rock (YTR)**. The platform features an administrative portal for National Admins and Chapter Admins to manage events, evaluate proposals, track poster production workflows, and monitor community attendance.

---

## 🛠️ Architecture Overview

The system utilizes a decoupled architecture with a **Python REST API backend** and an **interactive frontend**:

- **Backend / API**: Built in **Python 3.14+** using **Flask**, **PyMySQL**, **Flask-CORS**, and **bcrypt** for robust security and database transactions.
- **Database**: **MySQL / MariaDB** (`ytr_event_monitoring`) with relational foreign key integrity, audit trails, and progress templates.
- **Frontend**: Responsive UI with PHP/HTML views, styled with Phosphor Icons and Google Fonts (`Roboto` & `Inter`).
- **Client Integration**: Seamless communication via `assets/api-client.js` communicating directly with the Python backend server running on `http://127.0.0.1:5000/api`.

---

## 📋 Onboarding & Setup Checklist Status

| # | Task | Status | Details |
|---|---|:---:|---|
| 1 | Create project repository | ✅ Done | Initialized on branch `new_work` with remote repository. |
| 2 | Create frontend application | ✅ Done | Pages: `login.php`, `Dashboard.php`, `Proposal.php`, `Manage.php`, `Request.php`, `Report.php`. |
| 3 | Create backend application/API | ✅ Done | Python Flask REST API (`backend/`) covering Auth, Chapters, Accounts, Proposals, Reports, and Requests. |
| 4 | Install required dependencies | ✅ Done | Flask, flask-cors, python-dotenv, PyMySQL, bcrypt, Phosphor Icons. |
| 5 | Configure environment variables | ✅ Done | `.env` and `.env.example` configured for local development. |
| 6 | Connect development database | ✅ Done | `backend/db.py` connects to MySQL `ytr_event_monitoring`. |
| 7 | Create project folder structure | ✅ Done | Clean structure (`backend/`, `assets/`, `includes/`, `sql/`, `config/`). |
| 8 | Create reusable component structure | ✅ Done | Sidebar navigation (`includes/sidebar.php`), Topbar header (`includes/topbar.php`). |
| 9 | Confirm frontend runs | ✅ Done | Served via Apache (`http://localhost/Community-Centric-Event-Monitoring-and-Reporting-Platform%20-%20Copy`). |
| 10 | Confirm backend runs | ✅ Done | Python Flask server running on `http://127.0.0.1:5000`. |
| 11 | Confirm database connection works | ✅ Done | Automated tests pass all 9 endpoint integration checks. |
| 12 | Create initial README/project notes | ✅ Done | Comprehensive documentation created. |

---

## 🚀 Quick Start Guide

### 1. Prerequisites
- **Python 3.10+** (Python 3.14 supported)
- **XAMPP / WampServer** (with Apache and MySQL / MariaDB active on port 3306)
- **Git**

### 2. Database Setup
1. Open phpMyAdmin or your MySQL CLI: `http://localhost/phpmyadmin`
2. Import the database schema from [`sql/database.sql`](sql/database.sql):
   ```sql
   source sql/database.sql;
   ```
   This creates the database `ytr_event_monitoring` and 14 relational tables.

### 3. Environment Variables
Copy `.env.example` to `.env` (if not already present):
```ini
DB_HOST=localhost
DB_PORT=3306
DB_NAME=ytr_event_monitoring
DB_USER=root
DB_PASS=

APP_NAME="Youth on the Rock Platform"
APP_ENV=development
BACKEND_PORT=5000
```

### 4. Install Python Dependencies
```bash
pip install -r backend/requirements.txt
```

### 5. Seed Initial Development Data
Run the Python seeder to populate sample chapters, users, events, and reports:
```bash
python backend/seed.py
```

### 6. Start the Python Backend API
Run the backend runner script:
```bash
python run_backend.py
```
> The API server will start on **`http://127.0.0.1:5000`** with CORS support.

### 7. Run Test Suite
Verify that all API endpoints are healthy:
```bash
python backend/test_api.py
```

---

## 🔑 Default Test Accounts

| Role | Email | Password | Access Scope |
|---|---|---|---|
| **National Admin** | `admin@ytr.org` | `Password123!` | Full platform administration, account approvals, proposal reviews |
| **Chapter Admin** | `joan.dela@gmail.com` | `Password123!` | Chapter-specific dashboard, event proposal submissions |

---

## 📡 Python REST API Reference

All API routes are prefixed with `/api`.

### 1. Health Check
- `GET /api/health`: Returns server status, version, and database connection state.

### 2. Authentication (`/api/auth`)
- `POST /api/auth/login`: Authenticate user with bcrypt hash verification.
- `POST /api/auth/register`: Submit a new chapter admin or national admin account request.
- `POST /api/auth/logout`: Destroy active session.
- `GET /api/auth/me`: Retrieve currently logged-in user profile.

### 3. Chapters (`/api/chapters`)
- `GET /api/chapters`: List all active chapters for dropdowns and filter selectors.
- `GET /api/chapters/<id>`: Retrieve specific chapter details.

### 4. Account Management (`/api/accounts`)
- `GET /api/accounts/metrics`: Count of total accounts, pending requests, national admins, and chapter admins.
- `GET /api/accounts?filter=pending&search=`: List user accounts with filtering (`all`, `pending`, `national_admins`, `chapter_admins`, `disabled`).
- `GET /api/accounts/<id>`: Details of a single user account for review.
- `POST /api/accounts/<id>/approve`: Approve user account and assign role and chapter.
- `POST /api/accounts/<id>/decline`: Decline account request with review notes.

### 5. Chapter Event Proposals (`/api/proposals`)
- `GET /api/proposals/metrics`: Count of total proposals, pending review, approved, and needs revision.
- `GET /api/proposals?status=all&chapter_id=all&search=`: List event proposals with multi-parameter filtering.
- `GET /api/proposals/<id>`: Detailed proposal record with venue, budget, and description.
- `POST /api/proposals`: Submit a new event proposal (modal form).
- `POST /api/proposals/<id>/review`: Approve (`approved`) or request revision (`draft`) on a proposal.

### 6. Reports & Analytics (`/api/reports`)
- `GET /api/reports/metrics`: Active events count, completed events count, and first-timer attendance total.
- `GET /api/reports?filter=completed`: List event turnout reports.

### 7. Poster Requests (`/api/poster-requests`)
- `GET /api/poster-requests/metrics`: Counts of active events, pending posters, awaiting report, and concluded events.
- `GET /api/poster-requests?tab=accepted`: List poster requests by tab (`accepted` or `queue`).

---

## 📂 Project Directory Structure

```
Community-Centric-Event-Monitoring-and-Reporting-Platform/
├── backend/
│   ├── routes/
│   │   ├── auth_routes.py             # Auth endpoints (login, register, logout, me)
│   │   ├── chapters_routes.py         # Chapters list and lookup
│   │   ├── accounts_routes.py         # User approvals & management
│   │   ├── proposals_routes.py        # Event proposals CRUD & review
│   │   ├── reports_routes.py          # Attendance and turnout reports
│   │   └── poster_requests_routes.py  # Poster approval workflow
│   ├── app.py                         # Flask application & blueprint registry
│   ├── config.py                      # Environment and DB configuration
│   ├── db.py                          # PyMySQL connection pool and helpers
│   ├── auth_helpers.py                # Password hashing and response formatters
│   ├── seed.py                        # Python database seeder
│   ├── test_api.py                    # Automated test suite (9 integration tests)
│   └── requirements.txt               # Python package dependencies
├── assets/
│   ├── api-client.js                  # Centralized JavaScript client for Python API
│   ├── dashboard.css                  # Core application styling
│   ├── script.js                      # Login and registration interactive handlers
│   └── images/                        # Logos and icons
├── includes/
│   ├── sidebar.php                    # Navigation bar with Chapter Event Proposals tab
│   └── topbar.php                     # Profile header and search
├── sql/
│   └── database.sql                   # MySQL / MariaDB schema (14 tables & views)
├── .env                               # Local environment configuration (untracked)
├── .env.example                       # Sample environment configuration
├── .gitignore                         # Git ignore rules
├── run_backend.py                     # Root CLI runner for Python backend server
├── login.php                          # Sign in and account request page
├── Dashboard.php                      # Main overview dashboard
├── Proposal.php                       # Chapter Event Proposals screen & submission modal
├── Manage.php                         # Account access review & role assignment screen
├── Request.php                        # Event poster approval workflow screen
├── Report.php                         # Chapter turnout reports & analytics
└── README.md                          # Documentation and setup guide
```

---

## 🔒 Security Notes
- Passwords are encrypted using **bcrypt** (cost factor 10).
- Cross-Site Request considerations handled via Flask-CORS and JSON requests.
- Sensitive credentials must remain in `.env` and are excluded from Git via `.gitignore`.