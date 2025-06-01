<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("./koneksi.php");

// Ambil semua brand buat dropdown filter
$brand_list = [];
$brand_query = "SELECT id_brand, brand FROM brand"; // Asumsi tabel brand ada dan punya kolom id_brand dan nama_brand
$brand_result = $koneksi->query($brand_query);
if ($brand_result && $brand_result->num_rows > 0) {
    while ($b = $brand_result->fetch_assoc()) {
        $brand_list[] = $b;
    }
}

$brand = isset($_GET['brand']) ? $_GET['brand'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';

$query = "SELECT * FROM sepatu WHERE 1=1";
$params = [];
$types = "";

if ($brand != '') {
    $query .= " AND id_brand = ?";
    $params[] = $brand;
    $types .= "s";
}

if ($search != '') {
    $query .= " AND nama_sepatu LIKE ?";
    $params[] = '%' . $search . '%';
    $types .= "s";
}

if ($sort == 'asc') {
    $query .= " ORDER BY harga ASC";
} elseif ($sort == 'desc') {
    $query .= " ORDER BY harga DESC";
}

if (!empty($params)) {
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $all_products_result = $stmt->get_result();
} else {
    $all_products_result = $koneksi->query($query);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Produk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&amp;display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
</head>

<body class="font-roboto">
    <header class="bg-white shadow-md fixed w-full">
        <div class="container mx-auto flex justify-between items-center py-4 px-6">
            <div class="text-2xl font-bold text-blue-900">STEP UP</div>
            <nav class="space-x-4">
                <a class="text-gray-700 hover:text-blue-900" href="./index.php">Home</a>
                <a class="text-gray-700 hover:text-blue-900" href="./about.php">About</a>
                <a class="text-blue-900 font-bold hover:text-blue-900" href="#">Produk</a>
                <a class="text-gray-700 hover:text-blue-900" href="./user/pesanan/keranjang.php">
                    <i class="fas fa-shopping-cart"></i>
                </a>

                <?php if (isset($_SESSION['id_users'])) : ?>
                    <a href="./user/profil/profil.php" class="text-gray-700 hover:text-blue-900">
                        <i class="fas fa-user "></i>
                    </a>
                <?php else : ?>
                    <a href="./autentikasi/login.php" class="bg-blue-900 text-white px-4 py-1 rounded hover:bg-blue-800">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="container mx-auto px-6 py-8 pt-20">
        <h1 class="text-3xl uppercase text-center font-bold mb-6">
            <?php
            if ($brand != '') {
                // Cari nama brand biar tampil lebih ramah
                $brand_name = '';
                foreach ($brand_list as $b) {
                    if ($b['id_brand'] == $brand) {
                        $brand_name = $b['brand'];
                        break;
                    }
                }
                echo "Produk Brand: " . htmlspecialchars($brand_name ?: $brand);
            } else {
                echo "All Our Products";
            }
            ?>
        </h1>

         <!-- Filter Produk -->
        <div class="max-w-3xl mx-auto mb-8">
            <form method="GET" action="produk.php" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search -->
                <input type="text" name="search" placeholder="Cari nama sepatu..." value="<?= htmlspecialchars($search) ?>"
                    class="border p-2 rounded w-full" />

                <!-- Filter Brand -->
                <select name="brand" class="border p-2 rounded w-full">
                    <option value="">-- Semua Brand --</option>
                    <?php foreach ($brand_list as $b) : ?>
                        <option value="<?= htmlspecialchars($b['id_brand']) ?>" <?= ($b['id_brand'] == $brand) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($b['brand']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <!-- Sort Harga -->
                <select name="sort" class="border p-2 rounded w-full">
                    <option value="">Urutkan Harga</option>
                    <option value="asc" <?= $sort == 'asc' ? 'selected' : '' ?>>Harga Terendah</option>
                    <option value="desc" <?= $sort == 'desc' ? 'selected' : '' ?>>Harga Tertinggi</option>
                </select>

                <!-- Tombol Submit -->
                <div class="md:col-span-3">
                    <button type="submit" class="bg-blue-900 text-white px-4 py-2 rounded w-full hover:bg-blue-800">Terapkan Filter</button>
                </div>
            </form>
        </div>


        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php
            if ($all_products_result && $all_products_result->num_rows > 0) {
                while ($row = $all_products_result->fetch_assoc()) {
                    echo '
                    <a href="./user/produk/detail_produk.php?id_sepatu=' . $row["id_sepatu"] . '" class="hover:scale-105 transition">
                        <div class="produk-item">
                            <img alt="' . htmlspecialchars($row["nama_sepatu"]) . '" width="200" height="200"  class="mb-4 object-cover h-48 w-48" src="' . htmlspecialchars($row["gambar"]) . '" />
                            <h3 class="text-lg font-semibold">' . htmlspecialchars($row["nama_sepatu"]) . '</h3>
                            <p class="text-gray-700">Rp. ' . number_format($row["harga"], 0, ',', '.') . '</p>
                        </div>
                    </a>';
                }
            } else {
                echo '<p class="text-center col-span-3">Tidak ada produk.</p>';
            }
            ?>
        </div>
    </main>

    <footer class="bg-gray-100 py-8">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <h3 class="text-lg font-bold mb-4">About</h3>
                    <ul>
                        <li><a class="text-gray-700" href="#">Customer Care</a></li>
                        <li><a class="text-gray-700" href="#">Subscribe to our emails</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Subscribe to our emails</h3>
                    <input class="border p-2 w-full mb-4" placeholder="Masukan Email Anda" type="email" />
                    <button class="bg-black text-white py-2 px-4 w-full">Daftar</button>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Contact Us</h3>
                    <p class="text-gray-700 mb-4">@Step up shoes</p>
                    <p class="text-gray-700 mb-4">083457989156</p>
                    <p class="text-gray-700 mb-4">Stepupshoes@gmail.com</p>
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3956.551354756415!2d109.34427227391137!3d-7.404059292606023!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6559b9ff8d3795%3A0xa58daaef273f4e44!2sSMKN%201%20Purbalingga!5e0!3m2!1sen!2sid!4v1748691079616!5m2!1sen!2sid" width="400" height="200" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    <p class="text-gray-700">Jl. Mayor Jend. Sungkono No.34, Selabaya, Kec. Kalimanah, Kabupaten Purbalingga, Jawa Tengah 53371</p>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>
