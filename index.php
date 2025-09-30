<?php
session_start();

require_once __DIR__ . "../logic/database/initial.php";

$sesi = $_GET['page'] ?? $_SESSION['role'] ?? 'login';

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
    case "add-user":
        include "pages/form.php";
        break;
    case "product-detail":
        include "pages/product-details.php";
        break;
    default:
        echo "<h1>404 Not Found</h1>";
        break;
}
