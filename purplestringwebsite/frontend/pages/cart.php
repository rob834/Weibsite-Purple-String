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

    .remove-selected-btn:hover:not(:disabled) {
      background: #c53030;
    }

    .remove-selected-btn:disabled {
      opacity: 0.45;
      cursor: not-allowed;
    }

    .update-all-btn {
      background: #6b46c1;
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
                $stmt = mysqli_prepare($con, "SELECT product_id, name, price, stock FROM products WHERE product_id IN ($placeholders)");
                mysqli_stmt_bind_param($stmt, $types, ...$ids);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                $products_map = [];
                while ($row = mysqli_fetch_assoc($res)) {
                    $products_map[$row['product_id']] = $row;
                }
                mysqli_stmt_close($stmt);
            ?>

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
  <input type="number" name="quantity[]" value="<?= intval($qty) ?>" min="1" max="<?= intval($prod['stock']) ?>" class="qty-input" oninput="if(parseInt(this.value) > parseInt(this.max)) this.value = this.max;" />
  <span style="display:block; font-size:11px; color:#888; text-align:center; margin-top:2px;">Max: <?= $prod['stock'] ?></span>
</div>
                  <div class="item-total">
                    <p>₱<?= number_format($line_total, 2) ?></p>
                  </div>
                </div>
                <?php endforeach; ?>
              </div>
            </form>

            <form method="POST" action="../../backend/remove_from_cart.php" id="removeSelectedForm">
              <div id="removeInputsContainer"></div>
            </form>

            <?php } ?>
          </div>

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

            <form method="POST" action="../../backend/place_order.php" id="checkoutForm">
              <input type="hidden" name="reference_number" id="referenceNumberInput" value="" />
              <button type="button" class="checkout-btn" <?= empty($cart) ? 'disabled' : '' ?> onclick="startCheckout()">
                Proceed to Checkout
              </button>
            </form>

              </div>
        </div>
      </section>

      <footer id="footer">
        <div id="footer-content">
          <div id="footer-logo">
            <img
              src="./public/images/footer-logo.png"
              alt="Purple String Logo"
              width="100" />
          </div>

          <div id="footer-information">
            <div class="info-item">
              <img
                src="./public/images/mail.png"
                alt="Mail"
                class="footer-icon" />
              <span>purplestring@gmail.com</span>
            </div>

            <div class="info-item">
              <img
                src="./public/images/phonenum.png"
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
    </div>
  <div id="profileModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:14px;padding:36px 32px;max-width:420px;width:90%;text-align:center;box-shadow:0 8px 32px rgba(0,0,0,0.18);">
      <div style="font-size:48px;margin-bottom:12px;">⚠️</div>
      <h2 style="color:#6b21a8;margin:0 0 10px;">Profile Incomplete</h2>
      <p style="color:#555;margin:0 0 20px;line-height:1.6;">
        Please add your <strong>phone number</strong> and <strong>delivery address</strong>
        in your profile before checking out. We need these to deliver your order!
      </p>
      <a href="profile.php"
         style="display:inline-block;background:#6b21a8;color:#fff;text-decoration:none;padding:12px 28px;border-radius:8px;font-weight:700;margin-right:8px;">
        Go to Profile
      </a>
      <button onclick="closeModal('profileModal')"
              style="padding:12px 20px;border:2px solid #6b21a8;background:#fff;color:#6b21a8;border-radius:8px;font-weight:700;cursor:pointer;">
        Cancel
      </button>
    </div>
  </div>

  <div id="paymentModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:14px;padding:36px 32px;max-width:460px;width:90%;box-shadow:0 8px 32px rgba(0,0,0,0.18);">
      <h2 style="color:#6b21a8;margin:0 0 6px;text-align:center;">Complete Your Payment</h2>
      <p style="color:#888;font-size:13px;text-align:center;margin:0 0 10px;">Scan the QR code below and send the exact amount, then enter your reference number.</p>

      <h3 style="text-align:center; color:#6b46c1; margin:0 0 20px 0; font-family:'Inter', sans-serif; font-size:18px; font-weight:700;">
        Amount Payable: ₱<?= number_format($subtotal, 2) ?>
      </h3>

      <div style="border:2px dashed #d8b4fe;border-radius:10px;padding:20px;text-align:center;margin-bottom:20px;background:#faf5ff;">
        <img src="../public/images/qr_code.png?t=<?= time() ?>"
             onerror="this.style.display='none';document.getElementById('qrPlaceholder').style.display='block';"
             style="max-width:220px;width:100%;border-radius:6px;" />
        <div id="qrPlaceholder" style="display:none;padding:40px 0;color:#a855f7;font-size:14px;">
          <div style="font-size:48px;margin-bottom:8px;">📷</div>
          QR code will be placed here by admin.<br>
          <span style="font-size:12px;color:#bbb;">Upload <code>qr_code.png</code> to <code>frontend/public/images/</code></span>
        </div>
      </div>

      <div style="margin-bottom:20px;">
        <label style="display:block;font-weight:700;color:#4a1d96;margin-bottom:6px;font-size:14px;">
          Payment Reference Number <span style="color:#e53e3e;">*</span>
        </label>
        <input type="text" id="refNumberField" placeholder="e.g. 1234567890"
               style="width:100%;box-sizing:border-box;padding:10px 14px;border:2px solid #d8b4fe;border-radius:8px;font-size:15px;outline:none;"
               oninput="validateRefField()" />
        <p id="refError" style="color:#e53e3e;font-size:12px;margin:4px 0 0;display:none;">Please enter your reference number.</p>
      </div>

      <div style="display:flex;gap:10px;">
        <button onclick="closeModal('paymentModal')"
                style="flex:1;padding:12px;border:2px solid #ccc;background:#fff;color:#666;border-radius:8px;font-weight:700;cursor:pointer;font-size:14px;">
          Cancel
        </button>
        <button id="confirmPayBtn" onclick="confirmPayment()"
                style="flex:2;padding:12px;background:#6b21a8;color:#fff;border:none;border-radius:8px;font-weight:700;cursor:pointer;font-size:15px;">
          Confirm &amp; Place Order
        </button>
      </div>
    </div>
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

    function openModal(id) {
      document.getElementById(id).style.display = 'flex';
    }

    function closeModal(id) {
      document.getElementById(id).style.display = 'none';
    }

    // Close modals when clicking the backdrop
    ['profileModal', 'paymentModal'].forEach(function(id) {
      document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) closeModal(id);
      });
    });

    function validateRefField() {
      const val = document.getElementById('refNumberField').value.trim();
      document.getElementById('refError').style.display = val ? 'none' : 'block';
      return val.length > 0;
    }

    function startCheckout() {
      // Check profile completeness via AJAX
      fetch('/Weibsite-Purple-String/purplestringwebsite/backend/check_profile.php', {
        method: 'GET',
        credentials: 'same-origin'
      })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (data.complete) {
          openModal('paymentModal');
        } else {
          openModal('profileModal');
        }
      })
      .catch(function() {
        // If check fails, allow checkout to proceed anyway
        openModal('paymentModal');
      });
    }

    function confirmPayment() {
      const ref = document.getElementById('refNumberField').value.trim();
      if (!ref) {
        document.getElementById('refError').style.display = 'block';
        return;
      }
      document.getElementById('referenceNumberInput').value = ref;
      const btn = document.getElementById('confirmPayBtn');
      btn.disabled = true;
      btn.textContent = 'Placing Order...';
      document.getElementById('checkoutForm').submit();
    }
  </script>
  </body>
</html>