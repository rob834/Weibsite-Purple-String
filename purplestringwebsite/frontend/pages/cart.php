<?php
session_start();

if (!isset($_SESSION['user_id'])) {
        header("Location: /Weibsite-Purple-String/login.php");
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

        <div id="rightheader">
          <div id="shoppingcart">
            <a href="cart.php"
              ><img src="../public/images/shopping cart.png"
            /></a>
          </div>
          <div id="account-circle">
            <a href="profile.php"
              ><img src="../public/images/profile icon.png"
            /></a>
          </div>
        </div>

        <div id="menubar">
          <a
            href="/Weibsite-Purple-String/index.php"
            class="menubutton"
            >Home</a
          >
          <a
            href="../pages/products.php"
            class="menubutton"
            >Products</a
          >
          <a
            href="../pages/contacts.php"
            class="menubutton"
            >Contacts</a
          >
        </div>

        <div id="frills">
          <img src="../public/images/vectors/frills.png" />
        </div>
      </section>

      <section id="content">
        <div class="cart-container">
          <!-- My Cart Card -->
          <div class="cart-card">
            <h2>My Cart</h2>
            <div class="cart-items">
              <?php
              include_once __DIR__ . '/../../backend/connection.php';

              $cart = $_SESSION['cart'] ?? [];
              $subtotal = 0.0;

              if (empty($cart)) {
                  echo '<p>Your cart is empty.</p>';
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

                  foreach ($cart as $pid => $qty) {
                      if (!isset($products_map[$pid])) continue;
                      $prod = $products_map[$pid];
                      $line_total = floatval($prod['price']) * intval($qty);
                      $subtotal += $line_total;
                      $img = '../public/images/products/';
                      // attempt to find primary image
                      $imgres = mysqli_query($con, "SELECT file_name FROM product_images WHERE product_id = " . intval($pid) . " AND is_primary = 1 LIMIT 1");
                      $imgfile = ($imgres && mysqli_num_rows($imgres)) ? mysqli_fetch_assoc($imgres)['file_name'] : 'product image.png';
                      $imgsrc = $img . $imgfile;
                      ?>
                      <div class="cart-item">
                        <img src="<?= $imgsrc ?>" alt="<?= htmlspecialchars($prod['name']) ?>" class="item-image" />
                        <div class="item-details">
                          <h3><?= htmlspecialchars($prod['name']) ?></h3>
                          <p class="price">₱<?= number_format($prod['price'], 2) ?></p>
                        </div>
                        <div class="item-quantity">
                          <form method="POST" action="../../backend/update_cart.php">
                            <input type="hidden" name="product_id" value="<?= $pid ?>" />
                            <input type="number" name="quantity" value="<?= intval($qty) ?>" min="0" />
                            <button type="submit">Update</button>
                          </form>
                        </div>
                        <div class="item-total">
                          <p>₱<?= number_format($line_total, 2) ?></p>
                        </div>
                        <form method="POST" action="../../backend/remove_from_cart.php">
                          <input type="hidden" name="product_id" value="<?= $pid ?>" />
                          <button class="remove-btn" type="submit">Remove</button>
                        </form>
                      </div>
                      <?php
                  }
              }
              ?>
            </div>
          </div>

          <!-- Order Summary Card -->
          <div class="order-card">
            <h2>Order Summary</h2>
            <div class="order-details">
              <div class="order-row">
                <span>Subtotal:</span>
                <span class="amount">₱<?= number_format($subtotal, 2) ?></span>
              </div>
              <div class="order-row">
                <span>Shipping:</span>
                <?php $shipping = ($subtotal > 0) ? 50.00 : 0.00; ?>
                <span class="amount">₱<?= number_format($shipping, 2) ?></span>
              </div>
              <div class="order-row">
                <span>Tax:</span>
                <?php $tax = $subtotal * 0.08; ?>
                <span class="amount">₱<?= number_format($tax, 2) ?></span>
              </div>
              <div class="order-row total">
                <span>Total:</span>
                <span class="amount total-amount">₱<?= number_format($subtotal + $shipping + $tax, 2) ?></span>
              </div>
            </div>
            <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
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
  </body>
</html>
