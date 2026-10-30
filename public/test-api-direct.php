<?php
/**
 * Direct API Test - Shows Exact Errors
 */

// Enable error display
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define root path
define('ROOT_PATH', dirname(__DIR__));

// Load environment
require_once ROOT_PATH . '/app/helpers/EnvironmentHelper.php';
EnvironmentHelper::load(ROOT_PATH . '/.env');

// Test heartbeat directly
header('Content-Type: application/json');

try {
    // Load all required files manually
    require_once ROOT_PATH . '/app/Database.php';
    require_once ROOT_PATH . '/app/models/Device.php';
    require_once ROOT_PATH . '/app/repositories/DeviceRepository.php';
    require_once ROOT_PATH . '/app/helpers/functions.php';
    
    // Test data
    $deviceId = $_GET['device_id'] ?? 'ESP32_OUTLET_001';
    $apiKey = $_GET['api_key'] ?? 'IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T';
    
    // Find device
    $deviceRepo = new DeviceRepository();
    $device = $deviceRepo->findByDeviceId($deviceId);
    
    if (!$device) {
        echo json_encode([
            'success' => false,
            'error' => 'Device not found',
            'device_id' => $deviceId,
            'hint' => 'Device does not exist in database'
        ]);
        exit;
    }
    
    // Check API key
    if (!hash_equals($device->api_key, $apiKey)) {
        echo json_encode([
            'success' => false,
            'error' => 'Invalid API key',
            'device_id' => $deviceId,
            'api_key_provided' => substr($apiKey, 0, 20) . '...',
            'api_key_in_db' => substr($device->api_key, 0, 20) . '...',
            'hint' => 'API key does not match database'
        ]);
        exit;
    }
    
    // Success!
    echo json_encode([
        'success' => true,
        'message' => 'Authentication successful!',
        'device' => [
            'id' => $device->id,
            'device_id' => $device->device_id,
            'device_name' => $device->device_name,
            'is_active' => $device->is_active,
            'is_online' => $device->is_online,
            'outlet_status' => $device->outlet_status,
            'control_mode' => $device->control_mode
        ],
        'test' => 'Direct API authentication test passed'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Exception: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => explode("\n", $e->getTraceAsString())
    ]);
}
