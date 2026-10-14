<?php
/**
 * Manage Accounts API
 * Endpoints for National Admin to view, approve, decline, and reassign user accounts.
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/response.php';

$action = $_GET['action'] ?? $_POST['action'] ?? 'list';
$input = getJsonInput();
$db = getDB();

switch ($action) {
    case 'metrics':
        // Compute count metrics for Manage Accounts screen
        $totalStmt = $db->query("SELECT COUNT(*) FROM users");
        $totalAccounts = (int)$totalStmt->fetchColumn();

        $pendingStmt = $db->query("SELECT COUNT(*) FROM users WHERE account_status = 'pending'");
        $pendingRequests = (int)$pendingStmt->fetchColumn();

        $nationalStmt = $db->query("SELECT COUNT(*) FROM users WHERE role = 'national_admin' AND account_status = 'active'");
        $nationalAdmins = (int)$nationalStmt->fetchColumn();

        $chapterStmt = $db->query("SELECT COUNT(*) FROM users WHERE role = 'chapter_admin' AND account_status = 'active'");
        $chapterAdmins = (int)$chapterStmt->fetchColumn();

        jsonResponse([
            'total_accounts'   => $totalAccounts,
            'pending_requests' => $pendingRequests,
            'national_admins'  => $nationalAdmins,
            'chapter_admins'   => $chapterAdmins
        ]);
        break;

    case 'list':
        $filter = $_GET['filter'] ?? 'all';
        $search = trim($_GET['search'] ?? '');

        $sql = "
            SELECT u.id, u.chapter_id, u.full_name, u.email, u.role, u.account_status,
                   u.requested_at, u.reviewed_at, u.review_note,
                   c.chapter_name, c.chapter_code
            FROM users u
            LEFT JOIN chapters c ON c.id = u.chapter_id
            WHERE 1=1
        ";
        $params = [];

        if ($filter === 'pending') {
            $sql .= " AND u.account_status = 'pending'";
        } elseif ($filter === 'national_admins') {
            $sql .= " AND u.role = 'national_admin'";
        } elseif ($filter === 'chapter_admins') {
            $sql .= " AND u.role = 'chapter_admin'";
        } elseif ($filter === 'disabled') {
            $sql .= " AND u.account_status IN ('rejected', 'suspended')";
        }

        if (!empty($search)) {
            $sql .= " AND (u.full_name LIKE :search OR u.email LIKE :search OR c.chapter_name LIKE :search)";
            $params['search'] = "%{$search}%";
        }

        $sql .= " ORDER BY u.requested_at DESC, u.id DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $accounts = $stmt->fetchAll();

        jsonResponse($accounts);
        break;

    case 'get':
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) {
            jsonError('User ID is required.', 400);
        }

        $stmt = $db->prepare("
            SELECT u.id, u.chapter_id, u.full_name, u.email, u.role, u.account_status,
                   u.requested_at, u.reviewed_at, u.review_note,
                   c.chapter_name, c.chapter_code,
                   r.full_name AS reviewer_name
            FROM users u
            LEFT JOIN chapters c ON c.id = u.chapter_id
            LEFT JOIN users r ON r.id = u.reviewed_by
            WHERE u.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();

        if (!$user) {
            jsonError('User not found.', 404);
        }

        jsonResponse($user);
        break;

    case 'approve':
        $userId = (int)($input['user_id'] ?? 0);
        $role = $input['role'] ?? 'chapter_admin';
        $chapterId = !empty($input['chapter_id']) ? (int)$input['chapter_id'] : null;
        $reviewerId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            jsonError('User ID is required.', 400);
        }

        if (!in_array($role, ['national_admin', 'chapter_admin'])) {
            jsonError('Invalid role specified.', 400);
        }

        $updateStmt = $db->prepare("
            UPDATE users
            SET account_status = 'active',
                role = :role,
                chapter_id = :chapter_id,
                reviewed_by = :reviewed_by,
                reviewed_at = NOW()
            WHERE id = :id
        ");
        $updateStmt->execute([
            'role'        => $role,
            'chapter_id'  => ($role === 'chapter_admin') ? $chapterId : null,
            'reviewed_by' => $reviewerId,
            'id'          => $userId
        ]);

        jsonResponse([], 'Account approved successfully.');
        break;

    case 'decline':
        $userId = (int)($input['user_id'] ?? 0);
        $note = trim($input['review_note'] ?? 'Application declined by administrator.');
        $reviewerId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            jsonError('User ID is required.', 400);
        }

        $updateStmt = $db->prepare("
            UPDATE users
            SET account_status = 'rejected',
                review_note = :note,
                reviewed_by = :reviewed_by,
                reviewed_at = NOW()
            WHERE id = :id
        ");
        $updateStmt->execute([
            'note'        => $note,
            'reviewed_by' => $reviewerId,
            'id'          => $userId
        ]);

        jsonResponse([], 'Account request has been declined.');
        break;

    default:
        jsonError("Invalid action. Supported actions: 'metrics', 'list', 'get', 'approve', 'decline'.", 400);
}
