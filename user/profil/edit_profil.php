<?php
session_start();
include("../../koneksi.php");

if (!isset($_SESSION['id_users'])) {
    header("Location: ../../autentikasi/login.php");
    exit();
}

$id = $_SESSION['id_users'];
$query = $koneksi->prepare("SELECT * FROM users WHERE id_users = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil</title>
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

    <main class="pt-24 px-6 max-w-xl mx-auto">
        <h2 class="text-2xl font-bold mb-6">Edit Profil</h2>
        <form action="proses_update_profil.php" method="POST" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label>Username</label>
                <input type="text" name="username" required value="<?= htmlspecialchars($user['username']) ?>"
                    class="w-full p-2 border rounded" />
            </div>
            <div>
                <label>Email</label>
                <input type="email" name="email" required value="<?= htmlspecialchars($user['email']) ?>"
                    class="w-full p-2 border rounded" />
            </div>
            <div>
                <label>No Telepon</label>
                <input type="number" name="no_telepon" required value="<?= htmlspecialchars($user['no_telepon']) ?>"
                    class="w-full p-2 border rounded" />
            </div>
            <div>
                <label>Foto Profil</label><br>
                <img src="../../<?= htmlspecialchars($user['image']) ?>" alt="Foto Profil" width="100"
                    class="mb-2 rounded">
                <input type="file" name="image" accept="image/*" class="w-full" />
            </div>
            <button type="submit"
                class="bg-blue-900 text-white px-4 py-2 rounded hover:bg-blue-800">Simpan Perubahan</button>
        </form>
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