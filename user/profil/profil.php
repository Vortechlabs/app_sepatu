<?php
session_start();
include("../../koneksi.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['id_users'])) {
  header("Location: ../../autentikasi/login.php");
  exit;
}

$id_user = $_SESSION['id_users'];

if (isset($_POST['hapus_alamat'])) {
  $id_alamat_hapus = intval($_POST['id_alamat_hapus']);

  // Cek apakah alamat isih dipakai
  $cek_pemakaian = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pesanan WHERE id_alamat = $id_alamat_hapus");
  $data_cek = mysqli_fetch_assoc($cek_pemakaian);

  if ($data_cek['total'] > 0) {
    echo "<script>alert('Alamat iki ora bisa dihapus amarga isih digunakake ing pesanan.'); window.location.href='" . $_SERVER['PHP_SELF'] . "';</script>";
    exit;
  }

  mysqli_query($koneksi, "DELETE FROM alamat WHERE id_alamat = $id_alamat_hapus AND id_user = $id_user");
  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}

if (isset($_POST['batal_pesanan'])) {
  $id_pesanan = intval($_POST['id_pesanan_batal']);
  mysqli_query($koneksi, "UPDATE pesanan SET status_pesanan = 'dibatalkan' WHERE id_pesanan = $id_pesanan AND id_users = $id_user");
}


$query = mysqli_query($koneksi, "SELECT * FROM users WHERE id_users = $id_user");
$data_user = mysqli_fetch_assoc($query);

$q_pesanan = mysqli_query($koneksi, "
  SELECT * FROM pesanan 
  WHERE id_users = $id_user 
  ORDER BY tanggal_pesanan DESC 
  LIMIT 3
");

$query_alamat = mysqli_query($koneksi, "SELECT * FROM alamat WHERE id_user = $id_user ORDER BY id_alamat DESC");


if (isset($_POST['tambah_alamat'])) {
  $nama = mysqli_real_escape_string($koneksi, $_POST['nama_penerima']);
  $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
  $no_telp = mysqli_real_escape_string($koneksi, $_POST['no_telp']);

  $query = "INSERT INTO alamat (id_user, nama_penerima, alamat, no_telp) 
            VALUES ($id_user, '$nama', '$alamat', '$no_telp')";
  mysqli_query($koneksi, $query);

  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}


if (isset($_POST['logout'])) {
  session_destroy();
  header("Location: ../../index.php");
  exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil</title>
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
          <a href="#" class="text-gray-700 hover:text-blue-900">
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

  <main class="pt-24 pb-12 px-6 max-w-4xl mx-auto">

    <!-- Modal Tambah Alamat -->
    <div id="modalAlamat" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
      <div class="bg-white w-full max-w-md p-6 rounded shadow">
        <h3 class="text-lg font-bold mb-4">Tambah Alamat Baru</h3>
        <form method="POST" action="">
          <input type="text" name="nama_penerima" placeholder="Nama Penerima" class="w-full border p-2 mb-2 rounded" required>
          <textarea name="alamat" placeholder="Alamat Lengkap" class="w-full border p-2 mb-2 rounded" required></textarea>
          <input type="text" name="no_telp" placeholder="Nomor HP" class="w-full border p-2 mb-4 rounded" required>

          <div class="flex justify-end space-x-2">
            <button type="button" onclick="document.getElementById('modalAlamat').classList.add('hidden')" class="px-4 py-2 border rounded">Batal</button>
            <button type="submit" name="tambah_alamat" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan</button>
          </div>
        </form>

      </div>
    </div>


    <h1 class="text-3xl font-bold mb-6">Profil Saya</h1>

    <!-- Info Akun -->
    <section class="bg-white p-6 rounded shadow mb-6 flex items-center space-x-6">
      <img src="../../<?= $data_user['image']; ?>" alt="Foto Profil"
        class="w-24 h-24 rounded-full object-cover border">
      <div>
        <h2 class="text-xl font-bold mb-2">Informasi Akun</h2>
        <p><strong>Nama:</strong> <?= htmlspecialchars($data_user['username']); ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($data_user['email']); ?></p>
        <p><strong>Nomor HP:</strong> <?= htmlspecialchars($data_user['no_telepon']); ?></p>
        <a href="./edit_profil.php" class="inline-block mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
          Edit Profil
        </a>
      </div>
    </section>


    <!-- Riwayat Pemesanan Terakhir -->
    <section class="bg-white p-6 rounded shadow mb-6">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Pesanan Terakhir</h2>
        <a href="./riwayat_pesanan.php" class="text-blue-600 hover:underline">Lihat Semua</a>
      </div>

      <div class="space-y-4">
      <?php while ($pesanan = mysqli_fetch_assoc($q_pesanan)) : ?>
        <div class="border p-4 rounded">
          <div class="flex justify-between items-center">
            <div>
              <p class="font-bold">Pesanan #<?= $pesanan['id_pesanan'] ?></p>
              <p class="text-sm text-gray-600"><?= date('d M Y', strtotime($pesanan['tanggal_pesanan'])) ?></p>
            </div>
            <div class="flex items-center gap-4">
              <span class="
                font-bold 
                <?= 
                  $pesanan['status_pesanan'] == 'diterima' ? 'text-green-600' : 
                  ($pesanan['status_pesanan'] == 'pending' ? 'text-yellow-600' : 
                  ($pesanan['status_pesanan'] == 'dibatalkan' ? 'text-red-600' : 'text-blue-600')) 
                ?>">
                <?= ucfirst($pesanan['status_pesanan']) ?>
              </span>

              <?php if ($pesanan['status_pesanan'] == 'pending') : ?>
                <form method="POST" onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')">
                  <input type="hidden" name="id_pesanan_batal" value="<?= $pesanan['id_pesanan'] ?>">
                  <button type="submit" name="batal_pesanan" class="text-red-600 hover:underline text-sm">Batalkan</button>
                </form>
              <?php endif; ?>
            </div>
          </div>
          <p class="text-sm mt-2">Total: <strong>Rp <?= number_format($pesanan['total_harga']) ?></strong></p>
        </div>
      <?php endwhile; ?>

      </div>

    </section>

    <!-- Alamat Tersimpan -->
    <section class="bg-white p-6 rounded shadow mb-6">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Alamat Tersimpan</h2>
        <button onclick="document.getElementById('modalAlamat').classList.remove('hidden')"
          class="text-blue-600 hover:underline">
          Tambah Alamat
        </button>
      </div>
      <div class="space-y-2">
        <?php while ($alamat = mysqli_fetch_assoc($query_alamat)) : ?>
          <div class="border p-4 rounded flex justify-between items-start">
            <div>
              <p class="font-bold"><?= htmlspecialchars($alamat['nama_penerima']) ?></p>
              <p><?= htmlspecialchars($alamat['alamat']) ?></p>
              <p><?= htmlspecialchars($alamat['no_telp']) ?></p>
            </div>
            <form method="POST" onsubmit="return confirm('Yakin ingin menghapus alamat ini?')">
              <input type="hidden" name="id_alamat_hapus" value="<?= $alamat['id_alamat'] ?>">
              <button type="submit" name="hapus_alamat" class="text-red-600 hover:underline ml-4">Hapus</button>
            </form>
          </div>
        <?php endwhile; ?>

      </div>

    </section>

    <!-- Kelola Akun -->
    <section class="bg-white p-6 rounded shadow">
      <h2 class="text-xl font-bold mb-4">Pengaturan Akun</h2>
      <form method="POST">
        <button type="submit" name="logout" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
          Keluar Akun
        </button>
      </form>
    </section>
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
            Jl. Mayor Jend. Sungkono No.34, Selabaya, Kec. Kalimanah, Kabupaten Purbalingga, Jawa Tengah 53371
          </p>
        </div>
      </div>
    </div>
  </footer>
</body>

</html>