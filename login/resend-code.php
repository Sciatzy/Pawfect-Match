<?php
session_start();
require '../includes/db.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['verification_email'])) {
    header('Location: login.php');
    exit();
}

$email = $_SESSION['verification_email'];

// Generate new verification code
$verification_code = sprintf("%06d", mt_rand(1, 999999));
$verification_expires = date('Y-m-d H:i:s', strtotime('+24 hours'));

try {
    // Update verification code in database
    $stmt = $pdo->prepare("UPDATE users SET verification_code = ?, verification_expires = ? WHERE email = ? AND is_verified = 0");
    $stmt->execute([$verification_code, $verification_expires, $email]);

    // Get user's name
    $stmt = $pdo->prepare("SELECT firstname, lastname FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Send new verification email
    $mail = new PHPMailer(true);

    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = $_ENV['SMTP_USERNAME'];
    $mail->Password = $_ENV['SMTP_PASSWORD'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Recipients
    $mail->setFrom($_ENV['SMTP_USERNAME'], 'Pawfect Match');
    $mail->addAddress($email, $user['firstname'] . ' ' . $user['lastname']);

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'New Verification Code - Pawfect Match';
    $mail->Body = "
        <h2>New Verification Code</h2>
        <p>Here is your new verification code:</p>
        <h1 style='font-size: 32px; color: #ff914d;'>{$verification_code}</h1>
        <p>This code will expire in 24 hours.</p>
        <p>If you didn't request this code, please ignore this email.</p>
    ";

    $mail->send();
    
    $_SESSION['success'] = "A new verification code has been sent to your email.";
    header('Location: verify-email.php');
    exit();

} catch (Exception $e) {
    $_SESSION['error'] = "Failed to send verification code. Please try again.";
    header('Location: verify-email.php');
    exit();
}
?> 