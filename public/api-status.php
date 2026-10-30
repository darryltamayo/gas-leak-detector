<?php
/**
 * Simple API Status Check
 * Access directly: /ac_controller_system/api-status.php
 */

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$status = [
    'api_status' => 'OK',
    'timestamp' => date('Y-m-d H:i:s'),
    'tests' => []
];

// Test 1: Check ROOT_PATH
define('ROOT_PATH', dirname(__DIR__));
$status['tests']['root_path'] = [
    'exists' => is_dir(ROOT_PATH),
    'path' => ROOT_PATH
];

// Test 2: Check .env file
$envFile = ROOT_PATH . '/.env';
$status['tests']['env_file'] = [
    'exists' => file_exists($envFile),
    'readable' => is_readable($envFile)
];

// Test 3: Check routes/api.php
$apiFile = ROOT_PATH . '/routes/api.php';
$status['tests']['api_file'] = [
    'exists' => file_exists($apiFile),
    'readable' => is_readable($apiFile),
    'size' => file_exists($apiFile) ? filesize($apiFile) : 0
];

// Test 4: Check database connection
try {
    require_once ROOT_PATH . '/app/helpers/EnvironmentHelper.php';
    EnvironmentHelper::load($envFile);
    require_once ROOT_PATH . '/app/Database.php';
    $db = Database::getInstance();
    $status['tests']['database'] = [
        'connected' => true,
        'database' => $_ENV['DB_NAME'] ?? 'unknown'
    ];
} catch (Exception $e) {
    $status['tests']['database'] = [
        'connected' => false,
        'error' => $e->getMessage()
    ];
}

// Test 5: Check if Device table exists and has data
try {
    if (isset($db)) {
        $stmt = $db->query("SELECT COUNT(*) as count FROM iot_outlet_devices");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $status['tests']['devices_table'] = [
            'exists' => true,
            'device_count' => $result['count']
        ];
        
        // Get device details
        $stmt = $db->query("SELECT device_id, device_name, api_key FROM iot_outlet_devices LIMIT 1");
        $device = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($device) {
            $status['tests']['sample_device'] = [
                'device_id' => $device['device_id'],
                'device_name' => $device['device_name'],
                'api_key_preview' => substr($device['api_key'], 0, 10) . '...' . substr($device['api_key'], -10)
            ];
        }
    }
} catch (Exception $e) {
    $status['tests']['devices_table'] = [
        'exists' => false,
        'error' => $e->getMessage()
    ];
}

// Test 6: Check if ApiRouter class exists
try {
    if (file_exists($apiFile)) {
        require_once $apiFile;
        $status['tests']['api_router_class'] = [
            'exists' => class_exists('ApiRouter'),
            'file_loaded' => true
        ];
    }
} catch (Exception $e) {
    $status['tests']['api_router_class'] = [
        'exists' => false,
        'error' => $e->getMessage()
    ];
}

// Overall status
$allPassed = true;
foreach ($status['tests'] as $test => $result) {
    if (isset($result['exists']) && !$result['exists']) {
        $allPassed = false;
        break;
    }
    if (isset($result['connected']) && !$result['connected']) {
        $allPassed = false;
        break;
    }
}

$status['overall'] = $allPassed ? 'READY' : 'ISSUES_FOUND';
$status['message'] = $allPassed ? 'All systems operational' : 'Some issues detected, check tests array';

echo json_encode($status, JSON_PRETTY_PRINT);
