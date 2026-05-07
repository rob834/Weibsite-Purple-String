<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../../login.php");
  exit();
}

include_once __DIR__ . '/../../backend/connection.php';
$con = get_db_connection();
$user = null;
if ($con) {
  $uid = intval($_SESSION['user_id']);
  $q = mysqli_prepare($con, "SELECT user_id, user_name, display_name, bio, phone, address, avatar FROM users WHERE user_id = ? LIMIT 1");
  mysqli_stmt_bind_param($q, 's', $uid);
  mysqli_stmt_execute($q);
  $res = mysqli_stmt_get_result($q);
  if ($res && mysqli_num_rows($res)>0) $user = mysqli_fetch_assoc($res);
  mysqli_stmt_close($q);
}

$avatar_src = '../public/images/profile icon.png';
if (!empty($user['avatar'])) {
  $path = __DIR__ . '/../public/images/avatars/' . $user['avatar'];
  if (file_exists($path)) $avatar_src = '../public/images/avatars/' . $user['avatar'];
}

?>

?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0" />
    <title>Profile</title>
    <link rel="stylesheet" href="../css/profile.css" />
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
            <a href="../pages/cart.php"><img src="../public/images/shopping cart.png" /></a>
          </div>
          <div id="account-circle">
            <a href="profile.php"><img src="<?= $avatar_src ?>" alt="profile" /></a>
            
            
          </div>
        </div>

       <div id="menubar">
          <button><a
            href="index.php"
            class="menubutton">Home</a></button>
          <button><a
            href="purplestringwebsite/frontend/pages/products.php"
            class="menubutton"
            >Proddwhaaoiiwodutietects</a
          ></button>
      
        </div>

        <div id="frills">
          <img src="../public/images/vectors/frills.png" />
        </div>
      </section>

<!-- content -->

      <section id="content">
        <div class="profile-grid">
          <div class="left-cards">
            <div
              class="profile-card"
              id="card-1">

              <div class="avatar-section">
                 <div class="pfpf">

                 <!-- pfp -->
                                   <?php
                   $avatar_src = '../public/images/profile icon.png';
                   if (!empty($user['avatar'])) {
                     $path = __DIR__ . '/../public/images/avatars/' . $user['avatar'];
                     if (file_exists($path)) {
                       $avatar_src = '../public/images/avatars/' . $user['avatar'];
                     }
                   }
                 ?>
                  <img src="<?= $avatar_src ?>" alt="profile" class="avatar-img">

                  <!-- pfp -->
                   
                 </div>
                 <div class="editbtn">
                 <button class="edit-btn"><a href="profileEditMode.php"><img src="../public/images/edit profile icon.png" alt="edit">Edit Profile</a></button>
                  </div>
                </div>

             <div class="info-section">
              <div class="row">
                <div class="label">Name</div>
                  <div class="value name"><h1><?= htmlspecialchars($user['display_name'] ?? ($user['user_name'] ?? '')) ?></h1></div>
              </div>

              <div class="row">
              <div class="label">Username</div>
                <div class="value username"><strong><?= htmlspecialchars($user['user_name'] ?? '') ?></strong></div>
              </div>

             
             <div class="row bio">
                <div class="label">Bio</div>
                <div class="value"><?= nl2br(htmlspecialchars($user['bio'] ?? '')) ?></div>
              </div>

              <div class="row">
                <div class="label">Phone Number</div>
                <div class="value"><?= htmlspecialchars($user['phone'] ?? '') ?></div>
              </div>

              <div class="row">
                <div class="label">Address</div>
                <div class="value"><?= nl2br(htmlspecialchars($user['address'] ?? '')) ?></div>
              </div>
         </div>
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
  </body>
</html>