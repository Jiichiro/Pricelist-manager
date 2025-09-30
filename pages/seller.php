<?php
// Validasi login dan role
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'penjualan') {
    header("Location: " . __DIR__ . "/../login.php");
    exit();
}

require __DIR__ . "/../components/header.php";

// Koneksi database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "pricelist_manager";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data produk
$sql = "SELECT * FROM produk ORDER BY id ASC";
$result = $conn->query($sql);
?>
<style>
.dashboard-container {
  max-width: 1100px;
  margin: 30px auto;
  padding: 30px;
  background: #ffffff;
  border-radius: 14px;
  box-shadow: 0 6px 14px rgba(0,0,0,0.08);
  font-family: 'Segoe UI', sans-serif;
  animation: fadeIn 0.8s ease-in-out;
}
.dashboard-container h1 {
  text-align: center;
  color: #2c3e50;
  margin-bottom: 8px;
}
.dashboard-container p {
  text-align: center;
  font-size: 15px;
  color: #555;
  margin-bottom: 25px;
  line-height: 1.6;
  background: #f8f9fa;
  padding: 12px;
  border-radius: 8px;
}
.filter-box {
  text-align: right;
  margin-bottom: 12px;
}
.filter-box select, .filter-box button {
  padding: 8px 12px;
  border-radius: 8px;
  border: 1px solid #ccc;
  font-size: 14px;
  transition: 0.3s;
}
.filter-box button {
  margin-left: 8px;
  background: #3498db;
  color: white;
  border: none;
  cursor: pointer;
}
.filter-box button:hover {
  background: #2980b9;
}
/* GRID PRODUK */
.product-grid {
  display: grid;
  gap: 16px;
}
@media (max-width: 480px) {
  .product-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (min-width: 481px) and (max-width: 768px) {
  .product-grid { grid-template-columns: repeat(4, 1fr); }
}
@media (min-width: 769px) and (max-width: 1164px) {
  .product-grid { grid-template-columns: repeat(6, 1fr); }
}
@media (min-width: 1165px) {
  .product-grid { grid-template-columns: repeat(7, 1fr); }
}
.product-card {
  border: 1px solid #ddd;
  border-radius: 12px;
  overflow: hidden;
  text-align: center;
  background: #fff;
  box-shadow: 0 2px 6px rgba(0,0,0,0.05);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.product-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.product-card img {
  width: 100%;
  height: 120px;
  object-fit: cover;
}
.product-card .name {
  font-size: 14px;
  font-weight: 600;
  margin: 8px 0 4px;
  color: #2c3e50;
}
.product-card .price {
  font-size: 13px;
  color: #27ae60;
  margin-bottom: 10px;
}
.export-buttons {
  text-align: center;
  margin-top: 20px;
}
.export-buttons button {
  padding: 12px 22px;
  margin: 5px;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  cursor: pointer;
  transition: 0.3s;
  color: white;
}
.btn-excel { background: #27ae60; }
.btn-excel:hover { background: #219150; }
.btn-pdf { background: #e67e22; }
.btn-pdf:hover { background: #ca6b1e; }

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>

<div class="dashboard-container">
  <h1>Dashboard Penjualan</h1>
  <p>
    Halaman ini menampilkan <b>Katalog / Pricelist</b> produk.<br>
    Anda bisa melakukan <span style="color:#2980b9;">filter kategori</span> dan 
    <span style="color:#e67e22;">export data ke Excel / PDF</span>. <br>
    <b style="color:#e74c3c;">❌ Tidak tersedia fitur edit produk.</b>
  </p>

  <div class="filter-box">
    <label for="kategori" style="font-weight:bold; margin-right:8px;">Kategori:</label>
    <select id="kategori">
      <option value="all">Semua</option>
      <option value="elektronik">Elektronik</option>
      <option value="pakaian">Pakaian</option>
    </select>
    <button onclick="filterGrid()">Filter</button>
  </div>

  <!-- Grid Produk -->
  <div class="product-grid" id="productGrid">
    <?php while ($row = $result->fetch_assoc()): ?>
      <a href="?productId=<?php echo $row['id']; ?>" 
         class="product-card" 
         data-kategori="<?php echo $row['id_kategori']; ?>">
        <img src="uploads/<?php echo $row['gambar_url']; ?>" alt="<?php echo htmlspecialchars($row['nama_produk']); ?>">
        <div class="name"><?php echo htmlspecialchars($row['nama_produk']); ?></div>
        <div class="price">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></div>
      </a>
    <?php endwhile; ?>
  </div>

  <!-- Tombol export -->
  <div class="export-buttons">
    <button class="btn-excel">📊 Export ke Excel</button>
    <button class="btn-pdf">📄 Export ke PDF</button>
  </div>
</div>

<script>
function filterGrid() {
  const kategori = document.getElementById("kategori").value;
  const cards = document.querySelectorAll("#productGrid .product-card");
  cards.forEach(card => {
    if (kategori === "all" || card.dataset.kategori === kategori) {
      card.style.display = "block";
    } else {
      card.style.display = "none";
    }
  });
}
</script>

<?php
require __DIR__ . "/../components/footer.php";
$conn->close();
?>                               