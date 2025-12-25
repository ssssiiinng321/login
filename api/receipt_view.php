<?php
require 'db.php';
$id = $_GET['id'] ?? 0;

if (!$id) die("Order ID required");

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order) die("Order not found");

$stmtItems = $pdo->prepare("
    SELECT oi.*, p.name 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
");
$stmtItems->execute([$id]);
$items = $stmtItems->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt #<?php echo $id; ?></title>
    <style>
        body { font-family: 'Courier New', monospace; width: 300px; margin: 0 auto; color: #000; }
        .header { text-align: center; margin-bottom: 20px; }
        .store-name { font-size: 20px; font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 10px 0; }
        .item-row { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .totals { margin-top: 20px; text-align: right; }
        .footer { margin-top: 20px; text-align: center; font-size: 12px; }
        @media print {
            body { width: 100%; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <div class="store-name">YOUR PURPOSE SHOP</div>
        <div>Small Online Shop</div>
        <div>Receipt #<?php echo $id; ?></div>
        <div><?php echo $order['created_at']; ?></div>
        <div>Cashier: <?php echo htmlspecialchars($order['cashier_name'] ?? 'Admin'); ?></div>
    </div>

    <div class="divider"></div>

    <?php foreach ($items as $item): ?>
    <div class="item-row">
        <span><?php echo htmlspecialchars($item['name']); ?> x <?php echo $item['quantity']; ?></span>
        <span>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
    </div>
    <?php endforeach; ?>

    <div class="divider"></div>

    <div class="totals">
        <div><strong>Total: $<?php echo number_format($order['total_amount'], 2); ?></strong></div>
        <div>Method: <?php echo ucfirst($order['payment_method']); ?></div>
    </div>

    <div class="footer">
        Thank you for your purchase!<br>
        Returns accepted within 7 days.
    </div>

</body>
</html>
