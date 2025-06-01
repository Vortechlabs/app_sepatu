<?php
session_start();
include("../../koneksi.php");

// ID user dari session login (pastikan user sudah login)
$id_user = $_SESSION['id_users'] ?? 0;

if ($id_user == 0) {
    die("Silakan login terlebih dahulu.");
}

// Ambil data keranjang user + info sepatu
$query = "SELECT keranjang.*, sepatu.nama_sepatu, sepatu.harga, sepatu.gambar 
          FROM keranjang 
          JOIN sepatu ON keranjang.id_sepatu = sepatu.id_sepatu 
          WHERE keranjang.id_user = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();

// Total harga
$total = 0;
?>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>
        Shopping Cart
    </title>
    <script src="https://cdn.tailwindcss.com">
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
</head>

<body class="bg-gray-100">
    <!-- Header -->
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
          <a href="../profil/profil.php" class="text-gray-700 hover:text-blue-900">
            <i class="fas fa-user"></i>
          </a>
        <?php else: ?>
          <!-- Jika belum login -->
          <a href="../../autentikasi/login.php" class="bg-blue-900 text-white px-4 py-1 rounded hover:bg-blue-800">
            Login
          </a>
        <?php endif; ?>
            </nav>
        </div>
    </header>
    <!-- Main Content -->
    <main class="p-4 pt-20 mx-80">
        <h1 class="text-2xl font-bold mb-4">
            Keranjang Anda
        </h1>
     
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
            <?php $subtotal = $row['harga'] * $row['jumlah']; $total += $subtotal; ?>
            <div class="flex items-center border-b pb-4 mb-4">
                <img src="../../<?php echo $row['gambar']; ?>" class="w-24 h-24 object-cover border" />
                <div class="ml-4">
                <h2 class="text-xl font-semibold"><?php echo $row['nama_sepatu']; ?></h2>
                <p>Ukuran: <?php echo $row['ukuran']; ?></p>
                <p>Warna: <?php echo $row['warna']; ?></p>
                <p>Jumlah: <?php echo $row['jumlah']; ?></p>
                <p class="text-2xl font-bold mt-2">Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></p>
                </div>
                <form method="post" action="hapus_keranjang.php" class="ml-auto">
                <input type="hidden" name="id_keranjang" value="<?php echo $row['id_keranjang']; ?>">
                <button type="submit" class="text-red-500 hover:text-red-700">
                    <i class="fas fa-trash-alt"></i>
                </button>
                </form>
            </div>
            <?php endwhile; ?>

            <div class="flex justify-between items-center font-bold text-xl">
            <span>Total</span>
            <span>Rp <?php echo number_format($total, 0, ',', '.'); ?></span>
            </div>
            <a href="./hlmnCO.php">
            <button class="w-full bg-gray-800 text-white py-2 mt-4 rounded hover:bg-black/80 transition">
                Check Out
            </button>
            </a>
        <?php else: ?>
            <p class="text-gray-600">Keranjang masih kosong.</p>
        <?php endif; ?>


    </main>
</body>

</html>