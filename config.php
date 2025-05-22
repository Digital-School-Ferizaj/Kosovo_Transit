<?php
// Database configuration
$host = 'localhost';           // Database host (often 'localhost')
$db   = 'kosovo_transit';      // Your database name
$user = 'root';        // Your database username
$pass = '';    // Your database password
$charset = 'utf8mb4';          // Character set

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Use real prepared statements
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // In production, don't show the error message directly!
    exit('Database connection failed: ' . $e->getMessage());
}
?>
