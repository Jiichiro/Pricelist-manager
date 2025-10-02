<?php
session_start();
require __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/logic/database/connect.php';

$routes = [
    'login'          => ['file' => 'pages/login.php',           'role' => null],
    'super_admin'    => ['file' => 'pages/admin.php',           'role' => 'super_admin'],
    'penjualan'      => ['file' => 'pages/seller.php',          'role' => 'penjualan'],
    'add-user'       => ['file' => 'pages/add-user.php',        'role' => 'super_admin'],
    'product-detail' => ['file' => 'pages/product-details.php', 'role' => 'penjualan'], 
    'admin-setting'  => ['file' => 'pages/admin-settings.php',  'role' => 'super_admin'], 
];

$page = $_GET['page'] ?? ($_SESSION['role'] ?? 'login');

if (array_key_exists($page, $routes)) {
    $route = $routes[$page];

    if ($route['role'] === null || ($_SESSION['role'] ?? null) === $route['role']) {
        include $route['file'];
    } else {
        echo "<h1>403 Forbidden</h1>";
    }
} else {
    echo "<h1>404 Not Found</h1>";
}
