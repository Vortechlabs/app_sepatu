<?php
session_start();
require '../../koneksi.php';

if (!isset($_SESSION['id_users'])) {
    header('Location: ../../login.php');
    exit;
}

$id_user = $_SESSION['id_users'];
$id_alamat = $_POST['id_alamat'];
$id_metode = $_POST['id_metode'];
$id_kurir = $_POST['id_kurir'];
$total_barang = $_POST['total_barang'];
$tanggal = date('Y-m-d H:i:s');

// Ambil harga kurir
$kurir = mysqli_query($koneksi, "SELECT harga FROM kurir WHERE id_kurir = $id_kurir");
$hargaKurir = mysqli_fetch_assoc($kurir)['harga'];
$total_harga = $total_barang + $hargaKurir;

// Insert pesanan dulu (tanpa id_keranjang)
$query_pesanan = "INSERT INTO pesanan 
    (id_users, id_alamat, id_metode_pembayaran, id_kurir, tanggal_pesanan, total_harga, status_pesanan)
    VALUES 
    ($id_user, $id_alamat, $id_metode, $id_kurir, '$tanggal', $total_harga, 'pending')";
mysqli_query($koneksi, $query_pesanan);
$id_pesanan = mysqli_insert_id($koneksi);

// Ambil semua item di keranjang user
$keranjang_all = mysqli_query($koneksi, "SELECT k.*, s.harga FROM keranjang k JOIN sepatu s ON k.id_sepatu = s.id_sepatu WHERE k.id_user = $id_user");

// Insert detail pesanan per item keranjang
while ($item = mysqli_fetch_assoc($keranjang_all)) {
    $id_sepatu = $item['id_sepatu'];
    $ukuran = $item['ukuran'];
    $warna = $item['warna'];
    $jumlah = $item['jumlah'];
    $harga_satuan = $item['harga'];
    $subtotal = $jumlah * $harga_satuan;

    $insert_detail = "INSERT INTO detail_pesanan 
        (id_pesanan, id_sepatu, ukuran, warna, jumlah, harga_satuan, subtotal)
        VALUES
        ($id_pesanan, $id_sepatu, '$ukuran', '$warna', $jumlah, $harga_satuan, $subtotal)";
    mysqli_query($koneksi, $insert_detail);
}

// Hapus keranjang user
mysqli_query($koneksi, "DELETE FROM keranjang WHERE id_user = $id_user");

// Redirect ke halaman pembayaran
header("Location: bayar.php?id_pesanan=$id_pesanan");
exit;
