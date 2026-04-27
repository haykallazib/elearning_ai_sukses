<?php
require_once 'config.php';
redirectIfLoggedIn();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validasi
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email tidak valid.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($password !== $confirm_password) {
        $error = 'Konfirmasi password tidak cocok.';
    } elseif (empty($nama)) {
        $error = 'Nama lengkap wajib diisi.';
    } else {
        // Cek apakah email sudah terdaftar
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email sudah terdaftar. Gunakan email lain.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (email, username, nama, password) VALUES (?, ?, ?, ?)");
            $stmt->execute([$email, $username, $nama, $hashed]);
            $success = 'Registrasi berhasil! Silakan <a href="login.php">login</a>.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - E-Learning AI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(145deg, #f4f7fc 0%, #e9eef5 100%);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-card {
            border: none;
            border-radius: 1.5rem;
            box-shadow: 0 20px 35px -10px rgba(0,0,0,0.1);
            background: white;
            padding: 2rem;
            width: 100%;
            max-width: 480px;
        }
        .register-card h3 {
            font-weight: 700;
            color: #0f172a;
        }
        .btn-register {
            background: #0f172a;
            border: none;
            border-radius: 2rem;
            padding: 0.7rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-register:hover {
            background: #1e293b;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59,130,246,0.2);
        }
    </style>
</head>
<body>
<div class="register-card">
    <div class="text-center mb-4">
        <i class="fas fa-brain fa-2x text-primary"></i>
        <h3 class="mt-2">E-Learning AI</h3>
        <p class="text-muted">Daftar akun baru</p>
    </div>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label fw-semibold">Email</label>
            <input type="email" name="email" class="form-control" required placeholder="contoh@email.com">
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Username</label>
            <input type="text" name="username" class="form-control" required placeholder="nama_pengguna">
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" required placeholder="Nama Anda">
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Password</label>
            <input type="password" name="password" class="form-control" required minlength="6">
            <div class="form-text">Minimal 6 karakter</div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Konfirmasi Password</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-register text-white w-100">Daftar Sekarang</button>
    </form>
    <p class="text-center mt-3 mb-0">Sudah punya akun? <a href="login.php">Login</a></p>
</div>
</body>
</html>