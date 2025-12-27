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
} else if ($type === 'all_stats') {
    $dateFilter = $_GET['date'] ?? null;
    $dateCondition = "";
    $params = [];

    // 1. Sales
    // If date is set, show sales for that date. If not, show defaults (Today's Sales) OR All Time? 
    // User requested: "if not then show whole calculation". So default = All Time.
    if ($dateFilter) {
        $stmt1 = $pdo->prepare("SELECT SUM(total_amount) as total FROM orders WHERE DATE(created_at) = ? AND status = 'completed'");
        $stmt1->execute([$dateFilter]);
    } else {
        // Whole Calculation (All Time)
        $stmt1 = $pdo->query("SELECT SUM(total_amount) as total FROM orders WHERE status = 'completed'");
    }
    $sales = $stmt1->fetch()['total'] ?? 0;

    // 2. Products Sold
    if ($dateFilter) {
        $stmt2 = $pdo->prepare("SELECT SUM(quantity) as total_qty FROM order_items oi JOIN orders o ON oi.order_id = o.id WHERE DATE(o.created_at) = ? AND o.status = 'completed'");
        $stmt2->execute([$dateFilter]);
    } else {
        $stmt2 = $pdo->query("SELECT SUM(quantity) as total_qty FROM order_items oi JOIN orders o ON oi.order_id = o.id WHERE o.status = 'completed'");
    }
    $productsSold = $stmt2->fetch()['total_qty'] ?? 0;

    // 3. Refund/Cancelled
    if ($dateFilter) {
        $stmt3 = $pdo->prepare("SELECT SUM(total_amount) as total FROM orders WHERE DATE(created_at) = ? AND status = 'cancelled'");
        $stmt3->execute([$dateFilter]);
    } else {
        $stmt3 = $pdo->query("SELECT SUM(total_amount) as total FROM orders WHERE status = 'cancelled'");
    }
    $cancelled = $stmt3->fetch()['total'] ?? 0;

    // 4. Last 7 Days Sales (Always show last 7 days chart regardless of filter, or maybe useful to leave as context)
    $stmt4 = $pdo->query("
        SELECT DATE(created_at) as date, SUM(total_amount) as total 
        FROM orders 
        WHERE status = 'completed' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        GROUP BY DATE(created_at)
        ORDER BY date ASC
    ");
    $chartData = $stmt4->fetchAll();

    echo json_encode([
        'daily_sales' => $sales, // This key name is now generic 'sales' really
        'products_sold' => $productsSold,
        'cancelled_total' => $cancelled,
        'chart_data' => $chartData
    ]);
}
?>
