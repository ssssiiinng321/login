<?php
$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$db   = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$ssl_ca = 'ca.pem'; // Aiven usually requires SSL, but often works without strict CA verification in simple PDO setups with correct options.

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    // SSL options removed for compatibility testing
];

try {
    echo "Connecting to Aiven Cloud Database...\n";
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Connected successfully!\n";

    // Create Users
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) NOT NULL UNIQUE,
                email VARCHAR(100) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        echo "Table 'users' checked/created.<br>";
    } catch (PDOException $e) { echo "Error creating users: " . $e->getMessage() . "<br>"; }

    // Create Products
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS products (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                description TEXT,
                price DECIMAL(10, 2) NOT NULL,
                stock INT DEFAULT 0,
                image_url VARCHAR(2048),
                barcode VARCHAR(100) UNIQUE,
                category VARCHAR(50) DEFAULT 'General',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        echo "Table 'products' checked/created.<br>";
    } catch (PDOException $e) { echo "Error creating products: " . $e->getMessage() . "<br>"; }

    // Create Orders
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS orders (
                id INT AUTO_INCREMENT PRIMARY KEY,
                customer_name VARCHAR(100) NOT NULL,
                total_amount DECIMAL(10, 2) NOT NULL,
                status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
                payment_method VARCHAR(50) DEFAULT 'cash',
                cashier_name VARCHAR(50),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        echo "Table 'orders' checked/created.<br>";
    } catch (PDOException $e) { echo "Error creating orders: " . $e->getMessage() . "<br>"; }

    // Create Order Items
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS order_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                order_id INT NOT NULL,
                product_id INT NOT NULL,
                quantity INT NOT NULL,
                price DECIMAL(10, 2) NOT NULL,
                FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
                FOREIGN KEY (product_id) REFERENCES products(id)
            )
        ");
        echo "Table 'order_items' checked/created.<br>";
    } catch (PDOException $e) { echo "Error creating order_items: " . $e->getMessage() . "<br>"; }

    // Create Sessions
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS sessions (
                id VARCHAR(128) NOT NULL PRIMARY KEY,
                data TEXT NOT NULL,
                timestamp INT(11) UNSIGNED NOT NULL
            )
        ");
        echo "Table 'sessions' checked/created.<br>";
    } catch (PDOException $e) { echo "Error creating sessions: " . $e->getMessage() . "<br>"; }

    echo "<h3>Migration Complete!</h3>";

} catch (\PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
?>
