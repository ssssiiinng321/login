<?php
require 'db.php';

try {
    $sql = "
    CREATE TABLE IF NOT EXISTS sessions (
        id VARCHAR(128) NOT NULL PRIMARY KEY,
        data MEDIUMTEXT NOT NULL,
        timestamp INT(10) UNSIGNED NOT NULL
    );
    ";
    
    echo "Creating sessions table...\n";
    $pdo->exec($sql);
    echo "Success! Table 'sessions' created.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
