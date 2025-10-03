<?php
// contoh data login (biasanya setelah login.php)
if (!isset($_SESSION['username'])) {
  $_SESSION['username'] = "Guest";
  $_SESSION['role'] = "Guest";
}
?>

<style>
  /* ===== Sidebar Default (Mobile First) ===== */
  #sidebar {
    background: linear-gradient(to bottom, #0f172a, #1e293b, #0f172a);
    color: white;
    width: 14rem;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: fixed;
    top: 0;
    left: 0;
    transform: translateX(-100%);
    transition: all 0.3s ease;
    z-index: 50;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
  }

  #sidebar.collapsed {
    width: 5rem;
  }

  #sidebar.hidden-mobile {
    transform: translateX(-100%);
  }

  /* === Sidebar Top Section === */
  .top-section .header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    border-bottom: 1px solid #334155;
  }

  #sidebarLogo {
    font-size: 1.25rem;
    font-weight: 800;
    background: linear-gradient(to right, #818cf8, #c084fc);
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    transition: all 0.3s ease;
  }

  /* === Sidebar Menu === */
  .menu {
    margin-top: 1.5rem;
    padding: 0 0.75rem;
    list-style: none;
  }

  .menu li {
    margin-bottom: 0.5rem;
  }

  .menu-item {
    display: flex;
    align-items: center;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    text-decoration: none;
    color: white;
    transition: all 0.2s ease-in-out;
  }

  .menu-item:hover {
    background-color: #6366f1;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }

  /* === Bottom Section === */
  .bottom-section {
    border-top: 1px solid #334155;
    padding: 1rem;
    text-align: center;
  }

  .user-info {
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .logout-btn {
    margin-top: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.25rem 0.75rem;
    font-size: 0.875rem;
    border-radius: 0.5rem;
    background: linear-gradient(to right, #ef4444, #ec4899);
    color: white;
    text-decoration: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: opacity 0.2s ease-in-out;
  }

  .logout-btn:hover {
    opacity: 0.9;
  }

  /* === Text & Logo Visibility === */
  .sidebar-text {
    margin-left: 0.75rem;
    transition: all 0.3s ease;
  }

  .hidden {
    display: none !important;
  }

  /* === Buttons === */
  .hamburger-btn {
    position: fixed;
    top: 1rem;
    left: 1rem;
    background-color: #6366f1;
    color: white;
    padding: 0.5rem;
    border-radius: 0.5rem;
    z-index: 50;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
    display: block;
  }

  .collapse-btn {
    display: none;
  }

  /* === Main Content Default === */
  .main-content {
    margin-left: 0;
    padding: 1rem;
    transition: margin-left 0.3s ease;
  }

  /* ===== Breakpoints ===== */

  /* Tablet (>=768px) */
  @media (min-width: 768px) {
    #sidebar {
      width: 15rem;
      transform: translateX(0);
    }

    .hamburger-btn {
      display: none;
    }

    .collapse-btn {
      display: flex;
      position: fixed;
      top: 1rem;
      left: 1rem;
      background-color: #1e293b;
      color: white;
      padding: 0.5rem;
      border-radius: 0.5rem;
      z-index: 40;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    }

    .main-content {
      margin-left: 15rem;
    }

    #sidebar.collapsed+.collapse-btn {
      left: 5rem;
    }

    #sidebar.collapsed~.main-content {
      margin-left: 5rem;
    }
  }

  /* Laptop (>=1164px) */
  @media (min-width: 1164px) {
    #sidebar {
      width: 16rem;
    }

    .main-content {
      margin-left: 16rem;
    }

    #sidebar.collapsed~.main-content {
      margin-left: 5rem;
    }
  }

  /* Desktop (>=1280px) */
  @media (min-width: 1280px) {
    #sidebar {
      width: 18rem;
    }

    .main-content {
      margin-left: 18rem;
    }

    #sidebar.collapsed~.main-content {
      margin-left: 5rem;
    }
  }

  @keyframes wiggle {
    0%,100% { transform: rotate(-8deg); }
    50% { transform: rotate(8deg); }
  }

  .icon-wiggle:hover {
    animation: wiggle 0.3s ease-in-out;
  }
</style>

<!-- Sidebar -->
<aside id="sidebar">
  <div class="top-section">
    <div class="header">
      <h2 id="sidebarLogo">
        <?php echo $_SESSION['role']; ?> Panel
      </h2>
      <button onclick="toggleSidebar()" class="close-btn">✖</button>
    </div>

    <ul class="menu">
      <li>
        <a href="./" class="menu-item">
          <span class="icon-wiggle">📊</span>
          <span class="sidebar-text">Dashboard</span>
        </a>
      </li>
      <li>
        <a href="?page=add-user" class="menu-item">
          <span class="icon-wiggle">👥</span>
          <span class="sidebar-text">Manage User</span>
        </a>
      </li>
      <li>
        <a href="?page=admin-setting" class="menu-item">
          <span class="icon-wiggle">⚙️</span>
          <span class="sidebar-text">Setting</span>
        </a>
      </li>
    </ul>
  </div>

  <div class="bottom-section">
    <div class="user-info">
      <span class="sidebar-text">
        <?php echo $_SESSION['name'] ?? 'Guest'; ?>
      </span>
      <a href="./logic/auth/logout.php" class="logout-btn">
        🚪 <span class="sidebar-text">Logout</span>
      </a>
    </div>
  </div>
</aside>

<!-- Mobile Toggle Button -->
<button id="hamburgerBtn" onclick="toggleSidebar()" class="hamburger-btn">☰</button>

<!-- Desktop Collapse Button -->
<button onclick="collapseSidebar()" class="collapse-btn">⇔</button>


<script>
  function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    const hamburger = document.getElementById("hamburgerBtn");
    sidebar.classList.toggle("hidden-mobile");
    hamburger.style.display = sidebar.classList.contains("hidden-mobile") ? "block" : "none";
  }

  function collapseSidebar() {
    const sidebar = document.getElementById("sidebar");
    const texts = sidebar.querySelectorAll(".sidebar-text");
    const logo = document.getElementById("sidebarLogo");

    sidebar.classList.toggle("collapsed");
    texts.forEach(el => el.classList.toggle("hidden"));
    logo.classList.toggle("hidden");
  }
</script>
