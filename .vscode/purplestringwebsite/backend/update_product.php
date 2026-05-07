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

// CSRF
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
    exit();
}

$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
if ($product_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid product id']);
    exit();
}

$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
$stock = isset($_POST['stock']) ? intval($_POST['stock']) : 0;
$category = trim($_POST['category'] ?? '');

if ($name === '') {
    die('Name required');
}

// Resolve or create category
$category_id = null;
if ($category !== '') {
    $cstmt = mysqli_prepare($con, "SELECT category_id FROM categories WHERE name = ? LIMIT 1");
    mysqli_stmt_bind_param($cstmt, 's', $category);
    mysqli_stmt_execute($cstmt);
    mysqli_stmt_bind_result($cstmt, $found_cat_id);
    if (mysqli_stmt_fetch($cstmt)) {
        $category_id = $found_cat_id;
    }
    mysqli_stmt_close($cstmt);

    if ($category_id === null) {
        $cins = mysqli_prepare($con, "INSERT INTO categories (name, slug) VALUES (?, ?)");
        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', trim($category)));
        mysqli_stmt_bind_param($cins, 'ss', $category, $slug);
        mysqli_stmt_execute($cins);
        $category_id = mysqli_insert_id($con);
        mysqli_stmt_close($cins);
    }
}

// Update product
if ($category_id === null) {
    $ustmt = mysqli_prepare($con, "UPDATE products SET name = ?, description = ?, price = ?, stock = ?, category_id = NULL WHERE product_id = ?");
    mysqli_stmt_bind_param($ustmt, 'ssdii', $name, $description, $price, $stock, $product_id);
} else {
    $ustmt = mysqli_prepare($con, "UPDATE products SET name = ?, description = ?, price = ?, stock = ?, category_id = ? WHERE product_id = ?");
    mysqli_stmt_bind_param($ustmt, 'ssdiii', $name, $description, $price, $stock, $category_id, $product_id);
}
mysqli_stmt_execute($ustmt);
mysqli_stmt_close($ustmt);

// Handle uploaded images (optional)
$target_dir = __DIR__ . "/../frontend/public/images/products/";
if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
if (!empty($_FILES['images']) && is_array($_FILES['images']['name'])) {
    $files = $_FILES['images'];
    $count = count($files['name']);
    for ($i = 0; $i < $count; $i++) {
        if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;
        $tmp = $files['tmp_name'][$i];
        $orig = basename($files['name'][$i]);
        $ext = pathinfo($orig, PATHINFO_EXTENSION);
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if (!in_array(strtolower($ext), $allowed)) continue;
        $newName = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $dest = $target_dir . $newName;
        if (move_uploaded_file($tmp, $dest)) {
            // if first new image, mark as primary and unset previous primary
            $is_primary = ($i === 0) ? 1 : 0;
            if ($is_primary) {
                $unset = mysqli_prepare($con, "UPDATE product_images SET is_primary = 0 WHERE product_id = ?");
                mysqli_stmt_bind_param($unset, 'i', $product_id);
                mysqli_stmt_execute($unset);
                mysqli_stmt_close($unset);
            }
            $imgstmt = mysqli_prepare($con, "INSERT INTO product_images (product_id, file_name, is_primary) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($imgstmt, 'isi', $product_id, $newName, $is_primary);
            mysqli_stmt_execute($imgstmt);
            mysqli_stmt_close($imgstmt);
        }
    }
}

// Respond JSON for AJAX or redirect back
$acceptsJson = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
if ($acceptsJson) {
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit();
}

header('Location: ../frontend/pages/admin/admin-products.php');
exit();
