<?php
require_once 'config.php';
require_once 'vendor/autoload.php';

// Ganti dengan kredensial yang sama di google-auth.php
$client_id = '117357464740-8cii3o5585l08jkccq0k42aaes26fmvi.apps.googleusercontent.com';
$client_secret = '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';
$redirect_uri = 'http://localhost/elearning_ai_final/google-callback.php'; // Path yang sama persis

$client = new Google_Client();
$client->setClientId($client_id);
$client->setClientSecret($client_secret);
$client->setRedirectUri($redirect_uri);

if (isset($_GET['code'])) {
    try {
        // Tukar kode dengan access token
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        $client->setAccessToken($token);

        // Ambil data profil user
        $oauth = new Google_Service_Oauth2($client);
        $userInfo = $oauth->userinfo->get();

        $email = $userInfo->email;
        $name = $userInfo->name;

        // Cek apakah email sudah ada di database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $userId = $user['id'];
            $userName = $user['nama'];
        } else {
            // Buat user baru (password kosong karena login via Google)
            $username = explode('@', $email)[0];
            $stmt = $pdo->prepare("INSERT INTO users (email, username, nama, password) VALUES (?, ?, ?, ?)");
            $stmt->execute([$email, $username, $name, '']);
            $userId = $pdo->lastInsertId();
            $userName = $name;
        }

        // Simpan ke session
        $_SESSION['user_id'] = $userId;
        $_SESSION['nama'] = $userName;
        $_SESSION['email'] = $email;

        // Redirect ke halaman utama
        header('Location: index.php');
        exit;
    } catch (Exception $e) {
        echo 'Terjadi kesalahan: ' . $e->getMessage();
    }
} else {
    echo 'Tidak ada kode otorisasi.';
}
?>