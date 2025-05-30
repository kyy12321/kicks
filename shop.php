<?php
session_start();
include 'includes/auth.php';
include 'includes/db_connect.php';

// Cart functionality
if(isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    // Add product to cart session
    if(!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    $_SESSION['cart'][$product_id] = ($_SESSION['cart'][$product_id] ?? 0) + 1;
}

$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Same head content as index.php -->
    <style>
        /* ========== Navbar Styles ========== */
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

/* Mobile Menu Styles */
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

.hamburger span:nth-child(1) { top: 0; }
.hamburger span:nth-child(2) { top: 9px; }
.hamburger span:nth-child(3) { top: 18px; }

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

/* User Dropdown */
.user-dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-content {
    display: none;
    position: absolute;
    right: 0;
    background: var(--background-card);
    min-width: 180px;
    box-shadow: var(--shadow-md);
    z-index: 1001;
    border-radius: var(--border-radius-sm);
    overflow: hidden;
    margin-top: 5px;
}

.user-dropdown:hover .dropdown-content {
    display: block;
}

.dropdown-content a {
    background: rgba(76, 175, 80, 0.1);
    padding-left: 20px;
}

/* Rest of your existing shop.php styles */
.shop-container {
    display: grid;
    grid-template-columns: 250px 1fr 350px;
    gap: 30px;
    padding: 2rem 5%;
    margin-top: 80px;
}
        /* Add shop-specific styles */
        .shop-container {
            display: grid;
            grid-template-columns: 250px 1fr 350px;
            gap: 30px;
            padding: 2rem 5%;
            margin-top: 80px;
        }

        /* Categories Sidebar */
        .categories-sidebar {
            background: var(--background-card);
            padding: 20px;
            border-radius: var(--border-radius-md);
            height: fit-content;
        }

        .filter-section {
            margin-bottom: 2rem;
        }

        .filter-title {
            font-size: 1.2rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }

        .filter-list {
            list-style: none;
        }

        .filter-item {
            margin-bottom: 0.8rem;
        }

        .filter-checkbox {
            accent-color: var(--primary-color);
            margin-right: 0.5rem;
        }

        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .product-card-shop {
            background: var(--background-card);
            border-radius: var(--border-radius-md);
            padding: 15px;
            transition: transform 0.3s ease;
        }

        .product-card-shop:hover {
            transform: translateY(-5px);
        }

        .product-image-shop {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: var(--border-radius-sm);
        }

        /* Cart Sidebar */
        .cart-sidebar {
            background: var(--background-card);
            padding: 20px;
            border-radius: var(--border-radius-md);
            height: fit-content;
            position: sticky;
            top: 100px;
        }

        .cart-items {
            margin: 1rem 0;
        }

        .cart-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            padding: 10px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--border-radius-sm);
        }

        .cart-item-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 1rem;
        }

        .cart-summary {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 1rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .checkout-btn {
            width: 100%;
            padding: 12px;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <!-- Reuse navbar from index.php -->
    <?php include 'navbar.php'; ?>

    <div class="shop-container">
        <!-- Categories Sidebar -->
        <aside class="categories-sidebar">
            <div class="filter-section">
                <h3 class="filter-title">Categories</h3>
                <ul class="filter-list">
                    <li class="filter-item">
                        <input type="checkbox" class="filter-checkbox" id="running">
                        <label for="running">Running Shoes</label>
                    </li>
                    <li class="filter-item">
                        <input type="checkbox" class="filter-checkbox" id="basketball">
                        <label for="basketball">Basketball Shoes</label>
                    </li>
                    <li class="filter-item">
                        <input type="checkbox" class="filter-checkbox" id="lifestyle">
                        <label for="lifestyle">Lifestyle</label>
                    </li>
                    <li class="filter-item">
                        <input type="checkbox" class="filter-checkbox" id="sneakers">
                        <label for="sneakers">Sneakers</label>
                    </li>
                </ul>
            </div>

            <div class="filter-section">
                <h3 class="filter-title">Price Range</h3>
                <input type="range" class="price-range" min="0" max="500" value="500">
                <div class="price-range-values">
                    <span>$0</span>
                    <span>$500</span>
                </div>
            </div>
        </aside>

        <!-- Products Grid -->
        <main class="products-main">
            <div class="products-grid">
                <?php
                // Sample products array - replace with database query
                $products = [
                    ['id' => 1, 'name' => 'AirMax Pro', 'price' => 149.99, 'image' => 'uploads/vomero.png', 'category' => 'running'],
                    ['id' => 2, 'name' => 'Urban Classic', 'price' => 129.99, 'image' => 'uploads/product2.png', 'category' => 'lifestyle'],
                    ['id' => 3, 'name' => 'Court Dominator', 'price' => 159.99, 'image' => 'uploads/product3.png', 'category' => 'basketball'],
                    // Add more products...
                ];

                foreach ($products as $product) : ?>
                <div class="product-card-shop">
                    <img src="<?= $product['image'] ?>" alt="<?= $product['name'] ?>" class="product-image-shop">
                    <h3><?= $product['name'] ?></h3>
                    <p class="product-price">$<?= number_format($product['price'], 2) ?></p>
                    <form method="post">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <button type="submit" name="add_to_cart" class="btn btn-primary">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        </main>

        <!-- Cart Sidebar -->
        <aside class="cart-sidebar">
            <h2>Shopping Cart (<?= $cart_count ?>)</h2>
            <div class="cart-items">
                <?php if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                    <?php foreach($_SESSION['cart'] as $id => $qty): ?>
                    <div class="cart-item">
                        <img src="uploads/product<?= $id ?>.png" class="cart-item-image">
                        <div>
                            <h4>Product <?= $id ?></h4>
                            <p>Qty: <?= $qty ?></p>
                            <p>$<?= number_format($products[$id-1]['price'] * $qty, 2) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Your cart is empty</p>
                <?php endif; ?>
            </div>

            <div class="cart-summary">
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span>$<?= number_format(array_sum(array_map(function($id, $qty) use ($products) {
                        return $products[$id-1]['price'] * $qty;
                    }, array_keys($_SESSION['cart'] ?? []), $_SESSION['cart'] ?? [])), 2) ?></span>
                </div>
                <div class="summary-row">
                    <span>Shipping:</span>
                    <span>$10.00</span>
                </div>
                <div class="summary-row">
                    <span>Total:</span>
                    <span>$<?= number_format((array_sum(array_map(function($id, $qty) use ($products) {
                        return $products[$id-1]['price'] * $qty;
                    }, array_keys($_SESSION['cart'] ?? []), $_SESSION['cart'] ?? [])) ?? 0) + 10, 2) ?></span>
                </div>
                <button class="btn btn-primary checkout-btn">Proceed to Checkout</button>
            </div>
        </aside>
    </div>

    <!-- Reuse footer and scripts from index.php -->
    <?php include 'footer.php'; ?>
</body>
</html>