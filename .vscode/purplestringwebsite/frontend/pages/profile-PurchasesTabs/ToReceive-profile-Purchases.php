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
    <link rel="stylesheet" href="../css/profile-PurchasesTabs/ToReceive-profile-Purchases.css" />
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
          if (!isset($con)) { include_once __DIR__ . '/../../../backend/connection.php'; $con = function_exists('get_db_connection') ? get_db_connection() : null; }
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
            <a href="../pages/cart.php"><img src="../../public/images/shopping cart.png" /></a>
          </div>
          <div id="account-circle">
            <a href="../pages/profile.php"><img src="<?= $avatar_src ?>" alt="profile" /></a>
          </div>
        </div>

        <div id="menubar">
          <a
            href="../pages/homepage.php"
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
<!--content-->
      <section id="content">
        <div class="profile-grid">
          <div class="left-cards">
            <div
              class="profile-card"
              id="card-1">
<!--menu-->

                <div class="order-menu">
                      <a href="../profilePurchases.php"><button class="tab">All</button></a>
                      <a href="Processing-profile-Purchases.php"><button class="tab">Processing</button></a>
                      <a href="Shipping-profile-Purchases.php"><button class="tab">Shipping</button></a>
                      <a href="ToReceive-profile-Purchases.php"><button class="tab active">To Receive</button></a>
                      <a href="Completed-profile-Purchases.php"><button class="tab">Completed</button></a>
                      <a href="Returned-profile-purchases.php"><button class="tab">Returned</button></a>
                </div>

                <div class="emptyContent">
                <img src="../public/images/No orders received yet.png" alt="no orders received">
                <p>No Orders Received Yet</p>
                </div>


            </div>
          </div>

           <div class="right-panel">
            <div class="profile-card right-card">
              <div class="account-menu">
                <div class="menu-item">
                  <span class="menu-icon"><img src="../public/images/myaccount updated.png" alt="profile icon"></span>
                  <a href="../profile.php" class="menu-link"><p>My Account</p></a>
                </div>
                <div class="menu-item">
                  <span class="menu-icon"><img src="../public/images/purchases icon updated.png" alt="purchases"></span>
                  <a href="../profilePurchases.php" class="menu-link"><p>Purchases</p></a>
                </div>
                <div class="menu-item">
                  <span class="menu-icon"><img src="../public/images/notif icon updated.png" alt="notif"></span>
                  <a href="#" class="menu-link"><p>Notification</p></a>
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
  </body>
</html>
