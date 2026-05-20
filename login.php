<?php
require_once 'db.php';
require_once 'Auth.php';

$auth = new Auth($conn);

if ($auth->checkLogin()) {
    header("Location: index.php");
    exit;
}

$error = "";

if (isset($_POST['login_btn'])) {

    $email = $_POST['email'];
    $pass = $_POST['password'];
    $remember = isset($_POST['remember']);

    if ($auth->login($email, $pass, $remember)) {

        header("Location: index.php");
        exit;

    } else {

        $error = "Data tidak cocok! Periksa kembali email dan passwordmu!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Halaman Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@600;700&display=swap" rel="stylesheet">

    <style>

        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body{
            background-color: #0a0a0a;
            font-family: 'Rajdhani', sans-serif;
            color: white;
            height: 100vh;

            display: flex;
            justify-content: center;
            align-items: center;

            background-image:
                linear-gradient(rgba(0,0,0,0.72), rgba(10,0,0,0.80)),
                url('https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?w=1920&q=80&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .login-card{
            width: 100%;
            max-width: 500px;

            background: #111;
            border: 1px solid #333;
            border-top: 5px solid #ff003c;

            padding: 40px;
            border-radius: 15px;

            box-shadow: 0 10px 30px rgba(255, 0, 60, 0.2);
        }

        h2{
            text-align: center;
            font-weight: 700;
            font-style: italic;
            text-transform: uppercase;
            margin-bottom: 30px;
            letter-spacing: 1px;
        }

        h2 span{
            color: #ff003c;
        }

        .form-label{
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }

        /* ICON */
        .input-group-text{
            background-color: #0a0a0a;
            border: 1px solid #444;
            border-right: none;
            color: #ff003c;
        }

        /* INPUT */
        .form-control{
            background-color: #0a0a0a;
            border: 1px solid #444;
            border-left: none;

            color: #ffffff !important;

            padding: 12px;
        }

        .form-control::placeholder{
            color: #bbbbbb;
            opacity: 1;
        }

        .form-control:focus{
            background-color: #111;
            border-color: #ff003c;

            color: #ffffff !important;

            box-shadow: 0 0 10px rgba(255, 0, 60, 0.3);
        }

        /* Agar autofill tetap putih */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus{
            -webkit-text-fill-color: #ffffff;
            -webkit-box-shadow: 0 0 0px 1000px #0a0a0a inset;
            transition: background-color 5000s ease-in-out 0s;
        }

        /* BUTTON */
        .btn-go{
            width: 100%;
            padding: 14px;

            border: none;

            background: linear-gradient(45deg, #ff003c, #990000);

            transform: skewX(-15deg);

            transition: 0.3s;

            margin-top: 10px;
        }

        .btn-go:hover{
            transform: skewX(-15deg) scale(1.02);
        }

        .btn-text{
            display: inline-block;

            transform: skewX(15deg);

            color: white;

            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-check-label{
            color: #dddddd;
        }

        .link-reg{
            color: #aaaaaa;
            text-decoration: none;
            transition: 0.3s;
        }

        .link-reg:hover{
            color: #ff003c;
        }

        .register-text{
            text-align: center;
            margin-top: 25px;
            color: #aaaaaa;
        }

        .register-text a{
            color: #ff003c;
            text-decoration: none;
            font-weight: bold;
        }

        .register-text a:hover{
            text-decoration: underline;
        }

        .alert{
            background-color: #3a0000;
            color: #ff7777;
            border: 1px solid #ff003c;
        }

    </style>

</head>
<body>

    <div class="login-card">

        <h2>
            USER <span>LOGIN</span>
        </h2>

        <?php if($error): ?>

            <div class="alert alert-danger text-center py-2">
                <?= $error; ?>
            </div>

        <?php endif; ?>

        <form action="" method="POST">

            <div class="mb-3">

                <label class="form-label">
                    Email
                </label>

                <div class="input-group">

                    <span class="input-group-text">
                        <i class="bi bi-envelope-fill"></i>
                    </span>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        placeholder="Masukkan Email"
                        required
                    >

                </div>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Password
                </label>

                <div class="input-group">

                    <span class="input-group-text">
                        <i class="bi bi-lock-fill"></i>
                    </span>

                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        placeholder="Masukkan Password"
                        required
                    >

                </div>

            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">

                <div class="form-check">

                    <input
                        type="checkbox"
                        class="form-check-input"
                        name="remember"
                        id="remember"
                    >

                    <label class="form-check-label" for="remember">
                        Remember Me
                    </label>

                </div>

                <a href="#" class="link-reg">
                    Forgot Password?
                </a>

            </div>

            <button type="submit" name="login_btn" class="btn btn-go">

                <span class="btn-text">
                    LOGIN
                </span>

            </button>

        </form>

        <div class="register-text">

            Belum punya akun?

            <a href="register.php">
                Register
            </a>

        </div>

    </div>

</body>
</html>