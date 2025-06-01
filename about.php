<?php
session_start();
include("./koneksi.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About STEP UP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"/>
</head>
<body class="font-roboto">
    <header class="bg-white shadow-md fixed w-full">
        <div class="container mx-auto flex justify-between items-center py-4 px-6">
            <div class="text-2xl font-bold text-blue-900">
                STEP UP
            </div>
            <nav class="space-x-4">
                <a class="text-gray-700 hover:text-blue-900" href="./index.php">
                    Home
                </a>
                <a class="text-blue-900 font-bold  hover:text-blue-900" href="#">
                    About
                </a>
                <a class="text-gray-700 hover:text-blue-900" href="./produk.php">
                    Produk
                </a>
                <a class="text-gray-700 hover:text-blue-900" href="./user/pesanan/keranjang.php">
                    <i class="fas fa-shopping-cart">
                    </i>
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

    <main class="pt-24 pb-12">
        <div class="container mx-auto px-6">
            <!-- Hero Section -->
            <section class="bg-blue-50 rounded-lg p-8 mb-12">
                <div class="flex flex-col md:flex-row items-center">
                    <div class="md:w-1/2 mb-6 md:mb-0 md:pr-8">
                        <h1 class="text-3xl md:text-4xl font-bold text-blue-900 mb-4">About STEP UP</h1>
                        <p class="text-gray-700 mb-6">
                            STEP UP bergabung dengan PT. Mitra Gaya Indah pada tahun 2025, dengan tujuan melayani konsumen yang fashion-conscious, selalu mengikuti trend, dan mencari sesuatu yang "berbeda".
                        </p>
                        <button class="bg-black text-white py-2 px-6 rounded hover:bg-gray-800 transition duration-300">
                            Discover More
                        </button>
                    </div>
                    <div class="md:w-1/2">
                        <img src="https://images.unsplash.com/photo-1491553895911-0055eca6402d?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                             alt="Woman with stylish shoes" 
                             class="rounded-lg shadow-lg w-full h-auto">
                    </div>
                </div>
            </section>

            <!-- Our Story Section -->
            <section class="mb-12">
                <h2 class="text-2xl font-bold text-blue-900 mb-6 text-center">Our Story</h2>
                <div class="grid md:grid-cols-2 gap-8">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-xl font-semibold mb-4 text-blue-900">Our Beginning</h3>
                        <p class="text-gray-700">
                            Founded in 2025, STEP UP emerged from a passion for unique footwear that combines style and comfort. We noticed a gap in the market for fashion-forward shoes that stand out from the crowd.
                        </p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-xl font-semibold mb-4 text-blue-900">Our Mission</h3>
                        <p class="text-gray-700">
                            To empower individuals to express their unique style through high-quality, trendsetting footwear that makes a statement with every step.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Why Choose Us Section -->
            <section class="mb-12">
                <h2 class="text-2xl font-bold text-blue-900 mb-6 text-center">Why Choose STEP UP</h2>
                <div class="grid md:grid-cols-3 gap-6">
                    <div class="text-center p-6 bg-white rounded-lg shadow-md">
                        <div class="text-blue-900 text-3xl mb-4">
                            <i class="fas fa-star"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Unique Designs</h3>
                        <p class="text-gray-700">Stand out with our exclusive, trendsetting footwear designs you won't find anywhere else.</p>
                    </div>
                    <div class="text-center p-6 bg-white rounded-lg shadow-md">
                        <div class="text-blue-900 text-3xl mb-4">
                            <i class="fas fa-gem"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Premium Quality</h3>
                        <p class="text-gray-700">Crafted with the finest materials for lasting comfort and durability.</p>
                    </div>
                    <div class="text-center p-6 bg-white rounded-lg shadow-md">
                        <div class="text-blue-900 text-3xl mb-4">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Customer Focus</h3>
                        <p class="text-gray-700">Dedicated to providing exceptional service and building lasting relationships.</p>
                    </div>
                </div>
            </section>

            <!-- Team Section -->
            <section>
                <h2 class="text-2xl font-bold text-blue-900 mb-6 text-center">Meet Our Team</h2>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <img src="https://randomuser.me/api/portraits/women/43.jpg" 
                             alt="Team Member" 
                             class="w-32 h-32 rounded-full mx-auto mb-4 object-cover">
                        <h3 class="text-xl font-semibold">Sarah Johnson</h3>
                        <p class="text-blue-900 mb-2">Founder & CEO</p>
                        <p class="text-gray-700">With 10+ years in fashion industry, Sarah brings visionary leadership to STEP UP.</p>
                    </div>
                    <div class="text-center">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" 
                             alt="Team Member" 
                             class="w-32 h-32 rounded-full mx-auto mb-4 object-cover">
                        <h3 class="text-xl font-semibold">Michael Chen</h3>
                        <p class="text-blue-900 mb-2">Head Designer</p>
                        <p class="text-gray-700">Michael's innovative designs are the heart of our unique product line.</p>
                    </div>
                    <div class="text-center">
                        <img src="https://randomuser.me/api/portraits/women/65.jpg" 
                             alt="Team Member" 
                             class="w-32 h-32 rounded-full mx-auto mb-4 object-cover">
                        <h3 class="text-xl font-semibold">Lisa Rodriguez</h3>
                        <p class="text-blue-900 mb-2">Customer Experience</p>
                        <p class="text-gray-700">Lisa ensures every STEP UP customer receives exceptional service.</p>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <footer class="bg-gray-100 py-8">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <h3 class="text-lg font-bold mb-4">About</h3>
                    <ul>
                        <li class="mb-2">
                            <a class="text-gray-700 hover:text-blue-900" href="#">Customer Care</a>
                        </li>
                        <li>
                            <a class="text-gray-700 hover:text-blue-900" href="#">Subscribe to our emails</a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Subscribe to our emails</h3>
                    <input class="border p-2 w-full mb-4 rounded" placeholder="Masukan Email Anda" type="email"/>
                    <button class="bg-black text-white py-2 px-4 w-full rounded hover:bg-gray-800 transition duration-300">
                        Daftar
                    </button>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Contact Us</h3>
                    <p class="text-gray-700 mb-2">
                         @Step up shoes
                    </p>
                    <p class="text-gray-700 mb-2">
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
                        Jl. Mayor Jend. Sungkono No.34, Selabaya, Kec. Kalimanah, Kabupaten Purbalingga, Jawa Tengah 53371
                    </p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>