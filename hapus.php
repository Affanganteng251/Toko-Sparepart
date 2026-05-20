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

$query = mysqli_query($conn, "SELECT gambar FROM spareparts WHERE id = '$id'");
$data = mysqli_fetch_assoc($query);

$file_gambar = "uploads/" . $data['gambar'];
if (file_exists($file_gambar)) {
    unlink($file_gambar);
}

$sql = "DELETE FROM spareparts WHERE id = '$id'";

if (mysqli_query($conn, $sql)) {
    echo "<script>alert('Data berhasil dihapus!'); window.location='index.php';</script>";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>