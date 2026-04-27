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

    <?php
    // load clients from backend JSON (clients pushed by checkout flow)
    include_once __DIR__ . '/../../../backend/admin_clients_storage.php';
    $clients = read_clients();
    if (empty($clients)) {
        echo '<div class="order-row"><div class="col-customer">No clients found</div></div>';
    }
    foreach ($clients as $client) {
        $id = htmlspecialchars($client['id']);
        $name = htmlspecialchars($client['name'] ?? 'Unknown');
        $total = htmlspecialchars($client['total'] ?? '₱0');
        $date = htmlspecialchars($client['date'] ?? '');
        $status = htmlspecialchars($client['status'] ?? 'Pending');
        $avatar = htmlspecialchars($client['avatar'] ?? '../../public/images/admin/account_profile.png');
    ?>
    <div class="order-row" data-client-id="<?= $id ?>">
        <div class="col-icon"><button class="delete-btn" data-client-id="<?= $id ?>"><img src="../../public/images/admin/delete-btn.png" alt="Delete Icon"></button></div>
        <div class="col-customer">
            <div class="user-avatar"><img src="<?= $avatar ?>" alt="User Avatar"></div>
            <?= $name ?>
        </div>
        <div class="col-status">
            <select class="status-select" data-client-id="<?= $id ?>">
                <?php
                $options = ['Pending','Processing','Shipping','Completed','Returned','Cancelled'];
                foreach ($options as $opt) {
                    $sel = $opt === $status ? 'selected' : '';
                    echo "<option value=\"$opt\" $sel>$opt</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-total"><?= $total ?></div>
        <div class="col-date"><?= $date ?></div>
    </div>
    <?php } ?>
</div>
    </div>
    <script>
    (function(){
        function postForm(url, data){
            return fetch(url, { method: 'POST', body: data, credentials: 'same-origin' }).then(r=>r.json());
        }

        document.querySelectorAll('.status-select').forEach(function(sel){
            sel.addEventListener('change', function(){
                var clientId = this.dataset.clientId;
                var fd = new FormData(); fd.append('client_id', clientId); fd.append('status', this.value);
                postForm('../../../backend/admin_update_client.php', fd).then(function(resp){
                    if (!resp.ok && resp.error) alert('Update failed: '+resp.error);
                }).catch(function(){ alert('Update failed'); });
            });
        });

        document.querySelectorAll('.delete-btn').forEach(function(btn){
            btn.addEventListener('click', function(e){
                var id = this.dataset.clientId || this.getAttribute('data-client-id');
                if (!id) return;
                if (!confirm('Delete this client from the list?')) return;
                var fd = new FormData(); fd.append('client_id', id);
                postForm('../../../backend/admin_delete_client.php', fd).then(function(resp){
                    if (resp.ok) {
                        var row = document.querySelector('.order-row[data-client-id="'+id+'"]');
                        if (row) row.remove();
                    } else {
                        alert('Delete failed');
                    }
                }).catch(function(){ alert('Delete failed'); });
            });
        });
    })();
    </script>
</body>
</html>