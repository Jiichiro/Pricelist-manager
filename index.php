<?php 

$param = $_GET["page"] ?? "login";

switch ($param) {
    case "login":
        include "pages/login.php";
        break;
    case "admin":
        include "pages/admin.php";
        break;
    case "seller":
        include "pages/seller.php";
        break;
    default:
        echo "<h1>404 Not Found</h1>";
        break;
}
?>