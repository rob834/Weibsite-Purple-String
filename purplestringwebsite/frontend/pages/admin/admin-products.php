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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Products</title>
    <link rel="stylesheet" href="../../css/admin/admin-products.css">
</head>
<body>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,500;1,500&display=swap" rel="stylesheet">

    <div id="admin-sidebar">
        <img src="../../public/images/admin/companylogo.png" alt="Company Logo" class="logo">
        <p>
            <a href="../admin-homepage.php"><img src="../../public/images/admin/dashboard icon.png" class="icon">Dashboard</a>
            <a id="toggled" href="admin-products.php"><img src="../../public/images/admin/products icon-toggled.png" class="icon">Products</a>
            <a href="admin-customers.php"><img src="../../public/images/admin/customers icon.png" class="icon">Customers</a>
            <a href="admin-chat.php"><img src="../../public/images/admin/chats icon.png" class="icon">Chat</a>
            <a href="admin-notification.php"><img src="../../public/images/admin/Notification bell icon.png" class="icon">Notifications</a>
        </p>
    </div>
    <div id="admin-content">
        <div id="upper-right-accountname">
            <img src="../../public/images/admin/account_profile.png" alt="Account Icon" class="account-icon">
            <span>Seller Name</span>
        </div>

        <div class="productsMainContent">
        <div id="admin-products-content">
            <div id="products-header">
                <div id="upper-left-header">
                    <h1 id="title">Products</h1>
                </div>
                <div id="upper-right-header">
                    <button id="add-product-btn"><a href="admin-products-add.php">+Add a new product</a></button>
                    <input type="text" id="search-bar" placeholder="Search for products">
                </div>
            </div>
            
            <div id="product-cards">
                <?php
                include_once __DIR__ . '/../../../backend/connection.php';

                $sql = "SELECT p.*, pi.file_name AS image_file
                    FROM products p
                    LEFT JOIN product_images pi ON pi.product_id = p.product_id AND pi.is_primary = 1
                    ORDER BY p.product_id DESC";

                $products = [];

                $con = get_db_connection();
                if (!$con) {
                    echo '<p>Database unavailable. Please try again later.</p>';
                } else {
                    try {
                        $res = mysqli_query($con, $sql);
                    } catch (mysqli_sql_exception $e) {
                        error_log('DB query error on admin-products: ' . $e->getMessage());
                        $res = false;
                    }

                    if ($res) {
                        while ($row = mysqli_fetch_assoc($res)) {
                            $products[] = $row;
                        }
                    }
                }

                if (empty($products)) {
                    echo '<p>No products found.</p>';
                }

                // ensure CSRF token
                if (empty($_SESSION['csrf_token'])) {
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                }

                foreach ($products as $prod):
                    $img = $prod['image_file'] ? '../../public/images/products/' . $prod['image_file'] : '../../public/images/admin/product.png';
                ?>
                <div class="product-card">
                    <div class="card-left">
                        <img src="<?= htmlspecialchars($img) ?>" alt="Product Image" class="product-image">
                        <div class="product-actions">
                            <a class="edit-btn" href="admin-products-edit.php?id=<?= $prod['product_id'] ?>">Edit</a>
                            <form class="delete-form" method="POST" action="../../../backend/delete_product.php" style="display:inline">
                                <input type="hidden" name="product_id" value="<?= $prod['product_id'] ?>" />
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>" />
                                <button type="submit" class="delete-btn">Delete</button>
                            </form>
                        </div>
                    </div>
                    <div class="card-middle">
                        <div class="card-middle-upper">
                            <h1><?= htmlspecialchars($prod['name']) ?></h1>
                        </div>
                        <div class="card-middle-lower">
                            <p><img src="../../public/images/admin/Star.png" alt="Star Icon" class="icon-product">4.6 Rating</p>
                            <p><img src="../../public/images/admin/Tag.png" alt="Sold Icon" class="icon-product"><?= intval($prod['stock']) ?> Stock</p>
                        </div>
                    </div>
                    <div class="card-right">
                        <p class="product-price">₱<?= number_format($prod['price'], 2) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <script>
            document.addEventListener('DOMContentLoaded', function(){
                function showMessage(msg){
                    // simple alert for now; could be replaced with toast UI
                    alert(msg);
                }

                document.querySelectorAll('.delete-form').forEach(function(form){
                    form.addEventListener('submit', function(e){
                        e.preventDefault();
                        if (!confirm('Delete this product?')) return;
                        var fd = new FormData(form);
                        fetch(form.action, {
                            method: 'POST',
                            body: fd,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        }).then(function(res){
                            return res.json();
                        }).then(function(data){
                            if (data && data.success) {
                                var card = form.closest('.product-card');
                                if (card) card.remove();
                                showMessage('Product deleted');
                            } else {
                                showMessage('Delete failed: ' + (data.error || 'unknown'));
                            }
                        }).catch(function(){
                            showMessage('Delete failed (network)');
                        });
                    });
                });
            });
            </script>
    </div>
</body>
</html>