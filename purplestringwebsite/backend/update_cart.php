<?php
session_start();
include_once __DIR__ . "/connection.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../frontend/pages/cart.php');
    exit();
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Support both bulk (product_id[] / quantity[]) and legacy single (product_id / quantity)
$product_ids = isset($_POST['product_id']) ? (array) $_POST['product_id'] : [];
$quantities  = isset($_POST['quantity'])   ? (array) $_POST['quantity']   : [];

foreach ($product_ids as $index => $pid) {
    $pid = intval($pid);
    $qty = isset($quantities[$index]) ? intval($quantities[$index]) : 0;

    if ($pid <= 0) continue;

    if ($qty <= 0) {
        unset($_SESSION['cart'][$pid]);
    } else {
        $_SESSION['cart'][$pid] = $qty;
    }
}

header('Location: ../../../../../Weibsite-Purple-String/purplestringwebsite/frontend/pages/cart.php');
exit();