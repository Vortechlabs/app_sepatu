<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../autentikasi/admin_login.php');
    exit;
}

require_once '../koneksi.php';

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    $cekUser = mysqli_query($koneksi, "SELECT * FROM users WHERE id_users = $id");
    if (mysqli_num_rows($cekUser) > 0) {
        $user = mysqli_fetch_assoc($cekUser);

        if (!empty($user['image']) && file_exists('../' . $user['image'])) {
            unlink('../' . $user['image']);
        }

        $hapus = mysqli_query($koneksi, "DELETE FROM users WHERE id_users = $id");

        if ($hapus) {
            $_SESSION['notif'] = 'Pengguna berhasil dihapus.';
        } else {
            $_SESSION['notif'] = 'Gagal menghapus pengguna.';
        }
    } else {
        $_SESSION['notif'] = 'Pengguna tidak ditemukan.';
    }
} else {
    $_SESSION['notif'] = 'Permintaan tidak valid.';
}

header('Location: kelola_user.php');
exit;
?>
