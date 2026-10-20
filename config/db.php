<?php
/**
 * Youth on the Rock (YTR) - Database Connection
 * Community-Centric Event Monitoring and Reporting Platform
 */

// Simple .env parser function
function loadEnv($path) {
    if (!file_exists($path)) {
        return;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) {
            continue;
        }
        if (strpos($line, '=') !== false) {
            list($key, $val) = explode('=', $line, 2);
            $key = trim($key);
            $val = trim($val);
            $val = trim($val, "\"'"); // Remove surrounding quotes
            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $val;
                putenv("$key=$val");
            }
        }
    }
}

// Load .env from project root
$envPath = __DIR__ . '/../.env';
loadEnv($envPath);

$dbHost = getenv('DB_HOST') ?: 'sql313.infinityfree.com';
$dbPort = getenv('DB_PORT') ?: '3306';
$dbName = getenv('DB_NAME') ?: 'if0_43221777_ytr_event_monitoring';
$dbUser = getenv('DB_USER') ?: 'sql313.infinityfree.com';
$dbPass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : 'rayvhen1438';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
];

try {
    $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (PDOException $e) {
    // If called directly via CLI or test endpoint, log the error
    error_log("Database connection failed: " . $e->getMessage());
    if (php_sapi_name() === 'cli') {
        throw new Exception("Database Connection Error: " . $e->getMessage());
    }
    $pdo = null;
    $dbError = $e->getMessage();
}

/**
 * Helper to get the active PDO instance
 */
function getDB() {
    global $pdo;
    if (!$pdo) {
        throw new Exception("No active database connection available.");
    }
    return $pdo;
}
