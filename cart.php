<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/auth.php';

// Get cart items from session
$cartItems = $_SESSION['cart'] ?? [];

// Calculate order summary
$subtotal = 0;
$totalItems = 0;

foreach ($cartItems as $productId => $item) {
    $stmt = $conn->prepare("SELECT price, name, image_path FROM products WHERE id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    
    if ($product) {
        $subtotal += $product['price'] * $item['quantity'];
        $totalItems += $item['quantity'];
    }
}
// After creating the order and getting $order_id
$payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : 'online';
$stmt = $conn->prepare("INSERT INTO payments (order_id, method, status) VALUES (?, ?, 'completed')");
$stmt->bind_param("is", $order_id, $payment_method);
$stmt->execute();


$shipping = $totalItems > 0 ? 50 : 0; // Flat shipping rate
$tax = $subtotal * 0.12; // 12% tax
$total = $subtotal + $shipping + $tax;
?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="error-notification">
        <?= $_SESSION['error'] ?>
        <?php unset($_SESSION['error']) ?>
    </div>
<?php endif; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KICKS | Shopping Cart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .error-notification {
            position: fixed;
            top: 100px;
            left: 50%;
            transform: translateX(-50%);
            background-color: var(--error-color);
            color: white;
            padding: 15px 30px;
            border-radius: var(--border-radius-sm);
            z-index: 10000;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from { transform: translate(-50%, -100px); }
            to { transform: translate(-50%, 0); }
        }

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

        /* Updated Navbar Styles */
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
        .navbar.scrolled {
            height: 70px;
            background-color: rgba(17, 19, 21, 0.98);
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
        /* Logo Consistency */
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

        /* Nav Links Consistency */
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

        /* User Profile Consistency */
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
        /* User Dropdown Consistency */
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

        /* Cart Button Consistency */
        .btn-register {
            color: white;
            background-color: var(--primary-color);
            box-shadow: 0 4px 10px rgba(76, 175, 80, 0.3);
            padding: 10px 20px;
            border-radius: var(--border-radius-sm);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-register:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(76, 175, 80, 0.4);
        }

        /* Mobile Menu Consistency */
        .hamburger {
            display: none;
            cursor: pointer;
            width: 30px;
            height: 20px;
            position: relative;
            z-index: 1001;
        }
        .hamburger span {
            display: block;
            position: absolute;
            height: 2px;
            width: 100%;
            background: var(--text-light);
            border-radius: 2px;
            transition: all 0.3s ease;
        }
        .mobile-menu {
            position: fixed;
            top: 0;
            right: -100%;
            width: 80%;
            height: 100vh;
            background-color: var(--background-card);
            z-index: 1000;
            padding: 100px 40px 40px;
            transition: right 0.4s ease;
            box-shadow: -5px 0 30px rgba(0, 0, 0, 0.3);
        }
        .mobile-menu.active {
            right: 0;
        }
        .mobile-nav-links {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }
        .mobile-nav-link {
            color: var(--text-light);
            text-decoration: none;
            font-size: 18px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        .overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .cart-content {
                grid-template-columns: 1fr;
            }
            .order-summary {
                position: static;
            }
        }
        @media (max-width: 768px) {
            .navbar {
                padding: 0 3%;
            }
            .nav-links, .nav-buttons {
                display: none;
            }
            .hamburger {
                display: block;
            }
            .cart-item {
                grid-template-columns: 100px 1fr;
                gap: 15px;
            }
            .cart-item-image {
                width: 100px;
                height: 100px;
            }
            .cart-item-price {
                grid-column: 2;
                text-align: left;
                margin-top: 10px;
            }
            .mobile-nav-links {
                display: flex;
                flex-direction: column;
                gap: 25px;
            }
            .mobile-nav-link {
                color: var(--text-light);
                text-decoration: none;
                font-size: 18px;
                font-weight: 500;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.7);
                z-index: 999;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
            }
            .overlay.active {
                opacity: 1;
                visibility: visible;
            }
        }

        /* Cart Container */
        .cart-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .cart-header {
            margin-bottom: 30px;
        }

        .cart-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .cart-breadcrumb {
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

        /* Cart Content */
        .cart-content {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
        }

        /* Cart Items Section */
        .cart-items-section {
            background-color: var(--background-card);
            border-radius: var(--border-radius-lg);
            padding: 30px;
            box-shadow: var(--shadow-sm);
        }

        .cart-items-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .items-count {
            font-size: 18px;
            font-weight: 600;
        }

        .clear-cart-btn {
            background: none;
            border: none;
            color: var(--error-color);
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .clear-cart-btn:hover {
            text-decoration: underline;
        }

        /* Cart Item */
        .cart-item {
            display: grid;
            grid-template-columns: 120px 1fr auto;
            gap: 20px;
            padding: 20px 0;
            border-bottom: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-item-image {
            width: 120px;
            height: 120px;
            border-radius: var(--border-radius-md);
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .cart-item-image:hover {
            transform: scale(1.05);
        }

        .cart-item-details {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .item-name {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .item-info {
            display: flex;
            gap: 15px;
            color: var(--text-muted);
            font-size: 14px;
            margin-bottom: 10px;
        }

        .item-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-sm);
            overflow: hidden;
        }

        .quantity-btn {
            width: 35px;
            height: 35px;
            background-color: transparent;
            color: var(--text-light);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .quantity-btn:hover {
            background-color: var(--primary-light);
            color: var(--primary-color);
        }

        
        .quantity-input {
            width: 50px;
            text-align: center;
            border: none;
            background-color: transparent;
            color: var(--text-light);
            font-weight: 500;
        }

        .remove-item {
            color: var(--text-muted);
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .remove-item:hover {
            color: var(--error-color);
        }

        .cart-item-price {
            text-align: right;
            min-width: 100px;
        }

        .item-price {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-price);
        }

        .item-original-price {
            font-size: 14px;
            color: var(--text-muted);
            text-decoration: line-through;
            margin-top: 5px;
        }

        /* Empty Cart */
        .empty-cart {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-cart-icon {
            font-size: 80px;
            color: var(--text-muted);
            margin-bottom: 20px;
        }

        .empty-cart-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .empty-cart-text {
            color: var(--text-muted);
            margin-bottom: 30px;
        }

        .continue-shopping-btn {
            background-color: var(--primary-color);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: var(--border-radius-sm);
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .continue-shopping-btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        /* Order Summary */
        .order-summary {
            background-color: var(--background-card);
            border-radius: var(--border-radius-lg);
            padding: 30px;
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 100px;
        }

        .summary-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .summary-label {
            color: var(--text-muted);
        }

        .summary-divider {
            height: 1px;
            background-color: var(--border-color);
            margin: 20px 0;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 30px;
        }

        .total-price {
            color: var(--text-price);
        }

        /* Promo Code */
        .promo-section {
            margin-bottom: 20px;
        }

        .promo-input-group {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .promo-input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-sm);
            background-color: transparent;
            color: var(--text-light);
            font-size: 14px;
        }

        .promo-input:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .apply-promo-btn {
            padding: 10px 20px;
            background-color: transparent;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
            border-radius: var(--border-radius-sm);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .apply-promo-btn:hover {
            background-color: var(--primary-color);
            color: white;
        }

        /* Checkout Button */
        .checkout-btn {
            width: 100%;
            padding: 15px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius-sm);
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .checkout-btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        /* Animations */
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

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

        /* Responsive Design */
        @media (max-width: 1024px) {
            .cart-content {
                grid-template-columns: 1fr;
            }

            .order-summary {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 0 3%;
            }

            .nav-links {
                display: none;
            }

            .cart-item {
                grid-template-columns: 100px 1fr;
                gap: 15px;
            }

            .cart-item-image {
                width: 100px;
                height: 100px;
            }

            .cart-item-price {
                grid-column: 2;
                text-align: left;
                margin-top: 10px;
            }
        }

        
    </style>

    <style>
/* Add these styles to the existing style section */
.checkout-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.checkout-header {
    margin-bottom: 30px;
}

.checkout-title {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 10px;
}

.checkout-content {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 30px;
}

.form-section {
    background-color: var(--background-card);
    border-radius: var(--border-radius-lg);
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: var(--shadow-sm);
}

.section-title {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 1px solid var(--border-color);
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.input-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.input-group.full-width {
    grid-column: 1 / -1;
}

label {
    font-size: 14px;
    font-weight: 500;
    color: var(--text-light);
}

input, textarea {
    padding: 12px 15px;
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius-sm);
    background-color: transparent;
    color: var(--text-light);
    font-size: 14px;
    transition: all var(--transition-speed) ease;
}

input:focus, textarea:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 2px var(--primary-light);
}

.error-message {
    color: var(--error-color);
    font-size: 12px;
    height: 16px;
}

.payment-methods {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.payment-option {
    display: flex;
    align-items: center;
    padding: 15px;
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius-sm);
    cursor: pointer;
    transition: all var(--transition-speed) ease;
}

.payment-option.active {
    border-color: var(--primary-color);
    background-color: var(--primary-light);
}

.payment-option label {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    margin-left: 10px;
}

.payment-option i {
    font-size: 20px;
}

.card-details {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid var(--border-color);
}

@media (max-width: 1024px) {
    .checkout-content {
        grid-template-columns: 1fr;
    }
    
    .order-summary {
        order: -1;
    }
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}

/* Consolidated Container Styles */
.checkout-container, .cart-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    animation: fadeIn 0.5s ease;
}

/* Unified Content Grid */
.checkout-content, .cart-content {
    display: grid;
    gap: 30px;
    grid-template-columns: 1fr 400px;
}

/* Single Order Summary Style */
.order-summary {
    background: var(--background-card);
    border-radius: var(--border-radius-lg);
    padding: 30px;
    position: sticky;
    top: 100px;
    box-shadow: var(--shadow-sm);
}

/* Removed duplicate cart items styles */
</style>

</head>
<body>
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
            <a href="index.php" class="nav-link active">Products</a>
            <a href="about.php" class="nav-link">About</a>
            <a href="contact.php" class="nav-link">Contact</a>
        </div>
        <div class="nav-buttons">
            <div class="theme-toggle" onclick="toggleTheme()">
                <div class="theme-toggle-track">
                    <i class="fas fa-sun theme-icon"></i>
                    <i class="fas fa-moon theme-icon"></i>
                    <div class="theme-toggle-thumb"></div>
                </div>
            </div>
            <div class="user-profile" onclick="toggleDropdown()">
                <div class="user-avatar">
                    <?php 
                    if (isLoggedIn()) {
                        $user = getCurrentUser();
                        $username = $user['username'];
                        $initials = '';
                        $nameParts = explode(' ', $username);
                        foreach($nameParts as $part) {
                            $initials .= strtoupper(substr($part, 0, 1));
                        }
                        echo substr($initials, 0, 2); 
                    } else {
                        echo 'GU';
                    }
                    ?>
                </div>
                <?php if (isLoggedIn()): ?>
                    <span class="user-name">
                        <?= htmlspecialchars(getCurrentUser()['username']) ?>
                    </span>
                <?php else: ?>
                    <span class="user-name">Guest</span>
                <?php endif; ?>
                <i class="fas fa-chevron-down"></i>
                <div class="user-dropdown" id="userDropdown">
                    <?php if (isLoggedIn()): ?>
                        <a href="profile.php" class="dropdown-item">
                            <i class="fas fa-user"></i> Profile
                        </a>
                        <a href="orders.php" class="dropdown-item">
                            <i class="fas fa-box"></i> Orders
                        </a>
                        <a href="wishlist.php" class="dropdown-item">
                            <i class="fas fa-heart"></i> Wishlist
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="auth/logout.php" class="dropdown-item">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    <?php else: ?>
                        <a href="auth/login.php" class="dropdown-item">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                        <a href="auth/register.php" class="dropdown-item">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- Hamburger for mobile -->
        <div class="hamburger" id="hamburger" onclick="toggleMobileMenu()">
            <span style="top:0"></span>
            <span style="top:9px"></span>
            <span style="top:18px"></span>
        </div>
        <!-- Mobile menu -->
        <div class="mobile-menu" id="mobileMenu">
            <div class="mobile-nav-links">
                <a href="index.php" class="mobile-nav-link">Home</a>
                <a href="ecomm.php" class="mobile-nav-link">Shop</a>
                <a href="index.php" class="mobile-nav-link active">Products</a>
                <a href="about.php" class="mobile-nav-link">About</a>
                <a href="contact.php" class="mobile-nav-link">Contact</a>
            </div>
            <div style="margin-top: 30px;">
                <?php if (isLoggedIn()): ?>
                    <a href="profile.php" class="mobile-nav-link"><i class="fas fa-user"></i> Profile</a>
                    <a href="orders.php" class="mobile-nav-link"><i class="fas fa-box"></i> Orders</a>
                    <a href="wishlist.php" class="mobile-nav-link"><i class="fas fa-heart"></i> Wishlist</a>
                    <a href="auth/logout.php" class="mobile-nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
                <?php else: ?>
                    <a href="auth/login.php" class="mobile-nav-link"><i class="fas fa-sign-in-alt"></i> Login</a>
                    <a href="auth/register.php" class="mobile-nav-link"><i class="fas fa-user-plus"></i> Register</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="overlay" id="overlay" onclick="toggleMobileMenu()"></div>
    </nav>
<div class="checkout-container" id="checkoutSection" style="display: none;">
    <div class="checkout-header">
        <h1 class="checkout-title">Checkout</h1>
        <div class="checkout-breadcrumb">
            <a href="index.php" class="breadcrumb-link">Home</a>
            <i class="fas fa-chevron-right"></i>
            <a href="cart.php" class="breadcrumb-link">Shopping Cart</a>
            <i class="fas fa-chevron-right"></i>
            <span>Checkout</span>
        </div>
    </div>

    <div class="checkout-content">
        <!-- Checkout Form -->
        <div class="checkout-form-section">
            <form id="checkoutForm" method="POST" action="process_checkout.php">
                <!-- Billing Details -->
                <div class="form-section">
                    <h2 class="section-title">Billing Details</h2>
                    <div class="form-grid">
                        <div class="input-group">
                            <label for="firstName">First Name</label>
                            <input type="text" id="firstName" name="first_name" required>
                            <span class="error-message"></span>
                        </div>
                        <div class="input-group">
                            <label for="lastName">Last Name</label>
                            <input type="text" id="lastName" name="last_name" required>
                            <span class="error-message"></span>
                        </div>
                        <div class="input-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                            <span class="error-message"></span>
                        </div>
                        <div class="input-group">
                            <label for="phone">Phone</label>
                            <input type="tel" id="phone" name="phone" required>
                            <span class="error-message"></span>
                        </div>
                        <div class="input-group full-width">
                            <label for="address">Shipping Address</label>
                            <textarea id="address" name="address" rows="3" required></textarea>
                            <span class="error-message"></span>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="form-section">
                    <h2 class="section-title">Payment Method</h2>
                    <div class="payment-methods">
                        <div class="payment-option active">
                            <input type="radio" name="payment_method" id="creditCard" value="credit_card" checked>
                            <label for="creditCard">
                                <i class="fab fa-cc-visa"></i>
                                <i class="fab fa-cc-mastercard"></i>
                                Credit/Debit Card
                            </label>
                        </div>
                        <div class="payment-option">
    <input type="radio" name="payment_method" id="gcash" value="gcash">
    <label for="gcash">
        <i class="fas fa-mobile-alt"></i>
        GCash
        <span class="payment-description">(Send to: +63 930 854 9550)</span>
    </label>
</div>
                        <div class="payment-option">
                            <input type="radio" name="payment_method" id="cod" value="cod">
                            <label for="cod">
                                <i class="fas fa-money-bill-wave"></i>
                                Cash on Delivery
                            </label>
                        </div>
                    </div>

                    <!-- Card Details -->
                    <div class="card-details">
                        <div class="form-grid">
                            <div class="input-group full-width">
                                <label for="cardNumber">Card Number</label>
                                <input type="text" id="cardNumber" name="card_number">
                                <span class="error-message"></span>
                            </div>
                            <div class="input-group">
                                <label for="expDate">Exp. Date</label>
                                <input type="text" id="expDate" name="exp_date" placeholder="MM/YY">
                                <span class="error-message"></span>
                            </div>
                            <div class="input-group">
                                <label for="cvv">CVV</label>
                                <input type="text" id="cvv" name="cvv">
                                <span class="error-message"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Order Summary -->
        <div class="order-summary">
            <h2 class="summary-title">Order Summary</h2>
            <div class="summary-content">
                <div class="summary-row">
                    <span class="summary-label">Subtotal (<?= $totalItems ?> items)</span>
                    <span>₱<?= number_format($subtotal, 2) ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Shipping</span>
                    <span>₱<?= number_format($shipping, 2) ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Tax (12%)</span>
                    <span>₱<?= number_format($tax, 2) ?></span>
                </div>
                <div class="summary-divider"></div>
                <div class="summary-total">
                    <span>Total</span>
                    <span class="total-price">₱<?= number_format($total, 2) ?></span>
                </div>
                <button type="submit" form="checkoutForm" class="checkout-btn">
                    <i class="fas fa-lock"></i>
                    Complete Order
                </button>
            </div>
        </div>
    </div>
</div>

   <!-- Cart Container -->
    <div class="cart-container" id="cartSection">
        <div class="cart-header">
            <h1 class="cart-title">Shopping Cart</h1>
            <div class="cart-breadcrumb">
                <a href="index.php" class="breadcrumb-link">Home</a>
                <i class="fas fa-chevron-right"></i>
                <span>Shopping Cart</span>
            </div>
        </div>

        <div class="cart-content">
            <!-- Cart Items Section -->
            <div class="cart-items-section">
                <div class="cart-items-header">
                    <span class="items-count"><?= $totalItems ?> Item<?= $totalItems !== 1 ? 's' : '' ?></span>
                    <button class="clear-cart-btn" onclick="clearCart()">
                        <i class="fas fa-trash"></i> Clear Cart
                    </button>
                </div>

                <div id="cartItems">
                    <?php if (!empty($cartItems)): ?>
                        <?php foreach ($cartItems as $productId => $item): 
                            $stmt = $conn->prepare("SELECT price, name, image_path FROM products WHERE id = ?");
                            $stmt->bind_param("i", $productId);
                            $stmt->execute();
                            $product = $stmt->get_result()->fetch_assoc();
                            
                            if (!$product) continue;
                        ?>
                            <div class="cart-item" data-product-id="<?= $productId ?>">
                                <img src="uploads/<?= htmlspecialchars($product['image_path']) ?>" 
                                     alt="<?= htmlspecialchars($product['name']) ?>" 
                                     class="cart-item-image"
                                     onerror="this.src='/assets/images/placeholder.jpg';this.alt='Image not available'">
                                
                                <div class="cart-item-details">
                                    <h3 class="item-name"><?= htmlspecialchars($product['name']) ?></h3>
                                    <div class="item-info">
                                        <span>Price: ₱<?= number_format($product['price'], 2) ?></span>
                                    </div>
                                    <div class="item-actions">
                                        <div class="quantity-control">
                                            <button class="quantity-btn" 
                                                    onclick="updateQuantity(<?= $productId ?>, -1)">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="text" class="quantity-input" 
                                                   value="<?= $item['quantity'] ?>" readonly>
                                            <button class="quantity-btn" 
                                                    onclick="updateQuantity(<?= $productId ?>, 1)">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                        <span class="remove-item" 
                                              onclick="removeItem(<?= $productId ?>)">
                                            <i class="fas fa-trash"></i> Remove
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="cart-item-price">
                                    <div class="item-price">
                                        ₱<?= number_format($product['price'] * $item['quantity'], 2) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-cart">
                            <i class="fas fa-shopping-cart empty-cart-icon"></i>
                            <h2 class="empty-cart-title">Your cart is empty</h2>
                            <p class="empty-cart-text">Looks like you haven't added any items to your cart yet.</p>
                            <a href="index.php" class="continue-shopping-btn">
                                <i class="fas fa-arrow-left"></i> Continue Shopping
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            

            <!-- Order Summary -->
            <div class="order-summary">
                <h2 class="summary-title">Order Summary</h2>
                
                <div class="summary-row">
                    <span class="summary-label">Subtotal (<?= $totalItems ?> items)</span>
                    <span>₱<?= number_format($subtotal, 2) ?></span>
                </div>
                
                <div class="summary-row">
                    <span class="summary-label">Shipping</span>
                    <span>₱<?= number_format($shipping, 2) ?></span>
                </div>
                
                <div class="summary-row">
                    <span class="summary-label">Tax (12%)</span>
                    <span>₱<?= number_format($tax, 2) ?></span>
                </div>
                
                <div class="summary-divider"></div>
                
                <div class="summary-total">
                    <span>Total</span>
                    <span class="total-price">₱<?= number_format($total, 2) ?></span>
                </div>
                
                <button class="checkout-btn" onclick="proceedToCheckout()" <?= empty($cartItems) ? 'disabled' : '' ?>>
    <i class="fas fa-lock"></i>
    <?= empty($cartItems) ? 'Cart is Empty' : 'Proceed to Checkout' ?>
</button>
                
                <div style="margin-top: 20px; text-align: center;">
                    <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 10px;">
                        <i class="fas fa-shield-alt"></i> Secure Checkout
                    </p>
                    <p style="font-size: 12px; color: var(--text-muted);">
                        We accept
                    </p>
                    <div style="display: flex; justify-content: center; gap: 10px; margin-top: 10px;">
    <i class="fab fa-cc-visa" style="font-size: 24px; color: var(--text-muted);"></i>
    <i class="fab fa-cc-mastercard" style="font-size: 24px; color: var(--text-muted);"></i>
    <i class="fab fa-cc-amex" style="font-size: 24px; color: var(--text-muted);"></i>
    <i class="fab fa-cc-paypal" style="font-size: 24px; color: var(--text-muted);"></i>
    <!-- Custom GCash icon -->
    <img src="assets/images/gcash-icon.png" alt="GCash" 
         style="width: 24px; height: 24px; filter: grayscale(100%) opacity(0.7);">
    <!-- OR Font Awesome mobile icon -->
    <i class="fas fa-mobile-alt" style="font-size: 24px; color: var(--text-muted);"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Order Complete Modal -->
<div id="orderCompleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h2>Order Complete!</h2>
        <p>Your order has been successfully placed.</p>
        <div class="modal-actions">
            <button onclick="closeModal()" class="modal-close-btn">Continue Shopping</button>
            <a href="orders.php" class="view-orders-btn">View Orders</a>
        </div>
    </div>
</div>

<style>
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    animation: fadeIn 0.3s ease;
}

.modal-content {
    position: relative;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: var(--background-card);
    padding: 40px;
    border-radius: var(--border-radius-lg);
    text-align: center;
    max-width: 500px;
}

.modal-icon {
    font-size: 80px;
    color: #4CAF50;
    margin-bottom: 20px;
}

.modal h2 {
    font-size: 28px;
    margin-bottom: 15px;
}

.modal p {
    color: var(--text-muted);
    margin-bottom: 30px;
}

.modal-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
}

.modal-close-btn {
    padding: 12px 30px;
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: var(--border-radius-sm);
    cursor: pointer;
    transition: all 0.3s ease;
}

.modal-close-btn:hover {
    background: var(--primary-dark);
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
</style>

<script>
function showOrderComplete() {
    document.getElementById('orderCompleteModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('orderCompleteModal').style.display = 'none';
    window.location.href = 'index.php';
}

// Add to your existing form submission handler
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Simulate successful order submission
    const formData = new FormData(this);
    
    fetch('process_checkout.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showOrderComplete();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});
</script>
    <script>
        // Theme Toggle
        function toggleTheme() {
            const body = document.body;
            const currentTheme = body.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
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

        // Hamburger/Mobile menu toggle
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            const overlay = document.getElementById('overlay');
            mobileMenu.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        // Navbar scroll shrink effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 10) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

              function updateOrderSummary() {
            const subtotal = cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const shipping = cartItems.length > 0 ? 10 : 0;
            const tax = subtotal * 0.1; // 10% tax
            const total = subtotal + shipping + tax;
            
            // Update summary values
            document.querySelector('.summary-row:nth-child(1) span:last-child').textContent = `$${subtotal.toFixed(2)}`;
            document.querySelector('.summary-row:nth-child(2) span:last-child').textContent = `$${shipping.toFixed(2)}`;
            document.querySelector('.summary-row:nth-child(3) span:last-child').textContent = `$${tax.toFixed(2)}`;
            document.querySelector('.total-price').textContent = `$${total.toFixed(2)}`;
            
            // Update items count in summary
            const totalItems = cartItems.reduce((sum, item) => sum + item.quantity, 0);
            document.querySelector('.summary-label').textContent = `Subtotal (${totalItems} item${totalItems !== 1 ? 's' : ''})`;
        }

        // Apply promo code
        document.querySelector('.apply-promo-btn').addEventListener('click', function() {
            const promoInput = document.querySelector('.promo-input');
            const promoCode = promoInput.value.trim().toUpperCase();
            
            if (promoCode === 'SAVE10') {
                showNotification('Promo code applied! 10% discount added.');
                // Apply discount logic here
            } else if (promoCode === 'FREESHIP') {
                showNotification('Free shipping applied!');
                // Apply free shipping logic here
            } else if (promoCode) {
                showNotification('Invalid promo code', 'error');
            }
        });

        // Proceed to checkout
        function proceedToCheckout() {
            const cartSection = document.getElementById('cartSection');
            const checkoutSection = document.getElementById('checkoutSection');
            
            if (cartItems.length === 0) {
                showNotification('Your cart is empty', 'error');
                return;
            }
            
            // Show checkout section, hide cart section
            cartSection.style.display = 'none';
            checkoutSection.style.display = 'block';
            
            // Scroll to top
            window.scrollTo(0, 0);
        }

        // Show notification
        function showNotification(message, type = 'success') {
            // Create notification element
            const notification = document.c // Cart functionality
        let cartItems = [
            { id: 1, name: 'Nike Air Max 270', price: 150, originalPrice: 180, quantity: 1, size: '10', color: 'Red' },
            { id: 2, name: 'Adidas Ultraboost 21', price: 180, originalPrice: null, quantity: 2, size: '9.5', color: 'Black' },
            { id: 3, name: 'Nike Air Jordan 1', price: 170, originalPrice: 200, quantity: 1, size: '11', color: 'Blue/White' }
        ];

        // Update quantity
        function updateQuantity(itemId, change) {
            const item = cartItems.find(item => item.id === itemId);
            if (item) {
                const newQuantity = item.quantity + change;
                if (newQuantity > 0) {
                    item.quantity = newQuantity;
                    updateCartDisplay();
                }
            }
        }

        // Remove item from cart
        function removeItem(itemId) {
            if (confirm('Are you sure you want to remove this item from your cart?')) {
                cartItems = cartItems.filter(item => item.id !== itemId);
                updateCartDisplay();
                
                // Show success message
                showNotification('Item removed from cart');
            }
        }

        // Clear entire cart
        function clearCart() {
            if (confirm('Are you sure you want to clear your entire cart?')) {
                cartItems = [];
                updateCartDisplay();
                showNotification('Cart cleared');
            }
        }

        // Update cart display
        function updateCartDisplay() {
            const cartItemsContainer = document.getElementById('cartItems');
            const emptyCartContainer = document.querySelector('.empty-cart');
            const itemsCountElement = document.querySelector('.items-count');
            
            if (cartItems.length === 0) {
                cartItemsContainer.style.display = 'none';
                emptyCartContainer.style.display = 'block';
                document.querySelector('.cart-items-header').style.display = 'none';
                updateOrderSummary();
                return;
            }
            
            cartItemsContainer.style.display = 'block';
            emptyCartContainer.style.display = 'none';
            document.querySelector('.cart-items-header').style.display = 'flex';
            
            // Update items count
            const totalItems = cartItems.reduce((sum, item) => sum + item.quantity, 0);
            itemsCountElement.textContent = `${totalItems} Item${totalItems !== 1 ? 's' : ''}`;
            
            // Update quantity inputs
            cartItems.forEach(item => {
                const quantityInput = document.querySelector(`.cart-item:nth-child(${item.id}) .quantity-input`);
                if (quantityInput) {
                    quantityInput.value = item.quantity;
                }
            });
            
            updateOrderSummary();
        }

        // Update order summary
 reateElement('div');
            notification.className = `notification ${type}`;
            notification.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                <span>${message}</span>
            `;
            
            // Add styles
            notification.style.cssText = `
                position: fixed;
                top: 100px;
                right: 20px;
                background-color: ${type === 'success' ? 'var(--primary-color)' : 'var(--error-color)'};
                color: white;
                padding: 15px 20px;
                border-radius: var(--border-radius-sm);
                display: flex;
                align-items: center;
                gap: 10px;
                box-shadow: var(--shadow-md);
                z-index: 1001;
                animation: slideInRight 0.3s ease;
            `;
            
            document.body.appendChild(notification);
            
            // Remove after 3 seconds
            setTimeout(() => {
                notification.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Add slide animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            @keyframes slideOutRight {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);

        // Initialize cart display
        updateCartDisplay();
    </script>

         <script>
        // Cart functionality
        function updateQuantity(productId, change) {
            fetch('update_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}&quantity_change=${change}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message);
                }
            });
        }

        function removeItem(productId) {
            if (confirm('Are you sure you want to remove this item from your cart?')) {
                fetch('remove_from_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `product_id=${productId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.message);
                    }
                });
            }
        }

        function clearCart() {
            if (confirm('Are you sure you want to clear your entire cart?')) {
                fetch('clear_cart.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    }
                });
            }
        }

        function proceedToCheckout() {
            <?php if (empty($cartItems)): ?>
                alert('Your cart is empty!');
            <?php else: ?>
                document.getElementById('cartSection').style.display = 'none';
                document.getElementById('checkoutSection').style.display = 'block';
                window.scrollTo(0, 0);
            <?php endif; ?>
        }
    </script>

    <script>
// Initialize checkout section as hidden
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('checkoutSection').style.display = 'none';
});

// Add to existing script
document.querySelectorAll('.payment-option input').forEach(radio => {
    radio.addEventListener('change', () => {
        document.querySelectorAll('.payment-option').forEach(option => {
            option.classList.remove('active');
        });
        radio.closest('.payment-option').classList.add('active');
        
        // Toggle card details visibility
        document.querySelector('.card-details').style.display = 
            radio.value === 'credit_card' ? 'block' : 'none';
    });
});

// Form validation
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    let valid = true;
    
    document.querySelectorAll('[required]').forEach(input => {
        if (!input.value.trim()) {
            valid = false;
            input.nextElementSibling.textContent = 'This field is required';
        } else {
            input.nextElementSibling.textContent = '';
        }
    });
    
    if (!valid) e.preventDefault();
});

// Add back to cart button
document.querySelector('.checkout-breadcrumb').innerHTML += `
<button onclick="backToCart()" style="background: none; border: none; color: var(--primary-color); cursor: pointer; margin-left: auto;">
    <i class="fas fa-arrow-left"></i> Back to Cart
</button>`;

function backToCart() {
    document.getElementById('cartSection').style.display = 'block';
    document.getElementById('checkoutSection').style.display = 'none';
}
</script>
<!-- jQuery first if needed -->
<script src="js/libs/jquery.min.js"></script>
<!-- Then your custom scripts -->
<script src="js/checkout.js"></script>
</body>
</html>
