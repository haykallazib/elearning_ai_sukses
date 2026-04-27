<?php
require_once 'config.php';
redirectIfNotLoggedIn();
$user_id = $_SESSION['user_id'];
$nama = $_SESSION['nama'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - E-Learning AI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: linear-gradient(145deg, #f8fafc 0%, #eef2ff 100%);
            font-family: 'Inter', sans-serif;
            color: #0f172a;
        }
        /* Navbar */
        .navbar {
            background-color: #0f172a !important;
            backdrop-filter: blur(8px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .navbar-brand {
            font-weight: 700;
            letter-spacing: -0.3px;
            font-size: 1.4rem;
        }
        .navbar-nav .nav-link {
            font-weight: 500;
            transition: all 0.2s;
            border-radius: 40px;
            padding: 0.5rem 1rem;
        }

        
        .navbar-nav .nav-link:hover {
            color: white !important;
            transform: translateY(-2px);
            background: rgba(255,255,255,0.05);
        }
        /* Hero Section */
        .hero-section {
            text-align: center;
            margin: 2rem 0 3rem;
        }
        .hero-section h1 {
            font-size: 2.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #1e293b, #3b82f6);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: -0.5px;
        }
        .hero-section p {
            font-size: 1.1rem;
            color: #475569;
            max-width: 600px;
            margin: 0 auto;
        }
        /* Feature Cards */
        .feature-card {
            border: none;
            border-radius: 1.5rem;
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(2px);
            transition: all 0.3s cubic-bezier(0.2, 0, 0, 1);
            box-shadow: 0 10px 20px -5px rgba(0,0,0,0.05);
            height: 100%;
        }
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 30px -10px rgba(0,0,0,0.1);
            background: white;
        }
        .feature-card .card-body {
            padding: 2rem 1.5rem;
        }
        .feature-card i {
            font-size: 2.8rem;
            margin-bottom: 1rem;
        }
        .feature-card h5 {
            font-weight: 700;
            margin-bottom: 0.75rem;
        }
        .feature-card p {
            color: #475569;
            font-size: 0.9rem;
        }
        /* Tombol CTA */
        .btn-cta {
            background: linear-gradient(95deg, #1e293b, #0f172a);
            border: none;
            border-radius: 3rem;
            padding: 0.9rem 2.2rem;
            font-weight: 600;
            transition: all 0.2s;
            color: white;
            box-shadow: 0 8px 14px rgba(0,0,0,0.1);
        }
        .btn-cta:hover {
            transform: translateY(-3px);
            background: linear-gradient(95deg, #0f172a, #1e293b);
            box-shadow: 0 12px 20px rgba(0,0,0,0.15);
            color: white;
        }
        /* Footer */
        footer {
            margin-top: 1rem;
            padding: 1rem;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            background: white;
            font-size: 0.8rem;
            color: #64748b;
        }
        /* Responsive */
        @media (max-width: 768px) {
            .hero-section h1 { font-size: 1.8rem; }
            .feature-card .card-body { padding: 1.5rem; }
        }
    </style>
</head>
<body>

<!-- Navbar konsisten -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fas fa-brain me-2"></i>E-Learning AI</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link active" href="index.php"><i class="fas fa-home me-1"></i> Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="riwayat.php"><i class="fas fa-history me-1"></i> Riwayat</a></li>
                <li class="nav-item"><a class="nav-link" href="admin.php"><i class="fas fa-cogs me-1"></i> Admin</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-1"></i> Logout</a></li>
            </ul>
            <span class="navbar-text text-white ms-3"><?= htmlspecialchars($nama) ?></span>
        </div>
    </div>
</nav>

<main class="container py-4">
    <!-- Hero -->
    <div class="hero-section">
        <h1>Selamat Datang di E-Learning AI</h1>
        <p>Platform pembelajaran adaptif yang menyesuaikan dengan gaya belajarmu</p>
    </div>

    <!-- 3 Card: Multi Platform, Interactive & Fun, Library Edukasi -->
    <div class="row g-4 mb-5">
        <!-- Multi Platform (link ke pembelajaran.php) -->
        <div class="col-md-4">
            <a href="pembelajaran.php" class="text-decoration-none">
                <div class="card feature-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-mobile-alt text-primary"></i>
                        <h5 class="card-title">Multi Platform</h5>
                        <p class="card-text">Temukan berbagai materi interaktif yang mendukung gaya belajarmu</p>
                    </div>
                </div>
            </a>
        </div>
        <!-- Interactive & Fun (link ke game.php) -->
        <div class="col-md-4">
            <a href="game.php" class="text-decoration-none">
                <div class="card feature-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-gamepad text-success"></i>
                        <h5 class="card-title">Interactive & Fun</h5>
                        <p class="card-text">Disajikan dalam format Game dan Simulasi yang interaktif dan menyenangkan</p>
                    </div>
                </div>
            </a>
        </div>
        <!-- Library Edukasi (link ke library.php) -->
        <div class="col-md-4">
            <a href="library.php" class="text-decoration-none">
                <div class="card feature-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-book-open text-info"></i>
                        <h5 class="card-title">Library Edukasi</h5>
                        <p class="card-text">Berisi tentang buku, jurnal maupun artikel untuk meningkatkan pembelajaran</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Tombol Mulai Kuesioner -->
    <div class="text-center">
        <a href="kuesioner.php" class="btn btn-cta"><i class="fas fa-arrow-right me-2"></i> Mulai Kuesioner Gaya Belajar</a>
    </div>
</main>

<footer>
    &copy; <?= date('Y') ?> E-Learning AI | Platform adaptif berbasis gaya belajar modern
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>