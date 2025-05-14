<?php
require '../includes/db.php';
session_start();

$strays = $pdo->query("SELECT * FROM strays ORDER BY urgency DESC, created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<style>
        :root {
            --urgent: #ff6b6b;
            --high: #ffa502;
            --medium: #feca57;
            --low: #1dd1a1;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
        }
        
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #eee;
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
            height: 250px;
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
    </style>
</head>
<body>
    <header>
        
        <h1>Stray Animal Alerts</h1>
        <?php if (isset($_SESSION['ID'])): ?>
            <a href="report-stray.php" class="report-btn">Report a Stray</a>
        <?php else: ?>
            <a href="login/login-report.php" class="report-btn">Login to Report</a>
        <?php endif; ?>
    </header>
    
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
                        
                        <a href="contact.php?stray_id=<?= $stray['stray_id'] ?>" class="contact-btn">
                            Help This Animal
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>