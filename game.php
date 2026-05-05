<?php
require_once 'config.php';
redirectIfNotLoggedIn();
$user_id = $_SESSION['user_id'];
$nama = $_SESSION['nama'];

// Ambil data game dari database
$stmt = $pdo->query("SELECT * FROM games ORDER BY category, title");
$games = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game & Simulasi - E-Learning AI</title>
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
        .hero-section {
            text-align: center;
            margin: 2rem 0 1.5rem;
        }
        .hero-section h1 {
            font-weight: 800;
            background: linear-gradient(135deg, #1e293b, #10b981);
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
            border-color: #10b981;
            box-shadow: 0 0 0 2px rgba(16,185,129,0.2);
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
            background: #10b981;
            border: none;
            border-radius: 2rem;
            padding: 0.5rem 1.2rem;
            color: white;
            font-weight: 500;
            transition: 0.2s;
        }
        .search-box button:hover { background: #059669; }
        .result-count {
            text-align: center;
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 1.5rem;
        }
        /* Area iframe */
        .game-iframe-container {
            background: white;
            border-radius: 1.25rem;
            border: 1px solid #e2e8f0;
            margin-bottom: 2rem;
            overflow: hidden;
            box-shadow: 0 4px 6px -2px rgba(0,0,0,0.05);
            transition: all 0.2s;
        }
        .iframe-header {
            background: #f8fafc;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
            color: #0f172a;
            position: relative;
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
        .iframe-header .btn-iframe {
            background: none;
            border: none;
            font-size: 1rem;
            color: #64748b;
            cursor: pointer;
            transition: 0.2s;
            padding: 0 0.4rem;
            line-height: 1;
        }
        .iframe-header .btn-iframe:hover {
            color: #10b981;
            transform: scale(1.05);
        }
        .iframe-header .btn-close:hover {
            color: #ef4444;
        }
        .iframe-wrapper {
            width: 100%;
            height: 400px;
            background: #f1f5f9;
        }
        .iframe-wrapper iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        /* Fullscreen mode styling */
        .game-iframe-container:-webkit-full-screen {
            width: 100%;
            height: 100%;
            background: white;
            border-radius: 0;
        }
        .game-iframe-container:fullscreen {
            width: 100%;
            height: 100%;
            background: white;
            border-radius: 0;
        }
        .game-iframe-container:-webkit-full-screen .iframe-wrapper {
            height: calc(100% - 52px);
        }
        .game-iframe-container:fullscreen .iframe-wrapper {
            height: calc(100% - 52px);
        }
        /* Game Card */
        .game-card {
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
        .game-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 25px -12px rgba(0,0,0,0.1);
        }
        .game-thumb {
            width: 100%;
            height: 160px;
            background-size: cover;
            background-position: center;
            position: relative;
            background-color: #f1f5f9;
        }
        .game-thumb .overlay-play {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.6);
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
            cursor: pointer;
            opacity: 0;
        }
        .game-card:hover .overlay-play {
            opacity: 1;
        }
        .game-thumb .overlay-play i {
            color: white;
            font-size: 1.2rem;
            margin-left: 3px;
        }
        .game-thumb:hover .overlay-play {
            background: #10b981;
            transform: translate(-50%, -50%) scale(1.05);
        }
        .game-content {
            padding: 1rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .game-category {
            font-size: 0.7rem;
            font-weight: 600;
            color: #10b981;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }
        .game-title {
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }
        .game-desc {
            font-size: 0.8rem;
            color: #475569;
            margin-bottom: 0.8rem;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
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
            padding: 0.4rem 0.7rem;
            font-size: 0.8rem;
            font-weight: 500;
            transition: all 0.2s;
            color: #1e293b;
            width: 100%;
            text-align: center;
            display: inline-block;
            text-decoration: none;
            margin-top: auto;
            cursor: pointer;
        }
        .btn-play:hover {
            background: #10b981;
            color: white;
            border-color: #10b981;
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
            .game-thumb { height: 140px; }
            .iframe-wrapper { height: 300px; }
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
                <li class="nav-item"><a class="nav-link active" href="game.php"><i class="fas fa-gamepad me-1"></i> Game</a></li>
                <li class="nav-item"><a class="nav-link" href="riwayat.php"><i class="fas fa-history me-1"></i> Riwayat</a></li> 
                <li class="nav-item"><a class="nav-link" href="admin.php"><i class="fas fa-cogs me-1"></i> Admin</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-1"></i> Logout</a></li>
            </ul>
            <span class="navbar-text ms-3"><?= htmlspecialchars($nama) ?></span>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="hero-section">
        <h1><i class="fas fa-gamepad me-2"></i> Game & Simulasi Interaktif</h1>
        <p>Klik tombol "Main Sekarang" pada game pilihanmu, mainkan langsung di sini!</p>
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

    <!-- Area iframe dengan tombol fullscreen dan close -->
    <div id="gameIframeContainer" class="game-iframe-container" style="display: none;">
        <div class="iframe-header">
            <div class="title-section">
                <i class="fas fa-gamepad me-2"></i> <span id="iframeGameTitle">Game</span>
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
            <iframe id="gameFrame" src="about:blank" allowfullscreen sandbox="allow-same-origin allow-scripts allow-popups allow-forms allow-modals"></iframe>
        </div>
    </div>

    <!-- Grid Game -->
    <div class="row g-4" id="gameGrid">
        <?php if (count($games) > 0): ?>
            <?php foreach ($games as $game): 
                $tags = explode(',', $game['tags'] ?? '');
                $thumbnail = !empty($game['thumbnail']) ? $game['thumbnail'] : "https://placehold.co/400x225/1e293b/white?text=" . urlencode(substr($game['title'], 0, 30));
            ?>
            <div class="col-md-6 col-lg-4 game-item" data-title="<?= htmlspecialchars($game['title']) ?>" data-category="<?= htmlspecialchars($game['category']) ?>" data-tags="<?= htmlspecialchars($game['tags']) ?>" data-url="<?= htmlspecialchars($game['game_url']) ?>" data-game-title="<?= htmlspecialchars($game['title']) ?>">
                <div class="game-card">
                    <div class="game-thumb" style="background-image: url('<?= $thumbnail ?>');">
                        <div class="overlay-play" data-url="<?= htmlspecialchars($game['game_url']) ?>" data-title="<?= htmlspecialchars($game['title']) ?>">
                            <i class="fas fa-play"></i>
                        </div>
                    </div>
                    <div class="game-content">
                        <div class="game-category"><?= htmlspecialchars($game['category']) ?></div>
                        <div class="game-title"><?= htmlspecialchars($game['title']) ?></div>
                        <div class="game-desc"><?= htmlspecialchars(substr($game['description'], 0, 100)) ?>...</div>
                        <?php if (!empty($tags)): ?>
                        <div class="tag-list">
                            <?php foreach ($tags as $tag): ?>
                                <?php if (trim($tag) !== ''): ?>
                                <span class="tag"><?= htmlspecialchars(trim($tag)) ?></span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        <button class="btn-play" data-url="<?= htmlspecialchars($game['game_url']) ?>" data-title="<?= htmlspecialchars($game['title']) ?>">
                            <i class="fas fa-play me-1"></i> Main Sekarang
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted">Belum ada game. Silakan tambahkan melalui database.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<footer>
    &copy; <?= date('Y') ?> E-Learning AI | Platform adaptif berbasis gaya belajar modern
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function() {
        const iframeContainer = document.getElementById('gameIframeContainer');
        const gameFrame = document.getElementById('gameFrame');
        const iframeGameTitle = document.getElementById('iframeGameTitle');
        const closeBtn = document.getElementById('closeIframeBtn');
        const fullscreenBtn = document.getElementById('fullscreenIframeBtn');

        // Fungsi memuat game ke iframe
        function loadGameInIframe(url, title) {
            iframeGameTitle.innerText = title;
            gameFrame.src = url;
            iframeContainer.style.display = 'block';
            iframeContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        // Tombol close: sembunyikan iframe dan reset src
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                iframeContainer.style.display = 'none';
                gameFrame.src = 'about:blank';
                iframeGameTitle.innerText = 'Game';
                // Jika dalam mode fullscreen, keluar dulu
                if (document.fullscreenElement) {
                    document.exitFullscreen();
                }
            });
        }

        // Tombol fullscreen untuk iframe container
        if (fullscreenBtn) {
            fullscreenBtn.addEventListener('click', function() {
                if (!document.fullscreenElement) {
                    if (iframeContainer.requestFullscreen) {
                        iframeContainer.requestFullscreen();
                    } else if (iframeContainer.webkitRequestFullscreen) { /* Safari */
                        iframeContainer.webkitRequestFullscreen();
                    } else if (iframeContainer.msRequestFullscreen) { /* IE/Edge */
                        iframeContainer.msRequestFullscreen();
                    }
                    // Ubah ikon menjadi compress
                    fullscreenBtn.innerHTML = '<i class="fas fa-compress"></i>';
                } else {
                    document.exitFullscreen();
                    fullscreenBtn.innerHTML = '<i class="fas fa-expand"></i>';
                }
            });
            // Event listener untuk perubahan fullscreen (misal ESC)
            document.addEventListener('fullscreenchange', function() {
                if (!document.fullscreenElement) {
                    fullscreenBtn.innerHTML = '<i class="fas fa-expand"></i>';
                } else {
                    fullscreenBtn.innerHTML = '<i class="fas fa-compress"></i>';
                }
            });
        }

        // Event listener untuk tombol main dan overlay
        function attachEvents() {
            document.querySelectorAll('.btn-play').forEach(btn => {
                btn.removeEventListener('click', handlePlayClick);
                btn.addEventListener('click', handlePlayClick);
            });
            document.querySelectorAll('.overlay-play').forEach(overlay => {
                overlay.removeEventListener('click', handleOverlayClick);
                overlay.addEventListener('click', handleOverlayClick);
            });
        }

        function handlePlayClick(e) {
            e.preventDefault();
            e.stopPropagation();
            const url = this.getAttribute('data-url');
            const title = this.getAttribute('data-title');
            if (url) loadGameInIframe(url, title);
        }

        function handleOverlayClick(e) {
            e.preventDefault();
            e.stopPropagation();
            const url = this.getAttribute('data-url');
            const title = this.getAttribute('data-title');
            if (url) loadGameInIframe(url, title);
        }

        // Filter pencarian
        const searchInput = document.getElementById('searchInput');
        const searchBtn = document.getElementById('searchBtn');
        const items = document.querySelectorAll('.game-item');
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
                resultCountSpan.innerText = `Menampilkan ${visibleCount} dari ${items.length} game`;
            }
        }

        if (searchInput) {
            searchInput.addEventListener('input', filterSearch);
            searchInput.addEventListener('keyup', (e) => { if (e.key === 'Enter') filterSearch(); });
        }
        if (searchBtn) searchBtn.addEventListener('click', filterSearch);
        filterSearch();
        attachEvents();
    })();
</script>
</body>
</html>