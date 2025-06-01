<?php
session_start();
include("../../koneksi.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_keranjang = intval($_POST['id_keranjang']);
    $query = "DELETE FROM keranjang WHERE id_keranjang = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $id_keranjang);
    $stmt->execute();
}

header("Location: keranjang.php");
exit;
