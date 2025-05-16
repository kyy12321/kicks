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
                    <li><a href="#"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <li><a href="#"><i class="fas fa-chart-bar"></i> Reports</a></li>
                    <li><a href="#"><i class="fas fa-cog"></i> Settings</a></li>
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
                        <div class="value">17</div>
                        <div class="icon"><i class="fas fa-shopping-cart"></i></div>
                    </div>
                    
                    <div class="stat-card revenue">
                        <h3>Revenue</h3>
                        <div class="value">$8,245</div>
                        <div class="icon"><i class="fas fa-dollar-sign"></i></div>
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
                                <th>Product</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#ORD-5289</td>
                                <td>John Doe</td>
                                <td>Nike Air Max</td>
                                <td>Aug 15, 2023</td>
                                <td>$129.99</td>
                                <td><span class="status completed">Completed</span></td>
                                <td>
                                    <button class="action-btn"><i class="fas fa-eye"></i></button>
                                    <button class="action-btn"><i class="fas fa-edit"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>#ORD-5288</td>
                                <td>Jane Smith</td>
                                <td>Adidas Ultraboost</td>
                                <td>Aug 15, 2023</td>
                                <td>$179.99</td>
                                <td><span class="status pending">Pending</span></td>
                                <td>
                                    <button class="action-btn"><i class="fas fa-eye"></i></button>
                                    <button class="action-btn"><i class="fas fa-edit"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>#ORD-5287</td>
                                <td>Robert Johnson</td>
                                <td>Puma RS-X</td>
                                <td>Aug 14, 2023</td>
                                <td>$99.99</td>
                                <td><span class="status cancelled">Cancelled</span></td>
                                <td>
                                    <button class="action-btn"><i class="fas fa-eye"></i></button>
                                    <button class="action-btn"><i class="fas fa-edit"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>#ORD-5286</td>
                                <td>Emily Davis</td>
                                <td>Jordan 1 Retro</td>
                                <td>Aug 14, 2023</td>
                                <td>$189.99</td>
                                <td><span class="status completed">Completed</span></td>
                                <td>
                                    <button class="action-btn"><i class="fas fa-eye"></i></button>
                                    <button class="action-btn"><i class="fas fa-edit"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="recent-section">
                    <div class="section-header">
                        <h3 class="section-title">Popular Products</h3>
                        <a href="#" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
                    </div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Sales</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Nike Air Max 270</td>
                                <td>Running</td>
                                <td>$150.00</td>
                                <td>45</td>
                                <td>128</td>
                                <td>
                                    <button class="action-btn"><i class="fas fa-eye"></i></button>
                                    <button class="action-btn"><i class="fas fa-edit"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>Adidas Ultraboost 21</td>
                                <td>Running</td>
                                <td>$180.00</td>
                                <td>32</td>
                                <td>96</td>
                                <td>
                                    <button class="action-btn"><i class="fas fa-eye"></i></button>
                                    <button class="action-btn"><i class="fas fa-edit"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>Jordan 1 Retro High</td>
                                <td>Basketball</td>
                                <td>$170.00</td>
                                <td>18</td>
                                <td>85</td>
                                <td>
                                    <button class="action-btn"><i class="fas fa-eye"></i></button>
                                    <button class="action-btn"><i class="fas fa-edit"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>Puma RS-X</td>
                                <td>Casual</td>
                                <td>$110.00</td>
                                <td>27</td>
                                <td>72</td>
                                <td>
                                    <button class="action-btn"><i class="fas fa-eye"></i></button>
                                    <button class="action-btn"><i class="fas fa-edit"></i></button>
                                </td>
                            </tr>
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