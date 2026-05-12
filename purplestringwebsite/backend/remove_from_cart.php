<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../frontend/pages/cart.php');
    exit();
}

// Support both bulk (product_id[]) and legacy single (product_id)
$product_ids = isset($_POST['product_id']) ? (array) $_POST['product_id'] : [];

foreach ($product_ids as $pid) {
    $pid = intval($pid);
    if ($pid > 0 && isset($_SESSION['cart'][$pid])) {
        unset($_SESSION['cart'][$pid]);
    }
}

header('Location: ../../../../../Weibsite-Purple-String/purplestringwebsite/frontend/pages/cart.php');
exit();