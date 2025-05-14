<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pawfect Match - Pet Adoption Dashboard</title>
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
        
        .main-content {
            flex: 1;
            padding: 40px;
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
        }
        
        .user-name {
            margin-right: 15px;
            font-weight: 500;
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
        }
        
        .stat-card {
            flex: 1;
            padding: 25px;
            border-radius: 12px;
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
        
        .action-button {
            background-color: #ee7721;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 15px 30px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-bottom: 40px;
        }
        
        .action-button:hover {
            background-color: #e06612;
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
        
        .logout {
            margin-top: auto;
            display: flex;
            align-items: center;
            padding: 15px;
            color: #333;
            cursor: pointer;
            font-weight: 500;
        }
        
        .logout:hover {
            color: #ee7721;
        }
        
        .logout-icon {
            margin-right: 15px;
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
            <div class="menu-item active">
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
            <div class="menu-item">
                <span class="menu-icon">📋</span>
                Pending Adoptions
            </div>
        </a>
        
        <div class="menu-item">
            <span class="menu-icon">💰</span>
            Donations Received
        </div>
        
        <div class="logout">
            <span class="logout-icon">↩️</span>
            Logout
        </div>
    </div>
    
    <div class="main-content">
        <div class="header">
            <div class="welcome">Welcome Back!</div>
            <div class="user-info">
                <div class="user-name">Waffieta Buena-Obra</div>
                <img class="user-avatar" src="/api/placeholder/45/45" alt="User Avatar">
            </div>
        </div>
        
        <div class="stats-container">
            <div class="stat-card card-sales">
                <div class="stat-title">Total Donations</div>
                <div class="stat-value">₱57,580</div>
            </div>
            
            <div class="stat-card card-orders">
                <div class="stat-title">Total Adoptions</div>
                <div class="stat-value">120</div>
            </div>
            
            <div class="stat-card card-customers">
                <div class="stat-title">Total Rescues</div>
                <div class="stat-value">85</div>
            </div>
        </div>
        
        <button class="action-button">View Reports</button>
        
        <div class="report-section">
            <div class="report-title">Donation Report</div>
            <div class="chart-container">
                <canvas id="salesChart"></canvas>
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
            
            // Add click event listeners to menu items
            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach(item => {
                item.addEventListener('click', function() {
                    menuItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                });
            });
            
            // Add click event listener to the action button
            const actionButton = document.querySelector('.action-button');
            actionButton.addEventListener('click', function() {
                alert('Redirecting to detailed reports...');
            });
        });
    </script>
</body>
</html>