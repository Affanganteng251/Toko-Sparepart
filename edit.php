<?php 
require_once 'db.php';
require_once 'Auth.php';

$auth = new Auth($conn);
if (!$auth->checkLogin()) {
    header("Location: login.php");
    exit;
}

include 'db.php'; 

$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM spareparts WHERE id = '$id'");
$data = mysqli_fetch_assoc($query);

if (isset($_POST['update'])) {
    $nama_barang = $_POST['nama_barang'];
    $deskripsi   = $_POST['deskripsi'];
    $harga       = $_POST['harga'];
    
    $nama_file = $_FILES['gambar']['name'];

    if ($nama_file != "") {
        $tmp_file  = $_FILES['gambar']['tmp_name'];
        $nama_file_baru = time() . "_" . $nama_file;
        $folder_tujuan  = "uploads/" . $nama_file_baru;

        if (file_exists("uploads/" . $data['gambar']) && $data['gambar'] != '') {
            unlink("uploads/" . $data['gambar']);
        }

        move_uploaded_file($tmp_file, $folder_tujuan);

        $sql = "UPDATE spareparts SET nama_barang='$nama_barang', deskripsi='$deskripsi', harga='$harga', gambar='$nama_file_baru' WHERE id='$id'";
    } else {
        $sql = "UPDATE spareparts SET nama_barang='$nama_barang', deskripsi='$deskripsi', harga='$harga' WHERE id='$id'";
    }

    if (mysqli_query($conn, $sql)) {
        echo "<script>window.location='index.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Sparepart - Racing Theme</title>
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
            border-top: 4px solid #ffaa00; 
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
            color: #ffaa00; 
            text-shadow: 0px 0px 15px rgba(255, 170, 0, 0.6);
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
            border-color: #ffaa00;
            color: #ffffff;
            box-shadow: 0 0 10px rgba(255, 170, 0, 0.3);
        }

        .img-preview {
            border: 2px solid #333;
            border-radius: 6px;
            max-width: 150px;
            margin-top: 5px;
            margin-bottom: 15px;
            display: block;
            box-shadow: 0 4px 10px rgba(0,0,0,0.5);
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
            background-color: #ffaa00;
            color: #000;
        }

        .btn-wrapper {
            margin-top: 30px;
            display: flex;
            gap: 15px;
        }

        .btn-update, .btn-batal {
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

        .btn-update {
            background: linear-gradient(45deg, #ffaa00, #cc6600);
            color: #000;
            box-shadow: 0 4px 15px rgba(255, 170, 0, 0.4);
        }

        .btn-update:hover {
            background: linear-gradient(45deg, #ffcc00, #ff8800);
            transform: skewX(-15deg) scale(1.05);
            box-shadow: 0 6px 20px rgba(255, 170, 0, 0.7);
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

        .btn-update span, .btn-batal span {
            transform: skewX(15deg);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-container">
        <h2>Edit <span>Data Sparepart</span></h2>
        
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nama_barang" class="form-label">Nama Barang</label>
                <input type="text" class="form-control" id="nama_barang" name="nama_barang" value="<?= $data['nama_barang']; ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" required><?= $data['deskripsi']; ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="harga" class="form-label">Harga (Rp)</label>
                <input type="number" class="form-control" id="harga" name="harga" value="<?= $data['harga']; ?>" required>
            </div>
            
            <div class="mb-4">
                <label class="form-label">Gambar Saat Ini</label><br>
                <img src="uploads/<?= $data['gambar']; ?>" class="img-preview" alt="Gambar Lama">
                
                <label for="gambar" class="form-label mt-2">Upload Gambar Baru (Opsional)</label>
                <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
            </div>
            
            <div class="btn-wrapper">
                <button type="submit" name="update" class="btn-update">
                    <span>Update Data</span>
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