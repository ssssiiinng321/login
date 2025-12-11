<?php
require_once 'db.php'; // Ensure we have the $pdo connection

class PdoSessionHandler implements SessionHandlerInterface
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function open($savePath, $sessionName): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read($id): string|false
    {
        $stmt = $this->pdo->prepare("SELECT data FROM sessions WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['data'] : '';
    }

    public function write($id, $data): bool
    {
        $timestamp = time();
        $stmt = $this->pdo->prepare("REPLACE INTO sessions (id, data, timestamp) VALUES (:id, :data, :timestamp)");
        return $stmt->execute([
            ':id' => $id,
            ':data' => $data,
            ':timestamp' => $timestamp
        ]);
    }

    public function destroy($id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM sessions WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function gc($maxlifetime): int|false
    {
        $old = time() - $maxlifetime;
        $stmt = $this->pdo->prepare("DELETE FROM sessions WHERE timestamp < :old");
        $stmt->execute([':old' => $old]);
        return $stmt->rowCount();
    }
}

// Initialize the session handler
$handler = new PdoSessionHandler($pdo);
session_set_save_handler($handler, true);

// Cookie settings for Vercel/Cross-site security
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'] ?? null, 
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();
?>
