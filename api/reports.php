<?php
require_once 'db.php';
header('Content-Type: application/json');

$type = $_GET['type'] ?? 'daily';

if ($type === 'daily') {
    $today = date('Y-m-d');
    $stmt = $pdo->prepare("SELECT SUM(total_amount) as total, COUNT(*) as count FROM orders WHERE DATE(created_at) = ? AND status = 'completed'");
    $stmt->execute([$today]);
    echo json_encode($stmt->fetch());
} else if ($type === 'top_products') {
    $stmt = $pdo->query("
        SELECT p.name, SUM(oi.quantity) as total_sold 
        FROM order_items oi 
        JOIN products p ON oi.product_id = p.id 
        JOIN orders o ON oi.order_id = o.id
        WHERE o.status = 'completed'
        GROUP BY p.id 
        ORDER BY total_sold DESC 
        LIMIT 5
    ");
    echo json_encode($stmt->fetchAll());
}
?>
