<?php
session_start();
include 'db.php';


interface VerifikasiInterface {
    public function approve($id);
    public function reject($id);
}

// Class Buku
/**
 * Class Buku
 * Digunakan untuk merepresentasikan data buku dalam sistem perpustakaan.
 * Setiap objek Buku berisi informasi seperti judul, penulis, tahun, status, dan pemilik buku.
 */
class Buku {
    public $id;
    public $judul;
    public $penulis;
    public $tahun;
    public $status;
    public $user_id;

        /**
     * Constructor untuk membuat objek buku
     * 
     * @param int $id ID buku
     * @param string $judul Judul buku
     * @param string $penulis Nama penulis
     * @param int $tahun Tahun terbit buku
     * @param string $status Status buku (pending, approved, rejected)
     * @param int $user_id ID pemilik buku
     */
    public function __construct($id, $judul, $penulis, $tahun, $status, $user_id){
        $this->id = $id;
        $this->judul = $judul;
        $this->penulis = $penulis;
        $this->tahun = $tahun;
        $this->status = $status;
        $this->user_id = $user_id;
    }
}

// Class User
/**
 * Class User
 * Digunakan untuk mengelola aktivitas pengguna dalam sistem.
 * User dapat menambahkan, mengedit, menghapus, dan melihat buku miliknya.
 */
class User {
    protected $username;
    protected $conn;
    protected $id;

     /**
     * Constructor untuk membuat objek user
     * 
     * @param int $id ID user
     * @param string $username Nama user
     * @param object $conn Koneksi database
     */
    public function __construct($id, $username, $conn){
        $this->id = $id;
        $this->username = $username;
        $this->conn = $conn;
    }

      /**
     * Menambahkan buku baru ke database
     * 
     * @param string $judul Judul buku
     * @param string $penulis Penulis buku
     * @param int $tahun Tahun terbit
     */
    public function tambahBuku($judul, $penulis, $tahun){
        $stmt = $this->conn->prepare("INSERT INTO buku(judul,penulis,tahun,status,user_id) VALUES(?,?,?,'pending',?)");
        $stmt->bind_param("ssii",$judul,$penulis,$tahun,$this->id);
        $stmt->execute();
    }

    public function editBuku($id, $judul, $penulis, $tahun){
        $stmt = $this->conn->prepare("UPDATE buku SET judul=?, penulis=?, tahun=? WHERE id=? AND user_id=?");
        $stmt->bind_param("ssiii",$judul,$penulis,$tahun,$id,$this->id);
        $stmt->execute();
    }

    public function hapusBuku($id){
        $stmt = $this->conn->prepare("DELETE FROM buku WHERE id=? AND user_id=?");
        $stmt->bind_param("ii",$id,$this->id);
        $stmt->execute();
    }

    public function ambilBuku(){
        $stmt = $this->conn->prepare("SELECT * FROM buku WHERE user_id=? ORDER BY id DESC");
        $stmt->bind_param("i",$this->id);
        $stmt->execute();
        $result = $stmt->get_result();
        $booksArray = [];
        while($row = $result->fetch_assoc()){
            $booksArray[] = new Buku($row['id'],$row['judul'],$row['penulis'],$row['tahun'],$row['status'],$row['user_id']);
        }
        return $booksArray;
    }

    public function pinjamUlang($id){
        $stmt = $this->conn->prepare("SELECT * FROM buku WHERE id=? AND user_id=?");
        $stmt->bind_param("ii",$id,$this->id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if($row && $row['status']=='rejected'){
            $this->tambahBuku($row['judul'],$row['penulis'],(int)$row['tahun']);
            return true;
        }
        return false;
    }
}

// Class Admin
/**
 * Class Admin
 * Merupakan turunan dari class User.
 * Admin memiliki hak akses untuk memverifikasi buku yang ditambahkan user.
 */
class Admin extends User implements VerifikasiInterface {
    public function ambilBuku(){ // Override → ambil semua buku
        $result = $this->conn->query("SELECT * FROM buku ORDER BY id DESC");
        $booksArray = [];
        while($row = $result->fetch_assoc()){
            $booksArray[] = new Buku($row['id'],$row['judul'],$row['penulis'],$row['tahun'],$row['status'],$row['user_id']);
        }
        return $booksArray;
    }

        /**
     * Menyetujui buku yang diajukan user
     * 
     * @param int $id ID buku
     */
    public function approve($id){
        $stmt = $this->conn->prepare("UPDATE buku SET status='approved' WHERE id=?");
        $stmt->bind_param("i",$id);
        $stmt->execute();
    }

    /**
     * Menolak buku yang diajukan user
     * 
     * @param int $id ID buku
     */
    public function reject($id){
        $stmt = $this->conn->prepare("UPDATE buku SET status='rejected' WHERE id=?");
        $stmt->bind_param("i",$id);
        $stmt->execute();
    }
}


if(!isset($_SESSION['role'])){
    header("Location: login.php");
    exit;
}

if($_SESSION['role']=='admin'){
    $user = new Admin($_SESSION['user_id'], $_SESSION['username'], $conn);
}else{
    $user = new User($_SESSION['user_id'], $_SESSION['username'], $conn);
}


if(isset($_POST['tambah'])){
    $user->tambahBuku(trim($_POST['judul']), trim($_POST['penulis']), (int)$_POST['tahun']);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

if(isset($_POST['edit'])){
    $user->editBuku((int)$_POST['id'], trim($_POST['judul']), trim($_POST['penulis']), (int)$_POST['tahun']);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

if(isset($_GET['hapus'])){
    $user->hapusBuku((int)$_GET['hapus']);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

if(isset($_GET['repinjam'])){
    $user->pinjamUlang((int)$_GET['repinjam']);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

if($_SESSION['role']=='admin'){
    if(isset($_GET['approve'])){
        $user->approve((int)$_GET['approve']);
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }
    if(isset($_GET['reject'])){
        $user->reject((int)$_GET['reject']);
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }
}


$books = $user->ambilBuku();
?>

<!DOCTYPE html>
<html>
<head>
<title><?= ucfirst($_SESSION['role']) ?> Dashboard</title>
<style>
body{font-family:Arial,sans-serif;margin:0;background:#e0f7fa;}
.container{max-width:900px;margin:30px auto;background:#fff;padding:25px;border-radius:12px;box-shadow:0 4px 15px rgba(0,0,0,0.2);}
h2{text-align:center;color:#00796b;}
form input, form button{padding:10px;margin:5px 0;width:100%;border-radius:6px;border:1px solid #ccc;}
form button{background:#00796b;color:white;border:none;cursor:pointer;}
form button:hover{background:#004d40;}
table{width:100%;border-collapse:collapse;margin-top:20px;}
th,td{border:1px solid #ccc;padding:10px;text-align:center;}
th{background:#b2dfdb;}
a.button{padding:5px 10px;border-radius:5px;color:white;text-decoration:none;font-weight:bold;}
a.approve{background:#4caf50;} a.reject{background:#f44336;}
.logout{display:block;width:120px;margin:20px auto;padding:10px 0;background:#d32f2f;color:white;text-align:center;border-radius:6px;text-decoration:none;font-weight:bold;}
</style>
</head>
<body>
<div class="container">
<h2><?= ucfirst($_SESSION['role']) ?> Dashboard</h2>

<?php if($_SESSION['role']=='user'){ ?>
<h3>Tambah Buku</h3>
<form method="POST">
    <input type="text" name="judul" placeholder="Judul Buku" required>
    <input type="text" name="penulis" placeholder="Penulis" required>
    <input type="number" name="tahun" placeholder="Tahun" required>
    <button type="submit" name="tambah">Tambah</button>
</form>
<?php } ?>

<h3>Daftar Buku</h3>
<table>
<tr><th>ID</th><th>Judul</th><th>Penulis</th><th>Tahun</th><th>Status</th><th>Aksi</th></tr>
<?php foreach($books as $b){ ?>
<tr>
    <td><?= $b->id ?></td>
    <td><?= htmlspecialchars($b->judul) ?></td>
    <td><?= htmlspecialchars($b->penulis) ?></td>
    <td><?= $b->tahun ?></td>
    <td><?= ucfirst($b->status) ?></td>
    <td>
    <?php 
    if($_SESSION['role']=='admin' && $b->status=='pending'){
        echo '<a class="button approve" href="?approve='.$b->id.'">Approve</a> ';
        echo '<a class="button reject" href="?reject='.$b->id.'">Reject</a>';
    } elseif($_SESSION['role']=='user'){
        echo '<a class="button" style="background:#4caf50" href="user_edit.php?id='.$b->id.'">Edit</a> ';
    echo '<a class="button" style="background:#f44336" href="user_delete.php?id='.$b->id.'" onclick="return confirm(\'Hapus buku?\')">Hapus</a>';
        if($b->status=='rejected'){
            echo ' <a class="button" style="background:#ff9800" href="?repinjam='.$b->id.'">Pinjam Ulang</a>';
        }
    } else {
        echo ucfirst($b->status);
    }
    ?>
    </td>
</tr>
<?php } ?>
</table>

<?php
$i = 0;
if(count($books) > 0){
    do {
        echo "Buku ke-".($i+1).": ".htmlspecialchars($books[$i]->judul)." | Penulis: ".htmlspecialchars($books[$i]->penulis)."<br>";
        $i++;
    } while($i < count($books));
} else {
    echo "Belum ada buku.";
}
?>

<a class="logout" href="index.php">Logout</a>
</div>
</body>
</html>