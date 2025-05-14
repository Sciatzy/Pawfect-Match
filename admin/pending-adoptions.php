<?php
require '../includes/db.php';
session_start();

// Get all pending adoptions with pet and adopter details
$query = "SELECT a.*, p.name as pet_name, p.image_path, p.gender, p.age, p.weight, p.description,
          u.*
          FROM adopters a 
          INNER JOIN pets p ON a.pet_id = p.pets_id 
          INNER JOIN users u ON a.user_id = u.ID 
          WHERE a.status = 'pending' 
          ORDER BY a.application_date DESC";
$pending_adoptions = $pdo->query($query)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pawfect Match - Pending Adoptions</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="pet-list.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .adoption-list {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .adoption-header {
            background: #fff5ed;
            padding: 20px 30px;
            border-bottom: 1px solid #ffe0cc;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .adoption-header h2 {
            color: #333;
            margin: 0;
            font-size: 1.5rem;
        }

        .adoption-count {
            background: #ff914d;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
        }

        .adoption-card {
            border-bottom: 1px solid #eee;
            padding: 25px 30px;
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 30px;
            transition: background-color 0.3s ease;
        }

        .adoption-card:hover {
            background-color: #fafafa;
        }

        .adoption-card:last-child {
            border-bottom: none;
        }

        .pet-image-container {
            position: relative;
        }

        .pet-image {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .pet-status {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(255, 145, 77, 0.9);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .adoption-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .detail-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
        }

        .detail-section h3 {
            color: #ff914d;
            margin: 0 0 15px 0;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .detail-section h3 i {
            font-size: 1rem;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .detail-label {
            font-size: 0.85rem;
            color: #666;
            font-weight: 600;
        }

        .detail-value {
            font-size: 1rem;
            color: #333;
        }

        .adoption-actions {
            grid-column: 1 / -1;
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .btn.approve {
            background-color: #4CAF50;
            color: white;
        }

        .btn.approve:hover {
            background-color: #45a049;
        }

        .btn.reject {
            background-color: #f44336;
            color: white;
        }

        .btn.reject:hover {
            background-color: #da190b;
        }

        .no-adoptions {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .no-adoptions i {
            font-size: 3rem;
            color: #ff914d;
            margin-bottom: 20px;
        }

        .no-adoptions h3 {
            color: #333;
            margin-bottom: 10px;
        }

        .no-adoptions p {
            color: #666;
        }

        @media (max-width: 1200px) {
            .adoption-card {
                grid-template-columns: 150px 1fr;
            }

            .pet-image {
                width: 150px;
                height: 150px;
            }
        }

        @media (max-width: 900px) {
            .adoption-details {
                grid-template-columns: 1fr;
            }

            .adoption-card {
                grid-template-columns: 1fr;
            }

            .pet-image-container {
                display: flex;
                justify-content: center;
            }
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
            <div class="menu-item">
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
            <div class="menu-item active">
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
        <div class="adoption-list">
            <div class="adoption-header">
                <h2>Pending Adoption Applications</h2>
                <div class="adoption-count">
                    <?= count($pending_adoptions) ?> Pending
                </div>
            </div>
            
            <?php if (empty($pending_adoptions)): ?>
                <div class="no-adoptions">
                    <i class="fas fa-clipboard-list"></i>
                    <h3>No Pending Adoptions</h3>
                    <p>There are no adoption applications waiting for review.</p>
                </div>
            <?php else: ?>
                <?php foreach ($pending_adoptions as $adoption): ?>
                    <div class="adoption-card">
                        <div class="pet-image-container">
                            <img src="<?= htmlspecialchars($adoption['image_path']) ?>" 
                                 alt="<?= htmlspecialchars($adoption['pet_name']) ?>" 
                                 class="pet-image">
                            <div class="pet-status">Pending Review</div>
                        </div>
                        
                        <div class="adoption-details">
                            <div class="detail-section">
                                <h3><i class="fas fa-paw"></i> Pet Information</h3>
                                <div class="detail-grid">
                                    <div class="detail-item">
                                        <span class="detail-label">Name</span>
                                        <span class="detail-value"><?= htmlspecialchars($adoption['pet_name']) ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Gender</span>
                                        <span class="detail-value"><?= ucfirst(htmlspecialchars($adoption['gender'])) ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Age</span>
                                        <span class="detail-value"><?= htmlspecialchars($adoption['age']) ?> years</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Weight</span>
                                        <span class="detail-value"><?= htmlspecialchars($adoption['weight']) ?> kg</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="detail-section">
                                <h3><i class="fas fa-user"></i> Adopter Information</h3>
                                <div class="detail-grid">
                                    <div class="detail-item">
                                        <span class="detail-label">Full Name</span>
                                        <span class="detail-value"><?= htmlspecialchars($adoption['full_name']) ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Email</span>
                                        <span class="detail-value"><?= htmlspecialchars($adoption['email']) ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Phone</span>
                                        <span class="detail-value"><?= htmlspecialchars($adoption['phone']) ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Address</span>
                                        <span class="detail-value"><?= htmlspecialchars($adoption['address']) ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="detail-section">
                                <h3><i class="fas fa-file-alt"></i> Application Details</h3>
                                <div class="detail-grid">
                                    <div class="detail-item">
                                        <span class="detail-label">Application ID</span>
                                        <span class="detail-value">#<?= $adoption['adopter_id'] ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Submitted On</span>
                                        <span class="detail-value"><?= date('F j, Y', strtotime($adoption['application_date'])) ?></span>
                                    </div>
                                    <div class="detail-item" style="grid-column: 1 / -1;">
                                        <span class="detail-label">Reason for Adoption</span>
                                        <span class="detail-value"><?= nl2br(htmlspecialchars($adoption['comments'])) ?></span>
                                    </div>
                                    <div class="detail-item" style="grid-column: 1 / -1;">
                                        <span class="detail-label">Previous Experience</span>
                                        <span class="detail-value"><?= nl2br(htmlspecialchars($adoption['pet_experience'])) ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="adoption-actions">
                                <form method="POST" action="process-adoption.php" style="display: inline;">
                                    <input type="hidden" name="adoption_id" value="<?= $adoption['adopter_id'] ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn approve">
                                        <i class="fas fa-check"></i> Approve Application
                                    </button>
                                </form>
                                
                                <form method="POST" action="process-adoption.php" style="display: inline;">
                                    <input type="hidden" name="adoption_id" value="<?= $adoption['adopter_id'] ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="btn reject">
                                        <i class="fas fa-times"></i> Reject Application
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 