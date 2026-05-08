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

// Delete from order_items first (foreign key constraint)
$delete_items_query = "DELETE FROM order_items WHERE order_id IN ($placeholders)";
$stmt = mysqli_prepare($con, $delete_items_query);
if ($stmt) {
    $types = str_repeat('i', count($order_ids));
    mysqli_stmt_bind_param($stmt, $types, ...$order_ids);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// Delete from orders
$delete_orders_query = "DELETE FROM orders WHERE order_id IN ($placeholders)";
$stmt = mysqli_prepare($con, $delete_orders_query);
if ($stmt) {
    $types = str_repeat('i', count($order_ids));
    mysqli_stmt_bind_param($stmt, $types, ...$order_ids);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Notifications deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete notifications: ' . mysqli_error($con)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Query preparation error: ' . mysqli_error($con)]);
}
