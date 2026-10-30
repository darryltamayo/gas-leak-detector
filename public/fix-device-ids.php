<?php
/**
 * Fix Device IDs - Update NULL or empty device IDs with auto-generated ones
 * 
 * Access: https://isu-documentrequest.online/ac_controller_system/public/fix-device-ids.php
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
    <title>Fix Device IDs</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif;
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background: #f5f5f7;
        }
        h1 {
            color: #1d1d1f;
            border-bottom: 3px solid #2d7a5b;
            padding-bottom: 10px;
        }
        .section {
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
        .warning {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
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
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #2d7a5b;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 10px 5px;
        }
        .btn:hover {
            background: #25694b;
        }
        .btn-danger {
            background: #dc2626;
        }
        .btn-danger:hover {
            background: #b91c1c;
        }
    </style>
</head>
<body>
    <h1>🔧 Fix Device IDs</h1>
    
    <?php
    try {
        $db = Database::getInstance();
        $repo = new DeviceRepository();
        
        // Check for devices with NULL or empty device_id
        echo '<div class="section">';
        echo '<h2>Step 1: Check Devices</h2>';
        
        $stmt = $db->query("SELECT id, device_name, device_id, sim_card_number FROM iot_outlet_devices");
        $devices = $stmt->fetchAll();
        
        if (empty($devices)) {
            echo '<div class="warning">⚠️ No devices found in database!</div>';
            echo '<p>You need to create a device first through the admin panel.</p>';
            echo '<a href="../device/create" class="btn">Create Device</a>';
        } else {
            echo '<div class="info">📋 Found ' . count($devices) . ' device(s)</div>';
            
            $needsFix = [];
            foreach ($devices as $device) {
                if (empty($device['device_id']) || $device['device_id'] === null) {
                    $needsFix[] = $device;
                }
            }
            
            if (empty($needsFix)) {
                echo '<div class="success">✅ All devices have valid Device IDs!</div>';
                
                echo '<table>';
                echo '<tr><th>ID</th><th>Device Name</th><th>Device ID</th><th>SIM Card</th></tr>';
                foreach ($devices as $d) {
                    echo '<tr>';
                    echo '<td>' . $d['id'] . '</td>';
                    echo '<td>' . htmlspecialchars($d['device_name'] ?: 'N/A') . '</td>';
                    echo '<td><strong>' . htmlspecialchars($d['device_id']) . '</strong></td>';
                    echo '<td>' . htmlspecialchars($d['sim_card_number'] ?: 'N/A') . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
                
                echo '<p>No fixes needed! All devices are properly configured.</p>';
            } else {
                echo '<div class="warning">⚠️ Found ' . count($needsFix) . ' device(s) with missing Device IDs</div>';
                
                echo '<table>';
                echo '<tr><th>ID</th><th>Device Name</th><th>Device ID</th><th>SIM Card</th></tr>';
                foreach ($needsFix as $d) {
                    echo '<tr>';
                    echo '<td>' . $d['id'] . '</td>';
                    echo '<td>' . htmlspecialchars($d['device_name'] ?: 'N/A') . '</td>';
                    echo '<td><em style="color: #dc2626;">MISSING/NULL</em></td>';
                    echo '<td>' . htmlspecialchars($d['sim_card_number'] ?: 'N/A') . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
                
                // Show fix option
                if (isset($_GET['fix']) && $_GET['fix'] === 'yes') {
                    echo '</div><div class="section">';
                    echo '<h2>Step 2: Fixing Devices...</h2>';
                    
                    $fixed = 0;
                    $failed = 0;
                    
                    foreach ($needsFix as $device) {
                        $newDeviceId = $repo->generateDeviceId();
                        
                        $stmt = $db->prepare("UPDATE iot_outlet_devices SET device_id = ? WHERE id = ?");
                        $success = $stmt->execute([$newDeviceId, $device['id']]);
                        
                        if ($success) {
                            echo '<div class="success">';
                            echo '✅ Fixed device ID: ' . htmlspecialchars($device['device_name']);
                            echo ' → <strong>' . htmlspecialchars($newDeviceId) . '</strong>';
                            echo '</div>';
                            $fixed++;
                        } else {
                            echo '<div class="error">';
                            echo '❌ Failed to fix device ID: ' . htmlspecialchars($device['device_name']);
                            echo '</div>';
                            $failed++;
                        }
                    }
                    
                    echo '<div class="success">';
                    echo '<h3>Summary</h3>';
                    echo '<p>✅ Fixed: ' . $fixed . ' device(s)</p>';
                    if ($failed > 0) {
                        echo '<p>❌ Failed: ' . $failed . ' device(s)</p>';
                    }
                    echo '</div>';
                    
                    echo '<a href="fix-device-ids.php" class="btn">Check Again</a>';
                    echo '<a href="../device" class="btn">View Devices</a>';
                    
                } else {
                    echo '<div class="warning">';
                    echo '<h3>⚠️ Ready to Fix</h3>';
                    echo '<p>Click the button below to auto-generate Device IDs for all devices that are missing them.</p>';
                    echo '<p><strong>This will:</strong></p>';
                    echo '<ul>';
                    echo '<li>Generate unique Device IDs (ESP32_OUTLET_001, ESP32_OUTLET_002, etc.)</li>';
                    echo '<li>Update the database with the new IDs</li>';
                    echo '<li>Not affect devices that already have valid IDs</li>';
                    echo '</ul>';
                    echo '<a href="?fix=yes" class="btn">Fix Device IDs Now</a>';
                    echo '</div>';
                }
            }
        }
        
        echo '</div>';
        
    } catch (Exception $e) {
        echo '<div class="error">';
        echo '<h2>❌ Error</h2>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '</div>';
    }
    ?>
    
    <div class="section">
        <h2>Next Steps</h2>
        <p>After fixing device IDs:</p>
        <ol>
            <li>Go to Device Management to view your devices</li>
            <li>Click "View Configuration" on each device to get the API key</li>
            <li>Update your ESP32 code with the Device ID and API key</li>
            <li>Upload the code to your ESP32</li>
        </ol>
        <a href="../device" class="btn">Go to Device Management</a>
    </div>
</body>
</html>
