<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../../login.php");
    exit();
}

// Include database connection
include_once __DIR__ . '/../../../backend/connection.php';

// Fetch all orders from the database (excluding soft deleted)
$orders_query = "SELECT o.order_id, o.user_id, o.total, o.status, o.created_at, o.is_read, o.notif_deleted,
                        u.user_name, u.display_name
                 FROM orders o
                 JOIN users u ON o.user_id = u.user_id
                 WHERE o.notif_deleted = 0
                 ORDER BY o.created_at DESC";

$orders_result = mysqli_query($con, $orders_query);
$orders = [];

if ($orders_result) {
    while ($row = mysqli_fetch_assoc($orders_result)) {
        $orders[] = $row;
    }
}

$total_orders = count($orders);

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
    <link rel="stylesheet" href="../../css/admin/admin-notification.css">
    <style>
        .notif1.notif-read {
            opacity: 0.5;
            background-color: #f5f5f5;
            pointer-events: none;
        }
    </style>
</head>
<body>
 
    <div id="admin-sidebar">
        <img src="../../public/images/admin/companylogo.png" alt="Company Logo" class="logo">
        <p>
            <a href="../admin-homepage.php"><img src="../../public/images/admin/dashboard icon.png" class="icon">Dashboard</a>
            <a href="admin-products.php"><img src="../../public/images/admin/products icon.png" class="icon">Products</a>
            <a href="admin-customers.php"><img src="../../public/images/admin/customers icon.png" class="icon">Customers</a>
            <a id="toggled" href="admin-notification.php"><img src="../../public/images/admin/Notification bell icon-toggled.png" class="icon"><b>Notifications</b></a>
        </p>
    </div>
    <div id="admin-content">
        <div id="upper-right-accountname">
            <a href="admin-profile.php" class="accountbtn">
            <img src="../../public/images/admin/account_profile.png" alt="Account Icon" class="account-icon">
            </a>
        </div>
     

        <div class="content">

    <div class="notif-header">
       <div><h1>Notifications (<?php echo $total_orders; ?>)</h1></div>
        <div><input type="text" id="search-bar" placeholder="Search for products"></div>
     </div>
        <hr>

        <?php 
        if (empty($orders)) {
            echo '<p style="text-align: center; padding: 20px; color: #999;">No notifications at this time.</p>';
        } else {
            foreach ($orders as $order) {
                $order_date = date('M. d, Y', strtotime($order['created_at']));
                $order_time = date('h:i A', strtotime($order['created_at']));
                $customer_name = htmlspecialchars($order['display_name'] ?? $order['user_name']);
                $order_id = htmlspecialchars($order['order_id']);
                $order_total = number_format($order['total'], 2);
                $order_status = htmlspecialchars(ucfirst($order['status']));
                $is_read = $order['is_read'] ?? 0;
                $read_class = $is_read ? 'notif-read' : '';
        ?>

        <div class="notif1 <?php echo $read_class; ?>" data-order-id="<?php echo $order['order_id']; ?>">
            <label class="container">
                <input type="checkbox" class="notif-checkbox" data-order-id="<?php echo $order['order_id']; ?>" checked="checked">
                <span class="checkmark"></span>
            </label>
            <div class="notif1-content">
                <div class="notif1-header">
                    <h2 class="notif1-title">Order #<?php echo $order_id; ?> - <?php echo $order_status; ?></h2>
                    <div class="notif1-meta">
                        <img src="/purplestringwebsite/frontend/public/images/delete message icon.png" alt="delete icon" class="delete-icon">
                        <div class="notif-meta-datetime">
                            <p class="notif-date"><?php echo $order_date; ?></p>
                            <p class="notif-time"><?php echo $order_time; ?></p>
                        </div>
                    </div>
                </div>
                <p class="notif-text">
                    <strong>Customer:</strong> <?php echo $customer_name; ?><br>
                    <strong>Order Total:</strong> $<?php echo $order_total; ?><br>
                    <strong>Status:</strong> <?php echo $order_status; ?>
                </p>
            </div>
        </div>

        <?php 
            }
        }
        ?>

        <button id="delete-all">Delete Selected</button>
        <button id="mark-all-as-read">Mark All as Read</button>
        </div>
    </div>

    <script>
        document.getElementById('delete-all').addEventListener('click', function() {
            const checkedBoxes = document.querySelectorAll('.notif-checkbox:checked');
            
            if (checkedBoxes.length === 0) {
                alert('Please select at least one notification to delete.');
                return;
            }
            
            if (!confirm('Are you sure you want to delete the selected notifications?')) {
                return;
            }
            
            const orderIds = Array.from(checkedBoxes).map(checkbox => checkbox.dataset.orderId);
            
            fetch('../../../backend/delete_notifications.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    order_ids: orderIds
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Notifications deleted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to delete notifications'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting notifications.');
            });
        });

        document.getElementById('mark-all-as-read').addEventListener('click', function() {
            const checkedBoxes = document.querySelectorAll('.notif-checkbox:checked');
            
            if (checkedBoxes.length === 0) {
                alert('Please select at least one notification to mark as read.');
                return;
            }
            
            const orderIds = Array.from(checkedBoxes).map(checkbox => checkbox.dataset.orderId);
            
            fetch('../../../backend/mark_read.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    order_ids: orderIds
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to mark notifications as read'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while marking notifications as read.');
            });
        });
    </script>
</body>
</html>