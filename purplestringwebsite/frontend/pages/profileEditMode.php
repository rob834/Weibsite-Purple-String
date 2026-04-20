<?php
session_start();

if (!isset($_SESSION['user_id'])) {
        header("Location: /Weibsite-Purple-String/login.php");
    exit();
}

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0" />
    <title>Profile</title>
    <link
      rel="stylesheet"
      href="/purplestringwebsite/frontend/css/profileEditMode.css" />
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
            <a href="../pages/cart.php"
              ><img src="../public/images/shopping cart.png"
            /></a>
          </div>
          <div id="account-circle">
            <a href="../pages/profile.php"
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
<!--content-->
      <section id="content">
        <div class="profile-grid">
          <div class="left-cards">
            <div
              class="profile-card"
              id="card-1">

              <div class="avatar-section">
                 <div class="pfpf">
                  <img src="https://cdn.pixabay.com/photo/2023/02/18/11/00/icon-7797704_640.png" alt="profile" class="avatar-img">
                 </div>
                 <input type="file" id="UploadBtn">
                 <div class="uploadbtn">
                 <label for="UploadBtn">Set Profile Picture</label>
                 </div>
              </div>

             <div class="info-section">
              <div class="row">
                <div class="label">Name</div>
                <form>
              <div class="form-group">
                <label for="displayname"></label>
                <input type="text" id="displayname" name="displayname" placeholder="Enter your name" required />
              </div>
              </form>
              </div>

              <div class="row">
              <div class="label">Username</div>
               <form>
              <div class="form-group">
                <label for="username"></label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required />
              </div>
              </form>
              </div>

             
             <div class="row bio">
                <div class="label">Bio</div>
                 <form>
              <div class="form-group">
                <label for="bio"></label>
                <input type="text" id="bio" name="bio" placeholder="Something about you" required />
              </div>
              </form>
              </div>

              <div class="row">
                <div class="label">Phone Number</div>

                <form>
              <div class="form-group">
                <label for="phonenumber"></label>
                <input type="number" id="phonenumber" name="phonenumber" placeholder="Phone number" required />
              </div>
              </form>
              </div>

              <div class="row">
                <div class="label">Address</div>
                <form>
              <div class="form-group">
                <label for="address"></label>
                <input type="text" id="address" name="address" placeholder="Address..." required />
              </div>
              </form>
              </div>
         </div>
            </div>
          </div>

           <div class="right-panel">
            <div class="profile-card right-card">
              <div class="account-menu">
                <div class="menu-item">
                  <span class="menu-icon"><img src="/purplestringwebsite/frontend/public/images/myaccount updated.png" alt="profile icon"></span>
                  <a href="/purplestringwebsite/frontend/pages/profile.php" class="menu-link"><p>My Account</p></a>
                </div>
                <div class="menu-item">
                  <span class="menu-icon"><img src="/purplestringwebsite/frontend/public/images/purchases icon updated.png" alt="purchases"></span>
                  <a href="/purplestringwebsite/frontend/pages/profilePurchases.php" class="menu-link"><p>Purchases</p></a>
                </div>
                <div class="menu-item">
                  <span class="menu-icon"><img src="/purplestringwebsite/frontend/public/images/notif icon updated.png" alt="notif"></span>
                  <a href="#" class="menu-link"><p>Notification</p></a>
                </div>
                <div class="menu-item">
                  <span class="menu-icon"></span>
                  <a href="/Weibsite-Purple-String/logout.php" class="menu-link"><p>Log Out</p></a>
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
            <img
              src="/purplestringwebsite/frontend/public/images/footer-logo.png"
              alt="Purple String Logo"
              width="100" />
          </div>

          <div id="footer-information">
            <div class="info-item">
              <img
                src="/purplestringwebsite/frontend/public/images/mail icon.png"
                alt="Mail"
                class="footer-icon" />
              <span>purplestring@gmail.com</span>
            </div>

            <div class="info-item">
              <img
                src="/purplestringwebsite/frontend/public/images/phonenum.png"
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
          src="/purplestringwebsite/frontend/public/images/whiteflower.png" />
        <img
          id="homepage_bluething"
          src="/purplestringwebsite/frontend/public/images/bluething.png" />
        <img
          id="homepage_heartbutton"
          src="/purplestringwebsite/frontend/public/images/heartbutton.png" />
        <img
          id="homepage_greenbutton"
          src="/purplestringwebsite/frontend/public/images/greenbutton.png"/>
          
        <img
          id="homepage_greenthread"
          src="/purplestringwebsite/frontend/public/images/greenthread.png" />
        <img
          id="homepage_whiteflower_2"
          src="/purplestringwebsite/frontend/public/images/whiteflower.png" />
      </div>
    </div>
    <script src="../js/profile.js"></script>
  </body>
</html>
