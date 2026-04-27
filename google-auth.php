<?php
require_once 'config.php';
require_once 'vendor/autoload.php'; // library Google API Client

// Ganti dengan kredensial yang sama di google-auth.php
$client_id = '117357464740-8cii3o5585l08jkccq0k42aaes26fmvi.apps.googleusercontent.com';
$client_secret = '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';
$redirect_uri = 'http://localhost/elearning_ai_final/google-callback.php'; // Path yang sama persis
// ===================================================

$client = new Google_Client();
$client->setClientId($client_id);
$client->setClientSecret($client_secret);
$client->setRedirectUri($redirect_uri);
$client->addScope('email');
$client->addScope('profile');
$client->setPrompt('select_account'); // 🔥 INI YANG MEMUNGKINKAN PILIH AKUN BERBEDA

$auth_url = $client->createAuthUrl();
header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
exit;
?>