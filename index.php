<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KICKS | Premium Footwear Collection</title>
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

        :root {
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
            --border-radius-sm: 8px;
            --border-radius-md: 12px;
            --border-radius-lg: 16px;
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.15);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.2);
            --transition-speed: 0.3s;
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

        /* Hero Section */
        .hero {
            background-image: url('uploads/11.png');
            background-repeat: no-repeat;         
            background-position: center center;   
            background-size: cover;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 80px 20px;
            min-height: calc(100vh - 80px);
            position: relative;
        }

        .hero-content {
            max-width: 800px;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 1s ease forwards;
        }

        .hero-title {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero-description {
            font-size: 18px;
            color: var(--text-muted);
            margin-bottom: 40px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .hero-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-bottom: 60px;
        }

        .btn {
            padding: 16px 32px;
            border-radius: var(--border-radius-sm);
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-speed) ease;
            border: none;
            outline: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: white;
            background-color: var(--primary-color);
            box-shadow: 0 4px 10px rgba(76, 175, 80, 0.3);
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }

        .btn::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(76, 175, 80, 0.4);
        }

        .btn:hover::after {
            transform: translateX(0);
        }

        .btn:active {
            transform: translateY(0);
        }

        /* Featured Products */
        .featured-products {
            padding: 80px 5%;
            text-align: center;
        }

        .section-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 50px;
            position: relative;
            display: inline-block;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background-color: var(--primary-color);
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .product-card {
            background-color: var(--background-card);
            border-radius: var(--border-radius-md);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateY(20px);
        }

        .product-card.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-md);
        }

        .product-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .product-card:hover .product-image {
            transform: scale(1.05);
        }

        .product-info {
            padding: 20px;
        }

        .product-name {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .product-price {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-price);
            margin-bottom: 15px;
        }

        .product-btn {
            width: 100%;
            padding: 12px;
            border-radius: var(--border-radius-sm);
            background-color: transparent;
            border: 1px solid var(--primary-color);
            color: var(--text-light);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .product-btn:hover {
            background-color: var(--primary-color);
            color: white;
        }

        /* Footer */
        .footer {
            background-color: var(--background-card);
            padding: 60px 5% 30px;
            margin-top: 80px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-column h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }

        .footer-column h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 2px;
            background-color: var(--primary-color);
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 12px;
        }

        .footer-links a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--primary-color);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-muted);
            font-size: 14px;
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
        @media (max-width: 992px) {
            .hero-title {
                font-size: 40px;
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
                border-top: 1px solid rgba(255, 255, 255, 0.1);
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

            .hero-title {
                font-size: 32px;
            }

            .hero-description {
                font-size: 16px;
            }

            .hero-buttons {
                flex-direction: column;
                gap: 15px;
            }

            .btn {
                width: 100%;
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
    </style>
</head>
<body>
    <!-- Navigation Bar -->
   <nav class="navbar">
    <a href="index.php" class="nav-logo">
        <div class="logo-container">
            <i class="fas fa-shoe-prints shoe-icon"></i>
            <span class="nav-logo-text">
                <span class="letter">K</span>
                <span class="letter">I</span>
                <span class="letter">C</span>
                <span class="letter">K</span>
                <span class="letter">S</span>
            </span>
        </div>
    </a>
    
    <div class="nav-links">
        <a href="index.php" class="nav-link">Home</a>
        <a href="#products" class="nav-link">Products</a>
        <a href="about_us.php" class="nav-link">About Us</a> <!-- Updated link -->
        <a href="#contact" class="nav-link">Contact</a>
    </div>
    
    <div class="nav-buttons">
        <a href="auth/login.php" class="nav-btn btn-signin">
            <i class="fas fa-sign-in-alt"></i> Sign In
        </a>
        <a href="auth/register.php" class="nav-btn btn-register">
            <i class="fas fa-user-plus"></i> Register
        </a>
    </div>
    
    <div class="hamburger">
        <span></span>
        <span></span>
        <span></span>
    </div>
</nav>
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1 class="hero-title">Step Into Premium Footwear</h1>
            <p class="hero-description">Discover the latest sneaker trends and exclusive collections. Join us today and elevate your sneaker game with KICKS.</p>
            <div class="hero-buttons">
                <a href="auth/login.php" class="btn">
                    <i class="fas fa-shopping-bag"></i> Shop Now
                </a>
                <a href="auth/register.php" class="btn" id="register-btn">
                    <i class="fas fa-user-plus"></i> Join Membership
                </a>
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section class="featured-products" id="products">
        <h2 class="section-title">Featured Products</h2>
        <div class="products-grid">
            <div class="product-card">
                <img src="uploads/1000.png" alt="Nike Air Max" class="product-image">
                <div class="product-info">
                    <h3 class="product-name">Nike Air Max 270</h3>
                    <p class="product-price">$150</p>
                    
                  
                </div>
            </div>
            <div class="product-card">
                <img src="uploads/adi.jpg" alt="Adidas Ultraboost" class="product-image">
                <div class="product-info">
                    <h3 class="product-name">Adidas Ultraboost 21</h3>
                    <p class="product-price">$180</p>
                   
                </div>
            </div>
            <div class="product-card">
                <img src="uploads/9060 .png" alt="Jordan 1" class="product-image">
                <div class="product-info">
                    <h3 class="product-name">Air Jordan 1 Retro High</h3>
                    <p class="product-price">$170</p>
                  
                </div>
            </div>
            <div class="product-card">
                <img src="uploads/nbb.jpg" alt="New Balance" class="product-image">
                <div class="product-info">
                    <h3 class="product-name">New Balance 990v5</h3>
                    <p class="product-price">$185</p>
                  
                </div>
            </div>
            <div class="product-card">
                <img src="uploads/sambaOGw.png" alt="New Balance" class="product-image">
                <div class="product-info">
                    <h3 class="product-name">New Balance 990v5</h3>
                    <p class="product-price">$185</p>
                
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-column">
                <h3>KICKS</h3>
                <p style="color: var(--text-muted); margin-bottom: 20px;">Premium footwear for every occasion. Quality, style, and comfort in every step.</p>
            </div>
            <div class="footer-column">
                <h3>Shop</h3>
                <ul class="footer-links">
                    <li><a href="#">Men's Collection</a></li>
                    <li><a href="#">Women's Collection</a></li>
                    <li><a href="#">New Arrivals</a></li>
                    <li><a href="#">Special Offers</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Support</h3>
                <ul class="footer-links">
                    <li><a href="#">FAQs</a></li>
                    <li><a href="#">Shipping & Returns</a></li>
                    <li><a href="#">Size Guide</a></li>
                    <li><a href="#">Contact Us</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Company</h3>
                <ul class="footer-links">
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Careers</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2025 KICKS by BigByte Cartel</p>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const hamburger = document.querySelector('.hamburger');
        const navLinks = document.querySelector('.nav-links');
        const navButtons = document.querySelector('.nav-buttons');

        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            navLinks.classList.toggle('active');
            navButtons.classList.toggle('active');
        });

        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Register button animation
        const registerBtn = document.getElementById('register-btn');
        registerBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Create ripple effect
            const ripple = document.createElement('span');
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.backgroundColor = 'rgba(255, 255, 255, 0.3)';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple 0.6s linear';
            ripple.style.pointerEvents = 'none';
            
            // Position the ripple
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            ripple.style.width = ripple.style.height = `${size}px`;
            ripple.style.left = `${e.clientX - rect.left - size/2}px`;
            ripple.style.top = `${e.clientY - rect.top - size/2}px`;
            
            this.appendChild(ripple);
            
            // Navigate after animation
            setTimeout(() => {
                window.location.href = 'auth/register.php';
            }, 300);
        });

        // Add keyframes for ripple animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);

        // Animate product cards on scroll
        const productCards = document.querySelectorAll('.product-card');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.classList.add('visible');
                    }, index * 100); // Stagger the animations
                }
            });
        }, { threshold: 0.1 });

        productCards.forEach(card => {
            observer.observe(card);
        });
    </script>
</body>
</html></qodoArtifact>
