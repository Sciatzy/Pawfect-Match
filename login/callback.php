<?php
require_once __DIR__ . '/vendor/autoload.php'; // Load Composer autoloader
require_once __DIR__ . '/config.php'; // Load configuration (DB, Google keys)

session_start();

// Initialize Google Client
$client = new Google\Client();
$client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
$client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
$client->setRedirectUri('http://localhost/login-callback.php');
$client->addScope('email');
$client->addScope('profile');

// Database connection (using PDO)
try {
    $pdo = new PDO(
        "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']}", 
        $_ENV['DB_USER'], 
        $_ENV['DB_PASS']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle Google callback
if (isset($_GET['code'])) {
    try {
        // Exchange authorization code for access token
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        $client->setAccessToken($token);

        // Get user info
        $oauth = new Google\Service\Oauth2($client);
        $userInfo = $oauth->userinfo->get();

        // Check if user exists in database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE google_id = ? OR email = ?");
        $stmt->execute([$userInfo->id, $userInfo->email]);
        $user = $stmt->fetch();

        if (!$user) {
            // New user - register them
            $stmt = $pdo->prepare(
                "INSERT INTO users (google_id, name, email, profile_pic) 
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([
                $userInfo->id,
                $userInfo->name,
                $userInfo->email,
                $userInfo->picture
            ]);
            $userId = $pdo->lastInsertId();
        } else {
            // Existing user - update info if needed
            $userId = $user['id'];
            if (empty($user['google_id'])) {
                $stmt = $pdo->prepare(
                    "UPDATE users SET google_id = ? WHERE id = ?"
                );
                $stmt->execute([$userInfo->id, $userId]);
            }
        }

        // Set user session
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_email'] = $userInfo->email;
        $_SESSION['user_name'] = $userInfo->name;
        $_SESSION['logged_in'] = true;

        // Redirect to dashboard
        header('Location: dashboard.php');
        exit();

    } catch (Exception $e) {
        // Log error and show message
        error_log("Google login error: " . $e->getMessage());
        die("An error occurred during login. Please try again.");
    }
} else {
    // No auth code - redirect to login
    header('Location: login.php');
    exit();
}