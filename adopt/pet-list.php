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

    .pet-list {
        padding: 40px 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    @media (max-width: 768px) {
        .pet-list {
            padding: 20px 15px;
        }
    }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="site-header">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light py-3">
                <a href="../login/index.php" class="site-logo navbar-brand">🐾 Pawfect Match</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto nav-menu">
                        <li class="nav-item"><a class="nav-link" href="../login/index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link active" href="pet-list.php">Adopt</a></li>
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
                    </div>
                    <?php endif;?>
                </div>
            </nav>
        </div>
    </header>

    <!-- Pet List Section -->
    <div class="pet-list">
        <?php foreach ($pets as $pet): ?>
            <?php if($pet['status'] !== 'adopted'): ?>
            <div class="pet-card">
                <div class="pet-link">
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
                    <form action="adopt-form.php" method="POST">
                        <input type="hidden" name="pet_id" value="<?= $pet['pets_id']?>">
                        <button type="submit" class="adopt-btn">
                            <i class="fas fa-heart"></i> Adopt Me
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>