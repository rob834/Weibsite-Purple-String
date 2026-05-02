<?php
session_start();

include_once __DIR__ . '/connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit();
}

$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
if ($product_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid product id']);
    exit();
}

// CSRF check
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
    exit();
}

// Fetch images to delete files
$imgRes = mysqli_prepare($con, "SELECT file_name FROM product_images WHERE product_id = ?");
mysqli_stmt_bind_param($imgRes, 'i', $product_id);
mysqli_stmt_execute($imgRes);
mysqli_stmt_bind_result($imgRes, $file_name);
$files = [];
while (mysqli_stmt_fetch($imgRes)) {
    $files[] = $file_name;
}
mysqli_stmt_close($imgRes);

$target_dir = __DIR__ . "/../frontend/public/images/products/";
foreach ($files as $f) {
    if (!$f) continue;
    $path = $target_dir . $f;
    if (file_exists($path)) {
        @unlink($path);
    }
}

// Delete image records
$delImgs = mysqli_prepare($con, "DELETE FROM product_images WHERE product_id = ?");
mysqli_stmt_bind_param($delImgs, 'i', $product_id);
mysqli_stmt_execute($delImgs);
mysqli_stmt_close($delImgs);

// Delete product
$delProd = mysqli_prepare($con, "DELETE FROM products WHERE product_id = ?");
mysqli_stmt_bind_param($delProd, 'i', $product_id);
mysqli_stmt_execute($delProd);
mysqli_stmt_close($delProd);

// If request expects JSON (AJAX), return JSON; otherwise redirect
$acceptsJson = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
if ($acceptsJson) {
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit();
}

header('Location: ../frontend/pages/admin/admin-products.php');
exit();
