<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KICKS | Customer Dashboard</title>
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

 /* Dashboard Layout */
 .dashboard-container {
     display: grid;
     grid-template-columns: 250px 1fr 350px;
     gap: 20px;
     padding: 20px 5%;
     min-height: calc(100vh - 80px);
 }

 /* Category Section */
 .category-section {
     background-color: var(--background-card);
     border-radius: var(--border-radius-md);
     padding: 20px;
     box-shadow: var(--shadow-sm);
     height: calc(100vh - 120px);
     overflow-y: auto;
     transition: background-color var(--transition-speed) ease, box-shadow var(--transition-speed) ease;
 }

 .section-header {
     font-size: 20px;
     font-weight: 600;
     margin-bottom: 20px;
     padding-bottom: 10px;
     border-bottom: 1px solid var(--border-color);
     display: flex;
     align-items: center;
     gap: 10px;
     transition: border-color var(--transition-speed) ease;
 }

 .section-header i {
     color: var(--primary-color);
 }

 .category-list {
     list-style: none;
 }

 .category-item {
     padding: 12px 15px;
     margin-bottom: 8px;
     border-radius: var(--border-radius-sm);
     cursor: pointer;
     transition: all 0.3s ease;
     display: flex;
     align-items: center;
     gap: 10px;
 }

 .category-item:hover, .category-item.active {
     background-color: var(--primary-light);
     color: var(--primary-color);
 }

 .category-item i {
     width: 20px;
     text-align: center;
 }

 /* Products Section */
 .products-section {
     background-color: var(--background-card);
     border-radius: var(--border-radius-md);
     padding: 20px;
     box-shadow: var(--shadow-sm);
     height: calc(100vh - 120px);
     overflow-y: auto;
     transition: background-color var(--transition-speed) ease, box-shadow var(--transition-speed) ease;
 }

 .search-bar {
     display: flex;
     margin-bottom: 20px;
 }

 .search-input {
     flex: 1;
     padding: 12px 15px;
     border-radius: var(--border-radius-sm) 0 0 var(--border-radius-sm);
     border: 1px solid var(--border-color);
     background-color: rgba(255, 255, 255, 0.05);
     color: var(--text-light);
     font-size: 14px;
     transition: border-color var(--transition-speed) ease, background-color var(--transition-speed) ease;
 }

 [data-theme="light"] .search-input {
     background-color: rgba(0, 0, 0, 0.05);
 }

 .search-input:focus {
     outline: none;
     border-color: var(--primary-color);
 }

 .search-btn {
     padding: 12px 20px;
     border-radius: 0 var(--border-radius-sm) var(--border-radius-sm) 0;
     background-color: var(--primary-color);
     color: white;
     border: none;
     cursor: pointer;
     transition: all 0.3s ease;
 }

 .search-btn:hover {
     background-color: var(--primary-dark);
 }

 .products-grid {
     display: grid;
     grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
     gap: 20px;
 }

 .product-card {
     background-color: rgba(255, 255, 255, 0.05);
     border-radius: var(--border-radius-md);
     overflow: hidden;
     transition: all 0.3s ease;
     cursor: pointer;
 }

 [data-theme="light"] .product-card {
     background-color: rgba(0, 0, 0, 0.03);
 }

 .product-card:hover {
     transform: translateY(-5px);
     box-shadow: var(--shadow-md);
 }

 .product-image {
     width: 100%;
     height: 150px;
     object-fit: cover;
 }

 .product-info {
     padding: 15px;
 }

 .product-name {
     font-size: 16px;
     font-weight: 600;
     margin-bottom: 5px;
     white-space: nowrap;
     overflow: hidden;
     text-overflow: ellipsis;
 }

 .product-price {
     font-size: 18px;
     font-weight: 700;
     color: var(--text-price);
     margin-bottom: 10px;
 }

 .product-btn {
     width: 100%;
     padding: 8px 0;
     border-radius: var(--border-radius-sm);
     background-color: var(--primary-color);
     color: white;
     border: none;
     font-weight: 600;
     cursor: pointer;
     transition: all 0.3s ease;
     display: flex;
     align-items: center;
     justify-content: center;
     gap: 8px;
 }

 .product-btn:hover {
     background-color: var(--primary-dark);
 }

 /* Cart Section */
 .cart-section {
     background-color: var(--background-card);
     border-radius: var(--border-radius-md);
     padding: 20px;
     box-shadow: var(--shadow-sm);
     height: calc(100vh - 120px);
     display: flex;
     flex-direction: column;
     transition: background-color var(--transition-speed) ease, box-shadow var(--transition-speed) ease;
 }

 .cart-items {
     flex: 1;
     overflow-y: auto;
     margin-bottom: 20px;
 }

 .cart-item {
     display: flex;
     align-items: center;
     padding: 15px 0;
     border-bottom: 1px solid var(--border-color);
     transition: border-color var(--transition-speed) ease;
 }

 .cart-item-image {
     width: 60px;
     height: 60px;
     border-radius: var(--border-radius-sm);
     object-fit: cover;
     margin-right: 15px;
 }

 .cart-item-details {
     flex: 1;
 }

 .cart-item-name {
     font-size: 14px;
     font-weight: 600;
     margin-bottom: 5px;
 }

 .cart-item-price {
     font-size: 14px;
     color: var(--text-price);
 }

 .cart-item-quantity {
     display: flex;
     align-items: center;
     margin-left: 15px;
 }

 .quantity-btn {
     width: 25px;
     height: 25px;
     border-radius: 50%;
     background-color: rgba(255, 255, 255, 0.1);
     color: var(--text-light);
     border: none;
     display: flex;
     align-items: center;
     justify-content: center;
     cursor: pointer;
     transition: all 0.3s ease;
 }

 [data-theme="light"] .quantity-btn {
     background-color: rgba(0, 0, 0, 0.1);
 }

 .quantity-btn:hover {
     background-color: var(--primary-color);
 }

 .quantity-value {
     margin: 0 10px;
     font-weight: 600;
 }

 .cart-item-remove {
     color: var(--error-color);
     background: none;
     border: none;
     cursor: pointer;
     margin-left: 10px;
     transition: all 0.3s ease;
 }

 .cart-item-remove:hover {
     transform: scale(1.2);
 }

 .cart-summary {
     padding: 20px 0;
     border-top: 1px solid var(--border-color);
     transition: border-color var(--transition-speed) ease;
 }

 .cart-row {
     display: flex;
     justify-content: space-between;
     margin-bottom: 10px;
 }

 .cart-total {
     font-size: 18px;
     font-weight: 700;
     color: var(--primary-color);
 }

 .checkout-btn {
     width: 100%;
     padding: 15px 0;
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
     margin-top: 20px;
 }

 .checkout-btn:hover {
     background-color: var(--primary-dark);
     transform: translateY(-2px);
     box-shadow: 0 4px 10px rgba(76, 175, 80, 0.3);
 }

 .empty-cart {
     display: flex;
     flex-direction: column;
     align-items: center;
     justify-content: center;
     height: 100%;
     color: var(--text-muted);
     text-align: center;
 }

 .empty-cart i {
     font-size: 48px;
     margin-bottom: 15px;
     color: rgba(255, 255, 255, 0.2);
 }

 [data-theme="light"] .empty-cart i {
     color: rgba(0, 0, 0, 0.2);
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
 
 @keyframes bounce {
     0%, 100% {
         transform: translateY(0);
     }
     50% {
         transform: translateY(-10px);
     }
 }

 /* Responsive Styles */
 @media (max-width: 1200px) {
     .dashboard-container {
         grid-template-columns: 200px 1fr 300px;
     }
 }

 @media (max-width: 992px) {
     .dashboard-container {
         grid-template-columns: 1fr;
         grid-template-rows: auto;
     }

     .category-section, .products-section, .cart-section {
         height: auto;
         max-height: 500px;
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

 /* Badge for cart count */
 .cart-badge {
     position: relative;
     display: inline-flex;
 }

 .cart-badge[data-count]:after {
     content: attr(data-count);
     position: absolute;
     top: -10px;
     right: -10px;
     width: 20px;
     height: 20px;
     line-height: 20px;
     text-align: center;
     background-color: var(--primary-color);
     color: white;
     border-radius: 50%;
     font-size: 12px;
     font-weight: 600;
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

 /* Filter Section Styles */
 .filter-section {
     margin-top: 25px;
     padding-top: 15px;
     border-top: 1px solid var(--border-color);
 }
 
 .filter-header {
     font-size: 16px;
     font-weight: 600;
     margin-bottom: 15px;
     display: flex;
     align-items: center;
     gap: 8px;
 }
 
 .filter-header i {
     color: var(--primary-color);
 }
 
 /* Price Range Styles */
 .price-range-container {
     margin-bottom: 20px;
 }
 
 .price-inputs {
     display: flex;
     align-items: center;
     gap: 8px;
     margin-bottom: 10px;
 }
 
 .price-input {
     width: 70px;
     padding: 8px;
     border-radius: var(--border-radius-sm);
     border: 1px solid var(--border-color);
     background-color: rgba(255, 255, 255, 0.05);
     color: var(--text-light);
     font-size: 14px;
 }
 
 [data-theme="light"] .price-input {
     background-color: rgba(0, 0, 0, 0.05);
 }
 
 .price-input:focus {
     outline: none;
     border-color: var(--primary-color);
 }
 
 .range-slider {
     position: relative;
     height: 5px;
     margin: 20px 0;
 }
 
 .slider-track {
     position: absolute;
     top: 0;
     left: 0;
     right: 0;
     height: 5px;
     background-color: var(--border-color);
     border-radius: 5px;
 }
 
 .slider-range {
     position: absolute;
     top: 0;
     height: 5px;
     background-color: var(--primary-color);
     border-radius: 5px;
 }
 
 .range-input {
     position: relative;
     width: 100%;
 }
 
 .range-min, .range-max {
     position: absolute;
     top: -5px;
     width: 100%;
     height: 5px;
     background: none;
     pointer-events: none;
     -webkit-appearance: none;
 }
 
 .range-min::-webkit-slider-thumb, .range-max::-webkit-slider-thumb {
     -webkit-appearance: none;
     width: 15px;
     height: 15px;
     border-radius: 50%;
     background-color: var(--primary-color);
     cursor: pointer;
     pointer-events: auto;
     box-shadow: 0 0 0 3px var(--background-card), 0 0 0 4px var(--primary-color);
 }
 
 .apply-price-btn {
     width: 100%;
     padding: 8px 0;
     border-radius: var(--border-radius-sm);
     background-color: var(--primary-color);
     color: white;
     border: none;
     font-weight: 600;
     cursor: pointer;
     transition: all 0.3s ease;
     margin-top: 10px;
 }
 
 .apply-price-btn:hover {
     background-color: var(--primary-dark);
 }
 
 /* Brands Styles */
 .brands-list {
     display: flex;
     flex-direction: column;
     gap: 10px;
     margin-bottom: 20px;
 }
 

 .brands-list {
     display: flex;
     flex-direction: column;
     gap: 10px;
     margin-bottom: 20px;
 }
 
 .brand-item {
     display: flex;
     align-items: center;
     gap: 10px;
 }
 
 .brand-checkbox {
     -webkit-appearance: none;
     appearance: none;
     width: 18px;
     height: 18px;
     border: 1px solid var(--border-color);
     border-radius: 4px;
     background-color: rgba(255, 255, 255, 0.05);
     cursor: pointer;
     position: relative;
     transition: all 0.3s ease;
 }
 
 [data-theme="light"] .brand-checkbox {
     background-color: rgba(0, 0, 0, 0.05);
 }
 
 .brand-checkbox:checked {
     background-color: var(--primary-color);
     border-color: var(--primary-color);
 }
 
 .brand-checkbox:checked::after {
     content: '\f00c';
     font-family: 'Font Awesome 6 Free';
     font-weight: 900;
     position: absolute;
     top: 50%;
     left: 50%;
     transform: translate(-50%, -50%);
     color: white;
     font-size: 10px;
 }
 
 .brand-label {
     cursor: pointer;
     font-size: 14px;
     transition: color 0.3s ease;
 }
 
 .brand-item:hover .brand-label {
     color: var(--primary-color);
 }
 
 /* Sizes Styles */
 .sizes-grid {
     display: grid;
     grid-template-columns: repeat(4, 1fr);
     gap: 10px;
     margin-bottom: 20px;
 }
 
 .size-item {
     position: relative;
 }
 
 .size-checkbox {
     position: absolute;
     opacity: 0;
     cursor: pointer;
 }
 
 .size-label {
     display: flex;
     align-items: center;
     justify-content: center;
     height: 35px;
     border-radius: var(--border-radius-sm);
     border: 1px solid var(--border-color);
     background-color: rgba(255, 255, 255, 0.05);
     cursor: pointer;
     font-size: 14px;
     transition: all 0.3s ease;
 }
 
 [data-theme="light"] .size-label {
     background-color: rgba(0, 0, 0, 0.05);
 }
 
 .size-checkbox:checked + .size-label {
     background-color: var(--primary-color);
     border-color: var(--primary-color);
     color: white;
 }
 
 .size-item:hover .size-label {
     border-color: var(--primary-color);
     color: var(--primary-color);
 }
 
 .size-checkbox:checked + .size-label:hover {
     color: white;
 }
 
 /* Ratings Styles */
 .ratings-list {
     display: flex;
     flex-direction: column;
     gap: 10px;
     margin-bottom: 20px;
 }
 
 .rating-item {
     display: flex;
     align-items: center;
     gap: 10px;
     cursor: pointer;
     padding: 5px;
     border-radius: var(--border-radius-sm);
     transition: background-color 0.3s ease;
 }
 
 .rating-item:hover {
     background-color: rgba(255, 255, 255, 0.05);
 }
 
 [data-theme="light"] .rating-item:hover {
     background-color: rgba(0, 0, 0, 0.05);
 }
 
 .rating-stars {
     display: flex;
     gap: 2px;
 }
 
 .rating-star {
     color: #FFD700;
     font-size: 14px;
 }
 
 .rating-count {
     font-size: 12px;
     color: var(--text-muted);
 }
 
 /* Reset Filters Button */
 .reset-filters {
     display: flex;
     align-items: center;
     justify-content: center;
     gap: 8px;
     width: 100%;
     padding: 10px 0;
     border-radius: var(--border-radius-sm);
     background-color: rgba(255, 255, 255, 0.05);
     color: var(--text-light);
     border: 1px solid var(--border-color);
     font-weight: 500;
     cursor: pointer;
     transition: all 0.3s ease;
     margin-top: 20px;
 }
 
 [data-theme="light"] .reset-filters {
     background-color: rgba(0, 0, 0, 0.05);
 }
 
 .reset-filters:hover {
     background-color: rgba(244, 67, 54, 0.1);
     color: var(--error-color);
     border-color: var(--error-color);
 }
 
 /* Pagination Styles */
 .pagination {
     display: flex;
     justify-content: center;
     align-items: center;
     gap: 10px;
     margin-top: 30px;
 }
 
 .page-item {
     width: 35px;
     height: 35px;
     display: flex;
     align-items: center;
     justify-content: center;
     border-radius: var(--border-radius-sm);
     background-color: rgba(255, 255, 255, 0.05);
     color: var(--text-light);
     cursor: pointer;
     transition: all 0.3s ease;
 }
 
 [data-theme="light"] .page-item {
     background-color: rgba(0, 0, 0, 0.05);
 }
 
 .page-item.active, .page-item:hover {
     background-color: var(--primary-color);
     color: white;
 }
 
 .page-item.disabled {
     opacity: 0.5;
     cursor: not-allowed;
 }
 
 /* Product Details Modal */
 .modal {
     position: fixed;
     top: 0;
     left: 0;
     width: 100%;
     height: 100%;
     background-color: rgba(0, 0, 0, 0.7);
     display: flex;
     align-items: center;
     justify-content: center;
     z-index: 2000;
     opacity: 0;
     visibility: hidden;
     transition: all 0.3s ease;
 }
 
 .modal.active {
     opacity: 1;
     visibility: visible;
 }
 
 .modal-content {
     background-color: var(--background-card);
     border-radius: var(--border-radius-lg);
     width: 90%;
     max-width: 800px;
     max-height: 90vh;
     overflow-y: auto;
     box-shadow: var(--shadow-md);
     animation: fadeInUp 0.3s ease forwards;
     transition: background-color var(--transition-speed) ease;
 }
 
 .modal-header {
     display: flex;
     justify-content: space-between;
     align-items: center;
     padding: 20px;
     border-bottom: 1px solid var(--border-color);
 }
 
 .modal-title {
     font-size: 20px;
     font-weight: 600;
 }
 
 .modal-close {
     background: none;
     border: none;
     color: var(--text-light);
     font-size: 20px;
     cursor: pointer;
     transition: all 0.3s ease;
 }
 
 .modal-close:hover {
     color: var(--error-color);
     transform: rotate(90deg);
 }
 
 .modal-body {
     padding: 20px;
     display: flex;
     flex-direction: column;
     gap: 20px;
 }
 
 @media (min-width: 768px) {
     .modal-body {
         flex-direction: row;
     }
 }
 
 .product-gallery {
     flex: 1;
 }
 
 .product-main-image {
     width: 100%;
     height: 300px;
     object-fit: cover;
     border-radius: var(--border-radius-md);
     margin-bottom: 15px;
 }
 
 .product-thumbnails {
     display: flex;
     gap: 10px;
     overflow-x: auto;
     padding-bottom: 10px;
 }
 
 .product-thumbnail {
     width: 60px;
     height: 60px;
     object-fit: cover;
     border-radius: var(--border-radius-sm);
     cursor: pointer;
     transition: all 0.3s ease;
 }
 
 .product-thumbnail:hover, .product-thumbnail.active {
     border: 2px solid var(--primary-color);
 }
 
 .product-details {
     flex: 1;
     display: flex;
     flex-direction: column;
     gap: 15px;
 }
 
 .product-detail-name {
     font-size: 24px;
     font-weight: 700;
 }
 
 .product-detail-price {
     font-size: 22px;
     font-weight: 700;
     color: var(--text-price);
 }
 
 .product-detail-description {
     color: var(--text-muted);
     line-height: 1.6;
 }
 
 .product-detail-sizes {
     display: flex;
     flex-wrap: wrap;
     gap: 10px;
     margin-top: 10px;
 }
 
 .product-detail-size {
     width: 40px;
     height: 40px;
     display: flex;
     align-items: center;
     justify-content: center;
     border-radius: var(--border-radius-sm);
     border: 1px solid var(--border-color);
     background-color: rgba(255, 255, 255, 0.05);
     cursor: pointer;
     transition: all 0.3s ease;
 }
 
 [data-theme="light"] .product-detail-size {
     background-color: rgba(0, 0, 0, 0.05);
 }
 
 .product-detail-size.active, .product-detail-size:hover {
     background-color: var(--primary-color);
     color: white;
     border-color: var(--primary-color);
 }
 
 .product-detail-actions {
     display: flex;
     gap: 15px;
     margin-top: 20px;
 }
 
 .product-detail-quantity {
     display: flex;
     align-items: center;
     border: 1px solid var(--border-color);
     border-radius: var(--border-radius-sm);
     overflow: hidden;
 }
 
 .quantity-detail-btn {
     width: 40px;
     height: 40px;
     background-color: rgba(255, 255, 255, 0.05);
     color: var(--text-light);
     border: none;
     display: flex;
     align-items: center;
     justify-content: center;
     cursor: pointer;
     transition: all 0.3s ease;
 }
 
 [data-theme="light"] .quantity-detail-btn {
     background-color: rgba(0, 0, 0, 0.05);
 }
 
 .quantity-detail-btn:hover {
     background-color: var(--primary-color);
     color: white;
 }
 
 .quantity-detail-value {
     width: 40px;
     height: 40px;
     display: flex;
     align-items: center;
     justify-content: center;
     font-weight: 600;
 }
 
 .add-to-cart-btn {
     flex: 1;
     padding: 0 20px;
     border-radius: var(--border-radius-sm);
     background-color: var(--primary-color);
     color: white;
     border: none;
     font-weight: 600;
     cursor: pointer;
     transition: all 0.3s ease;
     display: flex;
     align-items: center;
     justify-content: center;
     gap: 10px;
 }
 
 .add-to-cart-btn:hover {
     background-color: var(--primary-dark);
 }
 
 /* Toast Notification */
 .toast {
     position: fixed;
     bottom: 20px;
     right: 20px;
     background-color: var(--background-card);
     color: var(--text-light);
     padding: 15px 20px;
     border-radius: var(--border-radius-md);
     box-shadow: var(--shadow-md);
     display: flex;
     align-items: center;
     gap: 10px;
     z-index: 2000;
     opacity: 0;
     transform: translateY(20px);
     transition: all 0.3s ease;
 }
 
 .toast.active {
     opacity: 1;
     transform: translateY(0);
 }
 
 .toast-success {
     border-left: 4px solid var(--primary-color);
 }
 
 .toast-error {
     border-left: 4px solid var(--error-color);
 }
 
 .toast-icon {
     font-size: 20px;
 }
 
 .toast-success .toast-icon {
     color: var(--primary-color);
 }
 
 .toast-error .toast-icon {
     color: var(--error-color);
 }
 
 .toast-message {
     flex: 1;
 }
 
 .toast-close {
     background: none;
     border: none;
     color: var(--text-muted);
     cursor: pointer;
     transition: all 0.3s ease;
 }
 
 .toast-close:hover {
     color: var(--text-light);
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
            <a href="#" class="nav-link">Home</a>
            <a href="#" class="nav-link">Shop</a>
            <a href="#" class="nav-link">Categories</a>
            <a href="#" class="nav-link">About</a>
            <a href="#" class="nav-link">Contact</a>
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
                <div class="user-avatar">JD</div>
                <span class="user-name">John Doe</span>
                <i class="fas fa-chevron-down"></i>
                <div class="user-dropdown">
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-user"></i>
                        <span>Profile</span>
                    </a>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-shopping-bag"></i>
                        <span>Orders</span>
                    </a>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-heart"></i>
                        <span>Wishlist</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                    <a href="../logout.php" class="dropdown-item">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>

    <!-- Dashboard Container -->
    <div class="dashboard-container">
        <!-- Category Section -->
        <div class="category-section">
            <div class="section-header">
                <i class="fas fa-list"></i>
                <span>Categories</span>
            </div>
            <ul class="category-list">
                <li class="category-item active">
                    <i class="fas fa-shoe-prints"></i>
                    <span>All Shoes</span>
                </li>
                <li class="category-item">
                    <i class="fas fa-running"></i>
                    <span>Running</span>
                </li>
                <li class="category-item">
                    <i class="fas fa-basketball-ball"></i>
                    <span>Basketball</span>
                </li>
                <li class="category-item">
                    <i class="fas fa-futbol"></i>
                    <span>Soccer</span>
                </li>
                <li class="category-item">
                    <i class="fas fa-hiking"></i>
                    <span>Hiking</span>
                </li>
                <li class="category-item">
                    <i class="fas fa-tshirt"></i>
                    <span>Casual</span>
                </li>
            </ul>
            
            <!-- Filter Section -->
            <div class="filter-section">
                <div class="filter-header">
                    <i class="fas fa-filter"></i>
                    <span>Filters</span>
                </div>
                
                <!-- Price Range -->
                <div class="price-range-container">
                    <h4>Price Range</h4>
                    <div class="price-inputs">
                        <input type="number" class="price-input" placeholder="Min" min="0">
                        <span>-</span>
                        <input type="number" class="price-input" placeholder="Max" min="0">
                    </div>
                    <div class="range-slider">
                        <div class="slider-track"></div>
                        <div class="slider-range"></div>
                        <div class="range-input">
                            <input type="range" class="range-min" min="0" max="1000" value="0" step="10">
                            <input type="range" class="range-max" min="0" max="1000" value="1000" step="10">
                        </div>
                    </div>
                    <button class="apply-price-btn">Apply</button>
                </div>
                
                <!-- Brands -->
                <div class="brands-container">
                    <h4>Brands</h4>
                    <div class="brands-list">
                        <div class="brand-item">
                            <input type="checkbox" id="brand1" class="brand-checkbox">
                            <label for="brand1" class="brand-label">Nike</label>
                        </div>
                        <div class="brand-item">
                            <input type="checkbox" id="brand2" class="brand-checkbox">
                            <label for="brand2" class="brand-label">Adidas</label>
                        </div>
                        <div class="brand-item">
                            <input type="checkbox" id="brand3" class="brand-checkbox">
                            <label for="brand3" class="brand-label">Puma</label>
                        </div>
                        <div class="brand-item">
                            <input type="checkbox" id="brand4" class="brand-checkbox">
                            <label for="brand4" class="brand-label">New Balance</label>
                        </div>
                        <div class="brand-item">
                            <input type="checkbox" id="brand5" class="brand-checkbox">
                            <label for="brand5" class="brand-label">Reebok</label>
                        </div>
                    </div>
                </div>
                
                <!-- Sizes -->
                <div class="sizes-container">
                    <h4>Sizes</h4>
                    <div class="sizes-grid">
                        <div class="size-item">
                            <input type="checkbox" id="size7" class="size-checkbox">
                            <label for="size7" class="size-label">7</label>
                        </div>
                        <div class="size-item">
                            <input type="checkbox" id="size8" class="size-checkbox">
                            <label for="size8" class="size-label">8</label>
                        </div>
                        <div class="size-item">
                            <input type="checkbox" id="size9" class="size-checkbox">
                            <label for="size9" class="size-label">9</label>
                        </div>
                        <div class="size-item">
                            <input type="checkbox" id="size10" class="size-checkbox">
                            <label for="size10" class="size-label">10</label>
                        </div>
                        <div class="size-item">
                            <input type="checkbox" id="size11" class="size-checkbox">
                            <label for="size11" class="size-label">11</label>
                        </div>
                        <div class="size-item">
                            <input type="checkbox" id="size12" class="size-checkbox">
                            <label for="size12" class="size-label">12</label>
                        </div>
                        <div class="size-item">
                            <input type="checkbox" id="size13" class="size-checkbox">
                            <label for="size13" class="size-label">13</label>
                        </div>
                        <div class="size-item">
                            <input type="checkbox" id="size14" class="size-checkbox">
                            <label for="size14" class="size-label">14</label>
                        </div>
                    </div>
                </div>
                
                <!-- Ratings -->
                <div class="ratings-container">
                    <h4>Ratings</h4>
                    <div class="ratings-list">
                        <div class="rating-item">
                            <div class="rating-stars">
                                <i class="fas fa-star rating-star"></i>
                                <i class="fas fa-star rating-star"></i>
                                <i class="fas fa-star rating-star"></i>
                                <i class="fas fa-star rating-star"></i>
                                <i class="fas fa-star rating-star"></i>
                            </div>
                            <span class="rating-count">(124)</span>
                        </div>
                        <div class="rating-item">
                            <div class="rating-stars">
                                <i class="fas fa-star rating-star"></i>
                                <i class="fas fa-star rating-star"></i>
                                <i class="fas fa-star rating-star"></i>
                                <i class="fas fa-star rating-star"></i>
                                <i class="far fa-star rating-star"></i>
                            </div>
                            <span class="rating-count">(86)</span>
                        </div>
                        <div class="rating-item">
                            <div class="rating-stars">
                                <i class="fas fa-star rating-star"></i>
                                <i class="fas fa-star rating-star"></i>
                                <i class="fas fa-star rating-star"></i>
                                <i class="far fa-star rating-star"></i>
                                <i class="far fa-star rating-star"></i>
                            </div>
                            <span class="rating-count">(42)</span>
                        </div>
                        <div class="rating-item">
                            <div class="rating-stars">
                                <i class="fas fa-star rating-star"></i>
                                <i class="fas fa-star rating-star"></i>
                                <i class="far fa-star rating-star"></i>
                                <i class="far fa-star rating-star"></i>
                                <i class="far fa-star rating-star"></i>
                            </div>
                            <span class="rating-count">(15)</span>
                        </div>
                        <div class="rating-item">
                            <div class="rating-stars">
                                <i class="fas fa-star rating-star"></i>
                                <i class="far fa-star rating-star"></i>
                                <i class="far fa-star rating-star"></i>
                                <i class="far fa-star rating-star"></i>
                                <i class="far fa-star rating-star"></i>
                            </div>
                            <span class="rating-count">(3)</span>
                        </div>
                    </div>
                </div>
                
                <!-- Reset Filters Button -->
                <button class="reset-filters">
                    <i class="fas fa-undo"></i>
                    <span>Reset Filters</span>
                </button>
            </div>
        </div>

        <!-- Products Section -->
        <div class="products-section">
            <div class="section-header">
                <i class="fas fa-shoe-prints"></i>
                <span>All Shoes</span>
            </div>
            <div class="search-bar">
                <input type="text" class="search-input" placeholder="Search for shoes...">
                <button class="search-btn">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <div class="products-grid">
                <!-- Product cards will be dynamically generated -->
            </div>
            <div class="pagination">
                <div class="page-item">
                    <i class="fas fa-chevron-left"></i>
                </div>
                <div class="page-item active">1</div>
                <div class="page-item">2</div>
                <div class="page-item">3</div>
                <div class="page-item">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>
        </div>

        <!-- Cart Section -->
        <div class="cart-section">
            <div class="section-header">
                <i class="fas fa-shopping-cart"></i>
                <span>Your Cart</span>
            </div>
            <div class="cart-items">
                <!-- Cart items will be dynamically generated -->
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <p>Your cart is empty</p>
                    <p>Add some products to your cart</p>
                </div>
            </div>
            <div class="cart-summary">
                <div class="cart-row">
                    <span>Subtotal</span>
                    <span>$0.00</span>
                </div>
                <div class="cart-row">
                    <span>Shipping</span>
                    <span>$0.00</span>
                </div>
                <div class="cart-row">
                    <span>Tax</span>
                    <span>$0.00</span>
                </div>
                <div class="cart-row">
                    <span class="cart-total">Total</span>
                    <span class="cart-total">$0.00</span>
                </div>
                <button class="checkout-btn">
                    <i class="fas fa-lock"></i>
                    <span>Checkout</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Product Detail Modal -->
    <div class="modal" id="productModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Product Details</h3>
                <button class="modal-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <!-- Product details will be dynamically generated -->
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast toast-success" id="toast">
        <i class="fas fa-check-circle toast-icon"></i>
        <div class="toast-message">Item added to cart successfully!</div>
        <button class="toast-close">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <script>
        // Sample product data
        const products = [
            {
                id: 1,
                name: "Nike Air Max 270",
                price: 150,
                image: "https://static.nike.com/a/images/c_limit,w_592,f_auto/t_product_v1/i1-665455a5-45de-40fb-945f-c1852b82400d/air-max-270-mens-shoes-KkLcGR.png",
                category: "Running",
                brand: "Nike",
                sizes: [7, 8, 9, 10, 11],
                rating: 4.5,
                description: "The Nike Air Max 270 delivers a plush ride with a super-soft, lightweight foam midsole and a Max Air unit in the heel for responsive cushioning."
            },       
// Sample product data (continuation)
{
    id: 2,
    name: "Adidas Ultraboost 21",
    price: 180,
    image: "https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/fbaf991a78bc4896a3e9ad7800abcec6_9366/Ultraboost_22_Shoes_Black_GZ0127_01_standard.jpg",
    category: "Running",
    brand: "Adidas",
    sizes: [8, 9, 10, 11, 12],
    rating: 4.8,
    description: "The Adidas Ultraboost 21 features a Boost midsole for incredible energy return and a Primeknit+ upper that adapts to the shape of your foot for a comfortable fit."
},
{
    id: 3,
    name: "Puma RS-X³",
    price: 110,
    image: "https://images.puma.com/image/upload/f_auto,q_auto,b_rgb:fafafa,w_600,h_600/global/373308/04/sv01/fnd/PNA/fmt/png/RS-X%C2%B3-Puzzle-Men's-Sneakers",
    category: "Casual",
    brand: "Puma",
    sizes: [7, 8, 9, 10],
    rating: 4.2,
    description: "The Puma RS-X³ features bold design elements and a chunky silhouette with RS technology for cushioning and support."
},
{
    id: 4,
    name: "New Balance 990v5",
    price: 175,
    image: "https://nb.scene7.com/is/image/NB/m990gl5_nb_02_i?$pdpflexf2$&wid=440&hei=440",
    category: "Running",
    brand: "New Balance",
    sizes: [8, 9, 10, 11, 12, 13],
    rating: 4.7,
    description: "The New Balance 990v5 combines premium materials with exceptional cushioning and stability for a comfortable running experience."
},
{
    id: 6,
    name: "Adidas Predator Freak",
    price: 230,
    image: "https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/0e540bae86f24661b855ac8a00f45d3e_9366/Predator_Freak.1_Firm_Ground_Cleats_Black_FY1026_01_standard.jpg",
    category: "Soccer",
    brand: "Adidas",
    sizes: [7, 8, 9, 10, 11],
    rating: 4.9,
    description: "The Adidas Predator Freak features Demonskin rubber spikes for enhanced ball control and a comfortable fit for maximum performance on the field."
},
{
    id: 7,
    name: "Salomon X Ultra 3",
    price: 150,
    image: "https://www.salomon.com/sites/default/files/products-images/L40467400_8db8f8d5d4.jpg",
    category: "Hiking",
    brand: "Salomon",
    sizes: [7, 8, 9, 10, 11, 12],
    rating: 4.5,
    description: "The Salomon X Ultra 3 is a lightweight hiking shoe with excellent traction and support for challenging terrain."
},
{
    id: 8,
    name: "Reebok Classic Leather",
    price: 80,
    image: "https://assets.reebok.com/images/h_840,f_auto,q_auto:sensitive,fl_lossy,c_fill,g_auto/e21e6ce4dc7c43c9a0bfac6800a9c0e6_9366/Classic_Leather_Shoes_White_49799_01_standard.jpg",
    category: "Casual",
    brand: "Reebok",
    sizes: [7, 8, 9, 10, 11, 12],
    rating: 4.3,
    description: "The Reebok Classic Leather features a soft leather upper and a die-cut EVA midsole for lightweight cushioning and comfort."
}
];

// DOM Elements
const productsGrid = document.querySelector('.products-grid');
const cartItems = document.querySelector('.cart-items');
const cartSummary = document.querySelector('.cart-summary');
const categoryItems = document.querySelectorAll('.category-item');
const searchInput = document.querySelector('.search-input');
const searchBtn = document.querySelector('.search-btn');
const modal = document.getElementById('productModal');
const modalClose = document.querySelector('.modal-close');
const toast = document.getElementById('toast');
const toastClose = document.querySelector('.toast-close');
const themeToggle = document.querySelector('.theme-toggle');
const userProfile = document.querySelector('.user-profile');
const userDropdown = document.querySelector('.user-dropdown');
const hamburger = document.querySelector('.hamburger');
const navLinks = document.querySelector('.nav-links');
const navButtons = document.querySelector('.nav-buttons');
const resetFiltersBtn = document.querySelector('.reset-filters');
const priceInputs = document.querySelectorAll('.price-input');
const rangeInputs = document.querySelectorAll('.range-input input');
const rangeMin = document.querySelector('.range-min');
const rangeMax = document.querySelector('.range-max');
const sliderTrack = document.querySelector('.slider-track');
const sliderRange = document.querySelector('.slider-range');
const applyPriceBtn = document.querySelector('.apply-price-btn');
const brandCheckboxes = document.querySelectorAll('.brand-checkbox');
const sizeCheckboxes = document.querySelectorAll('.size-checkbox');
const ratingItems = document.querySelectorAll('.rating-item');

// Cart data
let cart = [];
let currentCategory = 'All Shoes';
let filteredProducts = [...products];
let priceRange = { min: 0, max: 1000 };
let selectedBrands = [];
let selectedSizes = [];
let selectedRating = 0;

// Initialize the application
function init() {
    renderProducts(products);
    updateCartUI();
    setupEventListeners();
    updateSlider();
}

// Setup event listeners
function setupEventListeners() {
    // Category selection
    categoryItems.forEach(item => {
        item.addEventListener('click', () => {
            categoryItems.forEach(cat => cat.classList.remove('active'));
            item.classList.add('active');
            currentCategory = item.querySelector('span').textContent;
            applyFilters();
        });
    });

    // Search functionality
    searchBtn.addEventListener('click', () => {
        applyFilters();
    });

    searchInput.addEventListener('keyup', (e) => {
        if (e.key === 'Enter') {
            applyFilters();
        }
    });

    // Modal close
    modalClose.addEventListener('click', () => {
        modal.classList.remove('active');
    });

    // Toast close
    toastClose.addEventListener('click', () => {
        toast.classList.remove('active');
    });

    // Theme toggle
    themeToggle.addEventListener('click', () => {
        document.body.dataset.theme = document.body.dataset.theme === 'light' ? 'dark' : 'light';
    });

    // User dropdown
    userProfile.addEventListener('click', () => {
        userDropdown.classList.toggle('active');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!userProfile.contains(e.target)) {
            userDropdown.classList.remove('active');
        }
    });

    // Mobile menu
    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('active');
        navLinks.classList.toggle('active');
        navButtons.classList.toggle('active');
    });

    // Price range slider
    rangeInputs.forEach(input => {
        input.addEventListener('input', (e) => {
            updateSlider();
        });
    });

    // Apply price filter
    applyPriceBtn.addEventListener('click', () => {
        priceRange.min = parseInt(rangeMin.value);
        priceRange.max = parseInt(rangeMax.value);
        applyFilters();
    });

    // Brand filters
    brandCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            updateSelectedBrands();
        });
    });

    // Size filters
    sizeCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            updateSelectedSizes();
        });
    });

    // Rating filters
    ratingItems.forEach((item, index) => {
        item.addEventListener('click', () => {
            selectedRating = 5 - index;
            applyFilters();
        });
    });

    // Reset filters
    resetFiltersBtn.addEventListener('click', resetFilters);

    // Window scroll for navbar effect
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            document.querySelector('.navbar').classList.add('scrolled');
        } else {
            document.querySelector('.navbar').classList.remove('scrolled');
        }
    });
}

// Update the price range slider
function updateSlider() {
    let minVal = parseInt(rangeMin.value);
    let maxVal = parseInt(rangeMax.value);
    
    if (maxVal - minVal < 50) {
        if (minVal === parseInt(rangeMin.min)) {
            maxVal = minVal + 50;
            rangeMax.value = maxVal;
        } else {
            minVal = maxVal - 50;
            rangeMin.value = minVal;
        }
    }
    
    const percent1 = ((minVal - parseInt(rangeMin.min)) / (parseInt(rangeMin.max) - parseInt(rangeMin.min))) * 100;
    const percent2 = ((maxVal - parseInt(rangeMin.min)) / (parseInt(rangeMin.max) - parseInt(rangeMin.min))) * 100;
    
    sliderRange.style.left = percent1 + '%';
    sliderRange.style.width = (percent2 - percent1) + '%';
    
    priceInputs[0].value = minVal;
    priceInputs[1].value = maxVal;
}

// Update selected brands array
function updateSelectedBrands() {
    selectedBrands = [];
    brandCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
            selectedBrands.push(checkbox.nextElementSibling.textContent);
        }
    });
    applyFilters();
}

// Update selected sizes array
function updateSelectedSizes() {
    selectedSizes = [];
    sizeCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
            selectedSizes.push(parseInt(checkbox.nextElementSibling.textContent));
        }
    });
    applyFilters();
}

// Reset all filters
function resetFilters() {
    // Reset category
    categoryItems.forEach(cat => cat.classList.remove('active'));
    categoryItems[0].classList.add('active');
    currentCategory = 'All Shoes';
    
    // Reset search
    searchInput.value = '';
    
    // Reset price range
    rangeMin.value = rangeMin.min;
    rangeMax.value = rangeMax.max;
    updateSlider();
    priceRange = { min: 0, max: 1000 };
    
    // Reset brands
    brandCheckboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    selectedBrands = [];
    
    // Reset sizes
    sizeCheckboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    selectedSizes = [];
    
    // Reset rating
    selectedRating = 0;
    
    // Apply reset filters
    renderProducts(products);
}

// Apply all filters
function applyFilters() {
    let filtered = [...products];
    
    // Filter by category
    if (currentCategory !== 'All Shoes') {
        filtered = filtered.filter(product => product.category === currentCategory);
    }
    
    // Filter by search
    const searchTerm = searchInput.value.toLowerCase().trim();
    if (searchTerm) {
        filtered = filtered.filter(product => 
            product.name.toLowerCase().includes(searchTerm) || 
            product.brand.toLowerCase().includes(searchTerm) ||
            product.category.toLowerCase().includes(searchTerm)
        );
    }
    
    // Filter by price range
    filtered = filtered.filter(product => 
        product.price >= priceRange.min && product.price <= priceRange.max
    );
    
    // Filter by brands
    if (selectedBrands.length > 0) {
        filtered = filtered.filter(product => 
            selectedBrands.includes(product.brand)
        );
    }
    
    // Filter by sizes
    if (selectedSizes.length > 0) {
        filtered = filtered.filter(product => 
            product.sizes.some(size => selectedSizes.includes(size))
        );
    }
    
    // Filter by rating
    if (selectedRating > 0) {
        filtered = filtered.filter(product => 
            Math.floor(product.rating) >= selectedRating
        );
    }
    
    filteredProducts = filtered;
    renderProducts(filtered);
}

// Render products to the grid
function renderProducts(productsToRender) {
    productsGrid.innerHTML = '';
    
    if (productsToRender.length === 0) {
        productsGrid.innerHTML = `
            <div class="empty-products" style="grid-column: 1 / -1; text-align: center; padding: 50px 0;">
                <i class="fas fa-search" style="font-size: 48px; color: var(--text-muted); margin-bottom: 20px;"></i>
                <p>No products found matching your criteria.</p>
                <p>Try adjusting your filters or search terms.</p>
            </div>
        `;
        return;
    }
    
    productsToRender.forEach(product => {
        const productCard = document.createElement('div');
        productCard.className = 'product-card';
        productCard.innerHTML = `
            <img src="${product.image}" alt="${product.name}" class="product-image">
            <div class="product-info">
                <h3 class="product-name">${product.name}</h3>
                <div class="product-price">$${product.price.toFixed(2)}</div>
                <button class="product-btn" data-id="${product.id}">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Add to Cart</span>
                </button>
            </div>
        `;
        
        // Add event listener to view product details
        productCard.querySelector('.product-image').addEventListener('click', () => {
            openProductModal(product);
        });
        
        // Add event listener to add to cart
        productCard.querySelector('.product-btn').addEventListener('click', (e) => {
            e.stopPropagation();
            addToCart(product);
            showToast('Item added to cart successfully!', 'success');
        });
        
        productsGrid.appendChild(productCard);
    });
}

// Open product modal with details
function openProductModal(product) {
    const modalBody = modal.querySelector('.modal-body');
    modalBody.innerHTML = `
        <div class="product-gallery">
            <img src="${product.image}" alt="${product.name}" class="product-main-image">
            <div class="product-thumbnails">
                <img src="${product.image}" alt="${product.name}" class="product-thumbnail active">
                <img src="${product.image}" alt="${product.name}" class="product-thumbnail">
                <img src="${product.image}" alt="${product.name}" class="product-thumbnail">
            </div>
        </div>
        <div class="product-details">
            <h2 class="product-detail-name">${product.name}</h2>
            <div class="product-detail-price">$${product.price.toFixed(2)}</div>
            <div class="rating-stars">
                ${generateRatingStars(product.rating)}
                <span style="margin-left: 10px; color: var(--text-muted);">(${product.rating.toFixed(1)})</span>
            </div>
            <p class="product-detail-description">${product.description}</p>
            <div>
                <h4 style="margin-top: 20px; margin-bottom: 10px;">Available Sizes</h4>
                <div class="product-detail-sizes">
                    ${product.sizes.map(size => `
                        <div class="product-detail-size" data-size="${size}">${size}</div>
                    `).join('')}
                </div>
            </div>
            <div class="product-detail-actions">
                <div class="product-detail-quantity">
                    <button class="quantity-detail-btn decrease">
                        <i class="fas fa-minus"></i>
                    </button>
                    <span class="quantity-detail-value">1</span>
                    <button class="quantity-detail-btn increase">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <button class="add-to-cart-btn" data-id="${product.id}">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Add to Cart</span>
                </button>
            </div>
        </div>
    `;
    
    // Add event listeners for the modal
    const sizeElements = modalBody.querySelectorAll('.product-detail-size');
    sizeElements.forEach(element => {
        element.addEventListener('click', () => {
            sizeElements.forEach(el => el.classList.remove('active'));
            element.classList.add('active');
        });
    });
    
    const quantityDecrease = modalBody.querySelector('.decrease');
    const quantityIncrease = modalBody.querySelector('.increase');
    const quantityValue = modalBody.querySelector('.quantity-detail-value');
    let quantity = 1;
    
    quantityDecrease.addEventListener('click', () => {
        if (quantity > 1) {
            quantity--;
            quantityValue.textContent = quantity;
        }
    });
    
    quantityIncrease.addEventListener('click', () => {
        quantity++;
        quantityValue.textContent = quantity;
    });
    
    const addToCartBtn = modalBody.querySelector('.add-to-cart-btn');
    addToCartBtn.addEventListener('click', () => {
        const selectedSize = modalBody.querySelector('.product-detail-size.active');
        if (!selectedSize) {
            showToast('Please select a size', 'error');
            return;
        }
        
        const size = parseInt(selectedSize.dataset.size);
        addToCart(product, quantity, size);
        showToast('Item added to cart successfully!', 'success');
        modal.classList.remove('active');
    });
    
    // Show the modal
    modal.classList.add('active');
}

// Generate rating stars HTML
function generateRatingStars(rating) {
    let starsHtml = '';
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 >= 0.5;
    
    for (let i = 1; i <= 5; i++) {
        if (i <= fullStars) {
            starsHtml += '<i class="fas fa-star rating-star"></i>';
        } else if (i === fullStars + 1 && hasHalfStar) {
            starsHtml += '<i class="fas fa-star-half-alt rating-star"></i>';
        } else {
            starsHtml += '<i class="far fa-star rating-star"></i>';
        }
    }
    
    return starsHtml;
}

// Add product to cart
function addToCart(product, quantity = 1, size = null) {
    const existingItem = cart.find(item => 
        item.id === product.id && (size ? item.size === size : true)
    );
    
    if (existingItem) {
        existingItem.quantity += quantity;
    } else {
        cart.push({
            id: product.id,
            name: product.name,
            price: product.price,
            image: product.image,
            quantity: quantity,
            size: size
        });
    }
    
    updateCartUI();
}

// Remove item from cart
function removeFromCart(index) {
    cart.splice(index, 1);
    updateCartUI();
    showToast('Item removed from cart', 'error');
}

// Update cart quantity
function updateCartQuantity(index, change) {
    const item = cart[index];
    const newQuantity = item.quantity + change;
    
    if (newQuantity < 1) {
        removeFromCart(index);
    } else {
        item.quantity = newQuantity;
        updateCartUI();
    }
}

// Update cart UI
function updateCartUI() {
    if (cart.length === 0) {
        cartItems.innerHTML = `
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <p>Your cart is empty</p>
                <p>Add some products to your cart</p>
            </div>
        `;
        updateCartSummary(0, 0, 0, 0);
        return;
    }
    
    cartItems.innerHTML = '';
    let subtotal = 0;
    
    cart.forEach((item, index) => {
        const itemTotal = item.price * item.quantity;
        subtotal += itemTotal;
        
        const cartItem = document.createElement('div');
        cartItem.className = 'cart-item';
        cartItem.innerHTML = `
            <img src="${item.image}" alt="${item.name}" class="cart-item-image">
            <div class="cart-item-details">
                <div class="cart-item-name">${item.name}</div>
                <div class="cart-item-price">$${item.price.toFixed(2)}</div>
                ${item.size ? `<div style="font-size: 12px; color: var(--text-muted);">Size: ${item.size}</div>` : ''}
            </div>
            <div class="cart-item-quantity">
                <button class="quantity-btn decrease" data-index="${index}">
                    <i class="fas fa-minus"></i>
                </button>
                <span class="quantity-value">${item.quantity}</span>
                <button class="quantity-btn increase" data-index="${index}">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <button class="cart-item-remove" data-index="${index}">
                <i class="fas fa-trash"></i>
            </button>
        `;
        
        cartItems.appendChild(cartItem);
    });
    
    // Add event listeners to cart items
    const decreaseButtons = cartItems.querySelectorAll('.decrease');
    const increaseButtons = cartItems.querySelectorAll('.increase');
    const removeButtons = cartItems.querySelectorAll('.cart-item-remove');
    
    decreaseButtons.forEach(button => {
        button.addEventListener('click', () => {
            const index = parseInt(button.dataset.index);
            updateCartQuantity(index, -1);
        });
    });
    
    increaseButtons.forEach(button => {
        button.addEventListener('click', () => {
            const index = parseInt(button.dataset.index);
            updateCartQuantity(index, 1);
        });
    });
    
    removeButtons.forEach(button => {
        button.addEventListener('click', () => {
            const index = parseInt(button.dataset.index);
            removeFromCart(index);
        });
    });
    
    // Update cart summary
    const shipping = subtotal > 0 ? 10 : 0;
    const tax = subtotal * 0.1;
    const total = subtotal + shipping + tax;
    
    updateCartSummary(subtotal, shipping, tax, total);
}

// Update cart summary values
function updateCartSummary(subtotal, shipping, tax, total) {
    const summaryRows = cartSummary.querySelectorAll('.cart-row');
    
    summaryRows[0].querySelector('span:last-child').textContent = `$${subtotal.toFixed(2)}`;
    summaryRows[1].querySelector('span:last-child').textContent = `$${shipping.toFixed(2)}`;
    summaryRows[2].querySelector('span:last-child').textContent = `$${tax.toFixed(2)}`;
    summaryRows[3].querySelector('span:last-child').textContent = `$${total.toFixed(2)}`;
    
    // Update cart badge if it exists
    const cartBadge = document.querySelector('.cart-badge');
    if (cartBadge) {
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        cartBadge.dataset.count = totalItems;
        cartBadge.style.display = totalItems > 0 ? 'inline-flex' : 'none';
    }
}

// Show toast notification
function showToast(message, type = 'success') {
    const toastElement = document.getElementById('toast');
    const toastMessage = toastElement.querySelector('.toast-message');
    const toastIcon = toastElement.querySelector('.toast-icon');
    
    toastElement.className = `toast toast-${type}`;
    toastMessage.textContent = message;
    
    if (type === 'success') {
        toastIcon.className = 'fas fa-check-circle toast-icon';
    } else {
        toastIcon.className = 'fas fa-exclamation-circle toast-icon';
    }
    
    toastElement.classList.add('active');
    
    // Auto hide after 3 seconds
    setTimeout(() => {
        toastElement.classList.remove('active');
    }, 3000);
}

// Initialize the app when DOM is loaded
document.addEventListener('DOMContentLoaded', init);
</script>
</body>
</html>
