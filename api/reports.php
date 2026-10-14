<?php
/**
 * Chapter Reports API
 * Handles event report metrics, event listings, and report submissions.
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

        $completedEventsStmt = $db->query("SELECT COUNT(*) FROM events WHERE status = 'completed'");
        $completedEvents = (int)$completedEventsStmt->fetchColumn();

        $firstTimersStmt = $db->query("SELECT COALESCE(SUM(first_timers), 0) FROM event_reports WHERE status = 'approved'");
        $firstTimers = (int)$firstTimersStmt->fetchColumn();

        jsonResponse([
            'total_active_events' => $activeEvents,
            'completed_events'    => $completedEvents,
            'first_timers'        => $firstTimers
        ]);
        break;

    case 'list':
        $filter = $_GET['filter'] ?? 'all';
        $search = trim($_GET['search'] ?? '');

        $sql = "
            SELECT r.id AS report_id, r.total_participants, r.first_timers, r.existing_members,
                   r.report_summary, r.status AS report_status, r.submitted_at,
                   e.id AS event_id, e.title AS event_title, e.start_datetime, e.end_datetime,
                   e.venue, e.status AS event_status,
                   c.chapter_name, c.chapter_code,
                   u.full_name AS submitted_by_name
            FROM events e
            LEFT JOIN event_reports r ON r.event_id = e.id
            LEFT JOIN chapters c ON c.id = e.chapter_id
            LEFT JOIN users u ON u.id = r.submitted_by
            WHERE 1=1
        ";
        $params = [];

        if ($filter === 'completed') {
            $sql .= " AND r.status = 'approved'";
        } elseif ($filter === 'pending') {
            $sql .= " AND (r.status = 'submitted' OR (r.id IS NULL AND e.status = 'completed'))";
        }

        if (!empty($search)) {
            $sql .= " AND (e.title LIKE :search OR c.chapter_name LIKE :search)";
            $params['search'] = "%{$search}%";
        }

        $sql .= " ORDER BY e.start_datetime DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $reports = $stmt->fetchAll();

        jsonResponse($reports);
        break;

    default:
        jsonError("Invalid action. Supported actions: 'metrics', 'list'.", 400);
}
