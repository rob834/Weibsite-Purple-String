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
      href="/purplestringwebsite/frontend/css/profile-PurchasesTabs/Returned-profile-purchases.css" />
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
              <img src="/purplestringwebsite/frontend/public/images/search.png" />
            </label>
            <input
              type="text"
              name="search"
              id="searchbar" />
          </div>
        </div>

        <div id="centerheader">
          <div id="logo">
            <img src="/purplestringwebsite/frontend/public/images/Logo.png" />
          </div>
        </div>

        <div id="rightheader">
          <div id="shoppingcart">
            <a href="../pages/cart.html"
              ><img src="/purplestringwebsite/frontend/public/images/shopping cart.png"
            /></a>
          </div>
          <div id="account-circle">
            <a href="../pages/profile.html"
              ><img src="/purplestringwebsite/frontend/public/images/profile icon.png"
            /></a>
          </div>
        </div>

        <div id="menubar">
          <a
            href="../pages/homepage.html"
            class="menubutton"
            >Home</a
          >
          <a
            href="../pages/products.html"
            class="menubutton"
            >Products</a
          >
          <a
            href="../pages/contacts.html"
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
                <a href="/purplestringwebsite/frontend/pages/profilePurchases.html"><button class="tab">All</button></a>
                <a href="/purplestringwebsite/frontend/pages/profile-PurchasesTabs/Processing-profile-Purchases.html"><button class="tab">Processing</button></a>
                <a href="/purplestringwebsite/frontend/pages/profile-PurchasesTabs/Shipping-profile-Purchases.html"><button class="tab">Shipping</button></a>
                <a href="/purplestringwebsite/frontend/pages/profile-PurchasesTabs/ToReceive-profile-Purchases.html"><button class="tab">To Receive</button></a>
                <a href="/purplestringwebsite/frontend/pages/profile-PurchasesTabs/Completed-profile-Purchases.html"><button class="tab">Completed</button></a>
                <a href="/purplestringwebsite/frontend/pages/profile-PurchasesTabs/Returned-profile-purchases.html"><button class="tab active">Returned</button></a>
                </div>

                <div class="emptyContent">
                <img src="/purplestringwebsite/frontend/public/images/No orders received yet.png" alt="no orders received">
                <p>No Orders Returned Yet</p>
                </div>


            </div>
          </div>

           <div class="right-panel">
            <div class="profile-card right-card">
              <div class="account-menu">
                <div class="menu-item">
                  <span class="menu-icon"><img src="/purplestringwebsite/frontend/public/images/myaccount updated.png" alt="profile icon"></span>
                  <a href="/purplestringwebsite/frontend/pages/profile.html" class="menu-link"><p>My Account</p></a>
                </div>
                <div class="menu-item">
                  <span class="menu-icon"><img src="/purplestringwebsite/frontend/public/images/purchases icon updated.png" alt="purchases"></span>
                  <a href="/purplestringwebsite/frontend/pages/profilePurchases.html" class="menu-link"><p>Purchases</p></a>
                </div>
                <div class="menu-item">
                  <span class="menu-icon"><img src="/purplestringwebsite/frontend/public/images/notif icon updated.png" alt="notif"></span>
                  <a href="#" class="menu-link"><p>Notification</p></a>
                </div>
                <div class="menu-item">
                  <span class="menu-icon"></span>
                  <a href="/purplestringwebsite/frontend/index.html" class="menu-link"><p>Log Out</p></a>
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
