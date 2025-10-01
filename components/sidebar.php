<?php
session_start();

// contoh data login (biasanya setelah login.php)
if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = "Guest";
    $_SESSION['role'] = "Guest";
}
?>

<!-- Tailwind CDN -->
<script src="https://cdn.tailwindcss.com"></script>

<style>
  @keyframes wiggle {
    0%, 100% { transform: rotate(-8deg); }
    50% { transform: rotate(8deg); }
  }
  .icon-wiggle:hover {
    animation: wiggle 0.3s ease-in-out;
  }
</style>

<!-- Sidebar -->
<aside id="sidebar" 
  class="bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 text-white w-64 min-h-screen flex flex-col justify-between fixed top-0 left-0 transform -translate-x-full md:translate-x-0 transition-all duration-300 z-50 shadow-xl">

  <!-- Bagian Atas -->
  <div>
    <div class="flex items-center justify-between p-4 border-b border-slate-700">
      <h2 id="sidebarLogo" class="text-xl font-extrabold tracking-wide bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent transition-all duration-300">
        <?php echo $_SESSION['role']; ?> Panel
      </h2>
      <!-- Tombol Close Sidebar (hanya muncul di mobile) -->
      <button onclick="toggleSidebar()" class="md:hidden text-white text-2xl focus:outline-none">✖</button>
    </div>

    <ul class="mt-6 space-y-2 px-3">
      <li>
        <a href="dashboard.php" class="flex items-center px-4 py-2 rounded-lg hover:bg-indigo-600 hover:shadow-md transition-all duration-200">
          <span class="icon-wiggle">📊</span> 
          <span class="ml-3 sidebar-text">Dashboard</span>
        </a>
      </li>
      <li>
        <a href="manage_user.php" class="flex items-center px-4 py-2 rounded-lg hover:bg-purple-600 hover:shadow-md transition-all duration-200">
          <span class="icon-wiggle">👥</span> 
          <span class="ml-3 sidebar-text">Manage User</span>
        </a>
      </li>
      <li>
        <a href="settings.php" class="flex items-center px-4 py-2 rounded-lg hover:bg-pink-600 hover:shadow-md transition-all duration-200">
          <span class="icon-wiggle">⚙️</span> 
          <span class="ml-3 sidebar-text">Setting</span>
        </a>
      </li>
    </ul>
  </div>

  <!-- Bagian Bawah -->
  <div class="border-t border-slate-700 p-4 text-center">
    <div class="flex flex-col items-center">
      <span class="font-semibold sidebar-text transition-all duration-300">
        <?php echo $_SESSION['username'] ?? 'Guest'; ?>
      </span>
      <a href="../logout.php" class="mt-2 flex items-center gap-2 px-3 py-1 text-sm rounded-lg bg-gradient-to-r from-red-500 to-pink-500 text-white hover:opacity-90 shadow-md transition-all duration-200">
        🚪 <span class="sidebar-text">Logout</span>
      </a>
    </div>
  </div>
</aside>

<!-- Tombol Toggle Mobile -->
<button id="hamburgerBtn" onclick="toggleSidebar()" class="md:hidden fixed top-4 left-4 bg-indigo-600 hover:bg-indigo-700 text-white p-2 rounded-lg shadow-lg z-50">
  ☰
</button>

<!-- Tombol Collapse Desktop -->
<button onclick="collapseSidebar()" class="hidden md:flex fixed top-4 left-4 bg-slate-800 hover:bg-slate-700 text-white p-2 rounded-lg shadow-md z-40">
  ⇔
</button>

<script>
  function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    const hamburger = document.getElementById("hamburgerBtn");

    sidebar.classList.toggle("-translate-x-full");

    // kalau sidebar terbuka → sembunyikan tombol ☰
    if (sidebar.classList.contains("-translate-x-full")) {
      hamburger.style.display = "block"; // sidebar ketutup → tombol muncul
    } else {
      hamburger.style.display = "none"; // sidebar kebuka → tombol hilang
    }
  }

  function collapseSidebar() {
    const sidebar = document.getElementById("sidebar");
    const texts = sidebar.querySelectorAll(".sidebar-text");
    const logo = document.getElementById("sidebarLogo");

    sidebar.classList.toggle("w-64");
    sidebar.classList.toggle("w-20");

    texts.forEach(el => {
      el.classList.toggle("hidden");
    });

    logo.classList.toggle("hidden");
  }
</script>
