<header>
  <div class="header-container">
    <!-- Logo -->
    <div class="logo">
      <h1>📊 Pricelist Manager</h1>
    </div>

    <!-- Navigation Menu -->
    <nav>
      <ul class="nav-links">
        <li><a href="#">Dashboard</a></li>
        <li><a href="#">Pricelist</a></li>
        <li><a href="#">Katalog</a></li>
        <li><a href="#">Laporan</a></li>
        <li><a href="#">Bantuan</a></li>
      </ul>
    </nav>

    <?php if ($_SESSION['username']) { ?>
      <div class="user-profile">
        <img src="https://via.placeholder.com/35" alt="User" />
        <span class="username"><?php echo $_SESSION['username'] ?> ▾</span>

        <!-- Dropdown Menu -->
        <ul class="dropdown-menu">
          <li><a href="#">Profil</a></li>
          <li><a href="#">Pengaturan</a></li>
          <li><a href="./logic/auth/logout.php">Logout</a></li>
        </ul>
      </div>
    <?php } else { ?>
      <p> login </p>
    <?php } ?>
    <!-- User Profile with Dropdown -->

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
    max-width: 1200px;
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
</style>