<?php
// Direct copy of db.php logic but with verbose output for CLI
$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '3306';
$db   = getenv('DB_NAME') ?: 'user_auth';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
echo "Connecting to DSN: $dsn with user: $user\n";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    echo "Connection successful!\n";
} catch (\PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
    echo "Drivers available: " . implode(', ', PDO::getAvailableDrivers()) . "\n";
}
?>
