<?php
session_start();
header('Content-Type: application/json');
include_once __DIR__ . '/connection.php';

$con = get_db_connection();
if (!$con) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

// require POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
$comment = isset($_POST['comment']) ? trim($_POST['comment']) : null;

if ($product_id <= 0 || $rating < 1 || $rating > 5) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

// Upsert: if user_id is provided, update their rating; otherwise insert anonymous rating
if ($user_id) {
    $sql = "SELECT rating_id FROM product_ratings WHERE product_id = ? AND user_id = ? LIMIT 1";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ii', $product_id, $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_bind_result($stmt, $existing_id);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        $u = mysqli_prepare($con, "UPDATE product_ratings SET rating = ?, comment = ?, updated_at = CURRENT_TIMESTAMP WHERE rating_id = ?");
        mysqli_stmt_bind_param($u, 'isi', $rating, $comment, $existing_id);
        mysqli_stmt_execute($u);
        mysqli_stmt_close($u);
    } else {
        mysqli_stmt_close($stmt);
        $i = mysqli_prepare($con, "INSERT INTO product_ratings (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($i, 'iiis', $product_id, $user_id, $rating, $comment);
        mysqli_stmt_execute($i);
        mysqli_stmt_close($i);
    }
} else {
    $i = mysqli_prepare($con, "INSERT INTO product_ratings (product_id, user_id, rating, comment) VALUES (?, NULL, ?, ?)");
    mysqli_stmt_bind_param($i, 'iis', $product_id, $rating, $comment);
    mysqli_stmt_execute($i);
    mysqli_stmt_close($i);
}

// return updated aggregate
$q = mysqli_prepare($con, "SELECT AVG(rating) AS avg_rating, COUNT(*) AS count_ratings FROM product_ratings WHERE product_id = ?");
mysqli_stmt_bind_param($q, 'i', $product_id);
mysqli_stmt_execute($q);
mysqli_stmt_bind_result($q, $avg, $count);
mysqli_stmt_fetch($q);
mysqli_stmt_close($q);

echo json_encode(["avg" => $avg ? round(floatval($avg),2) : 0, "count" => intval($count)]);

?>
