
<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in (you can customize this based on your authentication system)
// if (!isset($_SESSION['user_id'])) {
//     header("Location: ../login.php");
//     exit();
// }

// Sample product data - in a real application, this would come from a database
$products = [
    [
        'id' => 1,
        'name' => "Nike Air Max 270",
        'price' => 150,
        'image' => "https://static.nike.com/a/images/c_limit,w_592,f_auto/t_product_v1/i1-665455a5-45de-40fb-945f-c1852b82400d/air-max-270-mens-shoes-KkLcGR.png",
        'category' => "Running",
        'brand' => "Nike",
        'barcode' => "8901234567890"
    ],
    [
        'id' => 2,
        'name' => "Adidas Ultraboost 21",
        'price' => 180,
        'image' => "https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/fbaf991a78bc4896a3e9ad7800abcec6_9366/Ultraboost_22_Shoes_Black_GZ0127_01_standard.jpg",
        'category' => "Running",
        'brand' => "Adidas",
        'barcode' => "8901234567891"
    ],
    [
        'id' => 3,
        'name' => "Puma RS-X³",
        'price' => 110,
        'image' => "https://images.puma.com/image/upload/f_auto,q_auto,b_rgb:fafafa,w_600,h_600/global/373308/04/sv01/fnd/PNA/fmt/png/RS-X%C2%B3-Puzzle-Men's-Sneakers",
        'category' => "Casual",
        'brand' => "Puma",
        'barcode' => "8901234567892"
    ],
    [
        'id' => 4,
        'name' => "New Balance 990v5",
        'price' => 175,
        'image' => "https://nb.scene7.com/is/image/NB/m990gl5_nb_02_i?$pdpflexf2$&wid=440&hei=440",
        'category' => "Running",
        'brand' => "New Balance",
        'barcode' => "8901234567893"
    ],
    [
        'id' => 6,
        'name' => "Adidas Predator Freak",
        'price' => 230,
        'image' => "https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/0e540bae86f24661b855ac8a00f45d3e_9366/Predator_Freak.1_Firm_Ground_Cleats_Black_FY1026_01_standard.jpg",
        'category' => "Soccer",
        'brand' => "Adidas",
        'barcode' => "8901234567894"
    ],
    [
        'id' => 7,
        'name' => "Salomon X Ultra 3",
        'price' => 150,
        'image' => "https://www.salomon.com/sites/default/files/products-images/L40467400_8db8f8d5d4.jpg",
        'category' => "Hiking",
        'brand' => "Salomon",
        'barcode' => "8901234567895"
    ],
    [
        'id' => 8,
        'name' => "Reebok Classic Leather",
        'price' => 80,
        'image' => "https://assets.reebok.com/images/h_840,f_auto,q_auto:sensitive,fl_lossy,c_fill,g_auto/e21e6ce4dc7c43c9a0bfac6800a9c0e6_9366/Classic_Leather_Shoes_White_49799_01_standard.jpg",
        'category' => "Casual",
        'brand' => "Reebok",
        'barcode' => "8901234567896"
    ]
];

// Get all unique categories
$categories = array_unique(array_column($products, 'category'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KICKS | Cashier POS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../eco.css">
    <link rel="stylesheet" href="pos.css">
</head>
<body>
    <!-- Navigation Bar (Same as ecomm.php) -->
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
            <a href="../ecomm.php" class="nav-link">Store</a>
            <a href="#" class="nav-link active">POS</a>
            <a href="#" class="nav-link">Inventory</a>
            <a href="#" class="nav-link">Reports</a>
            <a href="#" class="nav-link">Settings</a>
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

    <!-- POS Dashboard Container -->
    <div class="pos-container">
        <!-- Left Side - Product Selection -->
        <div class="pos-products-section">
            <div class="pos-header">
                <h2><i class="fas fa-cash-register"></i> Point of Sale</h2>
                <div class="pos-search-container">
                    <input type="text" id="barcodeInput" class="pos-search-input" placeholder="Scan barcode or search products...">
                    <button class="pos-search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            
            <!-- Category Pills -->
            <div class="pos-categories">
                <button class="pos-category-pill active" data-category="all">All Products</button>
                <?php foreach($categories as $category): ?>
                    <button class="pos-category-pill" data-category="<?php echo htmlspecialchars($category); ?>">
                        <?php echo htmlspecialchars($category); ?>
                    </button>
                <?php endforeach; ?>
            </div>
            
            <!-- Products Grid -->
            <div class="pos-products-grid">
                <?php foreach($products as $product): ?>
                <div class="pos-product-card" data-id="<?php echo $product['id']; ?>" data-category="<?php echo htmlspecialchars($product['category']); ?>" data-barcode="<?php echo htmlspecialchars($product['barcode']); ?>">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="pos-product-image">
                    <div class="pos-product-info">
                        <h3 class="pos-product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <div class="pos-product-meta">
                            <span class="pos-product-brand"><?php echo htmlspecialchars($product['brand']); ?></span>
                            <span class="pos-product-price">$<?php echo number_format($product['price'], 2); ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Right Side - Cart & Checkout -->
        <div class="pos-cart-section">
            <div class="pos-cart-header">
                <h2><i class="fas fa-shopping-cart"></i> Current Sale</h2>
                <button id="newSaleBtn" class="pos-new-sale-btn">
                    <i class="fas fa-plus"></i> New Sale
                </button>
            </div>
            
            <div class="pos-cart-customer">
                <div class="pos-customer-select">
                    <i class="fas fa-user"></i>
                    <select id="customerSelect" class="pos-customer-dropdown">
                        <option value="0">Walk-in Customer</option>
                        <option value="1">John Smith</option>
                        <option value="2">Jane Doe</option>
                        <option value="3">Robert Johnson</option>
                    </select>
                    <button class="pos-add-customer-btn">
                        <i class="fas fa-user-plus"></i>
                    </button>
                </div>
            </div>
            
            <div class="pos-cart-items" id="posCartItems">
                <!-- Cart items will be dynamically added here -->
                <div class="pos-empty-cart">
                    <i class="fas fa-shopping-basket"></i>
                    <p>No items in cart</p>
                    <p>Add products by clicking on them</p>
                </div>
            </div>
            
            <div class="pos-cart-summary">
                <div class="pos-summary-row">
                    <span>Subtotal</span>
                    <span id="subtotalAmount">$0.00</span>
                </div>
                <div class="pos-summary-row">
                    <span>Tax (10%)</span>
                    <span id="taxAmount">$0.00</span>
                </div>
                <div class="pos-summary-row">
                    <span>Discount</span>
                    <div class="pos-discount-input">
                        <input type="number" id="discountInput" min="0" max="100" value="0">
                        <select id="discountType">
                            <option value="percentage">%</option>
                            <option value="fixed">$</option>
                        </select>
                    </div>
                </div>
                <div class="pos-summary-row pos-total-row">
                    <span>Total</span>
                    <span id="totalAmount">$0.00</span>
                </div>
            </div>
            
            <div class="pos-payment-options">
                <div class="pos-payment-header">
                    <h3>Payment Method</h3>
                </div>
                <div class="pos-payment-methods">
                    <button class="pos-payment-method active" data-method="cash">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>Cash</span>
                    </button>
                    <button class="pos-payment-method" data-method="card">
                        <i class="fas fa-credit-card"></i>
                        <span>Card</span>
                    </button>
                    <button class="pos-payment-method" data-method="mobile">
                        <i class="fas fa-mobile-alt"></i>
                        <span>Mobile</span>
                    </button>
                </div>
                
                <div class="pos-cash-input" id="cashPaymentSection">
                    <div class="pos-cash-row">
                        <span>Amount Tendered</span>
                        <div class="pos-amount-input">
                            <span class="pos-currency-symbol">$</span>
                            <input type="number" id="amountTendered" min="0" step="0.01" value="0.00">
                        </div>
                    </div>
                    <div class="pos-cash-row">
                        <span>Change Due</span>
                        <span id="changeDue">$0.00</span>
                    </div>
                </div>
                
                <div class="pos-card-input" id="cardPaymentSection" style="display: none;">
                    <div class="pos-card-row">
                        <span>Card Number</span>
                        <input type="text" id="cardNumber" placeholder="XXXX-XXXX-XXXX-XXXX">
                    </div>
                    <div class="pos-card-row pos-card-details">
                        <div>
                            <span>Expiry</span>
                            <input type="text" id="cardExpiry" placeholder="MM/YY">
                        </div>
                        <div>
                            <span>CVV</span>
                            <input type="text" id="cardCvv" placeholder="XXX">
                        </div>
                    </div>
                </div>
                
                <div class="pos-mobile-input" id="mobilePaymentSection" style="display: none;">
                    <div class="pos-qr-code">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=KicksPOSPayment" alt="QR Code">
                    </div>
                    <p>Scan this QR code with your mobile payment app</p>
                </div>
            </div>
            
            <div class="pos-checkout-actions">
                <button id="holdSaleBtn" class="pos-hold-btn">
                    <i class="fas fa-pause"></i> Hold
                </button>
                <button id="cancelSaleBtn" class="pos-cancel-btn">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button id="checkoutBtn" class="pos-checkout-btn">
                    <i class="fas fa-check"></i> Complete Sale
                </button>
            </div>
        </div>
    </div>
    
    <!-- Receipt Modal -->
    <div class="pos-modal" id="receiptModal">
        <div class="pos-modal-content">
            <div class="pos-modal-header">
                <h3>Receipt</h3>
                <button class="pos-modal-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="pos-modal-body">
                <div class="pos-receipt">
                    <div class="pos-receipt-header">
                        <div class="pos-receipt-logo">
                            <i class="fas fa-shoe-prints"></i>
                            <span>KICKS</span>
                        </div>
                        <div class="pos-receipt-info">
                            <p>123 Sneaker Street</p>
                            <p>Footwear City, FC 12345</p>
                            <p>Tel: (123) 456-7890</p>
                        </div>
                    </div>
                    
                    <div class="pos-receipt-details">
                        <div class="pos-receipt-row">
                            <span>Receipt #:</span>
                            <span id="receiptNumber">POS-20230001</span>
                        </div>
                        <div class="pos-receipt-row">
                            <span>Date:</span>
                            <span id="receiptDate">2023-08-15 14:30:45</span>
                        </div>
                        <div class="pos-receipt-row">
                            <span>Cashier:</span>
                            <span>John Doe</span>
                        </div>
                        <div class="pos-receipt-row">
                            <span>Customer:</span>
                            <span id="receiptCustomer">Walk-in Customer</span>
                        </div>
                    </div>
                    
                    <div class="pos-receipt-items">
                        <table>
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody id="receiptItemsList">
                                <!-- Receipt items will be added here -->
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="pos-receipt-summary">
                        <div class="pos-receipt-row">
                            <span>Subtotal:</span>
                            <span id="receiptSubtotal">$0.00</span>
                        </div>
                        <div class="pos-receipt-row">
                            <span>Tax (10%):</span>
                            <span id="receiptTax">$0.00</span>
                        </div>
                        <div class="pos-receipt-row">
                            <span>Discount:</span>
                            <span id="receiptDiscount">$0.00</span>
                        </div>
                        <div class="pos-receipt-row pos-receipt-total">
                            <span>Total:</span>
                            <span id="receiptTotal">$0.00</span>
                        </div>
                        <div class="pos-receipt-row">
                            <span>Payment Method:</span>
                            <span id="receiptPaymentMethod">Cash</span>
                        </div>
                        <div class="pos-receipt-row" id="receiptCashDetails">
                            <span>Amount Tendered:</span>
                            <span id="receiptAmountTendered">$0.00</span>
                        </div>
                        <div class="pos-receipt-row" id="receiptChangeDetails">
                            <span>Change:</span>
                            <span id="receiptChange">$0.00</span>
                        </div>
                    </div>
                    
                    <div class="pos-receipt-footer">
                        <p>Thank you for shopping at KICKS!</p>
                        <p>Please keep this receipt for returns or exchanges.</p>
                        <div class="pos-receipt-barcode">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=KICKS-POS-20230001" alt="Receipt QR Code">
                        </div>
                    </div>
                </div>
            </div>
            <div class="pos-modal-footer">
                <button id="printReceiptBtn" class="pos-print-btn">
                    <i class="fas fa-print"></i> Print Receipt
                </button>
                <button id="emailReceiptBtn" class="pos-email-btn">
                    <i class="fas fa-envelope"></i> Email Receipt
                </button>
                <button id="newSaleAfterReceiptBtn" class="pos-new-sale-btn">
                    <i class="fas fa-plus"></i> New Sale
                </button>
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
        // POS System JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // DOM Elements
            const posProductsGrid = document.querySelector('.pos-products-grid');
            const posCartItems = document.getElementById('posCartItems');
            const categoryPills = document.querySelectorAll('.pos-category-pill');
            const barcodeInput = document.getElementById('barcodeInput');
            const searchBtn = document.querySelector('.pos-search-btn');
            const subtotalAmount = document.getElementById('subtotalAmount');
            const taxAmount = document.getElementById('taxAmount');
            const discountInput = document.getElementById('discountInput');
            const discountType = document.getElementById('discountType');
            const totalAmount = document.getElementById('totalAmount');
            const amountTendered = document.getElementById('amountTendered');
            const changeDue = document.getElementById('changeDue');
            const paymentMethods = document.querySelectorAll('.pos-payment-method');
            const cashPaymentSection = document.getElementById('cashPaymentSection');
            const cardPaymentSection = document.getElementById('cardPaymentSection');
            const mobilePaymentSection = document.getElementById('mobilePaymentSection');
            const checkoutBtn = document.getElementById('checkoutBtn');
            const cancelSaleBtn = document.getElementById('cancelSaleBtn');
            const holdSaleBtn = document.getElementById('holdSaleBtn');
            const newSaleBtn = document.getElementById('newSaleBtn');
            const receiptModal = document.getElementById('receiptModal');
            const modalClose = document.querySelector('.pos-modal-close');
            const printReceiptBtn = document.getElementById('printReceiptBtn');
            const emailReceiptBtn = document.getElementById('emailReceiptBtn');
            const newSaleAfterReceiptBtn = document.getElementById('newSaleAfterReceiptBtn');
            const toast = document.getElementById('toast');
            const toastClose = document.querySelector('.toast-close');
            const customerSelect = document.getElementById('customerSelect');
            
            // Receipt elements
            const receiptNumber = document.getElementById('receiptNumber');
            const receiptDate = document.getElementById('receiptDate');
            const receiptCustomer = document.getElementById('receiptCustomer');
            const receiptItemsList = document.getElementById('receiptItemsList');
            const receiptSubtotal = document.getElementById('receiptSubtotal');
            const receiptTax = document.getElementById('receiptTax');
            const receiptDiscount = document.getElementById('receiptDiscount');
            const receiptTotal = document.getElementById('receiptTotal');
            const receiptPaymentMethod = document.getElementById('receiptPaymentMethod');
            const receiptAmountTendered = document.getElementById('receiptAmountTendered');
            const receiptChange = document.getElementById('receiptChange');
            const receiptCashDetails = document.getElementById('receiptCashDetails');
            const receiptChangeDetails = document.getElementById('receiptChangeDetails');
            
            // Cart data
            let cart = [];
            let currentCategory = 'all';
            let selectedPaymentMethod = 'cash';
            
            // Initialize the POS system
            function init() {
                setupEventListeners();
                updateCartUI();
            }
            
            // Setup event listeners
            function setupEventListeners() {
                // Category selection
                categoryPills.forEach(pill => {
                    pill.addEventListener('click', () => {
                        categoryPills.forEach(p => p.classList.remove('active'));
                        pill.classList.add('active');
                        currentCategory = pill.dataset.category;
                        filterProducts();
                    });
                });
                
                // Product selection
                const productCards = document.querySelectorAll('.pos-product-card');
                productCards.forEach(card => {
                    card.addEventListener('click', () => {
                        const productId = parseInt(card.dataset.id);
                        addToCart(productId);
                    });
                });
                
                // Barcode search
                barcodeInput.addEventListener('keyup', (e) => {
                    if (e.key === 'Enter') {
                        searchByBarcode();
                    }
                });
                
                searchBtn.addEventListener('click', searchByBarcode);
                
                // Discount input
                discountInput.addEventListener('input', updateCartSummary);
                discountType.addEventListener('change', updateCartSummary);
                
                // Amount tendered input
                amountTendered.addEventListener('input', updateChangeDue);
                
                // Payment method selection
                paymentMethods.forEach(method => {
                    method.addEventListener('click', () => {
                        paymentMethods.forEach(m => m.classList.remove('active'));
                        method.classList.add('active');
                        selectedPaymentMethod = method.dataset.method;
                        updatePaymentSection();
                    });
                });
                
                // Checkout button
                checkoutBtn.addEventListener('click', completeSale);
                
                // Cancel sale button
                cancelSaleBtn.addEventListener('click', () => {
                    if (confirm('Are you sure you want to cancel this sale?')) {
                        resetSale();
                    }
                });
                
                // Hold sale button
                holdSaleBtn.addEventListener('click', () => {
                    showToast('Sale has been put on hold', 'info');
                });
                
                // New sale button
                newSaleBtn.addEventListener('click', () => {
                    if (cart.length > 0) {
                        if (confirm('Are you sure you want to start a new sale? Current items will be lost.')) {
                            resetSale();
                        }
                    } else {
                        resetSale();
                    }
                });
                
                // Modal close
                modalClose.addEventListener('click', () => {
                    receiptModal.classList.remove('active');
                });
                
                // Print receipt
                printReceiptBtn.addEventListener('click', () => {
                    window.print();
                });
                
                // Email receipt
                emailReceiptBtn.addEventListener('click', () => {
                    showToast('Receipt has been emailed to the customer', 'success');
                });
                
                // New sale after receipt
                newSaleAfterReceiptBtn.addEventListener('click', () => {
                    receiptModal.classList.remove('active');
                    resetSale();
                });
                
                // Toast close
                toastClose.addEventListener('click', () => {
                    toast.classList.remove('active');
                });
                
                // Customer select
                customerSelect.addEventListener('change', () => {
                    updateCartSummary();
                });
                
                // Theme toggle (from original ecomm.php)
                const themeToggle = document.querySelector('.theme-toggle');
                themeToggle.addEventListener('click', () => {
                    document.body.dataset.theme = document.body.dataset.theme === 'light' ? 'dark' : 'light';
                });
                
                // User dropdown (from original ecomm.php)
                const userProfile = document.querySelector('.user-profile');
                const userDropdown = document.querySelector('.user-dropdown');
                userProfile.addEventListener('click', () => {
                    userDropdown.classList.toggle('active');
                });
                
                // Close dropdown when clicking outside (from original ecomm.php)
                document.addEventListener('click', (e) => {
                    if (!userProfile.contains(e.target)) {
                        userDropdown.classList.remove('active');
                    }
                });
                
                // Mobile menu (from original ecomm.php)
                const hamburger = document.querySelector('.hamburger');
                const navLinks = document.querySelector('.nav-links');
                const navButtons = document.querySelector('.nav-buttons');
                hamburger.addEventListener('click', () => {
                    hamburger.classList.toggle('active');
                    navLinks.classList.toggle('active');
                    navButtons.classList.toggle('active');
                });
            }
            
            // Filter products by category
            function filterProducts() {
                const productCards = document.querySelectorAll('.pos-product-card');
                
                productCards.forEach(card => {
                    if (currentCategory === 'all' || card.dataset.category === currentCategory) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }
            
            // Search by barcode
            function searchByBarcode() {
                const barcode = barcodeInput.value.trim();
                
                if (barcode === '') {
                    return;
                }
                
                const productCards = document.querySelectorAll('.pos-product-card');
                let found = false;
                
                productCards.forEach(card => {
                    if (card.dataset.barcode === barcode) {
                        const productId = parseInt(card.dataset.id);
                        addToCart(productId);
                        found = true;
                        
                        // Highlight the found product
                        card.classList.add('highlight');
                        setTimeout(() => {
                            card.classList.remove('highlight');
                        }, 2000);
                        
                        // Scroll to the product
                        card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                });
                
                if (!found) {
                    showToast('Product not found', 'error');
                }
                
                barcodeInput.value = '';
                barcodeInput.focus();
            }
            
            // Add product to cart
            function addToCart(productId) {
               
                // Find the product in our sample data
                const product = <?php echo json_encode($products); ?>.find(p => p.id === productId);
                
                if (!product) {
                    return;
                }
                
                // Check if product is already in cart
                const existingItem = cart.find(item => item.id === productId);
                
                if (existingItem) {
                    existingItem.quantity += 1;
                    showToast('Item quantity updated', 'success');
                } else {
                    cart.push({
                        id: product.id,
                        name: product.name,
                        price: product.price,
                        image: product.image,
                        brand: product.brand,
                        quantity: 1
                    });
                    showToast('Item added to cart', 'success');
                }
                
                updateCartUI();
            }
            
            // Remove item from cart
            function removeFromCart(productId) {
                cart = cart.filter(item => item.id !== productId);
                updateCartUI();
            }
            
            // Update item quantity in cart
            function updateCartItemQuantity(productId, quantity) {
                const item = cart.find(item => item.id === productId);
                
                if (item) {
                    if (quantity <= 0) {
                        removeFromCart(productId);
                    } else {
                        item.quantity = quantity;
                        updateCartUI();
                    }
                }
            }
            
            // Update cart UI
            function updateCartUI() {
                if (cart.length === 0) {
                    posCartItems.innerHTML = `
                        <div class="pos-empty-cart">
                            <i class="fas fa-shopping-basket"></i>
                            <p>No items in cart</p>
                            <p>Add products by clicking on them</p>
                        </div>
                    `;
                } else {
                    let cartHTML = '';
                    
                    cart.forEach(item => {
                        cartHTML += `
                            <div class="pos-cart-item" data-id="${item.id}">
                                <div class="pos-cart-item-image">
                                    <img src="${item.image}" alt="${item.name}">
                                </div>
                                <div class="pos-cart-item-details">
                                    <h4>${item.name}</h4>
                                    <p>${item.brand}</p>
                                    <span class="pos-cart-item-price">$${item.price.toFixed(2)}</span>
                                </div>
                                <div class="pos-cart-item-quantity">
                                    <button class="pos-quantity-btn pos-quantity-minus" data-id="${item.id}">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" class="pos-quantity-input" value="${item.quantity}" min="1" data-id="${item.id}">
                                    <button class="pos-quantity-btn pos-quantity-plus" data-id="${item.id}">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <div class="pos-cart-item-total">
                                    $${(item.price * item.quantity).toFixed(2)}
                                </div>
                                <button class="pos-cart-item-remove" data-id="${item.id}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        `;
                    });
                    
                    posCartItems.innerHTML = cartHTML;
                    
                    // Add event listeners for quantity buttons and remove buttons
                    const minusButtons = document.querySelectorAll('.pos-quantity-minus');
                    const plusButtons = document.querySelectorAll('.pos-quantity-plus');
                    const quantityInputs = document.querySelectorAll('.pos-quantity-input');
                    const removeButtons = document.querySelectorAll('.pos-cart-item-remove');
                    
                    minusButtons.forEach(button => {
                        button.addEventListener('click', () => {
                            const productId = parseInt(button.dataset.id);
                            const item = cart.find(item => item.id === productId);
                            if (item && item.quantity > 1) {
                                updateCartItemQuantity(productId, item.quantity - 1);
                            } else {
                                removeFromCart(productId);
                            }
                        });
                    });
                    
                    plusButtons.forEach(button => {
                        button.addEventListener('click', () => {
                            const productId = parseInt(button.dataset.id);
                            const item = cart.find(item => item.id === productId);
                            if (item) {
                                updateCartItemQuantity(productId, item.quantity + 1);
                            }
                        });
                    });
                    
                    quantityInputs.forEach(input => {
                        input.addEventListener('change', () => {
                            const productId = parseInt(input.dataset.id);
                            const quantity = parseInt(input.value);
                            updateCartItemQuantity(productId, quantity);
                        });
                    });
                    
                    removeButtons.forEach(button => {
                        button.addEventListener('click', () => {
                            const productId = parseInt(button.dataset.id);
                            removeFromCart(productId);
                        });
                    });
                }
                
                updateCartSummary();
            }
            
            // Update cart summary
            function updateCartSummary() {
                const subtotal = cart.reduce((total, item) => total + (item.price * item.quantity), 0);
                const taxRate = 0.10; // 10% tax
                const tax = subtotal * taxRate;
                
                let discount = 0;
                const discountValue = parseFloat(discountInput.value) || 0;
                
                if (discountType.value === 'percentage') {
                    discount = subtotal * (discountValue / 100);
                } else {
                    discount = discountValue;
                }
                
                const total = subtotal + tax - discount;
                
                subtotalAmount.textContent = `$${subtotal.toFixed(2)}`;
                taxAmount.textContent = `$${tax.toFixed(2)}`;
                totalAmount.textContent = `$${total.toFixed(2)}`;
                
                // Update amount tendered if it's less than the total
                if (parseFloat(amountTendered.value) < total) {
                    amountTendered.value = total.toFixed(2);
                }
                
                updateChangeDue();
            }
            
            // Update change due
            function updateChangeDue() {
                const total = parseFloat(totalAmount.textContent.replace('$', ''));
                const tendered = parseFloat(amountTendered.value) || 0;
                const change = tendered - total;
                
                changeDue.textContent = `$${Math.max(0, change).toFixed(2)}`;
                
                // Enable/disable checkout button based on payment
                if (selectedPaymentMethod === 'cash') {
                    checkoutBtn.disabled = tendered < total;
                } else {
                    checkoutBtn.disabled = false;
                }
            }
            
            // Update payment section based on selected method
            function updatePaymentSection() {
                cashPaymentSection.style.display = 'none';
                cardPaymentSection.style.display = 'none';
                mobilePaymentSection.style.display = 'none';
                
                if (selectedPaymentMethod === 'cash') {
                    cashPaymentSection.style.display = 'block';
                } else if (selectedPaymentMethod === 'card') {
                    cardPaymentSection.style.display = 'block';
                } else if (selectedPaymentMethod === 'mobile') {
                    mobilePaymentSection.style.display = 'block';
                }
                
                updateChangeDue();
            }
            
            // Complete sale
            function completeSale() {
                if (cart.length === 0) {
                    showToast('No items in cart', 'error');
                    return;
                }
                
                // For cash payment, check if enough money was tendered
                if (selectedPaymentMethod === 'cash') {
                    const total = parseFloat(totalAmount.textContent.replace('$', ''));
                    const tendered = parseFloat(amountTendered.value) || 0;
                    
                    if (tendered < total) {
                        showToast('Insufficient amount tendered', 'error');
                        return;
                    }
                }
                
                // Generate receipt
                generateReceipt();
                
                // Show receipt modal
                receiptModal.classList.add('active');
            }
            
            // Generate receipt
            function generateReceipt() {
                // Generate receipt number
                const receiptNum = 'POS-' + new Date().getFullYear() + 
                                  String(new Date().getMonth() + 1).padStart(2, '0') + 
                                  String(new Date().getDate()).padStart(2, '0') + '-' + 
                                  Math.floor(Math.random() * 10000).toString().padStart(4, '0');
                
                // Format date
                const now = new Date();
                const formattedDate = now.getFullYear() + '-' + 
                                     String(now.getMonth() + 1).padStart(2, '0') + '-' + 
                                     String(now.getDate()).padStart(2, '0') + ' ' + 
                                     String(now.getHours()).padStart(2, '0') + ':' + 
                                     String(now.getMinutes()).padStart(2, '0') + ':' + 
                                     String(now.getSeconds()).padStart(2, '0');
                
                // Get customer name
                const customerName = customerSelect.options[customerSelect.selectedIndex].text;
                
                // Set receipt header info
                receiptNumber.textContent = receiptNum;
                receiptDate.textContent = formattedDate;
                receiptCustomer.textContent = customerName;
                
                // Generate receipt items
                let receiptItemsHTML = '';
                cart.forEach(item => {
                    receiptItemsHTML += `
                        <tr>
                            <td>${item.name}</td>
                            <td>${item.quantity}</td>
                            <td>$${item.price.toFixed(2)}</td>
                            <td>$${(item.price * item.quantity).toFixed(2)}</td>
                        </tr>
                    `;
                });
                receiptItemsList.innerHTML = receiptItemsHTML;
                
                // Set receipt summary
                const subtotal = parseFloat(subtotalAmount.textContent.replace('$', ''));
                const tax = parseFloat(taxAmount.textContent.replace('$', ''));
                const total = parseFloat(totalAmount.textContent.replace('$', ''));
                
                let discount = 0;
                const discountValue = parseFloat(discountInput.value) || 0;
                
                if (discountType.value === 'percentage') {
                    discount = subtotal * (discountValue / 100);
                } else {
                    discount = discountValue;
                }
                
                receiptSubtotal.textContent = `$${subtotal.toFixed(2)}`;
                receiptTax.textContent = `$${tax.toFixed(2)}`;
                receiptDiscount.textContent = `$${discount.toFixed(2)}`;
                receiptTotal.textContent = `$${total.toFixed(2)}`;
                
                // Set payment method
                receiptPaymentMethod.textContent = selectedPaymentMethod.charAt(0).toUpperCase() + selectedPaymentMethod.slice(1);
                
                // Show/hide cash details
                if (selectedPaymentMethod === 'cash') {
                    receiptCashDetails.style.display = 'flex';
                    receiptChangeDetails.style.display = 'flex';
                    receiptAmountTendered.textContent = `$${parseFloat(amountTendered.value).toFixed(2)}`;
                    receiptChange.textContent = changeDue.textContent;
                } else {
                    receiptCashDetails.style.display = 'none';
                    receiptChangeDetails.style.display = 'none';
                }
            }
            
            // Reset sale
            function resetSale() {
                cart = [];
                updateCartUI();
                
                // Reset form fields
                discountInput.value = 0;
                discountType.value = 'percentage';
                amountTendered.value = '0.00';
                
                // Reset payment method
                paymentMethods.forEach(method => method.classList.remove('active'));
                paymentMethods[0].classList.add('active');
                selectedPaymentMethod = 'cash';
                updatePaymentSection();
                
                // Reset customer
                customerSelect.value = 0;
                
                // Focus on barcode input
                barcodeInput.focus();
            }
            
            // Show toast notification
            function showToast(message, type = 'success') {
                const toastElement = document.getElementById('toast');
                const toastMessage = toastElement.querySelector('.toast-message');
                const toastIcon = toastElement.querySelector('.toast-icon');
                
                // Set message
                toastMessage.textContent = message;
                
                // Set type
                toastElement.className = `toast toast-${type}`;
                
                // Set icon
                if (type === 'success') {
                    toastIcon.className = 'fas fa-check-circle toast-icon';
                } else if (type === 'error') {
                    toastIcon.className = 'fas fa-exclamation-circle toast-icon';
                } else if (type === 'info') {
                    toastIcon.className = 'fas fa-info-circle toast-icon';
                } else if (type === 'warning') {
                    toastIcon.className = 'fas fa-exclamation-triangle toast-icon';
                }
                
                // Show toast
                toastElement.classList.add('active');
                
                // Auto hide after 3 seconds
                setTimeout(() => {
                    toastElement.classList.remove('active');
                }, 3000);
            }
            
            // Initialize the POS system
            init();
        });
    </script>
</body>
</html>
</qodoArtifact>

