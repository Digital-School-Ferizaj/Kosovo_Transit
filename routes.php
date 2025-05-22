<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user destinations
$stmt = $pdo->prepare("
    SELECT * FROM user_destinations 
    WHERE user_id = ? 
    ORDER BY type = 'home' DESC, type = 'work' DESC, type = 'course' DESC
");
$stmt->execute([$user_id]);
$destinations = $stmt->fetchAll();

// Road improvement data
$roadData = [
    [
        'city' => 'Pristina',
        'suggestions' => [
            [
                'title' => 'Main Boulevard Expansion',
                'priority' => 'high',
                'description' => 'Severe congestion detected at coordinates 42.6629, 21.1655. Recommend widening the boulevard and adding dedicated bus lanes.',
                'icon' => 'road'
            ],
            [
                'title' => 'Downtown Intersection Redesign',
                'priority' => 'high',
                'description' => 'Heavy traffic at coordinates 42.6610, 21.1700. Implement smart traffic lights with AI-based timing.',
                'icon' => 'traffic-light'
            ]
        ]
    ],
    [
        'city' => 'Prizren',
        'suggestions' => [
            [
                'title' => 'Historic Center Traffic Management',
                'priority' => 'high',
                'description' => 'High traffic concentration at 42.2167, 20.7414. Implement one-way street system and pedestrian zones.',
                'icon' => 'road'
            ]
        ]
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>My Routes - Kosovo Transit</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* CSS Variables */
        :root {
            --primary: #3b82f6; /* Blue for car mode */
            --primary-dark: #2563eb;
            --primary-light: #dbeafe;
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

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 1.5rem 0;
            margin-top: 4rem; /* Add space below fixed header */
            overflow-y: visible;
            min-height: calc(100vh - 4rem - 5rem); /* Subtract header and footer height */
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

        /* Routes specific styles */
        .routes-section {
            background: var(--surface);
            border-radius: var(--radius);
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow);
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-title i {
            color: var(--primary);
        }

        .frequent-destinations {
            display: grid;
            gap: 1rem;
        }

        .destination-card {
            background: var(--background);
            border-radius: var(--radius);
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .destination-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .destination-icon {
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .destination-icon.work {
            background: #e3f2fd;
            color: #1976d2;
        }

        .destination-icon.school {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .destination-icon.course {
            background: #fff3e0;
            color: #f57c00;
        }

        .destination-info {
            flex: 1;
        }

        .destination-name {
            font-weight: 500;
            margin-bottom: 0.25rem;
        }

        .destination-details {
            font-size: 0.875rem;
            color: var(--text-light);
        }

        .destination-stats {
            display: flex;
            gap: 1rem;
            margin-top: 0.5rem;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.75rem;
            color: var(--text-light);
        }

        .stat-item i {
            font-size: 0.875rem;
        }

        .add-destination {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 1rem;
            background: var(--background);
            border: 2px dashed var(--border);
            border-radius: var(--radius);
            color: var(--text-light);
            cursor: pointer;
            transition: var(--transition);
        }

        .add-destination:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .recent-routes {
            margin-top: 1.5rem;
        }

        .route-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: var(--background);
            border-radius: var(--radius);
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

        .route-time {
            font-size: 0.875rem;
            color: var(--text-light);
            white-space: nowrap;
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
                <div class="welcome-section animate-in">
                    <h2 class="welcome-title">My Routes</h2>
                    <p class="welcome-subtitle">Your frequent destinations and saved routes</p>
                </div>

                <!-- Frequent Destinations Section -->
                <div class="routes-section">
                    <div class="section-header">
                        <h3 class="section-title">
                            <i class="fas fa-star"></i>
                            Frequent Destinations
                        </h3>
                    </div>

                    <div class="frequent-destinations">
                        <?php foreach ($destinations as $destination): ?>
                            <div class="destination-card" onclick="navigateToDestination('<?php echo $destination['id']; ?>')">
                                <div class="destination-icon <?php echo $destination['type']; ?>">
                                    <i class="fas fa-<?php 
                                        echo match($destination['type']) {
                                            'home' => 'home',
                                            'work' => 'briefcase',
                                            'course' => 'book',
                                            default => 'map-marker-alt'
                                        };
                                    ?>"></i>
                                </div>
                                <div class="destination-info">
                                    <div class="destination-name"><?php echo htmlspecialchars($destination['name']); ?></div>
                                    <div class="destination-details"><?php echo htmlspecialchars($destination['address']); ?></div>
                                    <div class="destination-stats">
                                        <div class="stat-item">
                                            <i class="fas fa-clock"></i>
                                            <span>Frequent</span>
                                        </div>
                                        <div class="stat-item">
                                            <i class="fas fa-route"></i>
                                            <span>Saved</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <!-- Add New Destination -->
                        <div class="add-destination" onclick="window.location.href='settings.php'">
                            <i class="fas fa-plus"></i>
                            <span>Add New Destination</span>
                        </div>
                    </div>
                </div>

                <!-- Recent Routes Section -->
                <div class="routes-section recent-routes">
                    <div class="section-header">
                        <h3 class="section-title">
                            <i class="fas fa-history"></i>
                            Recent Routes
                        </h3>
                    </div>

                    <div class="route-item" onclick="navigateToRoute('route1')">
                        <div class="route-icon">
                            <i class="fas fa-bus"></i>
                        </div>
                        <div class="route-info">
                            <div class="route-name">Home → Work</div>
                            <div class="route-details">Route 1A • 25 minutes</div>
                        </div>
                        <div class="route-time">Today, 8:30 AM</div>
                    </div>

                    <div class="route-item" onclick="navigateToRoute('route2')">
                        <div class="route-icon">
                            <i class="fas fa-bus"></i>
                        </div>
                        <div class="route-info">
                            <div class="route-name">Work → University</div>
                            <div class="route-details">Route 2B • 15 minutes</div>
                        </div>
                        <div class="route-time">Today, 2:00 PM</div>
                    </div>

                    <div class="route-item" onclick="navigateToRoute('route3')">
                        <div class="route-icon">
                            <i class="fas fa-bus"></i>
                        </div>
                        <div class="route-info">
                            <div class="route-name">University → Home</div>
                            <div class="route-details">Route 3C • 20 minutes</div>
                        </div>
                        <div class="route-time">Today, 5:45 PM</div>
                    </div>
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
        function navigateToDestination(destinationId) {
            window.location.href = `bardh.php?destination=${destinationId}`;
        }

        function navigateToRoute(routeId) {
            window.location.href = `route_details.php?id=${routeId}`;
        }
    </script>
</body>
</html> 