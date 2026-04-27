<?php
require_once 'config.php';
redirectIfNotLoggedIn();
$user_id = $_SESSION['user_id'];
$nama = $_SESSION['nama'];

// Ambil data dari tabel library
$stmt = $pdo->query("SELECT * FROM library ORDER BY created_at DESC");
$resources = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Edukasi - E-Learning AI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: #f5f7fb;
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
        .hero-library {
            text-align: center;
            margin: 2rem 0 1.5rem;
        }
        .hero-library h1 {
            font-weight: 800;
            background: linear-gradient(135deg, #1e293b, #3b82f6);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        /* Filter & Search */
        .filter-group {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            justify-content: center;
            margin-bottom: 1.5rem;
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
        .filter-chip.active, .filter-chip:hover {
            background: #1e293b;
            color: white;
            border-color: #1e293b;
        }
        /* SEARCH BAR FULL WIDTH */
        .search-wrapper {
            width: 100%;
            margin-bottom: 2rem;
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
        .search-box i {
            color: #94a3b8;
            font-size: 1.1rem;
        }
        .search-box input {
            border: none;
            flex: 1;
            padding: 0.8rem 0.8rem;
            font-size: 1rem;
            background: transparent;
            width: 100%;
        }
        .search-box input:focus {
            outline: none;
        }
        .search-box button {
            background: #1e293b;
            border: none;
            border-radius: 2rem;
            padding: 0.5rem 1.2rem;
            color: white;
            font-weight: 500;
            transition: 0.2s;
        }
        .search-box button:hover {
            background: #0f172a;
        }
        .result-count {
            text-align: center;
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 1.5rem;
        }
        /* Area iframe (muncul setelah klik) */
        .iframe-container {
            background: white;
            border-radius: 1.25rem;
            border: 1px solid #e2e8f0;
            margin-bottom: 2rem;
            overflow: hidden;
            box-shadow: 0 4px 6px -2px rgba(0,0,0,0.05);
        }
        .iframe-header {
            background: #f8fafc;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
            color: #0f172a;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .iframe-header .title-section {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .iframe-header .button-group {
            display: flex;
            gap: 0.5rem;
        }
        .btn-iframe {
            background: none;
            border: none;
            font-size: 1rem;
            color: #64748b;
            cursor: pointer;
            transition: 0.2s;
            padding: 0 0.4rem;
        }
        .btn-iframe:hover {
            color: #3b82f6;
            transform: scale(1.05);
        }
        .btn-close:hover {
            color: #ef4444;
        }
        .iframe-wrapper {
            width: 100%;
            height: 500px;
            background: #f1f5f9;
        }
        .iframe-wrapper iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        /* Fullscreen mode */
        .iframe-container:-webkit-full-screen {
            width: 100%;
            height: 100%;
            background: white;
            border-radius: 0;
        }
        .iframe-container:fullscreen {
            width: 100%;
            height: 100%;
            background: white;
            border-radius: 0;
        }
        .iframe-container:-webkit-full-screen .iframe-wrapper {
            height: calc(100% - 52px);
        }
        .iframe-container:fullscreen .iframe-wrapper {
            height: calc(100% - 52px);
        }
        /* Resource card horizontal (1 kolom) */
        .resource-grid {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .resource-card {
            background: white;
            border-radius: 1.25rem;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -2px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 1rem;
            padding: 0.8rem;
        }
        .resource-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px -12px rgba(0,0,0,0.1);
        }
        .card-thumb {
            background: linear-gradient(145deg, #eef2ff, #ffffff);
            width: 60px;
            height: 60px;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .card-thumb i {
            font-size: 1.8rem;
            color: #3b82f6;
        }
        .card-content {
            flex: 1;
        }
        .resource-category {
            font-size: 0.7rem;
            font-weight: 600;
            color: #3b82f6;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.2rem;
        }
        .resource-title {
            font-weight: 700;
            font-size: 0.95rem;
            margin-bottom: 0.25rem;
            line-height: 1.3;
        }
        .resource-desc {
            font-size: 0.75rem;
            color: #475569;
            margin-bottom: 0.4rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .resource-meta {
            font-size: 0.65rem;
            color: #64748b;
            margin-bottom: 0.4rem;
        }
        .tag-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.3rem;
            margin-bottom: 0.5rem;
        }
        .tag {
            background: #f1f5f9;
            color: #1e293b;
            font-size: 0.6rem;
            font-weight: 500;
            padding: 0.15rem 0.5rem;
            border-radius: 2rem;
        }
        .btn-access {
            background: transparent;
            border: 1px solid #cbd5e1;
            border-radius: 2rem;
            padding: 0.2rem 0.6rem;
            font-size: 0.7rem;
            font-weight: 500;
            transition: all 0.2s;
            color: #1e293b;
            width: auto;
            text-align: center;
            cursor: pointer;
            margin-top: 0.2rem;
        }
        .btn-access:hover {
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
            .resource-card {
                flex-direction: column;
                text-align: center;
            }
            .card-thumb {
                width: 80px;
                height: 80px;
                margin: 0 auto;
            }
            .iframe-wrapper {
                height: 300px;
            }
        }
    </style>
</head>
<body>

<!-- Navbar (sama persis) -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fas fa-brain me-2"></i>E-Learning AI</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-home me-1"></i> Beranda</a></li>
                <li class="nav-item"><a class="nav-link active" href="library.php"><i class="fas fa-book me-1"></i> Library</a></li>    
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
        <div class="hero-library">
            <h1><i class="fas fa-book-open me-2"></i> Library Edukasi</h1>
            <p>Koleksi buku, jurnal, dan artikel untuk mendukung pembelajaran Anda</p>
        </div>

        <!-- Filter Kategori -->
        <div class="filter-group">
            <button class="filter-chip active" data-filter="all">Semua</button>
            <button class="filter-chip" data-filter="Buku">📚 Buku</button>
            <button class="filter-chip" data-filter="Jurnal">📄 Jurnal</button>
            <button class="filter-chip" data-filter="Artikel">📰 Artikel</button>
        </div>

        <!-- Search Bar -->
        <div class="search-wrapper">
            <div class="search-box">
                <i class="fas fa-search me-2"></i>
                <input type="text" id="searchInput" placeholder="Cari judul, kategori, atau tag...">
                <button id="searchBtn">Cari</button>
            </div>
        </div>

        <!-- Result Count -->
        <div class="result-count" id="resultCount"></div>

        <!-- AREA IFRAME (AWALNYA HIDDEN) -->
        <div id="resourceIframeContainer" class="iframe-container" style="display: none;">
            <div class="iframe-header">
                <div class="title-section">
                    <i class="fas fa-file-alt me-2"></i> <span id="iframeResourceTitle">Sumber Belajar</span>
                </div>
                <div class="button-group">
                    <button id="fullscreenIframeBtn" class="btn-iframe" aria-label="Layar penuh">
                        <i class="fas fa-expand"></i>
                    </button>
                    <button id="closeIframeBtn" class="btn-iframe btn-close" aria-label="Tutup">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="iframe-wrapper">
                <iframe id="resourceFrame" src="about:blank" allowfullscreen sandbox="allow-same-origin allow-scripts allow-popups allow-forms allow-modals"></iframe>
            </div>
        </div>

        <!-- Grid Daftar Resource -->
        <div class="resource-grid" id="resourceGrid">
            <?php if (count($resources) > 0): ?>
                <?php foreach ($resources as $item): 
                    $iconMap = ['Buku' => 'book', 'Jurnal' => 'file-alt', 'Artikel' => 'newspaper'];
                    $icon = $iconMap[$item['type']] ?? 'book';
                    $tags = explode(',', $item['tags'] ?? '');
                ?>
                <div class="resource-item" data-type="<?= htmlspecialchars($item['type']) ?>" data-title="<?= htmlspecialchars($item['title']) ?>" data-category="<?= htmlspecialchars($item['category']) ?>" data-tags="<?= htmlspecialchars($item['tags']) ?>" data-url="<?= htmlspecialchars($item['link']) ?>">
                    <div class="resource-card">
                        <div class="card-thumb">
                            <i class="fas fa-<?= $icon ?>"></i>
                        </div>
                        <div class="card-content">
                            <div class="resource-category"><?= htmlspecialchars($item['category']) ?></div>
                            <div class="resource-title"><?= htmlspecialchars($item['title']) ?></div>
                            <div class="resource-desc"><?= htmlspecialchars(substr($item['description'], 0, 100)) ?>...</div>
                            <div class="resource-meta">
                                <i class="fas fa-building me-1"></i> <?= htmlspecialchars($item['source'] ?? '-') ?> • <?= $item['year'] ?? '-' ?>
                            </div>
                            <div class="tag-list">
                                <?php foreach ($tags as $tag): ?>
                                    <span class="tag"><?= htmlspecialchars(trim($tag)) ?></span>
                                <?php endforeach; ?>
                            </div>
                            <button class="btn-access" data-url="<?= htmlspecialchars($item['link']) ?>" data-title="<?= htmlspecialchars($item['title']) ?>">
                                <i class="fas fa-external-link-alt me-1"></i> Akses Sumber
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <p class="text-muted">Belum ada sumber belajar. Silakan tambahkan melalui database.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<footer>
    &copy; <?= date('Y') ?> E-Learning AI | Platform adaptif berbasis gaya belajar modern
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function() {
        const iframeContainer = document.getElementById('resourceIframeContainer');
        const resourceFrame = document.getElementById('resourceFrame');
        const iframeTitle = document.getElementById('iframeResourceTitle');
        const closeBtn = document.getElementById('closeIframeBtn');
        const fullscreenBtn = document.getElementById('fullscreenIframeBtn');

        // Fungsi memuat resource ke iframe dan menampilkan container
        function loadResourceInIframe(url, title) {
            iframeTitle.innerText = title;
            resourceFrame.src = url;
            iframeContainer.style.display = 'block';
            // Scroll ke iframe
            iframeContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // Tombol close
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                iframeContainer.style.display = 'none';
                resourceFrame.src = 'about:blank';
                iframeTitle.innerText = 'Sumber Belajar';
                if (document.fullscreenElement) {
                    document.exitFullscreen();
                }
            });
        }

        // Tombol fullscreen
        if (fullscreenBtn) {
            fullscreenBtn.addEventListener('click', function() {
                if (!document.fullscreenElement) {
                    if (iframeContainer.requestFullscreen) {
                        iframeContainer.requestFullscreen();
                    } else if (iframeContainer.webkitRequestFullscreen) {
                        iframeContainer.webkitRequestFullscreen();
                    } else if (iframeContainer.msRequestFullscreen) {
                        iframeContainer.msRequestFullscreen();
                    }
                    fullscreenBtn.innerHTML = '<i class="fas fa-compress"></i>';
                } else {
                    document.exitFullscreen();
                    fullscreenBtn.innerHTML = '<i class="fas fa-expand"></i>';
                }
            });
            document.addEventListener('fullscreenchange', function() {
                if (!document.fullscreenElement) {
                    fullscreenBtn.innerHTML = '<i class="fas fa-expand"></i>';
                } else {
                    fullscreenBtn.innerHTML = '<i class="fas fa-compress"></i>';
                }
            });
        }

        // Event listener untuk tombol akses
        function attachEvents() {
            document.querySelectorAll('.btn-access').forEach(btn => {
                btn.removeEventListener('click', handleAccessClick);
                btn.addEventListener('click', handleAccessClick);
            });
        }

        function handleAccessClick(e) {
            e.preventDefault();
            const url = this.getAttribute('data-url');
            const title = this.getAttribute('data-title');
            if (url) loadResourceInIframe(url, title);
        }

        // Filter dan search
        const filterChips = document.querySelectorAll('.filter-chip');
        const searchInput = document.getElementById('searchInput');
        const searchBtn = document.getElementById('searchBtn');
        const items = document.querySelectorAll('.resource-item');
        const resultCountSpan = document.getElementById('resultCount');

        function filterAndSearch() {
            const activeFilter = document.querySelector('.filter-chip.active')?.getAttribute('data-filter') || 'all';
            const keyword = searchInput ? searchInput.value.trim().toLowerCase() : '';
            let visibleCount = 0;

            items.forEach(item => {
                const type = item.getAttribute('data-type');
                const title = item.getAttribute('data-title') || '';
                const category = item.getAttribute('data-category') || '';
                const tags = item.getAttribute('data-tags') || '';
                const fullText = (title + ' ' + category + ' ' + tags).toLowerCase();
                const matchesType = (activeFilter === 'all' || type === activeFilter);
                const matchesSearch = keyword === '' || fullText.includes(keyword);
                if (matchesType && matchesSearch) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            if (resultCountSpan) {
                resultCountSpan.innerText = `Menampilkan ${visibleCount} dari ${items.length} sumber belajar`;
            }
        }

        filterChips.forEach(chip => {
            chip.addEventListener('click', () => {
                filterChips.forEach(c => c.classList.remove('active'));
                chip.classList.add('active');
                filterAndSearch();
            });
        });

        const doSearch = () => filterAndSearch();
        if (searchInput) {
            searchInput.addEventListener('input', doSearch);
            searchInput.addEventListener('keyup', (e) => { if (e.key === 'Enter') doSearch(); });
        }
        if (searchBtn) searchBtn.addEventListener('click', doSearch);
        filterAndSearch();
        attachEvents();
    })();
</script>
</body>
</html>