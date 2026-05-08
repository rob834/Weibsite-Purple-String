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
    <title>Profile</title>
    <link rel="stylesheet" href="../css/profilePurchases.css" />
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
          if (!isset($con)) { include_once __DIR__ . '/../../backend/connection.php'; $con = function_exists('get_db_connection') ? get_db_connection() : null; }
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
            <a href="../pages/cart.php"><img src="../public/images/shopping cart.png" /></a>
          </div>
          <div id="account-circle">
            <a href="../pages/profile.php"><img src="<?= $avatar_src ?>" alt="profile" /></a>
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
<!--content-->
      <section id="content">
        <div class="profile-grid">
          <div class="left-cards">
            <div
              class="profile-card"
              id="card-1">

              <?php
                include_once __DIR__ . '/../../backend/connection.php';

                $uid = $_SESSION['user_id'];

                $sort_by = $_GET['sort'] ?? 'recent';
                $order_sql_sort = 'o.created_at DESC';
                if ($sort_by === 'oldest')   $order_sql_sort = 'o.created_at ASC';
                if ($sort_by === 'expensive') $order_sql_sort = 'o.total DESC';
                if ($sort_by === 'cheapest')  $order_sql_sort = 'o.total ASC';

                $ostmt = mysqli_prepare($con,
                  "SELECT o.order_id, o.status, o.total, o.created_at
                   FROM orders o
                   WHERE o.user_id = ?
                   ORDER BY $order_sql_sort"
                );
                mysqli_stmt_bind_param($ostmt, 'i', $uid);
                mysqli_stmt_execute($ostmt);
                $ores = mysqli_stmt_get_result($ostmt);
                $orders = [];
                while ($orow = mysqli_fetch_assoc($ores)) $orders[] = $orow;
                mysqli_stmt_close($ostmt);

                // Fetch items per order
                $order_items_map = [];
                if (!empty($orders)) {
                  $oids = array_column($orders, 'order_id');
                  $placeholders = implode(',', array_fill(0, count($oids), '?'));
                  $types = str_repeat('i', count($oids));
                  $istmt = mysqli_prepare($con,
                    "SELECT oi.order_id, oi.quantity, oi.unit_price, p.name,
                            pi.file_name AS image_file
                     FROM order_items oi
                     JOIN products p ON p.product_id = oi.product_id
                     LEFT JOIN product_images pi ON pi.product_id = oi.product_id AND pi.is_primary = 1
                     WHERE oi.order_id IN ($placeholders)"
                  );
                  mysqli_stmt_bind_param($istmt, $types, ...$oids);
                  mysqli_stmt_execute($istmt);
                  $ires = mysqli_stmt_get_result($istmt);
                  while ($irow = mysqli_fetch_assoc($ires)) {
                    $order_items_map[$irow['order_id']][] = $irow;
                  }
                  mysqli_stmt_close($istmt);
                }
              ?>

              <div class="purchases-header">
                <h3>My Purchases</h3>
                <form method="GET" class="sort-form">
                  <label for="sort">Sort by:</label>
                  <select name="sort" id="sort" onchange="this.form.submit()">
                    <option value="recent"    <?= $sort_by==='recent'    ? 'selected':'' ?>>Most Recent</option>
                    <option value="oldest"    <?= $sort_by==='oldest'    ? 'selected':'' ?>>Oldest</option>
                    <option value="expensive" <?= $sort_by==='expensive' ? 'selected':'' ?>>Most Expensive</option>
                    <option value="cheapest"  <?= $sort_by==='cheapest'  ? 'selected':'' ?>>Cheapest</option>
                  </select>
                </form>
              </div>

              <?php if (empty($orders)): ?>
                <p class="no-orders">You have no orders yet. <a href="products.php">Start shopping!</a></p>
              <?php else: ?>

                <?php foreach ($orders as $order):
                  $items = $order_items_map[$order['order_id']] ?? [];
                  $status_colors = [
                    'pending'    => '#f0a500',
                    'paid'       => '#4caf50',
                    'delivering' => '#2196f3',
                    'completed'  => '#9c27b0',
                  ];
                  $status_color = $status_colors[$order['status']] ?? '#888';
                ?>
                <div class="order-block" id="order-block-<?= $order['order_id'] ?>">
                  <!-- Always visible: summary row -->
                  <div class="order-block-summary" onclick="toggleOrder(<?= $order['order_id'] ?>)">
                    <div class="order-summary-left">
                      <span class="order-id">Order #<?= $order['order_id'] ?></span>
                      <span class="order-summary-names">
                        <?= htmlspecialchars(implode(', ', array_column($items, 'name'))) ?>
                      </span>
                    </div>
                    <div class="order-summary-right">
                      <span class="order-status-badge" style="background-color:<?= $status_color ?>">
                        <?= ucfirst($order['status']) ?>
                      </span>
                      <span class="toggle-icon" id="toggle-icon-<?= $order['order_id'] ?>">▼</span>
                    </div>
                  </div>

                  <!-- Collapsible details -->
                  <div class="order-block-details" id="order-details-<?= $order['order_id'] ?>" style="display:none;">
                    <p class="order-date"><?= date('F j, Y', strtotime($order['created_at'])) ?></p>

                    <?php foreach ($items as $item):
                      $img = $item['image_file']
                        ? '../public/images/products/' . $item['image_file']
                        : '../public/images/product image.png';
                      $line_total = floatval($item['unit_price']) * intval($item['quantity']);
                    ?>
                    <div class="order-item">
                      <img src="<?= $img ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="order-img" />
                      <div class="order-details">
                        <p class="order-title"><?= htmlspecialchars($item['name']) ?></p>
                        <p class="order-qty">Qty: <?= intval($item['quantity']) ?></p>
                        <p class="order-var">₱<?= number_format($item['unit_price'], 2) ?> each</p>
                      </div>
                      <p class="order-price">₱<?= number_format($line_total, 2) ?></p>
                    </div>
                    <?php endforeach; ?>

                    <div class="order-block-footer">
                      <span class="order-total-label">Order Total:</span>
                      <span class="order-total-value">₱<?= number_format($order['total'], 2) ?></span>
                    </div>
                  </div>
                </div>

                <?php if (!($order === end($orders))): ?>
                  <hr class="divider" />
                <?php endif; ?>

                <?php endforeach; ?>
              <?php endif; ?>

            </div>
          </div>

           <div class="right-panel">
            <div class="profile-card right-card">
              <div class="account-menu">
                <div class="menu-item">
                  <span class="menu-icon"><img src="../public/images/myaccount updated.png" alt="profile icon"></span>
                  <a href="profile.php" class="menu-link"><p>My Account</p></a>
                </div>
                <div class="menu-item">
                  <span class="menu-icon"><img src="../public/images/purchases icon updated.png" alt="purchases"></span>
                  <a href="profilePurchases.php" class="menu-link"><p>Purchases</p></a>
                </div>

                <div class="menu-item">
                  <span class="menu-icon"></span>
                  <a href="../../../logout.php" class="menu-link"><p>Log Out</p></a>
                </div>
              </div>
            </div>
          </div>
      </section>

      <section>
        <div></div>
        <div></div>
        <div></div>
      </section>

      <footer id="footer">
          <div id="footer-content">
          <div id="footer-logo">
            <img src="../public/images/footer-logo.png" alt="Purple String Logo" width="100" />
          </div>

          <div id="footer-information">
            <div class="info-item">
              <img src="../public/images/mail icon.png" alt="Mail" class="footer-icon" />
              <span>purplestring@gmail.com</span>
            </div>

            <div class="info-item">
              <img src="../public/images/phonenum.png" alt="Phone" class="footer-icon" />
              <span>+63 900 123 4567</span>
            </div>
          </div>
        </div>
      </footer>
      <div id="page-design">
        <img id="homepage_whiteflower_1" src="../public/images/whiteflower.png" />
        <img id="homepage_bluething" src="../public/images/bluething.png" />
        <img id="homepage_heartbutton" src="../public/images/heartbutton.png" />
        <img id="homepage_greenbutton" src="../public/images/greenbutton.png" />
        <img id="homepage_greenthread" src="../public/images/greenthread.png" />
        <img id="homepage_whiteflower_2" src="../public/images/whiteflower.png" />
      </div>
    </div>
    <script src="../js/profile.js"></script>
    <script>
      function toggleOrder(id) {
        var details = document.getElementById('order-details-' + id);
        var icon    = document.getElementById('toggle-icon-' + id);
        if (details.style.display === 'none') {
          details.style.display = 'block';
          icon.textContent = '▲';
        } else {
          details.style.display = 'none';
          icon.textContent = '▼';
        }
      }
    </script>
  </body>
</html>