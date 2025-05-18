<?php
require '../includes/db.php';

// Make sure we have an ID parameter
if (!isset($_GET['pet_id']) || !is_numeric($_GET['pet_id'])) {
    header('Location: pet-list.php');
    exit;
}

$pets_id = (int)$_GET['pet_id'];

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
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .header {
            background-color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header a {
            color: black;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .header a:hover {
            text-decoration: underline;
        }
        .back-arrow {
            margin-right: 8px;
            font-size: 20px;
        }
        .header-logo {
            height: 30px;
            width: auto;
        }
        .pet-container {
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            box-sizing: border-box;
        }
        .pet-card {
            width: 100%;
            height: calc(100vh - 100px);
            min-height: 600px;
            max-height: 800px;
            background-color: white;
            border-radius: 15px;
            overflow: hidden;
            display: flex;
            position: relative;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .pet-image-container {
            width: 50%;
            overflow: hidden;
            background-color: #f0f0f0;
        }
        .pet-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .pet-details {
            width: 50%;
            padding: 40px;
            position: relative;
            display: flex;
            flex-direction: column;
        }
        .pet-logo {
            height: 75px;
            width: 75px;
            margin-bottom: 30px;
        }
        .pet-name {
            font-size: 64px;
            font-weight: 900;
            margin: 20px 0;
            letter-spacing: -1px;
            text-transform: uppercase;
        }
        .pet-description {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 20px;
            max-width: 80%;
        }
        .pet-stats {
            position: absolute;
            right: 40px;
            top: 40px;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }
        .stat-circle {
            background-color: #ffeb3b;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .weight-stat {
            width: 80px;
            height: 80px;
            font-size: 22px;
        }
        .age-stat {
            width: 65px;
            height: 65px;
            font-size: 18px;
        }
        .size-stat {
            width: 90px;
            height: 90px;
            font-size: 32px;
            margin-bottom: 0;
        }
        .apply-button {
            margin-top: 30px;
            padding: 18px 40px;
            background-color: #ff914d;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.2rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s, transform 0.2s;
            box-shadow: 0 2px 8px rgba(255,145,77,0.15);
            align-self: flex-start;
        }
        .apply-button:hover {
            background-color: #e87e3c;
            transform: translateY(-2px);
        }
        .yellow-circles {
            position: absolute;
            left: 0;
            bottom: 0;
            width: 180px;
            height: 180px;
            z-index: 0;
        }
        .pet-additional-info {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
            margin-bottom: 40px;
        }
        .info-item {
            background-color: #f5f5f5;
            padding: 12px 20px;
            border-radius: 30px;
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        .info-item strong {
            margin-right: 8px;
        }
        @media (max-width: 900px) {
            .pet-card {
                flex-direction: column;
                height: auto;
            }
            .pet-image-container, .pet-details {
                width: 100%;
            }
            .pet-image-container {
                height: 300px;
            }
            .pet-stats {
                position: static;
                flex-direction: row;
                justify-content: flex-end;
                margin-bottom: 20px;
                gap: 10px;
            }
            .stat-circle {
                margin-bottom: 0;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="pet-list.php">
            <span class="back-arrow">←</span>
            Back to List
        </a>
        <a href="../login/index.php">
            <img src="../images/logo.png" alt="Pawfect Match Logo" class="header-logo">
            Pawfect Match
        </a>
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
                    <?= htmlspecialchars($pet['name']) ?> 
                </p>
                <p class="pet-description">
                    <?= htmlspecialchars($pet['description']) ?>
                </p>
                <div class="pet-additional-info">
                    <div class="info-item"><strong>Gender:</strong> <?= ucfirst(htmlspecialchars($pet['gender'])) ?></div>
                </div>
                <h2 class="pet-name"><?= htmlspecialchars($pet['name']) ?></h2>
                <a href="adopt-form.php?pet_id=<?= $pets_id ?>"><button type="button" class="apply-button">Apply to Adopt</button></a>
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