<?php
namespace App\Config;

$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_perpustakaan";

$conn = mysqli_connect($host,$user,$pass,$db);

if(!$conn){
    die("Koneksi gagal");
}
?>