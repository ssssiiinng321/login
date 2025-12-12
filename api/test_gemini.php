<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

// Load Key
$apiKey = getenv('GEMINI_API_KEY');
if (!$apiKey && file_exists('secrets.php')) {
    include 'secrets.php';
}

if (!$apiKey) {
    die(json_encode(['error' => 'No API Key found']));
}

$url = "https://generativelanguage.googleapis.com/v1beta/models?key=" . $apiKey;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);

echo $response;
?>
