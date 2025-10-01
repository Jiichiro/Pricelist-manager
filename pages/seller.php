<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Penjualan</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      background-color: #f5f5f5;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 40px 20px;
    }

    h1 {
      text-align: center;
      color: #2c3e50;
      margin-bottom: 20px;
      font-size: 32px;
    }

    .info-text {
      text-align: center;
      color: #666;
      margin-bottom: 10px;
    }

    .info-text a {
      color: #3498db;
      text-decoration: none;
    }

    .warning {
      text-align: center;
      color: #e74c3c;
      margin-bottom: 30px;
    }

    .filter-section {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      margin-bottom: 20px;
      gap: 10px;
    }

    .filter-section label {
      font-weight: bold;
    }

    .filter-section select {
      padding: 8px 15px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 14px;
    }

    .filter-section button {
      padding: 8px 25px;
      background-color: #3498db;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
    }

    .filter-section button:hover {
      background-color: #2980b9;
    }

    table {
      width: 100%;
      background-color: white;
      border-collapse: collapse;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    th {
      background-color: #3498db;
      color: white;
      padding: 15px;
      text-align: center;
      font-weight: bold;
    }

    td {
      padding: 15px;
      text-align: center;
      border-bottom: 1px solid #ecf0f1;
    }

    tr:hover {
      background-color: #f8f9fa;
    }

    .export-buttons {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-top: 30px;
    }

    .btn-excel {
      padding: 12px 30px;
      background-color: #27ae60;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .btn-excel:hover {
      background-color: #229954;
    }

    .btn-pdf {
      padding: 12px 30px;
      background-color: #e67e22;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .btn-pdf:hover {
      background-color: #d35400;
    }

    footer {
      background-color: #1a252f;
      color: white;
      padding: 40px 20px;
      margin-top: 60px;
    }

    .footer-content {
      max-width: 1200px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 30px;
    }

    .footer-section h3 {
      margin-bottom: 15px;
      font-size: 18px;
    }

    .footer-section p,
    .footer-section a {
      color: #bbb;
      line-height: 1.8;
      text-decoration: none;
      display: block;
      margin-bottom: 8px;
    }

    .footer-section a:hover {
      color: #3498db;
    }
  </style>
</head>

<body>
  <div class="container">
    <h1>Dashboard Penjualan</h1>

    <p class="info-text">
      Halaman ini menampilkan <strong>Katalog / Pricelist</strong> produk.<br>
      Anda bisa melakukan <a href="#">filter kategori</a> dan <a href="#">export data ke Excel / PDF</a>.
    </p>

    <p class="warning">
      ❌ <strong>Tidak tersedia fitur edit produk.</strong>
    </p>

    <div class="filter-section">
      <label for="kategori">Kategori:</label>
      <select id="kategori" name="kategori">
        <option value="semua">Semua</option>
        <option value="elektronik">Elektronik</option>
        <option value="pakaian">Pakaian</option>
      </select>
      <button type="button">Filter</button>
    </div>

    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Produk</th>
          <th>Kategori</th>
          <th>Harga</th>
          <th>Stok</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>1</td>
          <td>Produk Dummy 1</td>
          <td>Elektronik</td>
          <td>Rp 25.000</td>
          <td>15</td>
        </tr>
        <tr>
          <td>2</td>
          <td>Produk Dummy 2</td>
          <td>Pakaian</td>
          <td>Rp 75.000</td>
          <td>8</td>
        </tr>
        <tr>
          <td>3</td>
          <td>Produk Dummy 3</td>
          <td>Elektronik</td>
          <td>Rp 120.000</td>
          <td>5</td>
        </tr>
      </tbody>
    </table>

    <div class="export-buttons">
      <a href="export.php?type=excel" class="btn-excel">
        📊 Export ke Excel
      </a>
      <a href="export.php?type=pdf" class="btn-pdf">
        📄 Export ke PDF
      </a>
    </div>

  </div>

  </div>

  <script>
    function filterTable() {
      const kategori = document.getElementById("kategori").value;
      const rows = document.querySelectorAll("tbody tr");

      rows.forEach(row => {
        const kategoriCell = row.cells[2].textContent;
        if (kategori === "semua" || kategoriCell === kategori) {
          row.style.display = "";
        } else {
          row.style.display = "none";
        }
      });
    }

    // Tambahkan event listener ke tombol filter
    document.querySelector(".filter-section button").addEventListener("click", filterTable);
  </script>

  <footer>
    <div class="footer-content">
      <div class="footer-section">
        <h3>Tentang Aplikasi</h3>
        <p>Website internal untuk manajemen pricelist dan katalog produk dengan role-based access (Super Admin &
          Penjualan). Dibuat</p>
      </div>
      <div class="footer-section">
        <h3>Fitur Utama</h3>
        <a href="#">Manajemen Pricelist</a>
        <a href="#">Manajemen Katalog</a>
        <a href="#">Hak Akses Super Admin</a>
      </div>
      <div class="footer-section">
        <h3>Informasi</h3>
        <a href="#">Tentang Sistem</a>
        <a href="#">Dokumentasi</a>
        <a href="#">FAQ</a>
      </div>
      <div class="footer-section">
        <h3>Kontak</h3>
        <p>📍 Jl. Teknologi No. 45, Jakarta</p>
        <p>📞 +62 812-3456-7890</p>
        <p>✉️ info@pricelist.com</p>
      </div>
    </div>
  </footer>
</body>

</html>

<?php
// Tutup koneksi database jika ada
if (isset($conn) && $conn !== null) {
  $conn->close();
}
?>