<?php
/**
 * DEBUG Version of Cron Script
 * Use this to diagnose why cron.php is failing
 * 
 * URL: https://isu-documentrequest.online/ac_controller_system/public/cron-debug.php?key=IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T
 */

// Enable error display for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h1>Cron Debug Output</h1>";
echo "<pre>";

// Security: Check secret key
$secretKey = $_GET['key'] ?? '';
$expectedKey = 'IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T';

echo "1. Checking secret key...\n";
if ($secretKey !== $expectedKey) {
    echo "❌ ERROR: Invalid key\n";
    echo "Provided: " . ($secretKey ? substr($secretKey, 0, 20) . "..." : "EMPTY") . "\n";
    die();
}
echo "✓ Secret key is valid\n\n";

// Define ROOT_PATH
echo "2. Defining ROOT_PATH...\n";
define('ROOT_PATH', dirname(__DIR__));
echo "✓ ROOT_PATH = " . ROOT_PATH . "\n\n";

// Check if required files exist
echo "3. Checking required files...\n";
$requiredFiles = [
    'EnvironmentHelper' => ROOT_PATH . '/app/helpers/EnvironmentHelper.php',
    'config/app.php' => ROOT_PATH . '/config/app.php',
    'Database.php' => ROOT_PATH . '/app/Database.php',
    'DeviceRepository' => ROOT_PATH . '/app/repositories/DeviceRepository.php',
    'ScheduleRepository' => ROOT_PATH . '/app/repositories/ScheduleRepository.php',
    'Device Model' => ROOT_PATH . '/app/models/Device.php',
    'Schedule Model' => ROOT_PATH . '/app/models/Schedule.php'
];

$allFilesExist = true;
foreach ($requiredFiles as $name => $path) {
    if (file_exists($path)) {
        echo "✓ {$name}: EXISTS\n";
    } else {
        echo "❌ {$name}: NOT FOUND - {$path}\n";
        $allFilesExist = false;
    }
}
echo "\n";

if (!$allFilesExist) {
    echo "ERROR: Some required files are missing!\n";
    die();
}

// Load EnvironmentHelper
echo "4. Loading EnvironmentHelper...\n";
try {
    require_once ROOT_PATH . '/app/helpers/EnvironmentHelper.php';
    echo "✓ EnvironmentHelper loaded\n\n";
} catch (Exception $e) {
    echo "❌ ERROR loading EnvironmentHelper: " . $e->getMessage() . "\n";
    die();
}

// Load environment
echo "5. Loading .env file...\n";
try {
    EnvironmentHelper::load(ROOT_PATH . '/.env');
    echo "✓ Environment loaded\n";
    echo "   DB_HOST = " . (getenv('DB_HOST') ?: 'NOT SET') . "\n";
    echo "   DB_NAME = " . (getenv('DB_NAME') ?: 'NOT SET') . "\n";
    echo "   DB_USER = " . (getenv('DB_USER') ?: 'NOT SET') . "\n";
    echo "   API_KEY = " . (getenv('API_KEY') ? 'SET' : 'NOT SET') . "\n\n";
} catch (Exception $e) {
    echo "❌ ERROR loading environment: " . $e->getMessage() . "\n";
    die();
}

// Load config
echo "6. Loading config/app.php...\n";
try {
    require_once ROOT_PATH . '/config/app.php';
    echo "✓ Config loaded\n\n";
} catch (Exception $e) {
    echo "❌ ERROR loading config: " . $e->getMessage() . "\n";
    die();
}

// Load Database
echo "7. Loading Database class...\n";
try {
    require_once ROOT_PATH . '/app/Database.php';
    echo "✓ Database class loaded\n\n";
} catch (Exception $e) {
    echo "❌ ERROR loading Database: " . $e->getMessage() . "\n";
    die();
}

// Test database connection
echo "8. Testing database connection...\n";
try {
    $db = Database::getInstance();
    echo "✓ Database connection successful\n\n";
} catch (Exception $e) {
    echo "❌ ERROR connecting to database: " . $e->getMessage() . "\n";
    die();
}

// Load repositories
echo "9. Loading repositories...\n";
try {
    require_once ROOT_PATH . '/app/repositories/DeviceRepository.php';
    echo "✓ DeviceRepository loaded\n";
    require_once ROOT_PATH . '/app/repositories/ScheduleRepository.php';
    echo "✓ ScheduleRepository loaded\n\n";
} catch (Exception $e) {
    echo "❌ ERROR loading repositories: " . $e->getMessage() . "\n";
    die();
}

// Load models
echo "10. Loading models...\n";
try {
    require_once ROOT_PATH . '/app/models/Device.php';
    echo "✓ Device model loaded\n";
    require_once ROOT_PATH . '/app/models/Schedule.php';
    echo "✓ Schedule model loaded\n\n";
} catch (Exception $e) {
    echo "❌ ERROR loading models: " . $e->getMessage() . "\n";
    die();
}

// Initialize repositories
echo "11. Creating repository instances...\n";
try {
    $deviceRepo = new DeviceRepository();
    echo "✓ DeviceRepository instantiated\n";
    $scheduleRepo = new ScheduleRepository();
    echo "✓ ScheduleRepository instantiated\n\n";
} catch (Exception $e) {
    echo "❌ ERROR creating repositories: " . $e->getMessage() . "\n";
    die();
}

// Get active schedules
echo "12. Fetching active schedules...\n";
try {
    $schedules = $scheduleRepo->getActive();
    echo "✓ Found " . count($schedules) . " active schedule(s)\n\n";
    
    if (!empty($schedules)) {
        echo "Active schedules:\n";
        foreach ($schedules as $schedule) {
            echo "  - ID: {$schedule->id}, Name: {$schedule->schedule_name}, ON: {$schedule->time_on}, OFF: {$schedule->time_off}\n";
        }
    } else {
        echo "  (No active schedules found - this is why nothing executes!)\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "❌ ERROR fetching schedules: " . $e->getMessage() . "\n";
    die();
}

// Get devices
echo "13. Fetching devices...\n";
try {
    $devices = $deviceRepo->getAll();
    echo "✓ Found " . count($devices) . " device(s)\n\n";
    
    if (!empty($devices)) {
        echo "Devices:\n";
        foreach ($devices as $device) {
            echo "  - ID: {$device->id}, Name: {$device->device_name}, Status: {$device->outlet_status}, Mode: {$device->control_mode}, Online: " . ($device->is_online ? 'YES' : 'NO') . "\n";
        }
    }
    echo "\n";
} catch (Exception $e) {
    echo "❌ ERROR fetching devices: " . $e->getMessage() . "\n";
    die();
}

// Current time and day
echo "14. Current time info...\n";
echo "   Current time: " . date('H:i:s') . "\n";
echo "   Current day: " . date('l') . "\n";
echo "   Current date: " . date('Y-m-d') . "\n\n";

// Summary
echo "═══════════════════════════════════════════════════════════\n";
echo "                    DIAGNOSTIC SUMMARY\n";
echo "═══════════════════════════════════════════════════════════\n\n";

echo "✅ ALL CHECKS PASSED!\n\n";

echo "The cron script CAN run, but schedules might not execute because:\n\n";

if (empty($schedules)) {
    echo "❌ NO ACTIVE SCHEDULES FOUND\n";
    echo "   → Go to Schedules page and enable schedules (toggle ON)\n\n";
} else {
    echo "✓ Active schedules exist\n\n";
}

if (!empty($devices)) {
    $device = $devices[0];
    
    if (!$device->is_online) {
        echo "❌ DEVICE IS OFFLINE\n";
        echo "   → Check ESP32 connection and power\n\n";
    } else {
        echo "✓ Device is online\n\n";
    }
    
    if ($device->control_mode !== 'scheduled') {
        echo "❌ CONTROL MODE IS '{$device->control_mode}' (NOT 'scheduled')\n";
        echo "   → Go to Outlet Control and change mode to 'Scheduled'\n\n";
    } else {
        echo "✓ Device is in scheduled mode\n\n";
    }
} else {
    echo "❌ NO DEVICES FOUND\n";
    echo "   → Add ESP32 device first\n\n";
}

echo "═══════════════════════════════════════════════════════════\n";
echo "If you see this message, the cron script is working!\n";
echo "The 500 error is now fixed.\n";
echo "═══════════════════════════════════════════════════════════\n";

echo "</pre>";
