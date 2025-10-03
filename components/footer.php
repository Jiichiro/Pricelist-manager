<footer>
  <div class="footer-container">
    <!-- Tentang Sistem -->
    <div class="footer-about">
      <h3>Tentang Aplikasi</h3>
      <p>Website internal untuk manajemen pricelist dan katalog produk 
         dengan role-based access (Super Admin & Penjualan). 
         Dibuat untuk mempermudah pengelolaan data harga, produk, dan distribusi informasi.</p>
      <div class="social-icons">
        <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
        <a href="#"><i class="fa-brands fa-instagram"></i></a>
        <a href="#"><i class="fa-brands fa-linkedin"></i></a>
        <a href="#"><i class="fa-brands fa-x-twitter"></i></a>
      </div>
    </div>

    <!-- Fitur Utama -->
    <div class="footer-menu">
      <h3>Fitur Utama</h3>
      <ul>
        <li><a href="#">Manajemen Pricelist</a></li>
        <li><a href="#">Manajemen Katalog</a></li>
        <li><a href="#">Hak Akses Super Admin</a></li>
        <li><a href="#">Hak Akses Penjualan</a></li>
      </ul>
    </div>

    <!-- Informasi -->
    <div class="footer-menu">
      <h3>Informasi</h3>
      <ul>
        <li><a href="#">Tentang Sistem</a></li>
        <li><a href="#">Dokumentasi</a></li>
        <li><a href="#">FAQ</a></li>
        <li><a href="#">Panduan Pengguna</a></li>
      </ul>
    </div>

    <!-- Kontak -->
    <div class="footer-menu">
      <h3>Kontak</h3>
      <ul>
        <li>📍 Jl. Teknologi No. 45, Jakarta</li>
        <li>📞 +62 812-3456-7890</li>
        <li>✉️ support@pricelistmanager.com</li>
        <li><a href="#">Hubungi Admin</a></li>
      </ul>
    </div>
  </div>

  <hr>

  <div class="footer-bottom">
    <p>&copy; 2025 Pricelist Manager. Semua Hak Dilindungi.</p>
    <p>Dikembangkan untuk efisiensi manajemen produk & harga</p>
  </div>
</footer>

<!-- Font Awesome -->
<script src="https://kit.fontawesome.com/yourkitid.js" crossorigin="anonymous"></script>

<style>
  footer {
    background: #0f172a;
    color: #f8fafc;
    padding: 50px 20px;
    font-family: 'Poppins', sans-serif;
  }

  .footer-container {
    max-width: 1200px;
    margin: auto;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 40px;
  }

  .footer-about p {
    font-size: 14px;
    margin: 15px 0;
    color: #cbd5e1;
  }

  .social-icons a {
    margin-right: 12px;
    color: #f8fafc;
    font-size: 18px;
    transition: 0.3s;
  }

  .social-icons a:hover {
    color: #38bdf8;
    transform: scale(1.2);
  }

  .footer-menu h3 {
    margin-bottom: 15px;
    font-size: 16px;
    color: #ffffff;
  }

  .footer-menu ul {
    list-style: none;
    padding: 0;
  }

  .footer-menu ul li {
    margin: 8px 0;
    font-size: 14px;
    color: #cbd5e1;
  }

  .footer-menu ul li a {
    color: #cbd5e1;
    text-decoration: none;
    transition: 0.3s;
  }

  .footer-menu ul li a:hover {
    color: #38bdf8;
    padding-left: 5px;
  }

  hr {
    border: none;
    border-top: 1px solid #334155;
    margin: 30px 0;
  }

  .footer-bottom {
    text-align: center;
    font-size: 14px;
    color: #94a3b8;
  }

  .footer-bottom p {
    margin: 5px 0;
  }
</style>
