<?php
session_start();
include '../includes/auth.php';
include '../includes/db_connect.php'; // Include database connection

if (!isLoggedIn() || $_SESSION['role'] !== 'superadmin') {
    header("Location: ../auth/login.php");
    exit();
}

// Handle admin account creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_admin'])) {
    $adminUsername = $_POST['admin_username'];
    $adminPassword = $_POST['admin_password'];
    $adminRole = $_POST['admin_role'];
    $hashedPassword = password_hash($adminPassword, PASSWORD_BCRYPT);

    // Check if username already exists
    $checkQuery = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("s", $adminUsername);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Username already exists. Please choose a different username.";
    } else {
        // Insert new admin user
        $insertQuery = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("sss", $adminUsername, $hashedPassword, $adminRole);

        if ($stmt->execute()) {
            echo "Admin account created successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    $stmt->close();
}

// Handle admin account deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_admin'])) {
    $adminId = $_POST['admin_id'];

    // Delete admin user
    $deleteQuery = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $adminId);

    if ($stmt->execute()) {
        echo "Admin account deleted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Handle admin account editing
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_admin'])) {
    $adminId = $_POST['admin_id'];
    $newUsername = $_POST['new_username'];
    $newRole = $_POST['new_role'];

    // Update admin user
    $updateQuery = "UPDATE users SET username = ?, role = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssi", $newUsername, $newRole, $adminId);

    if ($stmt->execute()) {
        echo "Admin account updated successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
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

// Query to get all admin users
$adminsQuery = "SELECT * FROM users WHERE role IN ('admin', 'cashier', 'customer')";
$adminsResult = $conn->query($adminsQuery);
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
    <style>
    /* Reset & Global Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

:root {
    --primary-color: #4CAF50;
    --primary-dark: #3e8e41;
    --secondary-color: #2d2d2d;
    --background-dark: #111315;
    --background-card: #1a1c1e;
    --text-light: #ffffff;
    --text-muted: rgba(255, 255, 255, 0.7);
    --text-price: #4CAF50;
    --border-radius-sm: 8px;
    --border-radius-md: 12px;
    --border-radius-lg: 16px;
    --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.15);
    --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.2);
    --transition-speed: 0.3s;
}

body {
    background-color: var(--background-dark);
    color: var(--text-light);
    font-family: 'Inter', sans-serif;
    line-height: 1.5;
    min-height: 100vh;
    overflow-x: hidden;
}

/* Scrollbar Styling */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: var(--background-dark);
}

::-webkit-scrollbar-thumb {
    background: var(--secondary-color);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Container Layout */
.container {
    padding: 20px;
}

/* Header Section */
.dashboard-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 15px 25px;
    background: linear-gradient(to right, var(--background-card), var(--background-dark));
    border-radius: var(--border-radius-md);
    margin-bottom: 20px;
    box-shadow: var(--shadow-sm);
    position: relative;
    overflow: hidden;
}

.dashboard-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(to right, transparent, var(--primary-color), transparent);
}

.dashboard-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--text-light);
    font-family: 'Poppins', sans-serif;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.dashboard-title i {
    font-size: 24px;
    color: var(--primary-color);
    filter: drop-shadow(0 0 2px rgba(76, 175, 80, 0.3));
}

.user-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.2);
}

.user-name {
    font-weight: 500;
    color: var(--text-light);
}

/* Dashboard Content */
.dashboard-content {
    display: grid;
    grid-template-columns: 250px 1fr;
    gap: 20px;
    min-height: calc(100vh - 150px);
}

/* Sidebar */
.sidebar {
    background: var(--background-card);
    border-radius: var(--border-radius-md);
    padding: 20px;
    box-shadow: var(--shadow-sm);
}

.sidebar ul {
    list-style: none;
}

.sidebar li {
    margin-bottom: 10px;
}

.sidebar a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    color: var(--text-light);
    text-decoration: none;
    border-radius: var(--border-radius-sm);
    transition: all var(--transition-speed) ease;
    position: relative;
    overflow: hidden;
    font-weight: 500;
}

.sidebar a::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background-color: transparent;
    transition: background-color var(--transition-speed) ease;
}

.sidebar a:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateX(5px);
}

.sidebar a:hover::after {
    background-color: var(--primary-color);
}

.sidebar a.active {
    background-color: var(--primary-color);
    color: white;
    box-shadow: 0 4px 8px rgba(76, 175, 80, 0.3);
}

.sidebar a.active::after {
    background-color: white;
}

.sidebar i {
    width: 20px;
    text-align: center;
    font-size: 18px;
}

.logout-btn {
    color: #ff6b6b !important;
    margin-top: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding-top: 15px !important;
}

/* Main Content */
.main-content {
    background: var(--background-card);
    border-radius: var(--border-radius-md);
    padding: 25px;
    box-shadow: var(--shadow-sm);
    overflow-y: auto;
}

.main-content h2 {
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 10px;
    color: var(--text-light);
}

.main-content p {
    color: var(--text-muted);
    margin-bottom: 25px;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: var(--secondary-color);
    border-radius: var(--border-radius-md);
    padding: 20px;
    box-shadow: var(--shadow-sm);
    position: relative;
    overflow: hidden;
    transition: transform var(--transition-speed) ease;
    border-left: 4px solid var(--primary-color);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-md);
}

.stat-card.products {
    border-left-color: var(--primary-color);
}

.stat-card.users {
    border-left-color: #3498db;
}

.stat-card.orders {
    border-left-color: #f39c12;
}

.stat-card.revenue {
    border-left-color: #9b59b6;
}

.stat-card h3 {
    font-size: 16px;
    color: var(--text-muted);
    margin-bottom: 10px;
    font-weight: 500;
}

.stat-card .value {
    font-size: 28px;
    font-weight: 700;
    color: var(--text-light);
}

.stat-card .icon {
    position: absolute;
    top: 20px;
    right: 20px;
    font-size: 40px;
    opacity: 0.2;
    color: var(--text-light);
}

/* Recent Sections */
.recent-section {
    margin-top: 30px;
    background: var(--secondary-color);
    border-radius: var(--border-radius-md);
    padding: 20px;
    box-shadow: var(--shadow-sm);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.section-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--text-light);
}

.view-all {
    color: var(--primary-color);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: color var(--transition-speed) ease;
}

.view-all:hover {
    color: var(--primary-dark);
    text-decoration: underline;
}

/* Tables */
table {
    width: 100%;
    border-collapse: collapse;
}

table th, table td {
    padding: 14px 15px;
    text-align: left;
}

table th {
    background-color: rgba(255, 255, 255, 0.05);
    font-weight: 600;
    color: var(--text-light);
    position: sticky;
    top: 0;
}

table tr {
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    transition: background-color var(--transition-speed) ease;
}

table tr:hover {
    background-color: rgba(255, 255, 255, 0.03);
}

table tr:last-child {
    border-bottom: none;
}

/* Status Badges */
.status {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
    display: inline-block;
}

.status.completed {
    background-color: rgba(46, 204, 113, 0.15);
    color: #2ecc71;
}

.status.pending {
    background-color: rgba(243, 156, 18, 0.15);
    color: #f39c12;
}

.status.cancelled {
    background-color: rgba(231, 76, 60, 0.15);
    color: #e74c3c;
}

/* Action Buttons */
.action-btn {
    background: rgba(255, 255, 255, 0.1);
    border: none;
    width: 32px;
    height: 32px;
    border-radius: var(--border-radius-sm);
    cursor: pointer;
    color: var(--text-light);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all var(--transition-speed) ease;
    margin-right: 5px;
}

.action-btn:hover {
    background: var(--primary-color);
    color: white;
    transform: translateY(-2px);
}

/* Animation for new items */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.stat-card, .recent-section {
    animation: fadeIn 0.3s ease-out;
}

/* Responsive Adjustments */
@media (max-width: 1200px) {
    .dashboard-content {
        grid-template-columns: 1fr;
    }
    
    .sidebar {
        display: flex;
        overflow-x: auto;
        padding: 15px;
    }
    
    .sidebar ul {
        display: flex;
        gap: 10px;
    }
    
    .sidebar li {
        margin-bottom: 0;
    }
    
    .sidebar a {
        white-space: nowrap;
    }
    
    .logout-btn {
        margin-top: 0;
        border-top: none;
        padding-top: 14px !important;
    }
}

@media (max-width: 768px) {
    .dashboard-header {
        flex-direction: column;
        gap: 15px;
        padding: 15px;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    table {
        display: block;
        overflow-x: auto;
    }
}

    </style>
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
                        <h3 class="section-title">Manage Admin Accounts</h3>
                    </div>
                    
                    <form method="POST" action="">
                        <h4>Create New Admin</h4>
                        <label for="admin_username">Username:</label>
                        <input type="text" id="admin_username" name="admin_username" required>
                        <br>
                        <label for="admin_password">Password:</label>
                        <input type="password" id="admin_password" name="admin_password" required>
                        <br>
                        <label for="admin_role">Role:</label>
                        <select id="admin_role" name="admin_role" required>
                            <option value="admin">Admin</option>
                            <option value="cashier">Cashier</option>
                            <option value="customer">Customer</option>
                        </select>
                        <br>
                        <button type="submit" name="create_admin">Create Admin</button>
                    </form>

                    <h4>Existing Admins</h4>
                    <table>
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($admin = $adminsResult->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $admin['username']; ?></td>
                                    <td><?php echo $admin['role']; ?></td>
                                    <td>
                                        <form method="POST" action="" style="display:inline;">
                                            <input type="hidden" name="admin_id" value="<?php echo $admin['id']; ?>">
                                            <input type="text" name="new_username" value="<?php echo $admin['username']; ?>" required>
                                            <select name="new_role" required>
                                                <option value="admin" <?php if ($admin['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                                                <option value="cashier" <?php if ($admin['role'] == 'cashier') echo 'selected'; ?>>Cashier</option>
                                                <option value="customer" <?php if ($admin['role'] == 'customer') echo 'selected'; ?>>Customer</option>
                                            </select>
                                            <button type="submit" name="edit_admin" class="action-btn"><i class="fas fa-edit"></i> Update</button>
                                            <button type="submit" name="delete_admin" class="action-btn"><i class="fas fa-trash"></i> Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
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