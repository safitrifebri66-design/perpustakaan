<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="user"){
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if($id){
    mysqli_query($conn,"DELETE FROM buku WHERE id=$id");
}

header("Location: user.php");
exit;