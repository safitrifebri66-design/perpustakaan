<?php
session_start();

include 'db.php';
require 'functions.php';

use App\Config;
use function App\Helpers\sanitize;

if(isset($_POST['login'])){

    $username = sanitize($_POST['username']);
    $password = sanitize($_POST['password']);

    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($conn,$sql);

    if(mysqli_num_rows($result) > 0){

        $user = mysqli_fetch_assoc($result);

        // cek password hash
        if(password_verify($password, $user['password'])){

            // SESSION LOGIN
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username']; 
            $_SESSION['role'] = $user['role'];

            // redirect berdasarkan role
            if($user['role']=="admin"){
                header("Location: admin.php");
            } else {
                header("Location: user.php");
            }
            exit;

        } else {
            $error = "Password salah!";
        }

    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>

<html>
<head>
<title>Login</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

<h2>Login</h2>

<?php
if(isset($error)){
    echo "<p style='color:red'>$error</p>";
}
?>

<form method="POST">

<input type="text" name="username" placeholder="Username" required>

<input type="password" name="password" placeholder="Password" required>

<button type="submit" name="login">Login</button>

</form>

<p>Belum punya akun? <a href="register.php">Register</a></p>
<p><a href="index.php">Kembali ke Beranda</a></p>

</div>

</body>
</html>
