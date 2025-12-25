<?php

// Define API context
define('IS_API_REQUEST', true);

// Prevent any HTML output
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Custom error handling to ensure JSON output even on fatal errors
function jsonErrorHandler() {
    $error = error_get_last();
    if ($error !== NULL && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: application/json');
        }
        echo json_encode(['error' => 'Fatal Error: ' . $error['message'] . ' in ' . $error['file'] . ':' . $error['line']]);
        exit;
    }
}
register_shutdown_function('jsonErrorHandler');

require_once 'db.php';

header('Content-Type: application/json');

function getJsonInput() {
    return json_decode(file_get_contents('php://input'), true);
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // If getting a specific order, return details + items
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $order = $stmt->fetch();
            
            if ($order) {
                // Fetch items
                $stmtItems = $pdo->prepare("
                    SELECT oi.*, p.name as product_name, p.image_url 
                    FROM order_items oi 
                    JOIN products p ON oi.product_id = p.id 
                    WHERE oi.order_id = ?
                ");
                $stmtItems->execute([$order['id']]);
                $order['items'] = $stmtItems->fetchAll();
                echo json_encode($order);
            } else {
                echo json_encode(['error' => 'Order not found']);
            }
        } else {
            // List all orders
            $stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
            echo json_encode($stmt->fetchAll());
        }
        break;

    case 'POST':
        // Create new order
        $data = getJsonInput();
        
        // Allow customer_name to be optional for POS (default to 'Walk-in Customer')
        $customerName = $data['customer_name'] ?? 'Walk-in Customer';
        $paymentMethod = $data['payment_method'] ?? 'cash';
        
        if (!isset($data['items']) || !is_array($data['items'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing items']);
            exit;
        }

        try {
            $pdo->beginTransaction();

            $totalAmount = 0;
            $orderItems = [];

            foreach ($data['items'] as $item) {
                // Lock the row for update to prevent race conditions
                $stmt = $pdo->prepare("SELECT price, stock FROM products WHERE id = ? FOR UPDATE");
                $stmt->execute([$item['product_id']]);
                $product = $stmt->fetch();

                if (!$product) {
                    throw new Exception("Product ID {$item['product_id']} not found");
                }
                
                if ($product['stock'] < $item['quantity']) {
                    throw new Exception("Insufficient stock for Product ID {$item['product_id']}");
                }
                
                $price = $product['price'];
                $qty = $item['quantity'];
                
                $totalAmount += $price * $qty;
                $orderItems[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $qty,
                    'price' => $price
                ];
                
                // Deduct stock
                $stmtUpdate = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                $stmtUpdate->execute([$qty, $item['product_id']]);
            }

            // Insert Order
            $stmtOrder = $pdo->prepare("INSERT INTO orders (customer_name, total_amount, status, payment_method) VALUES (?, ?, 'completed', ?)");
            $stmtOrder->execute([$customerName, $totalAmount, $paymentMethod]);
            $orderId = $pdo->lastInsertId();

            // Insert Items
            $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($orderItems as $item) {
                $stmtItem->execute([$orderId, $item['product_id'], $item['quantity'], $item['price']]);
            }

            $pdo->commit();
            echo json_encode(['id' => $orderId, 'message' => 'Order completed', 'total' => $totalAmount]);

        } catch (Exception $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;

    case 'PUT':
        // Update status
        $data = getJsonInput();
        if (!isset($data['id'], $data['status'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing ID or status']);
            exit;
        }

        $validStatuses = ['pending', 'completed', 'cancelled'];
        if (!in_array($data['status'], $validStatuses)) {
             http_response_code(400);
             echo json_encode(['error' => 'Invalid status']);
             exit;
        }

        try {
            $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->execute([$data['status'], $data['id']]);
            echo json_encode(['message' => 'Order status updated']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>
