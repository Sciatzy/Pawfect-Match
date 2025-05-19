<?php
require_once '../includes/db.php';
require '../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

session_start();

// Check for JWT token
$isLoggedIn = false;
$user_id = null;

if (isset($_COOKIE['token'])) {
    try {
        $secret_key = $_ENV['JWT_SECRET'];
        $token = $_COOKIE['token'];
        $decoded = JWT::decode($token, keyOrKeyArray: new Key($secret_key, 'HS256'));
        $isLoggedIn = true;
        $user_id = $decoded->data->user_id;
    } catch (Exception $e) {
        // Token is invalid
        $isLoggedIn = false;
    }
}

$strays = $pdo->query("SELECT * FROM strays WHERE rescued_date IS NULL ORDER BY urgency DESC, created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stray Animal Reports - Pawfect Match</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<style>
        :root {
            --urgent: #ff6b6b;
            --high: #ffa502;
            --medium: #feca57;
            --low: #1dd1a1;
        }
        
        html {
            height: 100%;
        }
        
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        
        /* Header styles */
        .site-header {
            background-color: #fff;
            display: flex;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
            width: 100%;
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
        
        .nav-menu .nav-link {
            color: #444;
            font-weight: 600;
            margin: 0 10px;
            transition: color 0.3s ease;
        }
        
        .nav-menu .nav-link:hover,
        .nav-menu .nav-link.active {
            color: #ff914d;
        }
        
        .auth-btn {
            margin-left: 10px;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .login-btn {
            background-color: transparent;
            border: 1px solid #333;
            color: #333;
        }
        
        .login-btn:hover {
            background-color: #333;
            color: white;
        }
        
        .signup-btn {
            background-color: #ff914d;
            color: #fff;
            border: 1px solid #ff914d;
        }
        
        .signup-btn:hover {
            background-color: #e87e3c;
        }
        
        .main-content {
            flex: 1 0 auto;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }
        
        @media (max-width: 768px) {
            .auth-buttons {
                margin-top: 15px;
                display: flex;
                justify-content: start;
        }
        
            .auth-btn {
                margin: 5px;
            }
        }
        
        /* Remove redundant header styles */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        h1 {
            color: #333;
            margin: 0;
        }
        
        .report-btn {
            background-color: var(--urgent);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .report-btn:hover {
            background-color: #ff5252;
            transform: translateY(-2px);
        }
        
        .stray-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .stray-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            position: relative;
        }
        
        .stray-card:hover {
            transform: translateY(-5px);
        }
        
        .urgency-tag {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 5px 10px;
            border-radius: 20px;
            color: white;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
        }
        
        .urgent { background: var(--urgent); }
        .high { background: var(--high); }
        .medium { background: var(--medium); }
        .low { background: var(--low); }
        
        .stray-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
        }
        
        .stray-details {
            padding: 20px;
        }
        
        .stray-name {
            margin: 0 0 10px;
            color: #333;
            font-size: 1.5rem;
        }
        
        .stray-meta {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            color: #666;
            font-size: 0.9rem;
        }
        
        .stray-description {
            margin-bottom: 15px;
            color: #555;
        }
        
        .location {
            display: flex;
            align-items: center;
            color: var(--urgent);
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .location svg {
            margin-right: 5px;
        }
        
        .contact-btn {
            display: inline-block;
            background-color: #333;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background 0.3s ease;
        }
        
        .contact-btn:hover {
            background-color: #555;
        }
        
        .no-strays {
            text-align: center;
            padding: 50px;
            color: #666;
            grid-column: 1 / -1;
        }
        
        /* Footer styles */
        .site-footer {
            background: #fff5ed;
            padding: 30px 0;
            width: 100%;
            flex-shrink: 0;
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
        
        .social-icon {
            margin-left: 10px;
            color: #ff914d;
            font-size: 1.2rem;
            transition: color 0.3s ease;
        }
        
        .social-icon:hover {
            color: #e87e3c;
        }

        .menu-item.active {
            background-color: #f5f5f5; /* lighter highlight */
            color: #ee7721;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="site-header">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light py-3">
                <a href="../login/index.php" class="site-logo navbar-brand">
                    <img src="../images/logo.png" alt="Pawfect Match Logo">
                    Pawfect Match
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto nav-menu">
                        <li class="nav-item"><a class="nav-link" href="../login/index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="../adopt/pet-list.php">Adopt</a></li>
                        <li class="nav-item"><a class="nav-link active" href="stray-reports.php">Stray Reports</a></li>
                    </ul>
                    <?php if(!isset($_COOKIE['token'])): ?>
                    <div class="auth-buttons">
                        <a href="../login/login.php" class="auth-btn login-btn">Login</a>
                        <a href="../login/login.php" class="auth-btn signup-btn">Sign Up</a>
                    </div>
        <?php else: ?>
                    <div class="auth-buttons">
                        <a href="../login/logout.php" class="auth-btn login-btn">Logout</a>
                        <a href="report-stray.php" class="auth-btn signup-btn">Report a Stray</a>
                    </div>
                    <?php endif;?>
                </div>
            </nav>
        </div>
    </header>

    <!-- Page Content -->
    <div class="main-content">
        <div class="page-header">
            <h1>Stray Animal Reports</h1>
            <?php if($isLoggedIn): ?>
                <a href="report-stray.php" class="report-btn">
                    <i class="fas fa-plus"></i> Report a Stray
                </a>
            <?php else: ?>
                <a href="../login/login-report.php" class="report-btn">
                    <i class="fas fa-sign-in-alt"></i> Login to Report
                </a>
            <?php endif; ?>
        </div>
    
    <div class="stray-list">
        <?php if (empty($strays)): ?>
            <div class="no-strays">
                <h2>No stray reports currently</h2>
                <p>Be the first to report a stray animal in need</p>
            </div>
        <?php else: ?>
            <?php foreach ($strays as $stray): ?>
                <div class="stray-card">
                    <div class="urgency-tag <?= htmlspecialchars($stray['urgency']) ?>">
                        <?= ucfirst(htmlspecialchars($stray['urgency'])) ?>
                    </div>
                    
                    <img src="<?= htmlspecialchars($stray['image_path']) ?>" 
                         alt="<?= htmlspecialchars($stray['name']) ?>" 
                         class="stray-image">
                    
                    <div class="stray-details">
                        <h2 class="stray-name"><?= htmlspecialchars($stray['name']) ?></h2>
                        
                        <div class="stray-meta">
                            <span><?= htmlspecialchars($stray['age']) ?> years</span>
                            <span><?= ucfirst(htmlspecialchars($stray['gender'])) ?></span>
                            <span><?= htmlspecialchars($stray['weight']) ?> kg</span>
                        </div>
                        
                        <div class="location">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                            </svg>
                            <?= htmlspecialchars($stray['location']) ?>
                        </div>
                        
                        <p class="stray-description">
                            <?= nl2br(htmlspecialchars($stray['description'])) ?>
                        </p>
                        
                        <?php $status = $stray['report_status'] ?? 'pending'; ?>
                        <?php if ($status !== 'resolved'): ?>
                            <form action="process-rescue.php" method="POST" style="display: inline;">
                                <input type="hidden" name="report_id" value="<?= $stray['stray_id'] ?>">
                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to mark this stray as rescued?')">
                                    <i class="fas fa-check-circle"></i> Mark as Rescued
                                </button>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-success text-center" style="background: #e6f9ec; color: #388e3c; border-radius: 5px; padding: 8px 0; font-weight: bold;">Already Rescued</div>
                        <?php endif; ?>
                        
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    </div>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="footer-logo">
                        <img src="../images/logo.png" alt="Pawfect Match Logo">
                        Pawfect Match
                    </div>
                </div>
                <div class="col-md-4 mb-3 mb-md-0 text-center">
                    <p class="mb-0">Copyright © 2025 Bukidnon Aspin Refuge Kennel - BARK. All Rights Reserved.</p>
                </div>
                <div class="col-md-4 text-md-end text-center">
                    <div class="social-icons">
                        <a href="https://www.facebook.com/bukidnonaspinrefugekennel" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>