<?php
require_once 'db.php';

/*
 * ALUR LUPA PASSWORD (tanpa kirim email — cocok untuk proyek lokal)
 * ─────────────────────────────────────────────────────────────────
 * Step 1 : Pengguna masukkan email → dicek ke database
 * Step 2 : Jika email ada → tampilkan form isi password baru
 * Step 3 : Simpan password baru (di-hash dengan password_hash)
 */

$step    = 1;       // step aktif
$error   = "";
$success = "";
$email_valid = "";  // email yang sudah terverifikasi

/* ──────────────────────────────
   STEP 1 : Cek email
────────────────────────────── */
if (isset($_POST['cek_email'])) {

    $email = trim($_POST['email']);

    $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        // Email ditemukan → lanjut ke step 2
        $step        = 2;
        $email_valid = $email;
    } else {
        $error = "Email tidak ditemukan. Periksa kembali emailmu.";
        $step  = 1;
    }
}

/* ──────────────────────────────
   STEP 2 : Reset password baru
────────────────────────────── */
if (isset($_POST['reset_pass'])) {

    $email        = trim($_POST['email_hidden']);
    $new_pass     = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if (strlen($new_pass) < 6) {
        $error = "Password minimal 6 karakter.";
        $step  = 2;
        $email_valid = $email;

    } elseif ($new_pass !== $confirm_pass) {
        $error = "Konfirmasi password tidak cocok.";
        $step  = 2;
        $email_valid = $email;

    } else {
        $hashed = password_hash($new_pass, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "ss", $hashed, $email);

        if (mysqli_stmt_execute($stmt)) {
            $success = "Password berhasil diubah! Silakan login dengan password baru.";
            $step    = 3; // step sukses
        } else {
            $error = "Terjadi kesalahan. Coba lagi.";
            $step  = 2;
            $email_valid = $email;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password — GARASI NGAPAK</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@600;700&display=swap" rel="stylesheet">

    <style>

        *{ margin:0; padding:0; box-sizing:border-box; }

        body{
            background-color: #0a0a0a;
            font-family: 'Rajdhani', sans-serif;
            color: white;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-image:
                linear-gradient(rgba(0,0,0,0.75), rgba(10,0,0,0.82)),
                url('https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?w=1920&q=80&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        /* ── CARD ── */
        .fp-card{
            width: 100%;
            max-width: 480px;
            background: #111;
            border: 1px solid #2a2a2a;
            border-top: 5px solid #ff003c;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(255, 0, 60, 0.22);
            position: relative;
        }

        /* Tombol kembali */
        .btn-back{
            position: absolute;
            top: 18px;
            left: 22px;
            color: #666;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: color 0.25s;
        }

        .btn-back:hover{ color: #ff003c; }

        /* ── STEP INDICATOR ── */
        .step-bar{
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0;
            margin-bottom: 30px;
        }

        .step-dot{
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: 2px solid #333;
            background: #1a1a1a;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 700;
            color: #555;
            transition: 0.3s;
            flex-shrink: 0;
            position: relative;
            z-index: 1;
        }

        .step-dot.done{
            border-color: #ff003c;
            background: #ff003c;
            color: #fff;
        }

        .step-dot.active{
            border-color: #ff003c;
            background: transparent;
            color: #ff003c;
            box-shadow: 0 0 12px rgba(255,0,60,0.4);
        }

        .step-line{
            flex: 1;
            height: 2px;
            background: #2a2a2a;
            max-width: 60px;
        }

        .step-line.done{ background: #ff003c; }

        /* ── TITLE ── */
        .fp-title{
            text-align: center;
            font-weight: 700;
            font-style: italic;
            text-transform: uppercase;
            margin-bottom: 6px;
            font-size: 1.7rem;
            letter-spacing: 1px;
        }

        .fp-title span{ color: #ff003c; }

        .fp-subtitle{
            text-align: center;
            color: #888;
            font-size: 0.92rem;
            margin-bottom: 28px;
            letter-spacing: 0.5px;
        }

        /* ── LABEL ── */
        .form-label{
            color: white;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 8px;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }

        /* ── INPUT GROUP ── */
        .input-group-text{
            background-color: #0a0a0a;
            border: 1px solid #444;
            border-right: none;
            color: #ff003c;
        }

        .form-control{
            background-color: #0a0a0a;
            border: 1px solid #444;
            border-left: none;
            color: #fff !important;
            padding: 12px;
        }

        .form-control::placeholder{ color: #888; opacity: 1; }

        .form-control:focus{
            background-color: #111;
            border-color: #ff003c;
            color: #fff !important;
            box-shadow: 0 0 10px rgba(255,0,60,0.3);
        }

        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus{
            -webkit-text-fill-color: #ffffff;
            -webkit-box-shadow: 0 0 0px 1000px #0a0a0a inset;
            transition: background-color 5000s ease-in-out 0s;
        }

        /* Password strength bar */
        .strength-wrap{ margin-top: 6px; }

        .strength-bar{
            height: 4px;
            border-radius: 4px;
            background: #222;
            overflow: hidden;
            margin-bottom: 3px;
        }

        .strength-fill{
            height: 100%;
            width: 0%;
            border-radius: 4px;
            transition: width 0.3s, background 0.3s;
        }

        .strength-label{
            font-size: 0.75rem;
            color: #666;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* ── BUTTON ── */
        .btn-go{
            width: 100%;
            padding: 14px;
            border: none;
            background: linear-gradient(45deg, #ff003c, #990000);
            transform: skewX(-15deg);
            transition: 0.3s;
            margin-top: 8px;
        }

        .btn-go:hover{ transform: skewX(-15deg) scale(1.02); box-shadow: 0 6px 20px rgba(255,0,60,0.4); }

        .btn-text{
            display: inline-block;
            transform: skewX(15deg);
            color: white;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-size: 1rem;
        }

        /* ── ALERT ── */
        .alert-err{
            background: #3a0000;
            color: #ff7777;
            border: 1px solid #ff003c;
            border-radius: 6px;
            padding: 10px 16px;
            font-size: 0.92rem;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* ── SUCCESS STATE ── */
        .success-box{
            text-align: center;
            padding: 20px 0;
        }

        .success-icon{
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff003c, #660020);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 20px;
            box-shadow: 0 0 30px rgba(255,0,60,0.4);
        }

        .success-title{
            font-size: 1.4rem;
            font-weight: 700;
            font-style: italic;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .success-title span{ color: #ff003c; }

        .success-desc{
            color: #888;
            font-size: 0.95rem;
            margin-bottom: 28px;
        }

        /* Login button on success */
        .btn-login-go{
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 13px 36px;
            background: linear-gradient(45deg, #ff003c, #990000);
            color: #fff;
            font-family: 'Rajdhani', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            text-decoration: none;
            transform: skewX(-15deg);
            transition: 0.3s;
        }

        .btn-login-go span{ display: inline-block; transform: skewX(15deg); }
        .btn-login-go:hover{ color:#fff; box-shadow: 0 6px 20px rgba(255,0,60,0.4); transform: skewX(-15deg) scale(1.02); }

        /* Email badge di step 2 */
        .email-badge{
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,0,60,0.08);
            border: 1px solid rgba(255,0,60,0.25);
            padding: 6px 16px;
            border-radius: 100px;
            color: #ff003c;
            font-size: 0.88rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 22px;
        }

        /* Show/hide password toggle */
        .btn-eye{
            background: #0a0a0a;
            border: 1px solid #444;
            border-left: none;
            color: #555;
            padding: 0 14px;
            cursor: pointer;
            transition: color 0.2s;
        }

        .btn-eye:hover{ color: #ff003c; }

    </style>
</head>
<body>

<div class="fp-card">

    <!-- Tombol kembali ke login -->
    <a href="login.php" class="btn-back">
        <i class="bi bi-arrow-left"></i> Login
    </a>

    <!-- ── STEP INDICATOR ── -->
    <div class="step-bar">
        <div class="step-dot <?= $step >= 1 ? ($step > 1 ? 'done' : 'active') : '' ?>">
            <?= $step > 1 ? '<i class="bi bi-check-lg"></i>' : '1' ?>
        </div>
        <div class="step-line <?= $step > 1 ? 'done' : '' ?>"></div>
        <div class="step-dot <?= $step === 2 ? 'active' : ($step > 2 ? 'done' : '') ?>">
            <?= $step > 2 ? '<i class="bi bi-check-lg"></i>' : '2' ?>
        </div>
        <div class="step-line <?= $step > 2 ? 'done' : '' ?>"></div>
        <div class="step-dot <?= $step === 3 ? 'done' : '' ?>">
            <?= $step === 3 ? '<i class="bi bi-check-lg"></i>' : '3' ?>
        </div>
    </div>

    <?php if ($step === 1): ?>
    <!-- ════════════════════════════
         STEP 1 : Input Email
    ════════════════════════════ -->
        <div class="fp-title">LUPA <span>PASSWORD</span></div>
        <p class="fp-subtitle">Masukkan email akunmu untuk melanjutkan</p>

        <?php if ($error): ?>
            <div class="alert-err"><i class="bi bi-exclamation-triangle-fill"></i> <?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-4">
                <label class="form-label">Email Akun</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        placeholder="Masukkan email terdaftar"
                        required
                        autofocus
                    >
                </div>
            </div>

            <button type="submit" name="cek_email" class="btn btn-go">
                <span class="btn-text"><i class="bi bi-search"></i> Cari Akun</span>
            </button>
        </form>

    <?php elseif ($step === 2): ?>
    <!-- ════════════════════════════
         STEP 2 : Buat Password Baru
    ════════════════════════════ -->
        <div class="fp-title">RESET <span>PASSWORD</span></div>
        <p class="fp-subtitle">Buat password baru untuk akunmu</p>

        <div class="text-center">
            <div class="email-badge">
                <i class="bi bi-person-check-fill"></i>
                <?= htmlspecialchars($email_valid) ?>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert-err"><i class="bi bi-exclamation-triangle-fill"></i> <?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <!-- Email tersembunyi untuk dikirim ke step berikutnya -->
            <input type="hidden" name="email_hidden" value="<?= htmlspecialchars($email_valid) ?>">

            <!-- Password Baru -->
            <div class="mb-3">
                <label class="form-label">Password Baru</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input
                        type="password"
                        name="new_password"
                        id="new_pass"
                        class="form-control"
                        placeholder="Minimal 6 karakter"
                        required
                        oninput="checkStrength(this.value)"
                    >
                    <button type="button" class="btn-eye" onclick="togglePass('new_pass', this)">
                        <i class="bi bi-eye-fill"></i>
                    </button>
                </div>
                <!-- Indikator kekuatan password -->
                <div class="strength-wrap">
                    <div class="strength-bar">
                        <div class="strength-fill" id="strengthFill"></div>
                    </div>
                    <span class="strength-label" id="strengthLabel">—</span>
                </div>
            </div>

            <!-- Konfirmasi Password -->
            <div class="mb-4">
                <label class="form-label">Konfirmasi Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input
                        type="password"
                        name="confirm_password"
                        id="confirm_pass"
                        class="form-control"
                        placeholder="Ulangi password baru"
                        required
                    >
                    <button type="button" class="btn-eye" onclick="togglePass('confirm_pass', this)">
                        <i class="bi bi-eye-fill"></i>
                    </button>
                </div>
            </div>

            <button type="submit" name="reset_pass" class="btn btn-go">
                <span class="btn-text"><i class="bi bi-shield-lock-fill"></i> Simpan Password</span>
            </button>
        </form>

    <?php elseif ($step === 3): ?>
    <!-- ════════════════════════════
         STEP 3 : Sukses
    ════════════════════════════ -->
        <div class="success-box">
            <div class="success-icon">
                <i class="bi bi-check-lg"></i>
            </div>
            <div class="success-title">PASSWORD <span>DIPERBARUI!</span></div>
            <p class="success-desc">
                Password kamu berhasil diubah.<br>
                Silakan login dengan password baru.
            </p>
            <a href="login.php" class="btn-login-go">
                <span><i class="bi bi-box-arrow-in-right"></i> Kembali Login</span>
            </a>
        </div>

    <?php endif; ?>

</div>

<script>
    // Toggle show/hide password
    function togglePass(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon  = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'bi bi-eye-slash-fill';
            btn.style.color = '#ff003c';
        } else {
            input.type = 'password';
            icon.className = 'bi bi-eye-fill';
            btn.style.color = '';
        }
    }

    // Strength meter
    function checkStrength(val) {
        const fill  = document.getElementById('strengthFill');
        const label = document.getElementById('strengthLabel');
        let score = 0;

        if (val.length >= 6)  score++;
        if (val.length >= 10) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        const levels = [
            { pct: '0%',   color: '#333',    text: '—' },
            { pct: '25%',  color: '#ff003c', text: 'Lemah' },
            { pct: '50%',  color: '#ff8800', text: 'Cukup' },
            { pct: '75%',  color: '#ffcc00', text: 'Kuat' },
            { pct: '100%', color: '#00cc66', text: 'Sangat Kuat' },
        ];

        const idx = Math.min(score, 4);
        fill.style.width      = levels[idx].pct;
        fill.style.background = levels[idx].color;
        label.textContent     = levels[idx].text;
        label.style.color     = levels[idx].color;
    }
</script>

</body>
</html>