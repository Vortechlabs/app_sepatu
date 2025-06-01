<?php
session_start();
include("../../koneksi.php");

// Cek login
if (!isset($_SESSION['id_users'])) {
    header("Location: ../../autentikasi/login.php");
    exit();
}

// Ambil data dari form
$id_users = $_SESSION['id_users'];
$id_sepatu = isset($_POST['id_sepatu']) ? intval($_POST['id_sepatu']) : 0;
$jumlah = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
$warna = isset($_POST['selected_color']) ? $_POST['selected_color'] : '';
$ukuran = isset($_POST['selected_size']) ? $_POST['selected_size'] : '';
$beli_sekarang = isset($_POST['beli_sekarang']) ? $_POST['beli_sekarang'] : '0';

// Validasi data
if ($id_sepatu <= 0) {
    $_SESSION['error'] = "Produk tidak valid";
    header("Location: ../produk/detail_produk.php?id_sepatu=".$id_sepatu);
    exit();
}

if ($jumlah <= 0) {
    $_SESSION['error'] = "Jumlah tidak valid";
    header("Location: ../produk/detail_produk.php?id_sepatu=".$id_sepatu);
    exit();
}

if (empty($warna)) {
    $_SESSION['error'] = "Silakan pilih warna";
    header("Location: ../produk/detail_produk.php?id_sepatu=".$id_sepatu);
    exit();
}

if (empty($ukuran)) {
    $_SESSION['error'] = "Silakan pilih ukuran";
    header("Location: ../produk/detail_produk.php?id_sepatu=".$id_sepatu);
    exit();
}

// Cek apakah produk sudah ada di keranjang
$check_query = "SELECT id_keranjang, jumlah FROM keranjang 
                WHERE id_user = ? AND id_sepatu = ? AND warna = ? AND ukuran = ?";
$stmt = $koneksi->prepare($check_query);
$stmt->bind_param("iiss", $id_users, $id_sepatu, $warna, $ukuran);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update jumlah jika sudah ada
    $row = $result->fetch_assoc();
    $new_jumlah = $row['jumlah'] + $jumlah;
    
    $update_query = "UPDATE keranjang SET jumlah = ? WHERE id_keranjang = ?";
    $stmt = $koneksi->prepare($update_query);
    $stmt->bind_param("ii", $new_jumlah, $row['id_keranjang']);
    $stmt->execute();
    
    $_SESSION['success'] = "Jumlah produk di keranjang telah diperbarui";
} else {
    // Tambahkan baru jika belum ada
    $insert_query = "INSERT INTO keranjang (id_user, id_sepatu, jumlah, warna, ukuran) 
                     VALUES (?, ?, ?, ?, ?)";
    $stmt = $koneksi->prepare($insert_query);
    $stmt->bind_param("iiiss", $id_users, $id_sepatu, $jumlah, $warna, $ukuran);
    $stmt->execute();
    
    $_SESSION['success'] = "Produk berhasil ditambahkan ke keranjang";
}

// Redirect berdasarkan aksi
if ($beli_sekarang == '1') {
    // Ambil ID keranjang yang baru ditambahkan
    $id_keranjang = $koneksi->insert_id;
    $_SESSION['checkout_items'] = [$id_keranjang]; // Simpan ID item yang akan checkout
    
    header("Location: ../pesanan/hlmnCO.php");
} else {
    header("Location: ../produk/detail_produk.php?id_sepatu=".$id_sepatu);
}
exit();
?>