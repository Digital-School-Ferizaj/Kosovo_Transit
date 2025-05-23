<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();


// Fetch user preferences
$stmt = $pdo->prepare("SELECT * FROM user_preferences WHERE user_id = ?");
$stmt->execute([$user_id]);
$preferences = $stmt->fetch();


// Fetch user destinations
$stmt = $pdo->prepare("SELECT * FROM user_destinations WHERE user_id = ? ORDER BY type = 'home' DESC, type = 'work' DESC, type = 'course' DESC");
$stmt->execute([$user_id]);
$destinations = $stmt->fetchAll();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        
        try {
            // Check if email is already taken by another user
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $user_id]);
            if ($stmt->fetch()) {
                $error = 'Email is already taken by another user';
            } else {
                $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
                $stmt->execute([$username, $email, $user_id]);
                $success = 'Profile updated successfully';
                
                // Update session data
                $_SESSION['username'] = $username;
                $_SESSION['user_email'] = $email;
                
                // Refresh user data
                $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();
            }
        } catch (PDOException $e) {
            $error = 'Failed to update profile';
        }
    } elseif (isset($_POST['update_preferences'])) {
        $notifications = isset($_POST['notifications']) ? 1 : 0;
        $dark_mode = isset($_POST['dark_mode']) ? 1 : 0;
        $language = filter_input(INPUT_POST, 'language', FILTER_SANITIZE_STRING);
        
        try {
            if ($preferences) {
                $stmt = $pdo->prepare("UPDATE user_preferences SET notifications_enabled = ?, dark_mode = ?, language = ? WHERE user_id = ?");
                $stmt->execute([$notifications, $dark_mode, $language, $user_id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO user_preferences (user_id, notifications_enabled, dark_mode, language) VALUES (?, ?, ?, ?)");
                $stmt->execute([$user_id, $notifications, $dark_mode, $language]);
            }
            $success = 'Preferences updated successfully';
            
            // Refresh preferences
            $stmt = $pdo->prepare("SELECT * FROM user_preferences WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $preferences = $stmt->fetch();
        } catch (PDOException $e) {
            $error = 'Failed to update preferences';
        }
    } elseif (isset($_POST['add_destination'])) {
        $type = filter_input(INPUT_POST, 'destination_type', FILTER_SANITIZE_STRING);
        $name = filter_input(INPUT_POST, 'destination_name', FILTER_SANITIZE_STRING);
        $address = filter_input(INPUT_POST, 'destination_address', FILTER_SANITIZE_STRING);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO user_destinations (user_id, type, name, address) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_id, $type, $name, $address]);
            $success = 'Destination added successfully';
            
            // Refresh destinations
            $stmt = $pdo->prepare("SELECT * FROM user_destinations WHERE user_id = ? ORDER BY type = 'home' DESC, type = 'work' DESC, type = 'course' DESC");
            $stmt->execute([$user_id]);
            $destinations = $stmt->fetchAll();
        } catch (PDOException $e) {
            $error = 'Failed to add destination';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Settings - Kosovo Transit</title>
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

        .settings-section {
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

        /* Profile Section Styles */
        .profile-header {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .profile-avatar {
            width: 5rem;
            height: 5rem;
            border-radius: 50%;
            background: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--primary);
            position: relative;
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .profile-avatar .edit-avatar {
            position: absolute;
            bottom: 0;
            right: 0;
            background: var(--primary);
            color: white;
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            cursor: pointer;
        }

        .profile-info {
            flex: 1;
        }

        .profile-name {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .profile-email {
            color: var(--text-light);
            font-size: 0.875rem;
        }

        /* Settings List Styles */
        .settings-list {
            display: grid;
            gap: 0.75rem;
        }

        .settings-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: var(--background);
            border-radius: var(--radius);
            cursor: pointer;
            transition: var(--transition);
        }

        .settings-item:hover {
            transform: translateX(4px);
        }

        .settings-icon {
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

        .settings-info {
            flex: 1;
        }

        .settings-label {
            font-weight: 500;
            margin-bottom: 0.25rem;
        }

        .settings-description {
            font-size: 0.875rem;
            color: var(--text-light);
        }

        .settings-arrow {
            color: var(--text-light);
        }

        /* Toggle Switch Styles */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 3rem;
            height: 1.5rem;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: var(--border);
            transition: .4s;
            border-radius: 1.5rem;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 1.25rem;
            width: 1.25rem;
            left: 0.125rem;
            bottom: 0.125rem;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .toggle-slider {
            background-color: var(--primary);
        }

        input:checked + .toggle-slider:before {
            transform: translateX(1.5rem);
        }

        /* Logout Button */
        .logout-button {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 1rem;
            background: #fee2e2;
            color: #dc2626;
            border: none;
            border-radius: var(--radius);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
        }

        .logout-button:hover {
            background: #fecaca;
        }

        .main-content {
            flex: 1;
            padding: 1.5rem 0;
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
                    <div class="logo-icon" style="background-color: #2563eb; border-radius: 10px;">
                        <img src="img/whitelogo.png" alt="Kosovo Transit Logo" style="height: 70px; width: 70px; margin: -15px;">
                    </div>
                    <h1 class="logo-text">Kosovo Transit</h1>
                </div>
            </div>
        </header>
        <br><br>

        <main class="main-content">
            <div class="container">
                <?php if ($error): ?>
                    <div class="error-message" style="color: #dc2626; margin-bottom: 1rem; text-align: center;">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="success-message" style="color: #059669; margin-bottom: 1rem; text-align: center;">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <!-- Profile Section -->
                <div class="settings-section">
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <img src="img/default-avatar.png" alt="Profile Picture" onerror="this.src='data:image/svg+xml,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'%236366f1\'><path d=\'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z\'/></svg>'">
                            <div class="edit-avatar">
                                <i class="fas fa-camera"></i>
                            </div>
                        </div>
                        <div class="profile-info">
                            <div class="profile-name"><?php echo htmlspecialchars($user['username']); ?></div>
                            <div class="profile-email"><?php echo htmlspecialchars($user['email']); ?></div>
                        </div>
                    </div>
                </div>

                <!-- Account Settings Section -->
                <div class="settings-section">
                    <div class="section-header">
                        <h3 class="section-title">
                            <i class="fas fa-user-cog"></i>
                            Account Settings
                        </h3>
                    </div>

                    <div class="settings-list">
                        <div class="settings-item" onclick="editProfile()">
                            <div class="settings-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="settings-info">
                                <div class="settings-label">Personal Information</div>
                                <div class="settings-description">Update your name, email, and phone number</div>
                            </div>
                            <i class="fas fa-chevron-right settings-arrow"></i>
                        </div>

                        <div class="settings-item" onclick="changePassword()">
                            <div class="settings-icon">
                                <i class="fas fa-lock"></i>
                            </div>
                            <div class="settings-info">
                                <div class="settings-label">Password & Security</div>
                                <div class="settings-description">Change your password and security settings</div>
                            </div>
                            <i class="fas fa-chevron-right settings-arrow"></i>
                        </div>

                        <div class="settings-item" onclick="managePayment()">
                            <div class="settings-icon">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <div class="settings-info">
                                <div class="settings-label">Payment Methods</div>
                                <div class="settings-description">Manage your payment cards and methods</div>
                            </div>
                            <i class="fas fa-chevron-right settings-arrow"></i>
                        </div>
                    </div>
                </div>

                <!-- Preferences Section -->
                <div class="settings-section">
                    <div class="section-header">
                        <h3 class="section-title">
                            <i class="fas fa-sliders-h"></i>
                            Preferences
                        </h3>
                    </div>

                    <div class="settings-list">
                        <div class="settings-item">
                            <div class="settings-icon">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div class="settings-info">
                                <div class="settings-label">Notifications</div>
                                <div class="settings-description">Manage your notification preferences</div>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" <?php echo ($preferences && $preferences['notifications_enabled']) ? 'checked' : ''; ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div class="settings-item">
                            <div class="settings-icon">
                                <i class="fas fa-language"></i>
                            </div>
                            <div class="settings-info">
                                <div class="settings-label">Language</div>
                                <div class="settings-description">Change app language</div>
                            </div>
                            <i class="fas fa-chevron-right settings-arrow"></i>
                        </div>

                        <div class="settings-item">
                            <div class="settings-icon">
                                <i class="fas fa-moon"></i>
                            </div>
                            <div class="settings-info">
                                <div class="settings-label">Dark Mode</div>
                                <div class="settings-description">Toggle dark mode</div>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" <?php echo ($preferences && $preferences['dark_mode']) ? 'checked' : ''; ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Logout Button -->
                <button class="logout-button" onclick="logout()">
                    <i class="fas fa-sign-out-alt"></i>
                    Log Out
                </button>
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
        function editProfile() {
            alert('Edit profile feature coming soon!');
        }

        function changePassword() {
            alert('Change password feature coming soon!');
        }

        function managePayment() {
            alert('Payment management feature coming soon!');
        }

        function logout() {
            if (confirm('Are you sure you want to log out?')) {
                window.location.href = 'logout.php';
            }
        }

        // Handle profile picture upload
        document.querySelector('.edit-avatar').addEventListener('click', function() {
            // Here you would typically open a file picker
            alert('Profile picture upload feature coming soon!');
        });
    </script>
</body>
</html> 