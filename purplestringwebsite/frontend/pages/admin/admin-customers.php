<?php
session_start();

if (!isset($_SESSION['user_id'])) {
        header("Location: /Weibsite-Purple-String/login.php");
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
        <div id="upper-left-searchbar">
            <input type="text" placeholder="Search Customers...">
            <button><img src="../../public/images/admin/search-icon.png" alt="Search Icon"></button>
        </div>
        <div id="upper-right-accountname">
            <img src="../../public/images/admin/account_profile.png" alt="Account Icon" class="account-icon">
            <span>Seller Name</span>
        </div>
        
        <div class="table-container">
    <div class="table-header">
        <div class="col-icon"></div>
        <div class="col-customer">Customer</div>
        <div class="col-status">Status</div>
        <div class="col-total">Total</div>
        <div class="col-date">Date</div>
    </div>

    <div class="order-row">
        <div class="col-icon"><span class="delete-btn"><img src="../../public/images/admin/delete-btn.png" alt="Delete Icon"></span></div>
        <div class="col-customer">
            <div class="user-avatar"><img src="../../public/images/admin/account_profile.png" alt="User Avatar"></div>
            Ryan Gossling Santos
        </div>
        <div class="col-status"><span class="badge paid-yellow">Paid</span></div>
        <div class="col-total">₱1,000.00</div>
        <div class="col-date">Mar 18</div>
    </div>

    <div class="order-row">
        <div class="col-icon"><span class="delete-btn"><img src="../../public/images/admin/delete-btn.png" alt="Delete Icon"></span></div>
        <div class="col-customer">
            <div class="user-avatar"><img src="../../public/images/admin/account_profile.png" alt="User Avatar"></div>
            Ryan Gossling Santos
        </div>
        <div class="col-status"><span class="badge completed">Completed</span></div>
        <div class="col-total">₱1,000.00</div>
        <div class="col-date">Mar 18</div>
    </div>

    <div class="order-row">
        <div class="col-icon"><span class="delete-btn"><img src="../../public/images/admin/delete-btn.png" alt="Delete Icon"></span></div>
        <div class="col-customer">
            <div class="user-avatar"><img src="../../public/images/admin/account_profile.png" alt="User Avatar"></div>
            Ryan Gossling Santos
        </div>
        <div class="col-status"><span class="badge delivering">Delivering</span></div>
        <div class="col-total">₱1,000.00</div>
        <div class="col-date">Mar 18</div>
    </div>

    <div class="order-row">
        <div class="col-icon"><span class="delete-btn"><img src="../../public/images/admin/delete-btn.png" alt="Delete Icon"></span></div>
        <div class="col-customer">
            <div class="user-avatar"><img src="../../public/images/admin/account_profile.png" alt="User Avatar"></div>
            Ryan Gossling Santos
        </div>
        <div class="col-status"><span class="badge cancelled">Cancelled Order</span></div>
        <div class="col-total">₱1,000.00</div>
        <div class="col-date">Mar 18</div>
    </div>

    <div class="order-row">
        <div class="col-icon"><span class="delete-btn"><img src="../../public/images/admin/delete-btn.png" alt="Delete Icon"></span></div>
        <div class="col-customer">
            <div class="user-avatar"><img src="../../public/images/admin/account_profile.png" alt="User Avatar"></div>
            Ryan Gossling Santos
        </div>
        <div class="col-status"><span class="badge paid-green">Paid</span></div>
        <div class="col-total">₱1,000.00</div>
        <div class="col-date">Mar 18</div>
    </div>
</div>
    </div>
</body>
</html>