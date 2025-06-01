<?php
session_start();
require '../../koneksi.php';

if (!isset($_SESSION['id_users'])) {
    header('Location: ../../autentikasi/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pesanan = intval($_POST['id_pesanan']);
    $id_users = intval($_SESSION['id_users']);

    // Cek status pesanan dulu
    $cek = mysqli_query($koneksi, "SELECT status_pesanan FROM pesanan WHERE id_pesanan = $id_pesanan AND id_users = $id_users");
    $data = mysqli_fetch_assoc($cek);

    if ($data && $data['status_pesanan'] === 'dikirim') {
        mysqli_query($koneksi, "UPDATE pesanan SET status_pesanan = 'selesai' WHERE id_pesanan = $id_pesanan");
    }
}

header('Location: ../profil/profil.php');
exit;
