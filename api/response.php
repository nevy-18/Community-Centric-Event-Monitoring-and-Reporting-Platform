<?php
/**
 * API Response & Utility Helper
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

/**
 * Return JSON Success Response
 */
function jsonResponse($data = [], $message = 'Success', $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode([
        'status'  => 'success',
        'message' => $message,
        'data'    => $data
    ]);
    exit;
}

/**
 * Return JSON Error Response
 */
function jsonError($message = 'An error occurred', $statusCode = 400, $errors = []) {
    http_response_code($statusCode);
    echo json_encode([
        'status'  => 'error',
        'message' => $message,
        'errors'  => $errors
    ]);
    exit;
}

/**
 * Get JSON Body Input
 */
function getJsonInput() {
    $raw = file_get_contents('php://input');
    if (empty($raw)) {
        return $_POST;
    }
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? array_merge($_POST, $decoded) : $_POST;
}

/**
 * Enforce Authenticated Session
 */
function requireAuth($allowedRoles = []) {
    if (empty($_SESSION['user_id'])) {
        jsonError('Unauthorized. Please log in.', 401);
    }
    if (!empty($allowedRoles) && !in_array($_SESSION['user_role'], $allowedRoles)) {
        jsonError('Forbidden. Insufficient permissions.', 403);
    }
    return $_SESSION;
}
