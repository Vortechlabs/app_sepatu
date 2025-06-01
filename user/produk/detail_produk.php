<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("../../koneksi.php");

// Get product ID from URL parameter
$id_sepatu = isset($_GET['id_sepatu']) ? intval($_GET['id_sepatu']) : 0;

// Fetch product details
$product_query = "SELECT sepatu.*, brand.brand FROM sepatu 
                  JOIN brand ON sepatu.id_brand = brand.id_brand 
                  WHERE sepatu.id_sepatu = ?";
$stmt = $koneksi->prepare($product_query);
$stmt->bind_param("i", $id_sepatu);
$stmt->execute();
$product_result = $stmt->get_result();

if ($product_result->num_rows === 0) {
    die("Sepatu tidak ditemukan");
}

$product = $product_result->fetch_assoc();

// Extract sizes and colors from the product data
$sizes = explode(' ', $product['ukuran']);
$colors = explode(' ', $product['warna']);

// Get stock quantity
$stock = $product['stok'];
?>

<!DOCTYPE html>
<html>

<head>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&amp;display=swap" rel="stylesheet" />
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
        <a class="text-gray-700 hover:text-blue-900" href="../../Sepatu.php">
          Sepatu
        </a>
        <a class="text-gray-700 hover:text-blue-900" href="../pesanan/keranjang.php">
          <i class="fas fa-shopping-cart"></i>
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
  
  <main class="p-8 pt-20">
    <div class="flex flex-col md:flex-row">
      <div class="md:w-1/2 p-4">
        <div class="border p-4 bg-white">
          <img alt="<?php echo htmlspecialchars($product['nama_sepatu']); ?>" class="w-full" src="../../<?php echo htmlspecialchars($product['gambar']); ?>" />
        </div>
      </div>
      
      <div class="md:w-1/2 p-4">
        <h1 class="text-2xl font-bold">
          <?php echo htmlspecialchars($product['nama_sepatu']); ?>
        </h1>
        <p class="text-xl text-gray-600 mt-2">
          <?php echo htmlspecialchars($product['deskripsi']); ?>
        </p>
        <p class="text-lg text-gray-500 mt-2">
          SKU#: <?php echo htmlspecialchars($product['sku']); ?>
        </p>
        <p class="text-lg text-gray-500 mt-2">
          Brand: <?php echo htmlspecialchars($product['brand']); ?>
        </p>
        
        <div class="mt-6">
          <p class="text-xl">
            Harga reguler
          </p>
          <p class="text-2xl font-bold text-blue-900">
            Rp <?php echo number_format($product['harga'], 0, ',', '.'); ?>
          </p>
        </div>
        
        <form id="productForm" method="post" action="../pesanan/proses_tambah_keranjang.php">

        <input type="hidden" name="id_sepatu" value="<?php echo $id_sepatu; ?>">
        <input type="hidden" name="beli_sekarang" id="beli_sekarang" value="0">
              
          <div class="mt-6">
            <h2 class="text-xl font-bold">
              Size
            </h2>
            <div class="flex flex-wrap mt-2">
              <?php
              if (!empty($sizes[0])) {
                  foreach($sizes as $size) {
                      $size = trim($size);
                      if (!empty($size)) {
                          echo '<button type="button" class="border p-2 m-1 w-12 text-center hover:bg-gray-100 size-option" data-size="'.htmlspecialchars($size).'">'
                              .htmlspecialchars($size).
                              '</button>';
                      }
                  }
              } else {
                  echo '<p class="text-red-500">Stok habis</p>';
              }
              ?>
              <input type="hidden" name="selected_size" id="selected_size" required>
            </div>
          </div>

          <div class="mt-6">
            <h2 class="text-xl font-bold">
              Color
            </h2>
            <div class="flex flex-wrap mt-2">
              <?php
              if (!empty($colors[0])) {
                  foreach($colors as $color) {
                      $color = trim($color);
                      if (!empty($color)) {
                          echo '<button type="button" class="border p-2 m-1 w-24 text-center hover:bg-gray-100 color-option" data-color="'.htmlspecialchars($color).'">'
                              .htmlspecialchars($color).
                              '</button>';
                      }
                  }
              }
              ?>
              <input type="hidden" name="selected_color" id="selected_color" required>
            </div>
          </div>
          
          <div class="mt-6">
            <h2 class="text-xl font-bold">
              Stok
            </h2>
            <p class="text-lg">
              <?php echo $stock > 0 ? $stock : 'Stok habis'; ?>
            </p>
          </div>
          
          <div class="mt-6">
            <label for="quantity" class="text-xl font-bold block mb-2">Quantity</label>
            <div class="flex items-center">
              <button type="button" class="border p-2 w-10 text-center" onclick="changeQuantity(-1)">-</button>
              <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $stock; ?>" 
                     class="border-t border-b p-2 w-16 text-center" readonly>
              <button type="button" class="border p-2 w-10 text-center" onclick="changeQuantity(1)">+</button>
            </div>
          </div>
          
           <div class="mt-8 flex space-x-4">
              <button type="button" onclick="addToCart()" class="bg-purple-600 text-white py-3 px-6 rounded-lg hover:bg-purple-700 transition flex-1">
                  Tambah ke Keranjang
              </button>
              <button type="submit" onclick="buyNow()" class="bg-blue-900 text-white py-3 px-6 rounded-lg hover:bg-blue-800 transition flex-1">
                  Beli Sekarang
              </button>
          </div> 
        </form>
      </div>
    </div>
    
    
    <!-- Related Sepatu Section -->
    <div class="mt-12">
      <h2 class="text-2xl font-bold mb-6">Sepatu Terkait</h2>
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <?php
        // Fetch related Sepatu (same brand)
        $related_query = "SELECT * FROM sepatu WHERE id_brand = ? AND id_sepatu != ? LIMIT 4";
        $stmt = $koneksi->prepare($related_query);
        $stmt->bind_param("ii", $product['id_brand'], $id_sepatu);
        $stmt->execute();
        $related_result = $stmt->get_result();
        
        if ($related_result->num_rows > 0) {
            while($related = $related_result->fetch_assoc()) {
                echo '
                <a href="./detail_Sepatu.php?id_sepatu='.$related['id_sepatu'].'" class="hover:scale-105 transition">
                  <div class="bg-white p-4 rounded-lg shadow">
                    <img src="'.htmlspecialchars($related['gambar']).'" alt="'.htmlspecialchars($related['nama_sepatu']).'" class="w-full h-48 object-cover mb-4">
                    <h3 class="font-semibold">'.htmlspecialchars($related['nama_sepatu']).'</h3>
                    <p class="text-blue-900 font-bold">Rp '.number_format($related['harga'], 0, ',', '.').'</p>
                  </div>
                </a>';
            }
        } else {
            echo '<p>Tidak ada Sepatu terkait</p>';
        }
        ?>
      </div>
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
          <p class="text-gray-700 mb-2">@Step up shoes</p>
          <p class="text-gray-700 mb-2">083457989156</p>
          <p class="text-gray-700 mb-4">Stepupshoes@gmail.com</p>
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3956.551354756415!2d109.34427227391137!3d-7.404059292606023!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6559b9ff8d3795%3A0xa58daaef273f4e44!2sSMKN%201%20Purbalingga!5e0!3m2!1sen!2sid!4v1748691079616!5m2!1sen!2sid"
            width="100%" height="200" style="border:0;" allowfullscreen="" loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"></iframe>
          <p class="text-gray-700">
            Jl. Mayor Jend. Sungkono No.34, Selabaya, Kec. Kalimanah, Kabupaten Purbalingga, Jawa Tengah 53371
          </p>
        </div>
      </div>
    </div>
  </footer>

  <script>
    // Select size and color
    document.querySelectorAll('.size-option').forEach(button => {
      button.addEventListener('click', function() {
        document.querySelectorAll('.size-option').forEach(btn => btn.classList.remove('bg-blue-900', 'text-white'));
        this.classList.add('bg-blue-900', 'text-white');
        document.getElementById('selected_size').value = this.dataset.size;
      });
    });

    document.querySelectorAll('.color-option').forEach(button => {
      button.addEventListener('click', function() {
        document.querySelectorAll('.color-option').forEach(btn => btn.classList.remove('bg-blue-900', 'text-white'));
        this.classList.add('bg-blue-900', 'text-white');
        document.getElementById('selected_color').value = this.dataset.color;
      });
    });

    // Quantity control
    function changeQuantity(change) {
      const quantityInput = document.getElementById('quantity');
      let newValue = parseInt(quantityInput.value) + change;
      const maxStock = parseInt(quantityInput.max);
      
      if (newValue < 1) newValue = 1;
      if (newValue > maxStock) newValue = maxStock;
      
      quantityInput.value = newValue;
    }

     function buyNow() {
        document.getElementById('beli_sekarang').value = '1';
        document.getElementById('productForm').submit();
    }
    
    function addToCart() {
        document.getElementById('beli_sekarang').value = '0';
        document.getElementById('productForm').submit();
    }
  </script>
</body>
</html>

<?php
// Close database connection
$koneksi->close();
?>