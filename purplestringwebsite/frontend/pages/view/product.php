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

        <div id="rightheader">
          <div id="shoppingcart">
            <a href="../cart.php"
              ><img src="../../public/images/shopping cart.png"
            /></a>
          </div>
          <div id="account-circle">
            <a href="../profile.php"
              ><img src="../../public/images/profile icon.png"
            /></a>
          </div>
        </div>

        <div id="menubar">
          <a
            href="../homepage.php"
            class="menubutton"
            >Home</a
          >
          <a
            href="../products.php"
            class="menubutton"
            >Products</a
          >
          <a
            href="../contacts.php"
            class="menubutton"
            >Contacts</a
          >
        </div>

        <div id="frills">
          <img src="../../public/images/vectors/frills.png" />
        </div>
      </section>
      <section id="content">
        <div class="product-card">
          <!-- Left: 5-image collage -->
          <div class="collage-left">
            <img src="../../public/images/product image.png" alt="Coillage main" />
            <img src="../../public/images/product image.png" alt="Coillage 2" />
            <img src="../../public/images/product image.png" alt="Coillage 3" />
            <img src="../../public/images/product image.png" alt="Coillage 4" />
            <img src="../../public/images/product image.png" alt="Coillage 5" />
          </div>

          <!-- Right: Product information -->
          <div class="product-info-right">
            <div class="product-header-row">
              <h2 class="product-title">Flyers/brochure Trifold Printing Glossy</h2>
              <div class="rating">
                <span class="stars">★★★★★</span>
                <span class="score">4.8</span>
                <span class="sold">• 1.2k sold</span>
              </div>
              <div class="prices">
                <div class="current-price">₱17-₱22</div>
              </div>
            </div>

            <div class="actions">
              <button class="btn-add-to-cart">Add to Cart</button>
              <button class="btn-buy-now">Buy Now</button>
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
    // Redirect 'Buy Now' button to cart page
    (function(){
      var buyBtn = document.querySelector('.btn-buy-now');
      if(buyBtn){
        buyBtn.addEventListener('click', function(){
          // relative path from this file to the cart page
          window.location.href = '../cart.php';
        });
      }
    })();
  </script>
  </body>
</html>