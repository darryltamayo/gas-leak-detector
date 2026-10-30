<?php
/**
 * Test Device Data Display
 * 
 * This file checks if device data is being retrieved correctly
 * Access: https://isu-documentrequest.online/ac_controller_system/public/test-device-data.php
 */

// Load dependencies
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/app/helpers/EnvironmentHelper.php';
EnvironmentHelper::load(ROOT_PATH . '/.env');

require_once ROOT_PATH . '/app/Database.php';
require_once ROOT_PATH . '/app/models/Device.php';
require_once ROOT_PATH . '/app/repositories/DeviceRepository.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Device Data Test</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif;
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background: #f5f5f7;
        }
        h1 {
            color: #1d1d1f;
            border-bottom: 3px solid #2d7a5b;
            padding-bottom: 10px;
        }
        .test-section {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .success {
            background: #d1fae5;
            border-left: 4px solid #2d7a5b;
            padding: 15px;
            margin: 10px 0;
        }
        .error {
            background: #fee2e2;
            border-left: 4px solid #dc2626;
            padding: 15px;
            margin: 10px 0;
        }
        .info {
            background: #dbeafe;
            border-left: 4px solid #1e40af;
            padding: 15px;
            margin: 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e5e7;
        }
        th {
            background: #2d7a5b;
            color: white;
            font-weight: 600;
        }
        tr:hover {
            background: #f5f5f7;
        }
        code {
            background: #f5f5f7;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: monospace;
        }
        pre {
            background: #1d1d1f;
            color: #d4d4d4;
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <h1>🔧 Device Data Test</h1>
    
    <?php
    try {
        // Test 1: Database Connection
        echo '<div class="test-section">';
        echo '<h2>Test 1: Database Connection</h2>';
        
        $db = Database::getInstance();
        echo '<div class="success">✅ Database connection successful!</div>';
        
        // Test 2: Raw SQL Query
        echo '</div><div class="test-section">';
        echo '<h2>Test 2: Raw SQL Query</h2>';
        
        $stmt = $db->query('SELECT * FROM iot_outlet_devices ORDER BY created_at DESC');
        $rawDevices = $stmt->fetchAll();
        
        if (empty($rawDevices)) {
            echo '<div class="error">❌ No devices found in database!</div>';
            echo '<div class="info">💡 Run this SQL to add a test device:';
            echo '<pre>INSERT INTO iot_outlet_devices (device_name, device_id, api_key, sim_card_number, outlet_status, control_mode, is_active) 
VALUES (\'Main Outlet\', \'ESP32_OUTLET_001\', \'IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T\', \'09605682714\', \'off\', \'manual\', 1);</pre>';
            echo '</div>';
        } else {
            echo '<div class="success">✅ Found ' . count($rawDevices) . ' device(s) in database</div>';
            
            echo '<table>';
            echo '<tr><th>ID</th><th>Device Name</th><th>Device ID</th><th>SIM Card</th><th>Status</th><th>Mode</th><th>Online</th></tr>';
            foreach ($rawDevices as $row) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['id']) . '</td>';
                echo '<td>' . htmlspecialchars($row['device_name'] ?: 'NULL') . '</td>';
                echo '<td>' . htmlspecialchars($row['device_id'] ?: 'NULL') . '</td>';
                echo '<td>' . htmlspecialchars($row['sim_card_number'] ?: 'NULL') . '</td>';
                echo '<td>' . htmlspecialchars($row['outlet_status']) . '</td>';
                echo '<td>' . htmlspecialchars($row['control_mode']) . '</td>';
                echo '<td>' . ($row['is_online'] ? '🟢 Yes' : '🔴 No') . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        
        // Test 3: Repository Method
        echo '</div><div class="test-section">';
        echo '<h2>Test 3: DeviceRepository Method</h2>';
        
        $repo = new DeviceRepository();
        $devices = $repo->getAll();
        
        if (empty($devices)) {
            echo '<div class="error">❌ DeviceRepository->getAll() returned empty array!</div>';
        } else {
            echo '<div class="success">✅ DeviceRepository->getAll() returned ' . count($devices) . ' device(s)</div>';
            
            echo '<h3>Device Objects:</h3>';
            foreach ($devices as $index => $device) {
                echo '<div style="background: #f5f5f7; padding: 15px; margin: 10px 0; border-radius: 8px;">';
                echo '<h4>Device #' . ($index + 1) . '</h4>';
                echo '<table style="margin: 10px 0;">';
                echo '<tr><td><strong>ID:</strong></td><td>' . ($device->id ?: 'NULL') . '</td></tr>';
                echo '<tr><td><strong>Device Name:</strong></td><td>' . htmlspecialchars($device->device_name ?: 'NULL') . '</td></tr>';
                echo '<tr><td><strong>Device ID:</strong></td><td>' . htmlspecialchars($device->device_id ?: 'NULL') . '</td></tr>';
                echo '<tr><td><strong>SIM Card:</strong></td><td>' . htmlspecialchars($device->sim_card_number ?: 'NULL') . '</td></tr>';
                echo '<tr><td><strong>Outlet Status:</strong></td><td>' . htmlspecialchars($device->outlet_status) . '</td></tr>';
                echo '<tr><td><strong>Control Mode:</strong></td><td>' . htmlspecialchars($device->control_mode) . '</td></tr>';
                echo '<tr><td><strong>Online:</strong></td><td>' . ($device->is_online ? 'Yes ✅' : 'No ❌') . '</td></tr>';
                echo '<tr><td><strong>Last Heartbeat:</strong></td><td>' . ($device->last_heartbeat ?: 'Never') . '</td></tr>';
                echo '<tr><td><strong>IP Address:</strong></td><td>' . ($device->ip_address ?: 'Unknown') . '</td></tr>';
                echo '</table>';
                echo '</div>';
            }
        }
        
        // Test 4: getAllWithOfflineCheck
        echo '</div><div class="test-section">';
        echo '<h2>Test 4: getAllWithOfflineCheck Method</h2>';
        
        $devicesWithCheck = $repo->getAllWithOfflineCheck();
        
        if (empty($devicesWithCheck)) {
            echo '<div class="error">❌ getAllWithOfflineCheck() returned empty array!</div>';
        } else {
            echo '<div class="success">✅ getAllWithOfflineCheck() returned ' . count($devicesWithCheck) . ' device(s)</div>';
            
            echo '<table>';
            echo '<tr><th>Device Name</th><th>Device ID</th><th>SIM</th><th>Status</th><th>Online</th></tr>';
            foreach ($devicesWithCheck as $dev) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($dev->device_name ?: 'NULL') . '</td>';
                echo '<td>' . htmlspecialchars($dev->device_id ?: 'NULL') . '</td>';
                echo '<td>' . htmlspecialchars($dev->sim_card_number ?: 'NULL') . '</td>';
                echo '<td>' . htmlspecialchars($dev->outlet_status) . '</td>';
                echo '<td>' . ($dev->is_online ? '🟢' : '🔴') . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        
        // Test 5: Object Properties
        echo '</div><div class="test-section">';
        echo '<h2>Test 5: First Device Properties (if exists)</h2>';
        
        if (!empty($devices)) {
            $firstDevice = $devices[0];
            echo '<div class="info">';
            echo '<h3>Testing property access methods:</h3>';
            echo '<p><code>$device->device_name</code> = <strong>' . htmlspecialchars($firstDevice->device_name ?: 'NULL') . '</strong></p>';
            echo '<p><code>$device->device_id</code> = <strong>' . htmlspecialchars($firstDevice->device_id ?: 'NULL') . '</strong></p>';
            echo '<p><code>htmlspecialchars($device->device_name)</code> = <strong>' . htmlspecialchars($firstDevice->device_name ?: 'NULL') . '</strong></p>';
            echo '</div>';
            
            echo '<h3>Full Object Dump:</h3>';
            echo '<pre>' . print_r($firstDevice, true) . '</pre>';
        } else {
            echo '<div class="error">❌ No devices to test properties</div>';
        }
        
        echo '</div>';
        
    } catch (Exception $e) {
        echo '<div class="error">';
        echo '<h2>❌ Error</h2>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        echo '</div>';
    }
    ?>
    
    <div class="test-section">
        <h2>Summary</h2>
        <p>If all tests show ✅ green checkmarks but the outlet page still doesn't show device names:</p>
        <ol>
            <li>Check browser console (F12) for JavaScript errors</li>
            <li>View page source of outlet page - search for "device-card-selectable-outlet"</li>
            <li>Check if CSS is hiding the text (inspect element)</li>
            <li>Clear browser cache and hard refresh (Ctrl+F5)</li>
        </ol>
    </div>
</body>
</html>
