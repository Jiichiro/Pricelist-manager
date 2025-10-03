<?php
require __DIR__ . "../../logic/database/connect.php";
// Cek koneksi
if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil productId dari URL
if (isset($_GET['productId'])) {
  $productId = intval($_GET['productId']); // biar aman

  $sql = "SELECT * FROM produk WHERE id = $productId";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    $produk = $result->fetch_assoc();
  } else {
    echo "<h2>Produk tidak ditemukan!</h2>";
    exit;
  }
} else {
  echo "<h2>ID Produk tidak ditemukan!</h2>";
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Produk</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
      background: #f4f6f9;
      color: #333;
    }

    header {
      background: #0d1b2a;
      /* biru navy gelap */
      color: white;
      padding: 15px 30px;
      text-align: center;
    }

    header h1 {
      margin: 0;
      font-size: 22px;
    }

    .container {
      max-width: 900px;
      margin: 30px auto;
      background: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .product-container {
      display: flex;
      flex-wrap: wrap;
      gap: 30px;
      align-items: flex-start;
    }

    .product-image {
      flex: 1;
      text-align: center;
    }

    .product-image img {
      max-width: 100%;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .product-info {
      flex: 2;
    }

    .product-info h2 {
      margin-top: 0;
      font-size: 26px;
      color: #0d1b2a;
    }

    .price {
      color: #28a745;
      /* hijau sama dengan dashboard */
      font-size: 22px;
      font-weight: bold;
      margin: 15px 0;
    }

    .stock {
      background: #e9ecef;
      display: inline-block;
      padding: 6px 12px;
      border-radius: 6px;
      font-size: 14px;
      margin-bottom: 15px;
      color: #495057;
    }

    .desc {
      line-height: 1.6;
      margin-bottom: 20px;
    }

    .btn {
      display: inline-block;
      margin-top: 10px;
      padding: 10px 18px;
      text-decoration: none;
      border-radius: 6px;
      font-size: 14px;
      font-weight: 500;
      transition: 0.3s;
    }

    .btn-back {
      background: #0d6efd;
      /* biru */
      color: white;
    }

    .btn-back:hover {
      background: #0a58ca;
    }
  </style>
</head>

<body>
  <header>
    <h1>Detail Produk</h1>
  </header>

  <div class="container">
    <div class="product-container">
      <div class="product-image">
        <img src="uploads/<?php echo htmlspecialchars($produk['gambar_url']); ?>" alt="<?php echo htmlspecialchars($produk['nama_produk']); ?>">
      </div>
      <div class="product-info">
        <h2><?php echo htmlspecialchars($produk['nama_produk']); ?></h2>
        <p class="price">Rp <?php echo number_format($produk['harga'], 0, ',', '.'); ?></p>
        <p class="stock">Stok: <?php echo (int)$produk['stok']; ?></p>
        <p class="desc"><strong>Deskripsi:</strong> <br> <?php echo nl2br(htmlspecialchars($produk['deskripsi'])); ?></p>

        <a href="index.php" class="btn btn-back">⬅ Kembali</a>
      </div>
    </div>
  </div>
</body>

</html>