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
    <title>Products</title>
    <link
      rel="stylesheet"
      href="../css/product.css" />
  </head>
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
          // determine avatar for header
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
            href="products.php" 
            class="menubuttonselected">Products</a></button>
      
        </div>

        <div id="frills">
          <img src="../public/images/vectors/frills.png" />
        </div>
      </section>
      <section id="content">
      <?php
      include_once __DIR__ . '/../../backend/connection.php';

      // Read filters from query params
      $selected_category = isset($_GET['category']) && is_numeric($_GET['category']) ? intval($_GET['category']) : null;
      $sort = $_GET['sort'] ?? 'popular'; // popular, latest, price_asc, price_desc

      // Fetch categories for the filter dropdown
      $categories = [];
      $cres = mysqli_query($con, "SELECT category_id, name FROM categories ORDER BY name ASC");
      if ($cres) {
        while ($crow = mysqli_fetch_assoc($cres)) {
          $categories[] = $crow;
        }
      }

      // Build products query
      $orderBy = 'p.product_id DESC';
      if ($sort === 'latest') $orderBy = 'p.created_at DESC';
      if ($sort === 'price_asc') $orderBy = 'p.price ASC';
      if ($sort === 'price_desc') $orderBy = 'p.price DESC';

      $where = '';
      if ($selected_category) {
        $where = 'WHERE p.category_id = ' . intval($selected_category);
      }

      $sql = "SELECT p.*, c.name AS category_name, pi.file_name AS image_file, pr.avg_rating, pr.count_ratings
          FROM products p
          LEFT JOIN categories c ON p.category_id = c.category_id
          LEFT JOIN product_images pi ON pi.product_id = p.product_id AND pi.is_primary = 1
          LEFT JOIN (
            SELECT product_id, AVG(rating) AS avg_rating, COUNT(*) AS count_ratings
            FROM product_ratings
            GROUP BY product_id
          ) pr ON pr.product_id = p.product_id
          $where
          ORDER BY $orderBy";

      $products = [];
      $pres = mysqli_query($con, $sql);
      if ($pres) {
        while ($prow = mysqli_fetch_assoc($pres)) {
          $products[] = $prow;
        }
      }
      ?>

      <section class="product-controls">
        <form method="GET" class="filters-form">
          <div class="sort-options">
            <label for="sort">Sort by:</label>
            <select id="sort" name="sort" onchange="this.form.submit()">
              <option value="popular" <?php if($sort==='popular') echo 'selected'; ?>>Popular</option>
              <option value="latest" <?php if($sort==='latest') echo 'selected'; ?>>Latest</option>
              <option value="price_asc" <?php if($sort==='price_asc') echo 'selected'; ?>>Price: Low → High</option>
              <option value="price_desc" <?php if($sort==='price_desc') echo 'selected'; ?>>Price: High → Low</option>
            </select>
          </div>

          <div class="category-search">
            <label for="category">Category:</label>
            <select id="category" name="category" onchange="this.form.submit()">
              <option value="">All</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['category_id'] ?>" <?php if($selected_category===$cat['category_id']) echo 'selected'; ?>><?= htmlspecialchars($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </form>
      </section>

      <!-- Product Grid Starts Here -->
      <section class="product-grid">
        <?php if (empty($products)): ?>
          <p>No products found.</p>
        <?php endif; ?>

        <?php foreach ($products as $p): 
          $img = $p['image_file'] ? '../public/images/products/' . $p['image_file'] : '../public/images/product image.png';
        ?>
        <div class="product-card">
          <a href="view/product.php?product_id=<?= $p['product_id'] ?>">
            <img src="<?= $img ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="product-img">
          </a>

          <div class="product-info">
            <p class="product-name"><a href="view/product.php?product_id=<?= $p['product_id'] ?>"><?= htmlspecialchars($p['name']) ?></a></p>
            <div class="rating">
              <span class="star">⭐</span>
              <span class="rating-value"><?= isset($p['avg_rating']) ? number_format(floatval($p['avg_rating']), 2) : '0.00' ?></span>
              <span class="rating-count"><?= isset($p['count_ratings']) ? intval($p['count_ratings']) : 0 ?></span>
            </div>
            <p class="price">₱<?= number_format($p['price'], 2) ?></p>
            <p class="category-label"><?= htmlspecialchars($p['category_name'] ?? 'Uncategorized') ?></p>
            <form class="cart-form">
              <input type="hidden" class="product-id" value="<?= $p['product_id'] ?>" />
              <input type="hidden" class="quantity" value="1" />
              <button type="submit" class="cart-btn">🛒</button>
            </form>
          </div>
        </div>
        <?php endforeach; ?>
      </section>

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
    // Handle cart form submissions
    (function(){
      var forms = document.querySelectorAll('.cart-form');
      forms.forEach(function(form){
        form.addEventListener('submit', function(e){
          e.preventDefault();
          
          var productId = form.querySelector('.product-id').value;
          var quantity = form.querySelector('.quantity').value;
          
          var fd = new FormData();
          fd.append('product_id', productId);
          fd.append('quantity', quantity);
          
          fetch('../../backend/add_to_cart.php', { 
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
              alert('Error adding to cart. Please try again.');
            }
          })
          .catch(function(err) {
            console.error('add to cart error', err);
            alert('Error adding to cart. Please try again.');
          });
        });
      });
    })();
  </script>
  </body>
</html>