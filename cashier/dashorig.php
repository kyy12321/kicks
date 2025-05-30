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
    <link rel="stylesheet" href="dash.css">
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
                                <!-- In the product price display (line ~403) -->
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
        <p>&copy; <?php echo date('Y'); ?> Bigb. Est 2025.</p>
    </footer>

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
                this.classList.remove('visible');
                filterProducts();
                productSearch.focus();
            });
            
            // Bill Management
            let billItems = [];
            const billItemsContainer = document.getElementById('billItems');
            const billTotalElement = document.getElementById('billTotal');
            const checkoutBtn = document.getElementById('checkoutBtn');
            
            // Add product to bill
            function addProductToBill(product) {
                // Check if product is already in the bill
                const existingItemIndex = billItems.findIndex(item => item.id === product.id);
                
                if (existingItemIndex !== -1) {
                    // Increment quantity if product already exists
                    billItems[existingItemIndex].quantity += 1;
                } else {
                    // Add new product to bill
                    billItems.push({
                        id: product.id,
                        name: product.name,
                        price: product.price,
                        quantity: 1
                    });
                }
                
                updateBillDisplay();
            }
            
            // Update bill display
            function updateBillDisplay() {
                // Clear current bill items
                billItemsContainer.innerHTML = '';
                
                // Add each item to the bill display
                billItems.forEach((item, index) => {
                    const itemTotal = item.price * item.quantity;
                    
                    const billItemElement = document.createElement('div');
                    billItemElement.className = 'bill-item';
                    billItemElement.innerHTML = `
                        <div class="bill-item-info">
                            <span class="bill-item-name">₱${item.name}</span>
                            <span class="bill-item-price">₱${item.price.toFixed(2)}</span>
                        </div>
                        <div class="bill-item-controls">
                            <button class="quantity-btn decrease-btn" data-index="${index}">-</button>
                            <span class="quantity-display">${item.quantity}</span>
                            <button class="quantity-btn increase-btn" data-index="${index}">+</button>
                            <button class="remove-btn" data-index="${index}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                        <div class="bill-item-total">$${itemTotal.toFixed(2)}</div>
                    `;
                    
                    billItemsContainer.appendChild(billItemElement);
                });
                
                // Calculate subtotal, tax and total
                const taxRate = 0.02; // 10% tax rate
                const subtotal = billItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                const taxAmount = subtotal * taxRate;
                const total = subtotal + taxAmount;
                
                // Update bill summary
                document.getElementById('billSubtotal').textContent = `Subtotal: ₱${subtotal.toFixed(2)}`;
                document.getElementById('billTax').textContent = `Tax (2%): ₱${taxAmount.toFixed(2)}`;
                billTotalElement.textContent = `Total: ₱${total.toFixed(2)}`;
                
                // Enable/disable checkout button
                checkoutBtn.disabled = billItems.length === 0;
                
                // Add event listeners to the new buttons
                addBillItemEventListeners();
            }
            
            // Add event listeners to bill item buttons
            function addBillItemEventListeners() {
                // Decrease quantity buttons
                document.querySelectorAll('.decrease-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const index = parseInt(this.getAttribute('data-index'));
                        if (billItems[index].quantity > 1) {
                            billItems[index].quantity -= 1;
                        } else {
                            billItems.splice(index, 1);
                        }
                        updateBillDisplay();
                    });
                });
                
                // Increase quantity buttons
                document.querySelectorAll('.increase-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const index = parseInt(this.getAttribute('data-index'));
                        billItems[index].quantity += 1;
                        updateBillDisplay();
                    });
                });
                
                // Remove item buttons
                document.querySelectorAll('.remove-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const index = parseInt(this.getAttribute('data-index'));
                        billItems.splice(index, 1);
                        updateBillDisplay();
                    });
                });
            }
            
            // Product click event
            productCards.forEach(card => {
                if (!card.hasAttribute('disabled')) {
                    card.addEventListener('click', function() {
                        const product = {
                            id: this.getAttribute('data-id'),
                            name: this.getAttribute('data-name'),
                            price: parseFloat(this.getAttribute('data-price')),
                            category: this.getAttribute('data-category')
                        };
                        
                        addProductToBill(product);
                    });
                }
            });
            
            // Barcode scanner functionality
            const barcodeInput = document.getElementById('barcodeInput');
            const barcodeStatus = document.getElementById('barcode-status');
            
            barcodeInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const barcode = this.value.trim();
                    
                    if (barcode) {
                        // Find product with matching barcode
                        const productCard = Array.from(productCards).find(card => 
                            card.getAttribute('data-barcode') === barcode
                        );
                        
                        if (productCard && !productCard.hasAttribute('disabled')) {
                            const product = {
                                id: productCard.getAttribute('data-id'),
                                name: productCard.getAttribute('data-name'),
                                price: parseFloat(productCard.getAttribute('data-price')),
                                category: productCard.getAttribute('data-category')
                            };
                            
                            addProductToBill(product);
                            
                            // Update status
                            barcodeStatus.innerHTML = `
                                <i class="fas fa-check-circle barcode-status-success"></i>
                               <span class="barcode-status-success">Product added: ${product.name} (₱${product.price.toFixed(2)})</span>
                            `;
                            
                            // Highlight the product card
                            productCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            productCard.style.animation = 'pulse 0.5s ease';
                            setTimeout(() => {
                                productCard.style.animation = '';
                            }, 500);
                        } else {
                            // Product not found or out of stock
                            barcodeStatus.innerHTML = `
                                <i class="fas fa-exclamation-circle barcode-status-error"></i>
                                <span class="barcode-status-error">Product not found or out of stock</span>
                            `;
                        }
                    }
                    
                    // Clear input
                    this.value = '';
                    
                    // Reset status after 3 seconds
                    setTimeout(() => {
                        barcodeStatus.innerHTML = `
                            <i class="fas fa-info-circle"></i>
                            <span>Ready to scan</span>
                        `;
                    }, 3000);
                }
            });
            
            // Checkout button
            checkoutBtn.addEventListener('click', async function() {
                if (billItems.length === 0) return;

                const taxRate = 0.02; // 10% tax rate
                const subtotal = billItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                const taxAmount = subtotal * taxRate;
                const total = subtotal + taxAmount;

                const saleData = {
                    subtotal: subtotal,
                    tax_amount: taxAmount,
                    total: total,
                    items: billItems.map(item => ({
                        product_id: parseInt(item.id),
                        quantity: item.quantity,
                        price: item.price,
                        tax_rate: taxRate
                    }))
                };

                try {
                    // Show loading state
                    checkoutBtn.disabled = true;
                    checkoutBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                    
                    const response = await fetch('process_checkout.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(saleData)
                    });

                    const result = await response.json();
                    if (result.status === 'success') {
                        // Show success message with receipt number
                        const successMessage = `
                            <div style="text-align:center">
                                <i class="fas fa-check-circle" style="color:var(--primary-color); font-size:48px;"></i>
                                <h3>Sale Completed!</h3>
                                <p>Receipt #: ${result.sale_id}</p>
                                <p>Total: ₱${total.toFixed(2)}</p>
                            </div>
                        `;
                        
                        // Create modal or use alert
                        alert(`Sale completed! Receipt #: ${result.sale_id}\nTotal: ₱${total.toFixed(2)}`);
                        
                        // Clear the bill
                        billItems = [];
                        updateBillDisplay();
                    } else {
                        alert('Checkout failed: ' + result.message);
                    }
                } catch (error) {
                    console.error('Checkout error:', error);
                    alert('Error processing checkout');
                } finally {
                    // Reset button state
                    checkoutBtn.disabled = false;
                    checkoutBtn.innerHTML = '<i class="fas fa-shopping-cart"></i> Checkout';
                }
            });
        });
    </script>
</body>
</html>
</qodoArtifact>
                
