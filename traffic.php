<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Traffic Updates - Kosovo Transit</title>
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

        html {
            height: 100%;
            overflow: hidden;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--background);
            color: var(--text);
            line-height: 1.5;
            min-height: 100%;
            position: relative;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            overflow: hidden;
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
            position: relative;
            overflow: hidden;
            width: 100%;
            max-width: 425px;
            background-color: var(--surface);
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            margin: 0 auto;
            height: 100vh;
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

        /* Traffic specific styles */
        .traffic-updates {
            background: var(--surface);
            border-radius: var(--radius);
            padding: 1.25rem;
            margin-top: 1.5rem;
            box-shadow: var(--shadow);
        }

        .traffic-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .traffic-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text);
        }

        .traffic-refresh {
            background: var(--primary-light);
            color: var(--primary);
            border: none;
            padding: 0.5rem;
            border-radius: 50%;
            cursor: pointer;
            transition: var(--transition);
        }

        .traffic-refresh:active {
            transform: scale(0.95);
        }

        .traffic-route {
            background: var(--background);
            border-radius: var(--radius);
            padding: 1rem;
            margin-bottom: 1rem;
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

        .traffic-status {
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

        .status-indicator.good {
            background: #10b981;
        }

        .status-indicator.moderate {
            background: #f59e0b;
        }

        .status-indicator.heavy {
            background: #ef4444;
        }

        .status-text {
            font-size: 0.875rem;
            font-weight: 500;
        }

        .traffic-details {
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

        /* Search Input Styles */
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
        .search-input-container i {
            color: var(--primary-dark);
            font-size: 1.25rem;
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

        .find-route-button:active {
            transform: scale(0.98);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 1.5rem 0;
            padding-bottom: 5rem; /* Space for fixed footer */
            margin-top: 4rem; /* Add space below fixed header */
            overflow-y: auto;
            height: calc(100vh - 4rem - 5rem); /* Subtract header and footer height */
        }

        .welcome-section {
            margin-bottom: 2rem;
            padding: 0 0rem;
            margin-top: 1rem;
        }

        .welcome-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            padding: 0 2rem;
            text-align:center;
        }

        .welcome-subtitle {
            color: var(--text-light);
            font-size: 0.875rem;
            padding: 0 2.5rem;
            line-height: 1.8;
        }

        /* Traffic Section */
        .traffic-section {
            background: var(--surface);
            border-radius: var(--radius);
            padding: 1.25rem;
            margin-top: 1.5rem;
            box-shadow: var(--shadow);
        }

        /* Footer specific styles */
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
                    <div class="logo-icon" style="background-color: #2563eb; border-radius: 10px;">
                        <img src="img/whitelogo.png" alt="Kosovo Transit Logo" style="height: 70px; width: 70px; margin: -15px;">
                    </div>
                    <h1 class="logo-text">Kosovo Transit</h1>
                </div>
            </div>
        </header>

        <main class="main-content">
            <div class="container">
                <div class="search-card">
                    <div class="search-input-container">
                        <i class="fas fa-map-marker-alt"></i>
                        <input type="text" placeholder="Where do you want to go?" class="search-input" id="destinationInput">
                    </div>
                    <button class="find-route-button car-mode">Find Route</button>
                </div>
            <br>
            
                <div class="welcome-section animate-in">
                    <h2 class="welcome-title">Traffic Updates</h2>
                    <p class="welcome-subtitle">Real-time traffic conditions and alerts</p>
                </div>

                <!-- Search Input Section -->
                

                <!-- Traffic Overview Section -->
                <div class="traffic-section" style="margin-bottom: 2rem;">
                    <div class="traffic-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <span class="traffic-title" style="font-size: 1.1rem; font-weight: 600;">Live Traffic News</span>
                        <a href="#" style="color: #3b82f6; font-size: 0.95rem; text-decoration: none; font-weight: 500;">View All</a>
                    </div>
                    <!-- News Card 1 -->
                    <div style="background: #fff; border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 1rem 1.25rem; margin-bottom: 1rem; display: flex; gap: 1rem; align-items: flex-start;">
                        <div style="font-size: 1.5rem; color: #ef4444; margin-top: 0.1rem;">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span style="font-weight: 600; color: #1e293b;">Traffic accident</span>
                                <span style="background: #ef4444; color: #fff; font-size: 0.8rem; font-weight: 600; border-radius: 8px; padding: 0.15rem 0.7rem; margin-left: 0.5rem;">High Impact</span>
                            </div>
                            <div style="color: #64748b; font-size: 0.95rem; margin-bottom: 0.1rem;">Bill Clinton Boulevard</div>
                            <div style="color: #334155; font-size: 0.95rem;">Multiple vehicle collision causing major delays</div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem;">
                                <span style="color: #94a3b8; font-size: 0.85rem;">10 minutes ago</span>
                                <a href="#" style="color: #3b82f6; font-size: 0.95rem; text-decoration: none;">Details &rarr;</a>
                            </div>
                        </div>
                    </div>
                    <!-- News Card 2 -->
                    <div style="background: #fff; border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 1rem 1.25rem; margin-bottom: 1rem; display: flex; gap: 1rem; align-items: flex-start;">
                        <div style="font-size: 1.5rem; color: #fbbf24; margin-top: 0.1rem;">
                            <i class="fas fa-tools"></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span style="font-weight: 600; color: #1e293b;">Road construction</span>
                                <span style="background: #fbbf24; color: #fff; font-size: 0.8rem; font-weight: 600; border-radius: 8px; padding: 0.15rem 0.7rem; margin-left: 0.5rem;">Medium Impact</span>
                            </div>
                            <div style="color: #64748b; font-size: 0.95rem; margin-bottom: 0.1rem;">George Bush Street</div>
                            <div style="color: #334155; font-size: 0.95rem;">Lane closure due to ongoing road repairs</div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem;">
                                <span style="color: #94a3b8; font-size: 0.85rem;">2 hours ago</span>
                                <a href="#" style="color: #3b82f6; font-size: 0.95rem; text-decoration: none;">Details &rarr;</a>
                            </div>
                        </div>
                    </div>
                    <!-- News Card 3 -->
                    <div style="background: #fff; border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 1rem 1.25rem; margin-bottom: 1rem; display: flex; gap: 1rem; align-items: flex-start;">
                        <div style="font-size: 1.5rem; color: #f59e42; margin-top: 0.1rem;">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span style="font-weight: 600; color: #1e293b;">Heavy traffic</span>
                                <span style="background: #fbbf24; color: #fff; font-size: 0.8rem; font-weight: 600; border-radius: 8px; padding: 0.15rem 0.7rem; margin-left: 0.5rem;">Medium Impact</span>
                            </div>
                            <div style="color: #64748b; font-size: 0.95rem; margin-bottom: 0.1rem;">Mother Teresa Boulevard</div>
                            <div style="color: #334155; font-size: 0.95rem;">Slow moving traffic due to rush hour</div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem;">
                                <span style="color: #94a3b8; font-size: 0.85rem;">Just now</span>
                                <a href="#" style="color: #3b82f6; font-size: 0.95rem; text-decoration: none;">Details &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Quick Navigation Section -->
                <div style="margin-bottom: 2rem;">
                    <div style="font-weight: 600; font-size: 1.1rem; margin-bottom: 1rem;">Quick Navigation</div>
                    <div style="display: flex; gap: 1rem;">
                        <div style="flex: 1; background: #fff; border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 1.25rem; display: flex; flex-direction: column; align-items: center;">
                            <div style="font-size: 2rem; color: #3b82f6; margin-bottom: 0.5rem;">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div style="font-weight: 600; color: #1e293b;">Saved Routes</div>
                            <div style="color: #64748b; font-size: 0.95rem; text-align: center;">Quick access to your routes</div>
                        </div>
                        <div style="flex: 1; background: #fff; border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 1.25rem; display: flex; flex-direction: column; align-items: center;">
                            <div style="font-size: 2rem; color: #3b82f6; margin-bottom: 0.5rem;">
                                <i class="fas fa-car"></i>
                            </div>
                            <div style="font-weight: 600; color: #1e293b;">Traffic Map</div>
                            <div style="color: #64748b; font-size: 0.95rem; text-align: center;">View live traffic conditions</div>
                        </div>
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
                    <a href="index.html" class="footer-nav-item map-center active">
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
        // Here you would typically handle traffic data updates
        function updateTrafficData() {
            // This would be replaced with actual API calls
            console.log('Updating traffic data...');
        }

        // Update traffic data every 5 minutes
        setInterval(updateTrafficData, 300000);

        // Search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const destinationInput = document.getElementById('destinationInput');
            const findRouteButton = document.querySelector('.find-route-button');
            const noRoute = document.getElementById('noRoute');
            const trafficSection = document.querySelector('.traffic-section');

            findRouteButton.addEventListener('click', function() {
                const destination = destinationInput.value.trim();
                if (destination) {
                    // Hide no route message and show traffic section
                    noRoute.style.display = 'none';
                    trafficSection.style.display = 'block';
                    
                    // Here you would typically fetch traffic data for the route
                    console.log(`Finding traffic updates for route to: ${destination}`);
                    
                    // Simulate live updates
                    updateTrafficData();
                }
            });
        });
    </script>
</body>
</html> 