<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0" />
    <title>About Us</title>
    <link
      rel="stylesheet"
      href="../css/contactus.css" />
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
            <a href="cart.html"
              ><img src="../public/images/shopping cart.png"
            /></a>
          </div>
          <div id="account-circle">
            <a href="profile.html"
              ><img src="../public/images/profile icon.png"
            /></a>
          </div>
        </div>

        <div id="menubar">
          <a
            href="../index.html"
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

      <section id="content">
        <div id="Hearmeout"></div>
        <div id="contact-details"></div>
        <div id="contact-form">
          <div class="form-card">
           
            <form>
              <div class="form-group">
                <label for="fullname">Full Name</label>
                <input type="text" id="fullname" name="fullname" placeholder="Enter your full name" required />
              </div>

              <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required />
              </div>

              <div class="form-group">
                <label for="message">Your Message</label>
                <textarea id="message" name="message" placeholder="Share your thoughts or message..." rows="6" required></textarea>
              </div>
              <button type="submit" class="submit-btn">Send Message</button>
            </form>
          </div>
        </div>
      </section>

<div id="contact-subheader">
            <div class="info-item">
              <img
                src="../public/images/mail.png"
                alt="Mail"
                class="footer-icon" />
              <h3>purplestring@gmail.com</h3>
            </div>

            <div class="info-item">
              <img
                src="../public/images/phonenum.png"
                alt="Phone"
                class="footer-icon" />
              <h3>+63 900 123 4567</h3>
            </div>
          </div>  

<div id="contact-header">
  <h1>We'd like to <br> hear from <br> you!</h1>
</div>

<!--design flowers-->
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

<!--footer-->
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
  </body>
</html>
