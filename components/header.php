<?php
// Mulai session di awal file
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fallback default jika session kosong
if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = "Guest";
    $_SESSION['name'] = "Guest";
}
?>

<header>
  <div class="header-container">
    <!-- Logo -->
    <div class="logo">
      <h1>📊 Pricelist Manager</h1>
    </div>

    <!-- Hamburger (Mobile Only) -->
    <div class="hamburger" onclick="document.querySelector('.nav-links').classList.toggle('active')">
      ☰
    </div>

    <!-- Navigation Menu -->
    <nav>
      <ul class="nav-links">
        <li><a href="?page=dashboard">Dashboard</a></li>
        <li><a href="?page=pricelist">Pricelist</a></li>
        <li><a href="?page=katalog">Katalog</a></li>
        <li><a href="?page=laporan">Laporan</a></li>
        <li><a href="?page=bantuan">Bantuan</a></li>
      </ul>
    </nav>

    <!-- User Profile with Dropdown -->
    <div class="user-profile">
      <img src="https://via.placeholder.com/35" alt="User" />
      <span class="username"><?php echo htmlspecialchars($_SESSION['name']); ?> ▾</span>

      <!-- Dropdown Menu -->
      <ul class="dropdown-menu">
        <?php if ($_SESSION['username'] !== "Guest") { ?>
          <li><a href="#">Profil</a></li>
          <li><a href="#">Pengaturan</a></li>
          <li><a href="./logic/auth/logout.php">Logout</a></li>
        <?php } else { ?>
          <li><a href="login.php">Login</a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
</header>

<style>
  header {
    background: #0f172a;
    color: #f8fafc;
    padding: 15px 30px;
    font-family: 'Poppins', sans-serif;
    position: sticky;
    top: 0;
    z-index: 1000;
  }

  .header-container {
    max-width: 1280px;
    margin: auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .logo h1 {
    font-size: 20px;
    color: #38bdf8;
    font-weight: 700;
  }

  nav ul {
    list-style: none;
    display: flex;
    gap: 25px;
    margin: 0;
    padding: 0;
  }

  nav ul li a {
    color: #f8fafc;
    text-decoration: none;
    font-size: 14px;
    transition: 0.3s;
  }

  nav ul li a:hover {
    color: #38bdf8;
  }

  .user-profile {
    position: relative;
    display: flex;
    align-items: center;
    gap: 10px;
    background: #1e293b;
    padding: 6px 12px;
    border-radius: 20px;
    cursor: pointer;
  }

  .user-profile img {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    border: 2px solid #38bdf8;
  }

  .username {
    font-size: 14px;
    font-weight: 500;
  }

  /* Dropdown Menu */
  .dropdown-menu {
    position: absolute;
    top: 55px;
    right: 0;
    background: #1e293b;
    list-style: none;
    padding: 10px 0;
    margin: 0;
    border-radius: 8px;
    display: none;
    flex-direction: column;
    width: 150px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  }

  .dropdown-menu li {
    padding: 10px 15px;
  }

  .dropdown-menu li a {
    color: #f8fafc;
    text-decoration: none;
    font-size: 14px;
    display: block;
  }

  .dropdown-menu li a:hover {
    background: #38bdf8;
    color: #0f172a;
    border-radius: 5px;
  }

  /* Show dropdown on hover */
  .user-profile:hover .dropdown-menu {
    display: flex;
  }

  /* Hamburger (mobile only) */
  .hamburger {
    display: none;
    font-size: 22px;
    cursor: pointer;
    color: #f8fafc;
  }

  /* 🔹 Breakpoints */
  /* Laptop (≤1164px) */
  @media (max-width: 1164px) {
    .header-container {
      max-width: 1164px;
      gap: 15px;
    }
    nav ul {
      gap: 18px;
    }
  }

  /* Tablet (≤768px) */
  @media (max-width: 768px) {
    .header-container {
      max-width: 768px;
    }
    nav ul {
      gap: 12px;
    }
    nav ul li a {
      font-size: 13px;
    }
    .username {
      font-size: 13px;
    }
  }

  /* Mobile (≤480px) */
  @media (max-width: 480px) {
    .header-container {
      max-width: 480px;
    }

    nav {
      position: absolute;
      top: 60px;
      left: 0;
      width: 100%;
    }

    .nav-links {
      flex-direction: column;
      background: #1e293b;
      padding: 15px;
      display: none;
    }

    .nav-links.active {
      display: flex;
    }

    .hamburger {
      display: block;
    }

    nav ul li {
      margin: 10px 0;
    }

    .user-profile {
      padding: 5px 10px;
    }
  }
</style>
