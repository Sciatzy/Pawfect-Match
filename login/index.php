<?php
session_start();

require '../includes/db.php';
require '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__))->load();

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if(!isset($_COOKIE['token'])){
  header('location: ../login/login.php');
  exit();
}

$decoded = JWT::decode($_COOKIE['token'], new Key($_ENV['JWT_SECRET'], 'HS256'));
$user_id = $decoded->data->user_id;

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pawfect Match</title>
  
  <!-- Bootstrap CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../styles/styles.css" />
  
  <style>
    /* Inline header styles for consistent application across pages */
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
    
    .site-footer {
      background: #fff5ed;
      padding: 30px 0;
      margin-top: 60px;
    }
    
    .footer-logo {
      font-size: 1.5rem;
      font-weight: bold;
      color: #ff914d;
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
  </style>
</head>
<body>
  <!-- Header with inline styles -->
  <header class="site-header">
    <div class="container">
      <nav class="navbar navbar-expand-lg navbar-light py-3">
        <a href="#" class="site-logo navbar-brand">🐾 Pawfect Match</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto nav-menu">
            <li class="nav-item"><a class="n  av-link active" href="#">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="../adopt/pet-list.php">Adopt</a></li>
            <li class="nav-item"><a class="nav-link" href="#">About</a></li>
          </ul>
          <?php if(!isset($_COOKIE['token'])): ?>
          <div class="auth-buttons">
            <a href="login.php" class="auth-btn login-btn">Login</a>
            <a href="login.php" class="auth-btn signup-btn">Sign Up</a>
          </div>
          <?php elseif($user['role'] === 'admin'):?>
            <div class="auth-buttons">
            <a href="../admin/dashboard.php" class="auth-btn login-btn">Admin</a>
            <a href="logout.php" class="auth-btn login-btn">Logout</a>
          </div>
          <?php else: ?>
            <div class="auth-buttons">
            <a href="logout.php" class="auth-btn login-btn">Logout</a>
          </div>
          <?php endif;?>
        </div>
      </nav>
    </div>
  </header>

  <section class="hero">
    <div class="hero-content">
      <h1>Find Your Pawfect Match</h1>
      <a href="../adopt/pet-list.php"><button class="btn btn-lg" id="adoptNowBtn">Adopt Now</button></a>
    </div>
  </section>

  <section class="section bg-light">
    <div class="container">
      <h2 class="mb-5">Why Adopt?</h2>
      <div class="row features">
        <div class="col-md-4 mb-4">
          <div class="card h-100 border-0 text-center p-4 feature-card">
            <div class="feature-icon display-4">❤️</div>
            <div class="card-body">
              <h4 class="card-title">Save a Life</h4>
              <p class="card-text">Give a homeless pet a second chance at happiness.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card h-100 border-0 text-center p-4 feature-card">
            <div class="feature-icon display-4">🐶</div>
            <div class="card-body">
              <h4 class="card-title">Find a Companion</h4>
              <p class="card-text">Discover unconditional love and friendship.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card h-100 border-0 text-center p-4 feature-card">
            <div class="feature-icon display-4">🏡</div>
            <div class="card-body">
              <h4 class="card-title">Make Space for Others</h4>
              <p class="card-text">Help shelters rescue more animals in need.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Updated Footer -->
  <footer class="site-footer">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-4 mb-3 mb-md-0">
          <div class="footer-logo">🐾 Pawfect Match</div>
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

  <!-- Bootstrap JS Bundle with Popper -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
  
  <!-- Custom JavaScript -->
  <script src="js/script.js"></script>
</body>
</html>