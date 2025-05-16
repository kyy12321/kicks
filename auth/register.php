<?php
include '../includes/auth.php';

$errors = [];
$username = $email = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate inputs
    if (empty($username)) {
        $errors['username'] = "Username is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Valid email is required.";
    }
    if (empty($password)) {
        $errors['password'] = "Password is required.";
    }
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match.";
    }

    // If no errors, proceed with registration
    if (empty($errors) && registerUser($username, $password)) {
        header("Location: login.php");
        exit();
    } else {
        $errors['general'] = "Registration failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | KICKS</title>
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
            max-width: 1200px;
            border-radius: var(--border-radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            position: relative;
            z-index: 1;
        }

        .image-side {
            width: 40%;
            background: url('https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1587&q=80') center/cover;
            position: relative;
            overflow: hidden;
            box-shadow: inset 0 0 80px rgba(0, 0, 0, 0.5);
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
            font-family: 'Poppins', sans-serif;
        }

        .image-subtext {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 20px;
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
        }

        .content-side {
            width: 60%;
            background-color: var(--background-card);
            padding: 50px;
            display: flex;
            flex-direction: column;
            box-shadow: var(--shadow-sm);
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
            gap: 12px;
            font-family: 'Poppins', sans-serif;
        }

        .logo i {
            font-size: 32px;
            color: var(--primary-color);
            filter: drop-shadow(0 0 2px rgba(76, 175, 80, 0.3));
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
            margin-bottom: 8px;
            font-size: 14px;
            color: var(--text-light);
            font-weight: 500;
            transition: all var(--transition-speed) ease;
        }

        .form-control {
            width: 100%;
            padding: 15px;
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
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .error-message::before {
            content: "\f071";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            font-size: 12px;
        }

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

        .login-link {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: var(--text-muted);
        }

        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all var(--transition-speed) ease;
        }

        .login-link a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .footer {
            margin-top: 40px;
            font-size: 13px;
            color: var(--text-muted);
            text-align: center;
        }

        /* Animation */
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
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="image-side">
            <div class="image-overlay">
                <h2 class="image-text">Join The Community</h2>
                <p class="image-subtext">Get exclusive access to limited editions and member-only events</p>
            </div>
        </div>
        <div class="content-side">  
            <a href="../index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Home</a>
            <div class="logo">
                <i class="fas fa-shoe-prints"></i> KICKS
            </div>
            <h1 class="form-title">Create Your Account</h1>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" placeholder="Choose a username">
                    <i class="fas fa-user form-icon"></i>
                    <?php if (isset($errors["username"])): ?>
                        <div class="error-message"><?php echo $errors["username"]; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="Enter your email">
                    <i class="fas fa-envelope form-icon"></i>
                    <?php if (isset($errors["email"])): ?>
                        <div class="error-message"><?php echo $errors["email"]; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Create a password">
                    <i class="fas fa-lock form-icon"></i>
                    <?php if (isset($errors["password"])): ?>
                        <div class="error-message"><?php echo $errors["password"]; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="confirm_password">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm your password">
                    <i class="fas fa-lock form-icon"></i>
                    <?php if (isset($errors["confirm_password"])): ?>
                        <div class="error-message"><?php echo $errors["confirm_password"]; ?></div>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>
            
            <div class="login-link">
                Already have an account? <a href="login.php">Sign In</a>
            </div>
            
            <div class="footer">© 2025 KICKS • All Rights Reserved</div>
        </div>
    </div>
</body>
</html>