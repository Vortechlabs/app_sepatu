<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../autentikasi/admin_login.php');
    exit;
}

require_once '../koneksi.php';
$adminName = $_SESSION['nama_admin'] ?? 'Admin';
$adminUsername = $_SESSION['username_admin'] ?? 'Admin';

$queryAdmin = mysqli_query($koneksi, "SELECT * FROM admin WHERE username = '$adminUsername'");
$adminData = mysqli_fetch_assoc($queryAdmin);

$brand_query = mysqli_query($koneksi, "SELECT * FROM brand");
$brandes = mysqli_fetch_all($brand_query, MYSQLI_ASSOC);

$ukuran_query = mysqli_query($koneksi, "SELECT * FROM ukuran");
$daftar_ukuran = mysqli_fetch_all($ukuran_query, MYSQLI_ASSOC);

$warna_query = mysqli_query($koneksi, "SELECT * FROM warna");
$daftar_warna = mysqli_fetch_all($warna_query, MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $sku = mysqli_real_escape_string($koneksi, $_POST['sku']);
    $harga = mysqli_real_escape_string($koneksi, $_POST['harga']);
    $stok = mysqli_real_escape_string($koneksi, $_POST['stok']);
    $id_brand = mysqli_real_escape_string($koneksi, $_POST['brand']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $ukuran_arr = $_POST['ukuran'];
    $ukuran = implode(' ', $ukuran_arr);
    $warna_arr = $_POST['warna'];
    $warna = implode(' ', $warna_arr);

    $gambar = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "../assets/produk/";
        $target_file = $target_dir . basename($_FILES["gambar"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        $new_filename = uniqid() . '.' . $imageFileType;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
            $gambar = "assets/produk/" . $new_filename;
        }
    }

    $query = "INSERT INTO sepatu (nama_sepatu, sku, gambar, harga, stok, id_brand, deskripsi, ukuran, warna) 
              VALUES ('$nama', '$sku', '$gambar', '$harga', '$stok', '$id_brand', '$deskripsi', '$ukuran', '$warna')";
    
    if (mysqli_query($koneksi, $query)) {
        $_SESSION['success'] = "Produk berhasil ditambahkan";
        header("Location: kelola_produk.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal menambahkan produk: " . mysqli_error($koneksi);
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
                        <a href="./kelola_produk.php" class="flex items-center px-4 py-2 text-white bg-blue-800 rounded-lg">
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

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-6 bg-gray-100">
                <div class="max-w-4xl mx-auto">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-2xl font-bold mb-6">Tambah Produk Baru</h2>
                        
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" enctype="multipart/form-data" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
                                    <input type="text" name="nama" required class="w-full px-4 py-2 border rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                                    <input type="text" name="sku" value="SPT-" required class="w-full px-4 py-2 border rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
                                    <input type="number" name="harga" required class="w-full px-4 py-2 border rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
                                    <input type="number" name="stok" required class="w-full px-4 py-2 border rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">brand</label>
                                    <select name="brand" required class="w-full px-4 py-2 border rounded-lg">
                                        <option value="">Pilih brand</option>
                                        <?php foreach ($brandes as $brand): ?>
                                            <option value="<?= $brand['id_brand'] ?>"><?= htmlspecialchars($brand['brand']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Produk</label>
                                    <input type="file" name="gambar" required class="w-full px-4 py-2 border rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Ukuran</label>
                                    <select name="ukuran[]" multiple required class="w-full px-4 py-2 border rounded-lg">
                                        <?php foreach ($daftar_ukuran as $u): ?>
                                            <option value="<?= htmlspecialchars($u['ukuran']) ?>"><?= htmlspecialchars($u['ukuran']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-gray-500">Gunakan Ctrl (Windows) atau Cmd (Mac) untuk pilih lebih dari satu</small>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Warna</label>
                                    <select name="warna[]" multiple required class="w-full px-4 py-2 border rounded-lg">
                                        <?php foreach ($daftar_warna as $w): ?>
                                            <option value="<?= htmlspecialchars($w['warna']) ?>"><?= htmlspecialchars($w['warna']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-gray-500">Gunakan Ctrl (Windows) atau Cmd (Mac) untuk pilih lebih dari satu</small>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                                    <textarea name="deskripsi" rows="4" required class="w-full px-4 py-2 border rounded-lg"></textarea>
                                </div>
                            </div>
                            
                            <div class="flex justify-end space-x-4 pt-4">
                                <a href="kelola_produk.php" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100">Batal</a>
                                <button type="submit" class="px-4 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800">Simpan Produk</button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>