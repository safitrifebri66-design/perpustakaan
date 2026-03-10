<?php
session_start();
include 'db.php';

// Interface untuk Verifikasi
interface VerifikasiInterface {
    public function approve($id);
    public function reject($id);
}

// Class dasar User
class User {
    protected $username;
    protected $role;

    public function __construct($username, $role){
        $this->username = $username;
        $this->role = $role;
    }

    public function isAdmin(){
        return $this->role === "admin";
    }

    // Method untuk polymorphism
    public function tampilkanBuku(){
        echo "User hanya dapat melihat daftar buku.";
    }
}

// Class Admin mewarisi User dan mengimplementasi interface Verifikasi
class Admin extends User implements VerifikasiInterface {

    private $conn; // hak akses private

    // Overloading sederhana (parameter role memiliki nilai default)
    public function __construct($username, $conn, $role = "admin"){
        parent::__construct($username, $role);
        $this->conn = $conn;
    }

    // Implementasi method interface
    public function approve($id){
        $stmt = $this->conn->prepare("UPDATE buku SET status='approved' WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }

    public function reject($id){
        $stmt = $this->conn->prepare("UPDATE buku SET status='rejected' WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }

    // Polymorphism (override method dari class User)
    public function tampilkanBuku(){
        $result = $this->conn->query("SELECT * FROM buku ORDER BY id DESC");
        return $result;
    }
}

// Cek hak akses admin
if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){
    header("Location: login_admin.php");
    exit;
}

// Buat objek Admin
$admin = new Admin($_SESSION['username'], $conn);

// Proses approve/reject
if(isset($_GET['approve'])){
    $admin->approve($_GET['approve']);
    header("Location: admin_dashboard.php");
    exit;
}

if(isset($_GET['reject'])){
    $admin->reject($_GET['reject']);
    header("Location: admin_dashboard.php");
    exit;
}

// Ambil semua buku
$books = $admin->tampilkanBuku();
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<style>
html, body {
    height:100%;
    margin:0;
    font-family:Arial;
    background: linear-gradient(to right,#74ebd5,#ACB6E5);
}
.container{
    width:700px;
    margin:50px auto;
    padding:30px;
    background:#fff;
    border-radius:10px;
    box-shadow:0 0 15px rgba(0,0,0,0.3);
}
h2{text-align:center; margin-bottom:20px;}
table{width:100%; border-collapse:collapse; margin-top:20px;}
th, td{border:1px solid #ccc; padding:10px; text-align:center;}
th{background:#f0f0f0;}
a.button{
    padding:5px 10px;
    border-radius:5px;
    color:white;
    text-decoration:none;
    font-weight:bold;
}
a.approve{background:green;}
a.reject{background:red;}
.logout{
    display:block;
    width:100px;
    margin:20px auto;
    padding:5px 0;
    background:red;
    color:white;
    text-align:center;
    font-weight:bold;
    border-radius:5px;
    text-decoration:none;
}
</style>
</head>
<body>

<div class="container">
<h2>Admin Dashboard - Verifikasi Buku</h2>

<table>
<tr>
<th>ID</th>
<th>Judul</th>
<th>Penulis</th>
<th>Tahun</th>
<th>Status</th>
<th>Aksi</th>
</tr>

<?php while($row = $books->fetch_assoc()){ ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= $row['judul'] ?></td>
<td><?= $row['penulis'] ?></td>
<td><?= $row['tahun'] ?></td>
<td><?= $row['status'] ?></td>
<td>

<?php if($row['status']=='pending'){ ?>
<a class="button approve" href="?approve=<?= $row['id'] ?>">Setujui</a>
<a class="button reject" href="?reject=<?= $row['id'] ?>">Tolak</a>
<?php } else { ?>
<?= ucfirst($row['status']); ?>
<?php } ?>

</td>
</tr>
<?php } ?>

</table>

<a class="logout" href="index.php">Logout</a>
</div>

</body>
</html>