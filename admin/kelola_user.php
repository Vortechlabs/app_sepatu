<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../autentikasi/admin_login.php');
    exit;
}

require_once '../koneksi.php';

// Ambil data admin
$adminName = $_SESSION['nama_admin'] ?? 'Admin';
$adminUsername = $_SESSION['username_admin'] ?? 'Admin';

$queryAdmin = mysqli_query($koneksi, "SELECT * FROM admin WHERE username = '$adminUsername'");
$adminData = mysqli_fetch_assoc($queryAdmin);

// Query untuk mendapatkan user
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';

$where = [];
$where[] = "(username LIKE '%$search%' OR email LIKE '%$search%' OR no_telepon LIKE '%$search%')";
$where_clause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$query = "SELECT * FROM users $where_clause ORDER BY id_users DESC";
$result = mysqli_query($koneksi, $query);

// Hitung total user
$total_users_query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM users");
$total_users = mysqli_fetch_assoc($total_users_query)['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna - STEP UP ADMIN</title>
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
                        <a href="#" class="flex items-center px-4 py-2 text-white bg-blue-800 rounded-lg">
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
                    <h2 class="ml-4 text-xl font-semibold text-gray-800">Kelola Pengguna</h2>
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
                <!-- Action Bar -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                    <div class="flex gap-4">
                    <form method="GET" class="relative w-full md:w-64 mb-4 md:mb-0">
                        <input type="text" name="search" placeholder="Cari pengguna..." 
                               value="<?= htmlspecialchars($search) ?>" 
                               class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </form>
                    <a href="./kelola_user.php">
                    <button class="bg-black text-white px-4 py-2 rounded-md">semua pelanggan</button>
                    </a>
                    </div>
                    <div class="flex space-x-2 w-full md:w-auto">
                        <a href="tambah_user.php" class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg flex items-center">
                            <i class="fas fa-plus mr-2"></i>
                            Tambah Pengguna
                        </a>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Pengguna
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Telepon
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Bergabung
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Peran
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php while ($user = mysqli_fetch_assoc($result)): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-full" src="../<?= !empty($user['image']) ? htmlspecialchars($user['image']) : '../assets/default-profile.png' ?>" alt="<?= htmlspecialchars($user['username']) ?>">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($user['nama'] ?? $user['username']) ?></div>
                                                <div class="text-sm text-gray-500">@<?= htmlspecialchars($user['username']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?= htmlspecialchars($user['email']) ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500"><?= htmlspecialchars($user['no_telepon'] ?? '-') ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500"><?= date('d M Y', strtotime($user['bergabung'])) ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?= $user['role'] == 'admin' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' ?>">
                                            pelanggan
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="edit_user.php?id=<?= $user['id_users'] ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="hapus_user.php?id=<?= $user['id_users'] ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Yakin ingin menghapus pengguna ini?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
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
                                    Menampilkan <span class="font-medium">1</span> sampai <span class="font-medium"><?= mysqli_num_rows($result) ?></span> dari <span class="font-medium"><?= $total_users ?></span> hasil
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>