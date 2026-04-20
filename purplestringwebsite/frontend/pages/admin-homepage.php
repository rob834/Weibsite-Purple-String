<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,500;1,500&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.0"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Homepage</title>
    <link rel="stylesheet" href="../css/admin-homepage.css">
</head>
<body>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap');
  </style>
    <div id="admin-sidebar">
        <img src="../public/images/admin/companylogo.png" alt="Company Logo" class="logo">
        <p>
            <a id="toggled" href="admin-homepage.html"><img src="../public/images/admin/dashboard icon-toggled.png" class="icon"><b>Dashboard</b></a>
            <a href="admin/admin-products.html"><img src="../public/images/admin/products icon.png" class="icon">Products</a>
            <a href="admin/admin-customers.html"><img src="../public/images/admin/customers icon.png" class="icon">Customers</a>
            <a href="admin/admin-chat.html"><img src="../public/images/admin/chats icon.png" class="icon">Chat</a>
            <a href="admin/admin-notification.html"><img src="../public/images/admin/Notification bell icon.png" class="icon">Notifications</a>
        </p>
    </div>
    <div id="admin-content">
        <div id="upper-right-accountname">
            <img src="../public/images/admin/account_profile.png" alt="Account Icon" class="account-icon">
            <span>Seller Name</span>
        </div>

        <div class="upper-row">

    <div class="total-sales">
        <div>
        <p>Total Sales:</p>
        <h2>&#8369; 1000.00</h2>
        </div>
        <div class="subtext">
            <p>+0.00</p>
            <p>since last week</p>
        </div>
    </div>

  <div class="total-orders">
    <div class="text">
        <p>Total Orders:</p>
        <h2>3,800</h2>
    </div>

    <img class="order-icon" src="/purplestringwebsite/frontend/public/images/products icon in admin.png" alt="products icon">
</div>

    <div class="conversion-rate">
        <div>
        <p>Conversion Rate:</p>
        <h2>25.00%</h2>
        </div>
    </div>

</div>

<div class="second-row">
    <div class="leftsidesecondrow">

    <div class="graph">
        <h2>Sales Analytic</h2>
        <canvas id="myChart" style="width:100%;max-width:600px"></canvas>
        <script>
           const xValues = ["Italy", "France", "Spain", "USA", "Argentina"];
            const yValues = [55, 49, 44, 24, 15];
            const barColors = ["red", "green","blue","orange","brown"];

            const ctx = document.getElementById('myChart');

new Chart(ctx, {
  type: "bar",
  data: {
    labels: xValues,
    datasets: [{
      backgroundColor: barColors,
      data: yValues
    }]
  },
  options: {
    plugins: {
      legend: {display: false},
      title: {
        display: false,
        text: "World Wine Production 2018",
        font: {size: 16}
      }
    }
  }
});
</script>

    </div>

       <div class="notifications-preview">
<div class="viewNotifsButton">
    <a href="/purplestringwebsite/frontend/pages/admin/admin-notification.html">
    <div class="view-notifs-button">
        <img src="/purplestringwebsite/frontend/public/images/view notifications icon admin.png" alt="">
        <p>view notifications</p>
    </div>
    </a>
</div>

    <ul>
        <li><p>Product out for shipment</p></li>
        <li><p>Order picked up by customer</p></li>
        <li><p>Payment pending</p></li>
    </ul>

</div>
    </div>

    <div class="recent-orders">
        <h2>Recent Orders</h2>
        <hr>
        <ul>
            <li>
                <p>Flyers/brochure Trifold Printing Glossy</p>
            </li>
            
            <li>
                <p>Custom Business Cards</p>
            </li>
            
            <li>
                <p>Custom Stickers</p>
            </li>
        </ul>
    </div>

</div>

 <div class="to-do-list">
    <h2>To-Do List</h2>

    <div class="todolistcontainer">
        <div>
        <p>0</p>
        
            <p>To-process Shipments</p>
        </div>

        <div>
            <p>2</p>
            <p>Processed shipments</p>
        </div>

        <div>
            <p>1</p>
            <p>Returned/Refunded/Cancelled</p>
        </div>

    </div>

    </div>

    </div>

</body>
</html>