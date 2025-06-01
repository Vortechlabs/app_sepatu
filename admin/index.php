<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../autentikasi/admin_login.php');
    exit;
}

error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../koneksi.php';

$adminName = $_SESSION['nama_admin'] ?? 'Admin';
$adminUsername = $_SESSION['username_admin'] ?? 'Admin';

$queryAdmin = mysqli_query($koneksi, "SELECT * FROM admin WHERE username = '$adminUsername'");
$adminData = mysqli_fetch_assoc($queryAdmin);

// Total Orders selesai
$total_orders_query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pesanan WHERE status_pesanan = 'selesai'");
$total_orders = mysqli_fetch_assoc($total_orders_query)['total'];

// Total Revenue dari pesanan selesai
$total_revenue_query = mysqli_query($koneksi, "SELECT SUM(total_harga) as revenue FROM pesanan WHERE status_pesanan = 'selesai'");
$total_revenue = mysqli_fetch_assoc($total_revenue_query)['revenue'] ?? 0;

// Jumlah Produk
$total_products_query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM sepatu");
$total_products = mysqli_fetch_assoc($total_products_query)['total'];

// Jumlah Users
$total_users_query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM users");
$total_users = mysqli_fetch_assoc($total_users_query)['total'];

// Recent Orders (4 terakhir)
$recent_orders_query = mysqli_query($koneksi, "SELECT 
    p.id_pesanan,
    p.status_pesanan,
    p.total_harga,
    p.tanggal_pesanan,
    s.nama_sepatu,
    dp.ukuran,
    dp.warna
FROM pesanan p
JOIN detail_pesanan dp ON p.id_pesanan = dp.id_pesanan
JOIN sepatu s ON dp.id_sepatu = s.id_sepatu
ORDER BY p.tanggal_pesanan DESC
LIMIT 4
");

// Produk Terbaru (3 terakhir)
$top_products_query = mysqli_query($koneksi, "SELECT * FROM sepatu ORDER BY id_sepatu DESC LIMIT 3");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - STEP UP</title>
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
                        <a href="#" class="flex items-center px-4 py-2 text-white bg-blue-800 rounded-lg">
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
                        <a href="./kelola_pesanan.php" class="flex items-center px-4 py-2 text-blue-200 hover:text-white hover:bg-blue-800 rounded-lg">
                            <i class="fas fa-receipt mr-3"></i>
                            Orders
                        </a>
                    </nav>
                </div>
                <div class="p-4 border-t border-blue-800">
                    <div class="flex items-center">
                        <img src="../<?= $adminData['gambar'] ?>" class="w-10 h-10 rounded-full" alt="<?= $adminName ?>">


                        <div class="ml-3">
                            <p class="text-sm font-medium"><?= $adminName ?></p>
                            
                            <p class="text-xs text-blue-200"><?= $adminUsername ?></p>
                        </div>
                    </div>
                    <a href="../autentikasi/admin_logout.php" class="mt-4 w-full flex items-center justify-center px-4 py-2 text-sm text-white bg-blue-700 hover:bg-blue-600 rounded-lg">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Logout
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
                    <h2 class="ml-4 text-xl font-semibold text-gray-800">Dashboard</h2>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <img src="../<?= $adminData['gambar'] ?>" class="w-10 h-10 rounded-full" alt="<?= $adminName ?>">
                        <span class="ml-2 text-sm font-medium text-gray-700">
                            <p class="text-sm font-medium"><?= $adminName ?></p>
                        </span>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-6 bg-gray-100">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500">Total Orders</p>
                                <h3 class="text-2xl font-bold"><?= $total_orders ?></h3>
                                <p class="text-green-500 text-sm mt-1">
                                    <i class="fas fa-arrow-up mr-1"></i> 12% from last month
                                </p>
                            </div>
                            <div class="p-3 rounded-full bg-blue-100 text-blue-900">
                                <i class="fas fa-shopping-cart text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500">Total Revenue</p>
                                <h3 class="text-2xl font-bold">Rp <?= number_format($total_revenue, 0, ',', '.') ?></h3>
                                <p class="text-green-500 text-sm mt-1">
                                    <i class="fas fa-arrow-up mr-1"></i> 8% from last month
                                </p>
                            </div>
                            <div class="p-3 rounded-full bg-green-100 text-green-900">
                                <i class="fas fa-wallet text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500">Products</p>
                                <h3 class="text-2xl font-bold"><?= $total_products ?></h3>
                                <p class="text-green-500 text-sm mt-1">
                                    <i class="fas fa-arrow-up mr-1"></i> 3 new this week
                                </p>
                            </div>
                            <div class="p-3 rounded-full bg-purple-100 text-purple-900">
                                <i class="fas fa-shoe-prints text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500">Customers</p>
                                <h3 class="text-2xl font-bold"><?= $total_users ?></h3>
                                <p class="text-green-500 text-sm mt-1">
                                    <i class="fas fa-arrow-up mr-1"></i> 5% from last month
                                </p>
                            </div>
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-900">
                                <i class="fas fa-users text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders & Top Products -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Recent Orders -->
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Recent Orders</h3>
                        </div>
                        <div class="divide-y divide-gray-200">
                            <?php while ($order = mysqli_fetch_assoc($recent_orders_query)): ?>
                                <div class="px-6 py-4 hover:bg-gray-50">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium">#ORD-<?= $order['id_pesanan'] ?></p>
                                            <p class="text-sm text-gray-500">
                                                <?= $order['nama_sepatu'] ?> - Size <?= $order['ukuran'] ?> - Warna <?= $order['warna'] ?>
                                            </p>

                                        </div>
                                        <div class="text-right">
                                            <p class="font-medium">Rp <?= number_format($order['total_harga'], 0, ',', '.') ?></p>
                                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800"><?= ucfirst($order['status_pesanan']) ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>

                        </div>
                        <div class="px-6 py-3 bg-gray-50 text-right">
                            <a href="./kelola_pesanan.php" class="text-sm font-medium text-blue-900 hover:text-blue-700">View All Orders</a>
                        </div>
                    </div>

                    <!-- Top Products -->
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Top Products</h3>
                        </div>
                        <div class="divide-y divide-gray-200">
                        <?php while ($produk = mysqli_fetch_assoc($top_products_query)): ?>
                            <div class="px-6 py-4 hover:bg-gray-50">
                                <div class="flex items-center">
                                    <img src="../<?= $produk['gambar'] ?>" class="w-16 h-16 object-cover rounded" alt="<?= $produk['nama_sepatu'] ?>">
                                    <div class="ml-4">
                                        <p class="font-medium"><?= $produk['nama_sepatu'] ?></p>
                                        <p class="text-sm font-medium text-blue-900">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>

                        </div>
                        <div class="px-6 py-3 bg-gray-50 text-right">
                            <a href="./kelola_produk.php" class="text-sm font-medium text-blue-900 hover:text-blue-700">View All Products</a>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>
</body>
</html>