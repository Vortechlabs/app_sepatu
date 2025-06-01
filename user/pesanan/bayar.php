<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
require '../../koneksi.php';
session_start();

if (!isset($_GET['id_pesanan'])) {
  echo "ID pesanan tidak ditemukan!";
  exit;
}

$id_pesanan = intval($_GET['id_pesanan']);

$query = mysqli_query($koneksi, "
  SELECT 
    p.id_pesanan, 
    p.total_harga, 
    mp.provider, 
    mp.nomor_akun, 
    pr.nama_sepatu, 
    pr.gambar, 
    dp.ukuran, 
    dp.warna 
  FROM pesanan p
  JOIN detail_pesanan dp ON p.id_pesanan = dp.id_pesanan
  JOIN sepatu pr ON dp.id_sepatu = pr.id_sepatu
  JOIN metode_bayar mp ON p.id_metode_pembayaran = mp.id_metode_bayar
  WHERE p.id_pesanan = $id_pesanan
");


$data = mysqli_fetch_assoc($query);



if (!$data) {
  echo "Data pesanan tidak ditemukan!";
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Pembayaran</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"/>
</head>
<body class="bg-gray-100 font-roboto">
  <header class="bg-white shadow-md fixed w-full z-50">
    <div class="container mx-auto flex justify-between items-center py-4 px-6">
      <div class="text-2xl font-bold text-blue-900">STEP UP</div>
      <nav class="space-x-4">
        <a class="text-gray-700 hover:text-blue-900" href="../../index.php">Home</a>
        <a class="text-gray-700 hover:text-blue-900" href="../../about.php">About</a>
        <a class="text-gray-700 hover:text-blue-900" href="../../produk.php">Produk</a>
        <a class="text-gray-700 hover:text-blue-900" href="../pesanan/keranjang.php">
          <i class="fas fa-shopping-cart"></i>
        </a>

        <?php if (isset($_SESSION['id_users'])): ?>
          <!-- Jika user sudah login -->
          <a href="../profil/profil.php" class="text-gray-700 hover:text-blue-900">
            <i class="fas fa-user"></i>
          </a>
        <?php else: ?>
          <!-- Jika belum login -->
          <a href="../../autentikasi/login.php" class="bg-blue-900 text-white px-4 py-1 rounded hover:bg-blue-800">
            Login
          </a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <main class="pt-24 pb-12">
    <div class="max-w-2xl mx-auto bg-white shadow-md rounded">
      <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Pembayaran</h1>

        <!-- Ringkasan Produk -->
        <div class="mb-4 border p-4 rounded">
          <div class="flex">
            <img src="../../<?= htmlspecialchars($data['gambar']) ?>" alt="Produk" class="w-24 h-24 object-cover border mr-4">
            <div>
              <div class="font-bold"><?= htmlspecialchars($data['nama_sepatu']) ?></div>
              <div>Ukuran: <?= htmlspecialchars($data['ukuran']) ?></div>
              <div>Warna: <?= htmlspecialchars($data['warna']) ?></div>
              <div class="text-xl font-bold mt-2 text-green-700">Rp <?= number_format($data['total_harga'], 0, ',', '.') ?></div>
            </div>
          </div>
        </div>

        <!-- Form -->
        <form method="POST" action="proses_bayar.php" enctype="multipart/form-data">
          <input type="hidden" name="id_pesanan" value="<?= $id_pesanan ?>">
          <input type="hidden" name="no_bayar" id="no_bayar" value="1234567890">

          <!-- Metode Pembayaran -->
          <div class="mb-4">
            <label class="font-bold block mb-1">Metode Pembayaran</label>
            <input type="hidden" name="metode_pembayaran" value="<?= htmlspecialchars($data['provider']) ?>">
            <p class="p-2 border rounded bg-gray-100"><?= htmlspecialchars($data['provider']) ?></p>
          </div>

          <!-- Nomor Rekening Tujuan -->
          <div class="mb-4">
            <label class="font-bold block mb-1">Nomor Rekening Tujuan</label>
            <input type="hidden" name="no_bayar" value="<?= htmlspecialchars($data['nomor_akun']) ?>">
            <p class="p-2 border rounded bg-gray-100"><?= htmlspecialchars($data['nomor_akun']) ?> (<?= htmlspecialchars($data['provider']) ?>)</p>
          </div>


          <!-- Total -->
          <div class="flex justify-between items-center font-bold text-lg mb-4">
            <div>Total</div>
            <div class="text-green-700">Rp <?= number_format($data['total_harga'], 0, ',', '.') ?></div>
          </div>

          <!-- Upload Bukti Transfer -->
          <div class="mb-4">
            <label for="bukti" class="font-bold block mb-2">Upload Bukti Transfer</label>
            <input type="file" name="bukti_bayar" id="bukti" class="w-full border p-2 rounded bg-white" required>
          </div>

          <!-- Tombol -->
          <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
            Konfirmasi Pembayaran
          </button>
        </form>
      </div>
    </div>
  </main>

</body>
</html>
