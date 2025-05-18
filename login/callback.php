<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

use Firebase\JWT\JWT;

// Debug function
function debug_log($message) {
    error_log("[Google Login Debug] " . $message);
}

debug_log("Callback started");

try {
    // Initialize Google Client
    $client = new Google\Client();
    $client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
    $client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
    
    // Set the exact redirect URI that matches Google Cloud Console
    $redirect_uri = 'http://localhost/pawfect/login/callback.php';
    $client->setRedirectUri($redirect_uri);
    debug_log("Redirect URI set to: " . $redirect_uri);

    // Handle Google callback
    if (isset($_GET['code'])) {
        debug_log("Authorization code received");
        try {
            // Exchange authorization code for access token
            $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
            $client->setAccessToken($token);
            debug_log("Access token obtained successfully");

            // Get user info
            $oauth = new Google\Service\Oauth2($client);
            $userInfo = $oauth->userinfo->get();
            debug_log("User info retrieved for email: " . $userInfo->email);

            // Check if user exists
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR google_id = ?");
            $stmt->bind_param("ss", $userInfo->email, $userInfo->id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                debug_log("Creating new user");
                // Create new user
                $username = explode('@', $userInfo->email)[0] . rand(100, 999);
                $name_parts = explode(' ', $userInfo->name);
                $firstname = $name_parts[0];
                $lastname = count($name_parts) > 1 ? $name_parts[1] : '';
                $role = 'user';
                $is_verified = 1;
                $stmt = $conn->prepare("INSERT INTO users (username, email, firstname, lastname, google_id, is_verified, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssis", $username, $userInfo->email, $firstname, $lastname, $userInfo->id, $is_verified, $role);
                $stmt->execute();
                $user_id = $conn->insert_id;
                debug_log("New user created with ID: " . $user_id);
            } else {
                $user = $result->fetch_assoc();
                $user_id = $user['ID'];
                $username = $user['username'];
                debug_log("Existing user found with ID: " . $user_id);
                // Always update Google ID if not set or changed
                if (empty($user['google_id']) || $user['google_id'] !== $userInfo->id) {
                    $stmt = $conn->prepare("UPDATE users SET google_id = ? WHERE ID = ?");
                    $stmt->bind_param("si", $userInfo->id, $user_id);
                    $stmt->execute();
                    debug_log("Updated Google ID for user");
                }
            }
            
            // Set session
            $_SESSION['ID'] = $user_id;
            $_SESSION['username'] = $username;
            
            // Debug session state
            debug_log("Session data set - ID: " . $user_id . ", Username: " . $username);
            debug_log("Current session ID: " . session_id());
            debug_log("Session contents: " . print_r($_SESSION, true));
            
            // Generate JWT token
            $payload = [
                'iat' => time(),
                'exp' => time() + (60 * 60 * 24), // 24 hours
                'data' => [
                    'user_id' => $user_id,
                    'username' => $username
                ]
            ];
            
            $jwt = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
            
            // Set JWT token in cookie
            setcookie('token', $jwt, time() + (60 * 60 * 24), '/', '', false, true);
            
            // Ensure no output before redirect
            if (ob_get_length()) ob_clean();
            
            // Redirect to the login index page
            debug_log("Attempting redirect to login index.php");
            header('Location: /pawfect/login/index.php');
            exit();
            
        } catch (Exception $e) {
            debug_log("Error during Google login: " . $e->getMessage());
            $_SESSION['error'] = "An error occurred during Google login. Please try again.";
            header('Location: /pawfect/login/login.php');
            exit();
        }
    } else {
        debug_log("No authorization code received");
        $_SESSION['error'] = "Google authentication failed. No authorization code received.";
        header('Location: /pawfect/login/login.php');
        exit();
    }
} catch (Exception $e) {
    debug_log("Google client error: " . $e->getMessage());
    $_SESSION['error'] = "Google authentication error. Please try again.";
    header('Location: /pawfect/login/login.php');
    exit();
}
?>