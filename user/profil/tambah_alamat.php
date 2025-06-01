<?php
session_start();
require '../../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user = $_SESSION['id_users'];
    $nama = $_POST['nama_penerima'];
    $telp = $_POST['no_telp'];
    $alamat = $_POST['alamat'];

    $query = "INSERT INTO alamat (id_user, nama_penerima, no_telp, alamat) 
              VALUES ('$id_user', '$nama', '$telp', '$alamat')";

    if (mysqli_query($koneksi, $query)) {
        $id = mysqli_insert_id($koneksi);
        echo json_encode([
            'success' => true,
            'id_alamat' => $id,
            'text' => "$nama - $alamat ($telp)"
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan alamat']);
    }
}
