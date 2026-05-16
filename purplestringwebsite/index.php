<?php
session_start();

include("purplestringwebsite/backend/connection.php");
include("purplestringwebsite/backend/functions.php");

$user_data = check_login($con);

// If user is not logged in, redirect to login page
if (!$user_data) {
    header("Location: /Weibsite-Purple-String/login.php");
    exit;
}

?>

<?php
          // determine avatar for header
          if (!isset($con)) { include_once __DIR__ . '/purplestringwebsite/backend/connection.php'; $con = function_exists('get_db_connection') ? get_db_connection() : null; }
          $avatar_src = 'purplestringwebsite/frontend/public/images/profile icon.png';
          if (isset($_SESSION['user_id']) && $con) {
            $uid = $_SESSION['user_id'];
            $uqr = mysqli_prepare($con, "SELECT avatar FROM users WHERE user_id = ? LIMIT 1");
            mysqli_stmt_bind_param($uqr, 's', $uid);
            mysqli_stmt_execute($uqr);
            $ures = mysqli_stmt_get_result($uqr);
            if ($ures && ($urow = mysqli_fetch_assoc($ures))) {
              if (!empty($urow['avatar']) && file_exists(__DIR__ . '/purplestringwebsite/frontend/public/images/avatars/' . $urow['avatar'])) {
                $avatar_src = 'purplestringwebsite/frontend/public/images/avatars/' . $urow['avatar'];
              }
            }
            mysqli_stmt_close($uqr);
          }
        ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0" />
    <title>Homescreen</title>
    <link
      rel="stylesheet"
      href="purplestringwebsite/frontend/css/homepage.css" />
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
              <img src="purplestringwebsite/frontend/public/images/search.png" />
            </label>
            <input
              type="text"
              name="search"
              id="searchbar" />
          </div>
        </div>

        <div id="centerheader">
          <div id="logo">
            <img src="purplestringwebsite/frontend/public/images/Logo.png" />
          </div>
        </div>

        <div id="rightheader">
          <div id="shoppingcart">
            <a href="purplestringwebsite/frontend/pages/cart.php"
              ><img src="purplestringwebsite/frontend/public/images/shopping cart.png"
            /></a>
          </div>
          <div id="account-circle">
            <a href="purplestringwebsite/frontend/pages/profile.php"
              ><img src="<?= $avatar_src ?>" alt="profile" /></a>
          </div>
        </div>

        <div id="menubar">
          <button><a
            href="index.php"
            class="menubuttonselected">Home</a></button>
          <button><a
            href="purplestringwebsite/frontend/pages/products.php"
            class="menubutton"
            >Products</a
          ></button>
      
        </div>

        <div id="frills">
          <img src="purplestringwebsite/frontend/public/images/vectors/frills.png" />
        </div>
      </section>

      <section id="content">
        <div id="notepad">
          <img src="purplestringwebsite/frontend/public/images/vectors/notepad.png" />
          <h1 id="notepadtext">Recent Designs</h1>
          <div id="slides">
            <div id="slideshow">
              <div id="wrapper">
                 <?php
                  // Fetch 5 most recently added products with their images
                  $query = "SELECT p.product_id, pi.file_name FROM products p 
                            LEFT JOIN product_images pi ON p.product_id = pi.product_id 
                            WHERE p.is_active = 1 
                            ORDER BY p.created_at DESC LIMIT 5";
                  
                  $result = mysqli_query($con, $query);
                  
                  if($result && mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                      $image_path = $row['file_name'] ? "purplestringwebsite/frontend/public/images/products/" . htmlspecialchars($row['file_name']) : "purplestringwebsite/frontend/public/images/carousel pic 1.png";
                      echo '<div class="product-image-wrapper"><img class="productslide" src="' . $image_path . '" /></div>';
                    }
                  } else {
                    // Fallback to default images if no products found
                    echo '<div class="product-image-wrapper"><img class="productslide" src="purplestringwebsite/frontend/public/images/carousel pic 1.png" /></div>
                          <div class="product-image-wrapper"><img class="productslide" src="purplestringwebsite/frontend/public/images/carousel pic 2.png" /></div>
                          <div class="product-image-wrapper"><img class="productslide" src="purplestringwebsite/frontend/public/images/carousel pic 3.png" /></div>
                          <div class="product-image-wrapper"><img class="productslide" src="purplestringwebsite/frontend/public/images/carousel pic 4.png" /></div>
                          <div class="product-image-wrapper"><img class="productslide" src="purplestringwebsite/frontend/public/images/carousel pic 5.png" /></div>';
                  }
                ?>
              </div>
            </div>
          </div>
        </div>
        <div id="sideproducts">
          <div id="StartOrderingHere">
            <h1>Start Ordering...</h1>
          </div>
          <div id="productreccomendations">
            <div id="productbuttons">
              <div id="custom-crochet" onclick="window.location.href='purplestringwebsite/frontend/pages/products.php?category=Crochet'" style="cursor: pointer;">
                <div id="crochet-button">
                  <img src="purplestringwebsite/frontend/public/images/hover imgs/custom-crochet.png" />
                </div>
                <div id="extra-crochet">
                  <img
                    src="purplestringwebsite/frontend/public/images/hover imgs/custom-crochet-hover.png" />
                </div>
              </div>
              <div id="custom-miscellaneous" onclick="window.location.href='purplestringwebsite/frontend/pages/products.php?category=Miscellaneous'" style="cursor: pointer;">
                <div id="miscellaneous-button">
                  <img
                    src="purplestringwebsite/frontend/public/images/hover imgs/custom-miscellaneous.png" />
                </div>
                <div id="extra-miscellaneous">
                  <img
                    src="purplestringwebsite/frontend/public/images/hover imgs/custom-miscellaneous-hover.png" />
                </div>
              </div>
              <div id="custom-print" onclick="window.location.href='purplestringwebsite/frontend/pages/products.php?category=Print'" style="cursor: pointer;">
                <div id="print-button">
                  <img src="purplestringwebsite/frontend/public/images/hover imgs/custom-prints.png" />
                </div>
                <div id="extra-print">
                  <img
                    src="purplestringwebsite/frontend/public/images/hover imgs/custom-prints-hover.png" />
                </div>
              </div>
            </div>
          </div>
        </div>

        <div id="viewallprod" onclick="window.location.href='purplestringwebsite/frontend/pages/products.php'" style="cursor: pointer;">
          <div id="viewallprod-button">
            <img
              id="viewallprod-base"
              src="purplestringwebsite/frontend/public/images/hover imgs/viewallprod.png"
              alt="View All Products" />
            <img
              id="viewallprod-hover"
              src="purplestringwebsite/frontend/public/images/hover imgs/viewallprod-hover.png"
              alt="View All Products Hover" />
          </div>
        </div>

        <div id="purplestring-description">
          <h1>
            Purple String Crochet is an online made to order shop dedicated in
            crafting handmade crochet items for memorable occasions. 100%
            handmade with TLC!
          </h1>
        </div>

        <div id="otherwebsites">
          <a><img src="purplestringwebsite/frontend/public/images/shopeeicon.png" /></a>
          <a><img src="purplestringwebsite/frontend/public/images/fbicon.png" /></a>
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
            <img
              src="purplestringwebsite/frontend/public/images/footer-logo.png"
              alt="Purple String Logo"
              width="100" />
          </div>

          <div id="footer-information">
            <div class="info-item">
              <img
                src="purplestringwebsite/frontend/public/images/mail.png"
                alt="Mail"
                class="footer-icon" />
              <span>purplestring@gmail.com</span>
            </div>

            <div class="info-item">
              <img
                src="purplestringwebsite/frontend/public/images/phonenum.png"
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
          src="purplestringwebsite/frontend/public/images/whiteflower.png" />
        <img
          id="homepage_bluething"
          src="purplestringwebsite/frontend/public/images/bluething.png" />
        <img
          id="homepage_heartbutton"
          src="purplestringwebsite/frontend/public/images/heartbutton.png" />
        <img
          id="homepage_greenbutton"
          src="purplestringwebsite/frontend/public/images/greenbutton.png" />
        <img
          id="homepage_greenthread"
          src="purplestringwebsite/frontend/public/images/greenthread.png" />
        <img
          id="homepage_whiteflower_2"
          src="purplestringwebsite/frontend/public/images/whiteflower.png" />
      </div>
    </div>
  </body>
</html>
