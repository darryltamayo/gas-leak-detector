<?php
/**
 * DIRECT POST TEST - Test if POST data is received correctly
 * This file tests POST data handling without routing through index.php
 * 
 * To test from browser console:
 * fetch('https://isu-documentrequest.online/ac_controller_system/public/direct-post-test.php', {
 *   method: 'POST',
 *   headers: {'Content-Type': 'application/json'},
 *   body: JSON.stringify({device_id: 'ESP32_OUTLET_001', api_key: 'IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T'})
 * }).then(r=>r.json()).then(console.log)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle OPTIONS request (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get raw POST data
$rawInput = file_get_contents('php://input');
$jsonData = json_decode($rawInput, true);

// Comprehensive diagnostics
$response = [
    'status' => 'direct_post_test',
    'timestamp' => date('Y-m-d H:i:s'),
    'request_method' => $_SERVER['REQUEST_METHOD'],
    'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'not set',
    'content_length' => $_SERVER['CONTENT_LENGTH'] ?? 0,
    'raw_input' => [
        'length' => strlen($rawInput),
        'preview' => substr($rawInput, 0, 200),
        'full' => $rawInput
    ],
    'json_decode' => [
        'success' => $jsonData !== null,
        'data' => $jsonData,
        'error' => json_last_error_msg()
    ],
    'post_superglobal' => $_POST,
    'extracted_values' => [
        'device_id' => $jsonData['device_id'] ?? 'NOT FOUND',
        'api_key_present' => isset($jsonData['api_key']),
        'api_key_length' => isset($jsonData['api_key']) ? strlen($jsonData['api_key']) : 0,
        'api_key_preview' => isset($jsonData['api_key']) ? substr($jsonData['api_key'], 0, 20) . '...' : 'NOT FOUND'
    ]
];

echo json_encode($response, JSON_PRETTY_PRINT);
