<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'penjualan') {
    header("Location: ../login.php");
    exit();
}


$host = "localhost";
$user = "root";
$pass = "";
$db   = "pricelist_manager";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}


if (isset($_GET['action']) && isset($_GET['kategori'])) {
    $action   = $_GET['action'];
    $kategori = (int) $_GET['kategori'];

    if ($kategori <= 0) {
        die("Kategori tidak valid.");
    }

    
    $stmt = $conn->prepare("
        SELECT p.id, p.nama_produk, p.harga, p.stok, p.deskripsi, p.gambar_url, k.nama_kategori
        FROM produk p
        LEFT JOIN kategori k ON p.id_kategori = k.id
        WHERE p.id_kategori = ?
        ORDER BY p.id ASC
    ");
    $stmt->bind_param('i', $kategori);
    $stmt->execute();

    
    $resExport = null;
    if (method_exists($stmt, 'get_result')) {
        $resExport = $stmt->get_result();
    } else {
        
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

    
    if ($action === 'export_excel') {
        header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
        header("Content-Disposition: attachment; filename=produk_export_kategori_{$kategori}.xls");
        
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

    
    if ($action === 'export_pdf') {
        $autoload = __DIR__ . "/../vendor/autoload.php";
        if (file_exists($autoload)) {
            require_once $autoload;
            if (!class_exists('\Dompdf\Dompdf')) {
                die("⚠️ Dompdf belum tersedia. Install: composer require dompdf/dompdf");
            }

            
            if (is_array($resExport)) {
                $rows = $resExport;
            } else {
                $rows = $resExport->fetch_all(MYSQLI_ASSOC);
            }

            
            $html = "<html><head><meta charset='utf-8'><style>
                table{border-collapse:collapse;width:100%;font-family:Arial,Helvetica,sans-serif}
                th,td{border:1px solid #ddd;padding:6px;font-size:12px}
                th{background:#f3f3f3}
                </style></head><body>";
            $html .= "<h3 style='text-align:center;'>Export Produk - Kategori ID: {$kategori}</h3>";
            $html .= "<table><thead><tr><th>ID</th><th>Nama Produk</th><th>Harga</th><th>Stok</th><th>Kategori</th></tr></thead><tbody>";
            foreach ($rows as $r) {
                $html .= "<tr>
                    <td>{$r['id']}</td>
                    <td>" . htmlspecialchars($r['nama_produk']) . "</td>
                    <td>Rp " . number_format($r['harga'], 0, ',', '.') . "</td>
                    <td>{$r['stok']}</td>
                    <td>" . htmlspecialchars($r['nama_kategori']) . "</td>
                </tr>";
            }
            $html .= "</tbody></table></body></html>";

            
            if (ob_get_length()) ob_end_clean();

            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $dompdf->stream("produk_export_kategori_{$kategori}.pdf", ["Attachment" => true]);

            $stmt->close();
            $conn->close();
            exit();
        } else {
            
            header("Content-Type: text/html; charset=UTF-8");
            echo "<h2>Export Produk - Kategori ID: {$kategori}</h2>";
            echo "<p><strong>⚠️ Dompdf tidak ditemukan.</strong> Buka file ini lalu Print → Save as PDF.</p>";
            echo "<table border='1' cellpadding='6' cellspacing='0' style='border-collapse:collapse; width:100%'>";
            echo "<thead><tr><th>ID</th><th>Nama</th><th>Harga</th><th>Stok</th><th>Kategori</th></tr></thead><tbody>";
            if (is_array($resExport)) {
                foreach ($resExport as $r) {
                    echo "<tr>
                        <td>{$r['id']}</td>
                        <td>" . htmlspecialchars($r['nama_produk']) . "</td>
                        <td>Rp " . number_format($r['harga'],0,',','.') . "</td>
                        <td>{$r['stok']}</td>
                        <td>" . htmlspecialchars($r['nama_kategori']) . "</td>
                    </tr>";
                }
            } else {
                while ($row = $resExport->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>" . htmlspecialchars($row['nama_produk']) . "</td>
                        <td>Rp " . number_format($row['harga'],0,',','.') . "</td>
                        <td>{$row['stok']}</td>
                        <td>" . htmlspecialchars($row['nama_kategori']) . "</td>
                    </tr>";
                }
            }
            echo "</tbody></table>";
            $stmt->close();
            $conn->close();
            exit();
        }
    }

    $stmt->close();
}


$kategoriRes = $conn->query("SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori ASC");

$result = $conn->query("SELECT * FROM produk ORDER BY id ASC");


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
      <button class="btn-pdf" onclick="exportD ata('pdf')">📄 Export ke PDF</button>
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
  if(kategori === 'all'){ alert('⚠️ Pilih kategori dulu sebelum export!'); return; }
  window.location.href = '?action=export_' + type + '&kategori=' + encodeURIComponent(kategori);
}
</script>
</body>
</html>
<?php

$footerPath = __DIR__ . "/../components/footer.php";
if (file_exists($footerPath)) include_once $footerPath;


$conn->close();
?>
