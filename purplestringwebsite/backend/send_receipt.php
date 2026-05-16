<?php
/**
 * send_receipt.php
 * Sends the order receipt to the logged-in user's email address.
 * Called via AJAX POST from checkout.php.
 *
 * Place this file at: purplestringwebsite/backend/send_receipt.php
 *
 * Expected POST body (JSON): { "order_id": 7 }
 * Returns JSON: { "success": true|false, "message": "..." }
 */

session_start();
include_once __DIR__ . "/connection.php";
include_once __DIR__ . "/mailer.php";

header('Content-Type: application/json');

// ── Auth guard ────────────────────────────────────────────────────────────────
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit();
}

// ── Parse JSON body ───────────────────────────────────────────────────────────
$body     = json_decode(file_get_contents('php://input'), true);
$order_id = isset($body['order_id']) ? intval($body['order_id']) : 0;

if ($order_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid order ID.']);
    exit();
}

$user_id = $_SESSION['user_id'];

// ── Fetch order — must belong to the logged-in user ───────────────────────────
$ostmt = mysqli_prepare($con,
    "SELECT o.*,
            u.user_name, u.display_name, u.email, u.phone, u.address
     FROM orders o
     JOIN users u ON u.user_id = o.user_id
     WHERE o.order_id = ? AND o.user_id = ?
     LIMIT 1"
);
mysqli_stmt_bind_param($ostmt, 'ii', $order_id, $user_id);
mysqli_stmt_execute($ostmt);
$ores  = mysqli_stmt_get_result($ostmt);
$order = mysqli_fetch_assoc($ores);
mysqli_stmt_close($ostmt);

if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Order not found.']);
    exit();
}

$recipient = $order['email'] ?? '';
if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'No valid email address on your account. Please add one in profile settings.']);
    exit();
}

// ── Fetch order items ─────────────────────────────────────────────────────────
$istmt = mysqli_prepare($con,
    "SELECT oi.*, p.name FROM order_items oi
     JOIN products p ON p.product_id = oi.product_id
     WHERE oi.order_id = ?"
);
mysqli_stmt_bind_param($istmt, 'i', $order_id);
mysqli_stmt_execute($istmt);
$ires        = mysqli_stmt_get_result($istmt);
$order_items = [];
while ($row = mysqli_fetch_assoc($ires)) {
    $order_items[] = $row;
}
mysqli_stmt_close($istmt);

// ── Build item rows ───────────────────────────────────────────────────────────
$items_html = '';
foreach ($order_items as $item) {
    $name      = htmlspecialchars($item['name']);
    $qty       = intval($item['quantity']);
    $unit      = floatval($item['unit_price']);
    $line      = $unit * $qty;
    $items_html .= "<tr>
        <td style='padding:8px 12px;border-bottom:1px solid #f0e8ff;'>{$name}</td>
        <td style='padding:8px 12px;border-bottom:1px solid #f0e8ff;text-align:center;'>{$qty}</td>
        <td style='padding:8px 12px;border-bottom:1px solid #f0e8ff;text-align:right;'>₱" . number_format($unit, 2) . "</td>
        <td style='padding:8px 12px;border-bottom:1px solid #f0e8ff;text-align:right;'>₱" . number_format($line, 2) . "</td>
    </tr>";
}

$display      = ($order['display_name'] ?? '') ?: ($order['user_name'] ?? 'Customer');
$order_date   = date('F j, Y \a\t g:i A', strtotime($order['created_at']));
$status_label = ucfirst($order['status']);

// ── Compose the HTML email ────────────────────────────────────────────────────
$subject = "Your Purple String Order Receipt — Order #$order_id";
$html    = "
<!DOCTYPE html>
<html>
<head><meta charset='UTF-8'></head>
<body style='font-family:Inter,Arial,sans-serif;background:#f5f5f5;margin:0;padding:0;'>
<div style='max-width:600px;margin:32px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.08);'>

  <!-- Header -->
  <div style='background:#6b21a8;padding:28px 32px;'>
    <h1 style='color:#fff;margin:0;font-size:24px;'>🧶 Purple String</h1>
    <p style='color:#e9d5ff;margin:6px 0 0;font-size:14px;'>Order Receipt</p>
  </div>

  <!-- Body -->
  <div style='padding:28px 32px;'>
    <p style='font-size:16px;color:#333;'>Hi <strong>" . htmlspecialchars($display) . "</strong>, thank you for your order!</p>
    <p style='color:#888;font-size:14px;margin-top:0;'>
      Order <strong>#$order_id</strong> &nbsp;·&nbsp; Placed on $order_date &nbsp;·&nbsp;
      Status: <span style='color:#6b21a8;font-weight:700;'>$status_label</span>
    </p>
    " . (!empty($order['reference_number']) ? "<p style='color:#4a1d96;font-size:13px;font-weight:600;margin-top:-8px;'>Reference #: " . htmlspecialchars($order['reference_number']) . "</p>" : "") . "

    <!-- Items table -->
    <table style='width:100%;border-collapse:collapse;font-size:14px;margin-top:16px;'>
      <thead>
        <tr style='background:#f3e8ff;'>
          <th style='padding:10px 12px;text-align:left;color:#4a1d96;'>Product</th>
          <th style='padding:10px 12px;text-align:center;color:#4a1d96;'>Qty</th>
          <th style='padding:10px 12px;text-align:right;color:#4a1d96;'>Unit</th>
          <th style='padding:10px 12px;text-align:right;color:#4a1d96;'>Total</th>
        </tr>
      </thead>
      <tbody>
        $items_html
      </tbody>
    </table>

    <!-- Totals — product total only, no tax/shipping -->
    <table style='width:100%;font-size:15px;margin-top:16px;'>
      <tr style='font-weight:700;border-top:2px solid #e9d5ff;'>
        <td style='padding-top:10px;'>Products Total</td>
        <td style='text-align:right;padding-top:10px;color:#6b21a8;'>₱" . number_format($order['subtotal'], 2) . "</td>
      </tr>
    </table>

    <!-- Delivery info -->
    <div style='background:#faf5ff;border-radius:8px;padding:16px 20px;margin-top:24px;font-size:14px;'>
      <p style='margin:0 0 6px;color:#4a1d96;font-weight:700;'>Delivery Details</p>
      <p style='margin:0;color:#555;'><strong>Address:</strong> " . htmlspecialchars($order['address'] ?? 'Not provided') . "</p>
      <p style='margin:4px 0 0;color:#555;'><strong>Phone:</strong> " . htmlspecialchars($order['phone'] ?? 'Not provided') . "</p>
    </div>

    <!-- Payment reminder -->
    <div style='background:#fffbeb;border-left:4px solid #f59e0b;padding:14px 18px;margin-top:20px;border-radius:0 8px 8px 0;font-size:14px;'>
      <p style='margin:0;color:#92400e;'>
        ⚠️ <strong>Payment Reminder:</strong> Your order is currently <em>pending payment</em>.
        Please message us on
        <a href='https://m.me/purplestring.official' style='color:#6b21a8;'>Messenger</a>
        and send a screenshot of this receipt along with your <strong>Order #$order_id</strong>.
      </p>
    </div>
  </div>

  <!-- Footer -->
  <div style='background:#f9f5ff;padding:16px 32px;text-align:center;color:#aaa;font-size:12px;'>
    Purple String &nbsp;·&nbsp; purplestring@gmail.com &nbsp;·&nbsp; +63 900 123 4567
  </div>
</div>
</body>
</html>";

// ── Send via your existing mailer ─────────────────────────────────────────────
$sent = false;
if (function_exists('sendMail')) {
    $sent = sendMail($recipient, $subject, $html);
} elseif (function_exists('sendVerificationEmail')) {
    // Temporary fallback using PHP's mail() until sendMail() is added to mailer.php
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: Purple String <purplestring@gmail.com>\r\n";
    $sent = mail($recipient, $subject, $html, $headers);
}

if ($sent) {
    echo json_encode(['success' => true, 'message' => "Receipt sent to {$recipient}!"]);
} else {
    echo json_encode(['success' => false, 'message' => 'Could not send email. Please try again later.']);
}
exit();