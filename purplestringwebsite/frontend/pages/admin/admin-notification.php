<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header("Location: /Weibsite-Purple-String/login.php");
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,500;1,500&display=swap" rel="stylesheet">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Notifications</title>
    <link rel="stylesheet" href="/purplestringwebsite/frontend/css/admin/admin-notification.css">
</head>
<body>
 
    <div id="admin-sidebar">
        <img src="../../public/images/admin/companylogo.png" alt="Company Logo" class="logo">
        <p>
            <a href="../admin-homepage.php"><img src="../../public/images/admin/dashboard icon.png" class="icon">Dashboard</a>
            <a href="admin-products.php"><img src="../../public/images/admin/products icon.png" class="icon">Products</a>
            <a href="admin-customers.php"><img src="../../public/images/admin/customers icon.png" class="icon">Customers</a>
            <a href="admin-chat.php"><img src="../../public/images/admin/chats icon.png" class="icon">Chat</a>
            <a id="toggled" href="admin-notification.php"><img src="../../public/images/admin/Notification bell icon-toggled.png" class="icon"><b>Notifications</b></a>
        </p>
    </div>
    <div id="admin-content">
        <div id="upper-right-accountname">
            <img src="../../public/images/admin/account_profile.png" alt="Account Icon" class="account-icon">
            <span>Seller Name</span>
        </div>
     

        <div class="content">

    <div class="notif-header">
       <div><h1>Notifications (3)</h1></div>
        <div><input type="text" id="search-bar" placeholder="Search for products"></div>
     </div>
        <hr>

        <div class="notif1">
            <label class="container">
                <input type="checkbox" checked="checked">
                <span class="checkmark"></span>
            </label>
            <div class="notif1-content">
                <div class="notif1-header">
                    <h2 class="notif1-title">New Order</h2>
                    <div class="notif1-meta">
                        <img src="/purplestringwebsite/frontend/public/images/delete message icon.png" alt="delete icon" class="delete-icon">
                        <div class="notif-meta-datetime">
                            <p class="notif-date">Mar. 15, 2023</p>
                            <p class="notif-time">10:30 AM</p>
                        </div>
                    </div>
                </div>
                <p class="notif-text">Lorem ipsum dolor sit, amet consectetur adipisicing elit. Sunt eum asperiores ab, quia non perferendis praesentium iusto, reprehenderit animi culpa esse saepe dolores ipsa. Recusandae, doloremque. Aut doloremque doloribus vero.</p>
            </div>
        </div>

        <div class="notif1">
            <label class="container">
                <input type="checkbox" checked="checked">
                <span class="checkmark"></span>
            </label>
            <div class="notif1-content">
                <div class="notif1-header">
                    <h2 class="notif1-title">Package Sent</h2>
                    <div class="notif1-meta">
                        <img src="/purplestringwebsite/frontend/public/images/delete message icon.png" alt="delete icon" class="delete-icon">
                        <div class="notif-meta-datetime">
                            <p class="notif-date">Mar. 1, 2025</p>
                            <p class="notif-time">6:09 AM</p>
                        </div>
                    </div>
                </div>
                <p class="notif-text">Lorem ipsum dolor sit, amet consectetur adipisicing elit. Sunt eum asperiores ab, quia non perferendis praesentium iusto, reprehenderit animi culpa esse saepe dolores ipsa. Recusandae, doloremque. Aut doloremque doloribus vero.</p>
            </div>
        </div>

        <div class="notif1">
            <label class="container">
                <input type="checkbox" checked="checked">
                <span class="checkmark"></span>
            </label>
            <div class="notif1-content">
                <div class="notif1-header">
                    <h2 class="notif1-title">Order Complete</h2>
                    <div class="notif1-meta">
                        <img src="/purplestringwebsite/frontend/public/images/delete message icon.png" alt="delete icon" class="delete-icon">
                        <div class="notif-meta-datetime">
                            <p class="notif-date">Mar. 27, 2025</p>
                            <p class="notif-time">9:09 AM</p>
                        </div>
                    </div>
                </div>
                <p class="notif-text">Lorem ipsum dolor sit, amet consectetur adipisicing elit. Sunt eum asperiores ab, quia non perferendis praesentium iusto, reprehenderit animi culpa esse saepe dolores ipsa. Recusandae, doloremque. Aut doloremque doloribus vero.</p>
            </div>
        </div>

        <div class="notif1">
            <label class="container">
                <input type="checkbox" checked="checked">
                <span class="checkmark"></span>
            </label>
            <div class="notif1-content">
                <div class="notif1-header">
                    <h2 class="notif1-title">New Chat Messages</h2>
                    <div class="notif1-meta">
                        <img src="/purplestringwebsite/frontend/public/images/delete message icon.png" alt="delete icon" class="delete-icon">
                        <div class="notif-meta-datetime">
                            <p class="notif-date">Mar. 4, 2025</p>
                            <p class="notif-time">6:07 AM</p>
                        </div>
                    </div>
                </div>
                <p class="notif-text">Lorem ipsum dolor sit, amet consectetur adipisicing elit. Sunt eum asperiores ab, quia non perferendis praesentium iusto, reprehenderit animi culpa esse saepe dolores ipsa. Recusandae, doloremque. Aut doloremque doloribus vero.</p>
            </div>
        </div>

        <button id="delete-all">Delete Selected</button>
        <button id="mark-all-as-read">Mark All as Read</button>
        
</body>
</html>