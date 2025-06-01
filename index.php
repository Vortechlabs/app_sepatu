<?php
session_start();
include("./koneksi.php");

// Fetch special price products (6 cheapest products)
$special_price_query = "SELECT * FROM sepatu ORDER BY harga ASC LIMIT 6";
$special_price_result = $koneksi->query($special_price_query);

// Fetch new arrival products (6 newest products)
$new_arrival_query = "SELECT * FROM sepatu ORDER BY tanggal_ditambahkan DESC LIMIT 6";
$new_arrival_result = $koneksi->query($new_arrival_query);

// Fetch all products (max 12)
$all_products_query = "SELECT * FROM sepatu LIMIT 12";
$all_products_result = $koneksi->query($all_products_query);

// Fetch categories
$brand_query = "SELECT * FROM brand";
$brand_result = $koneksi->query($brand_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&amp;display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="style.css">
</head>

<body class="font-roboto">
    <header class="bg-white shadow-md fixed w-full z-20">
        <div class="container mx-auto flex justify-between items-center py-4 px-6">
            <div class="text-2xl font-bold text-blue-900">
                STEP UP
            </div>
            <nav class="space-x-4 flex items-center">
                <a class="text-blue-900 font-bold hover:text-blue-900" href="#">
                    Home
                </a>
                <a class="text-gray-700 hover:text-blue-900" href="./about.php">
                    About
                </a>
                <a class="text-gray-700 hover:text-blue-900" href="./produk.php">
                    Produk
                </a>
                <a class="text-gray-700 hover:text-blue-900" href="./user/pesanan/keranjang.php">
                    <i class="fas fa-shopping-cart"></i>
                </a>

                <?php if (isset($_SESSION['id_users'])): ?>
                    <!-- Jika user sudah login -->
                    <a href="./user/profil/profil.php" class="text-gray-700 hover:text-blue-900">
                        <i class="fas fa-user"></i>
                    </a>
                <?php else: ?>
                    <!-- Jika belum login -->
                    <a href="./autentikasi/login.php" class="bg-blue-900 text-white px-4 py-1 rounded hover:bg-blue-800">
                        Login
                    </a>
                <?php endif; ?>
            </nav>

        </div>
    </header>

    <main class="container mx-auto px-6 py-8 pt-20">
        <section class="flex flex-col md:flex-row items-center">
            <div class="md:w-1/2">
                <h1 class="text-3xl font-bold mb-4">
                    STEP UP
                </h1>
                <p class="text-gray-700 mb-4">
                    STEP UP bergabung dengan PT. Mitra Gaya Indah pada tahun 2025, dengan tujuan melayani konsumen yang
                    fashion-conscious, selalu mengikuti trend, dan mencari sesuatu yang "berbeda".
                </p>
                <a href="./produk.php">
                    <button class="bg-black hover:bg-black/80 hover:scale-105 transition text-white py-2 px-4">
                        Tampilkan Katalog
                    </button>
                </a>
            </div>
            <div class="md:w-1/2">
                <img alt="Woman sitting on a chair with stylish shoes" class="rounded-full"
                    src="https://placehold.co/600x400" />
            </div>
        </section>

        <section class="my-20">
            <h1 class="text-3xl uppercase text-center font-bold">Special Price</h1>
            <p class="text-center mb-10">Jelajahi item pilihan yang menonjol dari yang lain.</p>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 ">
                <?php
                if ($special_price_result->num_rows > 0) {
                    while($row = $special_price_result->fetch_assoc()) {
                        echo '
                        <a href="./user/produk/detail_produk.php?id_sepatu='.$row["id_sepatu"].'" class="hover:scale-105 transition">
                            <div class="produk-item">
                                <img alt="'.$row["nama_sepatu"].'" width="200" height="200" class="mb-4 object-cover h-48 w-48"
                                    src="'.$row["gambar"].'" />
                                <h3 class="text-lg font-semibold">'.$row["nama_sepatu"].'</h3>
                                <p class="text-gray-700">Rp. '.number_format($row["harga"], 0, ',', '.').'</p>
                            </div>
                        </a>';
                    }
                } else {
                    echo '<p class="text-center col-span-3">Tidak ada produk special price.</p>';
                }
                ?>
            </div>
        </section>

        <section class="mb-20">
            <h1 class="text-3xl uppercase text-center font-bold mb-10">NEW ARRIVAL</h1>
                
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <?php
                if ($new_arrival_result->num_rows > 0) {
                    while($row = $new_arrival_result->fetch_assoc()) {
                        echo '
                        <a href="./user/produk/detail_produk.php?id_sepatu='.$row["id_sepatu"].'" class="hover:scale-105 transition">
                            <div class="produk-item">
                                <img alt="'.$row["nama_sepatu"].'" width="200" height="200" class="mb-4 object-cover h-48 w-48"
                                    src="./'.$row["gambar"].'" />
                                <h3 class="text-lg font-semibold">'.$row["nama_sepatu"].'</h3>
                                <p class="text-gray-700">Rp. '.number_format($row["harga"], 0, ',', '.').'</p>
                            </div>
                        </a>';
                    }
                } else {
                    echo '<p class="text-center col-span-3">Tidak ada produk baru.</p>';
                }
                ?>
            </div>
        </section>

        <section>
            <h1 class="text-3xl uppercase text-center font-bold">Shop by Brand</h1>
            <p class="text-center mb-10">Nikmati pengalaman untuk menemukan pilihan dengan lebih mudah.</p>
            <div class="flex gap-6 justify-center">
                <?php
                if ($brand_result->num_rows > 0) {
                    while($row = $brand_result->fetch_assoc()) {
                        echo '
                        <div>
                            <a href="./produk.php?brand='.$row["id_brand"].'">
                                <img alt="'.$row["brand"].'" class="mb-4 h-40 w-40 object-cover" src="'.$row["logo_brand"].'" />
                            </a>
                        </div>';
                    }
                } else {
                    echo '<p class="text-center col-span-3">Tidak ada brand.</p>';
                }
                ?>
            </div>
        </section>

        <section class="mt-12">
            <h1 class="text-3xl uppercase text-center mb-10 font-bold">All Collection</h1>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <?php
                if ($all_products_result->num_rows > 0) {
                    while($row = $all_products_result->fetch_assoc()) {
                        echo '
                        <a href="./user/produk/detail_produk.php?id_sepatu='.$row["id_sepatu"].'" class="hover:scale-105 transition">
                            <div class="produk-item">
                                <img alt="'.$row["nama_sepatu"].'" width="200" height="200" class="mb-4 object-cover h-48 w-48"
                                    src="'.$row["gambar"].'" />
                                <h3 class="text-lg font-semibold">'.$row["nama_sepatu"].'</h3>
                                <p class="text-gray-700">Rp. '.number_format($row["harga"], 0, ',', '.').'</p>
                            </div>
                        </a>';
                    }
                } else {
                    echo '<p class="text-center col-span-3">Tidak ada produk.</p>';
                }
                ?>
            </div>
        </section>

        <a href="./produk.php" class="flex justify-center">
            <button class="bg-black hover:bg-black/80 hover:scale-105 transition text-white py-2 px-4 mt-4">
                Tampilkan Lebih
            </button>
        </a>

    </main>
    <footer class="bg-gray-100 py-8">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <h3 class="text-lg font-bold mb-4">
                        About
                    </h3>
                    <ul>
                        <li>
                            <a class="text-gray-700" href="#">
                                Customer Care
                            </a>
                        </li>
                        <li>
                            <a class="text-gray-700" href="#">
                                Subscribe to our emails
                            </a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">
                        Subscribe to our emails
                    </h3>
                    <input class="border p-2 w-full mb-4" placeholder="Masukan Email Anda" type="email" />
                    <button class="bg-black text-white py-2 px-4 w-full">
                        Daftar
                    </button>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">
                        Contact Us
                    </h3>
                    <p class="text-gray-700 mb-4">
                        @Step up shoes
                    </p>
                    <p class="text-gray-700 mb-4">
                        083457989156
                    </p>
                    <p class="text-gray-700 mb-4">
                        Stepupshoes@gmail.com
                    </p>
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3956.551354756415!2d109.34427227391137!3d-7.404059292606023!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6559b9ff8d3795%3A0xa58daaef273f4e44!2sSMKN%201%20Purbalingga!5e0!3m2!1sen!2sid!4v1748691079616!5m2!1sen!2sid"
                        width="400" height="200" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                    <p class="text-gray-700">
                        Jl. Mayor Jend. Sungkono No.34, Selabaya, Kec. Kalimanah, Kabupaten Purbalingga, Jawa Tengah
                        53371
                    </p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>