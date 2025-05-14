<?php
session_start();
require '../includes/db.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sign Up - Pawfect Match</title>
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
        }
        
        .logo::before {
            content: "🐾";
            margin-right: 8px;
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
            border-color: #4285f4;
        }
        
        .signup-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            margin-top: 8px;
            cursor: pointer;
        }
        
        .login-link {
            margin-top: 20px;
            color: #666;
            font-size: 13px;
        }
        
        .login-link a {
            color: #4285f4;
            text-decoration: none;
            font-weight: 500;
        }
        
        .alert {
            background: #ffebee;
            color: #d32f2f;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="signup-card">
        <div class="logo">Pawfect Match</div>
        
        <h1>Create Your Account</h1>
        <p class="subtext">Join our pet rescue community</p>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <form action="signup_validate.php" method="POST">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="firstname" required>
            </div>
            
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="lastname" required>
            </div>
            
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required>
            </div>
            
            <button type="submit" class="signup-btn">Sign Up</button>
        </form>
        
        <p class="login-link">Already have an account? <a href="login.php">Log in</a></p>
    </div>
</body>
</html>