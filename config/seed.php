<?php
/**
 * Database Seeder for Initial Development Data
 */
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db.php';

try {
    $db = getDB();

    // 1. Seed Chapters
    $chapters = [
        ['code' => 'YTR-LAG', 'name' => 'Laguna Chapter', 'gen' => 'Gen One', 'region' => 'Region IV-A', 'prov' => 'Laguna', 'city' => 'Los Baños'],
        ['code' => 'YTR-NCR', 'name' => 'NCR Chapter', 'gen' => 'Gen Two', 'region' => 'NCR', 'prov' => 'Metro Manila', 'city' => 'Quezon City'],
        ['code' => 'YTR-CAV', 'name' => 'Cavite Chapter', 'gen' => 'Gen One', 'region' => 'Region IV-A', 'prov' => 'Cavite', 'city' => 'Tagaytay'],
        ['code' => 'YTR-PAM', 'name' => 'Pampanga Chapter', 'gen' => 'Gen Three', 'region' => 'Region III', 'prov' => 'Pampanga', 'city' => 'San Fernando'],
        ['code' => 'YTR-BAT', 'name' => 'Batangas Chapter', 'gen' => 'Gen Two', 'region' => 'Region IV-A', 'prov' => 'Batangas', 'city' => 'Batangas City'],
    ];

    $chapterStmt = $db->prepare("
        INSERT INTO chapters (chapter_code, chapter_name, generation_name, region, province, city_municipality, status)
        VALUES (:code, :name, :gen, :region, :prov, :city, 'active')
        ON DUPLICATE KEY UPDATE chapter_name = VALUES(chapter_name)
    ");

    foreach ($chapters as $c) {
        $chapterStmt->execute($c);
    }

    $lagunaId = $db->query("SELECT id FROM chapters WHERE chapter_code = 'YTR-LAG'")->fetchColumn();
    $ncrId    = $db->query("SELECT id FROM chapters WHERE chapter_code = 'YTR-NCR'")->fetchColumn();
    $caviteId = $db->query("SELECT id FROM chapters WHERE chapter_code = 'YTR-CAV'")->fetchColumn();
    $pamId    = $db->query("SELECT id FROM chapters WHERE chapter_code = 'YTR-PAM'")->fetchColumn();

    // 2. Seed Initial Admin Users
    $defaultPassHash = password_hash('Password123!', PASSWORD_DEFAULT);

    $users = [
        [
            'chapter_id' => null,
            'full_name'  => 'Hamilton Jones Dela',
            'email'      => 'admin@ytr.org',
            'pass_hash'  => $defaultPassHash,
            'role'       => 'national_admin',
            'status'     => 'active'
        ],
        [
            'chapter_id' => $lagunaId,
            'full_name'  => 'Joan Dela Cruz',
            'email'      => 'joan.dela@gmail.com',
            'pass_hash'  => $defaultPassHash,
            'role'       => 'chapter_admin',
            'status'     => 'active'
        ],
        [
            'chapter_id' => $ncrId,
            'full_name'  => 'Mark Bautista',
            'email'      => 'mark.bautista@gmail.com',
            'pass_hash'  => $defaultPassHash,
            'role'       => 'chapter_admin',
            'status'     => 'pending'
        ],
        [
            'chapter_id' => $caviteId,
            'full_name'  => 'Elena Reyes',
            'email'      => 'elena.reyes@gmail.com',
            'pass_hash'  => $defaultPassHash,
            'role'       => 'chapter_admin',
            'status'     => 'pending'
        ],
        [
            'chapter_id' => $pamId,
            'full_name'  => 'Carlos Santos',
            'email'      => 'carlos.santos@gmail.com',
            'pass_hash'  => $defaultPassHash,
            'role'       => 'chapter_admin',
            'status'     => 'active'
        ]
    ];

    $userStmt = $db->prepare("
        INSERT INTO users (chapter_id, full_name, email, password_hash, role, account_status)
        VALUES (:chapter_id, :full_name, :email, :pass_hash, :role, :status)
        ON DUPLICATE KEY UPDATE full_name = VALUES(full_name), account_status = VALUES(account_status)
    ");

    foreach ($users as $u) {
        $userStmt->execute($u);
    }

    $adminId = $db->query("SELECT id FROM users WHERE email = 'admin@ytr.org'")->fetchColumn();
    $joanId  = $db->query("SELECT id FROM users WHERE email = 'joan.dela@gmail.com'")->fetchColumn();

    // 3. Seed Events / Proposals
    $events = [
        [
            'chapter_id'     => $lagunaId,
            'title'          => 'Youth Camp 2026: Unshakeable Faith',
            'description'    => 'Annual 3-day spiritual retreat and leadership workshop for youth members across Laguna.',
            'event_type'     => 'chapter',
            'start_datetime' => '2026-11-20 08:00:00',
            'end_datetime'   => '2026-11-22 17:00:00',
            'venue'          => 'Camp John Hay Eco Trail, Los Baños',
            'status'         => 'pending_approval',
            'created_by'     => $joanId
        ],
        [
            'chapter_id'     => $ncrId,
            'title'          => 'Metro Youth Leadership Summit 2026',
            'description'    => 'Gathering campus leaders from universities across Metro Manila for empowerment and ministry training.',
            'event_type'     => 'national',
            'start_datetime' => '2026-12-05 09:00:00',
            'end_datetime'   => '2026-12-05 18:00:00',
            'venue'          => 'SMX Convention Center, Pasay City',
            'status'         => 'approved',
            'created_by'     => $adminId
        ],
        [
            'chapter_id'     => $caviteId,
            'title'          => 'Tagaytay Prayer & Praise Night',
            'description'    => 'Evening of worship, testimony, and community fellowship at the ridge.',
            'event_type'     => 'chapter',
            'start_datetime' => '2026-10-28 18:00:00',
            'end_datetime'   => '2026-10-28 21:30:00',
            'venue'          => 'Tagaytay Highlands Amphitheater',
            'status'         => 'draft',
            'created_by'     => $joanId
        ],
        [
            'chapter_id'     => $pamId,
            'title'          => 'Central Luzon Youth Sportsfest',
            'description'    => 'Inter-church basketball, volleyball, and team-building games promoting fellowship.',
            'event_type'     => 'community',
            'start_datetime' => '2026-10-02 07:00:00',
            'end_datetime'   => '2026-10-02 17:00:00',
            'venue'          => 'Bren Z. Guiao Sports Complex, San Fernando',
            'status'         => 'completed',
            'created_by'     => $adminId
        ]
    ];

    $eventStmt = $db->prepare("
        INSERT INTO events (chapter_id, title, description, event_type, start_datetime, end_datetime, venue, status, created_by)
        VALUES (:chapter_id, :title, :description, :event_type, :start_datetime, :end_datetime, :venue, :status, :created_by)
    ");

    $existingEventCount = $db->query("SELECT COUNT(*) FROM events")->fetchColumn();
    if ($existingEventCount == 0) {
        foreach ($events as $ev) {
            $eventStmt->execute($ev);
        }
    }

    // 4. Seed Reports
    $sportsfestId = $db->query("SELECT id FROM events WHERE title LIKE '%Sportsfest%' LIMIT 1")->fetchColumn();
    if ($sportsfestId) {
        $reportCount = $db->query("SELECT COUNT(*) FROM event_reports WHERE event_id = {$sportsfestId}")->fetchColumn();
        if ($reportCount == 0) {
            $repStmt = $db->prepare("
                INSERT INTO event_reports (event_id, chapter_id, submitted_by, total_participants, first_timers, existing_members, report_summary, status, submitted_at)
                VALUES (:event_id, :chapter_id, :submitted_by, 142, 38, 104, 'The sportsfest was successfully held with high attendance and active engagement.', 'approved', NOW())
            ");
            $repStmt->execute([
                'event_id'     => $sportsfestId,
                'chapter_id'   => $pamId,
                'submitted_by' => $joanId
            ]);
        }
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Development database seeded successfully!',
        'chapters_seeded' => count($chapters),
        'users_seeded' => count($users),
        'events_seeded' => count($events),
        'demo_credentials' => [
            'national_admin' => ['email' => 'admin@ytr.org', 'password' => 'Password123!'],
            'chapter_admin'  => ['email' => 'joan.dela@gmail.com', 'password' => 'Password123!']
        ]
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()], JSON_PRETTY_PRINT);
}
