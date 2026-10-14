<?php
/**
 * Chapters API Endpoint
 * Returns list of active partner chapters
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/response.php';

try {
    $db = getDB();
    $stmt = $db->query("
        SELECT id, chapter_code, chapter_name, region, province, city_municipality, status
        FROM chapters
        WHERE status = 'active'
        ORDER BY chapter_name ASC
    ");
    $chapters = $stmt->fetchAll();

    jsonResponse($chapters, 'Chapters retrieved successfully.');
} catch (Exception $e) {
    jsonError('Failed to fetch chapters: ' . $e->getMessage(), 500);
}
