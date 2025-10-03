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
<style>
 <style>
  /* Reset & dasar */
  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }

  html, body {
    width: 100%;
    overflow-x: hidden;
  }  

  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f4f6f9;
    color: #333;
  }

  header {
    background: #0d1b2a;
    color: white;
    padding: 12px;
    text-align: center;
  }

  header h1 {
    font-size: 18px;
  }

  .container {
    max-width: 900px;
    width: 100%;
    margin: 20px auto;
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
  }

  .product-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
    align-items: flex-start;
  }

  .product-image {
    flex: 1 1 100%;
    text-align: center;
  }

  .product-image img {
    max-width: 100%;
    height: auto;
    border-radius: 10px;
    display: block;
    margin: 0 auto;
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
  }

  .product-info {
    flex: 1 1 100%;
    text-align: center;
  }

  .product-info h2 {
    font-size: 20px;
    margin-top: 10px;
    color: #0d1b2a;
  }

  .price {
    color: #28a745;
    font-size: 18px;
    font-weight: bold;
    margin: 10px 0;
  }

  .stock {
    background: #e9ecef;
    display: inline-block;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 14px;
    margin-bottom: 10px;
    color: #495057;
  }

  .desc {
    font-size: 14px;
    line-height: 1.5;
    margin-top: 5px;
  }

  .btn {
    display: inline-block;
    margin-top: 15px;
    padding: 10px 15px;
    text-decoration: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    transition: 0.3s;
  }

  .btn-back {
    background: #0d6efd;
    color: white;
  }

  .btn-back:hover {
    background: #0a58ca;
  }

  /* 📱 Mobile ≤ 480px */
  @media (max-width: 480px) {
    header h1 {
      font-size: 16px;
    }

    .container {
      max-width: 95%;
      margin: 10px auto;
      padding: 15px;
    }

    .product-info h2 {
      font-size: 18px;
    }

    .price {
      font-size: 16px;
    }

    .desc {
      font-size: 13px;
    }

    .btn-back {
      display: block;
      width: 100%;
      text-align: center;
    }
  }

  /* 📲 Tablet 481px–768px */
  @media (min-width: 481px) and (max-width: 768px) {
    .container {
      max-width: 90%;
    }

    .product-info h2 {
      font-size: 20px;
    }

    .price {
      font-size: 18px;
    }
  }

  /* 💻 Laptop 769px–1164px */
  @media (min-width: 769px) and (max-width: 1164px) {
    .product-container {
      flex-wrap: nowrap;
    }

    .product-image {
      flex: 1;
    }

    .product-info {
      flex: 1;
      text-align: left;
    }
  }

  /* 🖥 Desktop ≥ 1165px */
  @media (min-width: 1165px) {
    .product-container {
      flex-wrap: nowrap;
    }

    .product-info {
      text-align: left;
    }
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
