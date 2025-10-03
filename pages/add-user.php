<?php
require __DIR__ . "/../logic/database/connect.php";

// ✅ Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: ./index.php");
    exit;
}

// ✅ Cek role admin
if ($_SESSION['role'] !== 'super_admin') {
    echo "⚠️ Akses ditolak. Hanya admin yang bisa membuka halaman ini.";
    exit;
}

// ✅ Proses tambah user
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (name, username, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $username, $password, $role);

    if ($stmt->execute()) {
        $success = "✅ User berhasil ditambahkan.";
    } else {
        $error = "❌ Gagal menambah user: " . $conn->error;
    }
    $stmt->close();
}

// ✅ Proses edit user
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $username = $_POST['username'];
    $role = $_POST['role'];

    // Jika password diisi, update juga
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE users SET name=?, username=?, password=?, role=? WHERE id=?");
        $stmt->bind_param("ssssi", $name, $username, $password, $role, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET name=?, username=?, role=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $username, $role, $id);
    }

    if ($stmt->execute()) {
        $success = "✅ User berhasil diupdate.";
    } else {
        $error = "❌ Gagal update user: " . $conn->error;
    }
    $stmt->close();
}

// ✅ Proses hapus user (sekarang via POST, bukan GET)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success = "✅ User berhasil dihapus.";
    } else {
        $error = "❌ Gagal hapus user: " . $conn->error;
    }
    $stmt->close();
}

// ✅ Ambil daftar user
$result = $conn->query("SELECT id, name, username, role FROM users");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Page</title>
 <style>
/* ================= Global Styles ================= */
* {margin:0; padding:0; box-sizing:border-box;}
body {font-family:'Segoe UI', Tahoma, sans-serif; background:#f4f6f9; color:#2c3e50;}
a {text-decoration:none;}

/* ================= Dashboard Layout ================= */
.dashboard-container {display:flex;}
.main-content {margin-left:280px; padding:25px; width:100%; transition: margin 0.3s;}
/* Tambahkan jarak bawah header / hamburger menu */
.main-content h1 {
    margin-bottom: 20px;
    font-size: 28px;
    color: #34495e;
    margin-top: 70px; /* ini yang bikin turun, sesuaikan tinggi hamburger */
}

.main-content h1 {margin-bottom:20px; font-size:28px; color:#34495e;}
.container {max-width:950px; padding:25px; background:#fff; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,.08);}
h2 {margin-bottom:20px; color:#2c3e50;}
form {margin-bottom:30px;}
label {font-weight:600; display:block; margin-bottom:5px; color:#555;}
input, select {width:100%; padding:12px 14px; margin-bottom:18px; border:1px solid #ddd; border-radius:10px; font-size:15px;}
button {padding:12px 20px; border:none; background:#1abc9c; color:#fff; font-weight:600; border-radius:8px; cursor:pointer; font-size:15px;}
button:hover {background:#16a085;}
table {width:100%; border-collapse:collapse; margin-top:20px; border-radius:12px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.05);}
th {background:#34495e; color:#fff; padding:14px; text-align:left; font-size:15px;}
td {padding:14px; border-bottom:1px solid #eee; font-size:14px;}
tr:nth-child(even) {background:#f9f9f9;}
tr:hover {background:#f1f7fd;}
.btn {padding:7px 14px; border:none; border-radius:6px; color:#fff; cursor:pointer; text-decoration:none; font-size:13px;}
.btn-edit {background:#3498db;}
.btn-edit:hover {background:#2980b9;}
.btn-delete {background:#e74c3c;}
.btn-delete:hover {background:#c0392b;}

/* Modal */
.modal {display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; overflow:auto; background:rgba(0,0,0,.5);}
.modal-content {background:#fff; margin:8% auto; padding:25px; border-radius:12px; width:90%; max-width:450px;}
.close {color:#aaa; float:right; font-size:22px; font-weight:bold; cursor:pointer;}

/* ================= Responsive ================= */

/* Mobile: 480px */
@media screen and (max-width:480px) {
    .main-content {margin-left:0; padding:15px;}
    h1 {font-size:24px;}
    label, input, select, button {font-size:14px;}
    table th, table td {font-size:12px; padding:10px;}
}

/* Tablet: 768px */
@media screen and (max-width:768px) {
    .main-content {margin-left:0; padding:20px;}
    h1 {font-size:26px;}
    input, select, button {padding:10px 12px; font-size:14px;}
    table th, table td {font-size:13px; padding:12px;}
}

/* Laptop: 1164px */
@media screen and (max-width:1164px) {
    .main-content {margin-left:0; padding:22px;}
    h1 {font-size:27px;}
    input, select, button {padding:11px 13px; font-size:15px;}
}

/* Desktop: >1164px tetap default */
</style>
</head>
<body>
<div class="dashboard-container">
    <?php include __DIR__ . '../../components/sidebar.php'; ?>
    <div class="main-content">
        <h1>Halaman Admin</h1>
        <div class="container">

            <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
            <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

            <h2 id="tambah">Tambah User Baru</h2>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <label>Nama:</label>
                <input type="text" name="name" required>

                <label>Username (untuk login):</label>
                <input type="text" name="username" required>

                <label>Password:</label>
                <input type="password" name="password" required>

                <label>Role:</label>
                <select name="role" required>
                    <option value="penjualan">Sales</option>
                    <option value="super_admin">Admin</option>
                </select>

                <button type="submit">Tambah User</button>
            </form>

            <h2 id="daftar">Daftar User</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= $row['name']; ?></td>
                        <td><?= $row['username']; ?></td>
                        <td><?= $row['role']; ?></td>
                        <td>
                            <button class="btn btn-edit" onclick="openEditModal(<?= $row['id']; ?>, '<?= $row['name']; ?>', '<?= $row['username']; ?>', '<?= $row['role']; ?>')">Edit</button>
                            
                            <!-- ✅ Delete pakai POST -->
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus user ini?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                <button type="submit" class="btn btn-delete">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div id="editModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeEditModal()">&times;</span>
    <h2>Edit User</h2>
    <form method="POST">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" id="editId">
        
        <label>Nama:</label>
        <input type="text" name="name" id="editName" required>
        
        <label>Username:</label>
        <input type="text" name="username" id="editUsername" required>
        
        <label>Password (kosongkan jika tidak diubah):</label>
        <input type="password" name="password">
        
        <label>Role:</label>
        <select name="role" id="editRole" required>
            <option value="penjualan">Sales</option>
            <option value="super_admin">Admin</option>
        </select>
        
        <button type="submit">Update User</button>
    </form>
  </div>
</div>

<script>
function openEditModal(id, name, username, role) {
    document.getElementById("editId").value = id;
    document.getElementById("editName").value = name;
    document.getElementById("editUsername").value = username;
    document.getElementById("editRole").value = role;
    document.getElementById("editModal").style.display = "block";
}
function closeEditModal() {
    document.getElementById("editModal").style.display = "none";
}
window.onclick = function(event) {
    let modal = document.getElementById("editModal");
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>
</body>
</html>
