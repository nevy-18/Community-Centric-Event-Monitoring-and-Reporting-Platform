<?php
/**
 * Chapter Event Proposals API
 * Endpoints for submitting, evaluating, listing, and filtering chapter event proposals.
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/response.php';

$action = $_GET['action'] ?? $_POST['action'] ?? 'list';
$input = getJsonInput();
$db = getDB();

switch ($action) {
    case 'metrics':
        $totalStmt = $db->query("SELECT COUNT(*) FROM events");
        $total = (int)$totalStmt->fetchColumn();

        $pendingStmt = $db->query("SELECT COUNT(*) FROM events WHERE status = 'pending_approval'");
        $pending = (int)$pendingStmt->fetchColumn();

        $approvedStmt = $db->query("SELECT COUNT(*) FROM events WHERE status IN ('approved', 'published', 'ongoing', 'completed')");
        $approved = (int)$approvedStmt->fetchColumn();

        $revisionStmt = $db->query("SELECT COUNT(*) FROM events WHERE status = 'draft'");
        $revision = (int)$revisionStmt->fetchColumn();

        jsonResponse([
            'total_proposals' => $total,
            'pending_review'  => $pending,
            'approved'        => $approved,
            'needs_revision'  => $revision
        ]);
        break;

    case 'list':
        $statusFilter = $_GET['status'] ?? 'all';
        $chapterFilter = $_GET['chapter_id'] ?? 'all';
        $search = trim($_GET['search'] ?? '');

        $sql = "
            SELECT e.id, e.chapter_id, e.title, e.description, e.event_type,
                   e.start_datetime, e.end_datetime, e.venue, e.status,
                   e.ytr_program, e.target_audience,
                   e.target_turnout, e.budget_amount, e.budget_threshold, e.due_date,
                   e.attachment_path, e.drive_link,
                   e.created_at, e.review_note,
                   c.chapter_name, c.chapter_code,
                   u.full_name AS requester_name
            FROM events e
            LEFT JOIN chapters c ON c.id = e.chapter_id
            LEFT JOIN users u ON u.id = e.created_by
            WHERE 1=1
        ";
        $params = [];

        if ($statusFilter === 'pending') {
            $sql .= " AND e.status = 'pending_approval'";
        } elseif ($statusFilter === 'approved') {
            $sql .= " AND e.status IN ('approved', 'published', 'ongoing', 'completed')";
        } elseif ($statusFilter === 'revision') {
            $sql .= " AND e.status = 'draft'";
        }

        if ($chapterFilter !== 'all' && !empty($chapterFilter)) {
            $sql .= " AND e.chapter_id = :chapter_id";
            $params['chapter_id'] = (int)$chapterFilter;
        }

        if (!empty($search)) {
            $sql .= " AND (e.title LIKE :search OR e.venue LIKE :search OR c.chapter_name LIKE :search)";
            $params['search'] = "%{$search}%";
        }

        $sql .= " ORDER BY e.created_at DESC, e.id DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $proposals = $stmt->fetchAll();

        jsonResponse($proposals);
        break;

    case 'get':
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) {
            jsonError('Event ID is required.', 400);
        }

        $stmt = $db->prepare("
            SELECT e.*, c.chapter_name, c.chapter_code,
                   u.full_name AS requester_name,
                   r.full_name AS reviewer_name
            FROM events e
            LEFT JOIN chapters c ON c.id = e.chapter_id
            LEFT JOIN users u ON u.id = e.created_by
            LEFT JOIN users r ON r.id = e.reviewed_by
            WHERE e.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $proposal = $stmt->fetch();

        if (!$proposal) {
            jsonError('Proposal not found.', 404);
        }

        jsonResponse($proposal);
        break;

    case 'create':
        $title = trim($input['title'] ?? '');
        $chapterId = !empty($input['chapter_id']) ? (int)$input['chapter_id'] : ($_SESSION['chapter_id'] ?? null);
        $startDate = $input['start_date'] ?? null;
        $endDate = $input['end_date'] ?? null;
        $venue = trim($input['venue'] ?? '');
        $description = trim($input['description'] ?? '');
        $eventType = in_array($input['event_type'] ?? '', ['national', 'chapter', 'community', 'other']) ? $input['event_type'] : 'chapter';
        $createdBy = $_SESSION['user_id'] ?? null;

        // If no session user, fallback to the first active user
        if (!$createdBy) {
            $defaultUser = $db->query("SELECT id, chapter_id FROM users WHERE account_status = 'active' LIMIT 1")->fetch();
            if ($defaultUser) {
                $createdBy = $defaultUser['id'];
                if (!$chapterId) {
                    $chapterId = $defaultUser['chapter_id'];
                }
            }
        }

        if (empty($title) || empty($startDate) || empty($venue) || empty($description)) {
            jsonError('Title, start date, venue, and description are required.', 400);
        }

        if (!$chapterId) {
            // Pick default chapter if still null
            $chapterId = $db->query("SELECT id FROM chapters LIMIT 1")->fetchColumn();
        }

        $insertStmt = $db->prepare("
            INSERT INTO events (chapter_id, title, description, event_type, start_datetime, end_datetime, venue, status, created_by, created_at)
            VALUES (:chapter_id, :title, :description, :event_type, :start_datetime, :end_datetime, :venue, 'pending_approval', :created_by, NOW())
        ");
        $insertStmt->execute([
            'chapter_id'     => $chapterId,
            'title'          => $title,
            'description'    => $description,
            'event_type'     => $eventType,
            'start_datetime' => $startDate . ' 09:00:00',
            'end_datetime'   => ($endDate ?: $startDate) . ' 17:00:00',
            'venue'          => $venue,
            'created_by'     => $createdBy
        ]);

        $newEventId = $db->lastInsertId();

        jsonResponse([
            'event_id' => $newEventId
        ], 'Event proposal submitted successfully and added to review queue!', 201);
        break;

    case 'review':
        $id = (int)($input['id'] ?? 0);
        $status = $input['status'] ?? '';
        $note = trim($input['review_note'] ?? '');
        $reviewerId = $_SESSION['user_id'] ?? null;

        if (!$id || !in_array($status, ['approved', 'draft', 'rejected'])) {
            jsonError('Valid event ID and status (approved, draft, rejected) are required.', 400);
        }

        $updateStmt = $db->prepare("
            UPDATE events
            SET status = :status,
                review_note = :note,
                reviewed_by = :reviewed_by,
                updated_at = NOW()
            WHERE id = :id
        ");
        $updateStmt->execute([
            'status'      => $status,
            'note'        => $note,
            'reviewed_by' => $reviewerId,
            'id'          => $id
        ]);

        jsonResponse([], "Event proposal status updated to {$status}.");
        break;

    default:
        jsonError("Invalid action. Supported actions: 'metrics', 'list', 'get', 'create', 'review'.", 400);
}
