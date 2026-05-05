<?php
require_once 'config.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['status' => 'error', 'message' => 'No data received']);
    exit;
}

$userId = $input['userId'] ?? 0;
$activity_id = $input['activity_id'] ?? 0;
$score = $input['score_performance'] ?? 0;
$style_before = $input['style_before'] ?? '';
$summary = $input['summary'] ?? '';

if (!$userId || !$activity_id) {
    echo json_encode(['status' => 'error', 'message' => 'Missing userId or activity_id']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO user_activity_log (user_id, activity_id, style_before, score_performance, summary) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $activity_id, $style_before, $score, $summary]);
    echo json_encode(['status' => 'success', 'id' => $pdo->lastInsertId()]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>