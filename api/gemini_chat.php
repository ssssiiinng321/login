<?php
ini_set('display_errors', 0); // Suppress errors in production, handle via JSON
header('Content-Type: application/json');

require 'session.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get the user's message
$data = json_decode(file_get_contents('php://input'), true);
$message = $data['message'] ?? '';

if (empty($message)) {
    http_response_code(400);
    echo json_encode(['error' => 'Empty message']);
    exit;
}

// Gemini API Configuration
// Gemini API Configuration
$apiKey = getenv('GEMINI_API_KEY');
if (!$apiKey && file_exists('secrets.php')) {
    include 'secrets.php';
}
$apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=' . $apiKey;

// Prepare payload for Gemini
$payload = [
    'contents' => [
        [
            'parts' => [
                ['text' => $message]
            ]
        ]
    ]
];

// Initialize cURL
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
// XAMPP often has SSL issues, disabling verification for local testing
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// Execute request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);

curl_close($ch);

if ($curlError) {
    http_response_code(500);
    echo json_encode(['error' => 'Connection error: ' . $curlError]);
    exit;
}

if ($httpCode !== 200) {
    http_response_code($httpCode);
    $errorDetails = json_decode($response, true)['error']['message'] ?? $response;
    echo json_encode(['error' => 'API Error (' . $httpCode . '): ' . $errorDetails]);
    exit;
}

if ($httpCode !== 200) {
    http_response_code($httpCode);
    echo json_encode(['error' => 'API Error', 'details' => json_decode($response)]);
    exit;
}

// Parse Gemini response
$responseData = json_decode($response, true);
$aiReply = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? 'Sorry, I could not understand that.';

echo json_encode(['reply' => $aiReply]);
?>
