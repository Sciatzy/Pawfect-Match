<?php
session_start();
require '../includes/db.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sign Up - Pawfect Match</title>
    <link rel="stylesheet" href="styles/style.css">
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .signup-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            padding: 40px;
            width: 100%;
            max-width: 380px;
            text-align: center;
        }
        
        .logo {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 30px;
            color: #333;
            display: flex;
            justify-content: center;
        }
        
        .logo::before {
            content: none;
        }
        
        .logo img {
            max-width: 150px;
            height: auto;
        }
        
        h1 {
            font-size: 22px;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
        }
        
        .subtext {
            color: #666;
            margin-bottom: 25px;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 16px;
            text-align: left;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            color: #555;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #ff914d;
            box-shadow: 0 0 0 3px rgba(255, 145, 77, 0.2);
        }
        
        .signup-btn {
            width: 100%;
            padding: 12px;
            background: #ff914d;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            margin-top: 8px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .signup-btn:hover {
            background: #e87e3c;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 145, 77, 0.4);
        }
        
        .login-link {
            margin-top: 20px;
            color: #666;
            font-size: 13px;
        }
        
        .login-link a {
            color: #ff914d;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        
        .login-link a:hover {
            color: #e87e3c;
            text-decoration: underline;
        }
        
        .alert {
            background: #ffebee;
            color: #d32f2f;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .site-logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #ff914d;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .site-logo img {
            height: 40px;
            width: auto;
        }
        
        .footer-logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #ff914d;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .footer-logo img {
            height: 30px;
            width: auto;
        }

        /* Custom popup styles */
        .custom-popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
            text-align: center;
            min-width: 300px;
        }

        .custom-popup h3 {
            margin-bottom: 15px;
            color: #333;
        }

        .custom-popup p {
            margin-bottom: 20px;
            color: #666;
        }

        .custom-popup button {
            background: #ff914d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
        }

        .custom-popup button:hover {
            background: #e87e3c;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }
    </style>
</head>
<body>
    <div class="signup-card">
        <div class="logo">
            <img src="../images/logo.png" alt="Pawfect Match Logo" style="max-width: 150px; height: auto;">
        </div>
        
        <h1>Create Your Account</h1>
        <p class="subtext">Join our pet rescue community</p>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <form action="signup_validate.php" method="POST" id="signupForm" onsubmit="return validateForm()">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="firstname" required value="<?= isset($_SESSION['form_data']['firstname']) ? htmlspecialchars($_SESSION['form_data']['firstname']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="lastname" required value="<?= isset($_SESSION['form_data']['lastname']) ? htmlspecialchars($_SESSION['form_data']['lastname']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required value="<?= isset($_SESSION['form_data']['username']) ? htmlspecialchars($_SESSION['form_data']['username']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required value="<?= isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>
            
            <button type="submit" class="signup-btn">Sign Up</button>
        </form>
        
        <p class="login-link">Already have an account? <a href="login.php">Log in</a></p>
    </div>

    <!-- Custom Popup -->
    <div class="overlay" id="overlay"></div>
    <div class="custom-popup" id="customPopup">
        <h3>Password Requirements</h3>
        <p id="popupMessage"></p>
        <button onclick="closePopup()">OK</button>
    </div>

    <script>
        function showPopup(message) {
            document.getElementById('popupMessage').textContent = message;
            document.getElementById('customPopup').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }

        function closePopup() {
            document.getElementById('customPopup').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }

        function validateForm() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            // Check length
            if (password.length < 8) {
                showPopup('Password must be at least 8 characters long');
                return false;
            }
            
            // Check uppercase
            if (!/[A-Z]/.test(password)) {
                showPopup('Password must contain at least one uppercase letter');
                return false;
            }
            
            // Check lowercase
            if (!/[a-z]/.test(password)) {
                showPopup('Password must contain at least one lowercase letter');
                return false;
            }
            
            // Check number
            if (!/[0-9]/.test(password)) {
                showPopup('Password must contain at least one number');
                return false;
            }
            
            // Check special character
            if (!/[!@#$%^&*()\-_=+{};:,<.>]/.test(password)) {
                showPopup('Password must contain at least one special character (!@#$%^&*()-_=+{};:,<.>)');
                return false;
            }
            
            if (password !== confirmPassword) {
                showPopup('Passwords do not match');
                return false;
            }
            
            return true;
        }
    </script>
</body>
</html>
<?php
// Clear form data after displaying
unset($_SESSION['form_data']);
?>