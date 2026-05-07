<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../../login.php");
    exit();
}
include_once __DIR__ . '/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $oid  = intval($_POST['order_id']);
    $stmt = mysqli_prepare($con, "DELETE FROM orders WHERE order_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $oid);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit();