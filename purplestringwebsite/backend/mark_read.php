<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

include_once __DIR__ . '/connection.php';

header('Content-Type: application/json');

// Get the request body
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['order_ids']) || !is_array($input['order_ids']) || empty($input['order_ids'])) {
    echo json_encode(['success' => false, 'message' => 'No order IDs provided']);
    exit();
}

$order_ids = $input['order_ids'];

// Sanitize order IDs (ensure they're integers)
$order_ids = array_map('intval', $order_ids);

// Create placeholders for the IN clause
$placeholders = implode(',', array_fill(0, count($order_ids), '?'));

// First, check and add is_read column if it doesn't exist
$check_column = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='orders' AND COLUMN_NAME='is_read'";
$column_result = mysqli_query($con, $check_column);

if (!$column_result || mysqli_num_rows($column_result) === 0) {
    // Column doesn't exist, add it
    $alter_query = "ALTER TABLE orders ADD COLUMN is_read TINYINT(1) DEFAULT 0";
    mysqli_query($con, $alter_query);
}

// Update orders to mark them as read
$update_query = "UPDATE orders SET is_read = 1 WHERE order_id IN ($placeholders)";
$stmt = mysqli_prepare($con, $update_query);

if ($stmt) {
    $types = str_repeat('i', count($order_ids));
    mysqli_stmt_bind_param($stmt, $types, ...$order_ids);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Notifications marked as read']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($con)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Query preparation error: ' . mysqli_error($con)]);
}
