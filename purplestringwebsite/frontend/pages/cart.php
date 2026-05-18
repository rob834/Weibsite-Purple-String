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
      href="../css/cart.css" />
  </head>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap');

    .cart-card-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 12px;
    }

    .cart-card-header h2 {
      margin: 0;
    }

    .cart-header-actions {
      display: flex;
      gap: 8px;
      align-items: center;
    }

    .remove-selected-btn,
    .update-all-btn {
      padding: 7px 16px;
      border-radius: 6px;
      font-size: 0.85rem;
      font-weight: 600;
      cursor: pointer;
      border: none;
      transition: background 0.2s, opacity 0.2s;
    }

    .remove-selected-btn {
      background: #e53e3e;
      color: #fff;
    }

    .checkout-btn {
      margin-left: 50px;
    }

    .remove-selected-btn:hover:not(:disabled) {
      background: #c53030;
    }

    .remove-selected-btn:disabled {
      opacity: 0.45;
      cursor: not-allowed;
    }

    .update-all-btn {
      background: #C071D0;
      color: #fff;
    }

    .update-all-btn:hover {
      background: #553c9a;
    }

    .item-checkbox {
      width: 17px;
      height: 17px;
      accent-color: #6b46c1;
      cursor: pointer;
      flex-shrink: 0;
      margin-right: 4px;
    }

    .qty-input {
      width: 64px;
      padding: 4px 6px;
      border: 1px solid #ccc;
      border-radius: 4px;
      text-align: center;
      font-size: 0.95rem;
    }
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
            class="menubuttonselected">Home</a></button>
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
        <div class="cart-container">
          <!-- My Cart Card -->
          <div class="cart-card">
            <div class="cart-card-header">
              <h2>My Cart</h2>
              <div class="cart-header-actions">
                <button type="button" class="remove-selected-btn" id="removeSelectedBtn" onclick="submitRemoveSelected()" disabled>Remove Selected</button>
                <button type="button" class="update-all-btn" id="updateAllBtn" onclick="submitUpdateAll()">Update All</button>
              </div>
            </div>

            <?php
            include_once __DIR__ . '/../../backend/connection.php';

            $cart = $_SESSION['cart'] ?? [];
            $subtotal = 0.0;

            if (empty($cart)) {
                echo '<div class="cart-items"><p>Your cart is empty.</p></div>';
            } else {
                // Fetch product data for items in cart
                $ids = array_keys($cart);
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $types = str_repeat('i', count($ids));
                $stmt = mysqli_prepare($con, "SELECT product_id, name, price FROM products WHERE product_id IN ($placeholders)");
                mysqli_stmt_bind_param($stmt, $types, ...$ids);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                $products_map = [];
                while ($row = mysqli_fetch_assoc($res)) {
                    $products_map[$row['product_id']] = $row;
                }
                mysqli_stmt_close($stmt);
            ?>

            <!-- Update All form wraps all items -->
            <form method="POST" action="../../backend/update_cart.php" id="updateAllForm">
              <div class="cart-items">
                <?php foreach ($cart as $pid => $qty):
                    if (!isset($products_map[$pid])) continue;
                    $prod = $products_map[$pid];
                    $line_total = floatval($prod['price']) * intval($qty);
                    $subtotal += $line_total;
                    $img = '../public/images/products/';
                    $imgres = mysqli_query($con, "SELECT file_name FROM product_images WHERE product_id = " . intval($pid) . " AND is_primary = 1 LIMIT 1");
                    $imgfile = ($imgres && mysqli_num_rows($imgres)) ? mysqli_fetch_assoc($imgres)['file_name'] : 'product image.png';
                    $imgsrc = $img . $imgfile;
                ?>
                <div class="cart-item">
                  <input
                    type="checkbox"
                    class="item-checkbox"
                    name="remove_ids[]"
                    value="<?= $pid ?>"
                    onchange="updateRemoveBtn()" />
                  <img src="<?= $imgsrc ?>" alt="<?= htmlspecialchars($prod['name']) ?>" class="item-image" />
                  <div class="item-details">
                    <h3><?= htmlspecialchars($prod['name']) ?></h3>
                    <p class="price">₱<?= number_format($prod['price'], 2) ?></p>
                  </div>
                  <div class="item-quantity">
                    <input type="hidden" name="product_id[]" value="<?= $pid ?>" />
                    <input type="number" name="quantity[]" value="<?= intval($qty) ?>" min="1" class="qty-input" />
                  </div>
                  <div class="item-total">
                    <p>₱<?= number_format($line_total, 2) ?></p>
                  </div>
                </div>
                <?php endforeach; ?>
              </div>
            </form>

            <!-- Remove Selected hidden form -->
            <form method="POST" action="../../backend/remove_from_cart.php" id="removeSelectedForm">
              <div id="removeInputsContainer"></div>
            </form>

            <?php } ?>
          </div>

          <!-- Order Summary Card -->
                    <!-- Order Summary Card -->

          <div class="order-card">

            <h2>Order Summary</h2>

            <div class="order-details">

              <?php foreach ($cart as $pid => $qty):

                if (!isset($products_map[$pid])) continue;

                $prod = $products_map[$pid];

                $line_total = floatval($prod['price']) * intval($qty);

              ?>

              <div class="order-row">

                <span><?= htmlspecialchars($prod['name']) ?> x<?= intval($qty) ?></span>

                <span class="amount">₱<?= number_format($line_total, 2) ?></span>

              </div>

              <?php endforeach; ?>

              <div class="order-row total">

                <span>Total:</span>

                <span class="amount total-amount">₱<?= number_format($subtotal, 2) ?></span>

              </div>

            </div>

            <form method="POST" action="../../backend/place_order.php">

              <button type="submit" class="checkout-btn" <?= empty($cart) ? 'disabled' : '' ?>>

                Proceed to Checkout

              </button>

            </form>

              </div>
        </div>
      </section>



    </div>
  <script>
    function updateRemoveBtn() {
      const checked = document.querySelectorAll('.item-checkbox:checked');
      const btn = document.getElementById('removeSelectedBtn');
      if (btn) btn.disabled = checked.length === 0;
    }

    function submitRemoveSelected() {
      const checked = document.querySelectorAll('.item-checkbox:checked');
      if (checked.length === 0) return;
      const container = document.getElementById('removeInputsContainer');
      container.innerHTML = '';
      checked.forEach(function(cb) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'product_id[]';
        input.value = cb.value;
        container.appendChild(input);
      });
      document.getElementById('removeSelectedForm').submit();
    }

    function submitUpdateAll() {
      document.getElementById('updateAllForm').submit();
    }
  </script>



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
      
  </body>
</html>