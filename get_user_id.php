<?php
require_once 'config.php';
header('Content-Type: application/json');
if (isLoggedIn()) {
    echo json_encode(['userId' => $_SESSION['user_id'], 'nama' => $_SESSION['nama']]);
} else {
    echo json_encode(['userId' => null]);
}
?>