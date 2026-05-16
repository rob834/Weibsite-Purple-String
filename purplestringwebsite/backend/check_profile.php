<?php
/**
 * check_profile.php
 * Returns JSON { "complete": true/false } indicating whether the
 * logged-in user has both phone and address filled in.
 *
 * Place at: purplestringwebsite/backend/check_profile.php
 */

session_start();
include_once __DIR__ . '/connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['complete' => false]);
    exit();
}

$uid  = $_SESSION['user_id'];
$stmt = mysqli_prepare($con, "SELECT phone, address FROM users WHERE user_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 's', $uid);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

$complete = !empty($user['phone']) && !empty($user['address']);
echo json_encode(['complete' => $complete]);
exit();