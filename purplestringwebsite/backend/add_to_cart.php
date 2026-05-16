<?php
session_start();
include_once __DIR__ . "/connection.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    exit(json_encode(['success' => false, 'error' => 'Method Not Allowed']));
}

$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$quantity = isset($_POST['quantity']) ? max(1, intval($_POST['quantity'])) : 1;
$redirect = $_POST['redirect'] ?? ($_SERVER['HTTP_REFERER'] ?? '/Weibsite-Purple-String/purplestringwebsite/frontend/pages/products.php');

// Detect if this is an AJAX request
$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($product_id <= 0) {
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Invalid product ID']);
    } else {
        header('Location: ' . $redirect);
    }
    exit();
}

// CRITICAL SECURITY: Added `stock` to verification query
$pstmt = mysqli_prepare($con, "SELECT product_id, price, stock FROM products WHERE product_id = ? LIMIT 1");
if (!$pstmt) {
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Database error']);
    } else {
        header('Location: ' . $redirect);
    }
    exit();
}

mysqli_stmt_bind_param($pstmt, 'i', $product_id);
mysqli_stmt_execute($pstmt);
$res = mysqli_stmt_get_result($pstmt);
if (!$res || mysqli_num_rows($res) === 0) {
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Product not found']);
    } else {
        header('Location: ' . $redirect);
    }
    exit();
}

$product_data = mysqli_fetch_assoc($res);
$available_stock = intval($product_data['stock']);
mysqli_stmt_close($pstmt);

// Initialize cart in session
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// CRITICAL SECURITY: Check if the total combined quantity exceeds stock limits
$current_cart_qty = isset($_SESSION['cart'][$product_id]) ? intval($_SESSION['cart'][$product_id]) : 0;
if (($current_cart_qty + $quantity) > $available_stock) {
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false, 
            'error' => "Cannot add more. Only {$available_stock} item(s) are available in stock, and you already have {$current_cart_qty} in your cart."
        ]);
    } else {
        $_SESSION['error_message'] = "Insufficient stock available.";
        header('Location: ' . $redirect);
    }
    exit();
}

// Add or update quantity safely
if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id] += $quantity;
} else {
    $_SESSION['cart'][$product_id] = $quantity;
}

// Return appropriate response based on request type
if ($is_ajax) {
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Product added to cart']);
} else {
    header('Location: ' . $redirect);
}
exit();