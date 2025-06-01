<?php
session_start();
require '../../koneksi.php'; // sesuaikan path koneksi

$id_user = $_SESSION['id_users']; // pastikan user login

// Ambil data dari DB
$alamat = mysqli_query($koneksi, "SELECT * FROM alamat WHERE id_user = $id_user");
$keranjang = mysqli_query($koneksi, "SELECT k.*, s.nama_sepatu, s.harga, s.gambar 
    FROM keranjang k JOIN sepatu s ON k.id_sepatu = s.id_sepatu 
    WHERE k.id_user = $id_user");
$metode_bayar = mysqli_query($koneksi, "SELECT * FROM metode_bayar");
$kurir = mysqli_query($koneksi, "SELECT * FROM kurir");

// Hitung total belanja
$total = 0;
?>


<html>
 <head>
    <title>
        Checkout
    </title>
  <script src="https://cdn.tailwindcss.com">
  </script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&amp;display=swap" rel="stylesheet"/>
 </head>
 <body class="bg-gray-100 font-roboto">
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

    <main class="pt-24 pb-12">
    <!-- Modal Tambah Alamat -->
    <div id="modalAlamat" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white w-full max-w-md p-6 rounded shadow-lg relative">
        <h2 class="text-xl font-bold mb-4">Tambah Alamat Baru</h2>

 <form id="formAlamatBaru">
  <label class="block mb-2 text-sm font-medium">Nama Penerima</label>
  <input name="nama_penerima" type="text" class="w-full border p-2 rounded mb-3" required>

  <label class="block mb-2 text-sm font-medium">Nomor HP</label>
  <input name="no_telp" type="tel" class="w-full border p-2 rounded mb-3" required>

  <label class="block mb-2 text-sm font-medium">Alamat Lengkap</label>
  <textarea name="alamat" class="w-full border p-2 rounded mb-3" required></textarea>

        <div class="flex justify-end gap-2">
            <button type="button" onclick="tutupModal()" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Batal</button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
        </div>
        </form>

        <!-- Tombol Close -->
        <button onclick="tutupModal()" class="absolute top-3 right-3 text-gray-500 hover:text-black">
        &times;
        </button>
    </div>
    </div>

    <div class="max-w-2xl mx-auto bg-white shadow-md mt-10 rounded-md overflow-hidden">
        <div class="p-6">
        <h1 class="text-2xl font-bold mb-4 text-blue-900">Checkout</h1>

    <form action="proses_checkout.php" method="POST">
        <!-- Alamat -->
        <div class="flex justify-between items-center mb-2">
  <label class="block font-semibold">Alamat Pengiriman</label>
  <button type="button" onclick="bukaModal()" class="text-sm text-blue-600 hover:underline">
    + Tambah Alamat
  </button>
</div>
        <select name="id_alamat" required class="w-full border p-2 rounded mb-4">
            <?php while ($a = mysqli_fetch_assoc($alamat)) { ?>
                <option value="<?= $a['id_alamat'] ?>">
                    <?= $a['nama_penerima'] ?> - <?= $a['alamat'] ?> (<?= $a['no_telp'] ?>)
                </option>
            <?php } ?>
        </select>

          <!-- Daftar Produk -->
        <div class="mb-4">
            <label class="block font-semibold mb-1">Produk</label>
            <?php while ($k = mysqli_fetch_assoc($keranjang)) {
                $subtotal = $k['harga'] * $k['jumlah'];
                $total += $subtotal;
            ?>
                <div class="flex items-center mb-2 border p-2 rounded">
                    <img src="../../<?= $k['gambar'] ?>" class="w-20 h-20 object-cover mr-4" />
                    <div>
                        <div class="font-bold"><?= $k['nama_sepatu'] ?></div>
                        <div>Ukuran: <?= $k['ukuran'] ?> | Warna: <?= $k['warna'] ?></div>
                        <div>Jumlah: <?= $k['jumlah'] ?></div>
                        <div class="text-blue-900">
                            Harga: Rp <?= number_format($k['harga'], 0, ',', '.') ?>
                        </div>
                        <div class="font-bold text-green-700">
                            Subtotal: Rp <?= number_format($subtotal, 0, ',', '.') ?>
                        </div>

                    </div>
                </div>
            <?php } ?>
        </div>

        <!-- Metode Bayar -->
        <label class="block font-semibold mb-1">Metode Pembayaran</label>
        <select name="id_metode" required class="w-full border p-2 rounded mb-4">
            <?php while ($m = mysqli_fetch_assoc($metode_bayar)) { ?>
                <option value="<?= $m['id_metode_bayar'] ?>"><?= $m['provider'] ?> - <?= $m['nomor_akun'] ?></option>
            <?php } ?>
        </select>

        <!-- Kurir -->
        <label class="block font-semibold mb-1">Jasa Kirim</label>
        <select name="id_kurir" required class="w-full border p-2 rounded mb-4">
        <option value="">Pilih Jasa Kirim</option>
        <?php while ($kr = mysqli_fetch_assoc($kurir)) { ?>
            <option value="<?= $kr['id_kurir'] ?>" data-harga="<?= $kr['harga'] ?>">
                <?= $kr['jasa_kirim'] ?> - Rp <?= number_format($kr['harga'], 0, ',', '.') ?>
            </option>
        <?php } ?>

        </select>

        <!-- Total -->
        <div class="flex justify-between font-bold text-lg mt-4 border-t pt-4">
    
        <div class="mt-6 space-y-2 text-lg">
            <div class="flex justify-between font-semibold">
                <span>Total Barang:</span>
                <span id="totalBarang">Rp <?= number_format($total, 0, ',', '.') ?></span>
            </div>
            <div class="flex justify-between font-semibold">
                <span>Ongkir:</span>
                <span id="hargaOngkir">Rp 0</span>
            </div>
            <div class="flex justify-between font-bold text-blue-900 border-t pt-2 mt-2">
                <span>Total Keseluruhan:</span>
                <span id="totalAkhir">Rp <?= number_format($total, 0, ',', '.') ?></span>
            </div>
        </div>

        </div>

        <!-- Simpan total ke hidden input -->
        <input type="hidden" name="total_barang" value="<?= $total ?>">

        <!-- Tombol -->
        <button class="w-full bg-blue-600 text-white py-2 mt-6 rounded hover:bg-blue-700 transition">
            Buat Pesanan
        </button>
    </form>
    </div>
        </div>
    </main>

    <script>
    const modal = document.getElementById("modalAlamat");

    function bukaModal() {
        modal.classList.remove("hidden");
    }

    function tutupModal() {
        modal.classList.add("hidden");
    }

document.getElementById("formAlamatBaru").addEventListener("submit", function(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    fetch('../profil/tambah_alamat.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const select = document.querySelector("select[name='id_alamat']");
            const option = document.createElement("option");
            option.value = data.id_alamat;
            option.textContent = data.text;
            select.appendChild(option);
            select.value = data.id_alamat;

            tutupModal();
            form.reset();
        } else {
            alert("Gagal menambahkan alamat");
        }
    })
    .catch(err => {
        console.error(err);
        alert("Terjadi kesalahan");
    });
});


    document.querySelector('select[name="id_kurir"]').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const hargaOngkir = parseInt(selected.dataset.harga);
    const totalBarang = <?= $total ?>;
    const totalAkhir = totalBarang + hargaOngkir;

    
    // Update tampilan
    document.getElementById("hargaOngkir").textContent = formatRupiah(hargaOngkir);
    document.getElementById("totalAkhir").textContent = formatRupiah(totalAkhir);
});

function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
    }).format(angka);
}
    </script>



 </body>
</html>