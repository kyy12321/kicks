
<?php
session_start();
include 'includes/auth.php';
include 'includes/db_connect.php';

// Get cart count
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

// Fetch featured products
$featuredProductsQuery = "SELECT id, name, category, price, description, image_path, discount_type, discount_value 
                         FROM products 
                         WHERE featured = 1 
                         ORDER BY created_at DESC 
                         LIMIT 4";
$featuredProductsResult = $conn->query($featuredProductsQuery);

// Function to calculate final price after discount (returns value in PHP)
function calculateFinalPrice($price, $discountType, $discountValue) {
    if ($discountType === 'percentage') {
        return $price * (1 - ($discountValue / 100));
    } elseif ($discountType === 'fixed') {
        return max(0, $price - $discountValue);
    }
    return $price;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KICKS | Premium Footwear Collection</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="index.css">
    <style>
        .notification-container {
            position: fixed;
            top: 100px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .notification {
            background: var(--bg-secondary, #1a1c1e);
            color: var(--text-light, #ffffff);
            padding: 16px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 300px;
            max-width: 400px;
            animation: slideIn 0.3s ease-out;
            position: relative;
            overflow: hidden;
        }

        .notification.success {
            border-left: 4px solid #4CAF50;
        }

        .notification.error {
            border-left: 4px solid #f44336;
        }

        .notification.info {
            border-left: 4px solid #2196F3;
        }

        .notification-icon {
            font-size: 20px;
            flex-shrink: 0;
        }

        .notification.success .notification-icon {
            color: #4CAF50;
        }

        .notification.error .notification-icon {
            color: #f44336;
        }

        .notification-content {
            flex: 1;
        }

        .notification-title {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .notification-message {
            font-size: 14px;
            opacity: 0.9;
        }

        .notification-close {
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            padding: 4px;
            opacity: 0.7;
            transition: opacity 0.2s;
        }

        .notification-close:hover {
            opacity: 1;
        }

        .notification-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: rgba(255, 255, 255, 0.3);
            animation: progress 3s linear;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        @keyframes progress {
            from {
                width: 100%;
            }
            to {
                width: 0%;
            }
        }

        .product-action-btn.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .product-action-btn.loading i {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<body>
    <!-- Notification Container -->
    <div class="notification-container"></div>
    <!-- Navigation Bar - PRESERVED AS REQUESTED -->
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
            <a href="#featured-products" class="nav-link">Products</a>
            <a href="zpages/about_us.php" class="nav-link">About</a>
            <a href="#" class="nav-link">Contact</a>
        </div>
        <div class="nav-buttons">
 <?php if(isset($_SESSION['user_id'])): ?>
    <div class="user-dropdown">
        <?php
        // Get username from session or database if not set
        if(empty($_SESSION['username'])) {
            // Fetch username from database
            $userQuery = $conn->prepare("SELECT username FROM users WHERE id = ?");
            $userQuery->bind_param("i", $_SESSION['user_id']);
            $userQuery->execute();
            $userResult = $userQuery->get_result();
            
            if($userResult->num_rows > 0) {
                $userData = $userResult->fetch_assoc();
                $_SESSION['username'] = $userData['username'];
            }
        }
        ?>
        <button class="nav-btn btn-signin">
            <i class="fas fa-user"></i>
            <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?>
        </button>
        <div class="dropdown-content">
            <a href="profile.php"><i class="fas fa-cog"></i> Profile</a>
            <a href="auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
<?php else: ?>
    <a href="auth/login.php" class="nav-btn btn-signin">
        <i class="fas fa-user"></i>
        Sign In
    </a>
<?php endif; ?>
            
            <a href="cart.php" class="nav-btn btn-register">
                <i class="fas fa-shopping-cart"></i>
                Cart (<?= $cart_count ?>)
            </a>
        </div>
        <div class="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div class="mobile-menu">
        <div class="mobile-nav-links">
            <a href="#" class="mobile-nav-link"><i class="fas fa-home"></i> Home</a>
            <a href="ecomm.php" class="mobile-nav-link"><i class="fas fa-store"></i> Shop</a>
            <a href="#featured-products" class="mobile-nav-link"><i class="fas fa-tshirt"></i> Products</a>
            <a href="#" class="mobile-nav-link"><i class="fas fa-info-circle"></i> About</a>
            <a href="#" class="mobile-nav-link"><i class="fas fa-envelope"></i> Contact</a>
        </div>
        <div class="mobile-nav-buttons">
            <a href="./auth/login.php" class="nav-btn btn-signin">
                <i class="fas fa-user"></i>
                Sign In
            </a>
            <a href="cart.html" class="nav-btn btn-register">
                <i class="fas fa-shopping-cart"></i>
                Cart (0)
            </a>
        </div>
    </div>
    <div class="overlay"></div>

    <!-- Enhanced Hero Section -->
    <section class="hero">
        <div class="hero-bg"></div>
        <div class="hero-container">
            <div class="hero-content">
                <h2 class="hero-subtitle">Step Into Style</h2>
                <h1 class="hero-title">Elevate Your Footwear Game</h1>
                <p class="hero-description">Discover premium sneakers and footwear that combine style, comfort, and performance. From limited editions to timeless classics, find your perfect pair at KICKS.</p>
                <div class="hero-buttons">
                    <a href="#" class="btn btn-primary">
                        <i class="fas fa-shopping-bag"></i>
                        Shop Collection
                    </a>
                    <a href="#" class="btn btn-secondary">
                        <i class="fas fa-play"></i>
                        Watch Video
                    </a>
                </div>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <span class="stat-number">500+</span>
                        <span class="stat-label">Premium Products</span>
                    </div>
                    <div class="hero-stat">
                        <span class="stat-number">20k+</span>
                        <span class="stat-label">Happy Customers</span>
                    </div>
                    <div class="hero-stat">
                        <span class="stat-number">15+</span>
                        <span class="stat-label">Brand Partners</span>
                    </div>
                </div>
            </div>
            <div class="hero-image-container">
                <img src="uploads/products/nike1.png" alt="Premium Sneaker" class="hero-image">
            </div>
        </div>
        <div class="scroll-indicator">
            <span class="scroll-text">Scroll Down</span>
            <i class="fas fa-chevron-down scroll-icon"></i>
        </div>
    </section>

    <!-- Enhanced Featured Products -->
<section class="featured-products" id="featured-products">
    <div class="section-title-container">
        <h3 class="section-subtitle">Our Collection</h3>
        <h2 class="section-title">Featured Products</h2>
        <p class="section-description">Explore our handpicked selection of premium footwear, designed for style and comfort.</p>
    </div>
    
    <div class="products-grid">
        <?php 
        // Fetch all available products
        $productsQuery = "SELECT id, name, category, price, description, image_path, discount_type, discount_value 
                         FROM products 
                         ORDER BY created_at DESC";
        $productsResult = $conn->query($productsQuery);
        
        if ($productsResult && $productsResult->num_rows > 0): 
            while ($product = $productsResult->fetch_assoc()): 
                $finalPrice = calculateFinalPrice($product['price'], $product['discount_type'], $product['discount_value']);
                $hasDiscount = $finalPrice < $product['price'];
        ?>
                <div class="product-card">
                    <div class="product-image-container">
                        <img src="<?php echo htmlspecialchars(str_replace('../', '', $product['image_path'])); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>" 
                             class="product-image">
                        <div class="product-overlay"></div>
                        <div class="product-actions">
                            <button class="product-action-btn" onclick="addToWishlist(<?php echo $product['id']; ?>)">
                                <i class="fas fa-heart"></i>
                            </button>
                            <button class="product-action-btn" onclick="showProductQuickView(<?php echo $product['id']; ?>)">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="product-action-btn" onclick="addToCart(<?php echo $product['id']; ?>)">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="product-info">
                        <div class="product-category"><?php echo htmlspecialchars($product['category']); ?></div>
                        <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <div class="product-price">
                            <?php if ($hasDiscount): ?>
                                ₱<?php echo number_format($finalPrice, 2); ?>
                                <span class="price-old">₱<?php echo number_format($product['price'], 2); ?></span>
                            <?php else: ?>
                                ₱<?php echo number_format($product['price'], 2); ?>
                            <?php endif; ?>
                        </div>
                        <button class="product-btn" onclick="addToCart(<?php echo $product['id']; ?>)">
                            <i class="fas fa-shopping-bag"></i>
                            Add to Cart
                        </button>
                    </div>
                </div>
        <?php 
            endwhile; 
        else: 
        ?>
            <div class="no-products">
                <i class="fas fa-shoe-prints"></i>
                <p>No products available at the moment</p>
            </div>
        <?php endif; ?>
    </div>
</section>

    <!-- NEW Features Section -->
    <section class="features-section">
        <div class="features-container">
            <div class="section-title-container">
                <h3 class="section-subtitle">Why Choose Us</h3>
                <h2 class="section-title">The KICKS Advantage</h2>
                <p class="section-description">We're committed to providing the best shopping experience with premium products and exceptional service.</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h3 class="feature-title">Free Shipping</h3>
                    <p class="feature-description">Enjoy free shipping on all orders over ₱100. We deliver to your doorstep with care and precision.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-undo"></i>
                    </div>
                    <h3 class="feature-title">Easy Returns</h3>
                    <p class="feature-description">Not satisfied? Return within 30 days for a full refund or exchange, no questions asked.</p>
                </div>
                
                <div class="feature-card">
    <div class="feature-icon">
        <i class="fas fa-shield-alt"></i>
    </div>
    <h3 class="feature-title">Secure Payment</h3>
    <p class="feature-description">Shop with confidence. Our payment system uses advanced encryption to keep your data safe.</p>
</div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3 class="feature-title">24/7 Support</h3>
                    <p class="feature-description">Our customer support team is here for you anytime, anywhere. Get help when you need it.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- NEW Testimonials Section -->
    <section class="testimonials-section">
        <div class="testimonials-container">
            <div class="section-title-container">
                <h3 class="section-subtitle">Testimonials</h3>
                <h2 class="section-title">What Our Customers Say</h2>
                <p class="section-description">Hear from sneaker lovers who have experienced the KICKS difference.</p>
            </div>
            <div class="testimonials-slider">
                <div class="testimonials-track">
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            <p class="testimonial-text">"Absolutely love my new sneakers! The quality is top-notch and the delivery was super fast. Will definitely shop again."</p>
                            <div class="testimonial-author">
                                <img src="uploads/rekce.jpg" alt="Customer 1" class="author-avatar">
                                <div class="author-info">
                                    <span class="author-name">Rekce Laurito</span>
                                    <span class="author-title">Verified Buyer</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            <p class="testimonial-text">"The customer service was amazing and the shoes fit perfectly. Highly recommend KICKS to all sneakerheads!"</p>
                            <div class="testimonial-author">
                                <img src="uploads/kk.jpg" alt="Customer 2" class="author-avatar">
                                <div class="author-info">
                                    <span class="author-name">Kirk Ice Drake Mondragon</span>
                                    <span class="author-title">Sneaker Enthusiast</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            <p class="testimonial-text">"Great selection, easy returns, and secure checkout. My go-to place for the latest kicks!"</p>
                            <div class="testimonial-author">
                                <img src="uploads/lebron.avif" alt="Customer 3" class="author-avatar">
                                <div class="author-info">
                                    <span class="author-name">Lebron James</span>
                                    <span class="author-title">Athlete</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="testimonial-controls">
                    <button class="testimonial-btn" id="testimonial-prev"><i class="fas fa-chevron-left"></i></button>
                    <button class="testimonial-btn" id="testimonial-next"><i class="fas fa-chevron-right"></i></button>
                </div>
                <div class="testimonial-indicators">
                    <span class="indicator active"></span>
                    <span class="indicator"></span>
                    <span class="indicator"></span>
                </div>
            </div>
        </div>
    </section>

    <!-- NEW Newsletter Section -->
    <section class="newsletter-section">
        <div class="newsletter-container">
            <h2 class="newsletter-title">Join Our Newsletter</h2>
            <p class="newsletter-description">Get exclusive offers, updates on new arrivals, and style inspiration delivered straight to your inbox.</p>
            <form class="newsletter-form">
                <input type="email" class="newsletter-input" placeholder="Enter your email" required>
                <button type="submit" class="newsletter-btn">Subscribe</button>
            </form>
            <div class="newsletter-privacy">
                We respect your privacy. Read our <a href="#">Privacy Policy</a>.
            </div>
        </div>
    </section>

    <!-- NEW Brand Showcase Section -->
    <section class="brands-section">
        <div class="brands-container">
            <div class="section-title-container">
                <h3 class="section-subtitle">Our Partners</h3>
                <h2 class="section-title">Top Brands</h2>
                <p class="section-description">We collaborate with the world's leading footwear brands to bring you the best selection.</p>
            </div>
            <div class="brands-grid">
                <div class="brand-item"><img src="uploads/brands/nb.png" alt="Brand 1" class="brand-logo"></div>
                <div class="brand-item"><img src="uploads/brands/nike.png" alt="Brand 2" class="brand-logo"></div>
                <div class="brand-item"><img src="uploads/brands/adidas.png" alt="Brand 3" class="brand-logo"></div>
                <div class="brand-item"><img src="uploads/brands/ua.png" alt="Brand 4" class="brand-logo"></div>
                <div class="brand-item"><img src="uploads/brands/asics.png" alt="Brand 5" class="brand-logo"></div>
            </div>
        </div>
    </section>

    <!-- NEW Instagram Feed Section -->
    <section class="instagram-section">
        <div class="instagram-container">
            <div class="section-title-container">
                <h3 class="section-subtitle">Follow Us</h3>
                <h2 class="section-title">Instagram Feed</h2>
                <p class="section-description">See how our community styles their KICKS. Tag us <b>@kicks.official</b> for a chance to be featured!</p>
            </div>
            <div class="instagram-grid">
                <div class="instagram-item">
                    <img src="uploads/213.jpg" alt="Instagram 1" class="instagram-img">
                    <div class="instagram-overlay">
                        <i class="fab fa-instagram instagram-icon"></i>
                    </div>
                </div>
                <div class="instagram-item">
                    <img src="uploads/insta2.jpg" alt="Instagram 2" class="instagram-img">
                    <div class="instagram-overlay">
                        <i class="fab fa-instagram instagram-icon"></i>
                    </div>
                </div>
                <div class="instagram-item">
                    <img src="uploads/nier.jpg" alt="Instagram 3" class="instagram-img">
                    <div class="instagram-overlay">
                        <i class="fab fa-instagram instagram-icon"></i>
                    </div>
                </div>
                <div class="instagram-item">
                    <img src="uploads/insta4.jpg" alt="Instagram 4" class="instagram-img">
                    <div class="instagram-overlay">
                        <i class="fab fa-instagram instagram-icon"></i>
                    </div>
                </div>
                <div class="instagram-item">
                    <img src="uploads/insta5.jpg" alt="Instagram 5" class="instagram-img">
                    <div class="instagram-overlay">
                        <i class="fab fa-instagram instagram-icon"></i>
                    </div>
                </div>
                <div class="instagram-item">
                    <img src="uploads/nb.jpg" alt="Instagram 6" class="instagram-img">
                    <div class="instagram-overlay">
                        <i class="fab fa-instagram instagram-icon"></i>
                    </div>
                </div>
            </div>
            <a href="https://instagram.com/kicks.official" class="instagram-username" target="_blank">
                <i class="fab fa-instagram"></i> @kicks.official
            </a>
        </div>
    </section>

    <!-- Enhanced Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-col">
                <div class="footer-logo">
                    <i class="fas fa-shoe-prints shoe-icon"></i>
                    <span class="footer-logo-text">KICKS</span>
                </div>
                <div class="footer-description">
                    Premium sneakers and footwear for every style. Step up your game with KICKS.
                </div>
                <div class="footer-social">
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
            <div class="footer-col">
                <div class="footer-title">Quick Links</div>
                <div class="footer-links">
                    <a href="#" class="footer-link"><i class="fas fa-chevron-right"></i> Home</a>
                    <a href="#" class="footer-link"><i class="fas fa-chevron-right"></i> Shop</a>
                    <a href="#" class="footer-link"><i class="fas fa-chevron-right"></i> Collections</a>
                    <a href="#" class="footer-link"><i class="fas fa-chevron-right"></i> About</a>
                    <a href="#" class="footer-link"><i class="fas fa-chevron-right"></i> Contact</a>
                </div>
            </div>
            <div class="footer-col">
                <div class="footer-title">Customer Service</div>
                <div class="footer-links">
                    <a href="#" class="footer-link"><i class="fas fa-chevron-right"></i> FAQs</a>
                    <a href="#" class="footer-link"><i class="fas fa-chevron-right"></i> Shipping & Returns</a>
                    <a href="#" class="footer-link"><i class="fas fa-chevron-right"></i> Privacy Policy</a>
                    <a href="#" class="footer-link"><i class="fas fa-chevron-right"></i> Terms of Service</a>
                </div>
            </div>
            <div class="footer-col">
                <div class="footer-title">Contact Us</div>
                <div class="contact-item">
                    <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="contact-text">5th District Danao, Cebu<br>Philippines, 6003</div>
                </div>
                <div class="contact-item">
                    <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                    <div class="contact-text">kicksofficial@gmail.com</div>
                </div>
                <div class="contact-item">
                    <div class="contact-icon"><i class="fas fa-phone"></i></div>
                    <div class="contact-text">+63 930 854 9550</div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="copyright">
                &copy; <?php echo date('Y'); ?> KICKS. All rights reserved.
            </div>
            <div class="footer-bottom-links">
                <a href="#" class="footer-bottom-link">Privacy Policy</a>
                <a href="#" class="footer-bottom-link">Terms of Service</a>
            </div>
        </div>
    </footer>

    <div class="back-to-top" id="backToTop">
        <i class="fas fa-chevron-up"></i>
    </div>

    <!-- Scripts -->
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 30) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Hamburger menu
        const hamburger = document.querySelector('.hamburger');
        const mobileMenu = document.querySelector('.mobile-menu');
        const overlay = document.querySelector('.overlay');

        hamburger.addEventListener('click', function() {
            mobileMenu.classList.toggle('active');
            overlay.classList.toggle('active');
        });

        overlay.addEventListener('click', function() {
            mobileMenu.classList.remove('active');
            overlay.classList.remove('active');
        });

        // Product card animation on scroll
        const productCards = document.querySelectorAll('.product-card');
        function showCardsOnScroll() {
            const triggerBottom = window.innerHeight * 0.9;
            productCards.forEach(card => {
                const cardTop = card.getBoundingClientRect().top;
                if (cardTop < triggerBottom) {
                    card.classList.add('visible');
                }
            });
        }
        window.addEventListener('scroll', showCardsOnScroll);
        window.addEventListener('load', showCardsOnScroll);
        
        // Notification system
        class NotificationSystem {
            static show(title, message, type = 'success', duration = 3000) {
                const notification = document.createElement('div');
                notification.className = `notification ${type}`;
                
                const iconMap = {
                    success: 'fa-check-circle',
                    error: 'fa-exclamation-circle',
                    info: 'fa-info-circle'
                };
                
                notification.innerHTML = `
                    <i class="fas ${iconMap[type]} notification-icon"></i>
                    <div class="notification-content">
                        <div class="notification-title">${title}</div>
                        <div class="notification-message">${message}</div>
                    </div>
                    <button class="notification-close" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                    <div class="notification-progress"></div>
                `;
                
                const container = document.querySelector('.notification-container');
                container.appendChild(notification);
                
                // Auto remove after duration
                setTimeout(() => {
                    notification.style.animation = 'slideOut 0.3s ease-out';
                    setTimeout(() => notification.remove(), 300);
                }, duration);
            }
        }

        // Improved add to cart functionality
        function addToCart(productId, buttonElement = null) {
            // Find the button if not provided
            if (!buttonElement) {
                buttonElement = event.target.closest('button');
            }
            
            // Add loading state
            const originalContent = buttonElement.innerHTML;
            buttonElement.classList.add('loading');
            buttonElement.innerHTML = '<i class="fas fa-spinner"></i> Adding...';
            
            fetch('add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'product_id=' + productId
            })
            .then(response => response.json())
            .then(data => {
                // Remove loading state
                buttonElement.classList.remove('loading');
                buttonElement.innerHTML = originalContent;
                
                if (data.success) {
                    // Show success notification
                    NotificationSystem.show(
                        'Added to Cart!',
                        `${data.product_name || 'Product'} has been added to your cart.`,
                        'success'
                    );
                    
                    // Update cart count with animation
                    updateCartCount(data.cart_count);
                    
                    // Add a temporary success state to the button
                    buttonElement.innerHTML = '<i class="fas fa-check"></i> Added!';
                    buttonElement.style.backgroundColor = '#4CAF50';
                    
                    setTimeout(() => {
                        buttonElement.innerHTML = originalContent;
                        buttonElement.style.backgroundColor = '';
                    }, 2000);
                    
                } else {
                    // Show error notification
                    NotificationSystem.show(
                        'Error',
                        data.message || 'Invalid product selection',
                        'error'
                    );
                }
            })
            .catch(error => {
                // Remove loading state
                buttonElement.classList.remove('loading');
                buttonElement.innerHTML = originalContent;
                
                console.error('Error:', error);
                NotificationSystem.show(
                    'Connection Error',
                    'Unable to add product to cart. Please try again.',
                    'error'
                );
            });
        }

        // Update cart count with animation
        function updateCartCount(newCount) {
            const cartButtons = document.querySelectorAll('.btn-register');
            cartButtons.forEach(button => {
                const currentCount = parseInt(button.textContent.match(/\d+/) || 0);
                
                // Animate the count change
                button.style.transform = 'scale(1.2)';
                button.innerHTML = `<i class="fas fa-shopping-cart"></i> Cart (${newCount})`;
                
                setTimeout(() => {
                    button.style.transform = 'scale(1)';
                }, 300);
            });
        }

        // Testimonials slider
        const track = document.querySelector('.testimonials-track');
        const indicators = document.querySelectorAll('.indicator');
        let currentSlide = 0;
        function updateSlider() {
            track.style.transform = `translateX(-${currentSlide * 100}%)`;
            indicators.forEach((ind, idx) => {
                ind.classList.toggle('active', idx === currentSlide);
            });
        }
        document.getElementById('testimonial-prev').addEventListener('click', function() {
            currentSlide = (currentSlide - 1 + indicators.length) % indicators.length;
            updateSlider();
        });
        document.getElementById('testimonial-next').addEventListener('click', function() {
            currentSlide = (currentSlide + 1) % indicators.length;
            updateSlider();
        });
        indicators.forEach((ind, idx) => {
            ind.addEventListener('click', function() {
                currentSlide = idx;
                updateSlider();
            });
        });

        // Newsletter form (demo only)
        document.querySelector('.newsletter-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Thank you for subscribing!');
            this.reset();
        });

        // Quick view functionality
        function showProductQuickView(productId) {
            NotificationSystem.show(
                'Quick View',
                'Loading product details...',
                'info'
            );
            
            // In a real implementation, this would open a modal with product details
            // For now, we'll just show a notification and redirect
            setTimeout(() => {
                window.location.href = `product-detail.php?id=${productId}`;
            }, 1000);
        }

        // Add to wishlist functionality
        function addToWishlist(productId) {
            const button = event.target.closest('button');
            const icon = button.querySelector('i');
            
            // Toggle wishlist state
            if (icon.classList.contains('fas')) {
                icon.classList.remove('fas');
                icon.classList.add('far');
                NotificationSystem.show(
                    'Removed from Wishlist',
                    'Product has been removed from your wishlist.',
                    'info'
                );
            } else {
                icon.classList.remove('far');
                icon.classList.add('fas');
                icon.style.color = '#e74c3c';
                NotificationSystem.show(
                    'Added to Wishlist!',
                    'Product has been saved to your wishlist.',
                    'success'
                );
            }
            
            // In a real implementation, this would make an API call to save the wishlist state
        }

        // Back to top button
        const backToTop = document.getElementById('backToTop');
        window.addEventListener('scroll', function() {
            if (window.scrollY > 400) {
                backToTop.classList.add('visible');
            } else {
                backToTop.classList.remove('visible');
            }
        });
        backToTop.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Scroll indicator
        document.querySelector('.scroll-indicator').addEventListener('click', function() {
            window.scrollTo({ top: document.querySelector('.featured-products').offsetTop - 70, behavior: 'smooth' });
        });
    </script>
</body>
</html>
