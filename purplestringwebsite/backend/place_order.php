<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

include_once __DIR__ . '/connection.php';

$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    header("Location: ../frontend/pages/cart.php");
    exit();
}

// user_id in your DB is a bigint stored as a large number
$user_id = $_SESSION['user_id'];

// Fetch prices from DB — never trust client side
$ids          = array_keys($cart);
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$types        = str_repeat('i', count($ids));

$stmt = mysqli_prepare($con, "SELECT product_id, price FROM products WHERE product_id IN ($placeholders)");
mysqli_stmt_bind_param($stmt, $types, ...$ids);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

$prices = [];
while ($row = mysqli_fetch_assoc($res)) {
    $prices[$row['product_id']] = floatval($row['price']);
}
mysqli_stmt_close($stmt);

// Calculate totals
$subtotal = 0.0;
foreach ($cart as $pid => $qty) {
    if (!isset($prices[$pid])) continue;
    $subtotal += $prices[$pid] * intval($qty);
}

$shipping = ($subtotal > 0) ? 50.00 : 0.00;
$tax      = $subtotal * 0.08;
$total    = $subtotal + $shipping + $tax;

// Insert into orders — user_id is bigint so use 's' bind type to safely handle large numbers
$ostmt = mysqli_prepare($con,
    "INSERT INTO orders (user_id, subtotal, shipping, tax, total, status, created_at)
     VALUES (?, ?, ?, ?, ?, 'paid', NOW())"
);
mysqli_stmt_bind_param($ostmt, 'sdddd', $user_id, $subtotal, $shipping, $tax, $total);
mysqli_stmt_execute($ostmt);
$order_id = mysqli_insert_id($con);
mysqli_stmt_close($ostmt);

// Insert order items
$istmt = mysqli_prepare($con,
    "INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)"
);
foreach ($cart as $pid => $qty) {
    if (!isset($prices[$pid])) continue;
    $pid_int = intval($pid);
    $qty_int = intval($qty);
    $price   = $prices[$pid];
    mysqli_stmt_bind_param($istmt, 'iiid', $order_id, $pid_int, $qty_int, $price);
    mysqli_stmt_execute($istmt);
}
mysqli_stmt_close($istmt);

// Save for checkout.php to display
$_SESSION['last_order_id'] = $order_id;

// Clear cart
$_SESSION['cart'] = [];

header("Location: ../frontend/pages/checkout.php");
exit();