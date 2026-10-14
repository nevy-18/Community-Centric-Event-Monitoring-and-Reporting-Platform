<?php
/**
 * Database Connection Test Script
 */
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/db.php';

try {
    $db = getDB();
    $dbNameStmt = $db->query('SELECT DATABASE() AS current_db');
    $dbName = $dbNameStmt->fetchColumn();

    $tablesStmt = $db->query("SHOW TABLES");
    $tables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode([
        'status' => 'success',
        'message' => 'Database connection established successfully!',
        'database' => $dbName,
        'table_count' => count($tables),
        'tables' => $tables,
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
}
