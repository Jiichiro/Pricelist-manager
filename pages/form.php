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
if ($_SERVER["REQUEST_METHOD"] === "POST") {
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

// ✅ Ambil daftar user
$result = $conn->query("SELECT id, name, username, role FROM users");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Admin Page</title>
    <style>
        * {margin:0;padding:0;box-sizing:border-box;}
        body {font-family:'Segoe UI', Tahoma, sans-serif;background:#f4f6f9;color:#2c3e50;}
        .dashboard-container {display:flex;}
        .sidebar {width:220px;background:#2c3e50;color:#fff;height:100vh;padding:20px;position:fixed;top:0;left:0;}
        .sidebar h2 {font-size:22px;margin-bottom:30px;text-align:center;}
        .sidebar a {display:block;color:#ecf0f1;text-decoration:none;padding:10px 0;transition:.3s;}
        .sidebar a:hover {color:#1abc9c;padding-left:10px;}
        .main-content {margin-left:240px;padding:25px;width:100%;}
        .main-content h1 {margin-bottom:20px;font-size:26px;color:#34495e;}
        .container {max-width:900px;padding:20px;background:#fff;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,.08);}
        h2 {margin-bottom:15px;color:#2c3e50;}
        form {margin-bottom:30px;}
        label {font-weight:600;display:block;margin-bottom:5px;color:#555;}
        input, select {width:100%;padding:10px 12px;margin-bottom:15px;border:1px solid #ddd;border-radius:8px;font-size:14px;}
        button {padding:10px 18px;border:none;background:#1abc9c;color:#fff;font-weight:600;border-radius:8px;cursor:pointer;}
        button:hover {background:#16a085;}
        table {width:100%;border-collapse:collapse;margin-top:15px;border-radius:10px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.05);}
        th {background:#34495e;color:#fff;padding:12px;text-align:left;font-size:14px;}
        td {padding:12px;border-bottom:1px solid #eee;font-size:14px;}
        tr:nth-child(even) {background:#f9f9f9;}
        tr:hover {background:#f1f7fd;}
    </style>
</head>

<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <a href="./">Dashboard</a>
            <a href="#tambah">Tambah User</a>
            <a href="#daftar">Daftar User</a>
            <a href="./logic/auth/logout.php">Logout</a>
        </div>

        <div class="main-content">
            <h1>Halaman Admin</h1>
            <div class="container">

                <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
                <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

                <h2 id="tambah">Tambah User Baru</h2>
                <form method="POST">
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
                    </tr>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id']; ?></td>
                            <td><?= $row['name']; ?></td>
                            <td><?= $row['username']; ?></td>
                            <td><?= $row['role']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
