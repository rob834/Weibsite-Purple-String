<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../../login.php");
    exit();
  }

?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0" />
    <title>Cart</title>
    <link
      rel="stylesheet"
      href="../css/checkout.css" />
  </head>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap');
  </style>
  <body>
    <div id="page-container">
      <section id="header">
        <div id="PurpleBox"></div>
        <div id="leftheader">
          <div id="search">
            <label for="searchbar">
              <img src="../public/images/search.png" />
            </label>
            <input
              type="text"
              name="search"
              id="searchbar" />
          </div>
        </div>

        <div id="centerheader">
          <div id="logo">
            <img src="../public/images/Logo.png" />
          </div>
        </div>

        <?php
          if (!isset($con)) { include_once __DIR__ . '/../../backend/connection.php'; if (function_exists('get_db_connection')) { $con = get_db_connection(); } }
          $avatar_src = '../public/images/profile icon.png';
          if (isset($_SESSION['user_id']) && $con) {
            $uid = $_SESSION['user_id'];
            $uqr = mysqli_prepare($con, "SELECT avatar FROM users WHERE user_id = ? LIMIT 1");
            mysqli_stmt_bind_param($uqr, 's', $uid);
            mysqli_stmt_execute($uqr);
            $ures = mysqli_stmt_get_result($uqr);
            if ($ures && ($urow = mysqli_fetch_assoc($ures))) {
              if (!empty($urow['avatar']) && file_exists(__DIR__ . '/../public/images/avatars/' . $urow['avatar'])) {
                $avatar_src = '../public/images/avatars/' . $urow['avatar'];
              }
            }
            mysqli_stmt_close($uqr);
          }
        ?>
        <div id="rightheader">
          <div id="shoppingcart">
            <a href="cart.php"><img src="../public/images/shopping cart.png" /></a>
          </div>
          <div id="account-circle">
            <a href="profile.php"><img src="<?= $avatar_src ?>" alt="profile" /></a>
          </div>
        </div>

        <div id="menubar">
          <button><a
            href="../../../index.php"
            class="menubutton">Home</a></button>
          <button><a
            href="../pages/products.php"
            class="menubutton"
            >Products</a
          ></button>
      
        </div>

        <div id="frills">
          <img src="../public/images/vectors/frills.png" />
        </div>
      </section>

      <section id="content">
        <div class="logistics-section">
          <h1>Logistics</h1>

            <?php
              include_once __DIR__ . '/../../backend/connection.php';

                $order      = null;
                $order_items = [];

              if (isset($_SESSION['last_order_id'])) {
                $oid = intval($_SESSION['last_order_id']);

                 // JOIN on users.user_id (bigint) — use your actual column names
                 $ostmt = mysqli_prepare($con,
            "SELECT o.*,
                    u.user_name, u.display_name, u.phone, u.address, u.avatar
             FROM orders o
             JOIN users u ON u.user_id = o.user_id
             WHERE o.order_id = ?
             LIMIT 1"
        );
        mysqli_stmt_bind_param($ostmt, 'i', $oid);
        mysqli_stmt_execute($ostmt);
        $ores  = mysqli_stmt_get_result($ostmt);
        $order = mysqli_fetch_assoc($ores);
        mysqli_stmt_close($ostmt);

        // Fetch items
        $istmt = mysqli_prepare($con,
            "SELECT oi.*, p.name
             FROM order_items oi
             JOIN products p ON p.product_id = oi.product_id
             WHERE oi.order_id = ?"
        );
        mysqli_stmt_bind_param($istmt, 'i', $oid);
        mysqli_stmt_execute($istmt);
        $ires = mysqli_stmt_get_result($istmt);
        while ($irow = mysqli_fetch_assoc($ires)) {
            $order_items[] = $irow;
        }
        mysqli_stmt_close($istmt);
    }

    // display_name takes priority over user_name
    $display = $order
        ? (($order['display_name'] ?? '') ?: $order['user_name'])
        : '';
    ?>

    <?php if (!$order): ?>
      <p style="padding:2rem;">No recent order found. <a href="products.php">Continue shopping</a>.</p>
    <?php else: ?>

    <div class="invoice-card">

      <!-- LEFT: Invoice details -->
      <div class="invoice-left">
        <div class="invoice-header">
          <h2>Invoice Receipt</h2>
          <p class="email-note">Order #<?= $order['order_id'] ?> — <?= date('F j, Y', strtotime($order['created_at'])) ?></p>
        </div>

        <div class="customer-info">
          <div class="info-group">
            <label>Customer Name</label>
            <p><?= htmlspecialchars($display) ?></p>
          </div>
          <div class="info-group">
            <label>Contact Number</label>
            <p><?= htmlspecialchars($order['phone'] ?? 'Not provided (Please input in profile settings)') ?></p>
          </div>
          <div class="info-group">
            <label>Delivery Address</label>
            <p><?= htmlspecialchars($order['address'] ?? 'Not provided (Please input in profile settings)') ?></p>
          </div>
          <div class="info-group">
            <label>Items Ordered</label>
            <ul style="margin:0;padding-left:1rem;">
              <?php foreach ($order_items as $item): ?>
                <li>
                  <?= htmlspecialchars($item['name']) ?>
                  &times; <?= intval($item['quantity']) ?>
                  — ₱<?= number_format($item['unit_price'] * $item['quantity'], 2) ?>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
          <div class="info-group">
            <label>Order Total</label>
            <p><strong>₱<?= number_format($order['total'], 2) ?></strong></p>
          </div>
        </div>
      </div>

      <div class="invoice-separator"></div>

      <!-- RIGHT: Status timeline -->
      <div class="invoice-right">
        <div class="order-status-header">
          <h2>Your Order is Processed</h2>
        </div>

        <?php
        $statuses  = ['pending', 'paid', 'delivering', 'completed'];
        $cur_index = array_search($order['status'], $statuses);
        if ($cur_index === false) $cur_index = 0;
        $labels = [
            'Order Confirmed',
            'Payment Verified',
            'Out for Delivery',
            'Delivered',
        ];
        ?>

        <div class="order-timeline">
          <?php foreach ($labels as $i => $label): ?>
            <?php
              $cls = '';
              if ($i < $cur_index)      $cls = 'completed';
              elseif ($i === $cur_index) $cls = 'active';
            ?>
            <div class="timeline-item <?= $cls ?>">
              <div class="timeline-dot"></div>
              <div class="timeline-content">
                <p class="timeline-title"><?= $label ?></p>
                <p class="timeline-date">
                  <?= $i <= $cur_index
                      ? date('F j, Y', strtotime($order['created_at']))
                      : 'Pending' ?>
                </p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="tracking-history">
          <h3>Order Summary</h3>
          <?php foreach ($order_items as $item): ?>
          <div class="tracking-item">
            <span class="track-time"><?= htmlspecialchars($item['name']) ?> &times;<?= intval($item['quantity']) ?></span>
            <span class="track-status">₱<?= number_format($item['unit_price'] * $item['quantity'], 2) ?></span>
          </div>
          <?php endforeach; ?>
          <div class="tracking-item" style="font-weight:bold;">
            <span class="track-time">Total</span>
            <span class="track-status">₱<?= number_format($order['total'], 2) ?></span>
          </div>
        </div>

        <!-- Payment Instructions -->
        <div class="payment-instructions">
          <h3>💳 How to Pay</h3>
          <ol class="payment-steps">
            <li>Take a <strong>screenshot</strong> of this invoice and your order total above.</li>
            <li>Click the button below to open a <strong>private message</strong> with the shop owner on Messenger.</li>
            <li>Send the screenshot and include your <strong>Order #<?= $order['order_id'] ?></strong>.</li>
            <li>The owner will provide payment details and confirm your order once paid.</li>
          </ol>
          <a href="https://m.me/purplestring.official" target="_blank" class="messenger-btn">
            Message Us on Messenger
          </a>
          <p class="payment-note">⚠️ Your order will only be processed after payment is confirmed by the owner.</p>
        </div>

      </div>
    </div>

    <?php endif; ?>

    <!-- Continue Shopping -->
    <div class="continue-shopping">
      <h2>You Might Also Like</h2>
      <div class="products-grid">
        <?php
        $rec_sql = "SELECT p.*, pi.file_name AS image_file, pr.avg_rating, pr.count_ratings
            FROM products p
            LEFT JOIN product_images pi ON pi.product_id = p.product_id AND pi.is_primary = 1
            LEFT JOIN (
              SELECT product_id, AVG(rating) AS avg_rating, COUNT(*) AS count_ratings
              FROM product_ratings
              GROUP BY product_id
            ) pr ON pr.product_id = p.product_id
            ORDER BY RAND()
            LIMIT 4";
        $rec_res = mysqli_query($con, $rec_sql);
        if ($rec_res && mysqli_num_rows($rec_res) > 0):
          while ($rp = mysqli_fetch_assoc($rec_res)):
            $rec_img = $rp['image_file']
              ? '../public/images/products/' . $rp['image_file']
              : '../public/images/product image.png';
        ?>
        <div class="product-card">
          <a href="view/product.php?product_id=<?= $rp['product_id'] ?>">
            <img src="<?= $rec_img ?>" alt="<?= htmlspecialchars($rp['name']) ?>" class="product-img">
          </a>
          <div class="product-info">
            <p class="product-name">
              <a href="view/product.php?product_id=<?= $rp['product_id'] ?>"><?= htmlspecialchars($rp['name']) ?></a>
            </p>
            <div class="rating">
              <span class="star">⭐</span>
              <span class="rating-value"><?= isset($rp['avg_rating']) ? number_format(floatval($rp['avg_rating']), 2) : '0.00' ?></span>
              <span class="rating-count"><?= isset($rp['count_ratings']) ? intval($rp['count_ratings']) : 0 ?></span>
            </div>
            <p class="price">₱<?= number_format($rp['price'], 2) ?></p>
            <a href="products.php" class="buy-now-btn">Buy Now</a>
          </div>
        </div>
        <?php
          endwhile;
        else:
        ?>
          <p>No products available.</p>
        <?php endif; ?>
      </div>
    </div>

  </div>
</section>

      <footer id="footer">
        <div id="footer-content">
          <div id="footer-logo">
            <img
              src="../public/images/footer-logo.png"
              alt="Purple String Logo"
              width="100" />
          </div>

          <div id="footer-information">
            <div class="info-item">
              <img
                src="../public/images/mail.png"
                alt="Mail"
                class="footer-icon" />
              <span>purplestring@gmail.com</span>
            </div>

            <div class="info-item">
              <img
                src="../public/images/phonenum.png"
                alt="Phone"
                class="footer-icon" />
              <span>+63 900 123 4567</span>
            </div>
          </div>
        </div>
      </footer>

      <div id="page-design">
        <img
          id="homepage_whiteflower_1"
          src="../public/images/whiteflower.png" />
        <img
          id="homepage_bluething"
          src="../public/images/bluething.png" />
        <img
          id="homepage_heartbutton"
          src="../public/images/heartbutton.png" />
        <img
          id="homepage_greenbutton"
          src="../public/images/greenbutton.png" />
        <img
          id="homepage_greenthread"
          src="../public/images/greenthread.png" />
        <img
          id="homepage_whiteflower_2"
          src="../public/images/whiteflower.png" />
      </div>
      <script>
        // Redirect all 'View more' buttons to the products listing
        (function () {
          var buttons = document.querySelectorAll('.add-to-cart-btn');
          buttons.forEach(function (b) {
            b.addEventListener('click', function () {
              // products.php is in the same folder as checkout.php
              window.location.href = 'products.php';
            });
          });
        })();
      </script>
    </div>
  </body>
</html>