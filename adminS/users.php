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
        $createError = "Username already exists. Please choose a different username.";
    } else {
        // Insert new admin user
        $insertQuery = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("sss", $adminUsername, $hashedPassword, $adminRole);

        if ($stmt->execute()) {
            $createSuccess = "User account created successfully!";
        } else {
            $createError = "Error: " . $stmt->error;
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
        $deleteSuccess = "User account deleted successfully!";
    } else {
        $deleteError = "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Handle admin account editing - MODIFIED to only update role
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_admin'])) {
    $adminId = $_POST['admin_id'];
    $newRole = $_POST['new_role'];

    // Update admin user role only
    $updateQuery = "UPDATE users SET role = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("si", $newRole, $adminId);

    if ($stmt->execute()) {
        $updateSuccess = "User role updated successfully!";
    } else {
        $updateError = "Error: " . $stmt->error;
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

// Query to get the total number of cashier users
$cashierCountQuery = "SELECT COUNT(*) as total_cashiers FROM users WHERE role = 'cashier'";
$cashierCountResult = $conn->query($cashierCountQuery);
$totalCashiers = 0;

if ($cashierCountResult && $cashierCountResult->num_rows > 0) {
    $row = $cashierCountResult->fetch_assoc();
    $totalCashiers = $row['total_cashiers'];
}

// Query to get the total number of customer users
$customerCountQuery = "SELECT COUNT(*) as total_customers FROM users WHERE role = 'customer'";
$customerCountResult = $conn->query($customerCountQuery);
$totalCustomers = 0;

if ($customerCountResult && $customerCountResult->num_rows > 0) {
    $row = $customerCountResult->fetch_assoc();
    $totalCustomers = $row['total_customers'];
}

// Query to get admin and cashier users
$staffQuery = "SELECT * FROM users WHERE role IN ('admin', 'cashier')";
$staffResult = $conn->query($staffQuery);

// Query to get customer users
$customerQuery = "SELECT * FROM users WHERE role = 'customer'";
$customerResult = $conn->query($customerQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management | KICKS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Import Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="users.css">
</head>
<body>
    <div class="container">
        <div class="dashboard-header">
            <h1 class="dashboard-title">
                <i class="fas fa-users"></i>
                User Management
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
                    <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="users.php" class="active"><i class="fas fa-users"></i> Users</a></li>
                    <li><a href="products.php"><i class="fas fa-box"></i> Products</a></li>
                    <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
                    <li><a href="../auth/logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
            
            <div class="main-content">
                <!-- Status Messages -->
                <?php if(isset($createSuccess)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $createSuccess; ?>
                    </div>
                <?php endif; ?>
                
                <?php if(isset($createError)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $createError; ?>
                    </div>
                <?php endif; ?>
                
                <?php if(isset($updateSuccess)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $updateSuccess; ?>
                    </div>
                <?php endif; ?>
                
                <?php if(isset($deleteSuccess)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $deleteSuccess; ?>
                    </div>
                <?php endif; ?>
                
                <!-- User Stats Overview -->
                <div class="stats-grid">
                    <div class="stat-card customer">
                        <h3>Admin Users</h3>
                        <div class="value"><?php echo $totalAdmins; ?></div>
                        <div class="icon"><i class="fas fa-user-shield"></i></div>
                    </div>
                    <div class="stat-card cashier">
                        <h3>Cashier Users</h3>
                        <div class="value"><?php echo $totalCashiers; ?></div>
                        <div class="icon"><i class="fas fa-cash-register"></i></div>
                    </div>
                    <div class="stat-card admins">
                        <h3>Customer Users</h3>
                        <div class="value"><?php echo $totalCustomers; ?></div>
                        <div class="icon"><i class="fas fa-user"></i></div>
                    </div>
                </div>
                
                <!-- Create New User Section -->
                <div class="management-section">
                    <div class="section-header">
                        <h2 class="section-title">Create New User Account</h2>
                    </div>
                    
                    <form method="POST" action="" class="user-management-form">
                        <div class="form-group">
                            <label for="admin_username">
                                <i class="fas fa-user"></i> Username
                            </label>
                            <input type="text" id="admin_username" name="admin_username" required placeholder="Enter username">
                        </div>
                        
                        <div class="form-group">
                            <label for="admin_password">
                                <i class="fas fa-lock"></i> Password
                            </label>
                            <input type="password" id="admin_password" name="admin_password" required placeholder="Enter password">
                        </div>
                        
                        <div class="form-group">
                            <label for="admin_role">
                                <i class="fas fa-user-tag"></i> Role
                            </label>
                            <select id="admin_role" name="admin_role" required>
                                <option value="">Select role</option>
                                <option value="admin">Admin</option>
                                <option value="cashier">Cashier</option>
                                <option value="customer">Customer</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" name="create_admin">
                                <i class="fas fa-plus-circle"></i> Create User
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Staff Accounts Section (Admin & Cashier) -->
                <div class="management-section">
                    <div class="section-header">
                        <h2 class="section-title">Staff Accounts</h2>
                        <div class="user-count">
                            <span class="badge"><?php echo $staffResult->num_rows; ?> staff members</span>
                        </div>
                    </div>
                    
                    <table class="user-management-table">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($staffResult->num_rows > 0): ?>
                                <?php while ($staff = $staffResult->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <div class="user-info-cell">
                                                <div class="user-avatar-small">
                                                    <?php echo substr($staff['username'], 0, 1); ?>
                                                </div>
                                                <?php echo $staff['username']; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="role-badge role-<?php echo strtolower($staff['role']); ?>">
                                                <?php echo ucfirst($staff['role']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <form method="POST" action="" class="table-form">
                                                <input type="hidden" name="admin_id" value="<?php echo $staff['id']; ?>">
                                                <select name="new_role" required>
                                                    <option value="admin" <?php if ($staff['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                                                    <option value="cashier" <?php if ($staff['role'] == 'cashier') echo 'selected'; ?>>Cashier</option>
                                                    <option value="customer" <?php if ($staff['role'] == 'customer') echo 'selected'; ?>>Customer</option>
                                                </select>
                                                <button type="submit" name="edit_admin" class="action-btn">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                                <button type="submit" name="delete_admin" class="action-btn delete" onclick="return confirm('Are you sure you want to delete this user?');">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="no-data">No staff accounts found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Customer Accounts Section -->
                <div class="management-section">
                    <div class="section-header">
                        <h2 class="section-title">Customer Accounts</h2>
                        <div class="user-count">
                            <span class="badge"><?php echo $customerResult->num_rows; ?> customers</span>
                        </div>
                    </div>
                    
                    <table class="user-management-table">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($customerResult->num_rows > 0): ?>
                                <?php while ($customer = $customerResult->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <div class="user-info-cell">
                                                <div class="user-avatar-small customer-avatar">
                                                    <?php echo substr($customer['username'], 0, 1); ?>
                                                </div>
                                                <?php echo $customer['username']; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="role-badge role-customer">
                                                <?php echo ucfirst($customer['role']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <form method="POST" action="" class="table-form">
                                                <input type="hidden" name="admin_id" value="<?php echo $customer['id']; ?>">
                                                <select name="new_role" required>
                                                    <option value="admin">Admin</option>
                                                    <option value="cashier">Cashier</option>
                                                    <option value="customer" selected>Customer</option>
                                                </select>
                                                <button type="submit" name="edit_admin" class="action-btn">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                                <button type="submit" name="delete_admin" class="action-btn delete" onclick="return confirm('Are you sure you want to delete this user?');">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="no-data">No customer accounts found</td>
                                </tr>
                            <?php endif; ?>
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
            
            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.style.display = 'none';
                    }, 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>
</qodoArtifact>