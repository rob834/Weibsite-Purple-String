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
    <title>Admin Homepage</title>
    <link rel="stylesheet" href="../css/admin-homepage.css">
</head>
<body>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap');

    .kpi-btn, .chart-btn {
        padding: 4px 14px;
        border-radius: 20px;
        border: 2px solid #7c3aed;
        background: #fff;
        color: #7c3aed;
        cursor: pointer;
        font-size: 0.78rem;
        font-weight: 600;
        transition: background 0.15s, color 0.15s;
    }
    .kpi-btn.active, .chart-btn.active {
        background: #7c3aed;
        color: #fff;
    }
    .kpi-btn:hover, .chart-btn:hover {
        background: #7c3aed;
        color: #fff;
    }
  </style>

<?php
include_once __DIR__ . '/../../backend/connection.php';

// --- KPI Inventory Calculations ---
$instock_query = mysqli_query($con, "SELECT COUNT(*) as cnt FROM products WHERE stock > 0");
$instock_row = mysqli_fetch_assoc($instock_query);
$count_in_stock = intval($instock_row['cnt'] ?? 0);

$outofstock_query = mysqli_query($con, "SELECT COUNT(*) as cnt FROM products WHERE stock <= 0");
$outofstock_row = mysqli_fetch_assoc($outofstock_query);
$count_out_stock = intval($outofstock_row['cnt'] ?? 0);

// --- KPI: total sales + total orders for week / month / year ---
$kpi = [];
$period_clauses = [
    'week'  => "AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)",
    'month' => "AND created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)",
    'year'  => "AND created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)",
];
foreach ($period_clauses as $key => $clause) {
    $res = mysqli_query($con,
        "SELECT SUM(total) AS total_sales, COUNT(*) AS total_orders
         FROM orders
         WHERE status != 'cancelled' $clause"
    );
    $row = mysqli_fetch_assoc($res);
    $kpi[$key] = [
        'sales'  => floatval($row['total_sales'] ?? 0),
        'orders' => intval($row['total_orders']  ?? 0),
    ];
}

// --- Chart: Daily (last 30 days) ---
$daily_labels = []; $daily_values = [];
$dres = mysqli_query($con,
    "SELECT DATE_FORMAT(created_at,'%b %d') AS label,
            DATE(created_at) AS d,
            SUM(total) AS revenue
     FROM orders WHERE status != 'cancelled'
       AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
     GROUP BY d ORDER BY d ASC"
);
if ($dres) while ($r = mysqli_fetch_assoc($dres)) {
    $daily_labels[] = $r['label'];
    $daily_values[] = floatval($r['revenue']);
}

// --- Chart: Monthly (last 12 months) ---
$monthly_labels = []; $monthly_values = [];
$mres = mysqli_query($con,
    "SELECT DATE_FORMAT(created_at,'%b %Y') AS label,
            DATE_FORMAT(created_at,'%Y-%m') AS ym,
            SUM(total) AS revenue
     FROM orders WHERE status != 'cancelled'
       AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
     GROUP BY ym ORDER BY ym ASC"
);
if ($mres) while ($r = mysqli_fetch_assoc($mres)) {
    $monthly_labels[] = $r['label'];
    $monthly_values[] = floatval($r['revenue']);
}

// --- Chart: Yearly (last 5 years) ---
$yearly_labels = []; $yearly_values = [];
$yres = mysqli_query($con,
    "SELECT YEAR(created_at) AS label, SUM(total) AS revenue
     FROM orders WHERE status != 'cancelled'
       AND YEAR(created_at) >= YEAR(NOW()) - 4
     GROUP BY label ORDER BY label ASC"
);
if ($yres) while ($r = mysqli_fetch_assoc($yres)) {
    $yearly_labels[] = (string)$r['label'];
    $yearly_values[] = floatval($r['revenue']);
}
?>
<!-- sidebar -->
    <div id="admin-sidebar">
        <img src="../public/images/admin/companylogo.png" alt="Company Logo" class="logo">
        <p>
            <a id="toggled" href="admin-homepage.php"><img src="../public/images/admin/dashboard icon-toggled.png" class="icon"><b>Dashboard</b></a>
            <a href="admin/admin-products.php"><img src="../public/images/admin/products icon.png" class="icon">Products</a>
            <a href="admin/admin-customers.php"><img src="../public/images/admin/customers icon.png" class="icon">Customers</a>
            <a href="admin/admin-notification.php"><img src="../public/images/admin/Notification bell icon.png" class="icon">Notifications</a>
        </p>
    </div>
    
    <div id="admin-content">
    
    
<!-- Admin Account/Profile -->
        <div id="upper-right-accountname">
            <a href="admin/admin-profile.php" class="accountbtn">
            <img src="../public/images/admin/account_profile.png" alt="Account Icon" class="account-icon">
            </a>
        </div>

        <div class="upper-row">

            <div class="total-sales">
                <div>
                    <p>Total Sales:</p>
                    <h2 id="sales-value">&#8369; <?= number_format($kpi['week']['sales'], 2) ?></h2>
                    <div style="display:flex; gap:6px; margin-left: 40px; margin-bottom: 15px;">
                        <button class="kpi-btn active" id="sales-btn-week"  onclick="setKpi('week')">Week</button>
                        <button class="kpi-btn"        id="sales-btn-month" onclick="setKpi('month')">Month</button>
                        <button class="kpi-btn"        id="sales-btn-year"  onclick="setKpi('year')">Year</button>
                    </div>
                </div>
                <div class="subtext">
                    
                </div>
            </div>

            <div class="total-orders">
                <div class="text">
                    <p>Total Orders:</p>
                         <h2 id="orders-value"><?= number_format($kpi['week']['orders']) ?></h2>
                    <div style="display:flex; gap:6px; margin-left: 40px; margin-bottom: 15px;">
                        <button class="kpi-btn active" id="orders-btn-week"  onclick="setKpi('week')">Week</button>
                        <button class="kpi-btn"        id="orders-btn-month" onclick="setKpi('month')">Month</button>
                        <button class="kpi-btn"        id="orders-btn-year"  onclick="setKpi('year')">Year</button>
                    </div>
                </div>
                <img class="order-icon" src="../public/images/admin/products icon-toggled.png" alt="products icon">
            </div>

            <div class="conversion-rate" style="border: 2px solid #7c3aed; background: #faf5ff;">
    <div>
        <p>Stock Status Breakdown:</p>
        <h2 style="font-size: 1.2rem; margin-top: 8px;">
            <span style="color: #10b981; font-weight: 800;"><?= $count_in_stock ?> Active</span> 
            <span style="color: #6b7280; font-weight: 400; margin: 0 4px;">/</span> 
            <span style="color: #ef4444; font-weight: 800;"><?= $count_out_stock ?> Empty</span>
        </h2>
    </div>
</div>

        </div>

        <script>
        const kpiData = <?= json_encode($kpi) ?>;

        function setKpi(period) {
            const sales  = kpiData[period].sales;
            const orders = kpiData[period].orders;

            document.getElementById('sales-value').textContent =
                '₱ ' + sales.toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2});
            document.getElementById('orders-value').textContent =
                orders.toLocaleString('en-PH');

            ['week','month','year'].forEach(p => {
                document.getElementById('sales-btn-'  + p).classList.toggle('active', p === period);
                document.getElementById('orders-btn-' + p).classList.toggle('active', p === period);
            });
        }
        </script>

<div class="second-row">
    <div class="leftsidesecondrow">

        <div class="graph">
            <h2>Sales Analytic</h2>

            <div style="display:flex; gap:8px; margin-bottom:12px;">
                <button class="chart-btn active" id="chart-btn-daily"   onclick="showChart('daily')">Daily</button>
                <button class="chart-btn"        id="chart-btn-monthly" onclick="showChart('monthly')">Monthly</button>
                <button class="chart-btn"        id="chart-btn-yearly"  onclick="showChart('yearly')">Yearly</button>
            </div>

            <canvas id="myChart" style="width:100%; max-width:600px;"></canvas>

            <script>
            const chartDatasets = {
                daily:   { labels: <?= json_encode($daily_labels) ?>,   data: <?= json_encode($daily_values) ?>,   label: 'Daily Revenue (₱)'   },
                monthly: { labels: <?= json_encode($monthly_labels) ?>, data: <?= json_encode($monthly_values) ?>, label: 'Monthly Revenue (₱)' },
                yearly:  { labels: <?= json_encode($yearly_labels) ?>,  data: <?= json_encode($yearly_values) ?>,  label: 'Yearly Revenue (₱)'  },
            };

            const chart = new Chart(document.getElementById('myChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: chartDatasets.daily.labels,
                    datasets: [{
                        label: chartDatasets.daily.label,
                        data: chartDatasets.daily.data,
                        backgroundColor: 'rgba(124,58,237,0.7)',
                        borderColor: '#7c3aed',
                        borderWidth: 1,
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => '₱' + ctx.parsed.y.toLocaleString('en-PH', {minimumFractionDigits:2})
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { callback: val => '₱' + val.toLocaleString('en-PH') }
                        }
                    }
                }
            });

            function showChart(mode) {
                const ds = chartDatasets[mode];
                chart.data.labels            = ds.labels;
                chart.data.datasets[0].data  = ds.data;
                chart.data.datasets[0].label = ds.label;
                chart.update();

                ['daily','monthly','yearly'].forEach(m => {
                    document.getElementById('chart-btn-' + m)
                            .classList.toggle('active', m === mode);
                });
            }
            </script>
        </div>

       <div class="notifications-preview">
    <div class="viewNotifsButton">
    <a href="admin/admin-notification.php">
    <div class="view-notifs-button">
        <img src="../public/images/notification icon.png" alt="">
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
    <?php
    $rores = mysqli_query($con,
        "SELECT p.name
         FROM orders o
         JOIN order_items oi ON oi.order_id = o.order_id
         JOIN products p     ON p.product_id = oi.product_id
         ORDER BY o.created_at DESC
         LIMIT 10"
    );
    ?>
    <ul>
        <?php if (!$rores || mysqli_num_rows($rores) === 0): ?>
            <li><p>No recent orders.</p></li>
        <?php else: ?>
            <?php while ($ror = mysqli_fetch_assoc($rores)): ?>
                <li><p><?= htmlspecialchars($ror['name']) ?></p></li>
            <?php endwhile; ?>
        <?php endif; ?>
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