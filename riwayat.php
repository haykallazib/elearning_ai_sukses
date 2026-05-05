<?php
require_once 'config.php';
redirectIfNotLoggedIn();
$user_id = $_SESSION['user_id'];
$nama = $_SESSION['nama'];
// Ambil riwayat aktivitas user
$stmt = $pdo->prepare("
    SELECT l.*, 
           COALESCE(a.title, 'Quiz AI') as title, 
           COALESCE(a.type, 'quiz') as type 
    FROM user_activity_log l 
    LEFT JOIN activities a ON l.activity_id = a.id 
    WHERE l.user_id = ? 
    ORDER BY l.created_at DESC
");
$stmt->execute([$user_id]);
$logs = $stmt->fetchAll();

// Hitung statistik
$total_activities = count($logs);
$avg_score = $total_activities > 0 ? round(array_sum(array_column($logs, 'score_performance')) / $total_activities) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Belajar - E-Learning AI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #f1f5f9; }
        
        /* Navbar sama seperti pembelajaran.php */
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
        
        /* Header & Statistik */
        .hero-stats {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-radius: 1.5rem;
            padding: 1.8rem 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
        }
        .stat-card {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(4px);
            border-radius: 1.25rem;
            padding: 1rem;
            text-align: center;
            transition: all 0.2s;
        }
        .stat-card:hover { background: rgba(255,255,255,0.15); transform: translateY(-3px); }
        .stat-number { font-size: 2rem; font-weight: 800; color: white; line-height: 1; }
        .stat-label { font-size: 0.8rem; color: #cbd5e1; letter-spacing: 0.5px; }
        
        /* Filter Bar */
        .filter-bar {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .filter-group {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .filter-chip {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 2rem;
            padding: 0.4rem 1.2rem;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.2s;
            cursor: pointer;
            color: #334155;
        }
        .filter-chip.active {
            background: #1e293b;
            color: white;
            border-color: #1e293b;
        }
        .search-box {
            position: relative;
        }
        .search-box input {
            border: 1px solid #e2e8f0;
            border-radius: 2rem;
            padding: 0.5rem 1rem 0.5rem 2.5rem;
            width: 260px;
            background: white;
        }
        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }
        
        /* Timeline Card */
        .timeline-card {
            background: white;
            border-radius: 1.25rem;
            padding: 1.2rem 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.25s ease;
            border-left: 5px solid;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }
        .timeline-card:hover {
            transform: translateX(5px);
            box-shadow: 0 10px 20px -8px rgba(0,0,0,0.1);
        }
        .badge-style {
            background: #eef2ff;
            color: #1e40af;
            border-radius: 2rem;
            padding: 0.2rem 0.8rem;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .badge-type {
            border-radius: 2rem;
            padding: 0.2rem 0.8rem;
            font-size: 0.7rem;
            font-weight: 500;
        }
        .score-badge {
            background: #f1f5f9;
            border-radius: 2rem;
            padding: 0.3rem 1rem;
            font-weight: 700;
            font-size: 0.9rem;
        }
        .delete-btn {
            background: none;
            border: none;
            color: #94a3b8;
            transition: 0.2s;
        }
        .delete-btn:hover { color: #dc2626; transform: scale(1.1); }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 1.5rem;
        }
        @media (max-width: 768px) {
            .filter-bar { flex-direction: column; align-items: stretch; }
            .search-box input { width: 100%; }
            .timeline-card .row > div { margin-bottom: 0.5rem; }
        }
    </style>
</head>
<body>

<!-- Navbar sama persis seperti pembelajaran.php (dengan menu lengkap dan active pada Riwayat) -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fas fa-brain me-2"></i>E-Learning AI</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-home me-1"></i> Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="kuesioner.php"><i class="fas fa-clipboard-list me-1"></i> Kuesioner</a></li>
                <li class="nav-item"><a class="nav-link active" href="riwayat.php"><i class="fas fa-history me-1"></i> Riwayat</a></li>
                <li class="nav-item"><a class="nav-link" href="admin.php"><i class="fas fa-cogs me-1"></i> Admin</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-1"></i> Logout</a></li>
            </ul>
            <span class="navbar-text ms-3"></i><?= htmlspecialchars($nama) ?></span>
        </div>
    </div>
</nav>

<div class="container py-4">
    <!-- Hero + Statistik -->
    <div class="hero-stats">
        <div class="row align-items-center">
            <div class="col-md-7 mb-3 mb-md-0">
                <h2 class="text-white fw-bold mb-2"><i class="fas fa-history me-2"></i>Riwayat Belajar</h2>
                <p class="text-white-50 mb-0">Setiap langkah belajarmu tercatat di sini</p>
            </div>
            <div class="col-md-5">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="stat-card">
                            <div class="stat-number"><?= $total_activities ?></div>
                            <div class="stat-label">Total Aktivitas</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card">
                            <div class="stat-number"><?= $avg_score ?></div>
                            <div class="stat-label">Rata-rata Skor</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="filter-bar">
        <div class="filter-group">
            <button class="filter-chip active" data-filter="all">Semua</button>
            <button class="filter-chip" data-filter="video">📹 Video</button>
            <button class="filter-chip" data-filter="teks">📖 Teks</button>
            <button class="filter-chip" data-filter="praktik">✋ Praktik</button>
            <button class="filter-chip" data-filter="quiz">🧠 Quiz AI</button>
        </div>
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Cari aktivitas...">
        </div>
    </div>

    <!-- Daftar Riwayat (Timeline) -->
    <div id="riwayatContainer">
        <?php if ($total_activities > 0): ?>
            <?php foreach ($logs as $log): 
                $type = $log['type'] ?? 'quiz';
                $borderColors = ['video' => '#3b82f6', 'teks' => '#10b981', 'praktik' => '#f59e0b', 'quiz' => '#8b5cf6'];
                $borderColor = $borderColors[$type] ?? '#64748b';
                $typeLabel = ucfirst($type);
                $styleBefore = strtoupper($log['style_before'] ?? 'Umum');
            ?>
            <div class="timeline-card" data-id="<?= $log['id'] ?>" data-type="<?= $type ?>" style="border-left-color: <?= $borderColor ?>;">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <span class="badge-style"><?= $styleBefore ?></span>
                            <span class="badge-type" style="background: <?= $borderColor ?>20; color: <?= $borderColor ?>;"><?= $typeLabel ?></span>
                        </div>
                        <h5 class="fw-semibold mb-1"><?= htmlspecialchars($log['title'] ?? 'Kuis AI') ?></h5>
                        <p class="text-muted small mb-0">
                            <i class="far fa-calendar-alt me-1"></i> <?= date('d M Y, H:i', strtotime($log['created_at'])) ?>
                        </p>
                        <?php if (!empty($log['summary'])): ?>
                            <p class="text-secondary small mt-2 mb-0"><i class="fas fa-pen me-1"></i> <?= htmlspecialchars(substr($log['summary'], 0, 80)) ?>...</p>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-3 text-md-center mt-2 mt-md-0">
                        <div class="score-badge d-inline-block">
                            🎯 Skor: <strong><?= (int)$log['score_performance'] ?></strong>
                        </div>
                    </div>
                    <div class="col-md-2 text-md-end mt-2 mt-md-0">
                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                            <?php if ($type === 'quiz'): ?>
                            <button class="btn btn-sm btn-outline-primary" onclick="viewQuizDetail(<?= $log['id'] ?>, '<?= htmlspecialchars($log['summary'] ?? '') ?>')" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </button>
                            <?php endif; ?>
                            <button class="delete-btn" onclick="deleteRecord(<?= $log['id'] ?>, this)" title="Hapus">
                                <i class="fas fa-trash-alt fa-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-history fa-4x text-muted mb-3"></i>
                <h5>Belum ada riwayat aktivitas</h5>
                <p class="text-muted">Ikuti kuesioner dan kerjakan aktivitas untuk mencatat perkembanganmu.</p>
                <a href="kuesioner.php" class="btn btn-primary rounded-pill px-4">Mulai Kuesioner</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Modal untuk Detail Quiz -->
<div class="modal fade" id="quizDetailModal" tabindex="-1" aria-labelledby="quizDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; border: none;">
                <h5 class="modal-title" id="quizDetailLabel"><i class="fas fa-bar-chart"></i> Detail Quiz AI</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="quizDetailContent">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function viewQuizDetail(activityId, summary) {
        const modal = new bootstrap.Modal(document.getElementById('quizDetailModal'));
        const contentDiv = document.getElementById('quizDetailContent');
        
        // Parse summary untuk ditampilkan
        let detailHTML = '<div class="quiz-detail-content">';
        
        try {
            // Summary format: "Topik: ... | Skor: ... "
            const parts = summary.split('|').map(p => p.trim());
            
            detailHTML += '<div class="mb-3">';
            parts.forEach(part => {
                if (part.includes('Topik:')) {
                    const topik = part.replace('Topik:', '').trim();
                    detailHTML += `<p><strong>📚 Topik:</strong> ${topik}</p>`;
                } else if (part.includes('Skor:')) {
                    const skor = part.replace('Skor:', '').trim();
                    detailHTML += `<p><strong>🎯 Skor:</strong> <span style="font-size: 1.2rem; color: #667eea; font-weight: bold;">${skor}</span></p>`;
                }
            });
            detailHTML += '</div>';
            
            detailHTML += `<hr>
            <div class="alert alert-info" style="background: #eff6ff; border-color: #bfdbfe; color: #1e40af;">
                <i class="fas fa-info-circle"></i> Detail jawaban telah disimpan di database. Silakan kerjakan quiz lagi untuk melihat perbandingan hasil.
            </div>
            <div class="mt-3">
                <button class="btn btn-primary w-100" onclick="location.href='kuesioner.php'">
                    <i class="fas fa-sync"></i> Kerjakan Quiz Lagi
                </button>
            </div>`;
            
        } catch (e) {
            detailHTML += `<p class="text-muted">${summary}</p>`;
        }
        
        detailHTML += '</div>';
        contentDiv.innerHTML = detailHTML;
        modal.show();
    }

    function deleteRecord(id, btnElement) {
        Swal.fire({
            title: 'Hapus riwayat ini?',
            text: "Data tidak dapat dikembalikan setelah dihapus.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('hapus_riwayat.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'id=' + id
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Terhapus!', 'Riwayat berhasil dihapus.', 'success');
                        const card = btnElement.closest('.timeline-card');
                        card.remove();
                        // Update statistik
                        const remaining = document.querySelectorAll('.timeline-card').length;
                        const totalSpan = document.querySelector('.stat-number');
                        if (totalSpan) totalSpan.innerText = remaining;
                        if (remaining === 0) {
                            document.getElementById('riwayatContainer').innerHTML = `
                                <div class="empty-state">
                                    <i class="fas fa-history fa-4x text-muted mb-3"></i>
                                    <h5>Belum ada riwayat aktivitas</h5>
                                    <p class="text-muted">Ikuti kuesioner dan kerjakan aktivitas untuk mencatat perkembanganmu.</p>
                                    <a href="kuesioner.php" class="btn btn-primary rounded-pill px-4">Mulai Kuesioner</a>
                                </div>
                            `;
                        }
                    } else {
                        Swal.fire('Gagal!', data.message || 'Terjadi kesalahan.', 'error');
                    }
                })
                .catch(() => Swal.fire('Error!', 'Gagal menghubungi server.', 'error'));
            }
        });
    }

    // Filter dan Pencarian
    const filterChips = document.querySelectorAll('.filter-chip');
    const searchInput = document.getElementById('searchInput');
    
    function filterItems() {
        const activeFilter = document.querySelector('.filter-chip.active')?.getAttribute('data-filter') || 'all';
        const keyword = searchInput.value.toLowerCase();
        const cards = document.querySelectorAll('.timeline-card');
        cards.forEach(card => {
            const type = card.getAttribute('data-type');
            const title = card.querySelector('h5')?.innerText.toLowerCase() || '';
            const typeMatch = (activeFilter === 'all' || type === activeFilter);
            const searchMatch = keyword === '' || title.includes(keyword);
            card.style.display = (typeMatch && searchMatch) ? '' : 'none';
        });
    }
    
    filterChips.forEach(chip => {
        chip.addEventListener('click', () => {
            filterChips.forEach(c => c.classList.remove('active'));
            chip.classList.add('active');
            filterItems();
        });
    });
    searchInput.addEventListener('keyup', filterItems);
    filterItems();
</script>
</body>
</html>