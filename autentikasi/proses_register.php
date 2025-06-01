<?php
include '../koneksi.php'; // ganti sesuai path koneksi MySQL-mu

error_reporting(E_ALL);
ini_set('display_errors', 1);
// Ambil data dari form
$email      = $_POST['email'];
$username   = $_POST['username'];
$password   = $_POST['password'];
$konfirmasi = $_POST['konfirmasi'];
$no_hp      = $_POST['no_telepon'];
$image      = 'assets/profil/default-image.jpg'; // default foto
$bergabung  = date('Y-m-d H:i:s');

// Cek password & konfirmasi
if ($password !== $konfirmasi) {
    echo "<script>alert('Konfirmasi password tidak cocok!'); window.history.back();</script>";
    exit;
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Insert ke database
$query = "INSERT INTO users (username, email, password, no_telepon, image, bergabung)
          VALUES ('$username', '$email', '$hashed_password', '$no_hp', '$image', '$bergabung')";

if (mysqli_query($koneksi, $query)) {
    echo "<script>alert('Register berhasil! Silakan login.'); window.location.href='login.php';</script>";
} else {
    echo "<script>alert('Gagal register: " . mysqli_error($koneksi) . "'); window.history.back();</script>";
}
?>
