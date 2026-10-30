<?php
/**
 * API Echo Test - Shows exactly what the server receives
 * 
 * This helps diagnose if POST data is reaching the server correctly
 * 
 * Test from browser console:
 * fetch('https://isu-documentrequest.online/ac_controller_system/public/api-echo.php', {
 *   method: 'POST',
 *   headers: {'Content-Type': 'application/json'},
 *   body: JSON.stringify({device_id: 'ESP32_OUTLET_001', api_key: 'TEST_KEY'})
 * }).then(r=>r.json()).then(console.log)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Capture everything
$rawInput = file_get_contents('php://input');
$jsonDecoded = json_decode($rawInput, true);

$response = [
    'test' => 'API Echo',
    'timestamp' => date('Y-m-d H:i:s'),
    'server' => $_SERVER['HTTP_HOST'] ?? 'unknown',
    
    'request' => [
        'method' => $_SERVER['REQUEST_METHOD'],
        'content_type' => $_SERVER['CONTENT_TYPE'] ?? null,
        'content_length' => $_SERVER['CONTENT_LENGTH'] ?? 0,
        'request_uri' => $_SERVER['REQUEST_URI'] ?? null,
        'query_string' => $_SERVER['QUERY_STRING'] ?? null
    ],
    
    'raw_input' => [
        'length' => strlen($rawInput),
        'content' => $rawInput,
        'is_empty' => empty($rawInput)
    ],
    
    'json_decode' => [
        'success' => $jsonDecoded !== null,
        'error' => json_last_error_msg(),
        'data' => $jsonDecoded
    ],
    
    'post_superglobal' => $_POST,
    
    'get_params' => $_GET,
    
    'extracted_from_json' => $jsonDecoded ? [
        'device_id' => $jsonDecoded['device_id'] ?? 'NOT FOUND',
        'api_key_present' => isset($jsonDecoded['api_key']),
        'api_key_length' => isset($jsonDecoded['api_key']) ? strlen($jsonDecoded['api_key']) : 0,
        'api_key_preview' => isset($jsonDecoded['api_key']) ? substr($jsonDecoded['api_key'], 0, 20) . '...' : null
    ] : null,
    
    'verdict' => $jsonDecoded && isset($jsonDecoded['device_id']) && isset($jsonDecoded['api_key'])
        ? '✅ POST data received correctly!'
        : '❌ POST data NOT received correctly'
];

echo json_encode($response, JSON_PRETTY_PRINT);
