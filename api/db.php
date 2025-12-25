<?php
// Use environment variables for flexible configuration (Vercel/Production vs Local)
$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '3306'; // Add custom port support (default 3306)
$db   = getenv('DB_NAME') ?: 'user_auth';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // If connection fails, we can't really do anything for the main app, but we should handle it gracefully
    // potentially logging it or showing a maintenance page.
    // For now, let's silence the fatal error to see if we can render a friendly error.
    error_log("DB Connection failed: " . $e->getMessage());
    $pdo = null;
    
    // If this is a direct page load, show error.
    // If it's an API request (defined by products.php), be silent so JSON can be returned.
    if (!defined('IS_API_REQUEST') && basename($_SERVER['PHP_SELF']) != 'session.php') {
         die("Database connection failed. Please try again later.");
    }
}
?>
