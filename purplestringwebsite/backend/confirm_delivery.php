<?php
/**
 * confirm_delivery.php
 * Buyer clicks "Confirm I Received My Order" from their payment-confirmed email.
 * Verifies the token, sets order status to 'delivering'.
 *
 * Place at: purplestringwebsite/backend/confirm_delivery.php
 * URL:       confirm_delivery.php?order_id=7&token=<hex_token>
 */

include_once __DIR__ . "/connection.php";

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$token    = isset($_GET['token'])    ? trim($_GET['token'])      : '';

// ── Validate inputs ───────────────────────────────────────────────────────────
if ($order_id <= 0 || strlen($token) !== 64) {
    http_response_code(400);
    echo renderPage('Invalid Request', '⚠️ Invalid or missing parameters.');
    exit();
}

// ── Fetch the order ───────────────────────────────────────────────────────────
$stmt = mysqli_prepare($con,
    "SELECT order_id, status, confirm_delivery_token FROM orders WHERE order_id = ? LIMIT 1"
);
mysqli_stmt_bind_param($stmt, 'i', $order_id);
mysqli_stmt_execute($stmt);
$res   = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$order) {
    http_response_code(404);
    echo renderPage('Not Found', '❌ Order not found.');
    exit();
}

// ── Verify token ──────────────────────────────────────────────────────────────
if (empty($order['confirm_delivery_token']) || !hash_equals((string) $order['confirm_delivery_token'], $token)) {
    http_response_code(403);
    echo renderPage('Invalid Link', '🚫 This confirmation link is invalid or has already been used.');
    exit();
}

// ── Guard against wrong status ────────────────────────────────────────────────
if ($order['status'] === 'delivering') {
    echo renderPage('Already Confirmed', '📦 You have already confirmed delivery for Order #' . $order_id . '. Thank you!');
    exit();
}

if ($order['status'] === 'completed') {
    echo renderPage('Order Complete', '✅ Order #' . $order_id . ' is already marked as completed. Thank you!');
    exit();
}

if ($order['status'] !== 'paid') {
    echo renderPage('Not Ready', '⚠️ Order #' . $order_id . ' is not yet at the delivery stage (current status: ' . $order['status'] . ').');
    exit();
}

// ── Set status to 'delivering', clear the token (single-use) ─────────────────
$upd = mysqli_prepare($con,
    "UPDATE orders SET status = 'delivering', confirm_delivery_token = NULL WHERE order_id = ?"
);
mysqli_stmt_bind_param($upd, 'i', $order_id);
$ok = mysqli_stmt_execute($upd);
mysqli_stmt_close($upd);

if ($ok) {
    echo renderPage(
        'Delivery Confirmed 📦',
        "Thank you for confirming! <strong>Order #$order_id</strong> is now marked as <strong>Out for Delivery</strong>.<br><br>
         You will receive your items shortly. If you have any questions, message us on
         <a href='https://m.me/purplestring.official'>Messenger</a>."
    );
} else {
    http_response_code(500);
    echo renderPage('Error', '⚠️ Could not update your order. Please contact us on Messenger with your Order #' . $order_id . '.');
}
exit();

// ── Helper: minimal styled response page ─────────────────────────────────────
function renderPage(string $title, string $message): string {
    return "<!DOCTYPE html>
<html lang='en'>
<head>
  <meta charset='UTF-8'>
  <meta name='viewport' content='width=device-width,initial-scale=1'>
  <title>$title — Purple String</title>
  <style>
    body { font-family: Inter, sans-serif; background: #f5f5f5; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
    .card { background: #fff; border-radius: 12px; box-shadow: 0 2px 16px rgba(0,0,0,0.10); padding: 40px 48px; max-width: 480px; text-align: center; }
    h1 { color: #6b21a8; margin-top: 0; }
    p  { color: #444; line-height: 1.6; }
    a  { color: #6b21a8; }
  </style>
</head>
<body>
  <div class='card'>
    <h1>$title</h1>
    <p>$message</p>
    <p style='margin-top:28px;'><a href='/Weibsite-Purple-String/purplestringwebsite/frontend/pages/products.php'>← Continue Shopping</a></p>
  </div>
</body>
</html>";
}