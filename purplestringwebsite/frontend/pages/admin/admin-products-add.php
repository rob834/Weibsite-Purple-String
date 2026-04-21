<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header("Location: /Weibsite-Purple-String/login.php");
    exit();
}

?>


<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0"/>
    <title>Admin Products</title>
    <link
      rel="stylesheet"
      href="../../css/admin/admin-products-add.css"/>
  </head>
  <body>
    <link
      rel="preconnect"
      href="https://fonts.googleapis.com"/>
    <link
      rel="preconnect"
      href="https://fonts.gstatic.com"crossorigin/>
    <link
      href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,500;1,500&display=swap"
      rel="stylesheet"/>

    <div id="admin-sidebar">
      <img
        src="../../public/images/admin/companylogo.png"
        alt="Company Logo"
        class="logo"/>
      <p>
        <a href="../admin-homepage.php">
          <img src="../../public/images/admin/dashboard icon.png"
            class="icon"/>Dashboard</a>
        <a href="admin-products.php">
          <img src="../../public/images/admin/products icon.png"
            class="icon" />Products</a>
        <a href="admin-customers.php">
          <img src="../../public/images/admin/customers icon.png"
            class="icon" />Customers</a>
        <a href="admin-chat.php">
          <img src="../../public/images/admin/chats icon.png"
            class="icon" />Chat</a>
        <a href="admin-notification.php">
          <img src="../../public/images/admin/Notification bell icon.png"
            class="icon" />Notifications</a>
      </p>
    </div>
    <div id="admin-content">
      <div id="upper-right-accountname">
        <img
          src="../../public/images/admin/account_profile.png"
          alt="Account Icon"
          class="account-icon" />
        <span>Seller Name</span>
      </div>

      <div id="add-product-card">
        <form id="add-product-form" method="POST" action="../../../backend/add_product.php" enctype="multipart/form-data">
          <div class="left-section">
            <h2>Add New Product</h2>

            <div class="dropbox-container">
              <label for="images">Upload Images</label>
              <input
                type="file"
                id="images"
                name="images[]"
                accept="image/*"
                multiple
                class="dropbox"/>
            </div>
            <small>Add up to 7 images (JPG, PNG)</small>

            <div class="dropbox-container">
              <label for="video">Upload Video (optional)</label>
              <input
                type="file"
                id="video"
                name="video"
                accept="video/mp4"
                class="dropbox"/>
            </div>
            <small>Max size: 30MB | Format: mp4</small>

            <button type="submit" class="save-btn">Save Product</button>
            <a href="admin-products.php" class="cancel-link">Cancel</a>
          </div>

          <div class="right-section">
            <label for="name">Product Name:</label>
            <input type="text" id="name" name="name" required />

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="6" required></textarea>

            <label for="price">Price:</label>
            <input type="number" id="price" name="price" step="0.01" required />

            <label for="stock">Stock:</label>
            <input type="number" id="stock" name="stock" value="0" />

            <label for="category">Category:</label>
            <input type="text" id="category" name="category" />
          </div>
        </form>
      </div>
    </div>
    <script src="../../js/admin/admin-products-edit.js"></script>
  </body>
</html>
