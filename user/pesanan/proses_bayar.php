<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require '../../koneksi.php';

if (!isset($_SESSION['id_users'])) {
    header('Location: ../../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pesanan = intval($_POST['id_pesanan']);
    $metode_pembayaran = mysqli_real_escape_string($koneksi, $_POST['metode_pembayaran']);
    $no_bayar = mysqli_real_escape_string($koneksi, $_POST['no_bayar']);

    // Upload file bukti bayar
    if (isset($_FILES['bukti_bayar']) && $_FILES['bukti_bayar']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['bukti_bayar']['tmp_name'];
        $file_name = basename($_FILES['bukti_bayar']['name']);
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'pdf'];

        if (in_array($ext, $allowed_ext)) {
            $new_name = uniqid('bukti_') . '.' . $ext;
            $upload_dir = '../../assets/bukti/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $upload_path = $upload_dir . $new_name;

            if (move_uploaded_file($file_tmp, $upload_path)) {
                // Insert ke tabel pembayaran
                $query = "INSERT INTO pembayaran (bukti_bayar, id_pesanan, metode_pembayaran, no_bayar, status_pembayaran) 
                          VALUES ('$new_name', $id_pesanan, '$metode_pembayaran', '$no_bayar', 'pending')";
                if (mysqli_query($koneksi, $query)) {
                    // Update status pesanan jadi 'pending pembayaran' misal, atau sesuai kebutuhan
                    mysqli_query($koneksi, "UPDATE pesanan SET status_pesanan='pending' WHERE id_pesanan=$id_pesanan");

                    header("Location: ../profil/riwayat_pesanan.php?id_pesanan=$id_pesanan");
                    exit;
                } else {
                    echo "Gagal menyimpan data pembayaran.";
                }
            } else {
                echo "Gagal mengupload file.";
            }
        } else {
            echo "Format file tidak didukung.";
        }
    } else {
        echo "File bukti bayar wajib diupload.";
    }
} else {
    header('Location: pembayaran.php');
    exit;
}
