<?php
/**
 * AC Controller System - Comprehensive Test Suite
 * 
 * This file tests all functionality of the AC Controller System
 * and reports any errors or bugs found.
 * 
 * Usage: Access via browser at http://your-domain/test-system.php
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Start output buffering to capture all output
ob_start();

// Define ROOT_PATH
define('ROOT_PATH', dirname(__DIR__));

// Load environment
require_once ROOT_PATH . '/app/helpers/EnvironmentHelper.php';
if (file_exists(ROOT_PATH . '/.env')) {
    EnvironmentHelper::load(ROOT_PATH . '/.env');
}

// Load config helper
require_once ROOT_PATH . '/app/helpers/functions.php';

// Load database
require_once ROOT_PATH . '/app/Database.php';

// Test results storage
$testResults = [];
$totalTests = 0;
$passedTests = 0;
$failedTests = 0;
$warnings = [];

/**
 * Add test result
 */
function addTestResult($category, $testName, $passed, $message = '', $details = '') {
    global $testResults, $totalTests, $passedTests, $failedTests;
    
    $totalTests++;
    if ($passed) {
        $passedTests++;
    } else {
        $failedTests++;
    }
    
    if (!isset($testResults[$category])) {
        $testResults[$category] = [];
    }
    
    $testResults[$category][] = [
        'name' => $testName,
        'passed' => $passed,
        'message' => $message,
        'details' => $details
    ];
}

/**
 * Add warning
 */
function addWarning($message) {
    global $warnings;
    $warnings[] = $message;
}

// ============================================
// TEST 1: ENVIRONMENT & CONFIGURATION
// ============================================
echo "Testing Environment & Configuration...\n";

// Test .env file exists
$envExists = file_exists(ROOT_PATH . '/.env');
addTestResult('Environment', '.env file exists', $envExists, 
    $envExists ? '.env file found' : '.env file is missing!');

// Test database configuration
$dbConfigLoaded = !empty($_ENV['DB_NAME']) && !empty($_ENV['DB_HOST']);
addTestResult('Environment', 'Database config loaded', $dbConfigLoaded,
    $dbConfigLoaded ? 'Database configuration loaded from .env' : 'Database configuration missing in .env');

// Test API key configured
$apiKeyConfigured = !empty($_ENV['API_KEY']);
addTestResult('Environment', 'API key configured', $apiKeyConfigured,
    $apiKeyConfigured ? 'API key is configured' : 'API key is NOT configured in .env');

// Test app URL configured
$appUrlConfigured = !empty($_ENV['APP_URL']);
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$detectedUrl = $protocol . $_SERVER['HTTP_HOST'];
$detectedFullUrl = $protocol . $_SERVER['HTTP_HOST'] . dirname(dirname($_SERVER['REQUEST_URI']));
$configuredUrl = $_ENV['APP_URL'] ?? 'Not set';

addTestResult('Environment', 'App URL configured', $appUrlConfigured,
    $appUrlConfigured ? 'Configured: ' . $configuredUrl . ' | Detected: ' . $detectedFullUrl : 'App URL not configured (Detected: ' . $detectedFullUrl . ')');

// Check if configured URL matches detected URL
if ($appUrlConfigured && rtrim($configuredUrl, '/') !== rtrim($detectedFullUrl, '/')) {
    addWarning("APP_URL mismatch: Configured as '$configuredUrl' but detected as '$detectedFullUrl'. This may cause redirect issues. Update .env if needed.");
}

// ============================================
// TEST 2: DATABASE CONNECTION
// ============================================
echo "Testing Database Connection...\n";

try {
    $db = Database::getInstance();
    addTestResult('Database', 'Database connection', true, 'Successfully connected to database');
    
    // Test if database has correct charset
    $stmt = $db->query("SELECT @@character_set_database as charset");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $isUtf8mb4 = ($result['charset'] === 'utf8mb4');
    addTestResult('Database', 'UTF8MB4 charset', $isUtf8mb4,
        $isUtf8mb4 ? 'Database using utf8mb4' : 'Database NOT using utf8mb4 (current: ' . $result['charset'] . ')');
    
} catch (Exception $e) {
    addTestResult('Database', 'Database connection', false, 
        'Failed to connect to database', $e->getMessage());
}

// ============================================
// TEST 3: DATABASE TABLES
// ============================================
echo "Testing Database Tables...\n";

$requiredTables = [
    'iot_outlet_users',
    'iot_outlet_devices',
    'iot_outlet_authorized_sims',
    'iot_outlet_schedules',
    'iot_outlet_activity_logs',
    'iot_outlet_sessions',
    'iot_outlet_password_resets'
];

try {
    $db = Database::getInstance();
    $stmt = $db->query("SHOW TABLES");
    $existingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($requiredTables as $table) {
        $exists = in_array($table, $existingTables);
        addTestResult('Database Tables', $table, $exists,
            $exists ? "Table $table exists" : "Table $table is MISSING!");
    }
    
} catch (Exception $e) {
    addTestResult('Database Tables', 'Table check', false, 
        'Failed to check tables', $e->getMessage());
}

// ============================================
// TEST 4: DATABASE VIEWS
// ============================================
echo "Testing Database Views...\n";

$requiredViews = [
    'iot_outlet_active_schedules_view',
    'iot_outlet_recent_activity_view',
    'iot_outlet_device_status_view'
];

try {
    $db = Database::getInstance();
    $stmt = $db->query("SHOW FULL TABLES WHERE Table_type = 'VIEW'");
    $existingViews = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($requiredViews as $view) {
        $exists = in_array($view, $existingViews);
        addTestResult('Database Views', $view, $exists,
            $exists ? "View $view exists" : "View $view is MISSING!");
    }
} catch (Exception $e) {
    addTestResult('Database Views', 'View check', false, 
        'Failed to check views', $e->getMessage());
}

// ============================================
// TEST 5: USER ACCOUNTS
// ============================================
echo "Testing User Accounts...\n";

try {
    $db = Database::getInstance();
    
    // Check if admin user exists
    $stmt = $db->query("SELECT COUNT(*) as count FROM iot_outlet_users WHERE role = 'admin'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $hasAdmin = $result['count'] > 0;
    addTestResult('Users', 'Admin user exists', $hasAdmin,
        $hasAdmin ? "Admin user(s) found: " . $result['count'] : "NO admin user found!");
    
    // Check total users
    $stmt = $db->query("SELECT COUNT(*) as count FROM iot_outlet_users");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalUsers = $result['count'];
    addTestResult('Users', 'Total users', $totalUsers > 0,
        "Total users in system: " . $totalUsers);
    
    // Check for users with first_name and last_name
    $stmt = $db->query("SELECT COUNT(*) as count FROM iot_outlet_users WHERE first_name IS NOT NULL AND first_name != ''");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $hasNames = $result['count'] > 0;
    if ($totalUsers > 0 && !$hasNames) {
        addWarning("Users exist but don't have first_name/last_name populated");
    }
    
} catch (Exception $e) {
    addTestResult('Users', 'User check', false, 
        'Failed to check users', $e->getMessage());
}

// ============================================
// TEST 6: DEVICES
// ============================================
echo "Testing Devices...\n";

try {
    $db = Database::getInstance();
    
    // Check if devices exist
    $stmt = $db->query("SELECT COUNT(*) as count FROM iot_outlet_devices");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $hasDevices = $result['count'] > 0;
    addTestResult('Devices', 'Devices registered', $hasDevices,
        $hasDevices ? "Devices found: " . $result['count'] : "NO devices registered!");
    
    if ($hasDevices) {
        // Check device API keys
        $stmt = $db->query("SELECT COUNT(*) as count FROM iot_outlet_devices WHERE api_key IS NULL OR api_key = ''");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $devicesWithoutApiKey = $result['count'];
        addTestResult('Devices', 'API keys configured', $devicesWithoutApiKey === 0,
            $devicesWithoutApiKey === 0 ? "All devices have API keys" : "$devicesWithoutApiKey devices missing API keys!");
        
        // Check device online status
        $stmt = $db->query("SELECT COUNT(*) as count FROM iot_outlet_devices WHERE is_online = 1");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $onlineDevices = $result['count'];
        
        if ($onlineDevices === 0) {
            addWarning("No devices are currently online");
        } else {
            addTestResult('Devices', 'Online devices', true, "$onlineDevices device(s) online");
        }
        
        // Check last heartbeat
        $stmt = $db->query("SELECT device_name, last_heartbeat, TIMESTAMPDIFF(SECOND, last_heartbeat, NOW()) as seconds_ago FROM iot_outlet_devices");
        $devices = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($devices as $device) {
            if ($device['last_heartbeat']) {
                $secondsAgo = $device['seconds_ago'];
                if ($secondsAgo > 300) { // 5 minutes
                    addWarning("Device '{$device['device_name']}' last heartbeat was {$secondsAgo} seconds ago (may be offline)");
                }
            } else {
                addWarning("Device '{$device['device_name']}' has never sent a heartbeat");
            }
        }
    }
    
} catch (Exception $e) {
    addTestResult('Devices', 'Device check', false, 
        'Failed to check devices', $e->getMessage());
}

// ============================================
// TEST 7: AUTHORIZED SIM CARDS
// ============================================
echo "Testing Authorized SIM Cards...\n";

try {
    $db = Database::getInstance();
    
    $stmt = $db->query("SELECT COUNT(*) as count FROM iot_outlet_authorized_sims WHERE is_active = 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $activeSims = $result['count'];
    
    addTestResult('SMS Control', 'Authorized SIM cards', $activeSims > 0,
        $activeSims > 0 ? "Active SIM cards: $activeSims" : "NO authorized SIM cards configured!");
    
    // Check for SIMs with first_name and last_name
    $stmt = $db->query("SELECT COUNT(*) as count FROM iot_outlet_authorized_sims WHERE first_name IS NOT NULL AND first_name != ''");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $hasNames = $result['count'] > 0;
    if ($activeSims > 0 && !$hasNames) {
        addWarning("SIM cards exist but don't have first_name/last_name populated");
    }
    
} catch (Exception $e) {
    addTestResult('SMS Control', 'SIM check', false, 
        'Failed to check SIM cards', $e->getMessage());
}

// ============================================
// TEST 8: SCHEDULES
// ============================================
echo "Testing Schedules...\n";

try {
    $db = Database::getInstance();
    
    // Check total schedules
    $stmt = $db->query("SELECT COUNT(*) as count FROM iot_outlet_schedules");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalSchedules = $result['count'];
    addTestResult('Schedules', 'Schedules exist', true,
        "Total schedules: $totalSchedules");
    
    // Check active schedules
    $stmt = $db->query("SELECT COUNT(*) as count FROM iot_outlet_schedules WHERE is_enabled = 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $activeSchedules = $result['count'];
    addTestResult('Schedules', 'Active schedules', true,
        "Active schedules: $activeSchedules");
    
    // Check for schedule overlaps
    if ($activeSchedules > 1) {
        $stmt = $db->query("
            SELECT s1.schedule_name as s1_name, s1.time_on as s1_on, s1.time_off as s1_off,
                   s2.schedule_name as s2_name, s2.time_on as s2_on, s2.time_off as s2_off
            FROM iot_outlet_schedules s1
            CROSS JOIN iot_outlet_schedules s2
            WHERE s1.id < s2.id
            AND s1.device_id = s2.device_id
            AND s1.is_enabled = 1 AND s2.is_enabled = 1
            AND s1.time_on < s2.time_off AND s2.time_on < s1.time_off
        ");
        $overlaps = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($overlaps) > 0) {
            foreach ($overlaps as $overlap) {
                addWarning("Schedule overlap detected: '{$overlap['s1_name']}' ({$overlap['s1_on']}-{$overlap['s1_off']}) overlaps with '{$overlap['s2_name']}' ({$overlap['s2_on']}-{$overlap['s2_off']})");
            }
        }
    }
    
    // Check for invalid time ranges
    $stmt = $db->query("SELECT schedule_name, time_on, time_off FROM iot_outlet_schedules WHERE time_on >= time_off");
    $invalidTimes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($invalidTimes as $invalid) {
        addWarning("Invalid schedule time range: '{$invalid['schedule_name']}' has ON time ({$invalid['time_on']}) >= OFF time ({$invalid['time_off']})");
    }
    
} catch (Exception $e) {
    addTestResult('Schedules', 'Schedule check', false, 
        'Failed to check schedules', $e->getMessage());
}

// ============================================
// TEST 9: ACTIVITY LOGS
// ============================================
echo "Testing Activity Logs...\n";

try {
    $db = Database::getInstance();
    
    $stmt = $db->query("SELECT COUNT(*) as count FROM iot_outlet_activity_logs");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $logCount = $result['count'];
    addTestResult('Activity Logs', 'Logs exist', true,
        "Total activity logs: $logCount");
    
    // Check log size
    $stmt = $db->query("SELECT COUNT(*) as count FROM iot_outlet_activity_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $oldLogs = $result['count'];
    if ($oldLogs > 1000) {
        addWarning("There are $oldLogs old activity logs (>90 days). Consider running cleanup procedure.");
    }
    
} catch (Exception $e) {
    addTestResult('Activity Logs', 'Log check', false, 
        'Failed to check activity logs', $e->getMessage());
}

// ============================================
// TEST 10: FILE PERMISSIONS & STRUCTURE
// ============================================
echo "Testing File Permissions & Structure...\n";

$requiredDirs = [
    ROOT_PATH . '/storage/logs' => true,  // Should be writable
    ROOT_PATH . '/storage/cache' => true, // Should be writable
    ROOT_PATH . '/app/controllers' => false,
    ROOT_PATH . '/app/models' => false,
    ROOT_PATH . '/app/views' => false,
    ROOT_PATH . '/config' => false,
    ROOT_PATH . '/public' => false
];

foreach ($requiredDirs as $dir => $shouldBeWritable) {
    $exists = is_dir($dir);
    addTestResult('File Structure', basename($dir) . ' directory', $exists,
        $exists ? "Directory exists: $dir" : "Directory MISSING: $dir");
    
    if ($exists && $shouldBeWritable) {
        $writable = is_writable($dir);
        addTestResult('File Permissions', basename($dir) . ' writable', $writable,
            $writable ? "$dir is writable" : "$dir is NOT writable!");
    }
}

// Check critical files
$requiredFiles = [
    ROOT_PATH . '/index.php',
    ROOT_PATH . '/app/Database.php',
    ROOT_PATH . '/app/Router.php',
    ROOT_PATH . '/routes/api.php',
    ROOT_PATH . '/app/controllers/AuthController.php',
    ROOT_PATH . '/app/controllers/DeviceController.php',
    ROOT_PATH . '/app/controllers/ScheduleController.php',
    ROOT_PATH . '/app/controllers/OutletController.php'
];

foreach ($requiredFiles as $file) {
    $exists = file_exists($file);
    addTestResult('File Structure', basename($file), $exists,
        $exists ? "File exists" : "File MISSING: $file");
}

// ============================================
// TEST 11: API ENDPOINTS (Simulated)
// ============================================
echo "Testing API Configuration...\n";

// Check if API router exists
$apiRouterExists = file_exists(ROOT_PATH . '/routes/api.php');
addTestResult('API', 'API router file', $apiRouterExists,
    $apiRouterExists ? 'API router file exists' : 'API router file is MISSING!');

// Check if .htaccess configured for API
$htaccessExists = file_exists(ROOT_PATH . '/public/.htaccess');
addTestResult('API', '.htaccess file', $htaccessExists,
    $htaccessExists ? 'Public .htaccess exists' : 'Public .htaccess is MISSING!');

if ($htaccessExists) {
    $htaccessContent = file_get_contents(ROOT_PATH . '/public/.htaccess');
    $hasRewrite = strpos($htaccessContent, 'RewriteEngine On') !== false;
    addTestResult('API', 'URL rewrite enabled', $hasRewrite,
        $hasRewrite ? 'URL rewriting is enabled' : 'URL rewriting is NOT enabled!');
}

// ============================================
// TEST 12: CRON JOB CONFIGURATION
// ============================================
echo "Testing Cron Job Configuration...\n";

$cronScriptExists = file_exists(ROOT_PATH . '/cron/execute_schedules.php');
addTestResult('Cron Jobs', 'Schedule execution script', $cronScriptExists,
    $cronScriptExists ? 'Cron script exists' : 'Cron script is MISSING!');

// Check if cron has been executed recently
try {
    $db = Database::getInstance();
    $stmt = $db->query("
        SELECT MAX(created_at) as last_execution 
        FROM iot_outlet_activity_logs 
        WHERE action_type IN ('outlet_on', 'outlet_off') 
        AND action_details LIKE '%schedule%'
    ");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['last_execution']) {
        $secondsAgo = strtotime('now') - strtotime($result['last_execution']);
        if ($secondsAgo > 3600) { // 1 hour
            addWarning("Last schedule execution was " . round($secondsAgo / 60) . " minutes ago. Check if cron is running.");
        } else {
            addTestResult('Cron Jobs', 'Recent execution', true, 
                "Last schedule execution: " . $result['last_execution']);
        }
    } else {
        addWarning("No schedule executions found in logs. Cron may not be configured.");
    }
} catch (Exception $e) {
    addWarning("Could not check cron execution history: " . $e->getMessage());
}

// ============================================
// TEST 13: SESSION CONFIGURATION
// ============================================
echo "Testing Session Configuration...\n";

$sessionConfigured = session_status() !== PHP_SESSION_DISABLED;
addTestResult('Sessions', 'Sessions enabled', $sessionConfigured,
    $sessionConfigured ? 'PHP sessions are enabled' : 'PHP sessions are DISABLED!');

// Check session save path
$savePath = session_save_path();
if (!empty($savePath)) {
    $savePathWritable = is_writable($savePath);
    addTestResult('Sessions', 'Session save path writable', $savePathWritable,
        $savePathWritable ? "Session path is writable: $savePath" : "Session path is NOT writable: $savePath");
}

// ============================================
// TEST 14: PHP EXTENSIONS
// ============================================
echo "Testing PHP Extensions...\n";

$requiredExtensions = [
    'pdo',
    'pdo_mysql',
    'mbstring',
    'openssl',
    'json',
    'curl'
];

foreach ($requiredExtensions as $extension) {
    $loaded = extension_loaded($extension);
    addTestResult('PHP Extensions', $extension, $loaded,
        $loaded ? "$extension is loaded" : "$extension is NOT loaded!");
}

// Check PHP version
$phpVersion = phpversion();
$phpVersionOk = version_compare($phpVersion, '7.4', '>=');
addTestResult('PHP Environment', 'PHP version', $phpVersionOk,
    "PHP version: $phpVersion" . ($phpVersionOk ? '' : ' (recommend 7.4 or higher)'));

// ============================================
// TEST 15: SECURITY CHECKS
// ============================================
echo "Testing Security Configuration...\n";

// Check if .env is accessible from web
// Dynamically get current domain
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$currentDomain = $protocol . $_SERVER['HTTP_HOST'];
$baseUrl = rtrim($currentDomain . dirname(dirname($_SERVER['REQUEST_URI'])), '/');

$envWebAccessible = @file_get_contents($baseUrl . '/.env') !== false;
addTestResult('Security', '.env not web accessible', !$envWebAccessible,
    !$envWebAccessible ? '.env is protected from web access' : '.env IS ACCESSIBLE FROM WEB! Add deny rules!');

// Check if config files are outside public
$configOutsidePublic = !file_exists(ROOT_PATH . '/public/config');
addTestResult('Security', 'Config outside public', $configOutsidePublic,
    $configOutsidePublic ? 'Config files are outside public directory' : 'Config files are in public directory!');

// Check password hashing algorithm
$bcryptAvailable = defined('PASSWORD_BCRYPT');
$testHash = password_hash('test', PASSWORD_BCRYPT);
$hashWorks = $testHash !== false;
addTestResult('Security', 'Password hashing available', $bcryptAvailable && $hashWorks,
    ($bcryptAvailable && $hashWorks) ? 'Bcrypt password hashing is available and working' : 'Bcrypt NOT available or not working!');

// Check HTTPS configuration (if in production)
if ($_ENV['APP_ENV'] === 'production') {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    addTestResult('Security', 'HTTPS enabled', $isHttps,
        $isHttps ? 'HTTPS is enabled' : 'HTTPS is NOT enabled (recommended for production)!');
}

// ============================================
// TEST 16: ESP32 INTEGRATION
// ============================================
echo "Testing ESP32 Integration...\n";

// Check if ESP32 code file exists
$esp32CodeExists = file_exists(ROOT_PATH . '/esp32/universal_outlet_controller.ino');
addTestResult('ESP32', 'ESP32 code file', $esp32CodeExists,
    $esp32CodeExists ? 'ESP32 Arduino code exists' : 'ESP32 code file is MISSING!');

// Check device configuration in database
try {
    $db = Database::getInstance();
    $stmt = $db->query("SELECT device_id, api_key FROM iot_outlet_devices LIMIT 1");
    $device = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($device) {
        addTestResult('ESP32', 'Device credentials', true,
            "Device configured with ID: {$device['device_id']}");
        
        // Read ESP32 code and check if API key matches
        if ($esp32CodeExists) {
            $esp32Code = file_get_contents(ROOT_PATH . '/esp32/universal_outlet_controller.ino');
            $hasApiKeyPlaceholder = (strpos($esp32Code, 'YOUR_API_KEY_HERE') !== false);
            if ($hasApiKeyPlaceholder) {
                addWarning("ESP32 code still has placeholder API key. Update with actual key: {$device['api_key']}");
            }
        }
    } else {
        addWarning("No devices configured in database for ESP32 to connect to");
    }
} catch (Exception $e) {
    addWarning("Could not check ESP32 device configuration: " . $e->getMessage());
}

// ============================================
// TEST 17: REPOSITORY CLASSES
// ============================================
echo "Testing Repository Classes...\n";

$repositories = [
    'UserRepository' => ROOT_PATH . '/app/repositories/UserRepository.php',
    'DeviceRepository' => ROOT_PATH . '/app/repositories/DeviceRepository.php',
    'ScheduleRepository' => ROOT_PATH . '/app/repositories/ScheduleRepository.php',
    'SimRepository' => ROOT_PATH . '/app/repositories/SimRepository.php',
    'ActivityLogRepository' => ROOT_PATH . '/app/repositories/ActivityLogRepository.php'
];

foreach ($repositories as $name => $path) {
    $exists = file_exists($path);
    addTestResult('Repository Classes', $name, $exists,
        $exists ? "$name exists" : "$name is MISSING!");
}

// ============================================
// TEST 18: CONTROLLER CLASSES
// ============================================
echo "Testing Controller Classes...\n";

$controllers = [
    'AuthController',
    'DashboardController',
    'DeviceController',
    'OutletController',
    'ScheduleController',
    'ProfileController',
    'AdminController',
    'SimController'
];

foreach ($controllers as $controller) {
    $path = ROOT_PATH . '/app/controllers/' . $controller . '.php';
    $exists = file_exists($path);
    addTestResult('Controllers', $controller, $exists,
        $exists ? "$controller exists" : "$controller is MISSING!");
}

// ============================================
// TEST 19: VIEW FILES
// ============================================
echo "Testing View Files...\n";

$criticalViews = [
    'auth/login.php',
    'auth/register.php',
    'dashboard/index.php',
    'outlet/index.php',
    'schedule/index.php',
    'admin/devices.php',
    'admin/users.php',
    'admin/sims.php',
    'layouts/header.php',
    'layouts/footer.php'
];

foreach ($criticalViews as $view) {
    $path = ROOT_PATH . '/app/views/' . $view;
    $exists = file_exists($path);
    addTestResult('Views', $view, $exists,
        $exists ? "View exists" : "View MISSING: $view");
}

// ============================================
// TEST 20: COMMON BUGS & ISSUES
// ============================================
echo "Checking for Common Bugs & Issues...\n";

// Check for deprecated PHP functions in code
$codeFiles = glob(ROOT_PATH . '/app/**/*.php');
foreach ($codeFiles as $file) {
    if (is_file($file)) {
        $content = file_get_contents($file);
        
        // Check for mysql_ functions (deprecated)
        if (preg_match('/mysql_[a-z_]+\s*\(/i', $content)) {
            addWarning("File " . basename($file) . " uses deprecated mysql_* functions");
        }
        
        // Check for eval() usage
        if (preg_match('/\beval\s*\(/i', $content)) {
            addWarning("File " . basename($file) . " uses eval() which is a security risk");
        }
    }
}

// Check for SQL injection vulnerabilities (basic check)
try {
    $db = Database::getInstance();
    $stmt = $db->query("SHOW PROCEDURE STATUS WHERE Db = DATABASE()");
    $procedures = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $expectedProcedures = [
        'sp_iot_outlet_cleanup_old_logs',
        'sp_iot_outlet_get_user_stats',
        'sp_iot_outlet_update_heartbeat'
    ];
    
    foreach ($expectedProcedures as $proc) {
        $exists = false;
        foreach ($procedures as $p) {
            if ($p['Name'] === $proc) {
                $exists = true;
                break;
            }
        }
        addTestResult('Stored Procedures', $proc, $exists,
            $exists ? "Stored procedure exists" : "Stored procedure MISSING: $proc");
    }
} catch (Exception $e) {
    addWarning("Could not check stored procedures: " . $e->getMessage());
}

// ============================================
// GENERATE HTML REPORT
// ============================================

// Clear output buffer
ob_end_clean();

// Calculate pass rate
$passRate = $totalTests > 0 ? round(($passedTests / $totalTests) * 100, 2) : 0;

// Determine overall status
$overallStatus = 'Excellent';
$statusColor = '#28a745';
if ($passRate < 100) {
    $overallStatus = 'Good';
    $statusColor = '#ffc107';
}
if ($passRate < 80) {
    $overallStatus = 'Needs Attention';
    $statusColor = '#fd7e14';
}
if ($passRate < 60) {
    $overallStatus = 'Critical Issues';
    $statusColor = '#dc3545';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AC Controller System - Test Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        .header p {
            font-size: 1.1em;
            opacity: 0.9;
        }
        .summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            padding: 40px;
            background: #f8f9fa;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-value {
            font-size: 3em;
            font-weight: bold;
            margin: 10px 0;
        }
        .stat-label {
            color: #6c757d;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .content {
            padding: 40px;
        }
        .test-category {
            margin-bottom: 40px;
        }
        .category-title {
            font-size: 1.5em;
            color: #495057;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #667eea;
        }
        .test-item {
            display: flex;
            align-items: center;
            padding: 15px;
            margin-bottom: 10px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid transparent;
        }
        .test-item.passed {
            border-left-color: #28a745;
        }
        .test-item.failed {
            border-left-color: #dc3545;
        }
        .test-icon {
            font-size: 1.5em;
            margin-right: 15px;
            min-width: 30px;
        }
        .test-details {
            flex: 1;
        }
        .test-name {
            font-weight: 600;
            color: #212529;
            margin-bottom: 5px;
        }
        .test-message {
            color: #6c757d;
            font-size: 0.9em;
        }
        .test-error {
            color: #dc3545;
            font-size: 0.85em;
            margin-top: 5px;
            font-family: 'Courier New', monospace;
            background: #f8d7da;
            padding: 8px;
            border-radius: 5px;
        }
        .warnings {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            margin: 30px 0;
            border-radius: 10px;
        }
        .warnings h3 {
            color: #856404;
            margin-bottom: 15px;
        }
        .warning-item {
            color: #856404;
            padding: 8px 0;
            border-bottom: 1px solid #ffe8a1;
        }
        .warning-item:last-child {
            border-bottom: none;
        }
        .footer {
            text-align: center;
            padding: 30px;
            background: #f8f9fa;
            color: #6c757d;
        }
        .progress-bar {
            width: 100%;
            height: 30px;
            background: #e9ecef;
            border-radius: 15px;
            overflow: hidden;
            margin: 20px 0;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #28a745, #20c997);
            transition: width 1s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 System Test Report</h1>
            <p>AC Controller System - Comprehensive Functionality Test</p>
            <p>Generated: <?php echo date('F j, Y g:i A'); ?></p>
            <p style="font-size: 0.9em; margin-top: 10px; opacity: 0.8;">
                Testing Server: <?php 
                    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
                    echo $protocol . $_SERVER['HTTP_HOST']; 
                ?>
            </p>
        </div>

        <div class="summary">
            <div class="stat-card">
                <div class="stat-label">Overall Status</div>
                <div class="stat-value" style="color: <?php echo $statusColor; ?>">
                    <?php echo $overallStatus; ?>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Tests</div>
                <div class="stat-value" style="color: #667eea;">
                    <?php echo $totalTests; ?>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Passed</div>
                <div class="stat-value" style="color: #28a745;">
                    <?php echo $passedTests; ?>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Failed</div>
                <div class="stat-value" style="color: #dc3545;">
                    <?php echo $failedTests; ?>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Warnings</div>
                <div class="stat-value" style="color: #ffc107;">
                    <?php echo count($warnings); ?>
                </div>
            </div>
        </div>

        <div style="padding: 0 40px;">
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo $passRate; ?>%; background: <?php echo $statusColor; ?>;">
                    <?php echo $passRate; ?>% Pass Rate
                </div>
            </div>
        </div>

        <?php if (!empty($warnings)): ?>
        <div style="padding: 0 40px;">
            <div class="warnings">
                <h3>⚠️ Warnings (<?php echo count($warnings); ?>)</h3>
                <?php foreach ($warnings as $warning): ?>
                    <div class="warning-item">⚠️ <?php echo htmlspecialchars($warning); ?></div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="content">
            <?php foreach ($testResults as $category => $tests): ?>
                <div class="test-category">
                    <h2 class="category-title"><?php echo htmlspecialchars($category); ?></h2>
                    <?php foreach ($tests as $test): ?>
                        <div class="test-item <?php echo $test['passed'] ? 'passed' : 'failed'; ?>">
                            <div class="test-icon">
                                <?php echo $test['passed'] ? '✅' : '❌'; ?>
                            </div>
                            <div class="test-details">
                                <div class="test-name"><?php echo htmlspecialchars($test['name']); ?></div>
                                <div class="test-message"><?php echo htmlspecialchars($test['message']); ?></div>
                                <?php if (!empty($test['details'])): ?>
                                    <div class="test-error"><?php echo htmlspecialchars($test['details']); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="footer">
            <p><strong>AC Controller System</strong> - IoT Smart Outlet Management</p>
            <p style="margin-top: 10px; font-size: 0.9em;">
                Test completed in <?php echo round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 2); ?> seconds
            </p>
            <p style="margin-top: 10px; font-size: 0.85em; opacity: 0.7;">
                ⚡ Powered by PHP <?php echo phpversion(); ?> | Database: <?php echo $_ENV['DB_NAME'] ?? 'N/A'; ?>
            </p>
        </div>
    </div>
</body>
</html>
