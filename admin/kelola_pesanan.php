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

// Proses update status pesanan dan pembayaran
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pesanan = intval($_POST['id_pesanan']);
    
    if (isset($_POST['update_status'])) {
        $status_baru = mysqli_real_escape_string($koneksi, $_POST['status']);
        
        $update_query = "UPDATE pesanan SET status_pesanan = '$status_baru' WHERE id_pesanan = $id_pesanan";
        if (mysqli_query($koneksi, $update_query)) {
            $_SESSION['success'] = "Status pesanan berhasil diperbarui";
        } else {
            $_SESSION['error'] = "Gagal memperbarui status pesanan: " . mysqli_error($koneksi);
        }
    }
    
    if (isset($_POST['update_payment_status'])) {
        $payment_status = mysqli_real_escape_string($koneksi, $_POST['payment_status']);
        
        $update_payment_query = "UPDATE pembayaran SET status_pembayaran = '$payment_status' WHERE id_pesanan = $id_pesanan";
        if (mysqli_query($koneksi, $update_payment_query)) {
            $_SESSION['success'] = "Status pembayaran berhasil diperbarui";
            
            // Jika pembayaran dikonfirmasi, update status pesanan menjadi diproses
            if ($payment_status == 'paid') {
                mysqli_query($koneksi, "UPDATE pesanan SET status_pesanan = 'diproses' WHERE id_pesanan = $id_pesanan");
            }
        } else {
            $_SESSION['error'] = "Gagal memperbarui status pembayaran: " . mysqli_error($koneksi);
        }
    }
}

// Filter dan pencarian
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($koneksi, $_GET['status']) : '';
$payment_status_filter = isset($_GET['payment_status']) ? mysqli_real_escape_string($koneksi, $_GET['payment_status']) : '';
$date_filter = isset($_GET['date']) ? mysqli_real_escape_string($koneksi, $_GET['date']) : '';

$where = [];
if ($search) $where[] = "(p.id_pesanan LIKE '%$search%' OR u.username LIKE '%$search%' OR u.email LIKE '%$search%')";
if ($status_filter) $where[] = "p.status_pesanan = '$status_filter'";
if ($payment_status_filter) $where[] = "pb.status_pembayaran = '$payment_status_filter'";
if ($date_filter) $where[] = "DATE(p.tanggal_pesanan) = '$date_filter'";
$where_clause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Query untuk mendapatkan pesanan dengan informasi pembayaran
$query = "SELECT p.*, u.username, u.email, u.image as user_gambar, 
                 pb.status_pembayaran, pb.bukti_bayar, pb.metode_pembayaran, pb.no_bayar, pb.tanggal_pembayaran
          FROM pesanan p
          JOIN users u ON p.id_users = u.id_users
          LEFT JOIN pembayaran pb ON p.id_pesanan = pb.id_pesanan
          $where_clause
          ORDER BY p.tanggal_pesanan DESC";
$result = mysqli_query($koneksi, $query);

// Hitung total pesanan
$total_orders_query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pesanan");
$total_orders = mysqli_fetch_assoc($total_orders_query)['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan - STEP UP ADMIN</title>
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
        
        function showPaymentProof(imageUrl) {
            if (imageUrl) {
                document.getElementById('paymentProofImage').src = '../assets/bukti/' + imageUrl;
                document.getElementById('paymentProofModal').classList.remove('hidden');
            } else {
                alert('Bukti pembayaran tidak tersedia');
            }
        }
        
        function closeModal() {
            document.getElementById('paymentProofModal').classList.add('hidden');
        }
    </script>
    <style>
        .order-status, .payment-status {
            width: 180px;
        }
    </style>
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
                        <a href="#" class="flex items-center px-4 py-2 text-white bg-blue-800 rounded-lg">
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
                <!-- Filter and Action Bar -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <form method="GET" class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
                        <div class="relative w-full md:w-64">
                            <input type="text" name="search" placeholder="Cari pesanan..." 
                                   value="<?= htmlspecialchars($search) ?>" 
                                   class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                        <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-2 w-full md:w-auto">
                            <select name="status" class="border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua Status Pesanan</option>
                                <option value="pending" <?= $status_filter == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="diproses" <?= $status_filter == 'diproses' ? 'selected' : '' ?>>Diproses</option>
                                <option value="dikirim" <?= $status_filter == 'dikirim' ? 'selected' : '' ?>>Dikirim</option>
                                <option value="diterima" <?= $status_filter == 'diterima' ? 'selected' : '' ?>>Diterima</option>
                                <option value="selesai" <?= $status_filter == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                <option value="dibatalkan" <?= $status_filter == 'dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                            </select>
                            <select name="payment_status" class="border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua Status Pembayaran</option>
                                <option value="pending" <?= $payment_status_filter == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="paid" <?= $payment_status_filter == 'paid' ? 'selected' : '' ?>>Dibayar</option>
                                <option value="failed" <?= $payment_status_filter == 'failed' ? 'selected' : '' ?>>Gagal</option>
                                <option value="expired" <?= $payment_status_filter == 'expired' ? 'selected' : '' ?>>Kadaluarsa</option>
                                <option value="dibatalkan" <?= $payment_status_filter == 'dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                            </select>
                            <input type="date" name="date" value="<?= htmlspecialchars($date_filter) ?>" 
                                   class="border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <button type="submit" class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg">
                                Filter
                            </button>
                            <?php if ($search || $status_filter || $payment_status_filter || $date_filter): ?>
                                <a href="kelola_pesanan.php" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100">
                                    Reset
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <!-- Orders Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID Pesanan
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Pelanggan
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status Pesanan
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status Pembayaran
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php while ($pesanan = mysqli_fetch_assoc($result)): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-blue-900">#ORD-<?= $pesanan['id_pesanan'] ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?= date('d M Y', strtotime($pesanan['tanggal_pesanan'])) ?></div>
                                        <div class="text-xs text-gray-500"><?= date('H:i', strtotime($pesanan['tanggal_pesanan'])) ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-full" src="../<?= !empty($pesanan['user_gambar']) ? htmlspecialchars($pesanan['user_gambar']) : 'assets/default-profile.png' ?>" alt="<?= htmlspecialchars($pesanan['username']) ?>">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($pesanan['username']) ?></div>
                                                <div class="text-sm text-gray-500"><?= htmlspecialchars($pesanan['email']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <form method="POST" class="order-status">
                                            <input type="hidden" name="id_pesanan" value="<?= $pesanan['id_pesanan'] ?>">
                                            <select name="status" onchange="this.form.submit()" 
                                                    class="form-select block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md 
                                                    <?= $pesanan['status_pesanan'] == 'pending' ? 'bg-purple-100 text-purple-800 border-purple-200' : 
                                                       ($pesanan['status_pesanan'] == 'diproses' ? 'bg-yellow-100 text-yellow-800 border-yellow-200' : 
                                                       ($pesanan['status_pesanan'] == 'dikirim' ? 'bg-blue-100 text-blue-800 border-blue-200' : 
                                                       ($pesanan['status_pesanan'] == 'diterima' ? 'bg-blue-300 text-blue-800 border-blue-200' : 
                                                       ($pesanan['status_pesanan'] == 'selesai' ? 'bg-green-100 text-green-800 border-green-200' : 
                                                       'bg-red-100 text-red-800 border-red-200')))) ?>">
                                                <option value="pending" <?= $pesanan['status_pesanan'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="diproses" <?= $pesanan['status_pesanan'] == 'diproses' ? 'selected' : '' ?>>Diproses</option>
                                                <option value="dikirim" <?= $pesanan['status_pesanan'] == 'dikirim' ? 'selected' : '' ?>>Dikirim</option>
                                                <option value="diterima" <?= $pesanan['status_pesanan'] == 'diterima' ? 'selected' : '' ?>>Diterima</option>
                                                <option value="selesai" <?= $pesanan['status_pesanan'] == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                                <option value="dibatalkan" <?= $pesanan['status_pesanan'] == 'dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                                            </select>
                                            <input type="hidden" name="update_status" value="1">
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if (!empty($pesanan['status_pembayaran'])): ?>
                                        <form method="POST" class="payment-status">
                                            <input type="hidden" name="id_pesanan" value="<?= $pesanan['id_pesanan'] ?>">
                                            <select name="payment_status" onchange="this.form.submit()" 
                                                    class="form-select block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md 
                                                    <?= $pesanan['status_pembayaran'] == 'pending' ? 'bg-purple-100 text-purple-800 border-purple-200' : 
                                                       ($pesanan['status_pembayaran'] == 'dibayar' ? 'bg-green-100 text-green-800 border-green-200' : 
                                                       ($pesanan['status_pembayaran'] == 'dibatalkan' ? 'bg-red-100 text-red-800 border-red-200' : 
                                                       ($pesanan['status_pembayaran'] == 'belum dibayar' ? 'bg-yellow-100 text-yellow-800 border-yellow-200' : 
                                                       'bg-gray-100 text-gray-800 border-gray-200'))) ?>">
                                                <option value="pending" <?= $pesanan['status_pembayaran'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="dibayar" <?= $pesanan['status_pembayaran'] == 'dibayar' ? 'selected' : '' ?>>Dibayar</option>
                                                <option value="belum dibayar" <?= $pesanan['status_pembayaran'] == 'belum dibayar' ? 'selected' : '' ?>>Belum Dibayar</option>
                                                <option value="dibatalkan" <?= $pesanan['status_pembayaran'] == 'dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                                            </select>
                                            <input type="hidden" name="update_payment_status" value="1">
                                        </form>
                                        <?php else: ?>
                                        <span class="text-sm text-gray-500">Tidak ada data</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="detail_pesanan.php?id=<?= $pesanan['id_pesanan'] ?>" class="text-blue-600 hover:text-blue-900 mr-3" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if (!empty($pesanan['bukti_bayar'])): ?>
                                        <button onclick="showPaymentProof('<?= htmlspecialchars($pesanan['bukti_bayar']) ?>')" 
                                                class="text-green-600 hover:text-green-900 mr-3" title="Lihat Bukti Bayar">
                                            <i class="fas fa-receipt"></i>
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Previous
                            </a>
                            <a href="#" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Next
                            </a>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Menampilkan <span class="font-medium">1</span> sampai <span class="font-medium"><?= mysqli_num_rows($result) ?></span> dari <span class="font-medium"><?= $total_orders ?></span> hasil
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Payment Proof Modal -->
    <div id="paymentProofModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-2xl max-h-screen overflow-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Bukti Pembayaran</h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <img id="paymentProofImage" src="" alt="Bukti Pembayaran" class="w-full h-auto rounded">
            <div class="mt-4 flex justify-end">
                <button onclick="closeModal()" class="px-4 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</body>
</html>