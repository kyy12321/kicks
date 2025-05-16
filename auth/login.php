<?php
session_start();
include '../includes/auth.php';

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (loginUser($username, $password)) {
        // Redirect based on the user's role
        if ($_SESSION['role'] === 'admin') {
            header("Location: ../admin/dashboard.php");
        } elseif ($_SESSION['role'] === 'cashier') {
            header("Location: ../cashier/dashboard.php");
        } elseif ($_SESSION['role'] === 'superadmin') {
            header("Location: ../superadmin/dashboard.php");
        } elseif ($_SESSION['role'] === 'customer') {
            header("Location: ../ecomm.php");
        } else {
            header("Location: ../dashboard/index.php");
        }
        exit();
    } else {
        $error_message = "Invalid username or password.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | KICKS</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            font-family: 'Inter', sans-serif;
            line-height: 1.5;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at top right, rgba(76, 175, 80, 0.05), transparent 70%);
            z-index: -1;
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--background-dark);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--secondary-color);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .container {
            display: flex;
            width: 100%;
            max-width: 1000px;
            border-radius: var(--border-radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            position: relative;
            z-index: 1;
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(to right, var(--primary-color), var(--primary-dark));
            z-index: 2;
        }

        /* Image Side */
        .image-side {
            width: 40%;
            background: url('https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1587&q=80') center/cover;
            position: relative;
            overflow: hidden;
        }

        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.2) 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 40px;
        }

        .image-text {
            font-size: 32px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            color: var(--text-light);
        }

        .image-subtext {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 20px;
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
            color: var(--text-muted);
        }

        /* Content Side */
        .content-side {
            width: 60%;
            background-color: var(--background-card);
            padding: 50px;
            display: flex;
            flex-direction: column;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 30px;
            text-decoration: none;
            transition: color var(--transition-speed) ease;
        }

        .back-link:hover {
            color: var(--primary-color);
        }

        .back-link i {
            margin-right: 5px;
            font-size: 12px;
        }

        .logo {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 20px;
            background: linear-gradient(90deg, #4CAF50, #3e8e41);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: 'Poppins', sans-serif;
        }

        .logo i {
            font-size: 32px;
            color: var(--primary-color);
        }

        .form-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 30px;
            color: var(--text-light);
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 10px;
            font-size: 14px;
            color: var(--text-light);
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 16px;
            padding-left: 45px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--border-radius-sm);
            background-color: rgba(255, 255, 255, 0.05);
            color: var(--text-light);
            font-size: 15px;
            transition: all var(--transition-speed) ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            background-color: rgba(255, 255, 255, 0.07);
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.2);
        }

        .form-icon {
            position: absolute;
            left: 16px;
            top: 42px;
            color: var(--text-muted);
            font-size: 18px;
            transition: color var(--transition-speed) ease;
        }

        .form-control:focus + .form-icon {
            color: var(--primary-color);
        }

        .error-message {
            color: var(--error-color);
            font-size: 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .error-message i {
            font-size: 16px;
        }

        .forgot-password {
            text-align: right;
            margin-top: 5px;
            margin-bottom: 25px;
        }

        .forgot-password a {
            color: var(--primary-color);
            font-size: 14px;
            text-decoration: none;
            transition: all var(--transition-speed) ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .forgot-password a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .forgot-password a i {
            font-size: 12px;
        }

        /* Buttons */
        .btn {
            padding: 16px;
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
            width: 100%;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            box-shadow: 0 4px 10px rgba(76, 175, 80, 0.3);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(76, 175, 80, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn i {
            font-size: 18px;
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            margin: 30px 0;
            color: var(--text-muted);
            font-size: 14px;
        }

        .divider::before, .divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .divider::before {
            margin-right: 15px;
        }

        .divider::after {
            margin-left: 15px;
        }

        /* Social Login */
        .social-login {
            display: flex;
            justify-content: space-between;
            gap: 15px;
        }

        .social-btn {
            flex: 1;
            padding: 14px;
            border-radius: var(--border-radius-sm);
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background-color: rgba(255, 255, 255, 0.05);
            color: var(--text-light);
            border: 1px solid rgba(255, 255, 255, 0.1);
            cursor: pointer;
            transition: all var(--transition-speed) ease;
        }

        .social-btn:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .social-btn i {
            font-size: 18px;
        }

        .fb-btn i {
            color: #3b5998;
        }

        .google-btn i {
            color: #db4437;
        }

        /* Signup Link */
        .signup-link {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: var(--text-muted);
        }

        .signup-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all var(--transition-speed) ease;
        }

        .signup-link a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            font-size: 13px;
            color: var(--text-muted);
            text-align: center;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .content-side > * {
            animation: fadeIn 0.6s ease forwards;
        }

        .content-side > *:nth-child(1) { animation-delay: 0.1s; }
        .content-side > *:nth-child(2) { animation-delay: 0.2s; }
        .content-side > *:nth-child(3) { animation-delay: 0.3s; }
        .content-side > *:nth-child(4) { animation-delay: 0.4s; }
        .content-side > *:nth-child(5) { animation-delay: 0.5s; }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                max-width: 500px;
            }

            .image-side, .content-side {
                width: 100%;
            }

            .image-side {
                height: 200px;
            }

            .content-side {
                padding: 30px;
            }

            .social-login {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="image-side">
            <div class="image-overlay">
                <h2 class="image-text">Welcome Back</h2>
                <p class="image-subtext">Sign in to access your exclusive sneaker collection</p>
            </div>
        </div>
        <div class="content-side">
            <a href="../index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Home</a>
            <div class="logo">
                <i class="fas fa-shoe-prints"></i> KICKS
            </div>
            <h1 class="form-title">Sign In to Your Account</h1>
            
            <?php if (!empty($error_message)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username">
                    <i class="fas fa-user form-icon"></i>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password">
                    <i class="fas fa-lock form-icon"></i>
                </div>
                
                <div class="forgot-password">
                    <a href="forgot-password.php"><i class="fas fa-key"></i> Forgot Password?</a>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>
            
            <div class="signup-link">
                Don't have an account? <a href="register.php">Create Account</a>
            </div>
            
            <div class="footer">© 2025 BigByte Cartel</div>
        </div>
    </div>
</body>
</html>