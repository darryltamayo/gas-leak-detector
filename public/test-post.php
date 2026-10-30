<?php
/**
 * Test POST request handling
 */

header('Content-Type: application/json');

// Get raw POST data
$rawInput = file_get_contents('php://input');
$jsonData = json_decode($rawInput, true);

// Get from $_POST
$postData = $_POST;

// Test response
$response = [
    'test' => 'POST endpoint working',
    'timestamp' => date('Y-m-d H:i:s'),
    'method' => $_SERVER['REQUEST_METHOD'],
    'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'not set',
    'raw_input_length' => strlen($rawInput),
    'raw_input_preview' => substr($rawInput, 0, 200),
    'json_decoded' => $jsonData,
    'post_data' => $postData,
    'device_id_in_json' => $jsonData['device_id'] ?? 'NOT FOUND',
    'api_key_in_json' => isset($jsonData['api_key']) ? 'PRESENT (' . strlen($jsonData['api_key']) . ' chars)' : 'NOT FOUND'
];

echo json_encode($response, JSON_PRETTY_PRINT);
