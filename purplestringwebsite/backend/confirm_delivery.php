<?php
/**
 * confirm_delivery.php
 * Buyer clicks "Confirm & Start Delivery" from the payment-confirmed email.
 * - Verifies the token
 * - Sets order status to 'delivering'
 * - Generates a confirm_received_token
 * - Emails the buyer asking if their order has arrived
 *
 * Place at: purplestringwebsite/backend/confirm_delivery.php
 */

include_once __DIR__ . '/connection.php';
include_once __DIR__ . '/mailer.php';

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$token    = isset($_GET['token'])    ? trim($_GET['token'])      : '';

if ($order_id <= 0 || strlen($token) !== 64) {
    http_response_code(400);
    echo renderPage('Invalid Request', 'Invalid or missing parameters.');
    exit();
}

// ── Fetch order + user ────────────────────────────────────────────────────────
$stmt = mysqli_prepare($con,
    'SELECT o.order_id, o.status, o.confirm_delivery_token,
            u.user_name, u.display_name, u.email
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

if (empty($order['confirm_delivery_token']) || !hash_equals((string) $order['confirm_delivery_token'], $token)) {
    http_response_code(403);
    echo renderPage('Invalid Link', 'This link is invalid or has already been used.');
    exit();
}

if ($order['status'] === 'delivering') {
    echo renderPage('Already Confirmed', 'Order #' . $order_id . ' is already out for delivery. Check your email for the next step!');
    exit();
}

if ($order['status'] === 'completed') {
    echo renderPage('Order Complete', 'Order #' . $order_id . ' has already been completed. Thank you!');
    exit();
}

if ($order['status'] !== 'paid') {
    echo renderPage('Not Ready', 'Order #' . $order_id . ' is not yet at the delivery stage (status: ' . $order['status'] . ').');
    exit();
}

// ── Generate confirm-received token ──────────────────────────────────────────
$confirm_received_token = bin2hex(random_bytes(32));

// ── Update: delivering, clear confirm_delivery_token, store confirm_received_token
$upd = mysqli_prepare($con,
    "UPDATE orders SET status = 'delivering', confirm_delivery_token = NULL, confirm_received_token = ? WHERE order_id = ?"
);
mysqli_stmt_bind_param($upd, 'si', $confirm_received_token, $order_id);
$ok = mysqli_stmt_execute($upd);
mysqli_stmt_close($upd);

if (!$ok) {
    http_response_code(500);
    echo renderPage('Error', 'Could not update your order. Please contact us on Messenger with Order #' . $order_id . '.');
    exit();
}

// ── Fetch order items for email ───────────────────────────────────────────────
$istmt = mysqli_prepare($con,
    'SELECT oi.quantity, oi.unit_price, p.name
     FROM order_items oi
     JOIN products p ON p.product_id = oi.product_id
     WHERE oi.order_id = ?'
);
mysqli_stmt_bind_param($istmt, 'i', $order_id);
mysqli_stmt_execute($istmt);
$ires      = mysqli_stmt_get_result($istmt);
$items_html = '';
$subtotal   = 0.0;
while ($row = mysqli_fetch_assoc($ires)) {
    $iline     = floatval($row['unit_price']) * intval($row['quantity']);
    $subtotal += $iline;
    $items_html .= '<tr>'
        . '<td style="padding:6px 12px;border-bottom:1px solid #eee;">' . htmlspecialchars($row['name']) . '</td>'
        . '<td style="padding:6px 12px;border-bottom:1px solid #eee;text-align:center;">' . intval($row['quantity']) . '</td>'
        . '<td style="padding:6px 12px;border-bottom:1px solid #eee;text-align:right;">&#8369;' . number_format($row['unit_price'], 2) . '</td>'
        . '<td style="padding:6px 12px;border-bottom:1px solid #eee;text-align:right;">&#8369;' . number_format($iline, 2) . '</td>'
        . '</tr>';
}
mysqli_stmt_close($istmt);

$receipt_table =
    '<table style="width:100%;border-collapse:collapse;font-size:14px;">'
    . '<thead><tr style="background:#f3e8ff;">'
    . '<th style="padding:8px 12px;text-align:left;color:#4a1d96;">Product</th>'
    . '<th style="padding:8px 12px;text-align:center;color:#4a1d96;">Qty</th>'
    . '<th style="padding:8px 12px;text-align:right;color:#4a1d96;">Unit Price</th>'
    . '<th style="padding:8px 12px;text-align:right;color:#4a1d96;">Total</th>'
    . '</tr></thead>'
    . '<tbody>' . $items_html . '</tbody>'
    . '</table>'
    . '<table style="width:100%;font-size:15px;margin-top:12px;">'
    . '<tr style="font-weight:700;border-top:2px solid #e9d5ff;">'
    . '<td style="padding-top:10px;">Products Total</td>'
    . '<td style="text-align:right;padding-top:10px;color:#6b21a8;">&#8369;' . number_format($subtotal, 2) . '</td>'
    . '</tr></table>';

// ── Build confirm-received URL ────────────────────────────────────────────────
$scheme               = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$confirm_received_url = $scheme . '://' . $_SERVER['HTTP_HOST']
    . '/Weibsite-Purple-String/purplestringwebsite/backend/confirm_received.php'
    . '?order_id=' . $order_id . '&token=' . $confirm_received_token;

$display_name = ($order['display_name'] ?? '') ?: ($order['user_name'] ?? 'Customer');

// ── Email buyer: has your order arrived? ──────────────────────────────────────
$buyer_email = $order['email'] ?? '';
if (filter_var($buyer_email, FILTER_VALIDATE_EMAIL)) {
    $buyer_subject = 'Has Your Purple String Order #' . $order_id . ' Arrived?';
    $buyer_html =
        '<!DOCTYPE html><html><head><meta charset="UTF-8"></head>'
        . '<body style="font-family:Arial,sans-serif;background:#f5f5f5;margin:0;padding:0;">'
        . '<div style="max-width:600px;margin:32px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.08);">'
        . '<div style="background:#6b21a8;padding:28px 32px;">'
        . '<h1 style="color:#fff;margin:0;font-size:24px;">Purple String</h1>'
        . '<p style="color:#e9d5ff;margin:6px 0 0;font-size:14px;">Your order is out for delivery!</p>'
        . '</div>'
        . '<div style="padding:28px 32px;">'
        . '<p style="font-size:16px;color:#333;margin-top:0;">Hi <strong>' . htmlspecialchars($display_name) . '</strong>!</p>'
        . '<p style="color:#555;">Your <strong>Order #' . $order_id . '</strong> is currently <strong>out for delivery</strong>. We hope it reaches you soon!</p>'
        . $receipt_table
        . '<div style="margin-top:32px;text-align:center;background:#faf5ff;border-radius:10px;padding:28px;">'
        . '<p style="color:#4a1d96;font-weight:700;font-size:16px;margin:0 0 8px;">Has your order arrived?</p>'
        . '<p style="color:#555;font-size:14px;margin:0 0 24px;">If you have received all your items, please confirm below so we can complete your order.</p>'
        . '<a href="' . $confirm_received_url . '" style="display:inline-block;background:#16a34a;color:#fff;text-decoration:none;padding:14px 32px;border-radius:8px;font-weight:700;font-size:16px;">'
        . 'Yes, I Received My Order!'
        . '</a>'
        . '<p style="color:#aaa;font-size:12px;margin-top:12px;">Only click once all items have been received. If there is an issue, message us on '
        . '<a href="https://m.me/purplestring.official" style="color:#6b21a8;">Messenger</a>.</p>'
        . '</div>'
        . '</div>'
        . '<div style="background:#f9f5ff;padding:16px 32px;text-align:center;color:#aaa;font-size:12px;">'
        . 'Purple String &nbsp;&middot;&nbsp; purplestring@gmail.com &nbsp;&middot;&nbsp; +63 900 123 4567'
        . '</div></div></body></html>';
    sendMail($buyer_email, $buyer_subject, $buyer_html);
}

echo renderPage(
    'Delivery Started!',
    'Thank you for confirming, <strong>' . htmlspecialchars($display_name) . '</strong>!<br><br>'
    . 'Order #' . $order_id . ' is now <strong>Out for Delivery</strong>.<br><br>'
    . 'We have sent you another email to confirm once your items arrive.'
);
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
        . '<p style="margin-top:28px;"><a href="/Weibsite-Purple-String/purplestringwebsite/frontend/pages/products.php">Back to Shop</a></p>'
        . '</div></body></html>';
}