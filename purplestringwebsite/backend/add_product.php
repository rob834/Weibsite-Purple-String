<?php
session_start();

include_once __DIR__ . "/connection.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid request');
}

$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$price = $_POST['price'] ?? '';
$stock = $_POST['stock'] ?? 0;
$category = trim($_POST['category'] ?? '');
$created_by = $_SESSION['user_id'];

if ($name === '' || $price === '') {
    die('Name and price are required');
}

// Resolve or create category (optional)
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

// Insert product
$pstmt = mysqli_prepare($con, "INSERT INTO products (name, description, price, stock, category_id, created_by) VALUES (?, ?, ?, ?, ?, ?)");
$price = floatval($price);
$stock = intval($stock);
$cat_param = $category_id === null ? null : $category_id;
mysqli_stmt_bind_param($pstmt, 'ssdiii', $name, $description, $price, $stock, $cat_param, $created_by);
// Workaround for binding NULL integers: set as 0 when null and allow NULL in SQL by using prepared statement string change
mysqli_stmt_close($pstmt);

// Simpler insertion to handle NULL properly
if ($category_id === null) {
    $query = "INSERT INTO products (name, description, price, stock, created_by) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 'ssdii', $name, $description, $price, $stock, $created_by);
} else {
    $query = "INSERT INTO products (name, description, price, stock, category_id, created_by) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 'ssdiii', $name, $description, $price, $stock, $category_id, $created_by);
}

if (!mysqli_stmt_execute($stmt)) {
    die('Failed to insert product');
}

$product_id = mysqli_insert_id($con);
mysqli_stmt_close($stmt);

// Handle images
$target_dir = __DIR__ . "/../frontend/public/images/products/";
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0755, true);
}

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
            $is_primary = ($i === 0) ? 1 : 0;
            $imgstmt = mysqli_prepare($con, "INSERT INTO product_images (product_id, file_name, is_primary) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($imgstmt, 'isi', $product_id, $newName, $is_primary);
            mysqli_stmt_execute($imgstmt);
            mysqli_stmt_close($imgstmt);
        }
    }
}

// Success - redirect back to admin products list
header('Location: ../frontend/pages/admin/admin-products.php');
exit();
