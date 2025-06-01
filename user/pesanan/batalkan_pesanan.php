<?php
session_start();
require '../../koneksi.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['id_users'])) {
    header('Location: ../../autentikasi/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pesanan = intval($_POST['id_pesanan']);
    $id_users = intval($_SESSION['id_users']);

    // Cek status pesanan dan status pembayaran
    $cek = mysqli_query($koneksi, "
        SELECT 
            p.status_pesanan, 
            py.status_pembayaran 
        FROM pesanan p
        LEFT JOIN pembayaran py ON p.id_pesanan = py.id_pesanan
        WHERE p.id_pesanan = $id_pesanan AND p.id_users = $id_users
    ");
    $data = mysqli_fetch_assoc($cek);

    if ($data && in_array($data['status_pesanan'], ['pending', 'diproses'])) {
        // Ubah status pesanan
        mysqli_query($koneksi, "UPDATE pesanan SET status_pesanan = 'dibatalkan' WHERE id_pesanan = $id_pesanan");

        // Jika pembayaran juga masih dalam status tertentu, batalkan juga
        if (in_array($data['status_pembayaran'], ['belum dibayar', 'pending', 'dibayar'])) {
            mysqli_query($koneksi, "
                UPDATE pembayaran 
                SET status_pembayaran = 'dibatalkan', bukti_bayar = NULL 
                WHERE id_pesanan = $id_pesanan
            ");
        }
    }
}

header('Location: ../profil/profil.php');
exit;
