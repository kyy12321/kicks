
<?php
session_start();
include '../includes/auth.php';
include '../includes/db_connect.php'; // Include database connection

if (!isLoggedIn() || $_SESSION['role'] !== 'superadmin') {
    header("Location: ../auth/login.php");
    exit();
}

// Get superadmin information
$superadminName = isset($_SESSION['name']) ? $_SESSION['name'] : 'superadmin';

// Query to get the total number of admin users
$adminCountQuery = "SELECT COUNT(*) as total_admins FROM users WHERE role = 'admin'";
$adminCountResult = $conn->query($adminCountQuery);
$totalAdmins = 0;

if ($adminCountResult && $adminCountResult->num_rows > 0) {
    $row = $adminCountResult->fetch_assoc();
    $totalAdmins = $row['total_admins'];
}

// Query to get the total number of products
$productCountQuery = "SELECT COUNT(*) as total_products FROM products";
$productCountResult = $conn->query($productCountQuery);
$totalProducts = 0;

if ($productCountResult && $productCountResult->num_rows > 0) {
    $row = $productCountResult->fetch_assoc();
    $totalProducts = $row['total_products'];
}

// Query to get the total number of new orders (assuming orders have a status field)
$newOrdersQuery = "SELECT COUNT(*) as new_orders FROM orders WHERE status = 'Pending'";
$newOrdersResult = $conn->query($newOrdersQuery);
$newOrders = 0;

if ($newOrdersResult && $newOrdersResult->num_rows > 0) {
    $row = $newOrdersResult->fetch_assoc();
    $newOrders = $row['new_orders'];
}

// Get total revenue with proper null handling
$revenueQuery = "SELECT COALESCE(SUM(total), 0) as total_revenue 
                FROM orders 
                WHERE status = 'Completed'";
$revenueResult = $conn->query($revenueQuery);
$totalRevenue = 0;

if ($revenueResult) {
    $row = $revenueResult->fetch_assoc();
    $totalRevenue = (float)($row['total_revenue'] ?? 0);
}

// Query to get recent orders
$recentOrdersQuery = "SELECT o.order_id, u.username as customer, 
                     o.order_date, o.total, o.status 
                     FROM orders o
                     JOIN users u ON o.user_id = u.id
                     ORDER BY o.order_date DESC 
                     LIMIT 4";
$recentOrdersResult = $conn->query($recentOrdersQuery);

// Query to get popular products
$popularProductsQuery = "SELECT p.id, p.name, p.price, p.image_path, 
                        SUM(oi.quantity) as total_sold 
                        FROM order_items oi
                        JOIN products p ON oi.product_id = p.id
                        GROUP BY p.id
                        ORDER BY total_sold DESC
                        LIMIT 5";
$popularProductsResult = $conn->query($popularProductsQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Superadmin Dashboard | KICKS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Import Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/SA.css">
</head>
<body>
    <div class="container">
        <div class="dashboard-header">
            <h1 class="dashboard-title">
                <i class="fas fa-tachometer-alt"></i>
                Superadmin Dashboard
            </h1>
            <div class="user-info">
                <div class="user-avatar">
                    <?php echo substr($superadminName, 0, 1); ?>
                </div>
                <span class="user-name"><?php echo $superadminName; ?></span>
            </div>
        </div>
        
        <div class="dashboard-content">
            <div class="sidebar">
                <ul>
                    <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
                    <li><a href="products.php"><i class="fas fa-box"></i> Products</a></li>
                    <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
                    <li><a href="../auth/logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
            
            <div class="main-content">
                <h2>Welcome, <?php echo $superadminName; ?>!</h2>
                <p>Here's what's happening with your store today.</p>
                
                <div class="stats-grid">
                    <div class="stat-card products">
                        <h3>Total Products</h3>
                        <div class="value"><?php echo $totalProducts; ?></div>
                        <div class="icon"><i class="fas fa-box"></i></div>
                    </div>
                    
                    <div class="stat-card users">
                        <h3>Total Admins</h3>
                        <div class="value"><?php echo $totalAdmins; ?></div>
                        <div class="icon"><i class="fas fa-users"></i></div>
                    </div>
                    
                    <div class="stat-card orders">
                        <h3>New Orders</h3>
                        <div class="value"><?php echo $newOrders; ?></div>
                        <div class="icon"><i class="fas fa-shopping-cart"></i></div>
                    </div>
                    
 <div class="stat-card revenue">
        <h3>Revenue</h3>
        <div class="value">₱<?php echo number_format($totalRevenue, 2); ?></div>
        <div class="icon"><i class="fas fa-coins"></i></div> <!-- Updated icon -->
    </div>
                </div>
                
                <div class="recent-section">
                    <div class="section-header">
                        <h3 class="section-title">Recent Orders</h3>
                        <a href="#" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
                    </div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if ($recentOrdersResult && $recentOrdersResult->num_rows > 0) {
                                while ($order = $recentOrdersResult->fetch_assoc()) {
                                    // Determine status class
                                    $statusClass = '';
                                    switch (strtolower($order['status'])) {
                                        case 'completed':
                                            $statusClass = 'completed';
                                            break;
                                        case 'pending':
                                            $statusClass = 'pending';
                                            break;
                                        case 'cancelled':
                                            $statusClass = 'cancelled';
                                            break;
                                        default:
                                            $statusClass = 'pending';
                                    }
                                    
                                    // Format date
                                    $orderDate = date('M d, Y', strtotime($order['order_date']));
                            ?>
                            <tr>
                                <td>#<?php echo $order['order_id']; ?></td>
                                <td><?php echo htmlspecialchars($order['customer']); ?></td>
                                <td><?php echo $orderDate; ?></td>
                                <td>₱<?php echo number_format($order['total'], 2); ?></td>
                                <td><span class="status <?php echo $statusClass; ?>"><?php echo $order['status']; ?></span></td>
                                <td>
                                    <button class="action-btn"><i class="fas fa-eye"></i></button>
                                    <button class="action-btn"><i class="fas fa-edit"></i></button>
                                </td>
                            </tr>
                            <?php
                                }
                            } else {
                                echo '<tr><td colspan="7" class="text-center">No recent orders found</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="recent-section">
                    <div class="section-header">
                        <h3 class="section-title">Popular Products</h3>
                        <a href="products.php" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
                    </div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>Product ID</th>
                                <th>Product Name</th>
                                <th>Price</th>
                                <th>Units Sold</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if ($popularProductsResult && $popularProductsResult->num_rows > 0) {
                                while ($product = $popularProductsResult->fetch_assoc()) {
                            ?>
                            <tr>
                                <td>#<?php echo $product['id']; ?></td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td>₱<?php echo number_format($product['price'], 2); ?></td>
                                <td><strong><?php echo $product['total_sold']; ?></strong> units</td>
                                <td>
                                    <button class="action-btn"><i class="fas fa-eye"></i></button>
                                    <button class="action-btn"><i class="fas fa-edit"></i></button>
                                </td>
                            </tr>
                            <?php
                                }
                            } else {
                                echo '<tr><td colspan="5" class="text-center">No popular products found</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
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
            
            // Animation for stat cards
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });
        });
    </script>
</body>
</html>
</qodoArtifact>
