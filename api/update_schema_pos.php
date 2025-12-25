<?php
require_once 'db.php';

try {
    // Add columns if they don't exist
    $pdo->exec("ALTER TABLE products ADD COLUMN IF NOT EXISTS barcode VARCHAR(100) UNIQUE");
    $pdo->exec("ALTER TABLE products ADD COLUMN IF NOT EXISTS category VARCHAR(50) DEFAULT 'General'");
    
    $pdo->exec("ALTER TABLE orders ADD COLUMN IF NOT EXISTS payment_method VARCHAR(50) DEFAULT 'cash'");
    $pdo->exec("ALTER TABLE orders ADD COLUMN IF NOT EXISTS cashier_name VARCHAR(50)");
    
    echo "Schema updated successfully.<br>";
} catch (PDOException $e) {
    // Ignore duplicate column errors if "IF NOT EXISTS" is not supported by this MySQL version (older versions)
    echo "Note: " . $e->getMessage();
}
?>
