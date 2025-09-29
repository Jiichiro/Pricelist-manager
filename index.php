<?php 
session_start();

$sesi = $_SESSION['role'] ?? 'login';

switch ($sesi) {
    case "login":
        include "pages/login.php";
        break;
    case "super_admin":
        include "pages/admin.php";
        break;
    case "penjualan":
        include "pages/seller.php";
        break;
    default:
        echo "<h1>404 Not Found</h1>";
        break;
}
?>