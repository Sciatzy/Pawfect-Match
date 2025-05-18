<?php
require '../includes/db.php';
session_start();
$pets = $pdo->query("SELECT * FROM pets ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Our Pets - Pawfect Match</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- Custom CSS -->
    <link href="../styles/styles.css" rel="stylesheet">
    <link href="../styles/pet-list.css" rel="stylesheet">
    
    <style>
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
        color: #333;
        background-color: #f9f9f9;
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

    /* Adopt Button */
    .adopt-btn {
        width: 100%;
        padding: 15px;
        background: #ff914d;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: bold;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 1.1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 4px rgba(255, 145, 77, 0.2);
    }

    .adopt-btn:hover {
        background: #e87e3c;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 145, 77, 0.3);
    }

    .adopt-btn:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(255, 145, 77, 0.2);
    }

    /* Pet List Section Styles */
    .pet-list {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 32px;
        padding: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .pet-list-heading {
        text-align: center;
        color: #ff914d;
        font-size: 2.2rem;
        margin: 30px 0 10px;
        font-weight: 800;
        letter-spacing: 1px;
        line-height: 1.1;
        padding-bottom: 10px;
        position: relative;
    }

    .pet-list-heading:after {
        content: '';
        display: block;
        margin: 12px auto 0;
        width: 80px;
        height: 3px;
        background: #ff914d;
        border-radius: 2px;
    }

    @media (max-width: 600px) {
        .pet-list-heading {
            font-size: 1.3rem;
            padding-bottom: 6px;
        }
        .pet-list-heading:after {
            width: 40px;
            height: 2px;
        }
    }

    /* Footer styles */
    .site-footer {
        background: #fff5ed;
        padding: 30px 0;
        margin-top: auto;
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

    /* Pet Card Styles */
    .pet-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        overflow: hidden;
        transition: transform 0.3s ease;
        width: 320px;
        min-width: 260px;
        max-width: 100%;
        display: flex;
        flex-direction: column;
        align-items: stretch;
    }

    .pet-card:hover {
        transform: translateY(-5px);
    }

    .pet-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .pet-link:hover {
        text-decoration: none;
        color: inherit;
    }

    .pet-image {
        width: 100%;
        height: 320px;
        object-fit: cover;
    }

    .pet-details {
        padding: 20px;
        position: relative;
    }

    .pet-gender {
        position: absolute;
        top: -15px;
        right: 20px;
        background: #ff914d;
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }

    .pet-name {
        font-size: 1.5rem;
        margin-bottom: 10px;
        color: #333;
    }

    .pet-stats {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
    }

    .pet-stat {
        background: #f5f5f5;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.9rem;
    }

    .pet-description {
        color: #666;
        margin-bottom: 20px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Remove default link styles */
    .btn-primary {
        background: none;
        border: none;
        padding: 0;
        color: inherit;
    }

    .btn-primary:hover {
        background: none;
        color: inherit;
    }

    @media (max-width: 700px) {
        .pet-list {
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }
        .pet-card {
            width: 95%;
            min-width: unset;
        }
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
                        <li class="nav-item"><a class="nav-link active" href="pet-list.php">Adopt</a></li>
                        <li class="nav-item"><a class="nav-link" href="../userreport/stray-reports.php">Stray Reports</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">About</a></li>
                    </ul>
                    <?php if(!isset($_COOKIE['token'])): ?>
                    <div class="auth-buttons">
                        <a href="../login/login.php" class="auth-btn login-btn">Login</a>
                        <a href="../login/login.php" class="auth-btn signup-btn">Sign Up</a>
                    </div>
                    <?php else: ?>
                    <div class="auth-buttons">
                        <a href="../login/logout.php" class="auth-btn login-btn">Logout</a>
                        <a href="../userreport/report-stray.php" class="auth-btn signup-btn">Report a Stray</a>
                    </div>
                    <?php endif;?>
                </div>
            </nav>
        </div>
    </header>
    

    <!-- Pet List Section -->
    <h2 class="pet-list-heading">Pets Available for Adoption</h2>
    <div class="pet-list">
        <?php foreach ($pets as $pet): ?>
            <?php if($pet['status'] !== 'adopted'): ?>
            <div class="pet-card">
                <a href="pet-detail.php?pet_id=<?= $pet['pets_id']?>" class="pet-link">
                    <img src="../admin/<?= htmlspecialchars($pet['image_path']) ?>"
                        alt="<?= htmlspecialchars($pet['name']) ?>" class="pet-image">
                    <div class="pet-details">
                        <div class="pet-gender"><?= strtoupper(substr($pet['gender'] ?? 'M', 0, 1)) ?></div>
                        <h3 class="pet-name"><?= htmlspecialchars($pet['name']) ?></h3>

                        <div class="pet-stats">
                            <div class="pet-stat">
                                <span class="pet-stat-value"><?= htmlspecialchars($pet['weight']) ?>kg</span>
                            </div>
                            <div class="pet-stat">
                                <span class="pet-stat-value"><?= htmlspecialchars($pet['age']) ?>yrs</span>
                            </div>
                        </div>

                        <p class="pet-description">
                            <?= nl2br(htmlspecialchars($pet['description'])) ?>
                        </p>
                    </div>
                </a>
                <form action="adopt-form.php" method="GET" class="p-3">
                    <input type="hidden" name="pet_id" value="<?= $pet['pets_id']?>">
                    <button type="submit" class="adopt-btn">
                        <i class="fas fa-heart"></i> Adopt Me
                    </button>
                </form>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>
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

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>