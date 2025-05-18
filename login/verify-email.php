<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['verification_email'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_SESSION['verification_email'];
    $code = $_POST['verification_code'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND verification_code = ? AND verification_expires > NOW() AND is_verified = 0");
    $stmt->execute([$email, $code]);

    if ($stmt->rowCount() > 0) {
        // Update user as verified
        $stmt = $pdo->prepare("UPDATE users SET is_verified = 1, verification_code = NULL, verification_expires = NULL WHERE email = ?");
        $stmt->execute([$email]);

        unset($_SESSION['verification_email']);
        $_SESSION['success'] = "Email verified successfully! You can now login.";
        header('Location: login.php');
        exit();
    } else {
        $_SESSION['error'] = "Invalid or expired verification code.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Verify Email - Pawfect Match</title>
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
        
        .verify-card {
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
            text-align: center;
            letter-spacing: 4px;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #ff914d;
            box-shadow: 0 0 0 3px rgba(255, 145, 77, 0.2);
        }
        
        .verify-btn {
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
        
        .verify-btn:hover {
            background: #e87e3c;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 145, 77, 0.4);
        }
        
        .alert {
            background: #ffebee;
            color: #d32f2f;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .success {
            background: #e8f5e9;
            color: #2e7d32;
        }
    </style>
</head>
<body>
    <div class="verify-card">
        <div class="logo">
            <img src="../images/logo.png" alt="Pawfect Match Logo" style="max-width: 150px; height: auto;">
        </div>

        <h1>Verify Your Email</h1>
        <p class="subtext">Please enter the verification code sent to your email</p>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <form action="verify-email.php" method="POST">
            <div class="form-group">
                <label>Verification Code</label>
                <input type="text" name="verification_code" required maxlength="6" pattern="[0-9]{6}" title="Please enter the 6-digit code">
            </div>
            
            <button type="submit" class="verify-btn">Verify Email</button>
        </form>
        
        <p class="subtext" style="margin-top: 20px;">
            Didn't receive the code? <a href="resend-code.php" style="color: #ff914d; text-decoration: none;">Resend Code</a>
        </p>
    </div>
</body>
</html> 