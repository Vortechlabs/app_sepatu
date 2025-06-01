<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../autentikasi/admin_login.php');
    exit;
}

require_once '../koneksi.php';

if (isset($_GET['id'])) {
    $id_sepatu = intval($_GET['id']);

    $getQuery = mysqli_query($koneksi, "SELECT * FROM sepatu WHERE id_sepatu = $id_sepatu");
    $produk = mysqli_fetch_assoc($getQuery);

    if ($produk) {
        $gambarPath = '../' . $produk['gambar'];
        if (file_exists($gambarPath)) {
            unlink($gambarPath);
        }

        $deleteQuery = mysqli_query($koneksi, "DELETE FROM sepatu WHERE id_sepatu = $id_sepatu");

        if ($deleteQuery) {
            $_SESSION['pesan_sukses'] = "Produk berhasil dihapus.";
        } else {
            $_SESSION['pesan_error'] = "Gagal menghapus produk.";
        }
    } else {
        $_SESSION['pesan_error'] = "Produk tidak ditemukan.";
    }
} else {
    $_SESSION['pesan_error'] = "Permintaan tidak valid.";
}

header('Location: kelola_produk.php');
exit;
