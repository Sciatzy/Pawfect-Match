<?php
session_start();
require '../includes/db.php';

// Get total pets count
$stmt = $pdo->query("SELECT COUNT(*) FROM pets");
$totalPets = $stmt->fetchColumn();

// Get adopted pets count
$stmt = $pdo->query("SELECT COUNT(*) FROM adopters WHERE status = 'approved'");
$adoptedPets = $stmt->fetchColumn();

// Get stray reports count
$stmt = $pdo->query("SELECT COUNT(*) FROM strays");
$strayReports = $stmt->fetchColumn();

// Get rescued strays count
$stmt = $pdo->query("SELECT COUNT(*) FROM strays WHERE status = 'rescued'");
$rescuedStrays = $stmt->fetchColumn();

// Get all users
$users = $pdo->query("SELECT firstname, lastname, email, role FROM users ORDER BY ID DESC")->fetchAll();

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pawfect Match - Pet Adoption Dashboard</title>
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
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
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
        
        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .stats-container {
            display: flex;
            gap: 20px;
            margin-bottom: 40px;
            width: 100%;
        }
        
        .stat-card {
            flex: 1;
            padding: 25px;
            border-radius: 12px;
            min-width: 0;
        }
        
        .stat-title {
            font-size: 18px;
            color: #333;
            margin-bottom: 15px;
        }
        
        .stat-value {
            font-size: 36px;
            font-weight: bold;
            color: #333;
        }
        
        .card-sales {
            background-color: #fff2ea;
        }
        
        .card-orders {
            background-color: #fff9e6;
        }
        
        .card-customers {
            background-color: #ffe6e6;
        }
        
        .generate-report-btn {
            background-color: #ff914d;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.3s;
            text-decoration: none;
        }
        
        .generate-report-btn:hover {
            background-color: #e67e3d;
        }
        
        .generate-report-btn i {
            font-size: 20px;
        }
        
        .report-section {
            margin-bottom: 40px;
        }
        
        .report-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
        }
        
        .chart-container {
            background-color: white;
            border-radius: 12px;
            padding: 20px;
            height: 300px;
        }
        
        .main-content {
            margin-left: 320px;
            padding: 40px;
            min-height: 100vh;
            width: calc(100% - 320px);
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
        
        <a href="dashboard.php" class="menu-item<?= $current_page == 'dashboard.php' ? ' active' : '' ?>"><i class="fas fa-user menu-icon"></i>Dashboard</a>
        <a href="pet-list.php" class="menu-item<?= $current_page == 'pet-list.php' ? ' active' : '' ?>"><i class="fas fa-dog menu-icon"></i>Pets Listed</a>
        <a href="adopted-pets.php" class="menu-item<?= $current_page == 'adopted-pets.php' ? ' active' : '' ?>"><i class="fas fa-home menu-icon"></i>Adopted Pets</a>
        <a href="pending-adoptions.php" class="menu-item<?= $current_page == 'pending-adoptions.php' ? ' active' : '' ?>"><i class="fas fa-clipboard-list menu-icon"></i>Pending Adoptions</a>
        <a href="stray-reports.php" class="menu-item<?= $current_page == 'stray-reports.php' ? ' active' : '' ?>"><i class="fas fa-exclamation-triangle menu-icon"></i>Stray Reports</a>
        <a href="strays-rescued.php" class="menu-item<?= $current_page == 'strays-rescued.php' ? ' active' : '' ?>"><i class="fas fa-check-square menu-icon"></i>Rescued Strays</a>
        
        <a href="../login/logout.php" class="logout"><i class="fas fa-sign-out-alt logout-icon"></i>Logout</a>
    </div>
    
    <div class="main-content">
        <div class="header">
            <div class="welcome">Welcome Back!</div>
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
        
        <div class="stats-container">
            <div class="stat-card card-sales">
                <div class="stat-title">Total Pets</div>
                <div class="stat-value"><?php echo $totalPets; ?></div>
            </div>
            
            <div class="stat-card card-orders">
                <div class="stat-title">Adopted Pets</div>
                <div class="stat-value"><?php echo $adoptedPets; ?></div>
            </div>
            
            <div class="stat-card card-customers">
                <div class="stat-title">Stray Reports</div>
                <div class="stat-value"><?php echo $strayReports; ?></div>
            </div>

            <div class="stat-card" style="background-color: #e6ffe6;">
                <div class="stat-title">Rescued Strays</div>
                <div class="stat-value"><?php echo $rescuedStrays; ?></div>
            </div>
        </div>
        
        <div style="display: flex; justify-content: flex-end; margin-bottom: 40px;">
            <a href="generate-report.php" class="generate-report-btn" style="width: auto; min-width: 200px; text-align: center; padding: 12px 30px; display: inline-flex; align-items: center; justify-content: center; gap: 10px;">
                <i class="fas fa-file-pdf"></i>
                Generate Report
            </a>
        </div>
        
        <div class="report-section">
            <div class="report-title">Registered Users</div>
            <div class="chart-container" style="padding:0; background:none; box-shadow:none;">
                <table style="width:100%; border-collapse:collapse; background:white; border-radius:10px; overflow:hidden;">
                    <thead style="background:#fff2ea;">
                        <tr>
                            <th style="padding:12px; text-align:left;">Name</th>
                            <th style="padding:12px; text-align:left;">Email</th>
                            <th style="padding:12px; text-align:left;">Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr style="border-bottom:1px solid #f0f0f0;">
                            <td style="padding:12px;"><?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?></td>
                            <td style="padding:12px;"><?= htmlspecialchars($user['email']) ?></td>
                            <td style="padding:12px; text-transform:capitalize;"><?= htmlspecialchars($user['role']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('salesChart').getContext('2d');
            
            const salesChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                    datasets: [{
                        label: 'Donations (₱)',
                        data: [5700, 7200, 7800, 6400, 8100, 10500, 8800],
                        backgroundColor: 'rgba(238, 119, 33, 0.1)',
                        borderColor: '#ee7721',
                        borderWidth: 3,
                        tension: 0.3,
                        pointRadius: 5,
                        pointBackgroundColor: '#ee7721'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f5f5f5'
                            },
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            align: 'end'
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>