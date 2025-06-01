<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../autentikasi/admin_login.php');
    exit;
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../koneksi.php';

// Ambil data admin
$adminName = $_SESSION['nama_admin'] ?? 'Admin';
$adminUsername = $_SESSION['username_admin'] ?? 'Admin';

$queryAdmin = mysqli_query($koneksi, "SELECT * FROM admin WHERE username = '$adminUsername'");
$adminData = mysqli_fetch_assoc($queryAdmin);

// Ambil ID pesanan dari URL
$id_pesanan = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_pesanan <= 0) {
    header("Location: kelola_pesanan.php");
    exit;
}

// Ambil data pesanan
$query = "
SELECT p.*, u.username, u.email, u.no_telepon, u.image as user_gambar, 
       a.nama_penerima, a.alamat, a.no_telp,
       k.jasa_kirim, k.harga as harga_kurir,
       pb.status_pembayaran, mb.nomor_akun as nomor_rekening,
       mb.provider
FROM pesanan p
JOIN users u ON p.id_users = u.id_users
LEFT JOIN alamat a ON p.id_alamat = a.id_alamat
LEFT JOIN kurir k ON p.id_kurir = k.id_kurir
LEFT JOIN pembayaran pb ON p.id_pesanan = pb.id_pesanan
LEFT JOIN metode_bayar mb ON p.id_metode_pembayaran = mb.id_metode_bayar
WHERE p.id_pesanan = $id_pesanan
";
$result = mysqli_query($koneksi, $query);
$pesanan = mysqli_fetch_assoc($result);

if (!$pesanan) {
    $_SESSION['error'] = "Pesanan tidak ditemukan";
    header("Location: kelola_pesanan.php");
    exit;
}

// Ambil item pesanan
$query_items = "SELECT dp.*, s.nama_sepatu, s.gambar 
                FROM detail_pesanan dp
                JOIN sepatu s ON dp.id_sepatu = s.id_sepatu
                WHERE dp.id_pesanan = $id_pesanan";
$result_items = mysqli_query($koneksi, $query_items);
$items = mysqli_fetch_all($result_items, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan - STEP UP ADMIN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"/>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1E40AF', 
                        secondary: '#1F2937', 
                        accent: '#111827' 
                    }
                }
            }
        }
    </script>
</head>
<body class="font-roboto bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64 bg-primary text-white">
                <div class="flex items-center justify-center h-16 px-4 border-b border-blue-800">
                    <h1 class="text-xl font-bold">STEP UP ADMIN</h1>
                </div>
                <div class="flex flex-col flex-grow px-4 py-4">
                    <nav class="flex-1 space-y-2">
                        <a href="./index.php" class="flex items-center px-4 py-2 text-blue-200 hover:text-white hover:bg-blue-800 rounded-lg">
                            <i class="fas fa-tachometer-alt mr-3"></i>
                            Dashboard
                        </a>
                        <a href="./kelola_produk.php" class="flex items-center px-4 py-2 text-blue-200 hover:text-white hover:bg-blue-800 rounded-lg">
                            <i class="fas fa-shopping-bag mr-3"></i>
                            Products
                        </a>
                        <a href="./kelola_user.php" class="flex items-center px-4 py-2 text-blue-200 hover:text-white hover:bg-blue-800 rounded-lg">
                            <i class="fas fa-users mr-3"></i>
                            Customers
                        </a>
                        <a href="./kelola_pesanan.php" class="flex items-center px-4 py-2 text-white bg-blue-800 rounded-lg">
                            <i class="fas fa-receipt mr-3"></i>
                            Orders
                        </a>
                    </nav>
                </div>
                <div class="p-4 border-t border-blue-800">
                    <div class="flex items-center">
                        <img src="../<?= htmlspecialchars($adminData['gambar']) ?>" class="w-10 h-10 rounded-full" alt="<?= htmlspecialchars($adminName) ?>">
                        <div class="ml-3">
                            <p class="text-sm font-medium"><?= htmlspecialchars($adminName) ?></p>
                            <p class="text-xs text-blue-200"><?= htmlspecialchars($adminUsername) ?></p>
                        </div>
                    </div>
                    <a href="../autentikasi/admin_logout.php" class="mt-4 w-full flex items-center justify-center px-4 py-2 text-sm text-white bg-blue-700 hover:bg-blue-600 rounded-lg">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Keluar
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex flex-col flex-1 overflow-hidden">
            <!-- Top Navigation -->
            <header class="flex items-center justify-between h-16 px-6 bg-white shadow-sm">
                <div class="flex items-center">
                    <button class="md:hidden text-gray-500 focus:outline-none">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h2 class="ml-4 text-xl font-semibold text-gray-800">Kelola Pesanan</h2>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <img src="../<?= htmlspecialchars($adminData['gambar']) ?>" class="w-8 h-8 rounded-full" alt="<?= htmlspecialchars($adminName) ?>">
                        <span class="ml-2 text-sm font-medium text-gray-700"><?= htmlspecialchars($adminName) ?></span>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-6 bg-gray-100">
                <div class="max-w-6xl mx-auto">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold">Detail Pesanan #ORD-<?= $pesanan['id_pesanan'] ?></h2>
                        <a href="kelola_pesanan.php" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100">
                            Kembali
                        </a>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Order Summary -->
                        <div class="lg:col-span-2">
                            <div class="bg-white rounded-lg shadow p-6 mb-6">
                                <h3 class="text-lg font-semibold mb-4">Informasi Pesanan</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Tanggal Pesanan</p>
                                        <p class="font-medium"><?= date('d M Y H:i', strtotime($pesanan['tanggal_pesanan'])) ?></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Status Pesanan</p>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?= $pesanan['status_pesanan'] == 'pending' ? 'bg-purple-100 text-purple-800' : 
                                               ($pesanan['status_pesanan'] == 'diproses' ? 'bg-yellow-100 text-yellow-800' : 
                                               ($pesanan['status_pesanan'] == 'dikirim' ? 'bg-blue-100 text-blue-800' : 
                                               ($pesanan['status_pesanan'] == 'diterima' ? 'bg-blue-400 text-blue-800' : 
                                               ($pesanan['status_pesanan'] == 'selesai' ? 'bg-green-100 text-green-800' : 
                                               ($pesanan['status_pesanan'] == 'dibatalkan' ? 'bg-red-100 text-red-800' : 
                                               'bg-red-100 text-red-800'))))) ?>">
                                            <?= $pesanan['status_pesanan'] == 'pending' ? 'Menunggu Pembayaran' : 
                                               ($pesanan['status_pesanan'] == 'diproses' ? 'Diproses' : 
                                               ($pesanan['status_pesanan'] == 'dikirim' ? 'Dikirim' : 
                                               ($pesanan['status_pesanan'] == 'diterima' ? 'Diterima' : 
                                               ($pesanan['status_pesanan'] == 'selesai' ? 'Selesai' : 
                                               ($pesanan['status_pesanan'] == 'dibatalkan' ? 'Dibatalkan' : 'Dibatalkan'))))) ?>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Status Pembayaran</p>
                                        <p class="font-medium"><?= $pesanan['status_pembayaran'] == 'paid' ? 'Dibayar' : 'Belum Dibayar' ?></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Method -->
                            <div class="bg-white rounded-lg shadow p-6 mb-6">
                                <h3 class="text-lg font-semibold mb-4">Metode Pembayaran</h3>
                                <table class="w-full text-left border border-gray-300">
                                    <thead>
                                        <tr class="bg-gray-100">
                                            <th class="px-4 py-2 border">Provider</th>
                                            <th class="px-4 py-2 border">Nomor Rekening</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="px-4 py-2 border"><?= htmlspecialchars($pesanan['provider'] ?? '-') ?></td>
                                            <td class="px-4 py-2 border"><?= htmlspecialchars($pesanan['nomor_rekening'] ?? '-') ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Order Items -->
                            <div class="bg-white rounded-lg shadow p-6 mb-6">
                                <h3 class="text-lg font-semibold mb-4">Item Pesanan</h3>
                                <div class="divide-y divide-gray-200">
                                    <?php foreach ($items as $item): ?>
                                    <div class="py-4 flex">
                                        <div class="flex-shrink-0 h-24 w-24">
                                            <img class="h-24 w-24 rounded object-cover" src="../<?= htmlspecialchars($item['gambar']) ?>" alt="<?= htmlspecialchars($item['nama_sepatu']) ?>">
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <div class="flex justify-between">
                                                <div>
                                                    <h4 class="font-medium"><?= htmlspecialchars($item['nama_sepatu']) ?></h4>
                                                    <p class="text-sm text-gray-500">Ukuran: <?= htmlspecialchars($item['ukuran']) ?></p>
                                                    <p class="text-sm text-gray-500">Warna: <?= htmlspecialchars($item['warna']) ?></p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="font-medium">Rp <?= number_format($item['harga_satuan'], 0, ',', '.') ?></p>
                                                    <p class="text-sm text-gray-500">Qty: <?= $item['jumlah'] ?></p>
                                                    <p class="text-sm text-gray-500">Subtotal: Rp <?= number_format($item['harga_satuan'] * $item['jumlah'], 0, ',', '.') ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Order Summary -->
                            <div class="bg-white rounded-lg shadow p-6">
                                <h3 class="text-lg font-semibold mb-4">Ringkasan Pesanan</h3>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span>Subtotal</span>
                                        <span>Rp <?= number_format($pesanan['total_harga'] - $pesanan['harga_kurir'], 0, ',', '.') ?></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Ongkos Kirim (<?= htmlspecialchars($pesanan['jasa_kirim']) ?>)</span>
                                        <span>Rp <?= number_format($pesanan['harga_kurir'], 0, ',', '.') ?></span>
                                    </div>
                                    
                                    <div class="flex justify-between font-bold text-lg border-t pt-2">
                                        <span>Total</span>
                                        <span>Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Customer and Shipping Info -->
                        <div>
                            <div class="bg-white rounded-lg shadow p-6 mb-6">
                                <h3 class="text-lg font-semibold mb-4">Informasi Pelanggan</h3>
                                <div class="flex items-center mb-4">
                                    <img class="h-12 w-12 rounded-full" src="../<?= !empty($pesanan['user_gambar']) ? htmlspecialchars($pesanan['user_gambar']) : 'assets/default-profile.png' ?>" alt="<?= htmlspecialchars($pesanan['username']) ?>">
                                    <div class="ml-4">
                                        <p class="font-medium"><?= htmlspecialchars($pesanan['username']) ?></p>
                                        <p class="text-sm text-gray-500"><?= htmlspecialchars($pesanan['email']) ?></p>
                                        <p class="text-sm text-gray-500"><?= htmlspecialchars($pesanan['no_telepon']) ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white rounded-lg shadow p-6">
                                <h3 class="text-lg font-semibold mb-4">Alamat Pengiriman</h3>
                                <?php if ($pesanan['nama_penerima']): ?>
                                    <p class="font-medium"><?= htmlspecialchars($pesanan['nama_penerima']) ?></p>
                                    <p class="text-gray-700"><?= htmlspecialchars($pesanan['alamat']) ?></p>
                                    <p class="text-gray-700"><?= htmlspecialchars($pesanan['no_telp']) ?></p>
                                <?php else: ?>
                                    <p class="text-gray-500">Alamat tidak tersedia</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>