<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ../../frontend/pages/cart.php'); exit(); }
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
if ($product_id > 0 && isset($_SESSION['cart'][$product_id])) {
    unset($_SESSION['cart'][$product_id]);
}
header('Location: ../../../../../Weibsite-Purple-String/purplestringwebsite/frontend/pages/cart.php');
exit();
