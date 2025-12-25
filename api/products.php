
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
        $data = getJsonInput();
        if (!isset($data['name'], $data['price'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            exit;
        }
        
        $sql = "INSERT INTO products (name, description, price, stock, image_url) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([
                $data['name'], 
                $data['description'] ?? '', 
                $data['price'], 
                $data['stock'] ?? 0, 
                $data['image_url'] ?? ''
            ]);
            echo json_encode(['id' => $pdo->lastInsertId(), 'message' => 'Product created']);
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
