<?php
session_start();
require '../includes/db.php';

// Get all adopted pets with adopter details
$query = "
SELECT 
    ad.adoption_id,
    ad.adoption_date,
    ad.app_status,
    a.adopter_id,
    a.housing_type,
    a.yard_size,
    a.pet_experience,
    a.hours_alone,
    a.comments,
    p.pets_id,
    p.name as pet_name,
    p.image_path,
    p.gender,
    p.age,
    p.weight,
    p.description,
    u.firstname,
    u.lastname,
    u.email,
    a.phone,
    a.address
FROM adoptions ad
INNER JOIN adopters a ON ad.adopter_id = a.adopter_id
INNER JOIN pets p ON ad.pet_id = p.pets_id
INNER JOIN users u ON a.user_id = u.ID
WHERE ad.app_status = 'approved'
ORDER BY ad.adoption_date DESC
";
$adopted_pets = $pdo->query($query)->fetchAll();


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adopted Pets - Pawfect Match</title>
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
            background: rgba(76, 175, 80, 0.9);
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
            <a href="../login/index.php" class="site-logo navbar-brand">
                <img src="../images/logo.png" alt="Pawfect Match Logo">
            Pawfect Match
            </a>
        </div>
        
        <a href="dashboard.php" class="menu-item"><i class="fas fa-user menu-icon"></i>Dashboard</a>
        <a href="pet-list.php" class="menu-item"><i class="fas fa-dog menu-icon"></i>Pets Listed</a>
        <a href="adopted-pets.php" class="menu-item active  "><i class="fas fa-home menu-icon"></i>Adopted Pets</a>
        <a href="pending-adoptions.php" class="menu-item"><i class="fas fa-clipboard-list menu-icon"></i>Pending Adoptions</a>
        <a href="stray-reports.php" class="menu-item"><i class="fas fa-exclamation-triangle menu-icon"></i>Stray Reports</a>
        <a href="strays-rescued.php" class="menu-item"><i class="fas fa-check-square menu-icon"></i>Rescued Strays</a>
        
        <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt logout-icon"></i>Logout</a>
    </div>
    
    <div class="main-content">
        <div class="adoption-list">
            <div class="adoption-header">
                <h2>Adopted Pets</h2>
                <div style="display: flex; gap: 15px; align-items: center;">
                    <a href="generate-report.php" class="generate-report-btn" style="display: inline-flex; align-items: center; gap: 8px; background: #ff914d; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: 500;">
                        <i class="fas fa-file-pdf"></i>
                        Generate Report
                    </a>
                    <div class="adoption-count">
                        <?= count($adopted_pets) ?> Adopted
                    </div>
                </div>
            </div>
            
            <?php if (empty($adopted_pets)): ?>
                <div class="no-adoptions">
                    <i class="fas fa-home"></i>
                    <h3>No Adopted Pets Yet</h3>
                    <p>Check back soon to see our success stories!</p>
                </div>
            <?php else: ?>
                <?php foreach ($adopted_pets as $pet): ?>
                    <div class="adoption-card">
                        <div class="pet-image-container">
                            <img src="<?= htmlspecialchars($pet['image_path']) ?>" 
                                 alt="<?= htmlspecialchars($pet['pet_name']) ?>" 
                                 class="pet-image">
                            <div class="pet-status">Adopted</div>
                        </div>
                        
                        <div class="adoption-details">
                            <div class="detail-section">
                                <h3><i class="fas fa-paw"></i> Pet Information</h3>
                                <div class="detail-grid">
                                    <div class="detail-item">
                                        <span class="detail-label">Name</span>
                                        <span class="detail-value"><?= htmlspecialchars($pet['pet_name']) ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Gender</span>
                                        <span class="detail-value"><?= ucfirst(htmlspecialchars($pet['gender'])) ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Age</span>
                                        <span class="detail-value"><?= htmlspecialchars($pet['age']) ?> years</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Weight</span>
                                        <span class="detail-value"><?= htmlspecialchars($pet['weight']) ?> kg</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="detail-section">
                                <h3><i class="fas fa-user"></i> Adopter Information</h3>
                                <div class="detail-grid">
                                    <div class="detail-item">
                                        <span class="detail-label">Full Name</span>
                                        <span class="detail-value"><?= htmlspecialchars($pet['firstname'] . ' ' . $pet['lastname']) ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Email</span>
                                        <span class="detail-value"><?= htmlspecialchars($pet['email']) ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Phone</span>
                                        <span class="detail-value"><?= htmlspecialchars($pet['phone']) ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Address</span>
                                        <span class="detail-value"><?= htmlspecialchars($pet['address']) ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="detail-section">
                                <h3><i class="fas fa-file-alt"></i> Adoption Details</h3>
                                <div class="detail-grid">
                                    <div class="detail-item">
                                        <span class="detail-label">Application ID</span>
                                        <span class="detail-value">#<?= $pet['adopter_id'] ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Adoption Date</span>
                                        <span class="detail-value"><?= date('M d, Y h:i A', strtotime($pet['adoption_date'])) ?></span>
                                    </div>
                                    <div class="detail-item" style="grid-column: 1 / -1;">
                                        <span class="detail-label">Reason for Adoption</span>
                                        <span class="detail-value"><?= nl2br(htmlspecialchars($pet['comments'])) ?></span>
                                    </div>
                                    <div class="detail-item" style="grid-column: 1 / -1;">
                                        <span class="detail-label">Experience</span>
                                        <span class="detail-value"><?= nl2br(htmlspecialchars($pet['pet_experience'])) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>