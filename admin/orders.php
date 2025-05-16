<?php
session_start();
include '../includes/auth.php';
include '../includes/db_connect.php';

// Redirect if not logged in as admin
if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$error = '';
$success = '';

// Update order status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $orderId = $_POST['order_id'];
    $status = $_POST['status'];

    $updateQuery = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("si", $status, $orderId);

    if ($stmt->execute()) {
        $success = "Order #" . $orderId . " status updated to " . $status;
    } else {
        $error = "Failed to update order status: " . $conn->error;
    }
}

// Pagination setup
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$recordsPerPage = 10;
$offset = ($page - 1) * $recordsPerPage;

// Search functionality
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$searchCondition = '';
if (!empty($search)) {
    $searchCondition = " AND (users.username LIKE '%$search%' OR orders.id LIKE '%$search%')";
}

// Count total records for pagination
$countQuery = "SELECT COUNT(*) as total FROM orders JOIN users ON orders.user_id = users.id WHERE 1=1" . $searchCondition;
$countResult = $conn->query($countQuery);
$totalRecords = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);

// Fetch orders with pagination and search
$orderQuery = "SELECT orders.id AS order_id, users.username, orders.created_at AS order_date, 
               orders.total, orders.status 
               FROM orders 
               JOIN users ON orders.user_id = users.id 
               WHERE 1=1" . $searchCondition . "
               ORDER BY orders.created_at DESC 
               LIMIT $offset, $recordsPerPage";
$ordersResult = $conn->query($orderQuery);

if ($ordersResult === false) {
    $error = "Error fetching orders: " . $conn->error;
}

// Get admin name from session or set a default
$adminName = isset($_SESSION['name']) ? $_SESSION['name'] : 'Admin';

// Format date function
function formatDate($date) {
    return date('M d, Y h:i A', strtotime($date));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management | KICKS Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/Adashboard.css">
    <style>
        /* Additional styles for order management */
        .status-badge {
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
        }
        .status-pending {
            background-color: #FFF4DE;
            color: #FFA800;
        }
        .status-completed {
            background-color: #E8FFF3;
            color: #1BC5BD;
        }
        .status-cancelled {
            background-color: #FFE2E5;
            color: #F64E60;
        }
        .search-container {
            display: flex;
            margin-bottom: 20px;
        }
        .search-container input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 5px 0 0 5px;
            font-size: 14px;
        }
        .search-container button {
            background: #3498db;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 5px;
        }
        .pagination a, .pagination span {
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
            border: 1px solid #ddd;
            color: #333;
            background-color: #f8f9fa;
        }
        .pagination a:hover {
            background-color: #e9ecef;
        }
        .pagination .active {
            background-color: #3498db;
            color: white;
            border-color: #3498db;
        }
        .pagination .disabled {
            color: #adb5bd;
            cursor: not-allowed;
        }
        .btn-update {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn-update:hover {
            background-color: #2980b9;
        }
        .status-select {
            padding: 6px 10px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            margin-right: 5px;
        }
        .inline-form {
            display: flex;
            align-items: center;
        }
        .alert {
            padding: 12px 20px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-weight: 500;
        }
        .alert-success {
            background-color: #E8FFF3;
            color: #1BC5BD;
            border-left: 4px solid #1BC5BD;
        }
        .alert-error {
            background-color: #FFE2E5;
            color: #F64E60;
            border-left: 4px solid #F64E60;
        }
        .order-summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .summary-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            flex: 1;
            margin: 0 10px;
            text-align: center;
        }
        .summary-card h3 {
            margin: 0;
            font-size: 14px;
            color: #6c757d;
        }
        .summary-card .value {
            font-size: 24px;
            font-weight: 600;
            margin: 10px 0;
        }
        .summary-card.pending .value {
            color: #FFA800;
        }
        .summary-card.completed .value {
            color: #1BC5BD;
        }
        .summary-card.cancelled .value {
            color: #F64E60;
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .section-title {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }
        .refresh-btn {
            background: none;
            border: none;
            color: #3498db;
            cursor: pointer;
            font-size: 16px;
        }
        @media (max-width: 768px) {
            .order-summary {
                flex-direction: column;
            }
            .summary-card {
                margin: 5px 0;
            }
            .search-container {
                flex-direction: column;
            }
            .search-container input, .search-container button {
                border-radius: 5px;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="dashboard-header">
            <h1 class="dashboard-title">
                <i class="fas fa-shopping-cart"></i>
                Order Management
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
                    <li><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="admin_products.php"><i class="fas fa-box"></i> Products</a></li>
                    <li><a href="#"><i class="fas fa-users"></i> Users</a></li>
                    <li><a href="admin_orders.php" class="active"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <li><a href="#"><i class="fas fa-chart-bar"></i> Reports</a></li>
                    <li><a href="#"><i class="fas fa-cog"></i> Settings</a></li>
                    <li><a href="../auth/logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
            
            <div class="main-content">
                <div class="section-header">
                    <h2 class="section-title">Order Management</h2>
                    <button class="refresh-btn" onclick="window.location.reload();">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php elseif ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <!-- Order Summary Cards -->
                <div class="order-summary">
                    <?php
                    // Get counts for each status
                    $statusCounts = [
                        'Pending' => 0,
                        'Completed' => 0,
                        'Cancelled' => 0
                    ];
                    
                    $countStatusQuery = "SELECT status, COUNT(*) as count FROM orders GROUP BY status";
                    $statusResult = $conn->query($countStatusQuery);
                    
                    if ($statusResult) {
                        while ($row = $statusResult->fetch_assoc()) {
                            if (isset($statusCounts[$row['status']])) {
                                $statusCounts[$row['status']] = $row['count'];
                            }
                        }
                    }
                    ?>
                    <div class="summary-card pending">
                        <h3>Pending Orders</h3>
                        <div class="value"><?php echo $statusCounts['Pending']; ?></div>
                    </div>
                    <div class="summary-card completed">
                        <h3>Completed Orders</h3>
                        <div class="value"><?php echo $statusCounts['Completed']; ?></div>
                    </div>
                    <div class="summary-card cancelled">
                        <h3>Cancelled Orders</h3>
                        <div class="value"><?php echo $statusCounts['Cancelled']; ?></div>
                    </div>
                </div>

                <!-- Search Form -->
                <form method="GET" action="admin_orders.php" class="search-container">
                    <input type="text" name="search" placeholder="Search by order ID or customer name..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>

                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Order Date</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($ordersResult && $ordersResult->num_rows > 0): ?>
                                <?php while ($order = $ordersResult->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $order['order_id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['username']); ?></td>
                                    <td><?php echo formatDate($order['order_date']); ?></td>
                                    <td>$<?php echo number_format($order['total'], 2); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                            <?php echo $order['status']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" class="inline-form" onsubmit="return confirm('Are you sure you want to update this order status?');">
                                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                            <select name="status" class="status-select">
                                                <option value="Pending" <?php if ($order['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                                <option value="Completed" <?php if ($order['status'] == 'Completed') echo 'selected'; ?>>Completed</option>
                                                <option value="Cancelled" <?php if ($order['status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                                            </select>
                                            <button type="submit" class="btn-update">Update</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 20px;">
                                        <?php if (!empty($search)): ?>
                                            <i class="fas fa-search" style="font-size: 24px; color: #adb5bd; margin-bottom: 10px;"></i>
                                            <p>No orders found matching your search criteria.</p>
                                        <?php else: ?>
                                            <i class="fas fa-shopping-cart" style="font-size: 24px; color: #adb5bd; margin-bottom: 10px;"></i>
                                            <p>No orders found in the system.</p>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=1<?php if (!empty($search)) echo '&search=' . urlencode($search); ?>"><i class="fas fa-angle-double-left"></i></a>
                        <a href="?page=<?php echo $page - 1; ?><?php if (!empty($search)) echo '&search=' . urlencode($search); ?>"><i class="fas fa-angle-left"></i></a>
                    <?php else: ?>
                        <span class="disabled"><i class="fas fa-angle-double-left"></i></span>
                        <span class="disabled"><i class="fas fa-angle-left"></i></span>
                    <?php endif; ?>
                    
                    <?php
                    $startPage = max(1, $page - 2);
                    $endPage = min($totalPages, $page + 2);
                    
                    for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="active"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?page=<?php echo $i; ?><?php if (!empty($search)) echo '&search=' . urlencode($search); ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?><?php if (!empty($search)) echo '&search=' . urlencode($search); ?>"><i class="fas fa-angle-right"></i></a>
                        <a href="?page=<?php echo $totalPages; ?><?php if (!empty($search)) echo '&search=' . urlencode($search); ?>"><i class="fas fa-angle-double-right"></i></a>
                    <?php else: ?>
                        <span class="disabled"><i class="fas fa-angle-right"></i></span>
                        <span class="disabled"><i class="fas fa-angle-double-right"></i></span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
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
            
            // Fade in alerts
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.5s ease';
                    setTimeout(() => {
                        alert.style.display = 'none';
                    }, 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>