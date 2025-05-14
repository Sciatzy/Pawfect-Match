<?php
require '../includes/db.php';

$pets_id = $_POST['pet_id'];
// Get the pet details
$stmt = $pdo->prepare("SELECT * FROM pets WHERE pets_id = ?");
$stmt->execute([$pets_id]);
$pet = $stmt->fetch();

// If pet not found, redirect back to list
if (!$pet) {
    header('Location: pet-list.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($pet['name']) ?> - Pawfect Match</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles/pet-detail.css">
</head>
<body>
    <div class="header">
        <a href="pet-list.php"><span class="back-arrow">←</span> Back to Pets</a>
        <img src="../images/logo.png" alt="Pawfect Match" class="header-logo">
    </div>
    
    <div class="pet-container">
        <div class="pet-card">
            <div class="pet-image-container">
            <img src="../admin/<?= htmlspecialchars($pet['image_path']) ?>" 
                     alt="<?= htmlspecialchars($pet['name']) ?>" 
                     class="pet-image">
            </div>
            <div class="pet-details">
                <img src="../images/logo.png" alt="Pawfect Match" class="pet-logo">
                
                <div class="pet-stats">
                    <div class="stat-circle weight-stat">
                        <?= htmlspecialchars($pet['weight']) ?><small>kg</small>
                    </div>
                    <div class="stat-circle age-stat">
                        <?= htmlspecialchars($pet['age']) ?><small>y/o</small>
                    </div>
                    <div class="stat-circle size-stat">
                        <?= strtoupper(substr($pet['size'] ?? 'M', 0, 1)) ?>
                    </div>
                </div>
                
                <p class="pet-description">
                    <?= htmlspecialchars($pet['name']) ?> is neutered and full of charm—always ready with a wagging tail and a playful nudge.
                </p>
                
                <p class="pet-description">
                    <?= htmlspecialchars($pet['description']) ?>
                </p>
                
                <div class="pet-additional-info">
                    <div class="info-item"><strong>Gender:</strong> <?= ucfirst(htmlspecialchars($pet['gender'])) ?></div>
                </div>
                
                <h2 class="pet-name"><?= htmlspecialchars($pet['name']) ?></h2>
                
                <a href="adopt-form.php"><button type="button" class="apply-button">Apply to Adopt</button></a>
                
                <div class="adopt-me">
                    <br>
                </div>
                
                <svg class="yellow-circles" viewBox="0 0 180 180" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="30" cy="150" r="60" fill="#ffeb3b" opacity="0.3" />
                    <circle cx="90" cy="120" r="30" fill="#ffeb3b" opacity="0.5" />
                    <circle cx="15" cy="90" r="22" fill="#ffeb3b" opacity="0.7" />
                </svg>
            </div>
        </div>
    </div>
</body>
</html>