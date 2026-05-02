<?php
session_start();
include_once __DIR__ . "/connection.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ../../frontend/pages/cart.php'); exit(); }
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;

if ($product_id <= 0) { header('Location: ../../frontend/pages/cart.php'); exit(); }

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if ($quantity <= 0) {
    // remove
    unset($_SESSION['cart'][$product_id]);
} else {
    $_SESSION['cart'][$product_id] = $quantity;
}

header('Location: ../../frontend/pages/cart.php');
exit();
