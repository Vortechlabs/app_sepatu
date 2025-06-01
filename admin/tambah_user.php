<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $no_telepon = mysqli_real_escape_string($koneksi, $_POST['no_telepon']);

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $image = 'assets/default-profile.png';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/profil/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        $new_filename = uniqid() . '.' . $imageFileType;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image = "assets/profil/" . $new_filename;
        }
    }

    $query = "INSERT INTO users (username, email, password, no_telepon, image, bergabung) 
              VALUES ('$username', '$email', '$hashed_password', '$no_telepon', '$image', NOW())";
    
    if (mysqli_query($koneksi, $query)) {
        $_SESSION['success'] = "Pengguna berhasil ditambahkan";
        header("Location: kelola_user.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal menambahkan pengguna: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - STEP UP ADMIN</title>
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
                        <a href="./kelola_user.php" class="flex items-center px-4 py-2 text-white bg-blue-800 rounded-lg">
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
                    <h2 class="ml-4 text-xl font-semibold text-gray-800">Kelola Produk</h2>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <img src="../<?= htmlspecialchars($adminData['gambar']) ?>" class="w-8 h-8 rounded-full" alt="<?= htmlspecialchars($adminName) ?>">
                        <span class="ml-2 text-sm font-medium text-gray-700"><?= htmlspecialchars($adminName) ?></span>
                    </div>
                </div>
            </header>
            <main class="flex-1 overflow-y-auto p-6 bg-gray-100">
                <div class="max-w-4xl mx-auto">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-2xl font-bold mb-6">Tambah Pengguna Baru</h2>
                        
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" enctype="multipart/form-data" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                                    <input type="text" name="username" required 
                                           class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" name="email" required 
                                           class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                    <input type="password" name="password" required 
                                           class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                                    <input type="tel" name="no_telepon" 
                                           class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto Profil</label>
                                    <input type="file" name="image" 
                                           class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                            
                            <div class="flex justify-end space-x-4 pt-4">
                                <a href="kelola_user.php" 
                                   class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100 transition duration-300">
                                    Batal
                                </a>
                                <button type="submit" 
                                        class="px-4 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800 transition duration-300">
                                    Simpan Pengguna
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>