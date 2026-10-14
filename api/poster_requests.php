<?php
/**
 * Poster Requests API
 * Handles poster approval workflow, progress updates, and request listings.
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/response.php';

$action = $_GET['action'] ?? $_POST['action'] ?? 'list';
$input = getJsonInput();
$db = getDB();

switch ($action) {
    case 'metrics':
        $activeEventsStmt = $db->query("SELECT COUNT(*) FROM events WHERE status IN ('approved', 'published', 'ongoing')");
        $activeEvents = (int)$activeEventsStmt->fetchColumn();

        $pendingPostersStmt = $db->query("SELECT COUNT(*) FROM poster_requests WHERE status IN ('submitted', 'under_review')");
        $pendingPosters = (int)$pendingPostersStmt->fetchColumn();

        $awaitingReportStmt = $db->query("SELECT COUNT(*) FROM poster_requests WHERE status = 'in_progress'");
        $awaitingReport = (int)$awaitingReportStmt->fetchColumn();

        $concludedStmt = $db->query("SELECT COUNT(*) FROM poster_requests WHERE status = 'completed'");
        $concluded = (int)$concludedStmt->fetchColumn();

        jsonResponse([
            'total_active_events'    => $activeEvents,
            'pending_poster_request' => $pendingPosters,
            'awaiting_report'        => $awaitingReport,
            'concluded_events'       => $concluded
        ]);
        break;

    case 'list':
        $tab = $_GET['tab'] ?? 'accepted'; // 'accepted' or 'queue'

        $sql = "
            SELECT pr.*, c.chapter_name, c.chapter_code,
                   u.full_name AS requester_name,
                   a.full_name AS assignee_name
            FROM poster_requests pr
            LEFT JOIN chapters c ON c.id = pr.chapter_id
            LEFT JOIN users u ON u.id = pr.requested_by
            LEFT JOIN users a ON a.id = pr.assigned_to
            WHERE 1=1
        ";
        $params = [];

        if ($tab === 'accepted') {
            $sql .= " AND pr.status IN ('in_progress', 'completed')";
        } elseif ($tab === 'queue') {
            $sql .= " AND pr.status IN ('submitted', 'under_review', 'revision_requested')";
        }

        $sql .= " ORDER BY pr.submitted_at DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $requests = $stmt->fetchAll();

        jsonResponse($requests);
        break;

    default:
        jsonError("Invalid action. Supported actions: 'metrics', 'list'.", 400);
}
