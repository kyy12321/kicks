
<?php
session_start();
include '../includes/auth.php';
include '../includes/db_connect.php'; // Include database connection

if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Get admin information
$adminName = isset($_SESSION['name']) ? $_SESSION['name'] : 'Admin';

// Query to get the total number of cashier users
$userCountQuery = "SELECT COUNT(*) as total_cashiers FROM users WHERE role = 'cashier'";
$userCountResult = $conn->query($userCountQuery);
$totalCashiers = 0;

if ($userCountResult && $userCountResult->num_rows > 0) {
    $row = $userCountResult->fetch_assoc();
    $totalCashiers = $row['total_cashiers'];
}

// Query to get the total number of products
$productCountQuery = "SELECT COUNT(*) as total_products FROM products";
$productCountResult = $conn->query($productCountQuery);
$totalProducts = 0;

if ($productCountResult && $productCountResult->num_rows > 0) {
    $row = $productCountResult->fetch_assoc();
    $totalProducts = $row['total_products'];
}

// Query to count new orders
$newOrdersQuery = "SELECT COUNT(*) as new_orders FROM orders WHERE status = 'Pending'";
$newOrdersResult = $conn->query($newOrdersQuery);
$newOrdersCount = 0;

if ($newOrdersResult && $newOrdersResult->num_rows > 0) {
    $row = $newOrdersResult->fetch_assoc();
    $newOrdersCount = $row['new_orders'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | KICKS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Import Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/Adashboard.css">
</head>
<body>
    <div class="container">
        <div class="dashboard-header">
            <h1 class="dashboard-title">
                <i class="fas fa-tachometer-alt"></i>
                Admin Dashboard
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
                    <li><a href="#" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="admin_products.php"><i class="fas fa-box"></i> Products</a></li>
                    <li>
                        <a href="admin_orders.php">
                            <i class="fas fa-shopping-cart"></i> Orders
                            <span class="order-badge"><?php echo $newOrdersCount; ?></span>
                        </a>
                    </li>
                    <li><a href="#"><i class="fas fa-chart-bar"></i> Reports</a></li>
                    <li><a href="../auth/logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
            
            <div class="main-content">
                <h2>Welcome, <?php echo $adminName; ?>!</h2>
                <p>Here's what's happening with your store today.</p>
                
                <div class="stats-grid">
                    <div class="stat-card products">
                        <h3>Total Products</h3>
                        <div class="value"><?php echo $totalProducts; ?></div>
                        <div class="icon"><i class="fas fa-box"></i></div>
                    </div>
                    
                    <div class="stat-card users">
                        <h3>Total Users</h3>
                        <div class="value"><?php echo $totalCashiers; ?></div>
                        <div class="icon"><i class="fas fa-users"></i></div>
                    </div>
                    
                    <div class="stat-card orders">
                        <h3>New Orders</h3>
                        <div class="value"><?php echo $newOrdersCount; ?></div>
                        <div class="icon"><i class="fas fa-shopping-cart"></i></div>
                    </div>
                </div>
                
                <div class="recent-section">
    <div class="section-header">
        <h3 class="section-title">Recent Orders</h3>
        <a href="admin_orders.php" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
    </div>
    
    <div class="responsive-grid">
        <?php
        // Get recent orders
       $recentOrdersQuery = "SELECT o.id as order_id, u.username as customer, o.total, 
                     o.created_at as order_date, o.status 
                     FROM orders o
                     JOIN users u ON o.user_id = u.id
                     ORDER BY o.created_at DESC LIMIT 4";
        $recentOrdersResult = $conn->query($recentOrdersQuery);
        
        if ($recentOrdersResult && $recentOrdersResult->num_rows > 0) {
            while($order = $recentOrdersResult->fetch_assoc()) {
                echo '<div class="order-card">
                        <div class="order-header">
                            <span class="order-id">#'.htmlspecialchars($order['order_id']).'</span>
                            <span class="status '.htmlspecialchars($order['status']).'">'.htmlspecialchars($order['status']).'</span>
                        </div>
                        <div class="order-body">
                            <p><i class="fas fa-user"></i> '.htmlspecialchars($order['customer']).'</p>
                            <p><i class="fas fa-calendar-alt"></i> '.date('M j, Y', strtotime($order['order_date'])).'</p>
                            <p><i class="fas fa-dollar-sign"></i> '.number_format($order['total'], 2).'</p>
                        </div>
                        <div class="order-actions">
                            <button class="action-btn view-order" data-id="'.$order['order_id'].'"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>';
            }
        } else {
            echo '<div class="no-orders">No recent orders found</div>';
        }
        ?>
    </div>
</div>

    <div class="recent-section">
    <div class="section-header">
        <h3 class="section-title">Popular Products</h3>
        <a href="admin_products.php" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
    </div>
    
    <div class="responsive-grid">
        <?php
        // Get popular products - CORRECTED QUERY
        $popularProductsQuery = "SELECT name, category, price, stock 
                               FROM products 
                               ORDER BY stock ASC LIMIT 4";
        $popularProductsResult = $conn->query($popularProductsQuery);
        
        if ($popularProductsResult && $popularProductsResult->num_rows > 0) {
            while($product = $popularProductsResult->fetch_assoc()) {
                echo '<div class="product-card">
                        <div class="product-header">
                            <h4>'.htmlspecialchars($product['name']).'</h4>
                            <span class="category">'.htmlspecialchars($product['category']).'</span>
                        </div>
                        <div class="product-details">
                            <p><i class="fas fa-tag"></i> $'.number_format($product['price'], 2).'</p>
                            <p><i class="fas fa-cubes"></i> '.htmlspecialchars($product['stock']).' in stock</p>
                        </div>
                        <div class="product-actions">
                            <button class="action-btn view-product"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>';
            }
        } else {
            echo '<div class="no-products">No products found</div>';
        }
        ?>
    </div>
</div>

<style>
.responsive-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.order-card, .product-card {
    background: #fff;
    border-radius: 8px;
    padding: 1rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.order-header, .product-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.status {
    font-size: 0.8rem;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    text-transform: capitalize;
}

.status.pending { background: #fff3cd; color: #856404; }
.status.completed { background: #d4edda; color: #155724; }
.status.cancelled { background: #f8d7da; color: #721c24; }

.order-body p, .product-details p {
    margin: 0.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.order-actions, .product-actions {
    margin-top: 1rem;
    display: flex;
    gap: 0.5rem;
}

@media (max-width: 768px) {
    .responsive-grid {
        grid-template-columns: 1fr;
    }
    
    .order-card, .product-card {
        padding: 0.75rem;
    }
}
</style>
    
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
            
            // Hide badge if count is 0
            const orderBadge = document.querySelector('.order-badge');
            if (orderBadge && (orderBadge.textContent === '0' || orderBadge.textContent === '')) {
                orderBadge.style.display = 'none';
            }
            
            // Auto-refresh dashboard data every 60 seconds
            setInterval(function() {
                fetch(window.location.href)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        
                        // Update order count
                        const newOrdersValue = doc.querySelector('.stat-card.orders .value').textContent;
                        document.querySelector('.stat-card.orders .value').textContent = newOrdersValue;
                        
                        // Update order badge
                        const newOrderBadge = doc.querySelector('.order-badge').textContent;
                        document.querySelector('.order-badge').textContent = newOrderBadge;
                        
                        // Hide badge if count is 0
                        if (newOrderBadge === '0' || newOrderBadge === '') {
                            document.querySelector('.order-badge').style.display = 'none';
                        } else {
                            document.querySelector('.order-badge').style.display = 'inline-block';
                        }
                        
                        console.log('Dashboard data refreshed at ' + new Date().toLocaleTimeString());
                    })
                    .catch(error => console.error('Error refreshing dashboard data:', error));
            }, 60000); // Refresh every 60 seconds
        });

        // Auto-refresh orders count every 30 seconds
function updateOrdersCount() {
    fetch('/api/get_new_orders.php')
        .then(response => response.json())
        .then(data => {
            // Update stat card
            document.querySelector('.stat-card.orders .value').textContent = data.count;
            
            // Update sidebar badge
            const orderBadge = document.querySelector('.order-badge');
            orderBadge.textContent = data.count;
            
            // Toggle badge visibility
            orderBadge.style.display = data.count > 0 ? 'inline-block' : 'none';
        });
}

// Initial update and set interval
updateOrdersCount();
setInterval(updateOrdersCount, 30000);
    </script>
</body>
</html>
</qodoArtifact>
