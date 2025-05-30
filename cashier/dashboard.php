
<?php
session_start();
include '../includes/auth.php';
include '../includes/db_connect.php';

if (!isLoggedIn() || $_SESSION['role'] !== 'cashier') {
    header("Location: ../auth/login.php");
    exit();
}

// Get cashier information
$cashierName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Cashier';
$cashierInitials = strtoupper(substr($cashierName, 0, 2));

// Fetch unique categories (brands) from the products table
$categories = [];
$categoryResult = $conn->query("SELECT DISTINCT category FROM products");

if ($categoryResult && $categoryResult->num_rows > 0) {
    while ($row = $categoryResult->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}

// Fetch all products from the database
$products = [];
$productResult = $conn->query("SELECT id, name, category, price, stock, image_path, barcode FROM products");

if ($productResult && $productResult->num_rows > 0) {
    while ($row = $productResult->fetch_assoc()) {
        $products[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Billing Page</title>
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
            padding-top: 80px; /* Space for fixed navbar */
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
        
        .navbar.scrolled {
            height: 70px;
            background-color: rgba(17, 19, 21, 0.98);
        }
        
        [data-theme="light"] .navbar.scrolled {
            background-color: rgba(255, 255, 255, 0.98);
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
        
        .nav-link.active {
            color: var(--primary-color);
        }
        
        .nav-link.active::after {
            width: 100%;
        }
        
        .nav-buttons {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .nav-btn {
            padding: 10px 20px;
            border-radius: var(--border-radius-sm);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-speed) ease;
            border: none;
            outline: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }
        
        .btn-signin {
            color: var(--text-light);
            background-color: transparent;
            border: 1px solid var(--primary-color);
        }
        
        .btn-signin:hover {
            background-color: rgba(76, 175, 80, 0.1);
            transform: translateY(-2px);
        }
        
        .btn-register {
            color: white;
            background-color: var(--primary-color);
            box-shadow: 0 4px 10px rgba(76, 175, 80, 0.3);
        }
        
        .btn-register:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(76, 175, 80, 0.4);
        }
        
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
        
        .hamburger span:nth-child(1) {
            top: 0;
        }
        
        .hamburger span:nth-child(2) {
            top: 9px;
        }
        
        .hamburger span:nth-child(3) {
            top: 18px;
        }
        
        /* Theme Toggle Switch */
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
        
        .theme-toggle-track:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        [data-theme="light"] .theme-toggle-track:hover {
            background-color: rgba(0, 0, 0, 0.2);
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
        
        /* User profile in navbar */
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
        
        /* Container Layout */
        .container {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 20px;
            padding: 20px 5%;
            min-height: calc(100vh - 120px);
        }
        
        /* Left Panel */
        .left-panel {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        /* Barcode Scanner Section */
        .scanner-section {
            background-color: var(--background-card);
            border-radius: var(--border-radius-md);
            padding: 20px;
            box-shadow: var(--shadow-sm);
        }
        
        .scanner-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .scanner-header i {
            color: var(--primary-color);
            font-size: 20px;
        }
        
        .scanner-title {
            font-size: 18px;
            font-weight: 600;
        }
        
        .barcode-input-container {
            position: relative;
            width: 100%;
        }
        
        #barcodeInput {
            width: 100%;
            padding: 12px 15px 12px 40px;
            border: 2px solid var(--primary-color);
            border-radius: var(--border-radius-sm);
            background-color: rgba(255, 255, 255, 0.05);
            color: var(--text-light);
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        #barcodeInput:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.2);
        }
        
        .barcode-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-color);
            font-size: 18px;
        }
        
        #barcode-status {
            margin-top: 10px;
            font-size: 14px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        #barcode-status i {
            color: var(--primary-color);
        }
        
        .barcode-status-success {
            color: var(--primary-color) !important;
        }
        
        .barcode-status-error {
            color: var(--error-color) !important;
        }
        
        /* Categories Section */
        .categories {
            background-color: var(--background-card);
            border-radius: var(--border-radius-md);
            padding: 15px;
            overflow-x: auto;
            white-space: nowrap;
            box-shadow: var(--shadow-sm);
            display: flex;
            gap: 10px;
        }
        
        .category-btn {
            padding: 10px 15px;
            border: none;
            border-radius: var(--border-radius-sm);
            background-color: rgba(255, 255, 255, 0.05);
            color: var(--text-light);
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .category-btn:hover, .category-btn.active {
            background-color: var(--primary-color);
            color: white;
        }
        
        /* Search Section */
        .search-section {
            background-color: var(--background-card);
            border-radius: var(--border-radius-md);
            padding: 15px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 20px;
        }

        .search-container {
            position: relative;
            width: 100%;
        }

        .search-input {
            width: 100%;
            padding: 12px 15px 12px 40px;
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius-sm);
            background-color: rgba(255, 255, 255, 0.05);
            color: var(--text-light);
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.2);
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }

        .clear-search-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 5px;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .clear-search-btn.visible {
            opacity: 1;
        }

        .clear-search-btn:hover {
            color: var(--primary-color);
        }
        
        /* Products Section */
        .products {
            background-color: var(--background-card);
            border-radius: var(--border-radius-md);
            padding: 20px;
            height: 500px;
            overflow-y: auto;
            box-shadow: var(--shadow-sm);
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 15px;
        }
        
        .product-card {
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: var(--border-radius-md);
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }
        
        .product-card[disabled] {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .product-card img {
            width: 100%;
            height: 140px;
            object-fit: cover;
        }
        
        .product-info {
            padding: 12px;
        }
        
        .product-info h3 {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .product-category {
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 8px;
        }
        
        .product-price-stock {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .product-price {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-price);
        }
        
        .product-stock {
            font-size: 12px;
            color: var(--primary-color);
        }
        
        .out-of-stock {
            color: var(--error-color);
        }
        
        /* Bill Section */
        .bill {
            background-color: var(--background-card);
            border-radius: var(--border-radius-md);
            padding: 20px;
            display: flex;
            flex-direction: column;
            height: 100%;
            box-shadow: var(--shadow-sm);
        }
        
        .bill h2 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .bill h2 i {
            color: var(--primary-color);
        }
        
        .bill-items {
            flex: 1;
            overflow-y: auto;
            margin-bottom: 15px;
        }
        
       /* Updated Bill Item Styles */
.bill-item {
    padding: 12px;
    border-radius: var(--border-radius-sm);
    background-color: rgba(255, 255, 255, 0.05);
    margin-bottom: 10px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.bill-item-info {
    display: flex;
    justify-content: space-between;
}

.bill-item-name {
    font-weight: 500;
    flex: 1;
}

.bill-item-price {
    color: var(--text-price);
    font-size: 14px;
}

.bill-item-controls {
    display: flex;
    align-items: center;
    gap: 10px;
}

.quantity-btn {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    border: none;
    background-color: rgba(255, 255, 255, 0.1);
    color: var(--text-light);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.quantity-btn:hover {
    background-color: var(--primary-color);
    color: white;
}

.quantity-display {
    font-weight: 600;
    min-width: 30px;
    text-align: center;
}

.remove-btn {
    margin-left: auto;
    background-color: rgba(244, 67, 54, 0.1);
    color: var(--error-color);
    border: none;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.remove-btn:hover {
    background-color: var(--error-color);
    color: white;
}

.bill-item-total {
    text-align: right;
    font-weight: 600;
    color: var(--text-price);
}
        
        .bill-summary {
            padding-top: 15px;
            border-top: 1px solid var(--border-color);
        }
        
        .bill-subtotal, .bill-tax {
            font-size: 16px;
            color: var(--text-muted);
            text-align: right;
            margin-bottom: 5px;
        }
        
        .bill-total {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--text-price);
            text-align: right;
            border-top: 1px dashed var(--border-color);
            padding-top: 5px;
        }
        
        .checkout-btn {
            width: 100%;
            padding: 15px;
            border-radius: var(--border-radius-sm);
            background-color: var(--primary-color);
            color: white;
            border: none;
            font-size: 16px;
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
            box-shadow: 0 4px 10px rgba(76, 175, 80, 0.3);
        }
        
        .checkout-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        /* Footer */
        .footer {
            text-align: center;
            padding: 15px;
            color: var(--text-muted);
            font-size: 14px;
            border-top: 1px solid var(--border-color);
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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
        
        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        /* Responsive Styles */
        @media (max-width: 1200px) {
            .container {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .navbar {
                padding: 0 20px;
            }
            
            .nav-links, .nav-buttons {
                display: none;
            }
            
            .nav-links.active, .nav-buttons.active {
                display: flex;
                flex-direction: column;
                position: fixed;
                top: 80px;
                left: 0;
                width: 100%;
                background-color: var(--background-card);
                padding: 20px;
                box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
                z-index: 1000;
                animation: fadeInDown 0.3s ease forwards;
            }
            
            .nav-links.active {
                gap: 20px;
            }
            
            .nav-buttons.active {
                margin-top: 20px;
                padding-top: 20px;
                border-top: 1px solid var(--border-color);
            }
            
            .hamburger {
                display: block;
            }
            
            .hamburger.active span:nth-child(1) {
                transform: rotate(45deg) translate(5px, 5px);
            }
            
            .hamburger.active span:nth-child(2) {
                opacity: 0;
            }
            
            .hamburger.active span:nth-child(3) {
                transform: rotate(-45deg) translate(7px, -6px);
            }
        }

        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            width: 90%;
            max-width: 500px;
            padding: 24px;
            position: relative;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .modal-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-color);
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-color);
        }
        
        .payment-methods {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }
        
        .payment-method {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 16px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .payment-method.selected {
            border-color: var(--primary-color);
            background-color: rgba(var(--primary-rgb), 0.1);
        }
        
        .payment-method i {
            font-size: 2rem;
            margin-bottom: 8px;
            color: var(--text-color);
        }
        
        .payment-method.selected i {
            color: var(--primary-color);
        }
        
        .payment-method-name {
            font-weight: 500;
        }
        
        .payment-details {
            margin-top: 16px;
        }
        
        .payment-field {
            margin-bottom: 16px;
        }
        
        .payment-field label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .payment-field input {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            background-color: var(--input-bg);
            color: var(--text-color);
        }
        
        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 24px;
        }
        
        .modal-btn {
            padding: 10px 20px;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .modal-btn-cancel {
            background-color: var(--background-color);
            border: 1px solid var(--border-color);
            color: var(--text-color);
        }
        
        .modal-btn-confirm {
            background-color: var(--primary-color);
            border: 1px solid var(--primary-color);
            color: white;
        }
        
        /* Receipt Modal Styles */
        .receipt {
            background-color: white;
            color: black;
            font-family: 'Courier New', monospace;
            padding: 20px;
            width: 100%;
            max-width: 380px;
            margin: 0 auto;
        }
        
        .receipt-header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .receipt-logo {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .receipt-address {
            font-size: 12px;
            margin-bottom: 5px;
        }
        
        .receipt-contact {
            font-size: 12px;
            margin-bottom: 10px;
        }
        
        .receipt-divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        
        .receipt-info {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 5px;
        }
        
        .receipt-items {
            margin: 15px 0;
        }
        
        .receipt-item {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 5px;
        }
        
        .receipt-item-name {
            width: 60%;
        }
        
        .receipt-item-qty {
            width: 10%;
            text-align: center;
        }
        
        .receipt-item-price {
            width: 30%;
            text-align: right;
        }
        
        .receipt-summary {
            margin-top: 10px;
        }
        
        .receipt-total {
            font-weight: bold;
            font-size: 14px;
            text-align: right;
            margin-top: 5px;
        }
        
        .receipt-footer {
            text-align: center;
            font-size: 12px;
            margin-top: 20px;
        }
        
        .receipt-actions {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }
        
        .print-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .new-sale-btn {
            background-color: var(--background-color);
            border: 1px solid var(--border-color);
            color: var(--text-color);
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <a href="#" class="nav-logo">
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
            <a href="#" class="nav-link active">POS</a>
            <a href="#" class="nav-link">Sales History</a>
            <a href="#" class="nav-link">Reports</a>
        </div>
        <div class="nav-buttons">
            <div class="theme-toggle">
                <div class="theme-toggle-track">
                    <i class="fas fa-moon theme-icon"></i>
                    <i class="fas fa-sun theme-icon"></i>
                    <div class="theme-toggle-thumb"></div>
                </div>
            </div>
            <div class="user-profile">
                <div class="user-avatar"><?php echo $cashierInitials; ?></div>
                <span class="user-name"><?php echo $cashierName; ?></span>
                <i class="fas fa-chevron-down"></i>
                <div class="user-dropdown">
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-history"></i>
                        <span>Transaction History</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="../auth/logout.php" class="dropdown-item">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                    
                </div>
            </div>
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container">
        <!-- Left Panel -->
        <div class="left-panel">
            <!-- Barcode Scanner Section -->
            <div class="scanner-section">
                <div class="scanner-header">
                    <i class="fas fa-barcode"></i>
                    <h2 class="scanner-title">Barcode Scanner</h2>
                </div>
                <div class="barcode-input-container">
                    <i class="fas fa-barcode barcode-icon"></i>
                    <input type="text" id="barcodeInput" placeholder="Scan barcode or enter product code" autofocus>
                </div>
                <div id="barcode-status">
                    <i class="fas fa-info-circle"></i>
                    <span>Ready to scan</span>
                </div>
            </div>

            <!-- Categories Section -->
            <div class="categories">
                <button class="category-btn active" data-category="all">
                    <i class="fas fa-th-large"></i>
                    All Products
                </button>
                <?php foreach ($categories as $category): ?>
                <button class="category-btn" data-category="<?php echo htmlspecialchars($category); ?>">
                    <i class="fas fa-tag"></i>
                    <?php echo htmlspecialchars($category); ?>
                </button>
                <?php endforeach; ?>
            </div>

            <!-- Search Section -->
            <div class="search-section">
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="productSearch" placeholder="Search products..." class="search-input">
                    <button id="clearSearch" class="clear-search-btn">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Products Section -->
            <div class="products">
                <div class="products-grid">
                    <?php foreach ($products as $product): ?>
                    <div class="product-card" 
                         data-id="<?php echo $product['id']; ?>" 
                         data-name="<?php echo htmlspecialchars($product['name']); ?>" 
                         data-price="<?php echo $product['price']; ?>" 
                         data-category="<?php echo htmlspecialchars($product['category']); ?>"
                         data-barcode="<?php echo htmlspecialchars($product['barcode']); ?>"
                         <?php echo ($product['stock'] <= 0) ? 'disabled' : ''; ?>>
                        <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <div class="product-category"><?php echo htmlspecialchars($product['category']); ?></div>
                            <div class="product-price-stock">
                                <div class="product-price">₱<?php echo number_format($product['price'], 2); ?></div>
                                <div class="product-stock <?php echo ($product['stock'] <= 0) ? 'out-of-stock' : ''; ?>">
                                    <?php echo ($product['stock'] > 0) ? 'In Stock: ' . $product['stock'] : 'Out of Stock'; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        
<!-- Bill Section -->
<div class="bill">
    <h2><i class="fas fa-receipt"></i> Current Bill</h2>
    <div class="bill-items" id="billItems">
        <!-- Bill items will be added here dynamically -->
    </div>
    <div class="bill-summary">
        <div class="bill-subtotal" id="billSubtotal">Subtotal: ₱0.00</div>
        <div class="bill-tax" id="billTax">Tax (2%): ₱0.00</div>
        <div class="bill-total" id="billTotal">Total: ₱0.00</div>
        <button class="checkout-btn" id="checkoutBtn" disabled>
            <i class="fas fa-shopping-cart"></i>
            Checkout
        </button>
    </div>
</div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> Bigbyte Cartel. Kicks Est 2025.</p>
    </footer>

    <!-- Payment Method Modal -->
    <div class="modal-overlay" id="paymentModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Select Payment Method</h3>
                <button class="modal-close" id="closePaymentModal">&times;</button>
            </div>
            <div class="payment-methods">
                <div class="payment-method" data-method="cash">
                    <i class="fas fa-money-bill-wave"></i>
                    <span class="payment-method-name">Cash</span>
                </div>
                <div class="payment-method" data-method="card">
                    <i class="fas fa-credit-card"></i>
                    <span class="payment-method-name">Credit/Debit Card</span>
                </div>
                <div class="payment-method" data-method="gcash">
                    <i class="fas fa-mobile-alt"></i>
                    <span class="payment-method-name">GCash</span>
                </div>
                <div class="payment-method" data-method="maya">
                    <i class="fas fa-wallet"></i>
                    <span class="payment-method-name">Maya</span>
                </div>
            </div>
            
            <!-- Payment Details (will be shown/hidden based on selected payment method) -->
            <div class="payment-details" id="cashDetails">
                <div class="payment-field">
                    <label for="cashAmount">Amount Received (₱)</label>
                    <input type="number" id="cashAmount" min="0" step="0.01">
                </div>
                <div class="payment-field">
                    <label>Change</label>
                    <div id="changeAmount">₱0.00</div>
                </div>
            </div>
            
            <div class="payment-details" id="cardDetails" style="display: none;">
                <div class="payment-field">
                    <label for="cardNumber">Card Number</label>
                    <input type="text" id="cardNumber" placeholder="XXXX XXXX XXXX XXXX">
                </div>
                <div class="payment-field">
                    <label for="cardExpiry">Expiry Date</label>
                    <input type="text" id="cardExpiry" placeholder="MM/YY">
                </div>
                <div class="payment-field">
                    <label for="cardCvv">CVV</label>
                    <input type="text" id="cardCvv" placeholder="XXX">
                </div>
            </div>
            
            <div class="payment-details" id="gcashDetails" style="display: none;">
                <div class="payment-field">
                    <label for="gcashNumber">GCash Number</label>
                    <input type="text" id="gcashNumber" placeholder="09XX XXX XXXX">
                </div>
                <div class="payment-field">
                    <label for="gcashReference">Reference Number</label>
                    <input type="text" id="gcashReference">
                </div>
            </div>
            
            <div class="payment-details" id="mayaDetails" style="display: none;">
                <div class="payment-field">
                    <label for="mayaNumber">Maya Account</label>
                    <input type="text" id="mayaNumber" placeholder="09XX XXX XXXX">
                </div>
                <div class="payment-field">
                    <label for="mayaReference">Reference Number</label>
                    <input type="text" id="mayaReference">
                </div>
            </div>
            
            <div class="modal-actions">
                <button class="modal-btn modal-btn-cancel" id="cancelPayment">Cancel</button>
                <button class="modal-btn modal-btn-confirm" id="confirmPayment">Complete Payment</button>
            </div>
        </div>
    </div>

    <!-- Receipt Modal -->
    <div class="modal-overlay" id="receiptModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Receipt</h3>
                <button class="modal-close" id="closeReceiptModal">&times;</button>
            </div>
            
            <div class="receipt" id="receipt">
                <div class="receipt-header">
                    <div class="receipt-logo">KICKS</div>
                    <div class="receipt-address">123 Sneaker Street, Sabang, Danao City, Cebu</div>
                    <div class="receipt-contact">Tel: (63) 8123-4567 | www.kicks.com</div>
                </div>
                
                <div class="receipt-divider"></div>
                
                <div class="receipt-info">
                    <span>Receipt #: <span id="receiptNumber"></span></span>
                    <span id="receiptDate"></span>
                </div>
                
                <div class="receipt-info">
                    <span>Cashier: <span id="receiptCashier"></span></span>
                    <span id="receiptTime"></span>
                </div>
                
                <div class="receipt-divider"></div>
                
                <div class="receipt-items" id="receiptItems">
                    <!-- Receipt items will be added here dynamically -->
                </div>
                
                <div class="receipt-divider"></div>
                
                <div class="receipt-summary">
                    <div class="receipt-info">
                        <span>Subtotal:</span>
                        <span id="receiptSubtotal"></span>
                    </div>
                    <div class="receipt-info">
                        <span>Tax (2%):</span>
                        <span id="receiptTax"></span>
                    </div>
                    <div class="receipt-total">
                        <span>TOTAL:</span>
                        <span id="receiptTotal"></span>
                    </div>
                </div>
                
                <div class="receipt-divider"></div>
                
                <div class="receipt-info">
                    <span>Payment Method:</span>
                    <span id="receiptPaymentMethod"></span>
                </div>
                
                <div class="receipt-info" id="receiptCashInfo">
                    <span>Amount Received:</span>
                    <span id="receiptAmountReceived"></span>
                </div>
                
                <div class="receipt-info" id="receiptChangeInfo">
                    <span>Change:</span>
                    <span id="receiptChange"></span>
                </div>
                
                <div class="receipt-divider"></div>
                
                <div class="receipt-footer">
                    <p>Thank you for shopping at KICKS!</p>
                    <p>Follow us @kicks_official</p>
                </div>
            </div>
            
            <div class="receipt-actions">
                <button class="print-btn" id="printReceipt">
                    <i class="fas fa-print"></i> Print Receipt
                </button>
                <button class="new-sale-btn" id="newSale">
                    <i class="fas fa-plus"></i> New Sale
                </button>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Theme Toggle
            const themeToggle = document.querySelector('.theme-toggle');
            const body = document.body;
            
            // Check for saved theme preference
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme) {
                body.setAttribute('data-theme', savedTheme);
            }
            
            themeToggle.addEventListener('click', function() {
                const currentTheme = body.getAttribute('data-theme');
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                
                body.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
            });
            
            // User Dropdown
            const userProfile = document.querySelector('.user-profile');
            const userDropdown = document.querySelector('.user-dropdown');
            
            userProfile.addEventListener('click', function(e) {
                e.stopPropagation();
                userDropdown.classList.toggle('active');
            });
            
            document.addEventListener('click', function(e) {
                if (!userProfile.contains(e.target)) {
                    userDropdown.classList.remove('active');
                }
            });
            
            // Mobile Menu
            const hamburger = document.querySelector('.hamburger');
            const navLinks = document.querySelector('.nav-links');
            const navButtons = document.querySelector('.nav-buttons');
            
            hamburger.addEventListener('click', function() {
                hamburger.classList.toggle('active');
                navLinks.classList.toggle('active');
                navButtons.classList.toggle('active');
            });
            
            // Navbar Scroll Effect
            window.addEventListener('scroll', function() {
                const navbar = document.querySelector('.navbar');
                if (window.scrollY > 10) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });
            
            // Category Filtering
            const categoryButtons = document.querySelectorAll('.category-btn');
            const productCards = document.querySelectorAll('.product-card');
            let searchTerm = '';
            
            function filterProducts() {
                const activeCategory = document.querySelector('.category-btn.active').getAttribute('data-category');
                const searchValue = searchTerm.toLowerCase();
                
                productCards.forEach(card => {
                    const categoryMatch = activeCategory === 'all' || 
                        card.getAttribute('data-category') === activeCategory;
                    
                    const name = card.getAttribute('data-name').toLowerCase();
                    const category = card.getAttribute('data-category').toLowerCase();
                    const barcode = card.getAttribute('data-barcode').toLowerCase();
                    
                    const searchMatch = name.includes(searchValue) || 
                        category.includes(searchValue) || 
                        barcode.includes(searchValue);
                    
                    card.style.display = (categoryMatch && searchMatch) ? 'block' : 'none';
                });
            }
            
            categoryButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons
                    categoryButtons.forEach(btn => btn.classList.remove('active'));
                    
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    // Filter products
                    filterProducts();
                });
            });
            
            // Search Functionality
            const productSearch = document.getElementById('productSearch');
            const clearSearchBtn = document.getElementById('clearSearch');
            
            productSearch.addEventListener('input', function(e) {
                searchTerm = e.target.value;
                clearSearchBtn.classList.toggle('visible', searchTerm.length > 0);
                filterProducts();
            });
            
            clearSearchBtn.addEventListener('click', function() {
                productSearch.value = '';
                                searchTerm = '';
                clearSearchBtn.classList.remove('visible');
                filterProducts();
            });
            
            // Barcode Scanner Functionality
            const barcodeInput = document.getElementById('barcodeInput');
            const barcodeStatus = document.getElementById('barcode-status');
            
            barcodeInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const barcode = this.value.trim();
                    
                    if (barcode) {
                        // Find product with matching barcode
                        const product = Array.from(productCards).find(card => 
                            card.getAttribute('data-barcode') === barcode
                        );
                        
                        if (product) {
                            // Add product to bill
                            addToBill(product);
                            
                            // Show success message
                            barcodeStatus.innerHTML = '<i class="fas fa-check-circle"></i><span>Product added to bill</span>';
                            barcodeStatus.classList.add('success');
                            
                            setTimeout(() => {
                                barcodeStatus.innerHTML = '<i class="fas fa-info-circle"></i><span>Ready to scan</span>';
                                barcodeStatus.classList.remove('success');
                            }, 2000);
                        } else {
                            // Show error message
                            barcodeStatus.innerHTML = '<i class="fas fa-exclamation-circle"></i><span>Product not found</span>';
                            barcodeStatus.classList.add('error');
                            
                            setTimeout(() => {
                                barcodeStatus.innerHTML = '<i class="fas fa-info-circle"></i><span>Ready to scan</span>';
                                barcodeStatus.classList.remove('error');
                            }, 2000);
                        }
                    }
                    
                    // Clear input
                    this.value = '';
                }
            });
            
            // Bill Management
            const billItems = document.getElementById('billItems');
            const billSubtotal = document.getElementById('billSubtotal');
            const billTax = document.getElementById('billTax');
            const billTotal = document.getElementById('billTotal');
            const checkoutBtn = document.getElementById('checkoutBtn');
            let cart = [];
            
            // Add product to bill
            function addToBill(productCard) {
                if (productCard.hasAttribute('disabled')) {
                    return; // Don't add out-of-stock products
                }
                
                const productId = productCard.getAttribute('data-id');
                const productName = productCard.getAttribute('data-name');
                const productPrice = parseFloat(productCard.getAttribute('data-price'));
                
                // Check if product already in cart
                const existingItem = cart.find(item => item.id === productId);
                
                if (existingItem) {
                    // Increment quantity
                    existingItem.quantity++;
                    existingItem.total = existingItem.quantity * existingItem.price;
                    
                    // Update quantity in UI
                    const quantityElement = document.querySelector(`.bill-item[data-id="${productId}"] .bill-item-quantity`);
                    quantityElement.textContent = existingItem.quantity;
                    
                    // Update total in UI
                    const totalElement = document.querySelector(`.bill-item[data-id="${productId}"] .bill-item-total`);
                    totalElement.textContent = `₱${existingItem.total.toFixed(2)}`;
                } else {
                    // Add new item to cart
                    const newItem = {
                        id: productId,
                        name: productName,
                        price: productPrice,
                        quantity: 1,
                        total: productPrice
                    };
                    
                    cart.push(newItem);
                    
                    // Create bill item element
                    const billItem = document.createElement('div');
                    billItem.className = 'bill-item';
                    billItem.setAttribute('data-id', productId);
                    
                    billItem.innerHTML = `
                        <div class="bill-item-name">${productName}</div>
                        <div class="bill-item-price">₱${productPrice.toFixed(2)}</div>
                        <div class="bill-item-quantity-controls">
                            <button class="quantity-btn quantity-decrease">-</button>
                            <div class="bill-item-quantity">1</div>
                            <button class="quantity-btn quantity-increase">+</button>
                        </div>
                        <div class="bill-item-total">₱${productPrice.toFixed(2)}</div>
                        <button class="bill-item-remove"><i class="fas fa-times"></i></button>
                    `;
                    
                    billItems.appendChild(billItem);
                    
                    // Add event listeners for quantity controls
                    const decreaseBtn = billItem.querySelector('.quantity-decrease');
                    const increaseBtn = billItem.querySelector('.quantity-increase');
                    const removeBtn = billItem.querySelector('.bill-item-remove');
                    
                    decreaseBtn.addEventListener('click', function() {
                        updateItemQuantity(productId, -1);
                    });
                    
                    increaseBtn.addEventListener('click', function() {
                        updateItemQuantity(productId, 1);
                    });
                    
                    removeBtn.addEventListener('click', function() {
                        removeItem(productId);
                    });
                }
                
                // Update bill summary
                updateBillSummary();
            }
            
            // Update item quantity
            function updateItemQuantity(productId, change) {
                const item = cart.find(item => item.id === productId);
                
                if (item) {
                    item.quantity += change;
                    
                    if (item.quantity <= 0) {
                        // Remove item if quantity is 0 or less
                        removeItem(productId);
                    } else {
                        // Update quantity and total
                        item.total = item.quantity * item.price;
                        
                        // Update UI
                        const quantityElement = document.querySelector(`.bill-item[data-id="${productId}"] .bill-item-quantity`);
                        quantityElement.textContent = item.quantity;
                        
                        const totalElement = document.querySelector(`.bill-item[data-id="${productId}"] .bill-item-total`);
                        totalElement.textContent = `₱${item.total.toFixed(2)}`;
                        
                        // Update bill summary
                        updateBillSummary();
                    }
                }
            }
            
            // Remove item from bill
            function removeItem(productId) {
                // Remove from cart array
                cart = cart.filter(item => item.id !== productId);
                
                // Remove from UI
                const billItem = document.querySelector(`.bill-item[data-id="${productId}"]`);
                billItem.remove();
                
                // Update bill summary
                updateBillSummary();
            }
            
            // Update bill summary
            function updateBillSummary() {
                const subtotal = cart.reduce((sum, item) => sum + item.total, 0);
                const tax = subtotal * 0.02; // 2% tax
                const total = subtotal + tax;
                
                billSubtotal.textContent = `Subtotal: ₱${subtotal.toFixed(2)}`;
                billTax.textContent = `Tax (2%): ₱${tax.toFixed(2)}`;
                billTotal.textContent = `Total: ₱${total.toFixed(2)}`;
                
                // Enable/disable checkout button
                checkoutBtn.disabled = cart.length === 0;
            }
            
            // Add products to bill when clicked
            productCards.forEach(card => {
                card.addEventListener('click', function() {
                    if (!this.hasAttribute('disabled')) {
                        addToBill(this);
                    }
                });
            });
            
            // Payment Modal
            const paymentModal = document.getElementById('paymentModal');
            const closePaymentModal = document.getElementById('closePaymentModal');
            const paymentMethods = document.querySelectorAll('.payment-method');
            const cashDetails = document.getElementById('cashDetails');
            const cardDetails = document.getElementById('cardDetails');
            const gcashDetails = document.getElementById('gcashDetails');
            const mayaDetails = document.getElementById('mayaDetails');
            const cashAmount = document.getElementById('cashAmount');
            const changeAmount = document.getElementById('changeAmount');
            const cancelPayment = document.getElementById('cancelPayment');
            const confirmPayment = document.getElementById('confirmPayment');
            
            let selectedPaymentMethod = null;
            
            // Show payment modal
            checkoutBtn.addEventListener('click', function() {
                if (cart.length > 0) {
                    // Reset payment form
                    paymentMethods.forEach(method => method.classList.remove('selected'));
                    cashDetails.style.display = 'none';
                    cardDetails.style.display = 'none';
                    gcashDetails.style.display = 'none';
                    mayaDetails.style.display = 'none';
                    cashAmount.value = '';
                    changeAmount.textContent = '₱0.00';
                    selectedPaymentMethod = null;
                    
                    // Show modal
                    paymentModal.style.display = 'flex';
                }
            });
            
            // Close payment modal
            closePaymentModal.addEventListener('click', function() {
                paymentModal.style.display = 'none';
            });
            
            cancelPayment.addEventListener('click', function() {
                paymentModal.style.display = 'none';
            });
            
            // Select payment method
            paymentMethods.forEach(method => {
                method.addEventListener('click', function() {
                    // Remove selected class from all methods
                    paymentMethods.forEach(m => m.classList.remove('selected'));
                    
                    // Add selected class to clicked method
                    this.classList.add('selected');
                    
                    // Get payment method
                    selectedPaymentMethod = this.getAttribute('data-method');
                    
                    // Show/hide payment details
                    cashDetails.style.display = selectedPaymentMethod === 'cash' ? 'block' : 'none';
                    cardDetails.style.display = selectedPaymentMethod === 'card' ? 'block' : 'none';
                    gcashDetails.style.display = selectedPaymentMethod === 'gcash' ? 'block' : 'none';
                    mayaDetails.style.display = selectedPaymentMethod === 'maya' ? 'block' : 'none';
                    
                    // Set focus on cash amount input if cash is selected
                    if (selectedPaymentMethod === 'cash') {
                        setTimeout(() => cashAmount.focus(), 100);
                    }
                });
            });
            
            // Calculate change
            cashAmount.addEventListener('input', function() {
                const total = cart.reduce((sum, item) => sum + item.total, 0) * 1.02; // Including tax
                const received = parseFloat(this.value) || 0;
                const change = received - total;
                
                changeAmount.textContent = change >= 0 ? `₱${change.toFixed(2)}` : '₱0.00';
            });
            
            // Receipt Modal
            const receiptModal = document.getElementById('receiptModal');
            const closeReceiptModal = document.getElementById('closeReceiptModal');
            const receiptNumber = document.getElementById('receiptNumber');
            const receiptDate = document.getElementById('receiptDate');
            const receiptTime = document.getElementById('receiptTime');
            const receiptCashier = document.getElementById('receiptCashier');
            const receiptItems = document.getElementById('receiptItems');
            const receiptSubtotal = document.getElementById('receiptSubtotal');
            const receiptTax = document.getElementById('receiptTax');
            const receiptTotal = document.getElementById('receiptTotal');
            const receiptPaymentMethod = document.getElementById('receiptPaymentMethod');
            const receiptCashInfo = document.getElementById('receiptCashInfo');
            const receiptAmountReceived = document.getElementById('receiptAmountReceived');
            const receiptChangeInfo = document.getElementById('receiptChangeInfo');
            const receiptChange = document.getElementById('receiptChange');
            const printReceipt = document.getElementById('printReceipt');
            const newSale = document.getElementById('newSale');
            
            // Complete payment
            confirmPayment.addEventListener('click', function() {
                if (!selectedPaymentMethod) {
                    alert('Please select a payment method');
                    return;
                }
                
                // Validate payment details
                if (selectedPaymentMethod === 'cash') {
                    const total = cart.reduce((sum, item) => sum + item.total, 0) * 1.02; // Including tax
                    const received = parseFloat(cashAmount.value) || 0;
                    
                    if (received < total) {
                        alert('Insufficient amount');
                        return;
                    }
                } else if (selectedPaymentMethod === 'card') {
                    const cardNumber = document.getElementById('cardNumber').value;
                    const cardExpiry = document.getElementById('cardExpiry').value;
                    const cardCvv = document.getElementById('cardCvv').value;
                    
                    if (!cardNumber || !cardExpiry || !cardCvv) {
                        alert('Please fill in all card details');
                        return;
                    }
                } else if (selectedPaymentMethod === 'gcash') {
                    const gcashNumber = document.getElementById('gcashNumber').value;
                    const gcashReference = document.getElementById('gcashReference').value;
                    
                    if (!gcashNumber || !gcashReference) {
                        alert('Please fill in all GCash details');
                        return;
                    }
                } else if (selectedPaymentMethod === 'maya') {
                    const mayaNumber = document.getElementById('mayaNumber').value;
                    const mayaReference = document.getElementById('mayaReference').value;
                    
                    if (!mayaNumber || !mayaReference) {
                        alert('Please fill in all Maya details');
                        return;
                    }
                }
                
                // Close payment modal
                paymentModal.style.display = 'none';
                
                // Generate receipt
                generateReceipt();
                
                // Show receipt modal
                receiptModal.style.display = 'flex';
            });
            
            // Generate receipt
            function generateReceipt() {
                // Generate receipt number
                const receiptNum = 'R' + Date.now().toString().slice(-6);
                receiptNumber.textContent = receiptNum;
                
                // Set date and time
                const now = new Date();
                receiptDate.textContent = now.toLocaleDateString();
                receiptTime.textContent = now.toLocaleTimeString();
                
                // Set cashier name
                receiptCashier.textContent = '<?php echo $cashierName; ?>';
                
                // Add items to receipt
                receiptItems.innerHTML = '';
                cart.forEach(item => {
                    const itemRow = document.createElement('div');
                    itemRow.className = 'receipt-item';
                    itemRow.innerHTML = `
                        <div class="receipt-item-name">${item.name}</div>
                        <div class="receipt-item-qty">${item.quantity}</div>
                        <div class="receipt-item-price">₱${item.total.toFixed(2)}</div>
                    `;
                    receiptItems.appendChild(itemRow);
                });
                
                // Set totals
                const subtotal = cart.reduce((sum, item) => sum + item.total, 0);
                const tax = subtotal * 0.02;
                const total = subtotal + tax;
                
                receiptSubtotal.textContent = `₱${subtotal.toFixed(2)}`;
                receiptTax.textContent = `₱${tax.toFixed(2)}`;
                receiptTotal.textContent = `₱${total.toFixed(2)}`;
                
                // Set payment method
                const paymentMethodNames = {
                    'cash': 'Cash',
                    'card': 'Credit/Debit Card',
                    'gcash': 'GCash',
                    'maya': 'Maya'
                };
                receiptPaymentMethod.textContent = paymentMethodNames[selectedPaymentMethod];
                
                // Show/hide cash details
                if (selectedPaymentMethod === 'cash') {
                    const received = parseFloat(cashAmount.value) || 0;
                    const change = received - total;
                    
                    receiptCashInfo.style.display = 'flex';
                    receiptChangeInfo.style.display = 'flex';
                    receiptAmountReceived.textContent = `₱${received.toFixed(2)}`;
                    receiptChange.textContent = `₱${change.toFixed(2)}`;
                } else {
                    receiptCashInfo.style.display = 'none';
                    receiptChangeInfo.style.display = 'none';
                }
            }
            
            // Close receipt modal
            closeReceiptModal.addEventListener('click', function() {
                receiptModal.style.display = 'none';
            });
            
            // Print receipt
            printReceipt.addEventListener('click', function() {
                const receiptContent = document.getElementById('receipt');
                const printWindow = window.open('', '', 'width=600,height=600');
                
                printWindow.document.write('<html><head><title>Receipt</title>');
                printWindow.document.write('<style>');
                printWindow.document.write(`
                    body { font-family: 'Courier New', monospace; margin: 0; padding: 20px; }
                    .receipt { width: 300px; margin: 0 auto; }
                    .receipt-header { text-align: center; margin-bottom: 20px; }
                    .receipt-logo { font-size: 24px; font-weight: bold; margin-bottom: 5px; }
                    .receipt-address, .receipt-contact { font-size: 12px; margin-bottom: 5px; }
                    .receipt-divider { border-top: 1px dashed #000; margin: 10px 0; }
                    .receipt-info { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 5px; }
                    .receipt-items { margin: 15px 0; }
                    .receipt-item { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 5px; }
                    .receipt-item-name { width: 60%; }
                    .receipt-item-qty { width: 10%; text-align: center; }
                    .receipt-item-price { width: 30%; text-align: right; }
                    .receipt-summary { margin-top: 10px; }
                    .receipt-total { font-weight: bold; font-size: 14px; text-align: right; margin-top: 5px; }
                    .receipt-footer { text-align: center; font-size: 12px; margin-top: 20px; }
                `);
                printWindow.document.write('</style></head><body>');
                printWindow.document.write(receiptContent.innerHTML);
                printWindow.document.write('</body></html>');
                
                printWindow.document.close();
                printWindow.focus();
                
                // Print after a short delay to ensure content is loaded
                setTimeout(() => {
                    printWindow.print();
                    printWindow.close();
                }, 500);
            });
            
            // Start new sale
            newSale.addEventListener('click', function() {
                // Clear cart
                cart = [];
                
                // Clear bill items
                billItems.innerHTML = '';
                
                // Reset bill summary
                updateBillSummary();
                
                // Close receipt modal
                receiptModal.style.display = 'none';
            });
            
            // Close modals when clicking outside
            window.addEventListener('click', function(e) {
                if (e.target === paymentModal) {
                    paymentModal.style.display = 'none';
                }
                
                if (e.target === receiptModal) {
                    receiptModal.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
