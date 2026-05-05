<?php
require_once 'config.php';
redirectIfNotLoggedIn();
$user_id = $_SESSION['user_id'];
$nama = $_SESSION['nama'];

// Proses simpan bobot
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_weights'])) {
    $v = floatval($_POST['visual_weight']);
    $a = floatval($_POST['auditory_weight']);
    $r = floatval($_POST['reading_weight']);
    $k = floatval($_POST['kinesthetic_weight']);
    $stmt = $pdo->prepare("INSERT INTO classification_rules (rule_name, visual_weight, auditory_weight, reading_weight, kinesthetic_weight) VALUES ('admin_rule', ?, ?, ?, ?)");
    $stmt->execute([$v, $a, $r, $k]);
    $msg = "Bobot berhasil diperbarui.";
    // Refresh halaman agar bobot terbaru terbaca
    header("Location: admin.php?updated=1");
    exit;
}

// Ambil bobot terbaru
$stmt = $pdo->query("SELECT * FROM classification_rules ORDER BY id DESC LIMIT 1");
$rule = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$rule) {
    $rule = ['visual_weight' => 1, 'auditory_weight' =>1 , 'reading_weight' => 1, 'kinesthetic_weight' => 1];
}

// Ambil frekuensi dari hasil kuesioner user terakhir
$stmt = $pdo->prepare("SELECT scores FROM user_results WHERE user_id = ? ORDER BY id DESC LIMIT 1");
$stmt->execute([$user_id]);
$lastResult = $stmt->fetch(PDO::FETCH_ASSOC);
if ($lastResult && $lastResult['scores']) {
    $scores = json_decode($lastResult['scores'], true);
    $freq = [
        'visual' => $scores['visual'] ?? 0,
        'auditory' => $scores['auditory'] ?? 0,
        'reading' => $scores['reading'] ?? 0,
        'kinesthetic' => $scores['kinesthetic'] ?? 0
    ];
} else {
    $freq = ['visual' => 3, 'auditory' => 4, 'reading' => 2, 'kinesthetic' => 0];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin - Bobot Klasifikasi AI | E-Learning</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <style>
        body { background: #f3f6f9; font-family: 'Inter', sans-serif; }
        /* Navbar sama seperti index.php */
        .navbar {
            background-color: #0f172a !important;
            transition: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .navbar-brand {
            font-weight: 600;
            font-size: 1.25rem;
            color: white !important;
            transition: transform 0.2s ease;
        }
        .navbar-brand:hover {
            transform: translateY(-1px);
        }
        .navbar-nav .nav-link {
            color: #9ca3af !important;
            font-weight: 500;
            margin: 0 0.25rem;
            padding: 0.5rem 0.75rem;
            border-radius: 2rem;
            transition: all 0.25s cubic-bezier(0.2, 0, 0, 1);
            display: inline-block;
        }
        .navbar-nav .nav-link:hover {
            color: white !important;
            transform: translateY(-2px);
            background: rgba(255,255,255,0.05);
        }
        .navbar-nav .nav-link:active {
            transform: scale(0.96);
            background: rgba(255,255,255,0.2);
            box-shadow: 0 0 0 2px rgba(255,255,255,0.3);
            transition: all 0.05s linear;
        }
        .navbar-nav .nav-link.active {
            color: white !important;
            font-weight: 600;
            
            text-underline-offset: 6px;
            background: rgba(255,255,255,0.08);
        }
       
        .navbar-text {
            color: white !important;
        }
        /* Card styles */
        .card-custom { border: none; border-radius: 1.25rem; background: #ffffff; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.05); }
        .weight-card { background: #f8fafc; border-radius: 1rem; padding: 1rem; transition: all 0.2s; border: 1px solid #e5e7eb; }
        .weight-card:hover { border-color: #cbd5e1; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
        .weight-label { font-weight: 600; font-size: 0.9rem; color: #1f2937; margin-bottom: 0.5rem; }
        .weight-input { font-size: 1.1rem; font-weight: 500; text-align: center; border-radius: 2rem; border: 1px solid #d1d5db; background: white; padding: 0.5rem; width: 100%; }
        .weight-input:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59,130,246,0.2); }
        .stat-card { background: #f8fafc; border-radius: 1rem; padding: 1.25rem; height: 100%; }
        .stat-label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; color: #6c757d; font-weight: 600; }
        .stat-value { font-size: 1.5rem; font-weight: 700; color: #1e293b; }
        .dominant-badge { background: #1e40af; color: white; padding: 0.5rem 1.25rem; border-radius: 2rem; font-weight: 600; font-size: 1rem; }
        .table-custom th, .table-custom td { padding: 0.75rem; vertical-align: middle; border-bottom: 1px solid #e9ecef; }
        .btn-save { background: #111827; border: none; border-radius: 2rem; padding: 0.6rem 2rem; font-weight: 500; transition: all 0.2s; }
        .btn-save:hover { background: #1f2937; transform: translateY(-1px); }
    </style>
</head>
<body>

<!-- Navbar sama seperti index.php (menu lengkap, ikon, active pada Admin) -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fas fa-brain me-2"></i>E-Learning AI</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-home me-1"></i> Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="riwayat.php"><i class="fas fa-history me-1"></i> Riwayat</a></li>
                <li class="nav-item"><a class="nav-link active" href="admin.php"><i class="fas fa-cogs me-1"></i> Admin</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-1"></i> Logout</a></li>
            </ul>
            <span class="navbar-text ms-3"></i> <?= htmlspecialchars($nama) ?></span>
        </div>
    </div>
</nav>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Notifikasi -->
            <?php if (isset($_GET['updated'])): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                    Bobot klasifikasi berhasil diperbarui.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Judul -->
            <div class="text-center mb-4">
                <h2 class="fw-semibold"><i class="text-primary me-2"></i> Aturan Bobot Klasifikasi AI</h2>
                <p class="text-muted">Sesuaikan bobot untuk setiap gaya belajar. Perubahan akan langsung mempengaruhi perhitungan skor akhir.</p>
            </div>

            <!-- Form Bobot -->
            <div class="card-custom mb-4">
                <div class="card-body p-4 p-lg-5">
                    <h5 class="mb-3">Pengaturan Bobot</h5>
                    <form method="POST" id="weightForm">
                        <div class="row g-4">
                            <div class="col-md-3">
                                <div class="weight-card text-center">
                                    <div class="weight-label">Visual</div>
                                    <input type="number" step="0.1" name="visual_weight" id="visual_weight" class="weight-input" value="<?= $rule['visual_weight'] ?>" required>
                                    <div class="small text-muted mt-2">bobot visual</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="weight-card text-center">
                                    <div class="weight-label">Auditory</div>
                                    <input type="number" step="0.1" name="auditory_weight" id="auditory_weight" class="weight-input" value="<?= $rule['auditory_weight'] ?>" required>
                                    <div class="small text-muted mt-2">bobot auditori</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="weight-card text-center">
                                    <div class="weight-label">Reading</div>
                                    <input type="number" step="0.1" name="reading_weight" id="reading_weight" class="weight-input" value="<?= $rule['reading_weight'] ?>" required>
                                    <div class="small text-muted mt-2">bobot membaca</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="weight-card text-center">
                                    <div class="weight-label">Kinesthetic</div>
                                    <input type="number" step="0.1" name="kinesthetic_weight" id="kinesthetic_weight" class="weight-input" value="<?= $rule['kinesthetic_weight'] ?>" required>
                                    <div class="small text-muted mt-2">bobot kinestetik</div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-4">
                            <button type="submit" name="update_weights" class="btn btn-save text-white">Simpan Bobot</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Simulasi Perhitungan Otomatis (Live Preview) -->
            <div class="card-custom">
                <div class="card-body p-4 p-lg-5">
                    <h5 class="mb-3">Simulasi Perhitungan Otomatis</h5>
                    <p class="text-muted small">Data contoh diambil dari hasil kuesioner terakhir Anda. Skor akhir dihitung secara real-time saat bobot diubah.</p>

                    <div class="table-responsive">
                        <table class="table table-borderless table-custom" id="simulationTable">
                            <thead class="bg-light">
                                <tr>
                                    <th>Gaya Belajar</th>
                                    <th class="text-center">Frekuensi (Contoh)</th>
                                    <th class="text-center">Bobot</th>
                                    <th class="text-center">Skor Akhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Visual</td>
                                    <td class="text-center" id="freq_visual"><?= $freq['visual'] ?></td>
                                    <td class="text-center" id="weight_visual_display"><?= $rule['visual_weight'] ?></td>
                                    <td class="text-center fw-bold text-primary" id="score_visual">0</td>
                                </tr>
                                <tr>
                                    <td>Auditory</td>
                                    <td class="text-center" id="freq_auditory"><?= $freq['auditory'] ?></td>
                                    <td class="text-center" id="weight_auditory_display"><?= $rule['auditory_weight'] ?></td>
                                    <td class="text-center fw-bold text-primary" id="score_auditory">0</td>
                                </tr>
                                <tr>
                                    <td>Reading</td>
                                    <td class="text-center" id="freq_reading"><?= $freq['reading'] ?></td>
                                    <td class="text-center" id="weight_reading_display"><?= $rule['reading_weight'] ?></td>
                                    <td class="text-center fw-bold text-primary" id="score_reading">0</td>
                                </tr>
                                <tr>
                                    <td>Kinesthetic</td>
                                    <td class="text-center" id="freq_kinesthetic"><?= $freq['kinesthetic'] ?></td>
                                    <td class="text-center" id="weight_kinesthetic_display"><?= $rule['kinesthetic_weight'] ?></td>
                                    <td class="text-center fw-bold text-primary" id="score_kinesthetic">0</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-center mt-3">
                        <span class="dominant-badge" id="dominant_style">Menghitung...</span>
                    </div>

                    <hr class="my-4">
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Data frekuensi dari PHP
    const freq = {
        visual: <?= $freq['visual'] ?>,
        auditory: <?= $freq['auditory'] ?>,
        reading: <?= $freq['reading'] ?>,
        kinesthetic: <?= $freq['kinesthetic'] ?>
    };

    // Elemen display
    const weightInputs = {
        visual: document.getElementById('visual_weight'),
        auditory: document.getElementById('auditory_weight'),
        reading: document.getElementById('reading_weight'),
        kinesthetic: document.getElementById('kinesthetic_weight')
    };
    const scoreSpans = {
        visual: document.getElementById('score_visual'),
        auditory: document.getElementById('score_auditory'),
        reading: document.getElementById('score_reading'),
        kinesthetic: document.getElementById('score_kinesthetic')
    };
    const weightDisplays = {
        visual: document.getElementById('weight_visual_display'),
        auditory: document.getElementById('weight_auditory_display'),
        reading: document.getElementById('weight_reading_display'),
        kinesthetic: document.getElementById('weight_kinesthetic_display')
    };

    function updateSimulation() {
        // Ambil nilai bobot dari input
        const v = parseFloat(weightInputs.visual.value) || 0;
        const a = parseFloat(weightInputs.auditory.value) || 0;
        const r = parseFloat(weightInputs.reading.value) || 0;
        const k = parseFloat(weightInputs.kinesthetic.value) || 0;

        // Update tampilan bobot di tabel
        weightDisplays.visual.innerText = v;
        weightDisplays.auditory.innerText = a;
        weightDisplays.reading.innerText = r;
        weightDisplays.kinesthetic.innerText = k;

        // Hitung skor
        const scoreV = freq.visual * v;
        const scoreA = freq.auditory * a;
        const scoreR = freq.reading * r;
        const scoreK = freq.kinesthetic * k;

        scoreSpans.visual.innerText = scoreV.toFixed(1);
        scoreSpans.auditory.innerText = scoreA.toFixed(1);
        scoreSpans.reading.innerText = scoreR.toFixed(1);
        scoreSpans.kinesthetic.innerText = scoreK.toFixed(1);

        // Tentukan dominan
        const scores = {
            'Visual': scoreV,
            'Auditory': scoreA,
            'Reading': scoreR,
            'Kinesthetic': scoreK
        };
        let maxStyle = 'Visual';
        let maxVal = scoreV;
        for (const [style, val] of Object.entries(scores)) {
            if (val > maxVal) {
                maxVal = val;
                maxStyle = style;
            }
        }
        document.getElementById('dominant_style').innerHTML = `${maxStyle} (${maxVal.toFixed(1)})`;
    }

    // Pasang event listener untuk setiap input bobot
    for (const key in weightInputs) {
        weightInputs[key].addEventListener('input', updateSimulation);
    }

    // Panggil pertama kali
    updateSimulation();
</script>
</body>
</html>