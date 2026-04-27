<?php
require_once 'config.php';
redirectIfLoggedIn();

$error = '';
$email = '';
$rememberChecked = false;

// Cek cookie untuk remember me
$rememberEmail = $_COOKIE['user_email'] ?? '';
$rememberChecked = isset($_COOKIE['user_remember']) ? 'checked' : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']) ? true : false;
    $rememberChecked = $remember ? 'checked' : '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['username'] = $user['username'];

        if ($remember) {
            setcookie('user_email', $email, time() + (86400 * 30), "/");
            setcookie('user_remember', '1', time() + (86400 * 30), "/");
        } else {
            setcookie('user_email', '', time() - 3600, "/");
            setcookie('user_remember', '', time() - 3600, "/");
        }

        header('Location: index.php');
        exit;
    } else {
        $error = 'Email atau password salah.';
    }
}

// Jika ada cookie email, gunakan untuk nilai default email
$defaultEmail = $rememberEmail;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-Learning AI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: linear-gradient(145deg, #f4f7fc 0%, #e9eef5 100%);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .login-card {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 20px 35px -10px rgba(0, 0, 0, 0.1);
            padding: 2rem 1.8rem;
            width: 100%;
            max-width: 420px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .login-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 25px 40px -12px rgba(0, 0, 0, 0.15);
        }
        .login-header h2 {
            font-weight: 700;
            font-size: 1.6rem;
            color: #0f172a;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .form-group { margin-bottom: 1.2rem; }
        .form-group label {
            font-weight: 500;
            color: #1e293b;
            margin-bottom: 0.4rem;
            display: block;
            font-size: 0.85rem;
        }
        .form-control {
            width: 100%;
            padding: 0.7rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            font-size: 0.9rem;
            transition: all 0.2s;
            background: #f8fafc;
        }
        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
        }
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            font-size: 0.8rem;
        }
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            cursor: pointer;
            color: #475569;
        }
        .checkbox-label input { width: 1rem; height: 1rem; cursor: pointer; accent-color: #3b82f6; }
        .forgot-link { color: #3b82f6; text-decoration: none; font-weight: 500; }
        .forgot-link:hover { text-decoration: underline; }
        .btn-login {
            background: #0f172a;
            border: none;
            border-radius: 0.75rem;
            padding: 0.7rem;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s;
            width: 100%;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            cursor: pointer;
            margin-bottom: 0.5rem;
        }
        .btn-login:hover {
            background: #1e293b;
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(0,0,0,0.1);
        }
        .register-link {
            text-align: center;
            margin-top: 1.2rem;
            font-size: 0.85rem;
            color: #475569;
        }
        .register-link a { color: #3b82f6; text-decoration: none; font-weight: 500; }
        .register-link a:hover { text-decoration: underline; }
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.2rem 0 1rem;
            color: #94a3b8;
            font-size: 0.75rem;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e2e8f0;
        }
        .divider::before { margin-right: 1rem; }
        .divider::after { margin-left: 1rem; }
        .btn-google {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 0.6rem;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.2s;
            width: 100%;
            color: #334155;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
        }
        .btn-google:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            transform: translateY(-2px);
        }
        .alert {
            border-radius: 0.75rem;
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
<div class="login-card">
    <div class="login-header">
        <h2>Login</h2>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($defaultEmail) ?>" required autofocus>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" id="passwordInput" name="password" class="form-control" required>
        </div>
        <div class="form-options">
            <div>
                
                <label class="checkbox-label" style="margin-left: 1rem;">
                    <input type="checkbox" id="showPasswordCheckbox"> Show password
                </label>
            </div>
            <a href="forgot-password.php" class="forgot-link">Forgot Password?</a>
        </div>
        <button type="submit" class="btn-login">
            <i></i> Login
        </button>
    </form>

    <div class="register-link">
        Don't have an account? <a href="register.php">Register</a>
    </div>

    <div class="divider">atau</div>
    <a href="google-auth.php" class="btn-google">
        <i class="fab fa-google"></i> Login dengan Google
    </a>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('passwordInput');
        const showPasswordCheckbox = document.getElementById('showPasswordCheckbox');
        if (!passwordInput || !showPasswordCheckbox) return;

        showPasswordCheckbox.addEventListener('change', function() {
            passwordInput.type = this.checked ? 'text' : 'password';
        });
    });
</script>
</body>
</html>