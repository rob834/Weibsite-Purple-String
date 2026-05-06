<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../../login.php");
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Products</title>
    <link rel="stylesheet" href="../../css/admin/admin-customers.css">
</head>
<body>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap');
  </style>
    <div id="admin-sidebar">
        <img src="../../public/images/admin/companylogo.png" alt="Company Logo" class="logo">
        <p>
            <a href="../admin-homepage.php"><img src="../../public/images/admin/dashboard icon.png" class="icon">Dashboard</a>
            <a href="admin-products.php"><img src="../../public/images/admin/products icon.png" class="icon">Products</a>
            <a id="toggled" href="admin-customers.php"><img src="../../public/images/admin/customers icon-toggled.png" class="icon">Customers</a>
            <a href="admin-chat.php"><img src="../../public/images/admin/chats icon.png" class="icon">Chat</a>
            <a href="admin-notification.php"><img src="../../public/images/admin/Notification bell icon.png" class="icon">Notifications</a>
        </p>
    </div>
    <div id="admin-content">

  <?php
  include_once __DIR__ . '/../../../backend/connection.php';
  $search = isset($_GET['search']) ? trim($_GET['search']) : '';
  ?>

  <form method="GET" id="upper-left-searchbar">
    <input type="text" name="search" placeholder="Search Customers..."
           value="<?= htmlspecialchars($search) ?>">
    <button type="submit">
      <img src="../../public/images/admin/search-icon.png" alt="Search Icon">
    </button>
  </form>

  <div id="upper-right-accountname">
    <img src="../../public/images/admin/account_profile.png" alt="Account Icon" class="account-icon">
    <span>Admin</span>
  </div>

  <?php
  $where = '';
  if ($search !== '') {
      $safe  = mysqli_real_escape_string($con, $search);
      $where = "AND (u.display_name LIKE '%$safe%'
                  OR u.user_name   LIKE '%$safe%'
                  OR u.phone       LIKE '%$safe%')";
  }

  $sql = "SELECT o.order_id, o.total, o.status, o.created_at,
                 u.user_name, u.display_name, u.avatar
          FROM orders o
          JOIN users u ON u.user_id = o.user_id
          WHERE 1=1 $where
          ORDER BY o.created_at DESC";

  $rows = [];
  $res  = mysqli_query($con, $sql);
  if ($res) {
      while ($r = mysqli_fetch_assoc($res)) $rows[] = $r;
  }

  $badge_map = [
      'pending'    => 'paid-yellow',
      'paid'       => 'paid-green',
      'delivering' => 'delivering',
      'completed'  => 'completed',
      'cancelled'  => 'cancelled',
  ];
  $label_map = [
      'pending'    => 'Pending',
      'paid'       => 'Paid',
      'delivering' => 'Delivering',
      'completed'  => 'Completed',
      'cancelled'  => 'Cancelled',
  ];
  ?>

  <div class="table-container">
    <div class="table-header">
      <div class="col-icon"></div>
      <div class="col-customer">Customer</div>
      <div class="col-status">Status</div>
      <div class="col-total">Total</div>
      <div class="col-date">Date</div>
    </div>

    <?php if (empty($rows)): ?>
      <p style="padding:1rem 1.5rem;">No orders found.</p>
    <?php endif; ?>

    <?php foreach ($rows as $row):
      // display_name preferred, fallback to user_name
      $name       = htmlspecialchars(($row['display_name'] ?? '') ?: $row['user_name']);
      $avatar_src = !empty($row['avatar'])
          ? '../../public/images/avatars/' . htmlspecialchars($row['avatar'])
          : '../../public/images/admin/account_profile.png';
      $badge = $badge_map[$row['status']] ?? 'paid-yellow';
      $label = $label_map[$row['status']] ?? ucfirst($row['status']);
      $date  = date('M j', strtotime($row['created_at']));
    ?>
    <div class="order-row">
      <div class="col-icon">
        <form method="POST" action="../../../backend/delete_order.php"
              onsubmit="return confirm('Delete order #<?= $row['order_id'] ?>?')">
          <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
          <button type="submit" style="background:none;border:none;cursor:pointer;">
            <img src="../../public/images/admin/delete-btn.png" alt="Delete">
          </button>
        </form>
      </div>
      <div class="col-customer">
        <div class="user-avatar">
          <img src="<?= $avatar_src ?>" alt="Avatar">
        </div>
        <?= $name ?>
      </div>
      <div class="col-status">
        <span class="badge <?= $badge ?>"><?= $label ?></span>
      </div>
      <div class="col-total">₱<?= number_format($row['total'], 2) ?></div>
      <div class="col-date"><?= $date ?></div>
    </div>
    <?php endforeach; ?>
  </div>

</div>
</body>
</html>