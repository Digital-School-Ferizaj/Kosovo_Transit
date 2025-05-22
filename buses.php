<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Dummy bus schedule data
$cityBusLines = [
    [
        'number' => '101',
        'route' => 'Pristina Center → University',
        'frequency' => 'Every 10 min',
        'stops' => ['Pristina Center', 'City Park', 'Student Center', 'University'],
        'schedule' => [
            ['time' => '06:00', 'platform' => 'A1'],
            ['time' => '06:10', 'platform' => 'A1'],
            ['time' => '06:20', 'platform' => 'A1'],
            ['time' => '06:30', 'platform' => 'A1']
        ]
    ],
    [
        'number' => '102',
        'route' => 'Train Station → Hospital',
        'frequency' => 'Every 15 min',
        'stops' => ['Train Station', 'Main Square', 'City Center', 'Hospital'],
        'schedule' => [
            ['time' => '06:15', 'platform' => 'B2'],
            ['time' => '06:30', 'platform' => 'B2'],
            ['time' => '06:45', 'platform' => 'B2'],
            ['time' => '07:00', 'platform' => 'B2']
        ]
    ],
    [
        'number' => '103',
        'route' => 'Sunny Hill → Shopping Mall',
        'frequency' => 'Every 20 min',
        'stops' => ['Sunny Hill', 'Sports Center', 'Business District', 'Shopping Mall'],
        'schedule' => [
            ['time' => '06:20', 'platform' => 'C3'],
            ['time' => '06:40', 'platform' => 'C3'],
            ['time' => '07:00', 'platform' => 'C3'],
            ['time' => '07:20', 'platform' => 'C3']
        ]
    ]
];

$intercityBusLines = [
    [
        'number' => '201',
        'route' => 'Pristina → Prizren',
        'frequency' => 'Every 60 min',
        'stops' => ['Pristina', 'Ferizaj', 'Suhareka', 'Prizren'],
        'schedule' => [
            ['time' => '06:00', 'platform' => 'D1'],
            ['time' => '07:00', 'platform' => 'D1'],
            ['time' => '08:00', 'platform' => 'D1'],
            ['time' => '09:00', 'platform' => 'D1']
        ]
    ],
    [
        'number' => '202',
        'route' => 'Pristina → Mitrovica',
        'frequency' => 'Every 45 min',
        'stops' => ['Pristina', 'Vushtrri', 'Mitrovica'],
        'schedule' => [
            ['time' => '06:15', 'platform' => 'E2'],
            ['time' => '07:00', 'platform' => 'E2'],
            ['time' => '07:45', 'platform' => 'E2'],
            ['time' => '08:30', 'platform' => 'E2']
        ]
    ],
    [
        'number' => '203',
        'route' => 'Pristina → Gjilan',
        'frequency' => 'Every 60 min',
        'stops' => ['Pristina', 'Lipjan', 'Gjilan'],
        'schedule' => [
            ['time' => '06:30', 'platform' => 'F3'],
            ['time' => '07:30', 'platform' => 'F3'],
            ['time' => '08:30', 'platform' => 'F3'],
            ['time' => '09:30', 'platform' => 'F3']
        ]
    ]
];

// Ticket options
$ticketOptions = [
    [
        'name' => 'Single Ride',
        'description' => 'One-time journey on any bus route',
        'price' => '€0.50',
        'features' => ['Valid for 1 hour', 'One transfer'],
        'icon' => 'ticket-alt'
    ],
    [
        'name' => 'Day Pass',
        'description' => 'Unlimited rides for 24 hours',
        'price' => '€2.00',
        'features' => ['Unlimited rides', 'All routes'],
        'icon' => 'calendar-day'
    ],
    [
        'name' => 'Weekly Pass',
        'description' => '7 days of unlimited travel',
        'price' => '€10.00',
        'features' => ['7 days', 'Unlimited rides'],
        'icon' => 'calendar-week'
    ],
    [
        'name' => 'Monthly Pass',
        'description' => '30 days of unlimited travel',
        'price' => '€35.00',
        'features' => ['30 days', 'Unlimited rides', 'Priority boarding'],
        'icon' => 'calendar-alt'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Bus Routes - Kosovo Transit</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* CSS Variables */
        :root {
            --primary: #10b981; /* Green for bus mode */
            --primary-dark: #059669;
            --primary-light: #d1fae5;
            --public-primary: #10b981; /* Green for public transport */
            --public-primary-dark: #059669;
            --public-primary-light: #d1fae5;
            --surface: #ffffff;
            --background: #f8fafc;
            --text: #0f172a;
            --text-light: #64748b;
            --border: #e2e8f0;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --radius: 16px;
            --transition: all 0.2s ease;
        }

        /* Reset & Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--background);
            color: var(--text);
            line-height: 1.5;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        /* Layout */
        .app-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            max-width: 100%;
            margin: 0 auto;
            position: relative;
            width: 100%;
            max-width: 425px;
            background-color: var(--surface);
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            height: 100%;
            overflow-y: auto;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        /* Header */
        .app-header {
            position: fixed;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 425px;
            z-index: 100;
            background: var(--surface);
            box-shadow: var(--shadow);
            padding: 0.75rem 0;
            backdrop-filter: blur(8px);
            border-radius: var(--radius) var(--radius) 0 0;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .logo-icon {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            overflow: hidden;
            box-shadow: var(--shadow);
            background: transparent;
        }

        .logo-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .logo-text {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text);
            transition: color 0.3s ease;
        }

        .logo-text.public {
            color: var(--public-primary);
        }

        /* Bus specific styles */
        .bus-routes {
            background: var(--surface);
            border-radius: var(--radius);
            padding: 1.25rem;
            margin-top: 1.5rem;
            box-shadow: var(--shadow);
        }

        .bus-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .bus-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text);
        }

        .bus-refresh {
            background: var(--primary-light);
            color: var(--primary);
            border: none;
            padding: 0.5rem;
            border-radius: 50%;
            cursor: pointer;
            transition: var(--transition);
        }

        .bus-refresh:active {
            transform: scale(0.95);
        }

        .bus-route {
            background: var(--background);
            border-radius: var(--radius);
            padding: 1rem;
            margin-bottom: 1rem;
            transition: var(--transition);
        }

        .bus-route:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .bus-route:last-child {
            margin-bottom: 0;
        }

        .route-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .route-icon {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            background: var(--primary-light);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .route-info {
            flex: 1;
        }

        .route-name {
            font-weight: 500;
            margin-bottom: 0.25rem;
        }

        .route-details {
            font-size: 0.875rem;
            color: var(--text-light);
        }

        .bus-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem;
            background: var(--surface);
            border-radius: var(--radius);
            margin-top: 0.75rem;
        }

        .status-indicator {
            width: 0.75rem;
            height: 0.75rem;
            border-radius: 50%;
        }

        .status-indicator.on-time {
            background: #10b981;
        }

        .status-indicator.delayed {
            background: #f59e0b;
        }

        .status-indicator.cancelled {
            background: #ef4444;
        }

        .status-text {
            font-size: 0.875rem;
            font-weight: 500;
        }

        .bus-details {
            margin-top: 0.75rem;
            padding-top: 0.75rem;
            border-top: 1px solid var(--border);
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            color: var(--text-light);
        }

        .detail-item i {
            color: var(--primary);
            width: 1rem;
        }

        .live-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.75rem;
            color: var(--primary);
            background: var(--primary-light);
            padding: 0.25rem 0.5rem;
            border-radius: 1rem;
            margin-left: 0.5rem;
        }

        .live-indicator::before {
            content: '';
            width: 0.5rem;
            height: 0.5rem;
            background: var(--primary);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(0.95);
                opacity: 0.5;
            }
            50% {
                transform: scale(1);
                opacity: 1;
            }
            100% {
                transform: scale(0.95);
                opacity: 0.5;
            }
        }

        .no-route {
            text-align: center;
            padding: 2rem;
            color: var(--text-light);
        }

        .no-route i {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: var(--border);
        }

        .no-route p {
            font-size: 0.875rem;
        }

        .bus-schedule {
            margin-top: 1rem;
        }

        .schedule-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem;
            border-bottom: 1px solid var(--border);
        }

        .schedule-time {
            font-weight: 500;
            min-width: 4rem;
        }

        .schedule-info {
            flex: 1;
        }

        .schedule-route {
            font-size: 0.875rem;
            color: var(--text-light);
        }

        .schedule-platform {
            font-size: 0.75rem;
            color: var(--text-light);
            margin-top: 0.25rem;
        }

        /* Add new styles for the action buttons */
        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin: 1.5rem 0;
        }

        .action-button {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            padding: 1.5rem;
            background: var(--surface);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            text-decoration: none;
            color: var(--text);
            transition: var(--transition);
        }

        .action-button:active {
            transform: scale(0.98);
        }

        .action-button i {
            font-size: 2rem;
            color: var(--primary);
        }

        .action-button span {
            font-weight: 500;
            font-size: 1.125rem;
        }

        .action-button p {
            font-size: 0.875rem;
            color: var(--text-light);
            text-align: center;
        }

        /* Update existing styles */
        .search-card {
            margin-top: 1.5rem;
            background: var(--surface);
            border-radius: var(--radius);
            padding: 1.25rem;
            box-shadow: var(--shadow);
        }

        .search-input-container {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: var(--background);
            border-radius: var(--radius);
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
        }

        .search-input {
            flex: 1;
            border: none;
            background: none;
            font-size: 1rem;
            color: var(--text);
            outline: none;
        }

        .search-input::placeholder {
            color: var(--text-light);
        }

        /* Add new styles for ticket section */
        .ticket-options {
            display: grid;
            gap: 1rem;
            margin-top: 1rem;
        }

        .ticket-card {
            background: var(--background);
            border-radius: var(--radius);
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: var(--transition);
            cursor: pointer;
            border: 2px solid var(--border);
        }

        .ticket-card:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        .ticket-card.selected {
            border-color: var(--primary);
            background: var(--primary-light);
        }

        .ticket-icon {
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            background: var(--primary-light);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .ticket-info {
            flex: 1;
        }

        .ticket-name {
            font-weight: 600;
            font-size: 1.125rem;
            margin-bottom: 0.25rem;
        }

        .ticket-description {
            font-size: 0.875rem;
            color: var(--text-light);
            margin-bottom: 0.5rem;
        }

        .ticket-price {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary);
        }

        .ticket-features {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .ticket-feature {
            font-size: 0.75rem;
            color: var(--text-light);
            background: var(--surface);
            padding: 0.25rem 0.5rem;
            border-radius: 1rem;
        }

        .purchase-button {
            background: var(--primary);
            color: white;
            border: none;
            padding: 1rem;
            border-radius: var(--radius);
            font-weight: 600;
            width: 100%;
            margin-top: 1.5rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .purchase-button:hover {
            background: var(--primary-dark);
        }

        .ticket-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .ticket-header h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .ticket-header p {
            color: var(--text-light);
            font-size: 0.875rem;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 1.5rem 0;
            margin-top: 4rem;
            overflow-y: visible;
            min-height: calc(100vh - 4rem - 5rem);
        }

        .welcome-section {
            margin-bottom: 2rem;
            padding: 0 3rem;
            margin-top: 1rem;
        }

        .welcome-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            padding: 0 2rem;
        }

        .welcome-subtitle {
            color: var(--text-light);
            font-size: 0.875rem;
            padding: 0 2.5rem;
            line-height: 1.8;
        }

        .app-footer {
            background: var(--surface);
            border-top: 1px solid var(--border);
            padding: 0.75rem 0;
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 425px;
            z-index: 100;
            backdrop-filter: blur(8px);
            border-radius: 0 0 var(--radius) var(--radius);
        }

        .footer-nav {
            display: flex;
            justify-content: space-around;
            align-items: center;
            position: relative;
            padding: 0.5rem 0;
            margin: 0 auto;
        }

        .footer-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
            color: var(--text-light);
            text-decoration: none;
            font-size: 0.75rem;
            padding: 0.5rem;
            border-radius: var(--radius);
            transition: var(--transition);
            position: relative;
        }

        .footer-nav-item:active {
            background: var(--border);
            transform: scale(0.95);
        }

        .footer-nav-item i {
            font-size: 1.25rem;
        }

        .footer-nav-item.active {
            color: var(--primary);
        }

        /* Map center button styles */
        .footer-nav-item.map-center {
            position: relative;
            margin-top: -2.5rem;
        }

        .footer-nav-item.map-center i {
            font-size: 2rem;
            background: var(--primary);
            color: white;
            width: 4rem;
            height: 4rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-lg);
            transition: var(--transition);
        }

        .footer-nav-item.map-center:active i {
            transform: scale(0.95);
        }

        .footer-nav-item.map-center span {
            margin-top: 0.75rem;
            font-weight: 500;
        }

        .footer-nav-item.map-center.active i {
            background: var(--primary-dark);
            box-shadow: 0 0 0 4px var(--primary-light);
        }

        /* Active indicator for non-map items */
        .footer-nav-item:not(.map-center).active::after {
            content: '';
            position: absolute;
            bottom: -0.5rem;
            left: 50%;
            transform: translateX(-50%);
            width: 0.25rem;
            height: 0.25rem;
            background: var(--primary);
            border-radius: 50%;
        }

        /* Update the find-route-button class to use green */
        .find-route-button {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: var(--radius);
            background: var(--primary);
            color: white;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .find-route-button:hover {
            background: var(--primary-dark);
        }

        .search-input-container i {
            color: var(--primary);
            font-size: 1.25rem;
        }

        /* Add new styles for bus categories */
        .bus-category {
            margin-bottom: 2rem;
        }

        .bus-category:last-child {
            margin-bottom: 0;
        }

        .category-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--border);
        }

        .category-title i {
            color: var(--primary);
        }

        .bus-list {
            list-style: none;
            padding: 0;
            margin: 0 0 1.5rem 0;
        }
        .bus-list-item {
            display: flex;
            align-items: center;
            background: var(--surface);
            border-radius: 12px;
            box-shadow: var(--shadow);
            margin-bottom: 1rem;
            padding: 1rem 1.25rem;
            transition: box-shadow 0.2s;
        }
        .bus-list-item:last-child { margin-bottom: 0; }
        .bus-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            width: 3.2rem;
            height: 2.2rem;
            border-radius: 10px;
            margin-right: 1rem;
            color: #fff;
        }
        .bus-badge.city { background: #6ee7b7; color: #047857; }
        .bus-badge.intercity { background: #bfdbfe; color: #1e40af; }
        .bus-list-info { flex: 1; }
        .bus-list-route { font-weight: 500; font-size: 1.05rem; margin-bottom: 0.25rem; }
        .bus-list-meta { color: var(--text-light); font-size: 0.95rem; display: flex; align-items: center; gap: 0.4rem; }
        .bus-list-arrow { color: var(--border); font-size: 1.1rem; margin-left: 1rem; }

        .quick-access {
            background: var(--surface);
            border-radius: 12px;
            box-shadow: var(--shadow);
            padding: 1rem 1.25rem 0.5rem 1.25rem;
            margin-top: 2rem;
        }
        .quick-access-title {
            font-weight: 600;
            margin-bottom: 0.75rem;
            font-size: 1rem;
        }
        .quick-access-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .quick-btn {
            background: var(--background);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-size: 0.95rem;
            color: var(--text);
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s, border 0.2s;
        }
        .quick-btn:hover {
            background: var(--primary-light);
            border-color: var(--primary);
        }
    </style>
</head>
<body>
    <div class="app-container">
        <header class="app-header">
            <div class="container header-content">
                <div class="logo-container">
                    <div class="logo-icon">
                        <img src="img/logo_tiny.png" alt="Kosovo Transit Logo">
                    </div>
                    <h1 class="logo-text">Kosovo Transit</h1>
                </div>
            </div>
        </header>

        <main class="main-content">
            <div class="container">
                <div class="action-buttons">
                    <a href="#" class="action-button" id="scheduleButton">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Bus Schedules</span>
                        <p>View all bus timetables</p>
                    </a>
                    <a href="#" class="action-button" id="ticketButton">
                        <i class="fas fa-ticket-alt"></i>
                        <span>Buy Tickets</span>
                        <p>Purchase bus tickets</p>
                    </a>
                </div>

                <div class="search-card">
                    <div class="search-input-container">
                        <i class="fas fa-map-marker-alt"></i>
                        <input type="text" placeholder="Where do you want to go?" class="search-input" id="destinationInput">
                    </div>
                    <button class="find-route-button">Find Bus Route</button>
                </div>
            <br>
                <div class="welcome-section animate-in">
                    <h2 class="welcome-title">Bus Routes</h2>
                    <p class="welcome-subtitle">Find the best bus route to your destination</p>
                </div>

                
                

                <!-- Bus Routes Section -->
                <div class="bus-routes" id="busRoutes" style="display: block;">
                    <!-- City Bus Lines -->
                    <div class="bus-category">
                        <h4 class="category-title">
                            <i class="fas fa-bus"></i>
                            City Bus Lines
                        </h4>
                        <ul class="bus-list">
                            <?php foreach ($cityBusLines as $bus): ?>
                            <li class="bus-list-item city" onclick="showBusDetails('<?php echo $bus['number']; ?>')">
                                <span class="bus-badge city"><?php echo $bus['number']; ?></span>
                                <div class="bus-list-info">
                                    <div class="bus-list-route"><?php echo $bus['route']; ?></div>
                                    <div class="bus-list-meta">
                                        <i class="fas fa-clock"></i> <?php echo $bus['frequency']; ?>
                                    </div>
                                </div>
                                <span class="bus-list-arrow"><i class="fas fa-chevron-right"></i></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Intercity Bus Lines -->
                    <div class="bus-category">
                        <h4 class="category-title">
                            <i class="fas fa-bus"></i>
                            Intercity Bus Lines
                        </h4>
                        <ul class="bus-list">
                            <?php foreach ($intercityBusLines as $bus): ?>
                            <li class="bus-list-item intercity" onclick="showBusDetails('<?php echo $bus['number']; ?>')">
                                <span class="bus-badge intercity"><?php echo $bus['number']; ?></span>
                                <div class="bus-list-info">
                                    <div class="bus-list-route"><?php echo $bus['route']; ?></div>
                                    <div class="bus-list-meta">
                                        <i class="fas fa-clock"></i> <?php echo $bus['frequency']; ?>
                                    </div>
                                </div>
                                <span class="bus-list-arrow"><i class="fas fa-chevron-right"></i></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Quick Access -->
                    <div class="quick-access">
                        <div class="quick-access-title">Quick Access</div>
                        <div class="quick-access-buttons">
                            <button class="quick-btn" onclick="showSchedule()">Bus Timetables</button>
                            <button class="quick-btn" onclick="showDiscounts()">Student Discounts</button>
                            <button class="quick-btn" onclick="showServiceStatus()">Service Status</button>
                        </div>
                    </div>
                </div>

                <!-- Ticket Section (Hidden by default) -->
                <div class="bus-routes" id="ticketSection" style="display: none;">
                    <div class="ticket-header">
                        <h3>Choose Your Ticket</h3>
                        <p>Select the best option for your journey</p>
                    </div>
                    <div class="ticket-options">
                        <?php foreach ($ticketOptions as $ticket): ?>
                        <div class="ticket-card" onclick="selectTicket(this)">
                            <div class="ticket-icon">
                                <i class="fas fa-<?php echo $ticket['icon']; ?>"></i>
                            </div>
                            <div class="ticket-info">
                                <div class="ticket-name"><?php echo $ticket['name']; ?></div>
                                <div class="ticket-description"><?php echo $ticket['description']; ?></div>
                                <div class="ticket-price"><?php echo $ticket['price']; ?></div>
                                <div class="ticket-features">
                                    <?php foreach ($ticket['features'] as $feature): ?>
                                    <span class="ticket-feature"><?php echo $feature; ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="purchase-button" onclick="purchaseTicket()">Purchase Selected Ticket</button>
                </div>
            </div>
        </main>
        <br><br><br><br><br>

        <!-- Footer -->
        <footer class="app-footer">
            <div class="container">
                <nav class="footer-nav">
                    <a href="traffic.php" class="footer-nav-item">
                        <i class="fas fa-traffic-light"></i>
                        <span>Traffic</span>
                    </a>
                    <a href="buses.php" class="footer-nav-item">
                        <i class="fas fa-bus"></i>
                        <span>Buses</span>
                    </a>
                    <a href="bardh.html" class="footer-nav-item map-center active">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Map</span>
                    </a>
                    <a href="routes.php" class="footer-nav-item">
                        <i class="fas fa-route"></i>
                        <span>Routes</span>
                    </a>
                    <a href="settings.php" class="footer-nav-item">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                </nav>
            </div>
        </footer>
    </div>

    <script>
        // Store bus data in JavaScript for easy access
        const cityBusLines = <?php echo json_encode($cityBusLines); ?>;
        const intercityBusLines = <?php echo json_encode($intercityBusLines); ?>;
        const ticketOptions = <?php echo json_encode($ticketOptions); ?>;

        function showBusDetails(busNumber) {
            const bus = [...cityBusLines, ...intercityBusLines].find(b => b.number === busNumber);
            if (bus) {
                alert(`Bus ${bus.number}\nRoute: ${bus.route}\nStops: ${bus.stops.join(' → ')}\nSchedule: ${bus.schedule.map(s => s.time).join(', ')}`);
            }
        }

        function showSchedule() {
            alert('Bus timetables feature coming soon!');
        }

        function showDiscounts() {
            alert('Student discounts feature coming soon!');
        }

        function showServiceStatus() {
            alert('Service status feature coming soon!');
        }

        function selectTicket(element) {
            document.querySelectorAll('.ticket-card').forEach(card => {
                card.classList.remove('selected');
            });
            element.classList.add('selected');
        }

        function purchaseTicket() {
            const selectedTicket = document.querySelector('.ticket-card.selected');
            if (selectedTicket) {
                const ticketName = selectedTicket.querySelector('.ticket-name').textContent;
                const ticketPrice = selectedTicket.querySelector('.ticket-price').textContent;
                const ticketDescription = selectedTicket.querySelector('.ticket-description').textContent;
                const features = Array.from(selectedTicket.querySelectorAll('.ticket-feature')).map(f => f.textContent);
                
                // Create form and submit to payment.php
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'payment.php';
                
                const ticketData = {
                    name: ticketName,
                    price: ticketPrice,
                    description: ticketDescription,
                    features: features
                };
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ticket_data';
                input.value = JSON.stringify(ticketData);
                form.appendChild(input);
                
                document.body.appendChild(form);
                form.submit();
            } else {
                alert('Please select a ticket first');
            }
        }

        // Handle action buttons
        document.getElementById('scheduleButton').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('busRoutes').style.display = 'block';
            document.getElementById('ticketSection').style.display = 'none';
        });

        document.getElementById('ticketButton').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('busRoutes').style.display = 'none';
            document.getElementById('ticketSection').style.display = 'block';
        });
    </script>
</body>
</html> 