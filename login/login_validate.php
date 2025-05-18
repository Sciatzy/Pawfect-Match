<?php

session_start();

require '../includes/db.php';
require '../includes/jwt.php';
require '../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // Store form data in session
    $_SESSION['form_data'] = [
        'email' => $_POST['email'],
        'password' => $_POST['password']
    ];

    // Verify reCAPTCHA first
    if (!isset($_POST['g-recaptcha-response']) || empty($_POST['g-recaptcha-response'])) {
        $_SESSION['error'] = "Please check the reCAPTCHA box.";
        header("Location: login.php");
        exit();
    }

    // Verify the reCAPTCHA response with Google
    $recaptcha_secret = $_ENV['RECAPTCHA_SECRET_KEY'];
    $recaptcha_response = $_POST['g-recaptcha-response'];
    
    $verify_response = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$recaptcha_secret.'&response='.$recaptcha_response);
    $response_data = json_decode($verify_response);
    
    if (!$response_data->success) {
        $_SESSION['error'] = "reCAPTCHA verification failed. Please try again.";
        header("Location: login.php");
        exit();
    }

    $input = trim($_POST['email']); // This could be email or username
    $password = $_POST['password'];

    try {
        // Clear form data from session since validation passed
        unset($_SESSION['form_data']);
        
        // Check if input is email or username
        $isEmail = filter_var($input, FILTER_VALIDATE_EMAIL);
        
        if ($isEmail) {
            // If input is email, search by email
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        } else {
            // If input is username, search by username
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        }
        
        if (!$stmt) {
            throw new PDOException("Failed to prepare statement: " . print_r($pdo->errorInfo(), true));
        }
        
        $stmt->execute([$input]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['ID'] = $user['ID'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['lastname'] = $user['lastname'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            // Generate JWT token
            $payload = [
                'iat' => time(),
                'exp' => time() + (60 * 60 * 24), // 24 hours
                'data' => [
                    'user_id' => $user['ID'],
                    'username' => $user['username']
                ]
            ];
            
            $jwt = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
            
            // Set JWT token in cookie
            setcookie('token', $jwt, time() + (60 * 60 * 24), '/', '', false, true);
            
            // Create login_logs table if it doesn't exist
            try {
                $pdo->exec("CREATE TABLE IF NOT EXISTS login_logs (
                    log_id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    login_time DATETIME NOT NULL,
                    status VARCHAR(50) NOT NULL,
                    FOREIGN KEY (user_id) REFERENCES users(ID)
                )");
                
                // Log successful login
                $logStmt = $pdo->prepare("INSERT INTO login_logs (user_id, login_time, status) VALUES (?, NOW(), 'success')");
                $logStmt->execute([$user['ID']]);
            } catch (PDOException $e) {
                // Log the error but don't stop the login process
                error_log("Failed to log successful login: " . $e->getMessage());
            }
            
            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: /pawfect/login/index.php");
            }
        exit();
    } else {
            try {
                // Create login_logs table if it doesn't exist
                $pdo->exec("CREATE TABLE IF NOT EXISTS login_logs (
                    log_id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    login_time DATETIME NOT NULL,
                    status VARCHAR(50) NOT NULL,
                    FOREIGN KEY (user_id) REFERENCES users(ID)
                )");
                
                // Log failed login attempt
                if ($user) {
                    $logStmt = $pdo->prepare("INSERT INTO login_logs (user_id, login_time, status) VALUES (?, NOW(), 'failed - wrong password')");
                    $logStmt->execute([$user['ID']]);
                }
            } catch (PDOException $e) {
                error_log("Failed to log failed login attempt: " . $e->getMessage());
            }
            
            $_SESSION['error'] = "Invalid email/username or password.";
            header("Location: login.php");
            exit();
        }
    } catch (PDOException $e) {
        error_log("Login error details: " . $e->getMessage());
        error_log("SQL State: " . $e->getCode());
        error_log("Error Info: " . print_r($pdo->errorInfo(), true));
        $_SESSION['error'] = "An error occurred. Please try again later.";
        header("Location: login.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}

?>
