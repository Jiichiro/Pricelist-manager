<?php
// seller.php (dipasang satu file utuh)

// Start session hanya jika belum ada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Validasi login dan role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'penjualan') {
    header("Location: ../login.php");
    exit();
}

// Koneksi database
require __DIR__ . "/../logic/database/connect.php";

/* ---------------------------
   EXPORT HANDLER (Excel / PDF)
   ---------------------------
   Endpoint internal:
   ?action=export_excel&kategori=ID
   ?action=export_pdf&kategori=ID
*/
if (isset($_GET['action']) && isset($_GET['kategori'])) {
    $action   = $_GET['action'];
    $kategori = (int) $_GET['kategori'];

    // Validasi: kategori harus dipilih (tidak boleh 0 atau "all")
    if ($kategori <= 0) {
        die("❌ Kategori tidak valid. Silakan pilih salah satu kategori terlebih dahulu.");
    }

    // Ambil produk sesuai kategori (gabung nama kategori)
    $stmt = $conn->prepare("
        SELECT p.id, p.nama_produk, p.harga, p.stok, p.deskripsi, p.gambar_url, k.nama_kategori
        FROM produk p
        LEFT JOIN kategori k ON p.id_kategori = k.id
        WHERE p.id_kategori = ?
        ORDER BY p.id ASC
    ");
    $stmt->bind_param('i', $kategori);
    $stmt->execute();

    // dapatkan result (jika get_result tersedia)
    $resExport = null;
    if (method_exists($stmt, 'get_result')) {
        $resExport = $stmt->get_result();
    } else {
        // fallback manual: kumpulkan ke array
        $resExport = [];
        $stmt->bind_result($fid, $fnama, $fharga, $fstok, $fdes, $fgambar, $fnama_kat);
        while ($stmt->fetch()) {
            $resExport[] = [
                'id' => $fid,
                'nama_produk' => $fnama,
                'harga' => $fharga,
                'stok' => $fstok,
                'deskripsi' => $fdes,
                'gambar_url' => $fgambar,
                'nama_kategori' => $fnama_kat
            ];
        }
    }

    // EXPORT EXCEL (XLS simple, TSV) ----------------
    if ($action === 'export_excel') {
        header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
        header("Content-Disposition: attachment; filename=produk_export_kategori_{$kategori}.xls");
        // BOM agar Excel membaca UTF-8
        echo "\xEF\xBB\xBF";
        echo "ID\tNama Produk\tHarga\tStok\tKategori\tDeskripsi\n";

        if (is_array($resExport)) {
            foreach ($resExport as $r) {
                $desc = str_replace(["\r", "\n", "\t"], ' ', $r['deskripsi']);
                echo "{$r['id']}\t{$r['nama_produk']}\t{$r['harga']}\t{$r['stok']}\t{$r['nama_kategori']}\t{$desc}\n";
            }
        } else {
            while ($row = $resExport->fetch_assoc()) {
                $desc = str_replace(["\r", "\n", "\t"], ' ', $row['deskripsi']);
                echo "{$row['id']}\t{$row['nama_produk']}\t{$row['harga']}\t{$row['stok']}\t{$row['nama_kategori']}\t{$desc}\n";
            }
        }

        $stmt->close();
        $conn->close();
        exit();
    }

    // EXPORT PDF (Manual - Tanpa Library) ----------
    if ($action === 'export_pdf') {
        // pastikan $rows adalah array
        if (is_array($resExport)) {
            $rows = $resExport;
        } else {
            $rows = $resExport->fetch_all(MYSQLI_ASSOC);
        }

        // Ambil nama kategori untuk judul
        $namaKategori = !empty($rows[0]['nama_kategori']) ? $rows[0]['nama_kategori'] : "Kategori {$kategori}";

        // Build HTML yang siap di-print sebagai PDF
        $html = "<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Export Produk - {$namaKategori}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: Arial, Helvetica, sans-serif; 
            padding: 20px;
            background: white;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #3498db;
        }
        h1 { 
            color: #2c3e50; 
            font-size: 24px;
            margin-bottom: 5px;
        }
        .subtitle {
            color: #7f8c8d;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background: #3498db;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        tr:hover {
            background: #ecf0f1;
        }
        .no-print {
            text-align: center;
            margin: 20px 0;
        }
        .btn-print {
            background: #27ae60;
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .btn-print:hover {
            background: #219150;
        }
        @media print {
            .no-print { display: none; }
            body { padding: 10px; }
        }
    </style>
</head>
<body>
    <div class='header'>
        <h1>Export Produk</h1>
        <div class='subtitle'>Kategori: {$namaKategori} | Tanggal: " . date('d-m-Y H:i') . "</div>
    </div>
    
    <div class='no-print'>
        <button class='btn-print' onclick='window.print()'>🖨️ Cetak / Save as PDF</button>
        <p style='margin-top:10px; color:#7f8c8d; font-size:13px;'>
            Klik tombol di atas, lalu pilih <strong>\"Save as PDF\"</strong> sebagai printer
        </p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th style='width:50px;'>ID</th>
                <th>Nama Produk</th>
                <th style='width:150px;'>Harga</th>
                <th style='width:80px;'>Stok</th>
                <th style='width:120px;'>Kategori</th>
            </tr>
        </thead>
        <tbody>";
        
        foreach ($rows as $r) {
            $html .= "<tr>
                <td>{$r['id']}</td>
                <td>" . htmlspecialchars($r['nama_produk']) . "</td>
                <td>Rp " . number_format($r['harga'], 0, ',', '.') . "</td>
                <td style='text-align:center;'>{$r['stok']}</td>
                <td>" . htmlspecialchars($r['nama_kategori']) . "</td>
            </tr>";
        }
        
        $html .= "</tbody>
    </table>
</body>
</html>";

        // Bersihkan output buffer
        if (ob_get_length()) ob_end_clean();
        
        // Output HTML
        header("Content-Type: text/html; charset=UTF-8");
        echo $html;

        $stmt->close();
        $conn->close();
        exit();
    }

    $stmt->close();
}

/* ---------------------------
   NORMAL PAGE: tampilkan katalog
   --------------------------- */
// Ambil kategori untuk dropdown (id digunakan untuk filter)
$kategoriRes = $conn->query("SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori ASC");
// Ambil produk
$result = $conn->query("SELECT * FROM produk ORDER BY id ASC");

// include header (jika ada) — include_once supaya tidak duplikat
$headerPath = __DIR__ . "/../components/header.php";
if (file_exists($headerPath)) include_once $headerPath;
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <title>Dashboard Penjualan</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <style>
    .dashboard-container{max-width:1100px;margin:30px auto;padding:30px;background:#fff;border-radius:14px;box-shadow:0 6px 14px rgba(0,0,0,0.08);font-family:'Segoe UI',sans-serif; animation:fadeIn .8s ease-in-out;}
    .dashboard-container h1{ text-align:center;color:#2c3e50;margin-bottom:8px; }
    .dashboard-container p{ text-align:center;font-size:15px;color:#555;margin-bottom:25px;line-height:1.6;background:#f8f9fa;padding:12px;border-radius:8px; }
    .filter-box{ text-align:right;margin-bottom:12px; }
    .filter-box select,.filter-box button{ padding:8px 12px;border-radius:8px;border:1px solid #ccc;font-size:14px; }
    .filter-box button{ margin-left:8px;background:#3498db;color:white;border:none;cursor:pointer; }
    .filter-box button:hover{ background:#2980b9; }

    /* grid */
    .product-grid{ display:grid; gap:16px; }
    @media (max-width:480px){ .product-grid{ grid-template-columns:repeat(2,1fr); } }
    @media (min-width:481px) and (max-width:768px){ .product-grid{ grid-template-columns:repeat(4,1fr); } }
    @media (min-width:769px) and (max-width:1164px){ .product-grid{ grid-template-columns:repeat(6,1fr); } }
    @media (min-width:1165px){ .product-grid{ grid-template-columns:repeat(7,1fr); } }

    .product-card{ border:1px solid #e6e6e6; border-radius:10px; overflow:hidden; text-align:center; background:#fff; box-shadow:0 2px 6px rgba(0,0,0,0.04); transition:transform .15s ease, box-shadow .15s ease; display:block; color:inherit; text-decoration:none; padding-bottom:8px; }
    .product-card:hover{ transform:translateY(-4px); box-shadow:0 6px 18px rgba(0,0,0,0.08); }
    .product-card img{ width:100%; height:140px; object-fit:cover; display:block; background:#fafafa; }
    .name{ font-size:14px; font-weight:600; margin:8px 0 4px; color:#2c3e50; }
    .price{ font-size:13px; color:#27ae60; margin-bottom:6px; }

    .export-buttons{ text-align:center; margin-top:20px; }
    .export-buttons button{ padding:10px 18px; margin:6px; border:none; border-radius:8px; font-size:14px; cursor:pointer; color:#fff; }
    .btn-excel{ background:#27ae60; } .btn-excel:hover{ background:#219150; }
    .btn-pdf{ background:#e67e22; } .btn-pdf:hover{ background:#ca6b1e; }

    @keyframes fadeIn{ from{opacity:0; transform:translateY(10px);} to{opacity:1; transform:translateY(0);} }
  </style>
</head>
<body>
  <div class="dashboard-container">
    <h1>Dashboard Penjualan</h1>
    <p>Pilih kategori untuk melihat produk. Anda bisa export data ke Excel atau PDF.</p>

    <div class="filter-box">
      <label for="kategori">Kategori:</label>
      <select id="kategori">
        <option value="all">Semua</option>
        <?php while ($kat = $kategoriRes->fetch_assoc()): ?>
          <option value="<?= (int)$kat['id']; ?>"><?= htmlspecialchars($kat['nama_kategori']); ?></option>
        <?php endwhile; ?>
      </select>
      <button onclick="filterGrid()">Filter</button>
    </div>

    <div class="product-grid" id="productGrid">
      <?php while ($row = $result->fetch_assoc()): ?>
        <a href="?productId=<?= (int)$row['id']; ?>" class="product-card" data-kategori="<?= (int)$row['id_kategori']; ?>">
          <?php $img = !empty($row['gambar_url']) ? "uploads/" . $row['gambar_url'] : "https://via.placeholder.com/300x200?text=No+Image"; ?>
          <img src="<?= htmlspecialchars($img); ?>" alt="<?= htmlspecialchars($row['nama_produk']); ?>">
          <div class="name"><?= htmlspecialchars($row['nama_produk']); ?></div>
          <div class="price">Rp <?= number_format($row['harga'],0,',','.'); ?></div>
        </a>
      <?php endwhile; ?>
    </div>

    <div class="export-buttons">
      <button class="btn-excel" onclick="exportData('excel')">📊 Export ke Excel</button>
      <button class="btn-pdf" onclick="exportData('pdf')">📄 Export ke PDF</button>
    </div>
  </div>

<script>
function filterGrid(){
  const kategori = document.getElementById('kategori').value;
  document.querySelectorAll('#productGrid .product-card').forEach(card=>{
    if(kategori === 'all' || card.dataset.kategori === kategori.toString()){
      card.style.display = 'block';
    } else {
      card.style.display = 'none';
    }
  });
}
function exportData(type){
  const kategori = document.getElementById('kategori').value;
  if(kategori === 'all'){
    alert('⚠️ Silakan pilih salah satu kategori terlebih dahulu sebelum melakukan export!');
    return;
  }
  window.location.href = '?action=export_' + type + '&kategori=' + encodeURIComponent(kategori);
}
</script>
</body>
</html>
<?php
// Tutup koneksi database jika ada
if (isset($conn) && $conn !== null) {
  $conn->close();
}
?>