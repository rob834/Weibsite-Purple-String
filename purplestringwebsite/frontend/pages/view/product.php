<?php
session_start();

// allow guests to view but require login for rating action (frontend will handle redirect)
// (keep existing behavior: redirect to login if not logged in when necessary elsewhere)

// Load product information if product_id provided
include_once __DIR__ . '/../../../backend/connection.php';
$con = get_db_connection();
$product = null;
$images = [];
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
if ($product_id > 0 && $con) {
  $pstmt = mysqli_prepare($con, "SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id WHERE p.product_id = ? LIMIT 1");
  mysqli_stmt_bind_param($pstmt, 'i', $product_id);
  mysqli_stmt_execute($pstmt);
  $pres = mysqli_stmt_get_result($pstmt);
  if ($pres && mysqli_num_rows($pres) > 0) {
    $product = mysqli_fetch_assoc($pres);
  }
  mysqli_stmt_close($pstmt);

  // fetch images
  $ir = mysqli_prepare($con, "SELECT file_name, is_primary FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, image_id ASC");
  mysqli_stmt_bind_param($ir, 'i', $product_id);
  mysqli_stmt_execute($ir);
  $ires = mysqli_stmt_get_result($ir);
  if ($ires) {
    while ($row = mysqli_fetch_assoc($ires)) $images[] = $row['file_name'];
  }
  mysqli_stmt_close($ir);
}

// Check stock status dynamically
$is_out_of_stock = intval($product['stock'] ?? 0) <= 0;
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0" />
    <title>Products</title>
    <link rel="stylesheet" href="../../css/product.css" />
    <link rel="stylesheet" href="../../css/product-view.css" />
    <style>
      @import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap');
    </style>
  </head>
  <body>
    <div id="page-container">
      <section id="header">
        <div id="PurpleBox"></div>
        <div id="leftheader">
          <div id="search">
            <label for="searchbar">
              <img src="../../public/images/search.png" />
            </label>
            <input
              type="text"
              name="search"
              id="searchbar" />
          </div>
        </div>

        <div id="centerheader">
          <div id="logo">
            <img src="../../public/images/Logo.png" />
          </div>
        </div>

        <?php
          $avatar_src = '../../public/images/profile icon.png';
          if (isset($_SESSION['user_id']) && $con) {
            $uid = $_SESSION['user_id'];
            $uqr = mysqli_prepare($con, "SELECT avatar FROM users WHERE user_id = ? LIMIT 1");
            mysqli_stmt_bind_param($uqr, 's', $uid);
            mysqli_stmt_execute($uqr);
            $ures = mysqli_stmt_get_result($uqr);
            if ($ures && ($urow = mysqli_fetch_assoc($ures))) {
              if (!empty($urow['avatar']) && file_exists(__DIR__ . '/../../public/images/avatars/' . $urow['avatar'])) {
                $avatar_src = '../../public/images/avatars/' . $urow['avatar'];
              }
            }
            mysqli_stmt_close($uqr);
          }
        ?>
        <div id="rightheader">
          <div id="shoppingcart">
            <a href="../cart.php"><img src="../../public/images/shopping cart.png" /></a>
          </div>
          <div id="account-circle">
            <a href="../profile.php"><img src="<?= $avatar_src ?>" alt="profile" /></a>
          </div>
        </div>

        <div id="menubar">
          <button><a
            href="../../../../index.php"
            class="menubutton">Home</a></button>
          <button><a
            href="../products.php"
            class="menubutton"
            >Products</a
          ></button>
      
        </div>

        <div id="frills">
          <img src="../../public/images/vectors/frills.png" />
        </div>
      </section>
      <section id="content">
            <div class="product-card">
          <div class="collage-left">
            <?php if (!empty($images)): ?>
              <img src="../../public/images/products/<?= htmlspecialchars($images[0]) ?>" alt="Main" />
              <?php for ($i=1;$i<min(5,count($images));$i++): ?>
                <img src="../../public/images/products/<?= htmlspecialchars($images[$i]) ?>" alt="Extra <?= $i ?>" />
              <?php endfor; ?>
            <?php else: ?>
              <img src="../../public/images/product image.png" alt="Coillage main" />
              <img src="../../public/images/product image.png" alt="Coillage 2" />
              <img src="../../public/images/product image.png" alt="Coillage 3" />
              <img src="../../public/images/product image.png" alt="Coillage 4" />
              <img src="../../public/images/product image.png" alt="Coillage 5" />
            <?php endif; ?>
          </div>

          <div class="product-info-right">
            <div class="product-header-row">
              <h2 class="product-title"><?= htmlspecialchars($product['name'] ?? 'Product') ?></h2>
              <div class="rating" data-product-id="<?= $product_id ?>">
                <span class="stars">
                  <span class="star-buttons">
                    </span>
                </span>
                <span class="score">0</span>
                <span class="count">• 0 ratings</span>
              </div>
              <div class="prices">
                <div class="current-price">₱<?= number_format(($product['price'] ?? 0), 2) ?></div>
              </div>
            </div>

            <div class="stock-display" style="margin: 12px 0;">
              <?php if ($is_out_of_stock): ?>
                <span style="background-color: #fee2e2; color: #ef4444; padding: 6px 12px; border-radius: 6px; font-weight: bold; font-size: 0.9rem; display: inline-block;">⚠️ Out of Stock</span>
              <?php else: ?>
                <span style="background-color: #d1fae5; color: #10b981; padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 0.85rem; display: inline-block;">In Stock: <?= intval($product['stock']) ?> left</span>
              <?php endif; ?>
            </div>

            <div class="actions">
              <button class="btn-add-to-cart" <?= $is_out_of_stock ? 'disabled style="background-color: #cbd5e1; color: #64748b; cursor: not-allowed; border: none; box-shadow: none;"' : '' ?>>
                Add to Cart
              </button>
              
              <button class="btn-buy-now" <?= $is_out_of_stock ? 'disabled style="background-color: #cbd5e1; color: #64748b; cursor: not-allowed; border: none; box-shadow: none;"' : '' ?>>
                Buy Now
              </button>
            </div>

            <div class="product-description">
              <il><p>* 1side print</p></il>
              <il><p>* Back to back print</p></il>
              <il><p>* 120 gsm glossy</p></il>
              <il><p>* A4 size</p></il>
            </div>
            <div class="actions">
              <button class="btn-share">Share</button>
            </div>
          </div>
        </div>
      </section>

      <footer id="footer">
          <div id="footer-content">
          <div id="footer-logo">
            <img
              src="../../public/images/footer-logo.png"
              alt="Purple String Logo"
              width="100" />
          </div>

          <div id="footer-information">
            <div class="info-item">
              <img
                src="../../public/images/mail.png"
                alt="Mail"
                class="footer-icon" />
              <span>purplestring@gmail.com</span>
            </div>

            <div class="info-item">
              <img
                src="../../public/images/phonenum.png"
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
          src="../../public/images/whiteflower.png" />
        <img
          id="homepage_bluething"
          src="../../public/images/bluething.png" />
        <img
          id="homepage_heartbutton"
          src="../../public/images/heartbutton.png" />
        <img
          id="homepage_greenbutton"
          src="../../public/images/greenbutton.png" />
        <img
          id="homepage_greenthread"
          src="../../public/images/greenthread.png" />
        <img
          id="homepage_whiteflower_2"
          src="../../public/images/whiteflower.png" />
      </div>
  <script src="../../js/product.js"></script>
  <script>
    // Get product ID from URL
    var productId = new URLSearchParams(window.location.search).get('product_id') || 0;

    // Add to Cart functionality
    function addToCart() {
      var fd = new FormData();
      fd.append('product_id', productId);
      fd.append('quantity', 1);

      fetch('../../../backend/add_to_cart.php', { 
        method: 'POST', 
        body: fd,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
        .then(function(r) { return r.json(); })
        .then(function(data) {
          if (data.success) {
            alert('Product added to cart!');
          } else {
            // Enhanced Security: Display the backend error message directly to the customer
            alert(data.error || 'Error adding to cart. Please try again.');
          }
        })
        .catch(function(err) {
          console.error('add to cart error', err);
          alert('Error adding to cart. Please try again.');
        });
    }

    // Add to Cart Button
    (function(){
      var addBtn = document.querySelector('.btn-add-to-cart');
      if(addBtn){
        addBtn.addEventListener('click', function(){
          addToCart();
        });
      }
    })();

    // Buy Now Button - Add to Cart and Redirect
    (function(){
      var buyBtn = document.querySelector('.btn-buy-now');
      if(buyBtn){
        buyBtn.addEventListener('click', function(){
          var fd = new FormData();
          fd.append('product_id', productId);
          fd.append('quantity', 1);

          fetch('../../../backend/add_to_cart.php', { 
            method: 'POST', 
            body: fd,
            headers: {
              'X-Requested-With': 'XMLHttpRequest'
            }
          })
            .then(function(r) { return r.json(); })
            .then(function(data) {
              if (data.success) {
                window.location.href = '../cart.php';
              } else {
                // Enhanced Security: Display backend validation constraint rejection messages
                alert(data.error || 'Error adding to cart. Please try again.');
              }
            })
            .catch(function(err) {
              console.error('buy now error', err);
              alert('Error processing your request. Please try again.');
            });
        });
      }
    })();

    // Ratings: fetch and render, allow user to submit rating
    (function(){
      var ratingEl = document.querySelector('.rating');
      if(!ratingEl) return;
      var productId = ratingEl.getAttribute('data-product-id');
      var scoreEl = ratingEl.querySelector('.score');
      var countEl = ratingEl.querySelector('.count');
      var starButtons = ratingEl.querySelector('.star-buttons');

      function renderStars(selected){
        starButtons.innerHTML = '';
        for(var i=1;i<=5;i++){
          var btn = document.createElement('button');
          btn.type = 'button';
          btn.className = 'star-btn';
          btn.dataset.value = i;
          btn.textContent = i <= selected ? '★' : '☆';
          (function(v){
            btn.addEventListener('click', function(){ submitRating(v); });
          })(i);
          starButtons.appendChild(btn);
        }
      }

      function updateAggregate(avg, count){
        scoreEl.textContent = avg.toFixed(2);
        countEl.textContent = '• ' + count + (count === 1 ? ' rating' : ' ratings');
      }

      function fetchRatings(){
        fetch('../../../backend/get_ratings.php?product_id=' + encodeURIComponent(productId))
          .then(function(r){ return r.json(); })
          .then(function(data){
            var avg = parseFloat(data.avg) || 0;
            var count = parseInt(data.count) || 0;
            updateAggregate(avg, count);
            var userRating = data.user_rating && data.user_rating.rating ? data.user_rating.rating : 0;
            renderStars(userRating || Math.round(avg));
          }).catch(function(err){
            console.error('ratings fetch error', err);
            renderStars(0);
          });
      }

      function submitRating(value){
        // if user not logged in, redirect to login
        <?php if (!isset($_SESSION['user_id'])): ?>
          window.location.href = '/Weibsite-Purple-String/login.php?next=' + encodeURIComponent(window.location.pathname + window.location.search);
          return;
        <?php endif; ?>

        var fd = new FormData();
        fd.append('product_id', productId);
        fd.append('rating', value);

        fetch('../../../backend/add_rating.php', { method: 'POST', body: fd })
          .then(function(r){ return r.json(); })
          .then(function(data){
            if (data && typeof data.avg !== 'undefined') {
              updateAggregate(parseFloat(data.avg), parseInt(data.count));
              renderStars(value);
            }
          }).catch(function(err){ console.error('submit rating error', err); });
      }

      // initial load
      fetchRatings();
    })();
  </script>
  </body>
</html>