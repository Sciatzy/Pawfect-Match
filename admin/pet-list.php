<?php
require '../includes/db.php';
session_start();
$pets = $pdo->query("SELECT * FROM pets ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pawfect Match - Pet Adoption Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="pet-list.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .pet-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            padding: 0 15px 15px;
        }

        .btn {
            padding: 8px 15px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn.edit {
            background-color: #4CAF50;
            color: white;
        }

        .btn.edit:hover {
            background-color: #45a049;
        }

        .btn.delete {
            background-color: #f44336;
            color: white;
        }

        .btn.delete:hover {
            background-color: #da190b;
        }

        .add-pet-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #ff914d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .add-pet-btn:hover {
            background-color: #e87e3c;
        }

        .pet-card {
            position: relative;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <span class="paw-icon">🐾</span>
            Pawfect Match
        </div>
        
        <a href="dashboard.php">
            <div class="menu-item">
                <span class="menu-icon">👤</span>
                Dashboard
            </div>
        </a>
        
        <a href="pet-list.php">
            <div class="menu-item active">
                <span class="menu-icon">🐶</span>
                Pets Listed
            </div>
        </a>
        
        <a href="adopted-pets.php">
            <div class="menu-item">
                <span class="menu-icon">🏠</span>
                Adopted Pets
            </div>
        </a>
        
        <a href="pending-adoptions.php">
            <div class="menu-item">
                <span class="menu-icon">📋</span>
                Pending Adoptions
            </div>
        </a>
        
        <div class="menu-item">
            <span class="menu-icon">💰</span>
            Donations Received
        </div>
        
        <a href="../login/logout.php" class="logout">
            <span class="logout-icon">↩️</span>
            Logout
        </a>
    </div>
    
    <div class="main-content">
        <h1>Pawfect Match</h1>
        
        <?php if (isset($_SESSION['ID'])): ?>
            <a href="post-pet.php" class="add-pet-btn">
                <i class="fas fa-plus"></i> Add New Pet
            </a>
        <?php endif; ?>
        
        <div class="pet-list">
            <?php foreach ($pets as $pet): ?>
                <?php if($pet['status'] !== 'adopted'): ?>
                    <div class="pet-card">
                    <a href="pet-detail.php?pets_id=<?= $pet['pets_id'] ?>" class="pet-link">
                        <img src="<?= htmlspecialchars($pet['image_path']) ?>" 
                            alt="<?= htmlspecialchars($pet['name']) ?>" 
                            class="pet-image">
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
                    
                    <?php if (isset($_COOKIE['token'])): ?>
                        <div class="pet-buttons">
                            <a href="edit-pet.php?pets_id=<?= $pet['pets_id'] ?>" class="btn edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form method="POST" action="delete-pet.php" onsubmit="return confirm('Are you sure you want to delete this pet?');" style="margin: 0;">
                                <input type="hidden" name="pets_id" value="<?= $pet['pets_id'] ?>">
                                <button type="submit" class="btn delete">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>