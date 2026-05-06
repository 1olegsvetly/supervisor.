<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/stats.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $type = $data['type'] ?? '';
    $details = $data['details'] ?? '';

    if (in_array($type, ['tg_direct', 'tg_test'])) {
        StatsManager::logClick($type, $details);
        echo json_encode(['status' => 'success']);
        exit;
    }
}

http_response_code(400);
echo json_encode(['status' => 'error']);
