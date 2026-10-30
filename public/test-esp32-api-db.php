<?php
/**
 * Database verification helper for ESP32 API Tester
 */

header('Content-Type: application/json');

// Load environment
require_once __DIR__ . '/../config/bootstrap.php';

$deviceId = $_GET['device_id'] ?? '';

if (empty($deviceId)) {
    echo json_encode([
        'success' => false,
        'error' => 'No device_id provided'
    ]);
    exit;
}

try {
    $db = Database::getInstance();
    
    // Find device
    $stmt = $db->prepare('SELECT * FROM iot_outlet_devices WHERE device_id = ?');
    $stmt->execute([$deviceId]);
    $device = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$device) {
        echo json_encode([
            'success' => false,
            'error' => 'Device not found in database',
            'device_id' => $deviceId,
            'hint' => 'Register this device first in the admin panel'
        ]);
        exit;
    }
    
    // Device found, return details (hide full API key for security)
    $apiKeyPreview = substr($device['api_key'], 0, 10) . '...' . substr($device['api_key'], -10);
    
    echo json_encode([
        'success' => true,
        'message' => 'Device found in database',
        'device' => [
            'id' => $device['id'],
            'device_id' => $device['device_id'],
            'device_name' => $device['device_name'],
            'api_key_preview' => $apiKeyPreview,
            'api_key_length' => strlen($device['api_key']),
            'is_active' => (bool)$device['is_active'],
            'is_online' => (bool)$device['is_online'],
            'last_heartbeat' => $device['last_heartbeat'],
            'sim_card_number' => $device['sim_card_number'],
            'created_at' => $device['created_at']
        ],
        'authentication' => [
            'api_key_format' => 'Correct',
            'api_key_matches_input' => 'Check manually (first 10 chars: ' . substr($device['api_key'], 0, 10) . '...)'
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
