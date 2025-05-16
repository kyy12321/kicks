<?php
session_start();
include '../includes/auth.php';
include '../includes/db_connect.php';

if (!isLoggedIn() || $_SESSION['role'] !== 'cashier') {
    header("Location: ../auth/login.php");
    exit();
}

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
    <link rel="stylesheet" href="customerdash.css">
    <style>

        
        /* Styles for barcode input */
        .barcode-input-container {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            width: 100%;
            position: relative;
        }
        
        #barcodeInput {
            flex: 1;
            padding: 8px 12px 8px 36px;
            border: 2px solid #3498db;
            border-radius: 4px;
            font-size: 16px;
        }
        
        #barcodeInput:focus {
            outline: none;
            border-color: #2980b9;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.3);
        }
        
        .barcode-icon {
            position: absolute;
            left: 10px;
            color: #3498db;
        }
        
        #barcode-status {
            margin-top: 5px;
            font-size: 14px;
            color: #7f8c8d;
        }
        
        .barcode-status-success {
            color: #2ecc71 !important;
        }
        
        .barcode-status-error {
            color: #e74c3c !important;
        }
    </style>
</head>
<body>
    <!-- FontAwesome for icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    
    <div class="header">
        <div class="brand-logo">
            <i class="fas fa-shoe-prints"></i> <!-- Shoe icon -->
            Kicks
        </div>
        <div class="search-bar">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Search">
        </div>
        <div class="logout">
            <a href="../auth/logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <div class="container">
        <!-- Categories Section -->
        <div class="categories" id="categoriesSection">
            <button class="category-btn active" data-category="all">All Products</button>
            <?php foreach ($categories as $category): ?>
                <button class="category-btn" data-category="<?php echo htmlspecialchars($category); ?>">
                    <?php echo htmlspecialchars($category); ?>
                </button>
            <?php endforeach; ?>
        </div>
        
        <!-- Products Section -->
        <div class="products">
            <div class="products-grid" id="productsGrid">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="product-card" data-id="<?php echo $product['id']; ?>" data-category="<?php echo htmlspecialchars($product['category']); ?>" data-price="<?php echo $product['price']; ?>" data-name="<?php echo htmlspecialchars($product['name']); ?>" data-barcode="<?php echo htmlspecialchars($product['barcode']); ?>" <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>>
                            <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <div class="product-info">
                                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p class="product-category"><?php echo htmlspecialchars($product['category']); ?></p>
                                <div class="product-price-stock">
                                    <p class="product-price">$<?php echo number_format($product['price'], 2); ?></p>
                                    <p class="product-stock <?php echo $product['stock'] <= 0 ? 'out-of-stock' : ''; ?>">
                                        <?php echo $product['stock'] > 0 ? 'In Stock: ' . $product['stock'] : 'Out of Stock'; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-products">No products available.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Bill Section -->
        <div class="bill">
            <h2>Current Bill</h2>
            <div class="bill-items" id="billItems">
                <!-- Bill items will be added here dynamically -->
            </div>
            <div class="bill-summary">
                <div class="bill-total">Total: $<span id="billTotal">0.00</span></div>
                <button id="checkoutBtn" class="checkout-btn">Checkout</button>
            </div>
            
            <!-- Barcode input field -->
            <div class="barcode-input-container">
                <i class="fas fa-barcode barcode-icon"></i>
                <input type="text" id="barcodeInput" placeholder="Scan barcode..." autofocus>
            </div>
            <div id="barcode-status">Ready to scan</div>
        </div>
    </div>

    <div class="footer">@2025 BigByte Cartel</div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        // Global state for bill items
        let billItems = {};
        let lastBarcodeScan = '';
        let barcodeBuffer = '';
        let barcodeInputTimeout = null;
        
        // DOM Elements
        document.addEventListener('DOMContentLoaded', () => {
    // Initialize event listeners
    initializeEventListeners();
    
    // Set the first category as active
    const firstCategory = document.querySelector('.category-btn');
    if (firstCategory) {
        firstCategory.classList.add('active');
    }
    
    // Add FontAwesome icons to buttons
    addIconsToButtons();
    
    // Initialize barcode scanner
    initBarcodeScanner();
    
    // Auto-focus the barcode input field
    document.getElementById('barcodeInput').focus();
});

function initializeEventListeners() {
    // Category filtering
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all category buttons
            document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Filter products
            const categoryId = this.dataset.category;
            filterProducts(categoryId);
        });
    });
    
    // Product card clicks
    document.querySelectorAll('.product-card:not([disabled])').forEach(card => {
        card.addEventListener('click', function() {
            const { id, name, price } = this.dataset;
            addToBill(id, name, parseFloat(price));
            
            // Add visual feedback
            this.classList.add('pulse');
            setTimeout(() => this.classList.remove('pulse'), 300);
        });
    });
    
    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        searchProducts(searchTerm);
    });
    
    // Checkout button
    document.getElementById('checkoutBtn').addEventListener('click', handleCheckout);
}

// Initialize Barcode Scanner Input
function initBarcodeScanner() {
    const barcodeInput = document.getElementById('barcodeInput');
    const barcodeStatus = document.getElementById('barcode-status');
    
    // Process barcode input
    barcodeInput.addEventListener('keydown', function(event) {
        // Process when Enter key is pressed (most barcode scanners send Enter at the end)
        if (event.key === 'Enter') {
            event.preventDefault();
            
            const barcode = this.value.trim();
            if (barcode) {
                processBarcode(barcode);
                this.value = ''; // Clear input after processing
            }
        }
    });
    
    // Handle window focus to ensure barcode input is always ready
    window.addEventListener('focus', function() {
        setTimeout(() => barcodeInput.focus(), 100);
    });
    
    // Make sure barcode input is always focused when clicking on it
    barcodeInput.addEventListener('click', function() {
        this.focus();
    });
    
    // Always refocus after a short delay
    setInterval(() => {
        if (document.activeElement !== barcodeInput) {
            barcodeInput.focus();
        }
    }, 2000);
}

// Process scanned barcode
function processBarcode(barcode) {
    const barcodeStatus = document.getElementById('barcode-status');
    
    // Avoid duplicate scans
    if (barcode === lastBarcodeScan && Date.now() - lastBarcodeScanTime < 1000) {
        return;
    }
    
    lastBarcodeScan = barcode;
    lastBarcodeScanTime = Date.now();
    
    // Check if any product barcode matches the scanned barcode
    const productCards = document.querySelectorAll('.product-card');
    let found = false;
    
    productCards.forEach(card => {
        const productBarcode = card.dataset.barcode;
        
        if (productBarcode === barcode) {
            const { id, name, price } = card.dataset;
            addToBill(id, name, parseFloat(price));
            found = true;
            
            // Show success status
            barcodeStatus.textContent = `Found: ${name}`;
            barcodeStatus.className = 'barcode-status-success';
            
            // Play beep sound for successful scan
            playBeepSound();
            
            // Highlight the product card briefly
            card.classList.add('pulse');
            setTimeout(() => card.classList.remove('pulse'), 500);
            
            // Reset status after delay
            setTimeout(() => {
                barcodeStatus.textContent = 'Ready to scan';
                barcodeStatus.className = '';
            }, 2000);
        }
    });
    
    if (!found) {
        barcodeStatus.textContent = `Product with barcode ${barcode} not found`;
        barcodeStatus.className = 'barcode-status-error';
        
        // Play error sound
        playErrorSound();
        
        // Show notification
        showNotification(`Product with barcode ${barcode} not found`, 'warning');
        
        // Reset status after delay
        setTimeout(() => {
            barcodeStatus.textContent = 'Ready to scan';
            barcodeStatus.className = '';
        }, 2000);
    }
}

// Play beep sound for successful scan
function playBeepSound() {
    const beep = new Audio("data:audio/wav;base64,UklGRl9vT19XQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YU...");
    beep.volume = 0.3;
    beep.play().catch(e => console.log("Audio play failed:", e));
}

// Play error sound for failed scan
function playErrorSound() {
    const error = new Audio("data:audio/wav;base64,UklGRl9vT19XQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YT...");
    error.volume = 0.3;
    error.play().catch(e => console.log("Audio play failed:", e));
}

// Add FontAwesome icons to buttons
function addIconsToButtons() {
    // Add icon to checkout button
    const checkoutBtn = document.getElementById('checkoutBtn');
    checkoutBtn.innerHTML = '<i class="fas fa-cash-register"></i> Checkout';
    
    // Add icons to category buttons
    document.querySelectorAll('.category-btn').forEach(btn => {
        if (btn.dataset.category === 'all') {
            btn.innerHTML = '<i class="fas fa-th-large"></i> ' + btn.textContent;
        } else {
            btn.innerHTML = '<i class="fas fa-tag"></i> ' + btn.textContent;
        }
    });
}

// Filter products by category
function filterProducts(categoryId) {
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach(card => {
        if (categoryId === 'all' || card.dataset.category === categoryId) {
            card.style.display = '';
            // Add a subtle animation
            card.style.animation = 'fadeIn 0.3s ease-out forwards';
        } else {
            card.style.display = 'none';
        }
    });
}

// Search products
function searchProducts(searchTerm) {
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach(card => {
        const productName = card.dataset.name.toLowerCase();
        const categoryName = card.querySelector('.product-category').textContent.toLowerCase();
        
        if (productName.includes(searchTerm) || categoryName.includes(searchTerm)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
    
    // Reset category active state if searching
    if (searchTerm) {
        document.querySelectorAll('.category-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelector('.category-btn[data-category="all"]').classList.add('active');
    }
}

// Add item to bill
function addToBill(productId, productName, productPrice) {
    // Check if item already exists in bill
    if (billItems[productId]) {
        billItems[productId].quantity++;
    } else {
        billItems[productId] = {
            name: productName,
            price: productPrice,
            quantity: 1
        };
    }
    
    renderBillItems();
    updateTotal();
    
    // Show notification
    showNotification(`Added ${productName} to bill`, 'success');
}

// Render bill items
function renderBillItems() {
    const billItemsContainer = document.getElementById('billItems');
    
    billItemsContainer.innerHTML = Object.entries(billItems).map(([id, item]) => `
        <div class="bill-item" data-id="${id}">
            <div class="bill-item-info">
                <div class="bill-item-name">${item.name}</div>
                <div class="bill-item-price">$${item.price.toFixed(2)} each</div>
            </div>
            <div class="bill-item-controls">
                <button class="quantity-btn minus" onclick="updateQuantity('${id}', -1)">-</button>
                <span class="quantity-display">${item.quantity}</span>
                <button class="quantity-btn plus" onclick="updateQuantity('${id}', 1)">+</button>
                <button class="remove-btn" onclick="removeItem('${id}')">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="bill-item-total">
                $${(item.price * item.quantity).toFixed(2)}
            </div>
        </div>
    `).join('');
    
    // Update checkout button state
    const checkoutBtn = document.getElementById('checkoutBtn');
    checkoutBtn.disabled = Object.keys(billItems).length === 0;
    checkoutBtn.style.opacity = Object.keys(billItems).length === 0 ? '0.6' : '1';
}

// Update item quantity
function updateQuantity(id, change) {
    if (billItems[id]) {
        billItems[id].quantity += change;
        
        if (billItems[id].quantity <= 0) {
            removeItem(id);
        } else {
            renderBillItems();
            updateTotal();
        }
    }
}

// Remove item from bill
function removeItem(id) {
    const itemName = billItems[id].name;
    delete billItems[id];
    renderBillItems();
    updateTotal();
    
    // Show notification
    showNotification(`Removed ${itemName} from bill`, 'info');
}

// Update total calculations
function updateTotal() {
    const total = Object.values(billItems).reduce((sum, item) => 
        sum + (item.price * item.quantity), 0);
    
    document.getElementById('billTotal').textContent = total.toFixed(2);
}

// Handle checkout process
function handleCheckout() {
    if (Object.keys(billItems).length === 0) {
        showNotification('Please add items to the bill first', 'error');
        return;
    }
    
    // Generate receipt
    const receipt = generateReceipt();
    
    // Print receipt
    const receiptWindow = window.open('', '', 'width=350,height=600');
    receiptWindow.document.write('<html><head><title>Receipt</title>');
    
    // Add styles to receipt window
    receiptWindow.document.write(`
        <style>
            body {
                font-family: 'Courier New', monospace;
                padding: 20px;
                max-width: 300px;
                margin: 0 auto;
            }
            .receipt-header {
                text-align: center;
                margin-bottom: 20px;
            }
            .receipt-header h2 {
                margin-bottom: 5px;
            }
            .receipt-items {
                margin-bottom: 20px;
            }
            .receipt-item {
                display: flex;
                justify-content: space-between;
                margin-bottom: 5px;
            }
            .receipt-total {
                border-top: 1px dashed #000;
                margin-top: 10px;
                padding-top: 10px;
                font-weight: bold;
                display: flex;
                justify-content: space-between;
            }
            .receipt-footer {
                text-align: center;
                margin-top: 20px;
                font-size: 14px;
            }
            @media print {
                body {
                    width: 100%;
                    max-width: none;
                }
            }
        </style>
    `);
    
    receiptWindow.document.write('</head><body>');
    receiptWindow.document.write(receipt);
    receiptWindow.document.write('</body></html>');
    
    // Print and close
    setTimeout(() => {
        receiptWindow.print();
        receiptWindow.close();
        
        // Clear the bill after successful checkout
        billItems = {};
        renderBillItems();
        updateTotal();
        
        // Show success notification
        showNotification('Checkout completed successfully', 'success');
        
        // Refocus barcode input after checkout
        document.getElementById('barcodeInput').focus();
    }, 500);
}

// Generate receipt HTML
function generateReceipt() {
    const total = Object.values(billItems).reduce((sum, item) => 
        sum + (item.price * item.quantity), 0);
    
    const itemsHTML = Object.entries(billItems).map(([id, item]) => `
        <div class="receipt-item">
            <div>${item.name} x${item.quantity}</div>
            <div>$${(item.price * item.quantity).toFixed(2)}</div>
        </div>
    `).join('');
    
    return `
        <div class="receipt">
            <div class="receipt-header">
                <h2>KICKS</h2>
                <p>${new Date().toLocaleString()}</p>
            </div>
            <div class="receipt-items">
                ${itemsHTML}
            </div>
            <div class="receipt-total">
                <div>Total:</div>
                <div>$${total.toFixed(2)}</div>
            </div>
            <div class="receipt-footer">
                <p>Thank you for shopping at KICKS!</p>
                <p>Visit us again soon.</p>
            </div>
        </div>
    `;
}

// Show notification
function showNotification(message, type = 'info') {
    // Check if notification container exists, if not create it
    let container = document.getElementById('notification-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'notification-container';
        container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        `;
        document.body.appendChild(container);
    }
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.style.cssText = `
        padding: 12px 20px;
        margin-bottom: 10px;
        border-radius: 8px;
        background-color: ${getNotificationColor(type)};
        color: white;
        font-size: 14px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        gap: 10px;
        transform: translateX(100%);
        opacity: 0;
        transition: all 0.3s ease;
    `;
    
    // Add icon based on type
    const icon = document.createElement('i');
    icon.className = getNotificationIcon(type);
    notification.appendChild(icon);
    
    // Add message
    const messageSpan = document.createElement('span');
    messageSpan.textContent = message;
    notification.appendChild(messageSpan);
    
    // Add to container
    container.appendChild(notification);
    
    // Trigger animation
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
        notification.style.opacity = '1';
    }, 10);
    
    // Remove after delay
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        notification.style.opacity = '0';
        setTimeout(() => {
            container.removeChild(notification);
        }, 300);
    }, 3000);
}

// Get notification background color based on type
function getNotificationColor(type) {
    switch (type) {
        case 'success': return '#4CAF50';
        case 'error': return '#f44336';
        case 'info': return '#2196F3';
        case 'warning': return '#ff9800';
        default: return '#2196F3';
    }
}

// Get notification icon based on type
function getNotificationIcon(type) {
    switch (type) {
        case 'success': return 'fas fa-check-circle';
        case 'error': return 'fas fa-exclamation-circle';
        case 'info': return 'fas fa-info-circle';
        case 'warning': return 'fas fa-exclamation-triangle';
        default: return 'fas fa-info-circle';
    }
}
    </script>
</body>
</html>
