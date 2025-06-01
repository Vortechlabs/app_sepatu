<?php
session_start();
require '../koneksi.php';  // sesuaikan path koneksi database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $password = $_POST['password']; // password plain text

    $query = "SELECT * FROM admin WHERE username = '$username' LIMIT 1";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $admin = mysqli_fetch_assoc($result);

        if ($password === $admin['password']) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['id_admin'] = $admin['id_admin'];
            $_SESSION['nama_admin'] = $admin['nama'];
            $_SESSION['username_admin'] = $admin['username'];

            header('Location: ../admin/index.php');
            exit;
        }
    }

    // Kalau gagal login
    $_SESSION['login_error'] = 'Username atau password salah!';
    header('Location: ./admin_login.php');
    exit;
}
?>
