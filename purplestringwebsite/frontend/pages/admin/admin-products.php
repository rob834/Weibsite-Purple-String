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
            <a href="../admin-homepage.html"><img src="../../public/images/admin/dashboard icon.png" class="icon">Dashboard</a>
            <a id="toggled" href="admin-products.html"><img src="../../public/images/admin/products icon-toggled.png" class="icon">Products</a>
            <a href="admin-customers.html"><img src="../../public/images/admin/customers icon.png" class="icon">Customers</a>
            <a href="admin-chat.html"><img src="../../public/images/admin/chats icon.png" class="icon">Chat</a>
            <a href="admin-notification.html"><img src="../../public/images/admin/Notification bell icon.png" class="icon">Notifications</a>
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
                    <button id="add-product-btn"><a href="admin-products-add.html">+Add a new product</a></button>
                    <input type="text" id="search-bar" placeholder="Search for products">
                </div>
            </div>
            
            <div id="product-cards">
                <div class="product-card">
                    <div class="card-left">
                        <img src="../../public/images/admin/product.png" alt="Product Image" class="product-image">
                        <div class="product-actions">
                            <button class="edit-btn"><a href="admin-products-edit.html">Edit this product</a></button>
                        </div>
                    </div>
                    <div class="card-middle">
                        <div class="card-middle-upper">
                            <h1>Flyer/brochure Trifold Printing Glossy</h1>
                        </div>
                        <div class="card-middle-lower">
                            <div><p><img src="../../public/images/admin/Star.png" alt="Star Icon" class="icon-product">4.6 Rating</p></div>
                            <div><p><img src="../../public/images/admin/Tag.png" alt="Sold Icon" class="icon-product">905 Sold</p></div>
                        </div>
                    </div>
                    <div class="card-right">
                        <p class="product-price">$19.99</p>
                    </div>
                </div>
                <div class="product-card">
                    <div class="card-left">
                        <img src="../../public/images/admin/product.png" alt="Product Image" class="product-image">
                        <div class="product-actions">
                            <button class="edit-btn"><a href="admin-products-edit.html">Edit this product</a></button>
                        </div>
                    </div>
                    <div class="card-middle">
                        <div class="card-middle-upper">
                            <h1>Flyer/brochure Trifold Printing Glossy</h1>
                        </div>
                        <div class="card-middle-lower">
                            <p><img src="../../public/images/admin/Star.png" alt="Star Icon" class="icon-product">4.6 Rating</p>
                            <p><img src="../../public/images/admin/Tag.png" alt="Sold Icon" class="icon-product">905 Sold</p>
                        </div>
                    </div>
                    <div class="card-right">
                        <p class="product-price">$19.99</p>
                    </div>
                </div>
                <div class="product-card">
                    <div class="card-left">
                        <img src="../../public/images/admin/product.png" alt="Product Image" class="product-image">
                        <div class="product-actions">
                            <button class="edit-btn"><a href="admin-products-edit.html">Edit this product</a></button>
                        </div>
                    </div>
                    <div class="card-middle">
                        <div class="card-middle-upper">
                            <h1>Flyer/brochure Trifold Printing Glossy</h1>
                        </div>
                        <div class="card-middle-lower">
                            <p><img src="../../public/images/admin/Star.png" alt="Star Icon" class="icon-product">4.6 Rating</p>
                            <p><img src="../../public/images/admin/Tag.png" alt="Sold Icon" class="icon-product">905 Sold</p>
                        </div>
                    </div>
                    <div class="card-right">
                        <p class="product-price">$19.99</p>
                    </div>
                </div>

                </div>
                <!-- More product cards can be added here -->
            </div>
    </div>
</body>
</html>