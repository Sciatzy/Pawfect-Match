<?php

session_start();

require '../includes/db.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load environment variables safely
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->safeLoad();
} catch (Exception $e) {
    $_SESSION['error'] = "Configuration error. Please contact the administrator.";
    header('Location: signup.php');
    exit();
}

// Set default SMTP settings if not in .env
$smtp_username = $_ENV['SMTP_USERNAME'] ?? 'your-email@gmail.com';
$smtp_password = $_ENV['SMTP_PASSWORD'] ?? 'your-app-password';

// Debug environment variables (without exposing actual values)
if (!isset($_ENV['SMTP_USERNAME'])) {
    $_SESSION['error'] = "SMTP_USERNAME is not set in .env file";
    header('Location: signup.php');
    exit();
}

if (!isset($_ENV['SMTP_PASSWORD'])) {
    $_SESSION['error'] = "SMTP_PASSWORD is not set in .env file";
    header('Location: signup.php');
    exit();
}

// Check if SMTP username is a valid email
if (!filter_var($_ENV['SMTP_USERNAME'], FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "SMTP_USERNAME must be a valid email address";
    header('Location: signup.php');
    exit();
}

// Data validation functions
function validateEmail($email) {
    if (empty($email)) {
        return "Email is required";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Invalid email format";
    }
    return true;
}

function validateUsername($username) {
    if (empty($username)) {
        return "Username is required";
    }
    if (strlen($username) < 3 || strlen($username) > 20) {
        return "Username must be between 3 and 20 characters";
    }
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        return "Username can only contain letters, numbers, and underscores";
    }
    return true;
}

function validateName($name, $field) {
    if (empty($name)) {
        return ucfirst($field) . " is required";
    }
    if (strlen($name) < 2 || strlen($name) > 50) {
        return ucfirst($field) . " must be between 2 and 50 characters";
    }
    if (!preg_match('/^[a-zA-Z\s\-]+$/', $name)) {
        return ucfirst($field) . " can only contain letters, spaces, and hyphens";
    }
    return true;
}

function validatePassword($password) {
    if (empty($password)) {
        return "Password is required";
    }
    if (strlen($password) < 8) {
        return "Password must be at least 8 characters long";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return "Password must contain at least one uppercase letter";
    }
    if (!preg_match('/[a-z]/', $password)) {
        return "Password must contain at least one lowercase letter";
    }
    if (!preg_match('/[0-9]/', $password)) {
        return "Password must contain at least one number";
    }
    if (!preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $password)) {
        return "Password must contain at least one special character (!@#$%^&*()-_=+{};:,<.>)";
    }
    return true;
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    // Sanitize all inputs
    $firstname = sanitizeInput($_POST['firstname']);
    $lastname = sanitizeInput($_POST['lastname']);
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // Store form data in session
    $_SESSION['form_data'] = [
        'firstname' => $firstname,
        'lastname' => $lastname,
        'username' => $username,
        'email' => $email
    ];

    // Validate all fields
    $validations = [
        'firstname' => validateName($firstname, 'firstname'),
        'lastname' => validateName($lastname, 'lastname'),
        'username' => validateUsername($username),
        'email' => validateEmail($email),
        'password' => validatePassword($password)
    ];

    // Check for validation errors
    foreach ($validations as $field => $result) {
        if ($result !== true) {
            $_SESSION['error'] = $result;
            header('Location: signup.php');
            exit();
        }
    }

    if ($password !== $confirm) {
        $_SESSION['error'] = "Passwords do not match.";
        header('Location: signup.php');
        exit();
    }

    // Check for existing username or email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "Username or email already exists.";
        header('Location: signup.php');
        exit();
    }

    // Generate verification code
    $verification_code = sprintf("%06d", mt_rand(1, 999999));
    $verification_expires = date('Y-m-d H:i:s', strtotime('+24 hours'));

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    try {
        // Start transaction
        $pdo->beginTransaction();

        // Insert new user
        $stmt = $pdo->prepare("INSERT INTO users (firstname, lastname, username, email, password, verification_code, verification_expires, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?, 0)");
        $stmt->execute([$firstname, $lastname, $username, $email, $hashedPassword, $verification_code, $verification_expires]);

        // Send verification email
        $mail = new PHPMailer(true);

        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_username;
        $mail->Password = $smtp_password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // Enable detailed debugging
        $mail->SMTPDebug = 3;
        $mail->Debugoutput = function($str, $level) {
            error_log("SMTP Debug: $str");
        };

        // Add timeout settings
        $mail->Timeout = 60;
        $mail->SMTPKeepAlive = true;

        // Test SMTP connection before sending
        try {
            $mail->smtpConnect([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]);
            error_log("SMTP Connection successful");
        } catch (Exception $e) {
            error_log("SMTP Connection failed: " . $e->getMessage());
            throw new Exception("SMTP Connection failed: " . $e->getMessage());
        }

        // Validate email addresses
        if (!filter_var($smtp_username, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid SMTP username email format");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid recipient email format");
        }

        // Recipients
        $mail->setFrom($smtp_username, 'Pawfect Match');
        $mail->addAddress($email, $firstname . ' ' . $lastname);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Verify Your Email - Pawfect Match';
        $mail->Body = "
            <h2>Welcome to Pawfect Match!</h2>
            <p>Thank you for signing up. Please use the following code to verify your email address:</p>
            <h1 style='font-size: 32px; color: #ff914d;'>{$verification_code}</h1>
            <p>This code will expire in 24 hours.</p>
            <p>If you didn't create this account, please ignore this email.</p>
        ";

        $mail->send();
        
        // Commit transaction
        $pdo->commit();

        // Clear form data on success
        unset($_SESSION['form_data']);
        
        $_SESSION['success'] = "Your account has been created. Please check your email for verification code.";
        $_SESSION['verification_email'] = $email;
        header('Location: verify-email.php');
    exit();

    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        
        // Log the error for debugging
        error_log("Registration error: " . $e->getMessage());
        
        // Show the actual error message for debugging
        $_SESSION['error'] = "Registration failed: " . $e->getMessage();
        header('Location: signup.php');
        exit();
    }
}