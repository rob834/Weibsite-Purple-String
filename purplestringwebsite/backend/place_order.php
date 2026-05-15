<?php
ob_start();
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /Weibsite-Purple-String/login.php");
    exit();
}

include_once __DIR__ . '/connection.php';
include_once __DIR__ . '/mailer.php';

$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    header("Location: /Weibsite-Purple-String/purplestringwebsite/frontend/pages/cart.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ── 1. Fetch product names + prices ──────────────────────────────────────────
$ids          = array_keys($cart);
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$types        = str_repeat('i', count($ids));

$stmt = mysqli_prepare($con, "SELECT product_id, name, price FROM products WHERE product_id IN ($placeholders)");
mysqli_stmt_bind_param($stmt, $types, ...$ids);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

$prices       = [];
$products_map = [];
while ($row = mysqli_fetch_assoc($res)) {
    $prices[$row['product_id']]       = floatval($row['price']);
    $products_map[$row['product_id']] = $row;
}
mysqli_stmt_close($stmt);

// ── 2. Calculate subtotal (products only) ─────────────────────────────────────
$subtotal = 0.0;
foreach ($cart as $pid => $qty) {
    if (!isset($prices[$pid])) continue;
    $subtotal += $prices[$pid] * intval($qty);
}

$shipping = ($subtotal > 0) ? 50.00 : 0.00;
$tax      = $subtotal * 0.08;
$total    = $subtotal + $shipping + $tax;

// ── 3. Insert order ───────────────────────────────────────────────────────────
$mark_paid_token = bin2hex(random_bytes(32));

$ostmt = mysqli_prepare($con,
    "INSERT INTO orders (user_id, subtotal, shipping, tax, total, status, created_at, mark_paid_token)
     VALUES (?, ?, ?, ?, ?, 'pending', NOW(), ?)"
);
mysqli_stmt_bind_param($ostmt, 'sdddds', $user_id, $subtotal, $shipping, $tax, $total, $mark_paid_token);
mysqli_stmt_execute($ostmt);
$order_id = mysqli_insert_id($con);
mysqli_stmt_close($ostmt);

if (!$order_id) {
    // Order insert failed — go back to cart
    header("Location: /Weibsite-Purple-String/purplestringwebsite/frontend/pages/cart.php");
    exit();
}

// ── 4. Insert order items ─────────────────────────────────────────────────────
$istmt = mysqli_prepare($con,
    "INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)"
);
foreach ($cart as $pid => $qty) {
    if (!isset($prices[$pid])) continue;
    $p = intval($pid);
    $q = intval($qty);
    $u = $prices[$pid];
    mysqli_stmt_bind_param($istmt, 'iiid', $order_id, $p, $q, $u);
    mysqli_stmt_execute($istmt);
}
mysqli_stmt_close($istmt);

// ── 5. Save order ID in session, clear cart ───────────────────────────────────
$_SESSION['last_order_id'] = $order_id;
$_SESSION['cart']          = [];

// ── 6. Fetch user details ─────────────────────────────────────────────────────
$ustmt = mysqli_prepare($con,
    "SELECT user_name, display_name, email, phone, address FROM users WHERE user_id = ? LIMIT 1"
);
mysqli_stmt_bind_param($ustmt, 's', $user_id);
mysqli_stmt_execute($ustmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($ustmt)) ?? [];
mysqli_stmt_close($ustmt);

$display_name = ($user['display_name'] ?? '') ?: ($user['user_name'] ?? 'Customer');
$order_date   = date('F j, Y');

// ── 7. Build item rows for emails ─────────────────────────────────────────────
$items_html = '';
foreach ($cart as $pid => $qty) {
    if (!isset($products_map[$pid])) continue;
    $n    = htmlspecialchars($products_map[$pid]['name']);
    $u    = $prices[$pid];
    $line = $u * intval($qty);
    $items_html .=
        '<tr>'
        . '<td style="padding:6px 12px;border-bottom:1px solid #eee;">' . $n . '</td>'
        . '<td style="padding:6px 12px;border-bottom:1px solid #eee;text-align:center;">' . intval($qty) . '</td>'
        . '<td style="padding:6px 12px;border-bottom:1px solid #eee;text-align:right;">&#8369;' . number_format($u, 2) . '</td>'
        . '<td style="padding:6px 12px;border-bottom:1px solid #eee;text-align:right;">&#8369;' . number_format($line, 2) . '</td>'
        . '</tr>';
}

$receipt_table =
    '<table style="width:100%;border-collapse:collapse;font-size:14px;margin-top:16px;">'
    . '<thead><tr style="background:#f3e8ff;">'
    . '<th style="padding:8px 12px;text-align:left;color:#4a1d96;">Product</th>'
    . '<th style="padding:8px 12px;text-align:center;color:#4a1d96;">Qty</th>'
    . '<th style="padding:8px 12px;text-align:right;color:#4a1d96;">Unit Price</th>'
    . '<th style="padding:8px 12px;text-align:right;color:#4a1d96;">Total</th>'
    . '</tr></thead>'
    . '<tbody>' . $items_html . '</tbody>'
    . '</table>'
    . '<table style="width:100%;font-size:15px;margin-top:10px;">'
    . '<tr style="font-weight:700;border-top:2px solid #e9d5ff;">'
    . '<td style="padding-top:10px;">Products Total</td>'
    . '<td style="text-align:right;padding-top:10px;color:#6b21a8;">&#8369;' . number_format($subtotal, 2) . '</td>'
    . '</tr></table>';

// ── 8. Mark-as-paid URL ───────────────────────────────────────────────────────
$scheme        = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host          = $_SERVER['HTTP_HOST'];
$mark_paid_url = $scheme . '://' . $host
    . '/Weibsite-Purple-String/purplestringwebsite/backend/mark_paid.php'
    . '?order_id=' . $order_id . '&token=' . $mark_paid_token;

// ── 9. Email: buyer receipt ───────────────────────────────────────────────────
$buyer_email = $user['email'] ?? '';
if (filter_var($buyer_email, FILTER_VALIDATE_EMAIL)) {
    $subject = 'Your Purple String Order Receipt - Order #' . $order_id;
    $html =
        '<!DOCTYPE html><html><head><meta charset="UTF-8"></head>'
        . '<body style="font-family:Arial,sans-serif;background:#f5f5f5;margin:0;padding:0;">'
        . '<div style="max-width:600px;margin:32px auto;background:#fff;border-radius:12px;overflow:hidden;">'
        . '<div style="background:#6b21a8;padding:28px 32px;">'
        . '<h1 style="color:#fff;margin:0;font-size:24px;">Purple String</h1>'
        . '<p style="color:#e9d5ff;margin:6px 0 0;">Thank you for your order!</p>'
        . '</div>'
        . '<div style="padding:28px 32px;">'
        . '<p style="color:#333;">Hi <strong>' . htmlspecialchars($display_name) . '</strong>, here is your order summary:</p>'
        . '<p style="color:#888;font-size:13px;">Order #' . $order_id . ' &nbsp;&middot;&nbsp; ' . $order_date . '</p>'
        . $receipt_table
        . '<div style="background:#fffbeb;border-left:4px solid #f59e0b;padding:14px 18px;margin-top:20px;">'
        . '<p style="margin:0;color:#92400e;"><strong>Next step:</strong> Message us on '
        . '<a href="https://m.me/purplestring.official" style="color:#6b21a8;">Messenger</a>'
        . ' with a screenshot of this receipt and your Order #' . $order_id . ' to arrange payment.</p>'
        . '</div>'
        . '</div>'
        . '<div style="background:#f9f5ff;padding:16px 32px;text-align:center;color:#aaa;font-size:12px;">'
        . 'Purple String &nbsp;&middot;&nbsp; purplestring@gmail.com &nbsp;&middot;&nbsp; +63 900 123 4567'
        . '</div></div></body></html>';
    sendMail($buyer_email, $subject, $html);
}

// ── 10. Email: admin notification with mark-as-paid button ───────────────────
$admin_subject = 'New Order #' . $order_id . ' - Purple String';
$admin_html =
    '<!DOCTYPE html><html><head><meta charset="UTF-8"></head>'
    . '<body style="font-family:Arial,sans-serif;background:#f5f5f5;margin:0;padding:0;">'
    . '<div style="max-width:600px;margin:32px auto;background:#fff;border-radius:12px;overflow:hidden;">'
    . '<div style="background:#6b21a8;padding:24px 32px;">'
    . '<h1 style="color:#fff;margin:0;font-size:22px;">New Order Received!</h1>'
    . '<p style="color:#e9d5ff;margin:6px 0 0;">Order #' . $order_id . ' &nbsp;&middot;&nbsp; ' . $order_date . '</p>'
    . '</div>'
    . '<div style="padding:28px 32px;">'
    . '<h2 style="color:#4a1d96;margin-top:0;">Customer Details</h2>'
    . '<table style="width:100%;border-collapse:collapse;font-size:14px;margin-bottom:20px;">'
    . '<tr><td style="color:#888;padding:4px 0;width:110px;">Name</td><td style="font-weight:600;">' . htmlspecialchars($display_name) . '</td></tr>'
    . '<tr><td style="color:#888;padding:4px 0;">Email</td><td>' . htmlspecialchars($user['email'] ?? 'Not provided') . '</td></tr>'
    . '<tr><td style="color:#888;padding:4px 0;">Phone</td><td>' . htmlspecialchars($user['phone'] ?? 'Not provided') . '</td></tr>'
    . '<tr><td style="color:#888;padding:4px 0;">Address</td><td>' . htmlspecialchars($user['address'] ?? 'Not provided') . '</td></tr>'
    . '</table>'
    . $receipt_table
    . '<div style="margin-top:28px;text-align:center;padding:24px;background:#faf5ff;border-radius:8px;">'
    . '<p style="color:#555;margin:0 0 16px;">Once payment is confirmed from the customer, click below:</p>'
    . '<a href="' . $mark_paid_url . '" '
    . 'style="display:inline-block;background:#6b21a8;color:#fff;text-decoration:none;'
    . 'padding:14px 32px;border-radius:8px;font-weight:700;font-size:16px;">'
    . 'Mark Order #' . $order_id . ' as Paid'
    . '</a>'
    . '<p style="color:#aaa;font-size:12px;margin-top:10px;">Single-use link tied to this order.</p>'
    . '</div>'
    . '</div>'
    . '<div style="background:#f9f5ff;padding:16px 32px;text-align:center;color:#aaa;font-size:12px;">'
    . 'Purple String &nbsp;&middot;&nbsp; purplestring@gmail.com &nbsp;&middot;&nbsp; +63 900 123 4567'
    . '</div></div></body></html>';

sendMail('u.toob.poob.noob.poop@gmail.com', $admin_subject, $admin_html);

// ── 11. Redirect to checkout ──────────────────────────────────────────────────
header("Location: /Weibsite-Purple-String/purplestringwebsite/frontend/pages/checkout.php");
exit();