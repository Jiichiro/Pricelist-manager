<?php
require __DIR__ . "../../logic/database/connect.php";

// --- Handle form submissions (Add or Edit) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_produk = $_POST['nama_produk'];
    $id_kategori = !empty($_POST['id_kategori']) ? intval($_POST['id_kategori']) : null;
    $harga = floatval($_POST['harga']);
    $stok = intval($_POST['stok']);
    $deskripsi = $_POST['deskripsi'];
    $gambar_url = $_POST['gambar_url'];
    $editId = $_POST['editId'];

    if (!empty($editId)) {
        // Update existing produk
        $stmt = $conn->prepare("UPDATE produk SET nama_produk=?, id_kategori=?, harga=?, stok=?, deskripsi=?, gambar_url=? WHERE id=?");
        $stmt->bind_param("sidissi", $nama_produk, $id_kategori, $harga, $stok, $deskripsi, $gambar_url, $editId);
    } else {
        // Insert new produk
        $stmt = $conn->prepare("INSERT INTO produk (nama_produk, id_kategori, harga, stok, deskripsi, gambar_url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sidiss", $nama_produk, $id_kategori, $harga, $stok, $deskripsi, $gambar_url);
    }

    $stmt->execute();
    $stmt->close();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// --- Handle delete request ---
if (isset($_GET['delete'])) {
    $deleteId = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM produk WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();
    $stmt->close();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// --- Fetch all produk data ---
$result = $conn->query("SELECT * FROM produk ORDER BY id DESC");
$produkData = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
            color: #2c3e50;
        }

        .dashboard-container {
            display: flex;
        }

        .sidebar {
            width: 220px;
            background: #2c3e50;
            color: white;
            height: 100vh;
            padding: 20px;
            box-sizing: border-box;
            position: fixed;
            top: 0;
            left: 0;
        }

        .sidebar h2 {
            font-size: 22px;
            margin-bottom: 30px;
        }

        .sidebar a {
            display: block;
            color: #ecf0f1;
            text-decoration: none;
            padding: 10px 0;
            transition: 0.3s;
        }

        .sidebar a:hover {
            color: #1abc9c;
            padding-left: 10px;
        }

        .main-content {
            margin-left: 240px;
            padding: 20px;
            width: 100%;
        }

        .main-content h1 {
            font-size: 28px;
            margin-bottom: 5px;
        }

        .main-content p {
            margin-bottom: 20px;
            color: #666;
        }

        .card {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card h3 {
            margin: 0 0 15px;
        }

        .price-table {
            width: 100%;
            border-collapse: collapse;
            overflow: hidden;
            border-radius: 8px;
        }

        .price-table th,
        .price-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .price-table th {
            background: #1abc9c;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            user-select: none;
            position: relative;
        }

        .price-table th:hover {
            background: #16a085;
        }

        .price-table th::after {
            content: ' ⇅';
            font-size: 12px;
            opacity: 0.5;
        }

        .price-table th.sort-asc::after {
            content: ' ▲';
            opacity: 1;
        }

        .price-table th.sort-desc::after {
            content: ' ▼';
            opacity: 1;
        }

        .price-table tbody tr {
            background: #fff;
            cursor: move;
            transition: background 0.2s;
        }

        .price-table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        .price-table tbody tr:hover {
            background: #f1f7fd;
        }

        .price-table tbody tr.dragging {
            opacity: 0.5;
            background: #e8f5e9;
        }

        .price-table tbody tr.drag-over {
            border-top: 3px solid #1abc9c;
        }

        .drag-handle {
            cursor: move;
            color: #95a5a6;
            margin-right: 5px;
        }

        .actions button {
            margin: 0 3px;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .actions .edit {
            background: #3498db;
            color: #fff;
        }

        .actions .delete {
            background: #e74c3c;
            color: #fff;
        }

        .search-add {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .search-add input {
            padding: 8px;
            width: 220px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .search-add button {
            background: #1abc9c;
            color: #fff;
            padding: 8px 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            width: 350px;
        }

        .modal-content h4 {
            margin-top: 0;
            margin-bottom: 10px;
        }

        .modal-content input {
            width: 100%;
            padding: 8px;
            margin: 6px 0 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .modal-content button {
            padding: 8px 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .close {
            float: right;
            font-size: 20px;
            cursor: pointer;
        }

        .info-text {
            font-size: 13px;
            color: #7f8c8d;
            margin-bottom: 10px;
            font-style: italic;
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <a href="./">Dashboard</a>
            <a href="#">Manage Price List</a>
            <a href="?page=add-user">Users</a>
            <a href="#">Settings</a>
            <a href="./logic/auth/logout.php">Logout</a>
        </div>

        <div class="main-content">
            <h1>Admin Dashboard</h1>
            <p>Welcome to the admin dashboard. Here you can manage the price list.</p>

            <div class="card">
                <h3>Price List</h3>

                <div class="search-add">
                    <input type="text" id="searchInput" placeholder="Search item...">
                    <button onclick="openModal()">+ Add Item</button>
                </div>

                <table class="price-table" id="priceTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Item</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php foreach ($produkData as $index => $row): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($row['nama_produk']) ?></td>
                                <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                <td class="actions">
                                    <button class="edit" onclick="editItem(<?= $row['id'] ?>,'<?= addslashes($row['nama_produk']) ?>','<?= $row['harga'] ?>','<?= $row['stok'] ?>','<?= addslashes($row['deskripsi']) ?>','<?= addslashes($row['gambar_url']) ?>')">Edit</button>

                                    <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">
                                        <button class="delete">Delete</button>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal for Add/Edit -->
    <div class="modal" id="itemModal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h4 id="modalTitle">Add New Item</h4>
            <form method="POST">
                <input type="hidden" id="editId" name="editId">
                <input type="text" id="nama_produk" name="nama_produk" placeholder="Nama Produk" required>
                <input type="number" step="0.01" id="harga" name="harga" placeholder="Harga (contoh: 100000)" required>
                <input type="number" id="stok" name="stok" placeholder="Stok" required>
                <input type="text" id="deskripsi" name="deskripsi" placeholder="Deskripsi">
                <input type="text" id="gambar_url" name="gambar_url" placeholder="URL Gambar (opsional)">
                <button style="background:#1abc9c; color:#fff;" type="submit">Save</button>
            </form>

        </div>
    </div>

    <script>
        // Filter/Search
        document.getElementById("searchInput").addEventListener("keyup", function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll("#tableBody tr");
            rows.forEach((row) => {
                let item = row.cells[1].textContent.toLowerCase();
                row.style.display = item.includes(filter) ? "" : "none";
            });
        });

        function openModal() {
            document.getElementById('modalTitle').textContent = 'Add New Item';
            document.getElementById('editId').value = '';
            document.getElementById('itemInput').value = '';
            document.getElementById('priceInput').value = '';
            document.getElementById("itemModal").style.display = "flex";
        }

        function closeModal() {
            document.getElementById("itemModal").style.display = "none";
        }

        function editItem(id, nama_produk, harga, stok, deskripsi, gambar_url) {
            document.getElementById('modalTitle').textContent = 'Edit Item';
            document.getElementById('editId').value = id;
            document.getElementById('nama_produk').value = nama_produk;
            document.getElementById('harga').value = harga;
            document.getElementById('stok').value = stok;
            document.getElementById('deskripsi').value = deskripsi;
            document.getElementById('gambar_url').value = gambar_url;
            document.getElementById("itemModal").style.display = "flex";
        }


        // Modal background click to close
        window.onclick = function(e) {
            let modal = document.getElementById('itemModal');
            if (e.target === modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>

</html>