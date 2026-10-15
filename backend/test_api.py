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
        self.assertEqual(data['data']['redirect'], 'Dashboard.php')
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

    def test_13_chapter_workspace(self):
        response = self.client.get('/api/chapters/1/workspace')
        self.assertEqual(response.status_code, 200)
        data = response.get_json()
        self.assertEqual(data.get('status'), 'success')
        self.assertIn('chapter', data.get('data', {}))
        self.assertIn('metrics', data.get('data', {}))
        self.assertIn('events', data.get('data', {}))
        print("[PASS] Chapter workspace API passed for chapter:", data['data']['chapter']['chapter_name'])

    def test_14_chapter_unreported_events(self):
        response = self.client.get('/api/chapters/1/unreported-events')
        self.assertEqual(response.status_code, 200)
        data = response.get_json()
        self.assertEqual(data.get('status'), 'success')
        print("[PASS] Chapter unreported events passed: count =", len(data.get('data', [])))

    def test_15_national_admin_blocked_from_submitting_report(self):
        payload = {
            'event_id': 1,
            'submitted_by': 1,  # National Admin ID
            'total_participants': 100
        }
        response = self.client.post('/api/reports', json=payload)
        self.assertEqual(response.status_code, 403)
        data = response.get_json()
        self.assertEqual(data.get('status'), 'error')
        self.assertIn('Only Chapter-based', data.get('message', ''))
        print("[PASS] Security policy: National admin correctly blocked from submitting reports (HTTP 403).")

    def test_16_chapter_admin_can_submit_report(self):
        payload = {
            'event_id': 1,
            'submitted_by': 2,  # Chapter Admin ID for Laguna (Chapter 1)
            'total_participants': 135,
            'first_timers': 30,
            'existing_members': 90,
            'youth_leaders_count': 15,
            'budget_spent': 19200.0,
            'report_summary': 'Updated test report from Chapter Admin.',
            'signed_by_name': 'Joan Dela Cruz'
        }
        response = self.client.post('/api/reports', json=payload)
        self.assertIn(response.status_code, [200, 201])
        data = response.get_json()
        self.assertEqual(data.get('status'), 'success')
        print("[PASS] Authorized Chapter Admin successfully submitted report (HTTP 200/201).")

    def test_17_proposal_review_accept_and_reject(self):
        # 1. Review proposal as approved
        res_approve = self.client.post('/api/proposals/1/review', json={'status': 'approved', 'review_note': 'Officially cleared by National Secretariat.'})
        self.assertEqual(res_approve.status_code, 200)
        data_app = res_approve.get_json()
        self.assertEqual(data_app.get('status'), 'success')

        # 2. Verify single proposal endpoint reflects status
        res_get = self.client.get('/api/proposals/1')
        self.assertEqual(res_get.status_code, 200)
        p = res_get.get_json().get('data', {})
        self.assertEqual(p.get('status'), 'approved')
        print("[PASS] National admin proposal evaluation & status update passed.")

    def test_18_poster_timeline_retrieval(self):
        res = self.client.get('/api/poster-requests/2/timeline')
        self.assertEqual(res.status_code, 200)
        data = res.get_json().get('data', {})
        self.assertIn('request', data)
        self.assertIn('steps', data)
        self.assertIn('updates', data)
        self.assertEqual(len(data['steps']), 5)
        print("[PASS] Poster request timeline retrieval passed with 5-step pipeline.")

    workflow_req_id = None

    def test_19_poster_creation_and_verify_approve_workflow(self):
        # 1. Create a fresh poster request
        create_payload = {
            'chapter_id': 1,
            'event_title': 'Youth Leadership Summit 2026',
            'event_date': '2026-10-20',
            'event_time': '09:00 AM',
            'venue': 'Provincial Capitol Gym',
            'theme_or_concept': 'Igniting Tomorrow Leaders',
            'target_participants': '250 Youth Leaders',
            'requested_deadline': '2026-10-25',
            'requested_by': 2
        }
        res_create = self.client.post('/api/poster-requests', json=create_payload)
        self.assertIn(res_create.status_code, [200, 201])
        req_id = res_create.get_json()['data']['request_id']
        TestYTRBackendAPI.workflow_req_id = req_id

        # 2. National Admin verifies checklist, approves, and provides the necessary poster
        payload = {
            'checklist': ['date_time', 'venue', 'participants', 'chapter_name', 'visuals', 'caption_link'],
            'review_note': 'Pre-approval checklist verified and official poster released by National Creative.',
            'final_poster_path': 'assets/uploads/posters/official_summit_poster.png'
        }
        res_approve = self.client.post(f'/api/poster-requests/{req_id}/verify-approve', json=payload)
        self.assertEqual(res_approve.status_code, 200)

        # 3. Verify timeline reflects in_progress, step 1 completed, and official poster attached
        res_tl = self.client.get(f'/api/poster-requests/{req_id}/timeline')
        data = res_tl.get_json().get('data', {})
        self.assertEqual(data['request']['status'], 'in_progress')
        self.assertEqual(data['request']['final_poster_path'], 'assets/uploads/posters/official_summit_poster.png')
        steps = {s['step_order']: s['status'] for s in data['steps']}
        self.assertEqual(steps[1], 'completed')
        self.assertEqual(steps[2], 'in_progress')
        print("[PASS] Fresh poster request creation, approval, and official poster release workflow passed.")

    def test_20_poster_timeline_step_update_and_proof(self):
        req_id = TestYTRBackendAPI.workflow_req_id
        payload = {
            'step_order': 2,
            'step_status': 'completed',
            'notes': 'Official Facebook post published with 500+ impressions: https://facebook.com/ytr/posts/12345',
            'proof_file_path': 'assets/uploads/proofs/sample_fb_proof.png'
        }
        res = self.client.post(f'/api/poster-requests/{req_id}/timeline-step', json=payload)
        self.assertEqual(res.status_code, 200)

        # Verify update recorded & step 2 completed
        res_tl = self.client.get(f'/api/poster-requests/{req_id}/timeline')
        data = res_tl.get_json().get('data', {})
        steps = {s['step_order']: s['status'] for s in data['steps']}
        self.assertEqual(steps[2], 'completed')
        self.assertEqual(steps[3], 'in_progress')
        print("[PASS] Poster milestone step 2 completed and proof ledger passed.")

    def test_21_skip_promo_video(self):
        req_id = TestYTRBackendAPI.workflow_req_id
        # Skip promo video (Stage 3 is optional)
        res = self.client.post(f'/api/poster-requests/{req_id}/skip-promo-video', json={'updated_by': 1})
        self.assertEqual(res.status_code, 200)

        # Check timeline: step 3 should be completed, step 4 in_progress
        res_tl = self.client.get(f'/api/poster-requests/{req_id}/timeline')
        data = res_tl.get_json().get('data', {})
        steps = {s['step_order']: s['status'] for s in data['steps']}
        self.assertEqual(steps[3], 'completed')
        self.assertEqual(steps[4], 'in_progress')
        print("[PASS] Skip promo video endpoint passed: Step 3 marked completed, Step 4 in_progress.")

    def test_22_mark_event_conducted_and_timer(self):
        req_id = TestYTRBackendAPI.workflow_req_id
        # Mark event conducted on 2026-10-15
        res = self.client.post(f'/api/poster-requests/{req_id}/mark-conducted', json={'conducted_date': '2026-10-15', 'updated_by': 1})
        self.assertEqual(res.status_code, 200)
        data = res.get_json().get('data', {})
        self.assertEqual(data.get('report_due_date'), '2026-10-29')

        # Check timeline computes 2-week timer
        res_tl = self.client.get(f'/api/poster-requests/{req_id}/timeline')
        req = res_tl.get_json().get('data', {}).get('request', {})
        self.assertEqual(req.get('is_event_conducted'), 1)
        self.assertIsNotNone(req.get('report_timer'))
        self.assertEqual(req['report_timer']['due_date'], 'Oct 29, 2026')
        print("[PASS] 2-week reporting countdown timer computation passed.")

    def test_23_submit_documentation_attendees_and_proof(self):
        req_id = TestYTRBackendAPI.workflow_req_id
        # Chapter submits attendees, summary, and proof
        payload = {
            'attendees_count': 185,
            'first_timers_count': 42,
            'report_summary': 'Successfully hosted event with high youth participation and engagement.',
            'conducted_date': '2026-10-15',
            'proof_file_path': 'assets/uploads/proofs/event_photo_sample.jpg'
        }
        res = self.client.post(f'/api/poster-requests/{req_id}/submit-documentation', json=payload)
        self.assertEqual(res.status_code, 200)

        # Check request reflects submitted documentation
        res_tl = self.client.get(f'/api/poster-requests/{req_id}/timeline')
        req = res_tl.get_json().get('data', {}).get('request', {})
        self.assertEqual(req.get('documentation_status'), 'submitted')
        self.assertEqual(req.get('attendees_count'), 185)
        self.assertEqual(req.get('first_timers_count'), 42)
        print("[PASS] Chapter post-event documentation & attendance figures submission passed.")

    def test_24_approve_event_documentation(self):
        req_id = TestYTRBackendAPI.workflow_req_id
        # National Admin reviews and approves documentation
        res = self.client.post(f'/api/poster-requests/{req_id}/approve-documentation', json={
            'review_note': 'Attendance and post-event photos verified. Approved.',
            'reviewed_by': 1
        })
        self.assertEqual(res.status_code, 200)

        # Check request status is completed, steps 4 & 5 completed
        res_tl = self.client.get(f'/api/poster-requests/{req_id}/timeline')
        data = res_tl.get_json().get('data', {})
        req = data.get('request', {})
        self.assertEqual(req.get('status'), 'completed')
        self.assertEqual(req.get('documentation_status'), 'approved')
        steps = {s['step_order']: s['status'] for s in data['steps']}
        self.assertEqual(steps[1], 'completed')
        self.assertEqual(steps[2], 'completed')
        self.assertEqual(steps[3], 'completed')
        self.assertEqual(steps[4], 'completed')
        self.assertEqual(steps[5], 'completed')
        print("[PASS] National Admin documentation audit approval & event conclusion passed: all 5 steps completed.")

    def test_25_stage_validations(self):
        # Create a blank request to test validations
        create_payload = {
            'chapter_id': 1,
            'event_title': 'Validation Test Event',
            'event_date': '2026-11-01',
            'requested_by': 2
        }
        res_create = self.client.post('/api/poster-requests', json=create_payload)
        v_id = res_create.get_json()['data']['request_id']

        # Test 1: Stage 1 requires poster graphic
        res_stg1 = self.client.post(f'/api/poster-requests/{v_id}/timeline-step', json={
            'step_order': 1,
            'step_status': 'completed'
        })
        self.assertEqual(res_stg1.status_code, 400)
        self.assertIn('official poster', res_stg1.get_json().get('message', '').lower())

        # Test 2: Stage 2 requires proof file or link/notes
        res_stg2 = self.client.post(f'/api/poster-requests/{v_id}/timeline-step', json={
            'step_order': 2,
            'step_status': 'completed',
            'notes': ''
        })
        self.assertEqual(res_stg2.status_code, 400)
        self.assertIn('proof screenshot', res_stg2.get_json().get('message', '').lower())

        # Test 3: Stage 4/5 requires report submitted via Report tab and validated by National
        res_stg4 = self.client.post(f'/api/poster-requests/{v_id}/timeline-step', json={
            'step_order': 4,
            'step_status': 'completed'
        })
        self.assertEqual(res_stg4.status_code, 400)
        self.assertIn('report', res_stg4.get_json().get('message', '').lower())
        self.assertIn('report tab', res_stg4.get_json().get('message', '').lower())
        self.assertIn('validated', res_stg4.get_json().get('message', '').lower())

        print("[PASS] Strict stage deliverables enforcement validations passed.")

if __name__ == '__main__':
    unittest.main()

