<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="user"){
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if(!$id) { header("Location: user.php"); exit; }

// Ambil data buku
$res = mysqli_query($conn,"SELECT * FROM buku WHERE id=$id");
$data = mysqli_fetch_assoc($res);

if(isset($_POST['edit'])){
    $judul = $_POST['judul'];
    $penulis = $_POST['penulis'];
    $tahun = $_POST['tahun'];
    mysqli_query($conn,"UPDATE buku SET judul='$judul', penulis='$penulis', tahun='$tahun' WHERE id=$id");
    header("Location: user.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Buku</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">

<h2>Edit Buku</h2>

<form method="POST">
<input type="text" name="judul" value="<?= $data['judul'] ?>" required><br>
<input type="text" name="penulis" value="<?= $data['penulis'] ?>" required><br>
<input type="number" name="tahun" value="<?= $data['tahun'] ?>" required><br>
<button type="submit" name="edit">Update</button>
</form>

<br>

<a class="kembali" href="user.php">Kembali</a>

<br>

<a class="logout" href="logout.php">Logout</a>

</div>
</body>
</html>