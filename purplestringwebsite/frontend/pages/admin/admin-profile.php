<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header("Location: ../../../login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,500;1,500&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.0"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link rel="stylesheet" href="../../../frontend/css/admin/admin-profile.css">
</head>

<body>
<!-- SIDE BAR -->
    <div id="admin-sidebar">
        <img src="../../public/images/admin/companylogo.png" alt="Company Logo" class="logo">
        <p>
            <a href="../admin-homepage.php"><img src="../../public/images/admin/dashboard icon.png" class="icon">Dashboard</a>
            <a href="../admin/admin-products.php"><img src="../../public/images/admin/products icon.png" class="icon">Products</a>
            <a href="../admin/admin-customers.php"><img src="../../public/images/admin/customers icon.png" class="icon">Customers</a>
            <a href="../admin/admin-notification.php"><img src="../../public/images/admin/Notification bell icon.png" class="icon">Notifications</a>
        </p>
    </div>

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
                   $avatar_src = '../../public/images/profile icon.png';
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
                 <button class="edit-btn"><a href="Admin-profileEditMode.php"><img src="../../public/images/edit profile icon.png" alt="edit">Edit Profile</a></button>
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
                  <span class="menu-icon"><img src="../../public/images/myaccount updated.png" alt="profile icon"></span>
                  <a href="admin-profile.php" class="menu-link">My Account</a>
                </div>
                <div class="menu-item">
                  <span class="menu-icon"><img src="../../public/images/admin/qr code icon.png" alt="qr code" class="menu-icon-qr"></span>
                  <a href="--" class="menu-link">QR Code</a>
                </div>

                <div class="menu-item">
                  <span class="menu-icon"></span>
                  <a href="../../../../logout.php" class="menu-link"><strong>Log Out</strong></a>
                </div>
              </div>
            </div>
          </div>
      </section>

</body>
</html>