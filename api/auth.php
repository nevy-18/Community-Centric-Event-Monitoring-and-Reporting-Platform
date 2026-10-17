<?php
/**
 * Authentication API Endpoint
 * Handles login, registration (account request), logout, and session check
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/response.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$input = getJsonInput();

$db = getDB();

switch ($action) {
    case 'login':
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';

        if (empty($email) || empty($password)) {
            jsonError('Email and password are required.', 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            jsonError('Please provide a valid email address.', 400);
        }

        $stmt = $db->prepare("
            SELECT u.id, u.chapter_id, u.full_name, u.email, u.password_hash, u.role, u.account_status,
                   c.chapter_name
            FROM users u
            LEFT JOIN chapters c ON c.id = u.chapter_id
            WHERE u.email = :email
            LIMIT 1
        ");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        $storedHash = $user['password_hash'] ?? '';
        $normalizedHash = str_replace('$2b$', '$2y$', $storedHash);
        $isValid = ($password === $storedHash) || password_verify($password, $storedHash) || password_verify($password, $normalizedHash);

        if (!$user || !$isValid) {
            jsonError('Invalid email or password.', 401);
        }

        if ($user['account_status'] === 'pending') {
            jsonError('Your account request is still pending administrator review.', 403);
        }

        if ($user['account_status'] === 'rejected') {
            jsonError('Your account access request was declined. Please contact your administrator.', 403);
        }

        if ($user['account_status'] === 'suspended') {
            jsonError('Your account has been suspended. Please contact National Admin.', 403);
        }

        // Set session variables
        $_SESSION['user_id']      = $user['id'];
        $_SESSION['user_name']    = $user['full_name'];
        $_SESSION['user_email']   = $user['email'];
        $_SESSION['user_role']    = $user['role'];
        $_SESSION['chapter_id']   = $user['chapter_id'];
        $_SESSION['chapter_name'] = $user['chapter_name'];

        // Update last login timestamp
        $updateStmt = $db->prepare("UPDATE users SET last_login_at = NOW() WHERE id = :id");
        $updateStmt->execute(['id' => $user['id']]);

        jsonResponse([
            'user' => [
                'id'           => $user['id'],
                'full_name'    => $user['full_name'],
                'email'        => $user['email'],
                'role'         => $user['role'],
                'chapter_id'   => $user['chapter_id'],
                'chapter_name' => $user['chapter_name']
            ],
            'redirect' => 'Dashboard.php'
        ], 'Login successful.');
        break;

    case 'register':
        $fullName = trim($input['full_name'] ?? '');
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';
        $role = $input['requested_role'] ?? '';
        $chapterId = !empty($input['chapter_id']) && is_numeric($input['chapter_id']) ? (int)$input['chapter_id'] : null;

        if (empty($fullName) || empty($email) || empty($password) || empty($role)) {
            jsonError('All required fields must be filled out.', 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            jsonError('Please provide a valid email address.', 400);
        }

        if (strlen($password) < 8) {
            jsonError('Password must contain at least 8 characters.', 400);
        }

        if (!in_array($role, ['national_admin', 'chapter_admin'])) {
            jsonError('Invalid role requested.', 400);
        }

        if ($role === 'chapter_admin' && empty($chapterId)) {
            jsonError('Please select a valid chapter for Chapter Admin role.', 400);
        }

        // Check duplicate email
        $checkStmt = $db->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
        $checkStmt->execute(['email' => $email]);
        if ($checkStmt->fetch()) {
            jsonError('An account with this email address already exists or is pending review.', 409);
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $insertStmt = $db->prepare("
            INSERT INTO users (chapter_id, full_name, email, password_hash, role, account_status, requested_at)
            VALUES (:chapter_id, :full_name, :email, :password_hash, :role, 'pending', NOW())
        ");
        $insertStmt->execute([
            'chapter_id'    => ($role === 'chapter_admin') ? $chapterId : null,
            'full_name'     => $fullName,
            'email'         => $email,
            'password_hash' => $passwordHash,
            'role'          => $role
        ]);

        jsonResponse([
            'user_id' => $db->lastInsertId()
        ], 'Account request submitted successfully. It will be reviewed by an administrator.', 201);
        break;

    case 'logout':
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        jsonResponse([], 'Logged out successfully.');
        break;

    case 'me':
        if (empty($_SESSION['user_id'])) {
            jsonError('Not logged in.', 401);
        }
        jsonResponse([
            'user' => [
                'id'           => $_SESSION['user_id'],
                'full_name'    => $_SESSION['user_name'],
                'email'        => $_SESSION['user_email'],
                'role'         => $_SESSION['user_role'],
                'chapter_id'   => $_SESSION['chapter_id'],
                'chapter_name' => $_SESSION['chapter_name']
            ]
        ]);
        break;

    default:
        jsonError("Invalid action. Supported actions: 'login', 'register', 'logout', 'me'.", 400);
}
