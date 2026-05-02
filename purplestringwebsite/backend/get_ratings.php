<?php
header('Content-Type: application/json');
include_once __DIR__ . '/connection.php';
session_start();

$con = get_db_connection();
if (!$con) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
if ($product_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid product_id']);
    exit;
}

$res = mysqli_prepare($con, "SELECT AVG(rating) AS avg_rating, COUNT(*) AS count_ratings FROM product_ratings WHERE product_id = ?");
mysqli_stmt_bind_param($res, 'i', $product_id);
mysqli_stmt_execute($res);
mysqli_stmt_bind_result($res, $avg, $count);
mysqli_stmt_fetch($res);
mysqli_stmt_close($res);

$user_rating = null;
if (isset($_SESSION['user_id'])) {
    $uid = intval($_SESSION['user_id']);
    $u = mysqli_prepare($con, "SELECT rating, comment FROM product_ratings WHERE product_id = ? AND user_id = ? LIMIT 1");
    mysqli_stmt_bind_param($u, 'ii', $product_id, $uid);
    mysqli_stmt_execute($u);
    mysqli_stmt_bind_result($u, $urating, $ucomment);
    if (mysqli_stmt_fetch($u)) {
        $user_rating = ['rating' => intval($urating), 'comment' => $ucomment];
    }
    mysqli_stmt_close($u);
}

echo json_encode(["avg" => $avg ? round(floatval($avg),2) : 0, "count" => intval($count), "user_rating" => $user_rating]);

?>
