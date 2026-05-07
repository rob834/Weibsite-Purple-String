<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../../../../login.php");
    exit();
}

?>


<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0" />
    <title>Admin Products</title>
    <link
      rel="stylesheet"
      href="../../css/admin/admin-products-edit.css" />
  </head>
  <body>
    <link
      rel="preconnect"
      href="https://fonts.googleapis.com" />
    <link
      rel="preconnect"
      href="https://fonts.gstatic.com"
      crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,500;1,500&display=swap"
      rel="stylesheet" />

  <?php
  include_once __DIR__ . '/../../../backend/connection.php';
  if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }

  $product = null;
  $product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
  if ($product_id > 0) {
    $pstmt = mysqli_prepare($con, "SELECT p.*, c.name AS category_name, pi.file_name AS image_file FROM products p LEFT JOIN categories c ON p.category_id = c.category_id LEFT JOIN product_images pi ON pi.product_id = p.product_id AND pi.is_primary = 1 WHERE p.product_id = ? LIMIT 1");
    mysqli_stmt_bind_param($pstmt, 'i', $product_id);
    mysqli_stmt_execute($pstmt);
    $res = mysqli_stmt_get_result($pstmt);
    if ($res) {
      $product = mysqli_fetch_assoc($res);
    }
    mysqli_stmt_close($pstmt);
  }
  ?>

    <div id="admin-sidebar">
      <img
        src="../../public/images/admin/companylogo.png"
        alt="Company Logo"
        class="logo" />
      <p>
        <a href="../admin-homepage.php">
          ><img
            src="../../public/images/admin/dashboard icon.png"
            class="icon" />Dashboard</a
        >
        <a href="admin-products.php">
           <img src="../../public/images/admin/products icon.png"
             class="icon" />Products</a
          >
        >
        <a href="admin-customers.php">
           <img src="../../public/images/admin/customers icon.png"
             class="icon" />Customers</a
          >
        >
        <a href="admin-chat.php"
          ><img
            src="../../public/images/admin/chats icon.png"
            class="icon" />Chat</a
        >
        <a href="admin-notification.php"
          ><img
            src="../../public/images/admin/Notification bell icon.png"
            class="icon" />Notifications</a
        >
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
      <div id="edit-product-card">
        <div class="left-section">
          <div class="gallery">
            <div class="main-photo">
              <img
                src="../../public/images/products/product1.jpg"
                alt="Main Product Photo" />
            </div>
            <div class="4photos-slider">
              <!-- Placeholder for 4 additional photos -->
            </div>
          </div>
          <div class="change-photo-btn">
            <button type="button">Change Photos</button>
          </div>
        </div>
        <form id="edit-product-form" method="POST" action="../../../backend/update_product.php" enctype="multipart/form-data">
          <input type="hidden" name="product_id" value="<?= $product_id ?>" />
          <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>" />
          <label for="product-name">Product Name:</label>
          <input
            type="text"
            id="product-name"
            name="name"
            value="<?= htmlspecialchars($product['name'] ?? '') ?>"
            required />
        <label for="product-price">Price:</label>
          <input
            type="number"
            id="product-price"
            name="price"
            value="<?= isset($product['price']) ? number_format($product['price'], 2, '.', '') : '' ?>"
            step="0.01"
            required />
          <label for="product-description">Description:</label>
          <textarea
            id="product-description"
            name="description"
            rows="6"
            cols="70"
            required><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
          <label for="product-stock">Stock:</label>
          <input type="number" id="product-stock" name="stock" value="<?= intval($product['stock'] ?? 0) ?>" />
          <label for="product-category">Category (name):</label>
          <input type="text" id="product-category" name="category" value="<?= htmlspecialchars($product['category_name'] ?? '') ?>" />
          <label for="product-images">Change Photos:</label>
          <input type="file" id="product-images" name="images[]" accept="image/*" multiple />
          <button type="submit">Save Changes</button>
        </form>
      </div>
      <div class="popout-card">
        <div class="change-media-card">
          <h2>Change Product Photos</h2>
          <input
            type="file"
            id="photo-upload"
            name="photo-upload"
            accept="image/*"
            multiple />
          <button
            type="button"
            id="save-photos-btn">
            Save Photos
          </button>
        </div>
      </div>
      <div class="popout-card">
        <div class="delete-confirmation-card">
          <h2>Confirm Deletion</h2>
          <p>Are you sure you want to delete this product?</p>
          <button
            type="button"
            id="confirm-delete-btn">
            Yes, Delete
          </button>
          <button
            type="button"
            id="cancel-delete-btn">
            Cancel
          </button>
        </div>
      </div>
<!-- test -->
    </div>
    <script src="../../js/admin/admin-products-edit.js"></script>
  </body>
</html>
