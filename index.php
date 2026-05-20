<?php 
require_once 'db.php';
require_once 'Auth.php';

$auth = new Auth($conn);
if (!$auth->checkLogin()) {
    header("Location: login.php");
    exit;
}

// Ambil username dari session (sesuaikan key-nya jika berbeda)
$username = $_SESSION['username'] ?? $_SESSION['nama'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GARASI NGAPAK</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=Barlow+Condensed:ital,wght@0,700;0,800;1,800&display=swap" rel="stylesheet">

    <style>

        :root {
            --red:      #ff003c;
            --red-dark: #990000;
            --red-glow: rgba(255, 0, 60, 0.3);
            --bg:       #0a0a0a;
            --bg2:      #111111;
            --border:   #2a2a2a;
            --dim:      #aaaaaa;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background-color: var(--bg);
            color: #e0e0e0;
            font-family: 'Rajdhani', sans-serif;
            background-image: radial-gradient(#1e1e1e 1px, transparent 1px);
            background-size: 22px 22px;
        }

        /* ═══════════════════════════
           NAVBAR
        ═══════════════════════════ */
        .top-navbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(10, 10, 10, 0.96);
            border-bottom: 2px solid var(--red);
            padding: 0 48px;
            height: 66px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(8px);
            box-shadow: 0 4px 24px rgba(255, 0, 60, 0.12);
        }

        /* Logo */
        .brand {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 1.75rem;
            font-weight: 800;
            font-style: italic;
            color: #fff;
            letter-spacing: 3px;
            text-transform: uppercase;
            text-decoration: none;
            flex-shrink: 0;
        }

        .brand span { color: var(--red); }

        /* Nav links tengah */
        .nav-categories {
            display: flex;
            gap: 28px;
            align-items: center;
        }

        .nav-categories a {
            color: var(--dim);
            text-decoration: none;
            font-size: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            transition: color 0.25s;
            position: relative;
        }

        .nav-categories a::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--red);
            transition: width 0.3s;
        }

        .nav-categories a:hover,
        .nav-categories a.active {
            color: #fff;
        }

        .nav-categories a:hover::after,
        .nav-categories a.active::after {
            width: 100%;
        }

        /* Nav kanan: user + logout */
        .nav-right {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-shrink: 0;
        }

        .user-chip {
            display: flex;
            align-items: center;
            gap: 9px;
            background: #1a1a1a;
            border: 1px solid var(--border);
            padding: 6px 16px 6px 10px;
            border-radius: 100px;
        }

        .user-chip .avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--red), #660020);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            color: #fff;
            flex-shrink: 0;
        }

        .user-chip .uname {
            font-size: 0.9rem;
            font-weight: 700;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-logout-nav {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 20px;
            border: 1.5px solid var(--red);
            background: transparent;
            color: var(--red);
            font-family: 'Rajdhani', sans-serif;
            font-weight: 700;
            font-size: 0.88rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            text-decoration: none;
            transform: skewX(-12deg);
            transition: 0.3s;
        }

        .btn-logout-nav span { display: inline-block; transform: skewX(12deg); }

        .btn-logout-nav:hover {
            background: var(--red);
            color: #fff;
            box-shadow: 0 0 18px var(--red-glow);
        }

        /* ═══════════════════════════
           HERO BANNER (gambar tengah)
        ═══════════════════════════ */
        .hero-wrap {
            padding: 36px 48px 0;
        }

        .hero-banner {
            position: relative;
            width: 100%;
            height: 400px;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #333;
            border-top: 4px solid var(--red);
            box-shadow: 0 16px 48px rgba(255, 0, 60, 0.18);
        }

        .hero-banner .hero-bg {
            position: absolute;
            inset: 0;
            background-image: url('https://images.unsplash.com/photo-1603584173870-7f23fdae1b7a?q=80&w=2069&auto=format&fit=crop');
            background-size: cover;
            background-position: center 40%;
            transform: scale(1.04);
            animation: slowZoom 16s ease-in-out infinite alternate;
        }

        @keyframes slowZoom {
            from { transform: scale(1.04); }
            to   { transform: scale(1.10); }
        }

        .hero-banner .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                90deg,
                rgba(0,0,0,0.82) 0%,
                rgba(0,0,0,0.45) 55%,
                rgba(0,0,0,0.10) 100%
            );
        }

        .hero-banner .hero-deco {
            position: absolute;
            top: 0; right: 0;
            width: 40%;
            height: 100%;
            background: linear-gradient(135deg, transparent 55%, rgba(255,0,60,0.06) 100%);
            pointer-events: none;
        }

        .hero-content {
            position: absolute;
            bottom: 36px;
            left: 40px;
            z-index: 2;
        }

        .hero-tag {
            display: inline-block;
            padding: 3px 14px;
            border: 1px solid var(--red);
            color: var(--red);
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .hero-content h1 {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: clamp(2.2rem, 5vw, 4rem);
            font-weight: 800;
            font-style: italic;
            text-transform: uppercase;
            line-height: 0.95;
            color: #fff;
            text-shadow: 0 4px 20px rgba(0,0,0,0.7);
            margin-bottom: 12px;
        }

        .hero-content h1 span { color: var(--red); }

        .hero-content p {
            color: #cccccc;
            font-size: 1.05rem;
            font-weight: 500;
            letter-spacing: 0.5px;
            max-width: 400px;
        }

        /* Badge pojok kanan hero */
        .hero-badge-corner {
            position: absolute;
            top: 28px;
            right: 36px;
            z-index: 2;
            text-align: right;
        }

        .hero-badge-corner .big-num {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 3.8rem;
            font-weight: 800;
            font-style: italic;
            color: var(--red);
            line-height: 1;
            text-shadow: 0 0 30px rgba(255,0,60,0.5);
        }

        .hero-badge-corner .sub {
            font-size: 0.72rem;
            color: rgba(255,255,255,0.45);
            letter-spacing: 3px;
            text-transform: uppercase;
        }

        /* ═══════════════════════════
           CONTENT SECTION
        ═══════════════════════════ */
        .content-section {
            padding: 36px 48px 60px;
        }

        .section-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border);
        }

        .section-title {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 1.8rem;
            font-weight: 800;
            font-style: italic;
            text-transform: uppercase;
            color: #fff;
            letter-spacing: 2px;
        }

        .section-title span { color: var(--red); }

        /* Tombol tambah */
        .btn-tambah {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 26px;
            background: linear-gradient(90deg, var(--red), var(--red-dark));
            color: #fff;
            font-family: 'Rajdhani', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            text-decoration: none;
            border: none;
            transform: skewX(-12deg);
            transition: 0.3s;
        }

        .btn-tambah span { display: inline-block; transform: skewX(12deg); }

        .btn-tambah:hover {
            box-shadow: 0 6px 24px var(--red-glow);
            transform: skewX(-12deg) scale(1.03);
            color: #fff;
        }

        /* ═══════════════════════════
           PRODUCT CARDS
        ═══════════════════════════ */
        .card {
            background-color: #131313;
            border: 1px solid var(--border);
            border-top: 3px solid transparent;
            border-radius: 6px;
            transition: 0.35s;
            height: 100%;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-6px);
            border-color: var(--red);
            border-top-color: var(--red);
            box-shadow: 0 12px 36px rgba(255, 0, 60, 0.18);
        }

        .card-img-wrap { overflow: hidden; }

        .card-img-top {
            height: 200px;
            object-fit: cover;
            filter: brightness(0.9) contrast(1.05);
            transition: transform 0.5s ease, filter 0.5s ease;
            width: 100%;
        }

        .card:hover .card-img-top {
            transform: scale(1.06);
            filter: brightness(1) contrast(1.05);
        }

        .card-body { padding: 16px; }

        .card-title {
            color: #fff;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 1rem;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .harga-neon {
            color: #00ffcc;
            font-weight: 700;
            font-size: 1.15rem;
            font-family: 'Barlow Condensed', sans-serif;
            letter-spacing: 1px;
        }

        .card-text {
            color: #777;
            font-size: 0.88rem;
            line-height: 1.5;
        }

        .card-footer {
            background: transparent;
            border-top: 1px solid var(--border);
            padding: 10px 16px;
            display: flex;
            gap: 8px;
        }

        .btn-edit {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 7px 12px;
            border: 1.5px solid #f0a500;
            background: transparent;
            color: #f0a500;
            font-family: 'Rajdhani', sans-serif;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: none;
            transition: 0.25s;
            border-radius: 3px;
        }

        .btn-edit:hover {
            background: #f0a500;
            color: #000;
        }

        .btn-hapus {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 7px 12px;
            border: 1.5px solid var(--red);
            background: transparent;
            color: var(--red);
            font-family: 'Rajdhani', sans-serif;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: none;
            transition: 0.25s;
            border-radius: 3px;
        }

        .btn-hapus:hover {
            background: var(--red);
            color: #fff;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: var(--dim);
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--border);
            margin-bottom: 16px;
            display: block;
        }

        .empty-state p {
            font-size: 1.1rem;
            letter-spacing: 1px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .top-navbar { padding: 0 20px; }
            .nav-categories { display: none; }
            .hero-wrap, .content-section { padding-left: 20px; padding-right: 20px; }
            .hero-banner { height: 260px; }
            .hero-content h1 { font-size: 2rem; }
            .hero-badge-corner .big-num { font-size: 2.5rem; }
        }

    </style>
</head>
<body>

<!-- ═══════════════════════════════════════
     NAVBAR
═══════════════════════════════════════ -->
<nav class="top-navbar">

    <!-- Logo -->
    <a href="index.php" class="brand">GARASI <span>NGAPAK</span></a>

    <!-- Kategori navigasi tengah -->
    <div class="nav-categories">
        <a href="index.php" class="active">
            <i class="bi bi-grid-fill"></i> Semua
        </a>
        <a href="#">Mesin</a>
        <a href="#">Ban & Velg</a>
        <a href="#">Oli</a>
        <a href="#">Aksesoris</a>
    </div>

    <!-- Kanan: info user + tombol logout -->
    <div class="nav-right">

        <div class="user-chip">
            <div class="avatar">
                <i class="bi bi-person-fill"></i>
            </div>
            <span class="uname"><?= htmlspecialchars($username) ?></span>
        </div>

        <a href="logout.php"
           class="btn-logout-nav"
           onclick="return confirm('Apakah Anda yakin ingin Logout?')">
            <span><i class="bi bi-box-arrow-right"></i> Logout</span>
        </a>

    </div>

</nav>

<!-- ═══════════════════════════════════════
     HERO BANNER — GAMBAR DI TENGAH
═══════════════════════════════════════ -->
<div class="hero-wrap">
    <div class="hero-banner">

        <div class="hero-bg"></div>
        <div class="hero-overlay"></div>
        <div class="hero-deco"></div>

        <!-- Teks kiri bawah -->
        <div class="hero-content">
            <div class="hero-tag">
                <i class="bi bi-shield-check-fill"></i>&nbsp; Kualitas Terjamin
            </div>
            <h1>PERFORMA <span>MAKSIMAL</span></h1>
            <p>Upgrade kendaraanmu dengan sparepart racing kualitas terbaik.</p>
        </div>

        <!-- Jumlah produk pojok kanan atas -->
        <div class="hero-badge-corner">
            <div class="big-num">
                <?php
                    $count_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM spareparts");
                    $count_row = mysqli_fetch_assoc($count_res);
                    echo $count_row['total'];
                ?>
            </div>
            <div class="sub">Produk Tersedia</div>
        </div>

    </div>
</div>

<!-- ═══════════════════════════════════════
     DAFTAR PRODUK
═══════════════════════════════════════ -->
<div class="content-section">

    <div class="section-head">
        <div class="section-title">
            Daftar <span>Sparepart</span>
        </div>

        <a href="tambah.php" class="btn-tambah">
            <span><i class="bi bi-plus-lg"></i> Tambah Sparepart</span>
        </a>
    </div>

    <div class="row g-4">
        <?php
            $sql    = "SELECT * FROM spareparts ORDER BY id DESC";
            $result = mysqli_query($conn, $sql);
            $total  = mysqli_num_rows($result);

            if ($total === 0):
        ?>
            <div class="col-12">
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <p>Belum ada sparepart. Mulai tambahkan sekarang!</p>
                </div>
            </div>
        <?php
            else:
            while ($row = mysqli_fetch_assoc($result)):
        ?>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card">
                <div class="card-img-wrap">
                    <img
                        src="uploads/<?= htmlspecialchars($row['gambar']) ?>"
                        class="card-img-top"
                        alt="<?= htmlspecialchars($row['nama_barang']) ?>"
                        onerror="this.src='https://placehold.co/400x200/111/333?text=No+Image'"
                    >
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($row['nama_barang']) ?></h5>
                    <div class="harga-neon mb-2">
                        Rp <?= number_format($row['harga'], 0, ',', '.') ?>
                    </div>
                    <p class="card-text">
                        <?= htmlspecialchars(substr($row['deskripsi'], 0, 65)) ?>...
                    </p>
                </div>
                <div class="card-footer">
                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn-edit">
                        <i class="bi bi-pencil-fill"></i> Edit
                    </a>
                    <a href="hapus.php?id=<?= $row['id'] ?>"
                       class="btn-hapus"
                       onclick="return confirm('Hapus sparepart ini?')">
                        <i class="bi bi-trash-fill"></i> Hapus
                    </a>
                </div>
            </div>
        </div>
        <?php endwhile; endif; ?>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>