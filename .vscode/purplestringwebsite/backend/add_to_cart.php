<?php
session_start();
include_once __DIR__ . "/connection.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$quantity = isset($_POST['quantity']) ? max(1, intval($_POST['quantity'])) : 1;
$redirect = $_POST['redirect'] ?? ($_SERVER['HTTP_REFERER'] ?? '/Weibsite-Purple-String/purplestringwebsite/frontend/pages/products.php');

if ($product_id <= 0) {
    header('Location: ' . $redirect);
    exit();
}

// Verify product exists
$pstmt = mysqli_prepare($con, "SELECT product_id, price FROM products WHERE product_id = ? LIMIT 1");
mysqli_stmt_bind_param($pstmt, 'i', $product_id);
mysqli_stmt_execute($pstmt);
$res = mysqli_stmt_get_result($pstmt);
if (!$res || mysqli_num_rows($res) === 0) {
    header('Location: ' . $redirect);
    exit();
}
mysqli_stmt_close($pstmt);

// Initialize cart in session
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add or update quantity
if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id] += $quantity;
} else {
    $_SESSION['cart'][$product_id] = $quantity;
}

// Redirect back
header('Location: ' . $redirect);
exit();
