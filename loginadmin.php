<?php
session_start();
include 'db.php';

$error = "";

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query($conn,"SELECT * FROM users WHERE username='$username' AND role='admin'");
    $user = mysqli_fetch_assoc($query);

    if($user){
        if($password == $user['password']){
            $_SESSION['username'] = $username;
            $_SESSION['role'] = "admin";
            header("Location: admin_dashboard.php");
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username admin tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Admin</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(to right,#74ebd5,#ACB6E5);
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container{
            width: 350px;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow:0 0 15px rgba(0,0,0,0.3);
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
        }
        input, button {
            width: 95%;
            padding: 10px;
            margin: 8px 0;
            border-radius:5px;
            border:1px solid #ccc;
        }
        button{
            background:#74ebd5;
            border:none;
            cursor:pointer;
            font-weight:bold;
        }
        button:hover{
            background:#ACB6E5;
            color:white;
        }
        .error{
            color:red;
            margin-bottom:10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login Admin</h2>
        <?php if($error!="") echo "<div class='error'>$error</div>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit" name="login">Login</button>
        </form>
    </div>
</body>
</html>