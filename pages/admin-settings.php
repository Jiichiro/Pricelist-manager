<?php
require __DIR__ . '/../logic/database/connect.php';

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
        $stmt = $conn->prepare("UPDATE users SET name=? WHERE id=?");
        $stmt->bind_param("ss", $newName, $id);
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

        $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
        $stmt->bind_param("ss", $hashedNewPass, $id);

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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pengaturan Admin</title>
  <style>
/* General */
body {
  background: #f3f4f6;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: sans-serif;
  margin: 0;
}

.container {
  width: 100%;
  max-width: 600px;
  background: #fff;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  border-radius: 12px;
  padding: 2rem;
  margin: auto 20px; /* kasih jarak kanan kiri */
}

h1 {
  font-size: 1.5rem;
  font-weight: bold;
  text-align: center;
  color: #374151;
  margin-bottom: 1.5rem;
}

hr {
  margin: 2rem 0;
  border: none;
  border-top: 1px solid #e5e7eb;
}

/* Alerts */
.alert {
  padding: 0.75rem 1rem;
  border-radius: 8px;
  font-size: 0.9rem;
  margin-bottom: 1rem;
  border: 1px solid transparent;
}
.alert.success {
  background: #ecfdf5;
  color: #047857;
  border-color: #a7f3d0;
}
.alert.error {
  background: #fef2f2;
  color: #b91c1c;
  border-color: #fecaca;
}

/* Forms */
.form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.input-group {
  position: relative;
}

.input-group .icon {
  position: absolute;
  top: 50%;
  left: 0.75rem;
  transform: translateY(-50%);
  color: #9ca3af;
  pointer-events: none;
}

.icon-svg {
  width: 20px;
  height: 20px;
}

input {
  width: 100%; /* jangan fix 91% */
  padding: 0.5rem 0.75rem 0.5rem 2.5rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  outline: none;
  font-size: 0.95rem;
  transition: border 0.2s, box-shadow 0.2s;
  box-sizing: border-box; /* penting biar padding masuk hitungan */
}

input:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 2px #bfdbfe;
}

input.disabled {
  background: #f3f4f6;
  color: #9ca3af;
  cursor: not-allowed;
}

/* Buttons */
.btn {
  width: 100%;
  padding: 0.6rem;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  color: white;
  cursor: pointer;
  transition: background 0.2s;
}
.btn-blue {
  background: #2563eb;
}
.btn-blue:hover {
  background: #1d4ed8;
}
.btn-green {
  background: #16a34a;
}
.btn-green:hover {
  background: #15803d;
}

/* 🔽 RESPONSIF */
@media (max-width: 640px) {
  .container {
    padding: 1rem;      /* padding lebih kecil di HP */
    margin: 0 10px;     /* jarak kanan kiri biar nggak kejepit */
    border-radius: 8px; /* bisa juga 0 kalau mau full edge */
  }

  h1 {
    font-size: 1.2rem;  /* biar teks ga kegedean di HP */
  }

  input, .btn {
    font-size: 0.9rem;  /* lebih enak dibaca */
    /* padding: 0.5rem;    lebih compact */
  }
}

  </style>
</head>
<body>
  <div class="container">
    <?php require_once __DIR__ . '/../components/sidebar.php'; ?>
    <h1>Pengaturan Admin</h1>

    <!-- Notifikasi -->
    <?php if (isset($success)): ?>
      <div class="alert success">
        <?= htmlspecialchars($success) ?>
      </div>
    <?php elseif (isset($error)): ?>
      <div class="alert error">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <!-- Edit Nama & Username -->
    <form method="POST" class="form">
      <input type="hidden" name="update_profile" value="1">

      <div class="input-group">
        <span class="icon">
          <!-- Ikon User -->
          <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 12c2.28 0 4-1.72 4-4s-1.72-4-4-4-4 1.72-4 4 1.72 4 4 4zM6 20c0-2.67 2.67-4 6-4s6 1.33 6 4" />
          </svg>
        </span>
        <input type="text" name="name" value="<?= htmlspecialchars($_SESSION['name']) ?>" placeholder="Nama Lengkap">
      </div>

      <div class="input-group">
        <span class="icon">
          <!-- Ikon Username -->
          <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M16 7H8m8 4H8m-2 4h12" />
          </svg>
        </span>
        <input type="text" value="<?= $_SESSION["username"]?>" disabled placeholder="Username" class="disabled">
      </div>

      <button type="submit" class="btn btn-blue">Simpan Perubahan</button>
    </form>

    <hr>

    <!-- Ganti Password -->
    <form method="POST" class="form">
      <input type="hidden" name="change_password" value="1">

      <div class="input-group">
        <span class="icon">
          <!-- Ikon Lock -->
          <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2V9a6 6 0 10-12 0v2a2 2 0 00-2 2v6a2 2 0 002 2z" />
          </svg>
        </span>
        <input type="password" name="old_password" required placeholder="Password Lama">
      </div>

      <div class="input-group">
        <span class="icon">
          <!-- Ikon Key -->
          <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 7a2 2 0 11-4 0 2 2 0 014 0zM7 21a4 4 0 118-0M7 21v-4m8 4v-4m-4 0v-4" />
          </svg>
        </span>
        <input type="password" name="new_password" required placeholder="Password Baru">
      </div>

      <div class="input-group">
        <span class="icon">
          <!-- Ikon Check -->
          <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M5 13l4 4L19 7" />
          </svg>
        </span>
        <input type="password" name="confirm_password" required placeholder="Konfirmasi Password Baru">
      </div>

      <button type="submit" class="btn btn-green">Ganti Password</button>
    </form>
  </div>
</body>
</html>

