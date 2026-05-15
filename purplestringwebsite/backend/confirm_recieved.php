<?php
/**
 * confirm_received.php
 * Buyer clicks "Yes, I Received My Order!" from the delivery email.
 * - Verifies the token
 * - Sets order status to 'completed'
 *
 * Place at: purplestringwebsite/backend/confirm_received.php
 */

include_once __DIR__ . '/connection.php';

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$token    = isset($_GET['token'])    ? trim($_GET['token'])      : '';

if ($order_id <= 0 || strlen($token) !== 64) {
    http_response_code(400);
    echo renderPage('Invalid Request', 'Invalid or missing parameters.');
    exit();
}

// ── Fetch order ───────────────────────────────────────────────────────────────
$stmt = mysqli_prepare($con,
    'SELECT o.order_id, o.status, o.confirm_received_token,
            u.user_name, u.display_name
     FROM orders o
     JOIN users u ON u.user_id = o.user_id
     WHERE o.order_id = ?
     LIMIT 1'
);
mysqli_stmt_bind_param($stmt, 'i', $order_id);
mysqli_stmt_execute($stmt);
$order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$order) {
    http_response_code(404);
    echo renderPage('Not Found', 'Order not found.');
    exit();
}

if (empty($order['confirm_received_token']) || !hash_equals((string) $order['confirm_received_token'], $token)) {
    http_response_code(403);
    echo renderPage('Invalid Link', 'This link is invalid or has already been used.');
    exit();
}

if ($order['status'] === 'completed') {
    echo renderPage('Already Completed', 'Order #' . $order_id . ' has already been marked as completed. Thank you!');
    exit();
}

if ($order['status'] !== 'delivering') {
    echo renderPage('Not Ready', 'Order #' . $order_id . ' is not currently out for delivery (status: ' . $order['status'] . ').');
    exit();
}

// ── Set status to 'completed', clear the token ────────────────────────────────
$upd = mysqli_prepare($con,
    "UPDATE orders SET status = 'completed', confirm_received_token = NULL WHERE order_id = ?"
);
mysqli_stmt_bind_param($upd, 'i', $order_id);
$ok = mysqli_stmt_execute($upd);
mysqli_stmt_close($upd);

$display_name = ($order['display_name'] ?? '') ?: ($order['user_name'] ?? 'Customer');

if ($ok) {
    echo renderPage(
        'Order Completed!',
        'Thank you, <strong>' . htmlspecialchars($display_name) . '</strong>!<br><br>'
        . 'Order #' . $order_id . ' is now marked as <strong>Completed</strong>.<br><br>'
        . 'We hope you love your purchase! Feel free to shop with us again.'
    );
} else {
    http_response_code(500);
    echo renderPage('Error', 'Could not complete your order. Please contact us on Messenger with Order #' . $order_id . '.');
}
exit();

function renderPage(string $title, string $message): string {
    return '<!DOCTYPE html><html lang="en">'
        . '<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
        . '<title>' . $title . ' - Purple String</title>'
        . '<style>body{font-family:Arial,sans-serif;background:#f5f5f5;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;}'
        . '.card{background:#fff;border-radius:12px;box-shadow:0 2px 16px rgba(0,0,0,0.10);padding:40px 48px;max-width:480px;text-align:center;}'
        . 'h1{color:#6b21a8;margin-top:0;}p{color:#444;line-height:1.6;}a{color:#6b21a8;}</style>'
        . '</head><body><div class="card">'
        . '<h1>' . $title . '</h1><p>' . $message . '</p>'
        . '<p style="margin-top:28px;"><a href="/Weibsite-Purple-String/purplestringwebsite/frontend/pages/products.php">Shop Again</a></p>'
        . '</div></body></html>';
}