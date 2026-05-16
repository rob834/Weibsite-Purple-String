<?php
session_start();

// Verify session authenticity credentials 
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../../login.php");
    exit();
}

include_once __DIR__ . '/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $oid = intval($_POST['order_id']);
    
    // CRITICAL SECURITY: Initialize database isolation transaction sequence
    mysqli_begin_transaction($con);
    
    try {
        // 1. Fetch order status using FOR UPDATE to prevent race conditions
        $status_stmt = mysqli_prepare($con, "SELECT status FROM orders WHERE order_id = ? LIMIT 1 FOR UPDATE");
        mysqli_stmt_bind_param($status_stmt, 'i', $oid);
        mysqli_stmt_execute($status_stmt);
        $status_res = mysqli_stmt_get_result($status_stmt);
        $order_row = mysqli_fetch_assoc($status_res);
        mysqli_stmt_close($status_stmt);
        
        // Only run restoration logic if order exists and isn't already cancelled
        if ($order_row && $order_row['status'] !== 'cancelled') {
            
            // 2. Look up all line items and their corresponding quantities purchased
            $items_stmt = mysqli_prepare($con, "SELECT product_id, quantity FROM order_items WHERE order_id = ?");
            mysqli_stmt_bind_param($items_stmt, 'i', $oid);
            mysqli_stmt_execute($items_stmt);
            $items_res = mysqli_stmt_get_result($items_stmt);
            
            // 3. Prepare the inventory modification update loop sequence
            $restore_stmt = mysqli_prepare($con, "UPDATE products SET stock = stock + ? WHERE product_id = ?");
            
            while ($item = mysqli_fetch_assoc($items_res)) {
                $product_id = intval($item['product_id']);
                $quantity   = intval($item['quantity']);
                
                // Return stock numbers back to where they originally came from
                mysqli_stmt_bind_param($restore_stmt, 'ii', $quantity, $product_id);
                mysqli_stmt_execute($restore_stmt);
            }
            mysqli_stmt_close($items_stmt);
            mysqli_stmt_close($restore_stmt);
            
            // 4. Update the order flag state configuration down to 'cancelled' status
            $update_order_stmt = mysqli_prepare($con, "UPDATE orders SET status = 'cancelled' WHERE order_id = ?");
            mysqli_stmt_bind_param($update_order_stmt, 'i', $oid);
            mysqli_stmt_execute($update_order_stmt);
            mysqli_stmt_close($update_order_stmt);
            
            // Apply all matching modifications systematically
            mysqli_commit($con);
        } else {
            // Drop connection state calmly if item is already cancelled
            mysqli_rollback($con);
        }
        
    } catch (Exception $e) {
        // Revert any alterations if an exception is thrown
        mysqli_rollback($con);
    }
}

// Redirect back seamlessly to the admin dashboard panel screen view
$redirect_back = $_SERVER['HTTP_REFERER'] ?? '../frontend/pages/admin/admin-customers.php';
header("Location: " . $redirect_back);
exit();