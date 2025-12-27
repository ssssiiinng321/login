<?php
require_once 'db.php';

try {
    // Check if column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM products LIKE 'is_active'");
    if ($stmt->fetch()) {
        echo "Column 'is_active' already exists.\n";
    } else {
        $pdo->exec("ALTER TABLE products ADD COLUMN is_active TINYINT(1) DEFAULT 1");
        echo "Successfully added 'is_active' column to products table.\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
