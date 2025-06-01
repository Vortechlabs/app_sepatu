<?php
session_start();
require '../../koneksi.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['id_users'])) {
    header('Location: ../../login.php');
    exit;
}

$id_users = intval($_SESSION['id_users']);

$query = "
    SELECT 
        p.id_pesanan, 
        p.tanggal_pesanan, 
        p.status_pesanan, 
        py.status_pembayaran, 
        py.bukti_bayar,
        SUM(dp.subtotal) AS total_produk,
        GROUP_CONCAT(CONCAT(s.nama_sepatu, ' (', dp.jumlah, 'x)') SEPARATOR ' + ') AS daftar_produk
    FROM pesanan p
    JOIN detail_pesanan dp ON p.id_pesanan = dp.id_pesanan
    JOIN sepatu s ON dp.id_sepatu = s.id_sepatu
    LEFT JOIN pembayaran py ON p.id_pesanan = py.id_pesanan
    WHERE p.id_users = $id_users
    GROUP BY p.id_pesanan
    ORDER BY p.tanggal_pesanan DESC
";

$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <script src="https://cdn.tailwindcss.com">
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&amp;display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="style.css">
</head>

<body class="font-roboto">
    <header class="bg-white shadow-md fixed w-full">
        <div class="container mx-auto flex justify-between items-center py-4 px-6">
            <div class="text-2xl font-bold text-blue-900">
                STEP UP
            </div>
            <nav class="space-x-4">
                <a class="text-gray-700 hover:text-blue-900" href="../../index.php">
                    Home
                </a>
                <a class="text-gray-700 hover:text-blue-900" href="../../about.php">
                    About
                </a>
                <a class="text-gray-700 hover:text-blue-900" href="../../produk.php">
                    Produk
                </a>
                <a class="text-gray-700 hover:text-blue-900" href="../pesanan/keranjang.php">
                    <i class="fas fa-shopping-cart">
                    </i>
                </a>
                <?php if (isset($_SESSION['id_users'])): ?>
                    <!-- Jika user sudah login -->
                    <a href="./profil.php" class="text-gray-700 hover:text-blue-900">
                        <i class="fas fa-user"></i>
                    </a>
                <?php else: ?>
                    <!-- Jika belum login -->
                    <a href="../../autentikasi/login.php"
                        class="bg-blue-900 text-white px-4 py-1 rounded hover:bg-blue-800">
                        Login
                    </a>
                <?php endif; ?>

            </nav>
        </div>
    </header>

    <div class="pt-20"></div>
    <main class="max-w-6xl mx-auto px-6 py-8 bg-white rounded-lg shadow-md ">
        <h2 class="text-2xl font-semibold text-blue-900 mb-6">Riwayat Pesanan Anda</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase">ID Pesanan</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase">Produk</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase">Total Harga</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase">Status Pembayaran</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase">Status Pengiriman</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase">Bukti Bayar</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-gray-900">#<?= 'INV' . str_pad($row['id_pesanan'], 5, '0', STR_PAD_LEFT); ?></td>
                            <td class="px-6 py-4 text-gray-500"><?= date('d M Y', strtotime($row['tanggal_pesanan'])) ?>
                            </td>
                            <td class="px-6 py-4 text-gray-900"><?= htmlspecialchars($row['daftar_produk']) ?></td>
                            <td class="px-6 py-4 text-gray-900">
                                Rp <?= number_format($row['total_produk'], 0, ',', '.') ?>
                            </td>

                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                <?= $row['status_pembayaran'] === 'belum dibayar' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' ?>">
                                    <?= ucfirst($row['status_pembayaran'] ?? 'belum dibayar') ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <?= ucfirst($row['status_pesanan']) ?>
                                </span>
                            </td>
                           <td class="px-6 py-4">
                                <?php if ($row['status_pesanan'] === 'dibatalkan'): ?>
                                    <span class="text-red-500 italic">Pesanan dibatalkan</span>
                                <?php elseif (!empty($row['bukti_bayar'])): ?>
                                    <a href="../../assets/bukti/<?= htmlspecialchars($row['bukti_bayar']) ?>" target="_blank"
                                        class="text-blue-500 underline">Lihat</a>
                                <?php else: ?>
                                    <span class="text-red-400 italic">Belum ada</span>
                                <?php endif; ?>
                            </td>

                            <td class="px-6 py-4 space-x-2 space-y-2">
                               <?php 
                                $can_pay = in_array($row['status_pembayaran'], ['belum dibayar', 'pending']); 
                                if ($can_pay && empty($row['bukti_bayar'])): ?>
                                    <a href="../pesanan/bayar.php?id_pesanan=<?= $row['id_pesanan'] ?>" 
                                    class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 text-sm">
                                        Bayar
                                    </a>
                                <?php endif; ?>


                                <?php if ($row['status_pesanan'] === 'pending' || $row['status_pesanan'] === 'diproses'): ?>
                                    <form action="../pesanan/batalkan_pesanan.php" method="POST" class="inline">
                                        <input type="hidden" name="id_pesanan" value="<?= $row['id_pesanan'] ?>">
                                        <button type="submit" class="bg-red-600 m-1 text-white px-3 py-1 rounded hover:bg-red-700 text-sm"
                                            onclick="return confirm('Yakin ingin membatalkan pesanan ini?')">
                                            Batalkan
                                        </button>
                                    </form>
                                <?php elseif ($row['status_pesanan'] === 'dikirim'): ?>
                                    <form action="../pesanan/konfirmasi_diterima.php" method="POST" class="inline">
                                        <input type="hidden" name="id_pesanan" value="<?= $row['id_pesanan'] ?>">
                                        <button type="submit" class="bg-green-600 m-1 text-white px-3 py-1 rounded hover:bg-green-700 text-sm"
                                            onclick="return confirm('Apakah Anda sudah menerima pesanan?')">
                                            Diterima
                                        </button>
                                    </form>
                                <?php elseif ($row['status_pesanan'] === 'selesai'): ?>
                                    <span class="text-sm text-green-700 font-semibold">Selesai</span>
                                <?php elseif ($row['status_pesanan'] === 'dibatalkan'): ?>
                                    <span class="text-sm text-red-500 font-semibold">Dibatalkan</span>
                                <?php endif; ?>
                            </td>


                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>