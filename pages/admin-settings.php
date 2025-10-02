<?php
require_once __DIR__ . '/../logic/database/connect.php';

// Pastikan user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

$id = $_SESSION['user_id'];

// Ambil data user dari database
$stmt = $conn->prepare("SELECT id, name, password FROM users WHERE id = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$userData = $stmt->get_result()->fetch_assoc();

if (!$userData) {
    die("User tidak ditemukan.");
}

// Update nama
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $newName = trim($_POST['name']);
    if ($newName !== "") {
        $stmt = $conn->prepare("UPDATE users SET name=? WHERE username=?");
        $stmt->bind_param("ss", $newName, $username);
        if ($stmt->execute()) {
            $_SESSION['name'] = $newName;
            $success = "Nama berhasil diperbarui.";
        } else {
            $error = "Gagal memperbarui nama.";
        }
    }
}

// Ganti password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $oldPass = $_POST['old_password'];
    $newPass = $_POST['new_password'];
    $confirmPass = $_POST['confirm_password'];

    // Verifikasi password lama
    if (!password_verify($oldPass, $userData['password'])) {
        $error = "Password lama salah.";
    } elseif ($newPass !== $confirmPass) {
        $error = "Password baru dan konfirmasi tidak sama.";
    } else {
        $hashedNewPass = password_hash($newPass, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE users SET password=? WHERE username=?");
        $stmt->bind_param("ss", $hashedNewPass, $username);

        if ($stmt->execute()) {
            $success = "Password berhasil diganti.";
        } else {
            $error = "Gagal mengganti password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pengaturan Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center font-sans">
  <div class="w-full max-w-xl bg-white shadow-md rounded-xl p-8">
    <h1 class="text-2xl font-bold text-gray-700 mb-6 text-center">Pengaturan Admin</h1>

    <!-- Notifikasi -->
    <?php if (isset($success)): ?>
      <div class="mb-4 p-3 rounded bg-green-50 text-green-700 border border-green-200 text-sm">
        <?= htmlspecialchars($success) ?>
      </div>
    <?php elseif (isset($error)): ?>
      <div class="mb-4 p-3 rounded bg-red-50 text-red-700 border border-red-200 text-sm">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <!-- Edit Nama & Username -->
    <form method="POST" class="space-y-4 mb-8">
      <input type="hidden" name="update_profile" value="1">

      <div class="relative">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
          <!-- Ikon User -->
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12c2.28 0 4-1.72 4-4s-1.72-4-4-4-4 1.72-4 4 1.72 4 4 4zM6 20c0-2.67 2.67-4 6-4s6 1.33 6 4" />
          </svg>
        </span>
        <input type="text" name="name" value="<?= htmlspecialchars($userData['name']) ?>"
               class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"
               placeholder="Nama Lengkap">
      </div>

      <div class="relative">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
          <!-- Ikon Username -->
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7H8m8 4H8m-2 4h12" />
          </svg>
        </span>
        <input type="text" value="<?= $_SESSION["username"]?>" disabled
               class="w-full pl-10 pr-4 py-2 border rounded-lg bg-gray-100 text-gray-500 cursor-not-allowed"
               placeholder="Username">
      </div>

      <button type="submit"
              class="w-full py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition">
        Simpan Perubahan
      </button>
    </form>

    <hr class="my-6">

    <!-- Ganti Password -->
    <form method="POST" class="space-y-4">
      <input type="hidden" name="change_password" value="1">

      <div class="relative">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
          <!-- Ikon Lock -->
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2V9a6 6 0 10-12 0v2a2 2 0 00-2 2v6a2 2 0 002 2z" />
          </svg>
        </span>
        <input type="password" name="old_password" required
               class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"
               placeholder="Password Lama">
      </div>

      <div class="relative">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
          <!-- Ikon Key -->
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 11-4 0 2 2 0 014 0zM7 21a4 4 0 118-0M7 21v-4m8 4v-4m-4 0v-4" />
          </svg>
        </span>
        <input type="password" name="new_password" required
               class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"
               placeholder="Password Baru">
      </div>

      <div class="relative">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
          <!-- Ikon Check -->
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </span>
        <input type="password" name="confirm_password" required
               class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"
               placeholder="Konfirmasi Password Baru">
      </div>

      <button type="submit"
              class="w-full py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition">
        Ganti Password
      </button>
    </form>
  </div>
</body>
</html>
