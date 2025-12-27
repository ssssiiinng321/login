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

// Graceful check for DB
if (!isset($pdo) || !$pdo) {
    if (!headers_sent()) http_response_code(500);
    echo json_encode(['error' => 'Database connection failed. Check Vercel logs/env vars.']);
    exit;
}

// Helper to get raw input data
function getJsonInput() {
    return json_decode(file_get_contents('php://input'), true);
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $product = $stmt->fetch();
            echo json_encode($product ?: ['error' => 'Product not found']);
        } else {
            $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
            echo json_encode($stmt->fetchAll());
        }
        break;

    case 'POST':
        // Handle both JSON (if used elsewhere) and Multipart Form Data
        $inputData = getJsonInput();
        
        // If JSON input failed, fallback to $_POST
        $name = $inputData['name'] ?? $_POST['name'] ?? null;
        $price = $inputData['price'] ?? $_POST['price'] ?? null;
        $desc = $inputData['description'] ?? $_POST['description'] ?? '';
        $stock = $inputData['stock'] ?? $_POST['stock'] ?? 0;
        $imageUrl = $inputData['image_url'] ?? $_POST['image_url'] ?? '';

        if (!$name || !$price) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            exit;
        }

        // Handle File Upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            
            $fileExt = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array($fileExt, $allowed)) {
                $newFileName = uniqid('prod_', true) . '.' . $fileExt;
                $destPath = $uploadDir . $newFileName;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $destPath)) {
                    $imageUrl = $destPath;
                }
            }
        }
        
        $sql = "INSERT INTO products (name, description, price, stock, image_url) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$name, $desc, $price, $stock, $imageUrl]);
            echo json_encode(['id' => $pdo->lastInsertId(), 'message' => 'Product created', 'image_url' => $imageUrl]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;

    case 'PUT':
        $data = getJsonInput();
        if (!isset($data['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing product ID']);
            exit;
        }

        $fields = [];
        $values = [];
        if (isset($data['name'])) { $fields[] = "name = ?"; $values[] = $data['name']; }
        if (isset($data['description'])) { $fields[] = "description = ?"; $values[] = $data['description']; }
        if (isset($data['price'])) { $fields[] = "price = ?"; $values[] = $data['price']; }
        if (isset($data['stock'])) { $fields[] = "stock = ?"; $values[] = $data['stock']; }
        if (isset($data['image_url'])) { $fields[] = "image_url = ?"; $values[] = $data['image_url']; }

        if (empty($fields)) {
            echo json_encode(['message' => 'No changes provided']);
            exit;
        }

        $values[] = $data['id']; // For WHERE clause
        $sql = "UPDATE products SET " . implode(', ', $fields) . " WHERE id = ?";
        
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);
            echo json_encode(['message' => 'Product updated']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;

    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing product ID']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['message' => 'Product deleted']);
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
