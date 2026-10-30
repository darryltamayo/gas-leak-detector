<?php
/**
 * Debug page to check device data
 */

// Define root path
define('ROOT_PATH', dirname(__DIR__));

// Load environment
require_once ROOT_PATH . '/app/helpers/EnvironmentHelper.php';
EnvironmentHelper::load(ROOT_PATH . '/.env');

// Start session
session_start();

// Autoloader
spl_autoload_register(function ($class) {
    $paths = [
        ROOT_PATH . '/app/controllers/' . $class . '.php',
        ROOT_PATH . '/app/models/' . $class . '.php',
        ROOT_PATH . '/app/repositories/' . $class . '.php',
        ROOT_PATH . '/app/services/' . $class . '.php',
        ROOT_PATH . '/app/middleware/' . $class . '.php',
        ROOT_PATH . '/app/' . $class . '.php',
    ];
    
    foreach ($paths as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Load helper functions
require_once ROOT_PATH . '/app/helpers/functions.php';

// Get devices
try {
    $deviceRepo = new DeviceRepository();
    $devices = $deviceRepo->getAll();
    
    echo '<html>';
    echo '<head><title>Device Debug Info</title>';
    echo '<style>body { font-family: monospace; padding: 20px; background: #f5f5f5; }';
    echo 'table { background: white; border-collapse: collapse; width: 100%; margin: 20px 0; }';
    echo 'th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }';
    echo 'th { background: #2D7A5B; color: white; }';
    echo 'tr:hover { background: #f0f0f0; }';
    echo 'a { color: #2D7A5B; text-decoration: none; font-weight: bold; }';
    echo 'a:hover { text-decoration: underline; }';
    echo '</style></head>';
    echo '<body>';
    echo '<h1>Device Debug Information</h1>';
    echo '<p>Total devices found: ' . count($devices) . '</p>';
    
    if (empty($devices)) {
        echo '<p style="color: red;">No devices found in database!</p>';
    } else {
        echo '<table>';
        echo '<tr><th>ID</th><th>Device Name</th><th>Device ID</th><th>Type of ID</th><th>Edit URL</th><th>Test Link</th></tr>';
        
        foreach ($devices as $device) {
            $idType = gettype($device->id);
            $isNumeric = is_numeric($device->id) ? 'YES' : 'NO';
            $editUrl = url('device/edit/' . $device->id);
            
            echo '<tr>';
            echo '<td>' . htmlspecialchars(var_export($device->id, true)) . '</td>';
            echo '<td>' . htmlspecialchars($device->device_name) . '</td>';
            echo '<td>' . htmlspecialchars($device->device_id) . '</td>';
            echo '<td>' . $idType . ' (Numeric: ' . $isNumeric . ')</td>';
            echo '<td>' . htmlspecialchars($editUrl) . '</td>';
            echo '<td><a href="' . $editUrl . '" target="_blank">Test Edit Link</a></td>';
            echo '</tr>';
        }
        
        echo '</table>';
    }
    
    echo '<hr>';
    echo '<h2>URL Configuration</h2>';
    echo '<p><strong>Base URL:</strong> ' . url() . '</p>';
    echo '<p><strong>Script Name:</strong> ' . ($_SERVER['SCRIPT_NAME'] ?? 'N/A') . '</p>';
    echo '<p><strong>Request URI:</strong> ' . ($_SERVER['REQUEST_URI'] ?? 'N/A') . '</p>';
    echo '<p><strong>HTTP Host:</strong> ' . ($_SERVER['HTTP_HOST'] ?? 'N/A') . '</p>';
    
    echo '<hr>';
    echo '<p><a href="' . url('device') . '">Go to Device List</a></p>';
    
    echo '</body></html>';
    
} catch (Exception $e) {
    echo '<h1>Error</h1>';
    echo '<p style="color: red;">' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
}
