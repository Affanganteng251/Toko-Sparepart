<?php
require_once 'db.php';
require_once 'Auth.php';

$auth = new Auth($conn);

if ($auth->checkLogin()) {
    header("Location: index.php");
    exit;
}

$error = "";
$success = "";

if (isset($_POST['register_btn'])) {

    $user = trim($_POST['username']);
    $email = trim($_POST['email']);
    $pass = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];
    $terms = isset($_POST['terms']);

    if ($pass !== $confirm_pass) {
        $error = "Konfirmasi password tidak cocok.";
    }

    elseif (!$terms) {
        $error = "Anda harus menyetujui Syarat & Ketentuan kami.";
    }

    else {

        $register_attempt = $auth->register($user, $email, $pass);

        if ($register_attempt === true) {
            $success = "Akun berhasil didaftarkan! Silakan Login.";
        } else {
            $error = $register_attempt;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Registrasi</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@600;700&display=swap" rel="stylesheet">

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            background-color:#0a0a0a;
            color:#ffffff;
            font-family:'Rajdhani', sans-serif;
            min-height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            background-image:
                linear-gradient(rgba(0,0,0,0.72), rgba(0,10,15,0.82)),
                url('https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?w=1920&q=80&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        /* CARD */
        .register-card{
            width:100%;
            max-width:420px;
            background-color:#111111;
            border:1px solid #333;
            border-top:5px solid #00e5ff;
            border-radius:18px;
            padding:30px 25px;
            box-shadow:0 10px 25px rgba(0,229,255,0.15);
            position:relative;
        }

        /* CLOSE BUTTON */
        .btn-close-custom{
            position:absolute;
            top:15px;
            right:18px;
            color:rgba(255,255,255,0.6);
            font-size:1.2rem;
            text-decoration:none;
            transition:0.3s;
        }

        .btn-close-custom:hover{
            color:#00e5ff;
        }

        /* TITLE */
        h2{
            text-align:center;
            font-weight:700;
            font-style:italic;
            text-transform:uppercase;
            margin-bottom:28px;
            color:#ffffff;
            letter-spacing:1px;
        }

        h2 span{
            color:#00e5ff;
        }

        /* LABEL */
        .form-label{
            color:#ffffff;
            font-size:0.9rem;
            font-weight:700;
            text-transform:uppercase;
            margin-bottom:7px;
            letter-spacing:1px;
        }

        /* INPUT WRAPPER */
        .input-group-custom{
            position:relative;
            margin-bottom:18px;
        }

        /* ICON */
        .input-group-custom i{
            position:absolute;
            left:15px;
            top:50%;
            transform:translateY(-50%);
            color:#00e5ff;
            font-size:0.9rem;
            z-index:2;
        }

        /* INPUT */
        .form-control{
            width:100%;
            height:45px;
            background-color:#0a0a0a !important;
            border:1px solid #444 !important;
            border-radius:8px !important;
            padding-left:42px !important;
            color:#ffffff !important;
            font-size:0.95rem;
            transition:0.3s;
        }

        /* INPUT FOCUS */
        .form-control:focus{
            background-color:#111 !important;
            border-color:#00e5ff !important;
            color:#ffffff !important;
            box-shadow:0 0 8px rgba(0,229,255,0.25) !important;
        }

        /* PLACEHOLDER */
        .form-control::placeholder{
            color:#cccccc;
            opacity:1;
        }

        /* AUTOFILL */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus{
            -webkit-text-fill-color:#ffffff;
            -webkit-box-shadow:0 0 0px 1000px #0a0a0a inset;
            transition: background-color 5000s ease-in-out 0s;
        }

        /* CHECKBOX */
        .form-check{
            margin-top:8px;
        }

        .form-check-input{
            background-color:#0a0a0a;
            border:1px solid #555;
        }

        .form-check-input:checked{
            background-color:#00e5ff;
            border-color:#00e5ff;
        }

        .form-check-label{
            color:#dddddd;
            font-size:0.9rem;
        }

        /* BUTTON */
        .btn-go{
            width:100%;
            height:50px;
            margin-top:20px;
            border:none;
            border-radius:8px;
            background:linear-gradient(45deg,#00e5ff,#008b99);
            color:#ffffff;
            font-size:1rem;
            font-weight:700;
            text-transform:uppercase;
            letter-spacing:1px;
            transform:skewX(-10deg);
            transition:0.3s;
        }

        .btn-go span{
            display:inline-block;
            transform:skewX(10deg);
        }

        .btn-go:hover{
            transform:skewX(-10deg) scale(1.02);
            box-shadow:0 6px 18px rgba(0,229,255,0.25);
        }

        /* ALERT */
        .alert-danger{
            background:#3a0000;
            color:#ff7777;
            border:1px solid #ff003c;
        }

        .alert-success{
            background:#002b1f;
            color:#00e5ff;
            border:1px solid #00e5ff;
        }

        /* LOGIN */
        .login-text{
            text-align:center;
            margin-top:22px;
            color:#bbbbbb;
            font-size:0.95rem;
        }

        .login-text a{
            color:#00e5ff;
            text-decoration:none;
            font-weight:bold;
        }

        .login-text a:hover{
            text-decoration:underline;
        }

    </style>
</head>
<body>

<div class="register-card">

    <!-- CLOSE -->
    <a href="login.php" class="btn-close-custom">
        <i class="fas fa-times"></i>
    </a>

    <!-- TITLE -->
    <h2>
        USER <span>REGISTER</span>
    </h2>

    <!-- ERROR -->
    <?php if($error): ?>
        <div class="alert alert-danger text-center py-2 mb-3">
            <?= $error; ?>
        </div>
    <?php endif; ?>

    <!-- SUCCESS -->
    <?php if($success): ?>
        <div class="alert alert-success text-center py-2 mb-3">
            <?= $success; ?>
        </div>
    <?php endif; ?>

    <!-- FORM -->
    <form action="" method="POST">

        <!-- USERNAME -->
        <div class="mb-2">
            <label class="form-label">Username</label>

            <div class="input-group-custom">
                <i class="fas fa-user"></i>

                <input
                    type="text"
                    name="username"
                    class="form-control"
                    placeholder="Masukkan Username"
                    required
                >
            </div>
        </div>

        <!-- EMAIL -->
        <div class="mb-2">
            <label class="form-label">Email</label>

            <div class="input-group-custom">
                <i class="fas fa-envelope"></i>

                <input
                    type="email"
                    name="email"
                    class="form-control"
                    placeholder="Masukkan Email"
                    required
                >
            </div>
        </div>

        <!-- PASSWORD -->
        <div class="mb-2">
            <label class="form-label">Password</label>

            <div class="input-group-custom">
                <i class="fas fa-lock"></i>

                <input
                    type="password"
                    name="password"
                    class="form-control"
                    placeholder="Masukkan Password"
                    required
                >
            </div>
        </div>

        <!-- KONFIRMASI PASSWORD -->
        <div class="mb-2">
            <label class="form-label">Konfirmasi Password</label>

            <div class="input-group-custom">
                <i class="fas fa-lock"></i>

                <input
                    type="password"
                    name="confirm_password"
                    class="form-control"
                    placeholder="Konfirmasi Password"
                    required
                >
            </div>
        </div>

        <!-- TERMS -->
        <div class="form-check mb-2">

            <input
                type="checkbox"
                name="terms"
                class="form-check-input"
                id="terms"
            >

            <label class="form-check-label" for="terms">
                Saya setuju dengan Syarat & Ketentuan
            </label>

        </div>

        <!-- BUTTON -->
        <button type="submit" name="register_btn" class="btn btn-go">
            <span>Register Now</span>
        </button>

    </form>

    <!-- LOGIN -->
    <div class="login-text">
        Sudah punya akun?
        <a href="login.php">Login</a>
    </div>

</div>

</body>
</html>