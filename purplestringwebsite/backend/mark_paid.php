<?php
/**
 * mark_paid.php
 * Admin clicks "Mark as Paid" from their notification email.
 * - Verifies the one-time token
 * - Sets order status to 'paid'
 * - Emails the buyer that payment is confirmed with a "Confirm Delivery" button
 *
 * Place at: purplestringwebsite/backend/mark_paid.php
 * URL:       mark_paid.php?order_id=7&token=<64_char_hex>
 */

include_once __DIR__ . '/connection.php';
include_once __DIR__ . '/mailer.php';

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$token    = isset($_GET['token'])    ? trim($_GET['token'])      : '';

// ── Validate inputs ───────────────────────────────────────────────────────────
if ($order_id <= 0 || strlen($token) !== 64) {
    http_response_code(400);
    echo renderPage('Invalid Request', 'Invalid or missing parameters.');
    exit();
}

// ── Fetch order + user details ────────────────────────────────────────────────
$stmt = mysqli_prepare($con,
    'SELECT o.order_id, o.status, o.mark_paid_token, o.subtotal,
            u.user_name, u.display_name, u.email, u.phone, u.address
     FROM orders o
     JOIN users u ON u.user_id = o.user_id
     WHERE o.order_id = ?
     LIMIT 1'
);
mysqli_stmt_bind_param($stmt, 'i', $order_id);
mysqli_stmt_execute($stmt);
$res   = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$order) {
    http_response_code(404);
    echo renderPage('Not Found', 'Order not found.');
    exit();
}

// ── Verify token (constant-time) ──────────────────────────────────────────────
if (empty($order['mark_paid_token']) || !hash_equals((string) $order['mark_paid_token'], $token)) {
    http_response_code(403);
    echo renderPage('Invalid Link', 'This link is invalid or has already been used.');
    exit();
}

// ── Guard: already processed ──────────────────────────────────────────────────
if (in_array($order['status'], ['paid', 'delivering', 'completed'])) {
    echo renderPage('Already Processed', 'Order #' . $order_id . ' has already been processed (status: ' . $order['status'] . ').');
    exit();
}

// ── Generate confirm-delivery token for buyer ─────────────────────────────────
$confirm_token = bin2hex(random_bytes(32));

// ── Update: status = paid, clear mark_paid_token, save confirm_delivery_token ─
$upd = mysqli_prepare($con,
    "UPDATE orders SET status = 'paid', mark_paid_token = NULL, confirm_delivery_token = ?, is_read = 0 WHERE order_id = ?"
);
mysqli_stmt_bind_param($upd, 'si', $confirm_token, $order_id);
$ok = mysqli_stmt_execute($upd);
mysqli_stmt_close($upd);

if (!$ok) {
    http_response_code(500);
    echo renderPage('Error', 'Could not update the order. Please update it manually in the admin panel.');
    exit();
}

// ── Fetch order items for buyer email ─────────────────────────────────────────
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

// ── Confirm-delivery URL ──────────────────────────────────────────────────────
$base_url    = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
               . '://' . $_SERVER['HTTP_HOST']
               . '/Weibsite-Purple-String/purplestringwebsite/backend/confirm_delivery.php';
$confirm_url = $base_url . '?order_id=' . $order_id . '&token=' . $confirm_token;

$display_name = ($order['display_name'] ?? '') ?: ($order['user_name'] ?? 'Customer');

// ── Email buyer: payment confirmed + confirm delivery button ──────────────────
$buyer_email = $order['email'] ?? '';
if (filter_var($buyer_email, FILTER_VALIDATE_EMAIL)) {
    $buyer_subject = 'Payment Confirmed - Your Purple String Order #' . $order_id . ' is Ready!';
    $buyer_html =
        '<!DOCTYPE html><html><head><meta charset="UTF-8"></head>'
        . '<body style="font-family:Arial,sans-serif;background:#f5f5f5;margin:0;padding:0;">'
        . '<div style="max-width:600px;margin:32px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.08);">'
        . '<div style="background:#6b21a8;padding:28px 32px;">'
        . '<h1 style="color:#fff;margin:0;font-size:24px;">Purple String</h1>'
        . '<p style="color:#e9d5ff;margin:6px 0 0;font-size:14px;">Your payment has been confirmed!</p>'
        . '</div>'
        . '<div style="padding:28px 32px;">'
        . '<p style="font-size:16px;color:#333;margin-top:0;">Hi <strong>' . htmlspecialchars($display_name) . '</strong>!</p>'
        . '<p style="color:#555;">Your payment for <strong>Order #' . $order_id . '</strong> has been verified. Your order is now being prepared!</p>'
        . $receipt_table
        . '<div style="margin-top:32px;text-align:center;background:#f0fdf4;border-radius:10px;padding:24px;">'
        . '<p style="color:#166534;font-weight:600;margin:0 0 8px;">Once you receive your order, please confirm delivery:</p>'
        . '<p style="color:#555;font-size:13px;margin:0 0 20px;">This lets us know your items arrived safely.</p>'
        . '<a href="' . $confirm_url . '" style="display:inline-block;background:#16a34a;color:#fff;text-decoration:none;padding:14px 32px;border-radius:8px;font-weight:700;font-size:16px;">'
        . 'Confirm I Received My Order'
        . '</a>'
        . '<p style="color:#aaa;font-size:12px;margin-top:12px;">Only click once you have received your items.</p>'
        . '</div>'
        . '</div>'
        . '<div style="background:#f9f5ff;padding:16px 32px;text-align:center;color:#aaa;font-size:12px;">'
        . 'Purple String &nbsp;&middot;&nbsp; purplestring@gmail.com &nbsp;&middot;&nbsp; +63 900 123 4567'
        . '</div></div></body></html>';
    sendMail($buyer_email, $buyer_subject, $buyer_html);
}

echo renderPage(
    'Payment Confirmed',
    'Order #' . $order_id . ' has been marked as Paid.<br><br>'
    . 'An email has been sent to the customer with a delivery confirmation button.'
);
exit();

// ── Minimal styled response page ──────────────────────────────────────────────
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