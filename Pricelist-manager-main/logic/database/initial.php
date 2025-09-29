<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/prielist-manager/config.php');

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if ($conn->query($sql) !== TRUE) {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($dbname);

// Create table if not exists
if ($conn->query("CREATE TABLE IF NOT EXISTS users (
    id BIGINT(30) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    username varchar(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'penjualan') DEFAULT 'penjualan' NOT NULL
)") !== TRUE) {
    die("Error creating table: " . $conn->error);
}

if ($conn->query("CREATE TABLE IF NOT EXISTS kategori (
    id BIGINT(30) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(255) NOT NULL,
    deskripsi TEXT
)") !== TRUE) {
    die("Error creating table: " . $conn->error);
}

if ($conn->query("CREATE TABLE IF NOT EXISTS produk (
    id BIGINT(30) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_produk VARCHAR(255) NOT NULL,
    id_kategori BIGINT(30) UNSIGNED,
    harga DECIMAL(10, 2) NOT NULL,
    stok INT(11) NOT NULL,
    deskripsi TEXT,
    gambar_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_kategori) REFERENCES kategori(id) ON DELETE SET NULL
)") !== TRUE) {
    die("Error creating table: " . $conn->error);
}

// Insert dummy data for users
// Insert dummy data for users only if table is empty
$result = $conn->query("SELECT COUNT(*) as count FROM users");
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    $conn->query("INSERT INTO users (name, username, password, role) VALUES
        ('Admin User', 'admin', '" . password_hash('admin123', PASSWORD_DEFAULT) . "', 'super_admin'),
        ('Sales User', 'sales', '" . password_hash('sales123', PASSWORD_DEFAULT) . "', 'penjualan')
    ");
}

// Insert dummy data for kategori only if table is empty
$result = $conn->query("SELECT COUNT(*) as count FROM kategori");
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    $conn->query("INSERT INTO kategori (nama_kategori, deskripsi) VALUES
        ('Elektronik', 'Produk elektronik'),
        ('Pakaian', 'Produk pakaian')
    ");
}

// Get kategori IDs
$kategori_ids = [];
$result = $conn->query("SELECT id FROM kategori");
while ($row = $result->fetch_assoc()) {
    $kategori_ids[] = $row['id'];
}

// Insert dummy data for produk (10 products) only if table is empty
$result = $conn->query("SELECT COUNT(*) as count FROM produk");
$row = $result->fetch_assoc();
if ($row['count'] == 0 && !empty($kategori_ids)) {
    for ($i = 1; $i <= 10; $i++) {
        $nama_produk = "Produk Dummy $i";
        $id_kategori = $kategori_ids[array_rand($kategori_ids)];
        $harga = rand(10000, 100000);
        $stok = rand(1, 50);
        $deskripsi = "Deskripsi produk dummy ke-$i";
        $gambar_url = "produk$i.jpg";
        $conn->query("INSERT INTO produk (nama_produk, id_kategori, harga, stok, deskripsi, gambar_url)
            VALUES (
                '$nama_produk',
                $id_kategori,
                $harga,
                $stok,
                '$deskripsi',
                '$gambar_url'
            )
        ");
    }
}

$conn->close();