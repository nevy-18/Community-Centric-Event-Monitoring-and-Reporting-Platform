"""
Comprehensive Test Suite for YTR Python Backend Endpoints
Usage: python backend/test_api.py
"""
import sys
import io
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
            'requested_deadline': '2026-10-18',
            'caption': 'Official publicity caption and announcement blurb.',
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
            'event_time': '02:00 PM',
            'venue': 'Community Center',
            'theme_or_concept': 'Youth Unity',
            'target_participants': '100 Participants',
            'requested_deadline': '2026-10-28',
            'caption': 'Join our youth unity rally!',
            'requested_by': 2
        }
        res_create = self.client.post('/api/poster-requests', json=create_payload)
        self.assertIn(res_create.status_code, [200, 201])
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

    def test_26_system_summary(self):
        res = self.client.get('/api/system/summary')
        self.assertEqual(res.status_code, 200)
        data = res.get_json()
        self.assertEqual(data.get('status'), 'success')
        self.assertIn('table_counts', data.get('data', {}))
        self.assertIn('activity_records_total', data.get('data', {}))
        print(f"[PASS] System summary API passed: {data['data']['activity_records_total']} activity records.")

    def test_27_system_flush_requires_confirmation(self):
        # Missing or wrong confirmation code should fail with HTTP 400
        res = self.client.post('/api/system/flush', json={'mode': 'activity', 'confirmation': 'WRONG'})
        self.assertEqual(res.status_code, 400)
        data = res.get_json()
        self.assertEqual(data.get('status'), 'error')
        self.assertIn("confirmation code required", data.get('message', '').lower())
        print("[PASS] System flush security guard passed: Rejects unconfirmed wipe requests.")

    def test_28_system_flush_and_demo_seed(self):
        # Trigger verified demo flush & re-seed
        res = self.client.post('/api/system/flush', json={'mode': 'demo', 'confirmation': 'FLUSH'})
        self.assertEqual(res.status_code, 200)
        data = res.get_json()
        self.assertEqual(data.get('status'), 'success')
        self.assertIn('deleted_summary', data.get('data', {}))

        # Verify summary counts reflect clean baseline
        res_summary = self.client.get('/api/system/summary')
        data_s = res_summary.get_json().get('data', {})
        self.assertEqual(data_s['table_counts']['events'], 7)
        self.assertEqual(data_s['table_counts']['chapters'], 6)
        self.assertEqual(data_s['table_counts']['users'], 7)
        print("[PASS] System flush & baseline demo seed passed: Clean baseline restored.")

    def test_29_register_with_roles_and_positions(self):
        # 1. National role registration
        nat_email = 'test.nyo@ytr.org'
        res_nat = self.client.post('/api/auth/register', json={
            'full_name': 'Test Youth Officer',
            'email': nat_email,
            'password': 'Password123!',
            'role': 'National Youth Officer'
        })
        self.assertEqual(res_nat.status_code, 201)
        data_nat = res_nat.get_json()
        self.assertEqual(data_nat['data']['role'], 'national_admin')
        self.assertEqual(data_nat['data']['position'], 'National Youth Officer')

        # 2. Chapter role registration without chapter should fail
        res_fail = self.client.post('/api/auth/register', json={
            'full_name': 'Test Chapter Pres',
            'email': 'test.cp@ytr.org',
            'password': 'Password123!',
            'role': 'Chapter President'
        })
        self.assertEqual(res_fail.status_code, 400)

        # 3. Chapter role registration with chapter
        ch_email = 'test.cp@ytr.org'
        res_ch = self.client.post('/api/auth/register', json={
            'full_name': 'Test Chapter Pres',
            'email': ch_email,
            'password': 'Password123!',
            'role': 'Chapter President',
            'chapter_id': 1
        })
        self.assertEqual(res_ch.status_code, 201)
        data_ch = res_ch.get_json()
        self.assertEqual(data_ch['data']['role'], 'chapter_admin')
        self.assertEqual(data_ch['data']['position'], 'Chapter President')
        print("[PASS] Registration with National and Chapter positions passed.")

    def test_30_executive_authorization_backup_and_flush(self):
        # 1. Executive Director authorized for backup
        res_exec = self.client.get('/api/system/backup?format=json', headers={'X-User-Email': 'admin@ytr.org'})
        self.assertEqual(res_exec.status_code, 200)
        data_exec = res_exec.get_json()
        self.assertEqual(data_exec['status'], 'success')
        self.assertIn('data', data_exec)

        # 2. Regional Director (National) authorized for backup
        res_rd_nat = self.client.get('/api/system/backup?format=json', headers={'X-User-Email': 'adminhost@gmail.com'})
        self.assertEqual(res_rd_nat.status_code, 200)

        # 3. Non-executive National Youth Officer blocked from backup (HTTP 403)
        res_nyo = self.client.get('/api/system/backup?format=json', headers={'X-User-Email': 'test.nyo@ytr.org'})
        self.assertEqual(res_nyo.status_code, 403)
        self.assertIn('denied', res_nyo.get_json().get('message', '').lower())

        # 4. Chapter Coordinator blocked from backup (HTTP 403)
        res_chap = self.client.get('/api/system/backup?format=json', headers={'X-User-Email': 'joan.dela@gmail.com'})
        self.assertEqual(res_chap.status_code, 403)

        # 5. Non-executive blocked from flush (HTTP 403)
        res_flush_chap = self.client.post('/api/system/flush', json={'mode': 'activity', 'confirmation': 'FLUSH'}, headers={'X-User-Email': 'joan.dela@gmail.com'})
        self.assertEqual(res_flush_chap.status_code, 403)

        # 6. Executive allowed to flush (HTTP 200)
        res_flush_exec = self.client.post('/api/system/flush', json={'mode': 'demo', 'confirmation': 'FLUSH'}, headers={'X-User-Email': 'admin@ytr.org'})
        self.assertEqual(res_flush_exec.status_code, 200)
        print("[PASS] Executive authorization for backup and reset/flush passed.")

    def test_31_poster_request_empty_field_validations(self):
        # 1. Empty body returns 400
        res_empty = self.client.post('/api/poster-requests', json={})
        self.assertEqual(res_empty.status_code, 400)
        self.assertIn('missing or empty required fields', res_empty.get_json().get('message', '').lower())

        # 2. Whitespace-only title returns 400
        res_ws_title = self.client.post('/api/poster-requests', json={
            'chapter_id': 1,
            'event_title': '    ',
            'event_date': '2026-11-10',
            'event_time': '10:00 AM',
            'venue': 'Gym',
            'theme_or_concept': 'Leadership',
            'target_participants': '100',
            'requested_deadline': '2026-11-05',
            'caption': 'Sample caption'
        })
        self.assertEqual(res_ws_title.status_code, 400)
        self.assertIn('event_title', res_ws_title.get_json().get('message', ''))

        # 3. Missing caption returns 400
        res_no_cap = self.client.post('/api/poster-requests', json={
            'chapter_id': 1,
            'event_title': 'Sample Event',
            'event_date': '2026-11-10',
            'event_time': '10:00 AM',
            'venue': 'Gym',
            'theme_or_concept': 'Leadership',
            'target_participants': '100',
            'requested_deadline': '2026-11-05'
        })
        self.assertEqual(res_no_cap.status_code, 400)
        self.assertIn('caption', res_no_cap.get_json().get('message', ''))

        # 4. Deadline after event date returns 400
        res_bad_dl = self.client.post('/api/poster-requests', json={
            'chapter_id': 1,
            'event_title': 'Sample Event',
            'event_date': '2026-11-10',
            'event_time': '10:00 AM',
            'venue': 'Gym',
            'theme_or_concept': 'Leadership',
            'target_participants': '100',
            'requested_deadline': '2026-11-15',
            'caption': 'Sample caption'
        })
        self.assertEqual(res_bad_dl.status_code, 400)
        self.assertIn('deadline cannot be after', res_bad_dl.get_json().get('message', '').lower())

        # 5. Create a valid request to test post-creation endpoints
        res_valid = self.client.post('/api/poster-requests', json={
            'chapter_id': 1,
            'event_title': 'Strict Validation Demo Event',
            'event_date': '2026-11-10',
            'event_time': '10:00 AM',
            'venue': 'Main Auditorium',
            'theme_or_concept': 'Excellence and Integrity',
            'target_participants': '120 Leaders',
            'requested_deadline': '2026-11-05',
            'caption': 'Join us for this impactful gathering.'
        })
        self.assertIn(res_valid.status_code, [200, 201])
        req_id = res_valid.get_json()['data']['request_id']

        # 6. Approving without an official poster graphic returns 400
        res_no_poster_approve = self.client.post(f'/api/poster-requests/{req_id}/verify-approve', json={
            'checklist': ['date_time', 'venue']
        })
        self.assertEqual(res_no_poster_approve.status_code, 400)
        self.assertIn('official poster', res_no_poster_approve.get_json().get('message', '').lower())

        # 7. Submitting documentation with 0 attendees returns 400
        res_zero_att = self.client.post(f'/api/poster-requests/{req_id}/submit-documentation', json={
            'attendees_count': 0,
            'report_summary': 'Conducted successfully.'
        })
        self.assertEqual(res_zero_att.status_code, 400)
        self.assertIn('attendance figures', res_zero_att.get_json().get('message', '').lower())

        # 8. Submitting documentation with empty summary returns 400
        res_empty_summary = self.client.post(f'/api/poster-requests/{req_id}/submit-documentation', json={
            'attendees_count': 100,
            'report_summary': '   '
        })
        self.assertEqual(res_empty_summary.status_code, 400)
        self.assertIn('event summary', res_empty_summary.get_json().get('message', '').lower())

        print("[PASS] Comprehensive empty field submission and validation tests passed.")

    def test_32_target_audience_support(self):
        # 1. Propose event with specific target_audience (e.g. BSIT Students)
        prop_payload = {
            'title': 'BSIT Hackathon and Youth Leadership Forum',
            'chapter_id': 1,
            'start_date': '2026-11-20',
            'end_date': '2026-11-21',
            'venue': 'CCS IT Laboratory, Main Campus',
            'description': 'Targeted technology, ethics, and leadership summit for BSIT students.',
            'event_type': 'leadership_training',
            'ytr_program': 'Rise up (Youth Excellence)',
            'target_audience': 'BSIT Students',
            'target_turnout': 150,
            'budget_amount': 18000,
            'due_date': '2026-11-25'
        }
        res_prop = self.client.post('/api/proposals', json=prop_payload)
        self.assertIn(res_prop.status_code, [200, 201])
        prop_id = res_prop.get_json()['data']['event_id']

        # Verify get proposal returns target_audience
        res_get_prop = self.client.get(f'/api/proposals/{prop_id}')
        self.assertEqual(res_get_prop.status_code, 200)
        prop_data = res_get_prop.get_json().get('data', {})
        self.assertEqual(prop_data.get('target_audience'), 'BSIT Students')
        self.assertEqual(prop_data.get('ytr_program'), 'Rise up (Youth Excellence)')

        # 2. Test proposal default target_audience
        prop_default = {
            'title': 'General Youth Fellowship',
            'chapter_id': 1,
            'start_date': '2026-11-28',
            'venue': 'Community Center',
            'description': 'Fellowship open to all chapter youths.',
            'event_type': 'fellowship'
        }
        res_def = self.client.post('/api/proposals', json=prop_default)
        self.assertIn(res_def.status_code, [200, 201])
        def_id = res_def.get_json()['data']['event_id']
        res_get_def = self.client.get(f'/api/proposals/{def_id}')
        self.assertEqual(res_get_def.get_json()['data']['target_audience'], 'Open for All')

        # 3. Create poster request with target_audience
        poster_payload = {
            'chapter_id': 1,
            'event_title': 'BSIT Cybersecurity & Leadership Summit',
            'event_date': '2026-12-05',
            'event_time': '01:00 PM',
            'venue': 'Tech Auditorium',
            'theme_or_concept': 'Defending the Faith, Securing the Future',
            'target_participants': '200 BSIT Delegates',
            'target_audience': 'BSIT Students',
            'requested_deadline': '2026-11-30',
            'caption': 'Calling all BSIT Students! Register now for our annual tech summit.'
        }
        res_poster = self.client.post('/api/poster-requests', json=poster_payload)
        self.assertIn(res_poster.status_code, [200, 201])
        poster_id = res_poster.get_json()['data']['request_id']

        # Verify timeline reflects target_audience
        res_tl = self.client.get(f'/api/poster-requests/{poster_id}/timeline')
        self.assertEqual(res_tl.status_code, 200)
        tl_req = res_tl.get_json()['data']['request']
        self.assertEqual(tl_req.get('target_audience'), 'BSIT Students')

        print("[PASS] Target Audience / Demographics support verified for Proposals and Poster Requests.")

    def test_33_report_attendance_sheet_and_signature_upload(self):
        # 1. Create an event for report filing
        event_payload = {
            'title': 'Youth Leadership Bootcamp & Summit',
            'chapter_id': 1,
            'start_date': '2026-11-20 09:00:00',
            'end_date': '2026-11-20 17:00:00',
            'venue': 'Grand Ballroom',
            'description': 'Annual youth leadership empowerment seminar.',
            'event_type': 'leadership_training',
            'ytr_program': 'Lead up (Youth Leadership)',
            'target_audience': 'Youth Leaders & Student Councils',
            'target_turnout': 120,
            'budget_amount': 22000
        }
        res_ev = self.client.post('/api/proposals', json=event_payload)
        self.assertIn(res_ev.status_code, [200, 201])
        event_id = res_ev.get_json()['data']['event_id']

        # 2. Prepare sample files for attendance sheet and signature image
        sample_attendance_content = b"Attendee Name,Email,Role\nJohn Doe,john@test.com,First-Timer\nJane Smith,jane@test.com,Leader\n"
        sample_sig_content = b"\x89PNG\r\n\x1a\n\x00\x00\x00\rIHDR\x00\x00\x00\x01\x00\x00\x00\x01\x08\x06\x00\x00\x00\x1f\x15c4\x00\x00\x00\nIDATx\x9cc\x00\x01\x00\x00\x05\x00\x01\r\n-\xb4\x00\x00\x00\x00IEND\xaeB`\x82"

        # 3. Submit report using multipart/form-data with file uploads
        form_data = {
            'event_id': event_id,
            'chapter_id': 1,
            'total_participants': '135',
            'first_timers': '45',
            'existing_members': '80',
            'youth_leaders_count': '10',
            'budget_spent': '21500',
            'report_summary': 'Event successfully conducted with great student turnout.',
            'challenges': 'Minor audio setup delay.',
            'recommendations': 'Prepare auxiliary audio system in advance.',
            'signed_by_name': 'Joan Dela Cruz (Chapter Admin)',
            'attendee_roster': json.dumps([
                {'name': 'John Doe', 'contact': 'john@test.com', 'role': 'First-Timer'},
                {'name': 'Jane Smith', 'contact': 'jane@test.com', 'role': 'Youth Leader'}
            ]),
            'attendance_sheet': (io.BytesIO(sample_attendance_content), 'official_attendance_roster.csv', 'text/csv'),
            'signature_file': (io.BytesIO(sample_sig_content), 'joan_signature.png', 'image/png')
        }

        res_rep = self.client.post('/api/reports', data=form_data, content_type='multipart/form-data')
        self.assertEqual(res_rep.status_code, 201, msg=f"Submit failed: {res_rep.get_data(as_text=True)}")
        rep_id = res_rep.get_json()['data']['report_id']

        # 4. Fetch the report by ID and verify fields
        res_get = self.client.get(f'/api/reports/{rep_id}')
        self.assertEqual(res_get.status_code, 200)
        rep_data = res_get.get_json()['data']
        self.assertIsNotNone(rep_data.get('attendance_sheet_path'))
        self.assertTrue(rep_data['attendance_sheet_path'].startswith('assets/uploads/attendance/'))
        self.assertIsNotNone(rep_data.get('e_signature'))
        self.assertTrue(rep_data['e_signature'].startswith('assets/uploads/signatures/'))
        self.assertEqual(rep_data.get('total_participants'), 135)
        self.assertEqual(rep_data.get('first_timers'), 45)

        # 5. Test downloading the attached attendance sheet
        res_dl = self.client.get(f'/api/reports/{rep_id}/attendance-sheet')
        self.assertEqual(res_dl.status_code, 200)
        self.assertEqual(res_dl.data, sample_attendance_content)

        # 6. Verify report list query includes attendance_sheet_path
        res_list = self.client.get('/api/reports')
        self.assertEqual(res_list.status_code, 200)
        reports = res_list.get_json()['data']
        matching = [r for r in reports if r.get('report_id') == rep_id or r.get('id') == rep_id]
        self.assertTrue(len(matching) >= 1)
        self.assertIsNotNone(matching[0].get('attendance_sheet_path'))

        # 7. Test canvas pad signature fallback (data URL signature)
        canvas_sig_dataurl = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=="
        res_canvas_rep = self.client.post('/api/reports', json={
            'event_id': event_id,
            'chapter_id': 1,
            'total_participants': 140,
            'first_timers': 50,
            'existing_members': 80,
            'youth_leaders_count': 10,
            'budget_spent': 21500,
            'report_summary': 'Updated with canvas pad signature.',
            'signed_by_name': 'Joan Dela Cruz',
            'e_signature': canvas_sig_dataurl
        })
        self.assertEqual(res_canvas_rep.status_code, 201)
        res_get_canvas = self.client.get(f'/api/reports/{rep_id}')
        self.assertEqual(res_get_canvas.status_code, 200)
        # Should keep existing attendance_sheet_path while updating signature to data URL
        self.assertEqual(res_get_canvas.get_json()['data']['e_signature'], canvas_sig_dataurl)
        self.assertIsNotNone(res_get_canvas.get_json()['data']['attendance_sheet_path'])

        print("[PASS] Report attendance sheet and signature upload & download tests passed.")

if __name__ == '__main__':
    unittest.main()


