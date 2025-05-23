<?php
require_once '../config.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Transit Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f6fa;
            display: flex;
            height: 100vh;
            color: #333;
        }

        .sidebar {
            width: 260px;
            background-color: #2a3042;
            display: flex;
            flex-direction: column;
            color: #a6b0cf;
            position: relative;
            transition: all 0.3s ease;
            z-index: 10;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header h2 {
            color: #fff;
            font-size: 22px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .sidebar-header h2 i {
            color: #556ee6;
            margin-right: 10px;
            font-size: 24px;
        }

        .sidebar-menu {
            padding: 20px 0;
            flex: 1;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar li {
            margin: 8px 0;
        }

        .sidebar li a {
            text-decoration: none;
            color: #a6b0cf;
            padding: 12px 25px;
            display: flex;
            align-items: center;
            font-size: 15px;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .sidebar li a i {
            margin-right: 10px;
            font-size: 18px;
            width: 24px;
            text-align: center;
        }

        .sidebar li a:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.05);
        }

        .sidebar li a.active {
            color: #fff;
            background-color: rgba(85, 110, 230, 0.25);
            border-left: 3px solid #556ee6;
        }

        .main-content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background-color: #f5f6fa;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e9ecef;
        }

        .header h1 {
            font-size: 24px;
            font-weight: 600;
            color: #495057;
        }

        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.03);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .card-title {
            font-size: 16px;
            font-weight: 500;
            color: #495057;
        }

        .card-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .card-icon.bus {
            background-color: rgba(85, 110, 230, 0.1);
            color: #556ee6;
        }

        .card-icon.station {
            background-color: rgba(52, 195, 143, 0.1);
            color: #34c38f;
        }

        .card-icon.route {
            background-color: rgba(241, 180, 76, 0.1);
            color: #f1b44c;
        }

        .card-value {
            font-size: 24px;
            font-weight: 600;
            color: #2a3042;
            margin-bottom: 5px;
        }

        .card-label {
            font-size: 14px;
            color: #74788d;
        }

        .recent-activity {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.03);
        }

        .activity-list {
            list-style: none;
        }

        .activity-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #f8f9fc;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: #556ee6;
        }

        .activity-details {
            flex: 1;
        }

        .activity-title {
            font-size: 14px;
            font-weight: 500;
            color: #495057;
            margin-bottom: 3px;
        }

        .activity-time {
            font-size: 12px;
            color: #74788d;
        }

        .logout-btn {
            color: #f46a6a;
            text-decoration: none;
            padding: 12px 25px;
            display: flex;
            align-items: center;
            font-size: 15px;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .logout-btn i {
            margin-right: 10px;
            font-size: 18px;
            width: 24px;
            text-align: center;
        }

        .logout-btn:hover {
            color: #fff;
            background-color: rgba(244, 106, 106, 0.1);
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-chart-line"></i> Transit Admin</h2>
        </div>
        <div class="sidebar-menu">
            <ul>
                <li><a href="admin.php" class="active"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="road.php"><i class="fas fa-road"></i> Roads</a></li>
                <li><a href="bus.php"><i class="fas fa-bus"></i> Buses</a></li>
                <li><a href="map.php"><i class="fas fa-map-marked-alt"></i> Map</a></li>
                <li><a href="database.php"><i class="fas fa-database"></i> Database</a></li>
            </ul>
        </div>
        <a href="logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Dashboard</h1>
        </div>

        <div class="dashboard-cards">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Total Buses</h3>
                    <div class="card-icon bus">
                        <i class="fas fa-bus"></i>
                    </div>
                </div>
                <div class="card-value">24</div>
                <div class="card-label">Active buses in service</div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Bus Stations</h3>
                    <div class="card-icon station">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                </div>
                <div class="card-value">18</div>
                <div class="card-label">Stations across the city</div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Routes</h3>
                    <div class="card-icon route">
                        <i class="fas fa-route"></i>
                    </div>
                </div>
                <div class="card-value">12</div>
                <div class="card-label">Active routes</div>
            </div>
        </div>

        <div class="recent-activity">
            <h3 class="card-title" style="margin-bottom: 20px;">Recent Activity</h3>
            <ul class="activity-list">
                <li class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-bus"></i>
                    </div>
                    <div class="activity-details">
                        <div class="activity-title">New bus added to route 101</div>
                        <div class="activity-time">2 hours ago</div>
                    </div>
                </li>
                <li class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="activity-details">
                        <div class="activity-title">Station maintenance completed</div>
                        <div class="activity-time">5 hours ago</div>
                    </div>
                </li>
                <li class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-route"></i>
                    </div>
                    <div class="activity-details">
                        <div class="activity-title">Route 203 schedule updated</div>
                        <div class="activity-time">1 day ago</div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</body>
</html> 