<?php
require_once 'config.php';
redirectIfNotLoggedIn();
$user_id = $_SESSION['user_id'];
$nama = $_SESSION['nama'];

// Ambil data materi dari database
$stmt = $pdo->query("SELECT * FROM materi ORDER BY id ASC");
$materiList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembelajaran - E-Learning AI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: linear-gradient(145deg, #f8fafc 0%, #eef2ff 100%);
            font-family: 'Inter', sans-serif;
            color: #0f172a;
        }
        /* Navbar */
        .navbar {
            background-color: #0f172a !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .navbar-brand {
            font-weight: 600;
            font-size: 1.25rem;
            color: white !important;
            transition: transform 0.2s;
        }
        .navbar-brand:hover { transform: translateY(-1px); }
        .navbar-nav .nav-link {
            color: #9ca3af !important;
            font-weight: 500;
            margin: 0 0.25rem;
            padding: 0.5rem 0.75rem;
            border-radius: 2rem;
            transition: all 0.25s ease;
        }
        .navbar-nav .nav-link:hover {
            color: white !important;
            transform: translateY(-2px);
            background: rgba(255,255,255,0.05);
        }
        .navbar-nav .nav-link.active {
            color: white !important;
            font-weight: 600;
            background: rgba(255,255,255,0.08);
        }
        .navbar-text { color: white !important; }
        /* Hero */
        .hero-section {
            text-align: center;
            margin: 2rem 0 1.5rem;
        }
        .hero-section h1 {
            font-weight: 800;
            background: linear-gradient(135deg, #1e293b, #3b82f6);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .hero-section p { color: #475569; }
        /* Search bar full width */
        .search-wrapper {
            width: 100%;
            margin-bottom: 1.5rem;
        }
        .search-box {
            display: flex;
            align-items: center;
            background: white;
            border-radius: 3rem;
            border: 1px solid #e2e8f0;
            padding: 0.2rem 0.2rem 0.2rem 1.5rem;
            transition: all 0.2s;
            width: 100%;
        }
        .search-box:focus-within {
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59,130,246,0.2);
        }
        .search-box i { color: #94a3b8; font-size: 1.1rem; }
        .search-box input {
            border: none;
            flex: 1;
            padding: 0.8rem 0.8rem;
            font-size: 1rem;
            background: transparent;
            width: 100%;
        }
        .search-box input:focus { outline: none; }
        .search-box button {
            background: #1e293b;
            border: none;
            border-radius: 2rem;
            padding: 0.5rem 1.2rem;
            color: white;
            font-weight: 500;
            transition: 0.2s;
        }
        .search-box button:hover { background: #0f172a; }
        .result-count {
            text-align: center;
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 1.5rem;
        }
        /* Card video modern */
        .video-card {
            background: white;
            border-radius: 1.25rem;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -2px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .video-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 25px -12px rgba(0,0,0,0.1);
        }
        .card-thumb {
            width: 100%;
            height: 160px;
            background-size: cover;
            background-position: center;
            position: relative;
        }
        .card-thumb .overlay-play {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.6);
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
            cursor: pointer;
        }
        .card-thumb .overlay-play i {
            color: white;
            font-size: 1.5rem;
            margin-left: 4px;
        }
        .card-thumb:hover .overlay-play {
            background: #3b82f6;
            transform: translate(-50%, -50%) scale(1.05);
        }
        .card-content {
            padding: 1rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .card-category {
            font-size: 0.7rem;
            font-weight: 600;
            color: #3b82f6;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.3rem;
        }
        .card-title {
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }
        .card-desc {
            font-size: 0.8rem;
            color: #475569;
            margin-bottom: 0.8rem;
            line-height: 1.4;
        }
        .author-tag {
            font-size: 0.7rem;
            color: #64748b;
            margin-bottom: 0.8rem;
        }
        .author-tag i {
            margin-right: 0.2rem;
        }
        .tag-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.4rem;
            margin-bottom: 1rem;
        }
        .tag {
            background: #f1f5f9;
            color: #1e293b;
            font-size: 0.65rem;
            font-weight: 500;
            padding: 0.2rem 0.6rem;
            border-radius: 2rem;
        }
        .btn-play {
            background: transparent;
            border: 1px solid #cbd5e1;
            border-radius: 2rem;
            padding: 0.35rem 0.7rem;
            font-size: 0.8rem;
            font-weight: 500;
            transition: all 0.2s;
            color: #1e293b;
            width: 100%;
            text-align: center;
            display: inline-block;
            text-decoration: none;
            margin-top: auto;
        }
        .btn-play:hover {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }
        footer {
            margin-top: 3rem;
            padding: 1.2rem;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            background: white;
            font-size: 0.75rem;
            color: #64748b;
        }
        @media (max-width: 768px) {
            .hero-section h1 { font-size: 1.8rem; }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fas fa-brain me-2"></i>E-Learning AI</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-home me-1"></i> Beranda</a></li>
                <li class="nav-item"><a class="nav-link active" href="pembelajaran.php"><i class="fas fa-book-open me-1"></i> Pembelajaran</a></li>
                <li class="nav-item"><a class="nav-link" href="riwayat.php"><i class="fas fa-history me-1"></i> Riwayat</a></li>
                <li class="nav-item"><a class="nav-link" href="admin.php"><i class="fas fa-cogs me-1"></i> Admin</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-1"></i> Logout</a></li>
            </ul>
            <span class="navbar-text ms-3"> <?= htmlspecialchars($nama) ?></span>
        </div>
    </div>
</nav>

<main>
    <div class="container py-4">
        <div class="hero-section">
            <h1><i class="fas fa-video me-2"></i> Video Edukasi</h1>
            <p>Pilih materi pembelajaran yang sesuai dengan minat Anda</p>
        </div>

        <!-- Search Bar Full Width -->
        <div class="search-wrapper">
            <div class="search-box">
                <i class="fas fa-search me-2"></i>
                <input type="text" id="searchInput" placeholder="Cari judul, kategori, atau tag...">
                <button id="searchBtn">Cari</button>
            </div>
        </div>

        <!-- Result Count -->
        <div class="result-count" id="resultCount"></div>

        <!-- Grid Video dengan Thumbnail Gambar -->
        <div class="row g-4" id="videoGrid">
            <?php if (count($materiList) > 0): ?>
                <?php foreach ($materiList as $materi): 
                    $tags = array_map('trim', explode(',', $materi['tags']));
                    $deskripsiSingkat = strlen($materi['deskripsi']) > 100 ? substr($materi['deskripsi'], 0, 100) . '...' : $materi['deskripsi'];
                    // Thumbnail: gunakan kolom thumbnail jika ada, atau placeholder
                    $thumbnail = !empty($materi['thumbnail']) ? $materi['thumbnail'] : "https://placehold.co/400x225/1e293b/white?text=" . urlencode(substr($materi['judul'], 0, 30));
                ?>
                <div class="col-md-6 col-lg-4 video-item" data-title="<?= htmlspecialchars($materi['judul']) ?>" data-category="<?= htmlspecialchars($materi['kategori']) ?>" data-tags="<?= htmlspecialchars($materi['tags']) ?>">
                    <div class="video-card">
                        <div class="card-thumb" style="background-image: url('<?= $thumbnail ?>'); background-size: cover; background-position: center;">
                            <div class="overlay-play" data-bs-toggle="modal" data-bs-target="#videoModal" 
                                 data-video="<?= htmlspecialchars($materi['video_url']) ?>" 
                                 data-title="<?= htmlspecialchars($materi['judul']) ?>">
                                <i class="fas fa-play"></i>
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="card-category"><?= htmlspecialchars($materi['kategori']) ?></div>
                            <div class="card-title"><?= htmlspecialchars($materi['judul']) ?></div>
                            <div class="card-desc"><?= htmlspecialchars($deskripsiSingkat) ?></div>
                            <?php if (!empty($materi['author'])): ?>
                                <div class="author-tag"><i class="fas fa-user"></i> <?= htmlspecialchars($materi['author']) ?></div>
                            <?php endif; ?>
                            <div class="tag-list">
                                <?php foreach ($tags as $tag): ?>
                                    <span class="tag"><?= htmlspecialchars($tag) ?></span>
                                <?php endforeach; ?>
                            </div>
                            <button class="btn-play" data-bs-toggle="modal" data-bs-target="#videoModal" 
                                    data-video="<?= htmlspecialchars($materi['video_url']) ?>" 
                                    data-title="<?= htmlspecialchars($materi['judul']) ?>">
                                <i class="fas fa-play me-1"></i> Mulai Belajar
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted">Belum ada materi pembelajaran. Silakan tambahkan melalui database.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<footer>
    &copy; <?= date('Y') ?> E-Learning AI | Platform adaptif berbasis gaya belajar modern
</footer>

<!-- Modal Video -->
<div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold" id="videoModalLabel">Putar Video</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <div class="ratio ratio-16x9">
                    <iframe id="videoIframe" src="" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function() {
        const searchInput = document.getElementById('searchInput');
        const searchBtn = document.getElementById('searchBtn');
        const items = document.querySelectorAll('.video-item');
        const resultCountSpan = document.getElementById('resultCount');

        function filterSearch() {
            const keyword = searchInput ? searchInput.value.trim().toLowerCase() : '';
            let visibleCount = 0;

            items.forEach(item => {
                const title = item.getAttribute('data-title') || '';
                const category = item.getAttribute('data-category') || '';
                const tags = item.getAttribute('data-tags') || '';
                const fullText = (title + ' ' + category + ' ' + tags).toLowerCase();
                const matches = keyword === '' || fullText.includes(keyword);
                if (matches) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            if (resultCountSpan) {
                resultCountSpan.innerText = `Menampilkan ${visibleCount} dari ${items.length} materi`;
            }
        }

        const doSearch = () => filterSearch();
        if (searchInput) {
            searchInput.addEventListener('input', doSearch);
            searchInput.addEventListener('keyup', (e) => { if (e.key === 'Enter') doSearch(); });
        }
        if (searchBtn) searchBtn.addEventListener('click', doSearch);
        filterSearch();

        // Modal video
        const videoModal = document.getElementById('videoModal');
        if (videoModal) {
            videoModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const videoUrl = button.getAttribute('data-video');
                const title = button.getAttribute('data-title');
                const iframe = document.getElementById('videoIframe');
                iframe.src = videoUrl;
                document.getElementById('videoModalLabel').innerText = title;
            });
            videoModal.addEventListener('hidden.bs.modal', function () {
                const iframe = document.getElementById('videoIframe');
                iframe.src = '';
            });
        }
    })();
</script>
</body>
</html>