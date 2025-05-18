<?php
session_start();

require '../includes/db.php';
require_once __DIR__ . '/../vendor/autoload.php';

$doteenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$doteenv->load();

$siteKey = $_ENV['RECAPTCHA_SITE_KEY'];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user && password_verify($password, $user['password'])){
        $_SESSION['ID'] = $user['ID'];

        header('Location: ../stray-reports.php');
        exit();
    } else {
        $_SESSION['error'] = "Credentials are incorrect";
        header("Location: login.php");
        exit();
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
        /* Modern CSS Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }

        /* Full-page styling */
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        /* Main container */
        .login-container {
            display: flex;
            max-width: 1000px;
            width: 100%;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        /* Left side - Graphic */
        .graphic-side {
            flex: 1;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            color: white;
            text-align: center;
        }

        .graphic-side img {
            max-width: 80%;
            margin-bottom: 30px;
        }

        .graphic-side h2 {
            font-size: 1.8rem;
            margin-bottom: 15px;
        }

        /* Right side - Form */
        .form-side {
            flex: 1;
            padding: 60px 40px;
        }

        .form-header {
            margin-bottom: 40px;
        }

        .form-header h1 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 10px;
        }

        .form-header p {
            color: #666;
            font-size: 0.9rem;
        }

        /* Form elements */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        }

        /* Button and links */
        .actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
        }

        .login-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .reset-link {
            color: #667eea;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.2s;
        }

        .reset-link:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .signup-link {
            text-align: center;
            margin-top: 30px;
            color: #666;
        }

        .signup-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .signup-link a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }
            
            .graphic-side {
                padding: 30px 20px;
            }
            
            .form-side {
                padding: 40px 30px;
            }
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
    </style>
</head>
<body>
    <div class="login-container">
        <div class="graphic-side">
            <!-- Replace with your logo or image -->
            <img src="https://via.placeholder.com/300x200?text=Your+Logo" alt="Company Logo">
            <h2>Welcome Back</h2>
            <p>Sign in to access your account</p>
        </div>
        
        <div class="form-side">
            <div class="form-header">
                <h1>Welcome</h1>
                <p>Log in to your account</p>
            </div>
            <?php if (isset($_SESSION['error'])): ?>
        <p id="alertMessage" style="border: 1px solid #F74141; padding: 0.5rem 1rem !important; border-radius: 0.25rem; display: block; color: #F74141;">
          <?= $_SESSION['error'];
          unset($_SESSION['error']); ?>
        </p>
      <?php endif; ?>

      <?php if (isset($_SESSION['success'])): ?>
        <p id="alertMessage" style="border: 1px solid #77DD77; padding: 0.5rem 1rem !important; border-radius: 0.25rem; display: block; color: #77DD77;">
          <?= $_SESSION['success'];
          unset($_SESSION['success']); ?>
        </p>
      <?php endif; ?>
            
            <form action="" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="username" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div style="margin-bottom: 3px;" class="g-recaptcha" data-sitekey="<?= htmlspecialchars($siteKey)?>"></div>
                <div class="actions">
                    <button type="submit" class="login-btn">Log In</button>
                    <a href="forgot-password.php" class="reset-link">Reset Password</a>
                </div>
            </form>
            
            <div class="signup-link">
                <p>Don't have an account? <a href="signup.php">Signup</a></p>
            </div>
        </div>
    </div>
    <script src="https://www.google.com/recaptcha/api.js"></script>
</body>
</html>