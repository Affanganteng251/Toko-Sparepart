<?php
require_once 'db.php';
require_once 'Auth.php';

$auth = new Auth($conn);
if (!$auth->checkLogin()) {
    header("Location: login.php");
    exit;
}

include 'db.php';

if (isset($_POST['simpan'])) {
    $nama_barang = $_POST['nama_barang'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    
    // Proses upload gambar
    $gambar = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];
    $path = "uploads/" . $gambar;
    
    if (move_uploaded_file($tmp, $path)) {
        $sql = "INSERT INTO spareparts (nama_barang, deskripsi, harga, gambar) VALUES ('$nama_barang', '$deskripsi', '$harga', '$gambar')";
        if (mysqli_query($conn, $sql)) {
            header("Location: index.php");
            exit;
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "<script>alert('Gagal mengupload gambar!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Sparepart - Racing Theme</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            background-color: #0a0a0a;
            color: #e0e0e0;
            font-family: 'Rajdhani', sans-serif;
            background-image: radial-gradient(#222 1px, transparent 1px);
            background-size: 20px 20px;
        }

        .form-container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #141414;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.8);
            border-top: 4px solid #ff003c; /* Garis aksen merah di atas form */
        }

        h2 {
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #ffffff;
            font-style: italic;
            border-bottom: 1px solid #333;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }

        h2 span {
            color: #ff003c; 
            text-shadow: 0px 0px 15px rgba(255, 0, 60, 0.6);
        }

        .form-label {
            font-weight: 600;
            color: #00ffcc; 
            letter-spacing: 1px;
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        .form-control {
            background-color: #0a0a0a;
            border: 1px solid #444;
            color: #ffffff;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background-color: #111;
            border-color: #ff003c;
            color: #ffffff;
            box-shadow: 0 0 10px rgba(255, 0, 60, 0.3);
        }

        input[type="file"]::file-selector-button {
            background-color: #333;
            color: #fff;
            border: none;
            padding: 8px 15px;
            margin-right: 15px;
            border-radius: 4px;
            transition: all 0.3s ease;
            font-family: 'Rajdhani', sans-serif;
            font-weight: bold;
        }

        input[type="file"]::file-selector-button:hover {
            background-color: #ff003c;
            color: white;
        }

        .btn-wrapper {
            margin-top: 30px;
            display: flex;
            gap: 15px;
        }

        .btn-simpan, .btn-batal {
            border: none;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 1px;
            transform: skewX(-15deg);
            transition: all 0.3s ease;
            padding: 10px 30px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-simpan {
            background: linear-gradient(45deg, #ff003c, #990000);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 0, 60, 0.4);
        }

        .btn-simpan:hover {
            background: linear-gradient(45deg, #ff3366, #cc0000);
            transform: skewX(-15deg) scale(1.05);
            box-shadow: 0 6px 20px rgba(255, 0, 60, 0.7);
        }

        .btn-batal {
            background: #333;
            color: #aaa;
        }

        .btn-batal:hover {
            background: #555;
            color: white;
            transform: skewX(-15deg) scale(1.05);
        }

        .btn-simpan span, .btn-batal span {
            transform: skewX(15deg);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-container">
        <h2>Tambah <span>Sparepart Baru</span></h2>
        
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nama_barang" class="form-label">Nama Barang</label>
                <input type="text" class="form-control" id="nama_barang" name="nama_barang" required placeholder="Contoh: Turbocharger HKS">
            </div>
            
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" required placeholder="Jelaskan spesifikasi barang..."></textarea>
            </div>
            
            <div class="mb-3">
                <label for="harga" class="form-label">Harga (Rp)</label>
                <input type="number" class="form-control" id="harga" name="harga" required placeholder="Contoh: 5000000">
            </div>
            
            <div class="mb-4">
                <label for="gambar" class="form-label">Upload Gambar</label>
                <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*" required>
            </div>
            
            <div class="btn-wrapper">
                <button type="submit" name="simpan" class="btn-simpan">
                    <span>Simpan Data</span>
                </button>
                <a href="index.php" class="btn-batal">
                    <span>Batal</span>
                </a>
            </div>
        </form>
    </div>
</div>

</body>
</html>