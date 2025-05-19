<?php
session_start();
require '../includes/db.php';

// Fetch all stray reports
$strays = $pdo->query("SELECT * FROM strays WHERE rescued_date IS NULL ORDER BY stray_id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stray Reports - Pawfect Match Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        html, body { height: 100%; margin: 0; padding: 0; }
        body { display: flex; min-height: 100vh; background: #f9f9f9; }
        .sidebar { width: 320px; background: white; padding: 20px 0 20px 0; border-right: 1px solid #e1e1e1; display: flex; flex-direction: column; position: fixed; left: 0; top: 0; height: 100vh; overflow-y: auto; box-sizing: border-box; }
        .logo { display: flex; align-items: center; margin-bottom: 40px; color: #ee7721; font-size: 24px; font-weight: bold; padding-left: 20px; }
        .site-logo { font-size: 1.5rem; font-weight: bold; color: #ff914d; text-decoration: none; display: flex; align-items: center; gap: 10px; }
        .site-logo img { height: 40px; width: auto; }
        .menu-item, .menu-item:visited { display: flex; align-items: center; padding: 15px 20px; margin-bottom: 5px; border-radius: 10px; cursor: pointer; color: #333; font-weight: 500; text-decoration: none; }
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
        .logout { margin-top: auto; display: flex; align-items: center; padding: 15px 20px; color: #333; cursor: pointer; font-weight: 500; text-decoration: none; }
        .logout:hover { color: #ee7721; }
        .logout-icon { margin-right: 15px; }
        .main-content { 
            margin-left: 320px;
             padding: 40px; min-height: 100vh;
              width: calc(100% - 320px);
               box-sizing: border-box;
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
        .welcome { font-size: 36px; font-weight: bold; color: #333; }
        .user-info { display: flex; align-items: center; gap: 10px; background: #fff2ea; padding: 8px 15px; border-radius: 20px; }
        .user-name { font-weight: 500; color: #333; }
        .user-icon { color: #ff914d; font-size: 1.2rem; }
        .stray-table { width: 100%; border-collapse: collapse; background: white; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.08); overflow: hidden; }
        .stray-table th, .stray-table td { padding: 16px 12px; text-align: left; }
        .stray-table th { background: #fff2ea; color: #ee7721; font-size: 1rem; }
        .stray-table tr:not(:last-child) { border-bottom: 1px solid #f0e0d6; }
        .stray-table td { font-size: 0.98rem; color: #333; }
        .status-badge { padding: 5px 14px; border-radius: 20px; font-size: 0.9rem; font-weight: 600; color: white; background: #ff914d; display: inline-block; }
        .status-badge.resolved { background: #4CAF50; }
        .action-btns { display: flex; gap: 10px; }
        .btn { padding: 7px 16px; border-radius: 6px; border: none; font-weight: 600; cursor: pointer; font-size: 0.95rem; display: flex; align-items: center; gap: 6px; }
        .btn.resolve { background: #4CAF50; color: white; }
        .btn.resolve:hover { background: #388e3c; }
        .btn.delete { background: #f44336; color: white; }
        .btn.delete:hover { background: #c62828; }
        .btn.view { background: #2196F3; color: white; }
        .btn.view:hover { background: #1769aa; }
        @media (max-width: 900px) { .main-content { padding: 20px; } .stray-table th, .stray-table td { padding: 10px 6px; } }
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
            color: white;
            text-decoration: none;
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
        <a href="adopted-pets.php" class="menu-item"><i class="fas fa-home menu-icon"></i>Adopted Pets</a>
        <a href="pending-adoptions.php" class="menu-item"><i class="fas fa-clipboard-list menu-icon"></i>Pending Adoptions</a>
        <a href="stray-reports.php" class="menu-item active"><i class="fas fa-exclamation-triangle menu-icon"></i>Stray Reports</a>
        <a href="strays-rescued.php" class="menu-item"><i class="fas fa-check-square menu-icon"></i>Rescued Strays</a>
        <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt logout-icon"></i>Logout</a>
    </div>
    <div class="main-content">
        <div class="header">
            <div class="welcome">Stray Reports</div>
            <div style="display: flex; align-items: center; gap: 15px;">
                <a href="../login/index.php" class="back-home">
                    <i class="fas fa-home"></i>
                    Back to Home
                </a>
                <div class="user-info">
                    <i class="fas fa-user-circle user-icon"></i>
                    <div class="user-name">Admin</div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Stray Reports</h2>
        </div>
        <table class="stray-table">
            <tr>
                <th>ID</th>
                <th>Type</th>
                <th>Location</th>
                <th>Description</th>
                <th>Status</th>
                <th>Date Reported</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($strays as $stray): ?>
            <?php $status = $stray['report_status'] ?? 'pending'; ?>
            <tr>
                <td><?= htmlspecialchars($stray['stray_id']) ?></td>
                <td><?= htmlspecialchars($stray['animal_type']) ?></td>
                <td><?= htmlspecialchars($stray['location']) ?></td>
                <td><?= htmlspecialchars($stray['description']) ?></td>
                <td><span class="status-badge<?= $status === 'resolved' ? ' resolved' : '' ?>"><?= htmlspecialchars(ucfirst($status)) ?></span></td>
                <td><?= date('M d, Y H:i', strtotime($stray['date_reported'] ?? $stray['created_at'] ?? '')) ?></td>
                <td>
                    <div class="action-btns">
                        <form method="POST" action="process-stray.php" style="display:inline;">
                            <input type="hidden" name="stray_id" value="<?= $stray['stray_id'] ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="btn delete" onclick="return confirm('Delete this report?');"><i class="fas fa-trash"></i>Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html> 