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
              ><img src="purplestringwebsite/frontend/public/images/profile icon.png"
            /></a>
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
                <img
                  class="productslide"
                  src="purplestringwebsite/frontend/public/images/carousel pic 1.png" />
                <img
                  class="productslide"
                  src="purplestringwebsite/frontend/public/images/carousel pic 2.png" />
                <img
                  class="productslide"
                  src="purplestringwebsite/frontend/public/images/carousel pic 3.png" />
                <img
                  class="productslide"
                  src="purplestringwebsite/frontend/public/images/carousel pic 4.png" />
                <img
                  class="productslide"
                  src="purplestringwebsite/frontend/public/images/carousel pic 5.png" />
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
              <div id="custom-crochet">
                <div id="crochet-button">
                  <a>
                    <img src="purplestringwebsite/frontend/public/images/hover imgs/custom-crochet.png" />
                  </a>
                </div>
                <div id="extra-crochet">
                  <img
                    src="purplestringwebsite/frontend/public/images/hover imgs/custom-crochet-hover.png" />
                </div>
              </div>
              <div id="custom-miscellaneous">
                <div id="miscellaneous-button">
                  <a>
                    <img
                      src="purplestringwebsite/frontend/public/images/hover imgs/custom-miscellaneous.png" />
                  </a>
                </div>
                <div id="extra-miscellaneous">
                  <img
                    src="purplestringwebsite/frontend/public/images/hover imgs/custom-miscellaneous-hover.png" />
                </div>
              </div>
              <div id="custom-print">
                <div id="print-button">
                  <a>
                    <img src="purplestringwebsite/frontend/public/images/hover imgs/custom-prints.png" />
                  </a>
                </div>
                <div id="extra-print">
                  <img
                    src="purplestringwebsite/frontend/public/images/hover imgs/custom-prints-hover.png" />
                </div>
              </div>
            </div>
          </div>
        </div>

        <div id="viewallprod">
          <div id="viewallprod-button">
            <a>
              <img
                id="viewallprod-base"
                src="purplestringwebsite/frontend/public/images/hover imgs/viewallprod.png"
                alt="View All Products" />
            </a>
          </div>
          <img
            id="viewallprod-hover"
            src="purplestringwebsite/frontend/public/images/hover imgs/viewallprod-hover.png"
            alt="View All Products Hover" />
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
