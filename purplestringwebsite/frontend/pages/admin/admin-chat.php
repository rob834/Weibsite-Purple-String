<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Products</title>
    <link rel="stylesheet" href="../../css/admin/admin-chat.css">
</head>
<body>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap');
    @import url('https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap');
</style>
    <div id="admin-sidebar">
        <img src="../../public/images/admin/companylogo.png" alt="Company Logo" class="logo">
        <p>
            <a href="../admin-homepage.php"><img src="../../public/images/admin/dashboard icon.png" class="icon">Dashboard</a>
            <a href="admin-products.php"><img src="../../public/images/admin/products icon.png" class="icon">Products</a>
            <a href="admin-customers.php"><img src="../../public/images/admin/customers icon.png" class="icon">Customers</a>
            <a id="toggled" href="admin-chat.php"><img src="../../public/images/admin/chats icon-toggled.png" class="icon">Chat</a>
            <a href="admin-notification.php"><img src="../../public/images/admin/Notification bell icon.png" class="icon">Notifications</a>
        </p>
    </div>
    <div id="admin-content">
        <div id="upper-right-accountname">
            <img src="../../public/images/admin/account_profile.png" alt="Account Icon" class="account-icon">
            <span>Seller Name</span>
        </div>

    <div class="chat-app">
    <aside class="sidebar">
        <h2>Messages</h2>
        <ul class="chat-list">
        <li id="selected-chat" ><img src="../../public/images/admin/account_profile.png" alt="Account Icon" class="avatar-small" >Ryan Gossling Santos</li>
        <li><img src="../../public/images/admin/account_profile.png" alt="Account Icon" class="avatar-small">Ryan Gossling Santos</li>
        <li><img src="../../public/images/admin/account_profile.png" alt="Account Icon" class="avatar-small">Ryan Gossling Santos</li>
        <li><img src="../../public/images/admin/account_profile.png" alt="Account Icon" class="avatar-small">Ryan Gossling Santos</li>
        <li><img src="../../public/images/admin/account_profile.png" alt="Account Icon" class="avatar-small">Ryan Gossling Santos</li>
        </ul>
    </aside>
        
    <main class="conversation">
    <header class="chat-header">
        <img src="../../public/images/admin/account_profile.png" alt="Account Icon" class="avatar-small">Ryan Gossling Santos</li>
    </header>

    <div class="chat-body">
        <div class="message">
            <p>I like toes</p>
        </div>
        </div>

    <footer class="chat-footer">
    <input type="text" placeholder="Type a message..." value="I like toes">


    <button class="send-btn">
        <img src="../../public/images/admin/send.png" alt="Send Button" class="send-icon">
    </button>
    </footer>
    </main>
    </div>

</body>
</html>