<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Learn about KICKS Premium Footwear - our story, team, and mission to provide sustainable, high-quality footwear.">
    <title>About Us | KICKS Premium Footwear</title>
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
     gap: 10px;
     text-decoration: none;
 }

 .nav-logo-text {
     font-size: 28px;
     font-weight: 700;
     background: linear-gradient(90deg, #4CAF50, #3e8e41);
     -webkit-background-clip: text;
     -webkit-text-fill-color: transparent;
     font-family: 'Poppins', sans-serif;
 }

 .nav-logo i {
     font-size: 24px;
     color: var(--primary-color);
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

 /* Hero Section - About Page */
 .hero-about {
     position: relative;
     height: 400px;
     display: flex;
     align-items: center;
     justify-content: center;
     text-align: center;
     overflow: hidden;
 }

 .hero-bg {
     position: absolute;
     top: 0;
     left: 0;
     width: 100%;
     height: 100%;
     background-image: linear-gradient(rgba(17, 19, 21, 0.7), rgba(17, 19, 21, 0.8)), url('/api/placeholder/1200/600');
     background-size: cover;
     background-position: center;
     z-index: -1;
 }

 .hero-text {
     position: relative;
     z-index: 1;
     max-width: 800px;
     padding: 0 20px;
 }

 .hero-title {
     font-size: 72px;
     font-weight: 700;
     margin-bottom: 20px;
     text-transform: uppercase;
     opacity: 0.3;
     letter-spacing: 5px;
 }

 .section-title {
     font-size: 32px;
     font-weight: 700;
     margin-bottom: 20px;
     position: relative;
     display: inline-block;
 }

 .section-title::after {
     content: '';
     position: absolute;
     bottom: -10px;
     left: 0;
     width: 60px;
     height: 3px;
     background-color: var(--primary-color);
 }

 /* About Section */
 .about-section {
     padding: 80px 5%;
 }

 .about-content {
     display: grid;
     grid-template-columns: 1fr 1fr;
     gap: 50px;
     align-items: center;
     margin-top: 40px;
 }

 .about-image {
     border-radius: var(--border-radius-lg);
     overflow: hidden;
     box-shadow: var(--shadow-md);
 }

 .about-image img {
     width: 100%;
     height: auto;
     display: block;
     transition: transform 0.5s ease;
 }

 .about-image img:hover {
     transform: scale(1.03);
 }

 .about-text p {
     margin-bottom: 20px;
     color: var(--text-muted);
     font-size: 16px;
 }

 .stats-container {
     display: grid;
     grid-template-columns: repeat(3, 1fr);
     gap: 20px;
     margin-top: 30px;
 }

 .stat-item {
     padding: 20px;
     background-color: var(--background-card);
     border-radius: var(--border-radius-md);
     text-align: center;
     box-shadow: var(--shadow-sm);
     transition: transform 0.3s ease, box-shadow 0.3s ease;
 }

 .stat-item:hover {
     transform: translateY(-5px);
     box-shadow: var(--shadow-md);
 }

 .stat-value {
     font-size: 42px;
     font-weight: 700;
     color: var(--primary-color);
     margin-bottom: 5px;
 }

 .stat-label {
     font-size: 14px;
     color: var(--text-muted);
 }

 /* Team Section */
 .team-section {
     padding: 80px 5%;
     text-align: center;
 }

 .team-title {
     font-size: 32px;
     font-weight: 700;
     margin-bottom: 50px;
     position: relative;
     display: inline-block;
 }

 .team-title::after {
     content: '';
     position: absolute;
     bottom: -10px;
     left: 50%;
     transform: translateX(-50%);
     width: 60px;
     height: 3px;
     background-color: var(--primary-color);
 }

 .team-featured {
     position: relative;
     margin-bottom: 100px;
 }

 .team-featured-bg {
     position: relative;
     height: 400px;
     background-color: rgba(26, 28, 30, 0.3);
     border-radius: var(--border-radius-lg);
     overflow: hidden;
 }

 .team-featured-bg::before {
     content: '';
     position: absolute;
     top: 0;
     left: 0;
     width: 100%;
     height: 100%;
     background: url('/api/placeholder/1200/600');
     background-size: cover;
     background-position: center;
     opacity: 0.2;
     z-index: 0;
 }

 .team-profile {
     position: absolute;
     left: 50%;
     bottom: -70px;
     transform: translateX(-50%);
     width: 500px;
     background-color: var(--background-card);
     border-radius: var(--border-radius-md);
     box-shadow: var(--shadow-md);
     padding: 20px;
     display: flex;
     flex-direction: column;
     align-items: center;
 }

 .profile-img {
     width: 120px;
     height: 120px;
     border-radius: 50%;
     margin-top: -80px;
     border: 5px solid var(--background-card);
     object-fit: cover;
 }

 .profile-name {
     font-size: 24px;
     font-weight: 700;
     margin-top: 15px;
 }

 .profile-title {
     color: var(--primary-color);
     font-size: 16px;
     margin-bottom: 15px;
 }

 .social-links {
     display: flex;
     gap: 15px;
     margin-bottom: 15px;
 }

 .social-link {
     display: flex;
     align-items: center;
     justify-content: center;
     width: 36px;
     height: 36px;
     background-color: var(--background-dark);
     border-radius: 50%;
     color: var(--text-light);
     transition: all 0.3s ease;
 }

 .social-link:hover {
     background-color: var(--primary-color);
     transform: translateY(-3px);
 }

 .profile-bio {
     text-align: center;
     color: var(--text-muted);
     font-size: 14px;
     line-height: 1.6;
     max-width: 450px;
 }

 .team-gallery {
     display: flex;
     justify-content: center;
     gap: 30px;
     flex-wrap: wrap;
     margin-top: 50px;
 }

 .team-member {
     width: 180px;
     position: relative;
     text-align: center;
     margin-bottom: 30px;
 }

 .member-img {
     width: 150px;
     height: 150px;
     border-radius: 50%;
     object-fit: cover;
     border: 3px solid var(--background-card);
     transition: all 0.3s ease;
 }

 .member-img:hover {
     transform: scale(1.1);
     border-color: var(--primary-color);
 }

 .profile-name {
     font-size: 18px;
     font-weight: 600;
     margin-top: 15px;
     color: var(--text-light);
 }

 .profile-title {
     font-size: 14px;
     color: var(--text-muted);
     margin-top: 5px;
 }

 .nav-buttons {
     display: flex;
     justify-content: center;
     gap: 10px;
     margin-top: 30px;
 }

 .nav-arrow {
     width: 50px;
     height: 50px;
     display: flex;
     align-items: center;
     justify-content: center;
     background-color: var(--primary-color);
     color: white;
     border-radius: 50%;
     cursor: pointer;
     transition: all 0.3s ease;
 }

 .nav-arrow:hover {
     background-color: var(--primary-dark);
     transform: scale(1.1);
 }

 /* Mission Section */
 .mission-section {
     padding: 80px 5%;
     background-color: var(--background-card);
 }

 .mission-content {
     display: grid;
     grid-template-columns: repeat(3, 1fr);
     gap: 30px;
     margin-top: 40px;
 }

 .mission-card {
     padding: 30px;
     background-color: var(--background-dark);
     border-radius: var(--border-radius-md);
     box-shadow: var(--shadow-sm);
     transition: transform 0.3s ease, box-shadow 0.3s ease;
     height: 100%;
 }

 .mission-card:hover {
     transform: translateY(-10px);
     box-shadow: var(--shadow-md);
 }

 .mission-icon {
     font-size: 36px;
     color: var(--primary-color);
     margin-bottom: 20px;
 }

 .mission-title {
     font-size: 20px;
     font-weight: 600;
     margin-bottom: 15px;
 }

 .mission-text {
     color: var(--text-muted);
     font-size: 14px;
     line-height: 1.6;
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

 /* Responsive styles */
 @media (max-width: 992px) {
     .about-content {
         grid-template-columns: 1fr;
     }
     
     .mission-content {
         grid-template-columns: 1fr 1fr;
     }
     
     .team-profile {
         width: 90%;
         max-width: 450px;
     }
     
     .team-gallery {
         flex-wrap: wrap;
         justify-content: center;
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
     }

     .hamburger {
         display: block;
     }
     
     .hero-title {
         font-size: 48px;
     }
     
     .stats-container {
         grid-template-columns: 1fr;
     }
     
     .mission-content {
         grid-template-columns: 1fr;
     }
 }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar" aria-label="Main navigation">
        <a href="../index.php" class="nav-logo" aria-label="KICKS home">
            <i class="fas fa-shoe-prints" aria-hidden="true"></i>
            <span class="nav-logo-text">KICKS</span>
        </a>
        
        <div class="nav-links">
            <a href="index.php" class="nav-link">Home</a>
            <a href="index.php#products" class="nav-link">Products</a>
            <a href="about_us.php" class="nav-link" aria-current="page">About Us</a>
            <a href="index.php#contact" class="nav-link">Contact</a>
        </div>
        
        <div class="nav-buttons">
            <a href="../auth/login.php" class="nav-btn btn-signin">
                <i class="fas fa-sign-in-alt" aria-hidden="true"></i> Sign In
            </a>
            <a href="../auth/register.php" class="nav-btn btn-register">
                <i class="fas fa-user-plus" aria-hidden="true"></i> Register
            </a>
        </div>
        
        <button class="hamburger" aria-label="Toggle menu" aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </nav>

    <!-- Hero Section -->
    <section class="hero-about">
        <div class="hero-bg"></div>
        <div class="hero-text">
            <h1 class="hero-title">ABOUT US</h1>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section">
        <h2 class="section-title">Our Story</h2>
        <div class="about-content">
            <div class="about-image">
                <img src="../members/interior.png" alt="KICKS Store Interior" loading="lazy">
            </div>
            <div class="about-text">
                <p>Founded in 2025, KICKS emerged from a simple vision: to provide premium footwear that combines style, comfort, and sustainability. What began as a small online store has grown into a respected brand known for quality craftsmanship and innovative designs.</p>
                <p>At KICKS, we believe that great footwear should never compromise on comfort or environmental responsibility. That's why each pair is meticulously crafted using ethically sourced materials and sustainable production methods.</p>
                <p>Our team consists of passionate footwear experts, designers, and sneaker enthusiasts who are dedicated to pushing the boundaries of what premium footwear can be.</p>
                
                <div class="stats-container">
                    <div class="stat-item">
                        <div class="stat-value">5K+</div>
                        <div class="stat-label">HAPPY CUSTOMERS</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">200+</div>
                        <div class="stat-label">UNIQUE DESIGNS</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">15</div>
                        <div class="stat-label">COUNTRIES SERVED</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team-section">
        <h2 class="team-title">Meet Our Fellow Cartels</h2>
        
        <div class="team-gallery">
            <!-- Team Member 1 -->
            <div class="team-member">
                <img src="../members/dsds.jpg" alt="Dhenmarc T. Noval" class="member-img">
                <h3 class="profile-name">Dhenmarc T. Noval</h3>
                <p class="profile-title">Founder & Project Manager</p>
            </div>
            <!-- Team Member 2 -->
            <div class="team-member">
                <img src="../members/bossk.jpg" alt="Kyle Christian Ople" class="member-img">
                <h3 class="profile-name">Kyle Christian Ople</h3>
                <p class="profile-title">Full Stack Developer</p>
            </div>
            <!-- Team Member 3 -->
            <div class="team-member">
                <img src="../members/harhar.jpg" alt="John Harry Mata" class="member-img">
                <h3 class="profile-name">John Harry Mata</h3>
                <p class="profile-title">Back End Developer</p>
            </div>
            <!-- Team Member 4 -->
            <div class="team-member">
                <img src="../members/kappframework-HhGOSR(1)(1)(1).png" alt="Revenz Almaden" class="member-img">
                <h3 class="profile-name">Revenz Almaden</h3>
                <p class="profile-title">Ui/Ux Designer</p>
            </div>
            <!-- Team Member 5 -->
            <div class="team-member">
                <img src="../members/kyle (4).jpg" alt="Kent Justin Malazarte" class="member-img">
                <h3 class="profile-name">Kent Justin Malazarte</h3>
                <p class="profile-title">Ui/Ux Developer</p>
            </div>
            <!-- Team Member 6 -->
            <div class="team-member">
                <img src="../members/kyle (3).jpg" alt="Jeff Medio" class="member-img">
                <h3 class="profile-name">Jeff Medio</h3>
                <p class="profile-title">Documentation</p>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="mission-section">
        <h2 class="section-title">Our Mission & Values</h2>
        
        <div class="mission-content">
            <div class="mission-card">
                <div class="mission-icon">
                    <i class="fas fa-leaf" aria-hidden="true"></i>
                </div>
                <h3 class="mission-title">Sustainability</h3>
                <p class="mission-text">We're committed to reducing our environmental footprint. From recycled materials to eco-friendly packaging, sustainability is at the core of everything we do. Our goal is to create beautiful footwear that doesn't compromise the planet.</p>
            </div>
            
            <div class="mission-card">
                <div class="mission-icon">
                    <i class="fas fa-medal" aria-hidden="true"></i>
                </div>
                <h3 class="mission-title">Quality Craftsmanship</h3>
                <p class="mission-text">Each pair of KICKS undergoes rigorous quality control. Our footwear is designed to last, combining traditional craftsmanship with innovative materials. We stand behind every product we create with our satisfaction guarantee.</p>
            </div>
            
            <div class="mission-card">
                <div class="mission-icon">
                    <i class="fas fa-heart" aria-hidden="true"></i>
                </div>
                <h3 class="mission-title">Community Impact</h3>
                <p class="mission-text">We believe in giving back. Through our One Step Forward program, we donate a portion of every sale to providing footwear to communities in need. Our partnerships with local organizations help us create positive change where it's needed most.</p>
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
    document.addEventListener('DOMContentLoaded', function() {
        // No longer needed - team members are now directly in HTML

        // Mobile menu toggle
        const hamburger = document.querySelector('.hamburger');
        const navLinks = document.querySelector('.nav-links');
        const navButtons = document.querySelector('.nav-buttons');

        hamburger.addEventListener('click', function() {
            this.classList.toggle('active');
            navLinks.classList.toggle('active');
            navButtons.classList.toggle('active');
            
            // Update aria-expanded attribute for accessibility
            const expanded = this.classList.contains('active');
            this.setAttribute('aria-expanded', expanded);
        });

        // Navbar scroll effect
        function handleScroll() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        }

        window.addEventListener('scroll', handleScroll);
        
        // Initialize scroll state on page load
        handleScroll();

        // Add hover effect to team member images
        const memberImages = document.querySelectorAll('.member-img');
        memberImages.forEach(img => {
            img.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.1)';
                this.style.borderColor = 'var(--primary-color)';
            });
            
            img.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
                this.style.borderColor = 'var(--background-card)';
            });
        });
    });
    </script>
</body>
</html></qodoArtifact>
