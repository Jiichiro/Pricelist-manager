<?php
// Validasi login dan role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'penjualan') {
    header("Location: ".__DIR__."../login.php");
    exit();
}

require __DIR__."../../components/header.php"
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
    .filter-box select {
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 14px;
        transition: 0.3s;
    }
    .filter-box select:focus {
        border-color: #3498db;
        outline: none;
    }
    .filter-box button {
        padding: 8px 14px;
        margin-left: 8px;
        border: none;
        border-radius: 8px;
        background: #3498db;
        color: white;
        font-size: 14px;
        cursor: pointer;
        transition: 0.3s;
    }
    .filter-box button:hover {
        background: #2980b9;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        font-size: 14px;
    }
    table thead tr {
        background: linear-gradient(90deg, #3498db, #2980b9);
        color: white;
    }
    table th, table td {
        padding: 12px;
        border: 1px solid #ddd;
        text-align: center;
    }
    table tbody tr:hover {
        background: #f9f9f9;
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
        Anda bisa melakukan <span style="color:#2980b9;">filter kategori</span> 
        dan <span style="color:#e67e22;">export data ke Excel / PDF</span>.  
        <br><b style="color:#e74c3c;">❌ Tidak tersedia fitur edit produk.</b>
    </p>

    
    <div class="filter-box">
        <label for="kategori" style="font-weight:bold; margin-right:8px;">Kategori:</label>
        <select id="kategori">
            <option value="all">Semua</option>
            <option value="elektronik">Elektronik</option>
            <option value="pakaian">Pakaian</option>
        </select>
        <button onclick="filterTable()">Filter</button>
    </div>

    
    <table id="productTable">
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
            <tr data-kategori="elektronik">
                <td>1</td>
                <td>Produk Dummy 1</td>
                <td>Elektronik</td>
                <td>Rp 25.000</td>
                <td>15</td>
            </tr>
            <tr data-kategori="pakaian">
                <td>2</td>
                <td>Produk Dummy 2</td>
                <td>Pakaian</td>
                <td>Rp 75.000</td>
                <td>8</td>
            </tr>
            <tr data-kategori="elektronik">
                <td>3</td>
                <td>Produk Dummy 3</td>
                <td>Elektronik</td>
                <td>Rp 120.000</td>
                <td>5</td>
            </tr>
        </tbody>
    </table>

    <!-- Tombol export -->
    <div class="export-buttons">
        <button class="btn-excel">📊 Export ke Excel</button>
        <button class="btn-pdf">📄 Export ke PDF</button>
    </div>
</div>

<script>
function filterTable() {
    const kategori = document.getElementById("kategori").value;
    const rows = document.querySelectorAll("#productTable tbody tr");
    
    rows.forEach(row => {
        if (kategori === "all" || row.dataset.kategori === kategori) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
}
</script>

<?php require __DIR__."../../components/footer.php" ?>
