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
    <title>Kuesioner - E-Learning AI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(145deg, #f4f7fc 0%, #e9eef5 100%);
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            line-height: 1.5;
        }

        /* ========== NAVBAR ========== */
        .navbar {
            background-color: #0f172a !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 0.75rem 0;
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.4rem;
            letter-spacing: -0.3px;
            color: white !important;
        }
        .navbar-nav .nav-link {
            color: #cbd5e1 !important;
            font-weight: 500;
            margin: 0 0.25rem;
            padding: 0.5rem 0.9rem;
            border-radius: 2rem;
            transition: all 0.2s ease;
        }
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: white !important;
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-1px);
        }
        .navbar-text {
            color: #f1f5f9 !important;
            font-weight: 500;
        }

        /* ========== KONTEN UTAMA ========== */
        .kuesioner-wrapper {
            max-width: 900px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        /* Header */
        .kuesioner-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .kuesioner-header h2 {
            font-weight: 800;
            background: linear-gradient(135deg, #1e293b, #3b82f6);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-size: 2rem;
        }
        .kuesioner-header p {
            color: #475569;
            font-size: 1rem;
        }

        /* Card pertanyaan */
        .question-card {
            background: #ffffff;
            border-radius: 1.25rem;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
            border: 1px solid #e9edf2;
            transition: all 0.2s;
        }
        .question-card:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.04);
            border-color: #dce2e8;
        }

        /* Header pertanyaan (nomor + teks) */
        .question-header {
            display: flex;
            align-items: baseline;
            gap: 0.75rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }
        .question-number {
            background: #eef2ff;
            color: #1e40af;
            border-radius: 2rem;
            padding: 0.2rem 0.8rem;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }
        .question-text {
            font-weight: 600;
            font-size: 0.95rem;
            color: #0f172a;
            line-height: 1.4;
            margin: 0;
        }

        /* Opsi jawaban modern (pill) */
       .options-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
            margin-top: 0.5rem;
        }
        .option-pill {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 3rem;
            transition: all 0.2s;
            cursor: pointer;
        }
        .option-pill:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }
        .option-pill.active {
            background: #3b82f6;
            border-color: #3b82f6;
        }
        .option-pill.active .form-check-label {
            color: white;
        }
        .option-pill .form-check {
            margin: 0;
            padding: 0.3rem 1rem;
        }
        .form-check-input {
            margin-right: 0.5rem;
            cursor: pointer;
        }
        .form-check-label {
            font-size: 0.85rem;
            color: #1e293b;
            cursor: pointer;
        }

        /* Tombol analisis */
        .btn-analyze {
            background: #0f172a;
            border: none;
            border-radius: 3rem;
            padding: 0.7rem 2rem;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }
        .btn-analyze:hover {
            background: #1e293b;
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        /* Generator quiz card */
        .quiz-card {
            background: #ffffff;
            border-radius: 1.25rem;
            border: 1px solid #e9edf2;
            padding: 1.5rem;
            margin-top: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
        }
        .quiz-card h5 {
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        footer {
            text-align: center;
            padding: 1.5rem;
            color: #64748b;
            font-size: 0.75rem;
            border-top: 1px solid #e2e8f0;
            background: white;
            margin-top: 2rem;
        }

        /* Responsif */
        @media (max-width: 768px) {
            .question-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            .question-text {
                font-size: 0.9rem;
            }
            .options-wrapper {
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body>


<!-- Navbar sama persis seperti index.php (menu lengkap, ikon, active pada Kuesioner) -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fas fa-brain me-2"></i>E-Learning AI</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-home me-1"></i> Beranda</a></li>
                <li class="nav-item"><a class="nav-link active" href="kuesioner.php"><i class="fas fa-clipboard-list me-1"></i> Kuesioner</a></li>
                <li class="nav-item"><a class="nav-link" href="riwayat.php"><i class="fas fa-history me-1"></i> Riwayat</a></li>
                <li class="nav-item"><a class="nav-link" href="admin.php"><i class="fas fa-cogs me-1"></i> Admin</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-1"></i> Logout</a></li>
            </ul>
             <span class="navbar-text ms-3"></i><?= htmlspecialchars($nama) ?></span>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="kuesioner-header">
        <h2><i class="fas fa-clipboard-list me-2"></i> Kuesioner Gaya Belajar</h2>
        <p>Jawab 8 pertanyaan berikut untuk mengetahui kecenderungan gaya belajar Anda</p>
    </div>
                    <div id="questions-container"></div>
                    <div class="text-center mt-4">
        <button id="analyzeBtn" class="btn btn-analyze text-white"><i class="fas fa-chart-line me-2"></i> Analisis dengan AI</button>
    </div>
                    
                    
                    <div id="resultArea" style="display:none;" class="mt-4">
                        <div class="alert alert-info">
                            <h5>Hasil Klasifikasi AI</h5>
                            <div id="resultDetail"></div>
                        </div>
                        <div id="rekomendasiAktivitas"></div>
                        <div id="kontenAktivitas"></div>
                        <div id="performaForm"></div>
                    </div>

                    <!-- Generator Quiz AI (Gemini) -->
                    <div class="mt-5 p-3 border rounded bg-light">
                        <h5><i class="fas fa-magic"></i>Generate Quiz</h5>
                        <div class="row g-2">
                            <div class="col-md-5">
                                <input type="text" id="quizTopic" class="form-control" placeholder="Topik (contoh: 'Dasar ai')">
                            </div>
                            <div class="col-md-3">
                                <select id="quizCount" class="form-select">
                                    <option value="3">3 Soal</option>
                                    <option value="5" selected>5 Soal</option>
                                    <option value="10">10 Soal</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button id="generateQuizBtn" class="btn btn-success w-100">
                                    <i class="fas fa-bolt"></i> Generate Quiz dengan AI
                                </button>
                            </div>
                        </div>
                        <div id="quizResultArea" style="display:none;" class="mt-3">
                            <div id="quizQuestions"></div>
                            <button id="submitQuizBtn" class="btn btn-primary mt-2">Submit Jawaban</button>
                            <div id="quizFeedback"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="script.js"></script>
</body>
</html>