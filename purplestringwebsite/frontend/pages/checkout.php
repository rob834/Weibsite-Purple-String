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
            href="../../index.php"
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
        <!-- Logistics Section -->
        <div class="logistics-section">
          <h1>Logistics</h1>

          <!-- Invoice Receipt Card -->
          <div class="invoice-card">
            <!-- Left Side: Invoice Details -->
            <div class="invoice-left">
              <div class="invoice-header">
                <h2>Invoice Receipt</h2>
                <p class="email-note">Receipt sent to your email</p>
              </div>

              <div class="customer-info">
                <div class="info-group">
                  <label>Customer Name</label>
                  <p>Maria Santos</p>
                </div>
                <div class="info-group">
                  <label>Contact Number</label>
                  <p>+63 900 123 4567</p>
                </div>
                <div class="info-group">
                  <label>Delivery Address</label>
                  <p>123 Flower Street, Manila, Philippines 1000</p>
                </div>
              </div>
            </div>

            <!-- Separator Line -->
            <div class="invoice-separator"></div>

            <!-- Right Side: Order Processing Status -->
            <div class="invoice-right">
              <div class="order-status-header">
                <h2>Your Order is Processed</h2>
              </div>

              <div class="order-timeline">
                <div class="timeline-item completed">
                  <div class="timeline-dot"></div>
                  <div class="timeline-content">
                    <p class="timeline-title">Order Confirmed</p>
                    <p class="timeline-date">November 11, 2025</p>
                  </div>
                </div>

                <div class="timeline-item completed">
                  <div class="timeline-dot"></div>
                  <div class="timeline-content">
                    <p class="timeline-title">Payment Verified</p>
                    <p class="timeline-date">November 11, 2025</p>
                  </div>
                </div>

                <div class="timeline-item active">
                  <div class="timeline-dot"></div>
                  <div class="timeline-content">
                    <p class="timeline-title">Being Packaged</p>
                    <p class="timeline-date">In Progress</p>
                  </div>
                </div>

                <div class="timeline-item">
                  <div class="timeline-dot"></div>
                  <div class="timeline-content">
                    <p class="timeline-title">Out for Delivery</p>
                    <p class="timeline-date">Pending</p>
                  </div>
                </div>

                <div class="timeline-item">
                  <div class="timeline-dot"></div>
                  <div class="timeline-content">
                    <p class="timeline-title">Delivered</p>
                    <p class="timeline-date">Pending</p>
                  </div>
                </div>
              </div>

              <div class="tracking-history">
                <h3>Tracking History</h3>
                <div class="tracking-item">
                  <span class="track-time">11:30 AM</span>
                  <span class="track-status"
                    >Order confirmed and processing started</span
                  >
                </div>
                <div class="tracking-item">
                  <span class="track-time">2:15 PM</span>
                  <span class="track-status"
                    >Payment verified successfully</span
                  >
                </div>
                <div class="tracking-item">
                  <span class="track-time">3:45 PM</span>
                  <span class="track-status"
                    >Items being packed for shipment</span
                  >
                </div>
              </div>
            </div>
          </div>

          <!-- Continue Shopping Section -->
          <div class="continue-shopping">
            <h2>Continue Shopping</h2>
            <div class="products-grid">
              <div class="product-card">
                <img
                  src="../public/images/product image.png"
                  alt="Flyers" />
                <h3>Flyers/Brochure</h3>
                <p class="product-price">₱250.00</p>
                <button class="add-to-cart-btn">View more</button>
              </div>

              <div class="product-card">
                <img
                  src="../public/images/categories 2.png"
                  alt="Keychain" />
                <h3>Custom Keychain</h3>
                <p class="product-price">₱150.00</p>
                <button class="add-to-cart-btn">View more</button>
              </div>

              <div class="product-card">
                <img
                  src="../public/images/categories 3.png"
                  alt="Shirt" />
                <h3>Custom Shirt</h3>
                <p class="product-price">₱350.00</p>
                <button class="add-to-cart-btn">View more</button>
              </div>

              <div class="product-card">
                <img
                  src="../public/images/product image.png"
                  alt="Crochet" />
                <h3>Crochet Item</h3>
                <p class="product-price">₱200.00</p>
                <button class="add-to-cart-btn">View more</button>
              </div>
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
