
<?php
session_start();
include '../includes/auth.php';
include '../includes/db_connect.php';

// Redirect if not logged in as admin
if (!isLoggedIn() || $_SESSION['role'] !== 'superadmin') {
    header("Location: ../auth/login.php");
    exit();
}

// Get admin name from session or set a default
$adminName = isset($_SESSION['name']) ? $_SESSION['name'] : 'Superadmin';

// Get date ranges for reports
$today = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 day'));
$startOfWeek = date('Y-m-d', strtotime('monday this week'));
$startOfMonth = date('Y-m-01');
$startOfYear = date('Y-01-01');

// Fetch sales summary data
$salesSummary = [
    'today' => 0,
    'yesterday' => 0,
    'thisWeek' => 0,
    'thisMonth' => 0,
    'thisYear' => 0
];

// Today's sales
$todaySalesQuery = "SELECT SUM(total) as sales FROM orders WHERE DATE(order_date) = '$today' AND status != 'Cancelled'";
$result = $conn->query($todaySalesQuery);
if ($result && $row = $result->fetch_assoc()) {
    $salesSummary['today'] = $row['sales'] ?: 0;
}

// Yesterday's sales
$yesterdaySalesQuery = "SELECT SUM(total) as sales FROM orders WHERE DATE(order_date) = '$yesterday' AND status != 'Cancelled'";
$result = $conn->query($yesterdaySalesQuery);
if ($result && $row = $result->fetch_assoc()) {
    $salesSummary['yesterday'] = $row['sales'] ?: 0;
}

// This week's sales
$weekSalesQuery = "SELECT SUM(total) as sales FROM orders WHERE DATE(order_date) >= '$startOfWeek' AND status != 'Cancelled'";
$result = $conn->query($weekSalesQuery);
if ($result && $row = $result->fetch_assoc()) {
    $salesSummary['thisWeek'] = $row['sales'] ?: 0;
}

// This month's sales
$monthSalesQuery = "SELECT SUM(total) as sales FROM orders WHERE DATE(order_date) >= '$startOfMonth' AND status != 'Cancelled'";
$result = $conn->query($monthSalesQuery);
if ($result && $row = $result->fetch_assoc()) {
    $salesSummary['thisMonth'] = $row['sales'] ?: 0;
}

// This year's sales
$yearSalesQuery = "SELECT SUM(total) as sales FROM orders WHERE DATE(order_date) >= '$startOfYear' AND status != 'Cancelled'";
$result = $conn->query($yearSalesQuery);
if ($result && $row = $result->fetch_assoc()) {
    $salesSummary['thisYear'] = $row['sales'] ?: 0;
}

// Get top selling products
$topProductsQuery = "SELECT p.name, p.price, SUM(oi.quantity) as total_sold, SUM(oi.quantity * p.price) as revenue
                    FROM order_items oi
                    JOIN products p ON oi.product_id = p.id
                    JOIN orders o ON oi.order_id = o.order_id
                    WHERE o.status != 'Cancelled'
                    GROUP BY p.id
                    ORDER BY total_sold DESC
                    LIMIT 5";
$topProductsResult = $conn->query($topProductsQuery);

 // Get sales by category
$categorySalesQuery = "SELECT p.category as name, 
                      SUM(oi.quantity) as items_sold, 
                      SUM(oi.quantity * p.price) as revenue
                      FROM order_items oi
                      JOIN products p ON oi.product_id = p.id
                      JOIN orders o ON oi.order_id = o.order_id
                      WHERE o.status != 'Cancelled'
                      GROUP BY p.category
                      ORDER BY revenue DESC";
$categorySalesResult = $conn->query($categorySalesQuery);

// Get monthly sales for chart
$monthlySalesQuery = "SELECT DATE_FORMAT(order_date, '%Y-%m') as month, SUM(total) as sales
                     FROM orders
                     WHERE status != 'Cancelled' AND order_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                     GROUP BY month
                     ORDER BY month ASC";
$monthlySalesResult = $conn->query($monthlySalesQuery);
$monthlySalesData = [];
$monthlySalesLabels = [];

if ($monthlySalesResult) {
    while ($row = $monthlySalesResult->fetch_assoc()) {
        $monthLabel = date('M Y', strtotime($row['month'] . '-01'));
        $monthlySalesLabels[] = $monthLabel;
        $monthlySalesData[] = $row['sales'];
    }
}

/**
 * Get customer demographics
 * - total customers: unique users who placed orders
 * - returning customers: users with more than 1 completed order
 * - average order value: average total of all orders
 */
$customerDemographicsQuery = "
    SELECT 
        COUNT(DISTINCT user_id) as total_customers,
        SUM(is_returning) as returning_customers,
        AVG(total) as average_order_value
    FROM (
        SELECT user_id, total,
            CASE WHEN SUM(status = 'Completed') OVER (PARTITION BY user_id) > 1 THEN 1 ELSE 0 END as is_returning
        FROM orders
    ) t
";
$customerResult = $conn->query($customerDemographicsQuery);
$customerData = $customerResult ? $customerResult->fetch_assoc() : ['total_customers'=>0,'returning_customers'=>0,'average_order_value'=>0];

// Format numbers
function formatCurrency($amount) {
    return '₱' . number_format($amount, 2);
}

function formatNumber($number) {
    return number_format($number);
}

/**
 * Get order fulfillment status counts
 */
$statusCounts = [
    'Pending' => 0,
    'Processing' => 0,
    'Shipped' => 0,
    'Delivered' => 0,
    'Completed' => 0,
    'Cancelled' => 0
];
$statusQuery = "SELECT status, COUNT(*) as count FROM orders GROUP BY status";
$statusResult = $conn->query($statusQuery);
if ($statusResult) {
    while ($row = $statusResult->fetch_assoc()) {
        $status = $row['status'];
        if (isset($statusCounts[$status])) {
            $statusCounts[$status] = $row['count'];
        }
    }
}

// Calculate growth percentages
$todayGrowth = 0;
if ($salesSummary['yesterday'] > 0) {
    $todayGrowth = (($salesSummary['today'] - $salesSummary['yesterday']) / $salesSummary['yesterday']) * 100;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports | KICKS Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/Adashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Updated styles for reports page */
        .reports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }
        .report-card {
            background: #fff;
            border-radius: 10px;
            padding: 24px 20px 18px 20px;
            box-shadow: 0 4px 16px rgba(52,152,219,0.07), 0 1.5px 4px rgba(0,0,0,0.04);
            display: flex;
            flex-direction: column;
            transition: box-shadow 0.2s;
        }
        .report-card:hover {
            box-shadow: 0 8px 24px rgba(52,152,219,0.13), 0 2px 8px rgba(0,0,0,0.06);
        }
        .report-card .title {
            font-size: 13px;
            color: #7b8a99;
            margin-bottom: 8px;
            letter-spacing: 0.02em;
        }
        .report-card .value {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 6px;
            color: #222;
        }
        .report-card .growth {
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 4px;
            margin-top: 2px;
        }
        .growth.positive {
            color: #1BC5BD;
        }
        .growth.negative {
            color: #F64E60;
        }
        .report-card .subtitle {
            font-size: 12px;
            color: #a0aec0;
            margin-top: 2px;
        }
        .chart-container {
            position: relative;
            background: #fff;
            border-radius: 10px;
            padding: 24px 20px;
            box-shadow: 0 4px 16px rgba(52,152,219,0.07), 0 1.5px 4px rgba(0,0,0,0.04);
            margin-bottom: 32px;
            min-height: 400px;
            height: 400px;
        }
        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
        }
        .chart-title {
            font-size: 17px;
            font-weight: 600;
            color: #333;
            margin: 0;
        }
        .table-container {
            background: #fff;
            border-radius: 10px;
            padding: 24px 20px;
            box-shadow: 0 4px 16px rgba(52,152,219,0.07), 0 1.5px 4px rgba(0,0,0,0.04);
            margin-bottom: 32px;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
        }
        .report-table th, .report-table td {
            padding: 13px 16px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        .report-table th {
            font-weight: 700;
            color: #3b4252;
            background-color: #f8fafc;
            font-size: 14px;
        }
        .report-table tr:last-child td {
            border-bottom: none;
        }
        .product-name {
            display: flex;
            align-items: center;
        }
        .product-icon {
            width: 34px;
            height: 34px;
            background-color: #e2e8f0;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 18px;
            color: #3498db;
        }
        .category-badge {
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            background: #f1f5f9;
            color: #3498db;
        }
        .metrics-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }
        .metric-card {
            background: #fff;
            border-radius: 10px;
            padding: 24px 20px;
            box-shadow: 0 4px 16px rgba(52,152,219,0.07), 0 1.5px 4px rgba(0,0,0,0.04);
        }
        .metric-card .title {
            font-size: 13px;
            color: #7b8a99;
            margin-bottom: 8px;
        }
        .metric-card .value {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 6px;
            color: #222;
        }
        .metric-card .subtitle {
            font-size: 12px;
            color: #a0aec0;
        }
        .date-filter {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 22px;
        }
        .date-filter select, .date-filter button {
            padding: 8px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            background-color: #fff;
            font-size: 14px;
        }
        .date-filter button {
            background-color: #3498db;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background 0.15s;
        }
        .date-filter button:disabled {
            background: #e2e8f0;
            color: #aaa;
            cursor: not-allowed;
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
        }
        .section-title {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            color: #222;
        }
        .refresh-btn {
            background: none;
            border: none;
            color: #3498db;
            cursor: pointer;
            font-size: 18px;
            transition: color 0.15s;
        }
        .refresh-btn:hover {
            color: #217dbb;
        }
        @media (max-width: 900px) {
            .reports-grid, .metrics-container {
                grid-template-columns: 1fr;
            }
            .chart-container, .table-container, .metric-card {
                padding: 16px 8px;
            }
        }
        @media (max-width: 600px) {
            .dashboard-header, .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            .main-content {
                padding: 0 2vw;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="dashboard-header">
            <h1 class="dashboard-title">
                <i class="fas fa-chart-bar"></i>
                Reports & Analytics
            </h1>
            <div class="user-info">
                <div class="user-avatar">
                    <?php echo substr($adminName, 0, 1); ?>
                </div>
                <span class="user-name"><?php echo $adminName; ?></span>
            </div>
        </div>
        
        <div class="dashboard-content">
            <div class="sidebar">
                <ul>
                    <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
                    <li><a href="products.php"><i class="fas fa-box"></i> Products</a></li>
                    <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <li><a href="reports.php" class="active"><i class="fas fa-chart-bar"></i> Reports</a></li>
                    <li><a href="../auth/logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
            
            <div class="main-content">
                <div class="section-header">
                    <h2 class="section-title">Sales Analytics</h2>
                    <button class="refresh-btn" onclick="window.location.reload();">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
                
                <div class="date-filter">
                    <label for="date-range">Date Range:</label>
                    <select id="date-range" disabled>
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="week">This Week</option>
                        <option value="month" selected>This Month</option>
                        <option value="year">This Year</option>
                        <option value="custom">Custom Range</option>
                    </select>
                    <button type="button" disabled>Apply</button>
                    <span style="font-size:12px;color:#888;">(Coming soon)</span>
                </div>

                <!-- Sales Summary Cards -->
                <div class="reports-grid">
                    <div class="report-card">
                        <div class="title">Today's Sales</div>
                        <div class="value"><?php echo formatCurrency($salesSummary['today']); ?></div>
                        <div class="growth <?php echo $todayGrowth >= 0 ? 'positive' : 'negative'; ?>">
                            <?php if ($todayGrowth >= 0): ?>
                                <i class="fas fa-arrow-up"></i>
                            <?php else: ?>
                                <i class="fas fa-arrow-down"></i>
                            <?php endif; ?>
                            <?php echo abs(round($todayGrowth, 1)); ?>% vs yesterday
                        </div>
                    </div>
                    <div class="report-card">
                        <div class="title">This Week</div>
                        <div class="value"><?php echo formatCurrency($salesSummary['thisWeek']); ?></div>
                        <div class="subtitle"><?php echo date('M d', strtotime($startOfWeek)); ?> - <?php echo date('M d'); ?></div>
                    </div>
                    <div class="report-card">
                        <div class="title">This Month</div>
                        <div class="value"><?php echo formatCurrency($salesSummary['thisMonth']); ?></div>
                        <div class="subtitle"><?php echo date('M Y'); ?></div>
                    </div>
                    <div class="report-card">
                        <div class="title">This Year</div>
                        <div class="value"><?php echo formatCurrency($salesSummary['thisYear']); ?></div>
                        <div class="subtitle"><?php echo date('Y'); ?></div>
                    </div>
                </div>

                <!-- Revenue Trend Chart -->
                <div class="chart-container">
                    <div class="chart-header">
                        <h3 class="chart-title">Revenue Trend (Last 12 Months)</h3>
                    </div>
                    <canvas id="revenueChart"></canvas>
                </div>

                <!-- Top Selling Products -->
                <div class="section-header">
                    <h2 class="section-title">Top Selling Products</h2>
                </div>
                
                <div class="table-container">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Units Sold</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($topProductsResult && $topProductsResult->num_rows > 0): ?>
                                <?php while ($product = $topProductsResult->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <div class="product-name">
                                            <div class="product-icon">
                                                <i class="fas fa-shoe-prints"></i>
                                            </div>
                                            <?php echo htmlspecialchars($product['name']); ?>
                                        </div>
                                    </td>
                                    <td><?php echo formatCurrency($product['price']); ?></td>
                                    <td><?php echo formatNumber($product['total_sold']); ?></td>
                                    <td><?php echo formatCurrency($product['revenue']); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; padding: 20px;">
                                        <i class="fas fa-box" style="font-size: 24px; color: #adb5bd; margin-bottom: 10px;"></i>
                                        <p>No product sales data available.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Sales by Category -->
                <div class="section-header">
                    <h2 class="section-title">Sales by Category</h2>
                </div>
                
                <div class="chart-container">
                    <canvas id="categoryChart"></canvas>
                </div>

                <!-- Customer Insights -->
                <div class="section-header">
                    <h2 class="section-title">Customer Insights</h2>
                </div>
                
                <div class="metrics-container">
                    <div class="metric-card">
                        <div class="title">Total Customers</div>
                        <div class="value"><?php echo formatNumber($customerData['total_customers'] ?? 0); ?></div>
                    </div>
                    <div class="metric-card">
                        <div class="title">Returning Customers</div>
                        <div class="value"><?php echo formatNumber($customerData['returning_customers'] ?? 0); ?></div>
                    </div>
                    <div class="metric-card">
                        <div class="title">Average Order Value</div>
                        <div class="value"><?php echo formatCurrency($customerData['average_order_value'] ?? 0); ?></div>
                    </div>
                </div>

                <!-- Order Fulfillment Metrics -->
                <div class="section-header">
                    <h2 class="section-title">Order Fulfillment</h2>
                </div>
                
                <div class="chart-container">
                    <canvas id="fulfillmentChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add active class to current navigation item
            const currentLocation = location.href;
            const menuItems = document.querySelectorAll('.sidebar a');
            menuItems.forEach(item => {
                if(item.href === currentLocation) {
                    item.classList.add('active');
                }
            });
            
            // Revenue Chart
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            const revenueChart = new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($monthlySalesLabels); ?>,
                    datasets: [{
                        label: 'Monthly Revenue',
                        data: <?php echo json_encode($monthlySalesData); ?>,
                        backgroundColor: 'rgba(52, 152, 219, 0.1)',
                        borderColor: 'rgba(52, 152, 219, 1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('en-PH', { 
                                            style: 'currency', 
                                            currency: 'PHP' 
                                        }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₱' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
            
            // Category Chart
            // Prepare category chart data in PHP for reliability
            <?php
            $categoryLabels = [];
            $categoryRevenues = [];
            if ($categorySalesResult) {
                while ($category = $categorySalesResult->fetch_assoc()) {
                    $categoryLabels[] = $category['name'];
                    $categoryRevenues[] = $category['revenue'];
                }
            }
            ?>
            const categoryData = {
                labels: <?php echo json_encode($categoryLabels); ?>,
                datasets: [{
                    label: 'Revenue by Category',
                    data: <?php echo json_encode($categoryRevenues); ?>,
                    backgroundColor: [
                        'rgba(52, 152, 219, 0.7)',
                        'rgba(46, 204, 113, 0.7)',
                        'rgba(155, 89, 182, 0.7)',
                        'rgba(241, 196, 15, 0.7)',
                        'rgba(231, 76, 60, 0.7)',
                        'rgba(52, 73, 94, 0.7)'
                    ],
                    borderWidth: 1
                }]
            };
            
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            const categoryChart = new Chart(categoryCtx, {
                type: 'doughnut',
                data: categoryData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed !== null) {
                                        label += new Intl.NumberFormat('en-PH', { 
                                            style: 'currency', 
                                            currency: 'PHP' 
                                        }).format(context.parsed);
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
            
            // Order Fulfillment Chart
            const fulfillmentCtx = document.getElementById('fulfillmentChart').getContext('2d');
            const fulfillmentChart = new Chart(fulfillmentCtx, {
                type: 'bar',
                data: {
                    labels: ['Pending', 'Processing', 'Shipped', 'Delivered', 'Completed', 'Cancelled'],
                    datasets: [{
                        label: 'Orders by Status',
                        data: [
                            <?php 
                            echo (int)$statusCounts['Pending'] . ",";
                            echo (int)$statusCounts['Processing'] . ",";
                            echo (int)$statusCounts['Shipped'] . ",";
                            echo (int)$statusCounts['Delivered'] . ",";
                            echo (int)$statusCounts['Completed'] . ",";
                            echo (int)$statusCounts['Cancelled'];
                            ?>
                        ],
                        backgroundColor: [
                            'rgba(255, 168, 0, 0.7)',  // Pending - yellow
                            'rgba(52, 152, 219, 0.7)', // Processing - blue
                            'rgba(155, 89, 182, 0.7)', // Shipped - purple
                            'rgba(46, 204, 113, 0.7)', // Delivered - green
                            'rgba(27, 197, 189, 0.7)', // Completed - teal
                            'rgba(246, 78, 96, 0.7)'   // Cancelled - red
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
</qodoArtifact>
