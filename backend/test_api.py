"""
Comprehensive Test Suite for YTR Python Backend Endpoints
Usage: python backend/test_api.py
"""
import sys
from pathlib import Path
import unittest
import json

ROOT_DIR = Path(__file__).resolve().parent.parent
if str(ROOT_DIR) not in sys.path:
    sys.path.insert(0, str(ROOT_DIR))

from backend.app import app

class TestYTRBackendAPI(unittest.TestCase):
    def setUp(self):
        self.client = app.test_client()
        self.client.testing = True

    def test_01_health_check(self):
        response = self.client.get('/api/health')
        self.assertEqual(response.status_code, 200)
        data = response.get_json()
        self.assertEqual(data.get('status'), 'healthy')
        print("[PASS] Health check passed:", data.get('service'))

    def test_02_chapters_list(self):
        response = self.client.get('/api/chapters')
        self.assertEqual(response.status_code, 200)
        data = response.get_json()
        self.assertEqual(data.get('status'), 'success')
        self.assertTrue(len(data.get('data', [])) >= 1)
        print(f"[PASS] Chapters API passed: Loaded {len(data['data'])} active chapters.")

    def test_03_login_admin(self):
        payload = {
            'email': 'admin@ytr.org',
            'password': 'Password123!'
        }
        response = self.client.post('/api/auth/login', json=payload)
        self.assertEqual(response.status_code, 200)
        data = response.get_json()
        self.assertEqual(data.get('status'), 'success')
        self.assertEqual(data['data']['user']['role'], 'national_admin')
        print("[PASS] Admin login passed:", data['data']['user']['full_name'])

    def test_04_accounts_metrics(self):
        response = self.client.get('/api/accounts/metrics')
        self.assertEqual(response.status_code, 200)
        data = response.get_json()
        self.assertEqual(data.get('status'), 'success')
        self.assertIn('total_accounts', data.get('data', {}))
        print("[PASS] Accounts metrics passed:", data.get('data'))

    def test_05_accounts_list(self):
        response = self.client.get('/api/accounts?filter=all')
        self.assertEqual(response.status_code, 200)
        data = response.get_json()
        self.assertEqual(data.get('status'), 'success')
        self.assertTrue(len(data.get('data', [])) >= 1)
        print(f"[PASS] Accounts list passed: {len(data['data'])} accounts found.")

    def test_06_proposals_metrics(self):
        response = self.client.get('/api/proposals/metrics')
        self.assertEqual(response.status_code, 200)
        data = response.get_json()
        self.assertEqual(data.get('status'), 'success')
        self.assertIn('total_proposals', data.get('data', {}))
        print("[PASS] Proposals metrics passed:", data.get('data'))

    def test_07_proposals_list(self):
        response = self.client.get('/api/proposals')
        self.assertEqual(response.status_code, 200)
        data = response.get_json()
        self.assertEqual(data.get('status'), 'success')
        print(f"[PASS] Proposals list passed: {len(data.get('data', []))} proposals found.")

    def test_08_reports_metrics(self):
        response = self.client.get('/api/reports/metrics')
        self.assertEqual(response.status_code, 200)
        data = response.get_json()
        self.assertEqual(data.get('status'), 'success')
        print("[PASS] Reports metrics passed:", data.get('data'))

    def test_09_poster_requests_metrics(self):
        response = self.client.get('/api/poster-requests/metrics')
        self.assertEqual(response.status_code, 200)
        data = response.get_json()
        self.assertEqual(data.get('status'), 'success')
        print("[PASS] Poster requests metrics passed:", data.get('data'))

    def test_10_dashboard_overview(self):
        response = self.client.get('/api/dashboard/overview')
        self.assertEqual(response.status_code, 200)
        data = response.get_json()
        self.assertEqual(data.get('status'), 'success')
        self.assertIn('metrics', data.get('data', {}))
        self.assertIn('chapters', data.get('data', {}))
        print("[PASS] Dashboard overview passed: chapters count =", len(data['data']['chapters']))

    def test_11_poster_requests_tabs(self):
        resp_q = self.client.get('/api/poster-requests?tab=queue')
        self.assertEqual(resp_q.status_code, 200)
        resp_a = self.client.get('/api/poster-requests?tab=accepted')
        self.assertEqual(resp_a.status_code, 200)
        print("[PASS] Poster requests queue & accepted tabs passed.")

    def test_12_notifications_summary(self):
        response = self.client.get('/api/notifications/summary')
        self.assertEqual(response.status_code, 200)
        data = response.get_json()
        self.assertEqual(data.get('status'), 'success')
        self.assertIn('total_alerts', data.get('data', {}))
        print("[PASS] Notifications summary passed: Total alerts =", data['data']['total_alerts'])

if __name__ == '__main__':
    unittest.main()
