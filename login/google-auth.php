<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

try {
    // Initialize Google Client
    $client = new Google\Client();
    $client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
    $client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
    
    // Set the exact redirect URI that matches Google Cloud Console
    $redirect_uri = 'http://localhost/pawfect/login/callback.php';
    $client->setRedirectUri($redirect_uri);
    
    // For debugging
    error_log("Google Auth Redirect URI: " . $redirect_uri);
    
    $client->addScope('email');
    $client->addScope('profile');

    // Generate the authorization URL
    $authUrl = $client->createAuthUrl();

    // Redirect to Google's OAuth 2.0 server
    header('Location: ' . $authUrl);
    exit;
} catch (Exception $e) {
    $_SESSION['error'] = "Google authentication error: " . $e->getMessage();
    header('Location: login.php');
    exit;
}
?> 