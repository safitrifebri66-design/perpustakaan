<?php
include 'db.php';

if(isset($_POST['register'])){
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = "user";

    $sql = "INSERT INTO users(username,password,role) VALUES('$username','$password','$role')";
    if(mysqli_query($conn,$sql)){
        header("Location: index.php");
        exit;
    } else {
        $error = "Gagal register: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .register-container {
            max-width: 400px;
            margin: 80px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .register-container h2 {
            margin-bottom: 25px;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #4e73df;
        }
        .btn-register {
            background-color: #4e73df;
            color: white;
        }
        .btn-register:hover {
            background-color: #2e59d9;
        }
    </style>
</head>
<body>
<div class="register-container">
    <h2 class="text-center">Register</h2>
    <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <form method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" name="username" placeholder="Masukkan username" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" name="password" placeholder="Masukkan password" required>
        </div>
        <button type="submit" name="register" class="btn btn-register w-100">Register</button>
    </form>
    <p class="mt-3 text-center">
        Sudah punya akun? <a href="login.php">Login di sini</a>
    </p>
</div>

<!-- Bootstrap JS (opsional jika ingin fitur JS) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>