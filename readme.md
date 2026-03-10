# Sistem Perpustakaan

Aplikasi perpustakaan sederhana berbasis web menggunakan **PHP** dan **MySQL**.  
Sistem ini memungkinkan user untuk menambahkan dan mengelola buku, sedangkan admin dapat memverifikasi buku yang diajukan oleh user.

---

# Fitur Sistem

## User
- Register akun
- Login ke sistem
- Menambahkan buku
- Mengedit data buku
- Menghapus buku
- Melihat daftar buku yang dimiliki

## Admin
- Login sebagai admin
- Melihat daftar buku dari user
- Menyetujui buku (Approve)
- Menolak buku (Reject)

---

# Teknologi yang Digunakan

- **Backend** : PHP
- **Database** : MySQL
- **Frontend** : HTML, CSS
- **Dependency Manager** : Composer

---

# Konsep Pemrograman
## Object Oriented Programming (OOP)
Program ini menggunakan konsep **OOP** untuk mengatur struktur kode agar lebih rapi dan mudah dikembangkan.
Beberapa class yang digunakan:
- **Class Buku**  
  Merepresentasikan data buku seperti judul, penulis, tahun, dan status.
- **Class User**  
  Digunakan untuk mengelola aktivitas user seperti menambah, mengedit, dan menghapus buku.
- **Class Admin**  
  Merupakan turunan dari class User yang memiliki hak tambahan untuk menyetujui atau menolak buku.
---
## Namespace
Program ini juga menggunakan **namespace** untuk mengelompokkan kode agar lebih terstruktur.
Contoh namespace yang digunakan:
- **App\Config** → untuk konfigurasi database  
- **App\Helpers** → untuk fungsi bantuan seperti `sanitize()`  
Namespace membantu menghindari konflik nama pada program.
---
# Struktur Folder
perpustakaan/

│
├── admin_dashboard.php--#Halaman dashboard admin
├── db.php--#Koneksi database
├── db_perpustakaan.sql--#File database
├── functions.php--#Fungsi helper
├── hash_admin.php--#Membuat hash password admin
├── index.php--#Halaman utama
├── login.php--#Login user
├── loginadmin.php--#Login admin
├── logout.php--#Logout user/admin
├── register.php--#Registrasi user
├── user.php--#Dashboard user
├── user_edit.php--#Edit buku oleh user
├── user_delete.php--#Hapus buku oleh user
├── style.css--#File tampilan website
├── composer.json--#Konfigurasi composer
├── composer.lock--#File dependency composer
└── readme.md--#Dokumentasi project

---
# Alur Sistem
1. User melakukan **register atau login**.
2. Setelah login, user dapat **menambahkan buku** ke sistem.
3. Buku yang ditambahkan akan memiliki status **pending**.
4. Admin dapat melihat buku tersebut di **dashboard admin**.
5. Admin dapat **menyetujui (approve) atau menolak (reject)** buku.
6. Status buku akan berubah sesuai keputusan admin.

---

# Database
Database yang digunakan bernama:
Database ini menyimpan data:
- User
- Buku
- Status buku
---

