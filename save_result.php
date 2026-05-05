<?php
require_once 'config.php';
redirectIfNotLoggedIn();
$input = json_decode(file_get_contents('php://input'), true);
$userId = $input['userId'];
$learning_style = $input['learning_style'];
$scores = json_encode($input['scores']);
$stmt = $pdo->prepare("INSERT INTO user_results (user_id, learning_style, scores) VALUES (?, ?, ?)");
$stmt->execute([$userId, $learning_style, $scores]);
// Update users table
$stmt2 = $pdo->prepare("UPDATE users SET learning_style = ? WHERE id = ?");
$stmt2->execute([$learning_style, $userId]);
echo "OK";
?>