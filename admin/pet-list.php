<?php
session_start();
require '../includes/db.php';
$pets = $pdo->query("SELECT * FROM pets WHERE status != 'adopted' ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet List - Pawfect Match</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            display: flex;
            min-height: 100vh;
            background-color: #f9f9f9;
        }

        .sidebar {
            width: 320px;
            background-color: white;
            padding: 20px;
            border-right: 1px solid #e1e1e1;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .logo {
            display: flex;
            align-items: center;
            margin-bottom: 40px;
            color: #ee7721;
            font-size: 24px;
            font-weight: bold;
        }

        .paw-icon {
            color: #ee7721;
            font-size: 24px;
            margin-right: 10px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 15px;
            margin-bottom: 5px;
            border-radius: 10px;
            cursor: pointer;
            color: #333;
            font-weight: 500;
            text-decoration: none;
        }

        .menu-item.active {
            background-color: #fff2ea;
            color: #ee7721;
        }

        .menu-item:hover:not(.active) {
            background-color: #f5f5f5;
        }

        .menu-icon {
            margin-right: 15px;
            width: 24px;
            text-align: center;
        }

        .logout {
            margin-top: auto;
            display: flex;
            align-items: center;
            padding: 15px;
            color: #333;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
        }

        .logout:hover {
            color: #ee7721;
        }

        .logout-icon {
            margin-right: 15px;
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

        .main-content {
            margin-left: 320px;
            padding: 40px;
            min-height: 100vh;
            width: calc(100% - 320px);
        }

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
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .add-pet-btn:hover {
            background-color: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .add-pet-btn i {
            font-size: 1.1rem;
        }

        .pet-list {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 32px;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        @media (max-width: 1100px) {
            .pet-list {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 700px) {
            .pet-list {
                grid-template-columns: 1fr;
            }
        }

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
            height: 600px;
        }

        .pet-image-container {
            position: relative;
            width: 100%;
            /* max-height: 350px; */
            /* aspect-ratio: 16/9; */
            background: #f5f5f5;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pet-image {
            width: 100%;
            height: 320px;
            object-fit: cover;
        }

        .pet-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .pet-status {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(255, 145, 77, 0.95);
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            z-index: 2;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            pointer-events: none;
        }

        .pet-details {
            flex: 1 1 auto;
            overflow: hidden;
            padding: 1rem;
            display: flex;
            flex-direction: column;
        }

        .pet-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.5rem;
        }

        .pet-name {
            font-size: 1.1rem;
            color: #333;
            font-weight: 600;
            margin: 0;
        }

        .pet-id {
            font-size: 0.7rem;
            color: #666;
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .pet-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .info-label {
            font-size: 0.7rem;
        }
        .info-value {
            font-size: 0.9rem;
        }

        .pet-description {
            flex: 1 1 auto;
            overflow: hidden;
            font-size: 0.8rem;
            margin-bottom: 0.5rem;
        }

        .pet-buttons {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            padding: 0.75rem 0 1rem 0;
            width: 100%;
            margin-top: auto;
        }

        .btn {
            font-size: 0.85rem;
            padding: 7px 18px;
            border-radius: 8px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            padding: 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .welcome {
            font-size: 36px;
            font-weight: bold;
            color: #333;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fff2ea;
            padding: 8px 15px;
            border-radius: 20px;
        }
        
        .user-name {
            font-weight: 500;
            color: #333;
        }
        
        .user-icon {
            color: #ff914d;
            font-size: 1.2rem;
        }

        .back-home {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #ff914d;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        
        .back-home:hover {
            background: #e67e3d;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <a href="../login/index.php" class="site-logo navbar-brand">
                <img src="../images/logo.png" alt="Pawfect Match Logo">
            Pawfect Match
            </a>
        </div>
        
        <a href="dashboard.php" class="menu-item"><i class="fas fa-user menu-icon"></i>Dashboard</a>
        <a href="pet-list.php" class="menu-item active"><i class="fas fa-dog menu-icon"></i>Pets Listed</a>
        <a href="adopted-pets.php" class="menu-item"><i class="fas fa-home menu-icon"></i>Adopted Pets</a>
        <a href="pending-adoptions.php" class="menu-item"><i class="fas fa-clipboard-list menu-icon"></i>Pending Adoptions</a>
        <a href="stray-reports.php" class="menu-item"><i class="fas fa-exclamation-triangle menu-icon"></i>Stray Reports</a>
        <a href="strays-rescued.php" class="menu-item"><i class="fas fa-check-square menu-icon"></i>Rescued Strays</a>
        
        <a href="../login/logout.php" class="logout"><i class="fas fa-sign-out-alt logout-icon"></i>Logout</a>
    </div>
    
    <div class="main-content">
        <div class="header">
            <div class="welcome">Pet Management</div>
            <div style="display: flex; align-items: center; gap: 15px;">
                <a href="post-pet.php" class="add-pet-btn">
                    <i class="fas fa-plus"></i>
                    Add New Pet
                </a>
                <a href="../index.php" class="back-home">
                    <i class="fas fa-home"></i>
                    Back to Home
                </a>
                <div class="user-info">
                    <i class="fas fa-user-circle user-icon"></i>
                    <div class="user-name">Admin</div>
                </div>
            </div>
        </div>

        <div class="content-header">
            <h2>All Pets</h2>
            <div class="pet-count"><?= count($pets) ?> Pets Listed</div>
        </div>
        
        <div class="pet-list">
            <?php foreach ($pets as $pet): ?>
                <div class="pet-card">
                    <div class="pet-image-container">
                        <img src="<?= htmlspecialchars($pet['image_path']) ?>" 
                            alt="<?= htmlspecialchars($pet['name']) ?>" 
                            class="pet-image">
                        <div class="pet-status">Available</div>
                    </div>
                    <div class="pet-details">
                        <div class="pet-header">
                            <h3 class="pet-name"><?= htmlspecialchars($pet['name']) ?></h3>
                            <span class="pet-id">ID: #<?= $pet['pets_id'] ?></span>
                        </div>
                        
                        <div class="pet-info-grid">
                            <div class="info-item">
                                <span class="info-label">Gender</span>
                                <span class="info-value"><?= ucfirst(htmlspecialchars($pet['gender'])) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Age</span>
                                <span class="info-value"><?= htmlspecialchars($pet['age']) ?> years</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Weight</span>
                                <span class="info-value"><?= htmlspecialchars($pet['weight']) ?> kg</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Added On</span>
                                <span class="info-value"><?= date('M d, Y', strtotime($pet['created_at'])) ?></span>
                            </div>
                        </div>
                        
                        <p class="pet-description">
                            <?= nl2br(htmlspecialchars($pet['description'])) ?>
                        </p>
                    </div>
                    <div class="pet-buttons">
                        <a href="pet-detail.php?pets_id=<?= $pet['pets_id'] ?>" class="btn view">
                            <i class="fas fa-eye"></i> View
                        </a>
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
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>