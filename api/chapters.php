<?php
/**
 * Chapters API Endpoint
 * Handles retrieving and registering partner chapters
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/response.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'POST') {
    try {
        $db = getDB();
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        
        $chapter_name = trim($input['chapter_name'] ?? '');
        $chapter_code = strtoupper(trim($input['chapter_code'] ?? ''));
        $region = trim($input['region'] ?? '');
        $province = trim($input['province'] ?? '');
        $city_municipality = trim($input['city_municipality'] ?? '');
        $generation_name = trim($input['generation_name'] ?? 'Gen One');

        if (empty($chapter_name)) {
            jsonError('Chapter name is required.', 400);
        }

        if (empty($chapter_code)) {
            $clean = preg_replace('/chapter/i', '', $chapter_name);
            $clean = trim($clean);
            $words = preg_split('/\s+/', $clean);
            $prefix = '';
            foreach ($words as $w) {
                $prefix .= substr($w, 0, 3);
            }
            $prefix = strtoupper(substr($prefix ?: 'CHP', 0, 6));
            $chapter_code = 'YTR-' . $prefix;
        }

        $check = $db->prepare("SELECT id FROM chapters WHERE LOWER(chapter_name) = ? OR chapter_code = ? LIMIT 1");
        $check->execute([strtolower($chapter_name), $chapter_code]);
        if ($check->fetch()) {
            jsonError("A chapter with name '{$chapter_name}' or code '{$chapter_code}' already exists.", 409);
        }

        $stmt = $db->prepare("
            INSERT INTO chapters (chapter_code, chapter_name, generation_name, region, province, city_municipality, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, 'active', NOW(), NOW())
        ");
        $stmt->execute([$chapter_code, $chapter_name, $generation_name, $region ?: null, $province ?: null, $city_municipality ?: null]);
        $new_id = $db->lastInsertId();

        $getStmt = $db->prepare("SELECT * FROM chapters WHERE id = ?");
        $getStmt->execute([$new_id]);
        $new_ch = $getStmt->fetch();

        jsonResponse($new_ch, "Partner Chapter '{$chapter_name}' added successfully.", 201);
    } catch (Exception $e) {
        jsonError('Failed to create chapter: ' . $e->getMessage(), 500);
    }
} else {
    try {
        $db = getDB();
        $stmt = $db->query("
            SELECT id, chapter_code, chapter_name, region, province, city_municipality, status
            FROM chapters
            WHERE status = 'active' OR status IS NULL OR status = ''
            ORDER BY chapter_name ASC
        ");
        $chapters = $stmt->fetchAll();

        jsonResponse($chapters, 'Chapters retrieved successfully.');
    } catch (Exception $e) {
        jsonError('Failed to fetch chapters: ' . $e->getMessage(), 500);
    }
}
