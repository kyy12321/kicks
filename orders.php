
<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/auth.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: auth/login.php');
    exit();
}

$user = getCurrentUser();
$userId = $user['id'];

 // Get user orders
$stmt = $conn->prepare("
    SELECT 
        o.order_id AS id,
        o.status,
        o.total,
        o.payment_method,
        o.created_at,
        s.shipping_address,
        s.tracking_number,
        COUNT(DISTINCT oi.product_id) as item_count,
        SUM(oi.quantity) as total_items
    FROM orders o
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
    LEFT JOIN shipping s ON o.order_id = s.order_id
    WHERE o.user_id = ?
    GROUP BY o.order_id
    ORDER BY o.created_at DESC
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KICKS | My Orders</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Import Google Fonts */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        /* Reset & Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Theme Variables */
        :root {
            /* Dark Theme (Default) */
            --primary-color: #4CAF50;
            --primary-dark: #3e8e41;
            --primary-light: rgba(76, 175, 80, 0.1);
            --secondary-color: #2d2d2d;
            --background-dark: #111315;
            --background-card: #1a1c1e;
            --text-light: #ffffff;
            --text-muted: rgba(255, 255, 255, 0.7);
            --text-price: #4CAF50;
            --error-color: #f44336;
            --warning-color: #ff9800;
            --info-color: #2196f3;
            --border-color: rgba(255, 255, 255, 0.1);
            --border-radius-sm: 8px;
            --border-radius-md: 12px;
            --border-radius-lg: 16px;
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.15);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.2);
            --transition-speed: 0.3s;
        }

        /* Light Theme */
        [data-theme="light"] {
            --primary-color: #4CAF50;
            --primary-dark: #3e8e41;
            --primary-light: rgba(76, 175, 80, 0.1);
            --secondary-color: #f5f5f5;
            --background-dark: #f8f9fa;
            --background-card: #ffffff;
            --text-light: #333333;
            --text-muted: rgba(0, 0, 0, 0.6);
            --text-price: #4CAF50;
            --error-color: #f44336;
            --warning-color: #ff9800;
            --info-color: #2196f3;
            --border-color: rgba(0, 0, 0, 0.1);
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        body {
            background-color: var(--background-dark);
            color: var(--text-light);
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            line-height: 1.5;
            position: relative;
            overflow-x: hidden;
            padding-top: 80px;
            transition: background-color var(--transition-speed) ease;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at top right, rgba(76, 175, 80, 0.05), transparent 70%);
            z-index: -1;
        }

        /* Navigation Bar */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 5%;
            height: 80px;
            background-color: rgba(26, 28, 30, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        [data-theme="light"] .navbar {
            background-color: rgba(255, 255, 255, 0.95);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .nav-logo {
            display: flex;
            align-items: center;
            text-decoration: none;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
        }

        .shoe-icon {
            font-size: 24px;
            color: var(--primary-color);
            transition: transform 0.3s ease;
        }
        
        .nav-logo:hover .shoe-icon {
            transform: rotate(15deg) translateX(3px);
        }

        .nav-logo-text {
            display: flex;
            font-size: 28px;
            font-weight: 700;
            font-family: 'Poppins', sans-serif;
        }
        
        .letter {
            background: linear-gradient(90deg, #4CAF50, #3e8e41);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            transition: transform 0.3s ease, opacity 0.3s ease;
            display: inline-block;
        }
        
        .nav-logo:hover .letter {
            animation: bounce 0.5s ease;
            animation-fill-mode: both;
        }
        
        .nav-logo:hover .letter:nth-child(1) { animation-delay: 0s; }
        .nav-logo:hover .letter:nth-child(2) { animation-delay: 0.1s; }
        .nav-logo:hover .letter:nth-child(3) { animation-delay: 0.2s; }
        .nav-logo:hover .letter:nth-child(4) { animation-delay: 0.3s; }
        .nav-logo:hover .letter:nth-child(5) { animation-delay: 0.4s; }

        .nav-links {
            display: flex;
            gap: 30px;
        }

        .nav-link {
            color: var(--text-light);
            text-decoration: none;
            font-weight: 500;
            font-size: 16px;
            position: relative;
            padding: 5px 0;
            transition: all 0.3s ease;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--primary-color);
            transition: width 0.3s ease;
        }

        .nav-link:hover {
            color: var(--primary-color);
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .nav-buttons {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        /* Theme Toggle */
        .theme-toggle {
            display: flex;
            align-items: center;
            margin-right: 15px;
            cursor: pointer;
        }

        .theme-toggle-track {
            position: relative;
            width: 50px;
            height: 24px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            padding: 0 4px;
            justify-content: space-between;
        }

        [data-theme="light"] .theme-toggle-track {
            background-color: rgba(0, 0, 0, 0.1);
        }

        .theme-toggle-thumb {
            position: absolute;
            left: 2px;
            width: 20px;
            height: 20px;
            background-color: var(--primary-color);
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        [data-theme="light"] .theme-toggle-thumb {
            left: 28px;
        }

        .theme-icon {
            font-size: 12px;
            color: var(--text-light);
            z-index: 1;
        }

        /* User Profile */
        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            position: relative;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: var(--primary-color);
        }

        .user-name {
            font-weight: 500;
        }

        .user-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            width: 200px;
            background-color: var(--background-card);
            border-radius: var(--border-radius-md);
            box-shadow: var(--shadow-md);
            padding: 10px 0;
            z-index: 1000;
            display: none;
            transition: background-color var(--transition-speed) ease, box-shadow var(--transition-speed) ease;
        }

        .user-dropdown.active {
            display: block;
            animation: fadeInDown 0.3s ease forwards;
        }

        .dropdown-item {
            padding: 10px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-light);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background-color: rgba(255, 255, 255, 0.05);
            color: var(--primary-color);
        }

        [data-theme="light"] .dropdown-item:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .dropdown-divider {
            height: 1px;
            background-color: var(--border-color);
            margin: 5px 0;
            transition: background-color var(--transition-speed) ease;
        }

        /* Orders Container */
        .orders-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .orders-header {
            margin-bottom: 30px;
        }

        .orders-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .orders-breadcrumb {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-muted);
            font-size: 14px;
        }

        .breadcrumb-link {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .breadcrumb-link:hover {
            color: var(--primary-color);
        }

        /* Orders List */
        .orders-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .order-card {
            background-color: var(--background-card);
            border-radius: var(--border-radius-lg);
            padding: 25px;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
        }

        .order-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .order-info {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .order-number {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-light);
        }

        .order-date {
            font-size: 14px;
            color: var(--text-muted);
        }

        .order-status {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            text-transform: uppercase;
        }

        .status-pending {
            background-color: rgba(255, 152, 0, 0.1);
            color: var(--warning-color);
        }

        .status-processing {
            background-color: rgba(33, 150, 243, 0.1);
            color: var(--info-color);
        }

        .status-shipped {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--primary-color);
        }

        .status-delivered {
            background-color: rgba(76, 175, 80, 0.2);
            color: var(--primary-color);
        }

        .status-cancelled {
            background-color: rgba(244, 67, 54, 0.1);
            color: var(--error-color);
        }

        .order-details {
            display: grid;
            
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .detail-label {
            font-size: 12px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            font-size: 16px;
            font-weight: 500;
            color: var(--text-light);
        }

        .order-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }

        .order-total {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .total-label {
            font-size: 16px;
            color: var(--text-muted);
        }

        .total-amount {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-price);
        }

        .view-order-btn {
            padding: 10px 20px;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: var(--border-radius-sm);
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .view-order-btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background-color: var(--background-card);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-sm);
        }

        .empty-icon {
            font-size: 80px;
            color: var(--text-muted);
            margin-bottom: 20px;
        }

        .empty-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--text-light);
        }

        .empty-message {
            font-size: 16px;
            color: var(--text-muted);
            margin-bottom: 30px;
        }

        .shop-now-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 30px;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: var(--border-radius-sm);
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .shop-now-btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
        }

        /* Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar {
                padding: 0 3%;
            }

            .nav-links {
                display: none;
            }

            .orders-container {
                padding: 15px;
            }

            .order-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .order-details {
                grid-template-columns: 1fr;
            }

            .order-footer {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
        }

        /* --- Cancel Order Modal Container --- */
        .cancel-modal-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            width: 100vw;
            position: fixed;
            top: 0; left: 0;
            z-index: 2100;
            background: rgba(0,0,0,0.55);
            transition: opacity 0.3s;
            opacity: 0;
            pointer-events: none;
        }
        .cancel-modal-container.active {
            opacity: 1;
            pointer-events: auto;
        }

        /* --- Modal Styling Improvements --- */
        .cancel-modal-container .modal {
            background: var(--background-card);
            border-radius: var(--border-radius-lg);
            box-shadow: 0 8px 32px rgba(0,0,0,0.25), 0 1.5px 8px rgba(76,175,80,0.08);
            max-width: 420px;
            width: 95%;
            padding: 32px 28px 24px 28px;
            border: 2px solid var(--error-color);
            position: relative;
            animation: fadeInDown 0.35s cubic-bezier(.23,1.01,.32,1) both;
        }

        .cancel-modal-container .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .cancel-modal-container .modal-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--error-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cancel-modal-container .modal-close {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 26px;
            cursor: pointer;
            transition: color 0.2s;
        }
        .cancel-modal-container .modal-close:hover {
            color: var(--error-color);
        }

        .cancel-modal-container .modal-body {
            margin-bottom: 28px;
            color: var(--text-muted);
            font-size: 16px;
            line-height: 1.7;
        }

        .cancel-modal-container .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 14px;
        }

        .cancel-modal-container .modal-btn {
            padding: 10px 22px;
            border-radius: var(--border-radius-sm);
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            border: none;
            transition: background 0.2s, color 0.2s;
        }

        .cancel-modal-container .btn-cancel {
            background: transparent;
            color: var(--text-muted);
            border: 1.5px solid var(--border-color);
        }
        .cancel-modal-container .btn-cancel:hover {
            background: rgba(255,255,255,0.07);
            color: var(--text-light);
        }

        .cancel-modal-container .btn-confirm {
            background: var(--error-color);
            color: #fff;
            border: none;
        }
        .cancel-modal-container .btn-confirm:hover {
            background: #d32f2f;
            color: #fff;
        }

        @media (max-width: 600px) {
            .cancel-modal-container .modal {
                padding: 18px 8px 14px 8px;
                max-width: 98vw;
            }
            .cancel-modal-container .modal-title {
                font-size: 18px;
            }
        }
    </style>
</head>
<body data-theme="dark">
    <!-- Navigation Bar -->
    <nav class="navbar">
        <a href="index.php" class="nav-logo">
            <div class="logo-container">
                <i class="fas fa-shoe-prints shoe-icon"></i>
                <div class="nav-logo-text">
                    <span class="letter">K</span>
                    <span class="letter">I</span>
                    <span class="letter">C</span>
                    <span class="letter">K</span>
                    <span class="letter">S</span>
                </div>
            </div>
        </a>
        
        <div class="nav-links">
            <a href="index.php" class="nav-link">Home</a>
            <a href="ecomm.php" class="nav-link">Shop</a>
            <a href="about.php" class="nav-link">About</a>
            <a href="contact.php" class="nav-link">Contact</a>
        </div>
        
        <div class="nav-buttons">
            <div class="theme-toggle" onclick="toggleTheme()">
                <div class="theme-toggle-track">
                    <i class="fas fa-moon theme-icon"></i>
                    <i class="fas fa-sun theme-icon"></i>
                    <div class="theme-toggle-thumb"></div>
                </div>
            </div>
            
            <div class="user-profile" onclick="toggleDropdown()">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                </div>
                <span class="user-name"><?php echo htmlspecialchars($user['username']); ?></span>
                <i class="fas fa-chevron-down" style="font-size: 12px;"></i>
                
                <div class="user-dropdown" id="userDropdown">
                    <a href="profile.php" class="dropdown-item">
                        <i class="fas fa-user"></i>
                        Profile
                    </a>
                    <a href="orders.php" class="dropdown-item">
                        <i class="fas fa-box"></i>
                        Orders
                    </a>
                    <a href="wishlist.php" class="dropdown-item">
                        <i class="fas fa-heart"></i>
                        Wishlist
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="auth/logout.php" class="dropdown-item">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

     <div class="orders-container">
        <div class="orders-header">
            <h1 class="orders-title">My Orders</h1>
            <div class="orders-breadcrumb">
                <a href="index.php" class="breadcrumb-link">Home</a>
                <i class="fas fa-chevron-right"></i>
                <span>Orders</span>
            </div>
        </div>

        <?php if (empty($orders)): ?>
            <!-- Keep existing empty state -->
        <?php else: ?>
            <div class="orders-list">
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-info">
                                <div class="order-number">
                                    Order #<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?>
                                </div>
                                <div class="order-date">
                                    <?= date('F j, Y \a\t h:i A', strtotime($order['created_at'])) ?>
                                </div>
                            </div>
                            <div class="order-status status-<?= strtolower($order['status']) ?>">
                                <?= ucfirst(strtolower($order['status'])) ?>
                            </div>
                        </div>

                        <!-- Add Order Timeline -->
                        <div class="order-timeline">
                            <div class="timeline-step <?= $order['status'] === 'processing' ? 'active' : '' ?>">
                                <div class="timeline-icon">
                                    <i class="fas fa-box"></i>
                                </div>
                                <span class="timeline-label">Processing</span>
                            </div>
                            <div class="timeline-step <?= $order['status'] === 'shipped' ? 'active' : '' ?>">
                                <div class="timeline-icon">
                                    <i class="fas fa-shipping-fast"></i>
                                </div>
                                <span class="timeline-label">Shipped</span>
                            </div>
                            <div class="timeline-step <?= $order['status'] === 'delivered' ? 'active' : '' ?>">
                                <div class="timeline-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <span class="timeline-label">Delivered</span>
                            </div>
                        </div>

                        <div class="order-details">
                            <div class="detail-item">
                                <span class="detail-label">Items</span>
                                <span class="detail-value">
                                    <?= $order['total_items'] ?> items (<?= $order['item_count'] ?> products)
                                </span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Shipping Address</span>
                                <span class="detail-value"><?= htmlspecialchars($order['shipping_address']) ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Payment Method</span>
                                <span class="detail-value">
                                    <?= match($order['payment_method']) {
                                        'credit_card' => 'Credit Card',
                                        'gcash' => 'GCash',
                                        'cod' => 'Cash on Delivery',
                                        default => 'Unknown'
                                    } ?>
                                </span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Tracking Number</span>
                                <span class="detail-value">
                                    <?= $order['tracking_number'] ?: 
                                        '<span class="text-muted">Will be updated soon</span>' ?>
                                </span>
                            </div>
                        </div>

                        <div class="order-footer">
                            <div class="order-total">
                                <span class="total-label">Total:</span>
                                <span class="total-amount">₱<?= number_format($order['total'], 2) ?></span>
                            </div>
                            <div class="order-actions">
                                <?php if ($order['status'] === 'pending'): ?>
                                    <a href="#" class="cancel-order-btn" onclick="showCancelModal(<?= $order['id'] ?>); return false;">
                                        Cancel Order
                                    </a>
                                <?php endif; ?>
                                <a href="order-details.php?id=<?= $order['id'] ?>" class="view-order-btn">
                                    View Details
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Cancel Order Modal -->
    <div class="cancel-modal-container" id="cancelModal">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i>
                    Cancel Order
                </div>
                <button class="modal-close" onclick="hideCancelModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel this order? This action cannot be undone.</p>
                <p>If you've already been charged, the refund process will be initiated according to our refund policy.</p>
            </div>
            <div class="modal-footer">
                <button class="modal-btn btn-cancel" onclick="hideCancelModal()">No, Keep Order</button>
                <form id="cancelOrderForm" method="POST" style="margin: 0;">
                    <input type="hidden" name="order_id" id="cancelOrderId" value="">
                    <input type="hidden" name="cancel_order" value="1">
                    <button type="submit" class="modal-btn btn-confirm">Yes, Cancel Order</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Theme Toggle
        function toggleTheme() {
            const body = document.body;
            const currentTheme = body.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            body.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        }

        // Load saved theme
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.body.setAttribute('data-theme', savedTheme);
        });

        // User Dropdown Toggle
        function toggleDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('active');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const userProfile = document.querySelector('.user-profile');
            const dropdown = document.getElementById('userDropdown');
            
            if (!userProfile.contains(event.target)) {
                dropdown.classList.remove('active');
            }
        });

        // Cancel Order Modal
        function showCancelModal(orderId) {
            document.getElementById('cancelOrderId').value = orderId;
            var modal = document.getElementById('cancelModal');
            modal.classList.add('active');
        }

        function hideCancelModal() {
            var modal = document.getElementById('cancelModal');
            modal.classList.remove('active');
        }

        // Optional: Submit the form via JS for debugging
        document.getElementById('cancelOrderForm').addEventListener('submit', function(e) {
            // Optionally, you can add a confirmation here
            // e.preventDefault(); // Uncomment to debug without submitting
            // alert('Submitting order cancel for order_id: ' + document.getElementById('cancelOrderId').value);
        });
    </script>
</body>
</html>
