<?php
/**
 * Web-accessible Cron Job Replacement
 * 
 * Since you don't have access to cron jobs, you can:
 * 1. Call this URL manually to test
 * 2. Use a free cron service to call it automatically
 * 
 * URL: https://isu-documentrequest.online/ac_controller_system/public/cron.php?key=YOUR_SECRET_KEY
 */

// Enable error logging (but don't display errors)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Define ROOT_PATH early for loading .env
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

// Load environment first to get API_KEY
require_once ROOT_PATH . '/app/helpers/EnvironmentHelper.php';
EnvironmentHelper::load(ROOT_PATH . '/.env');

// Security: Check secret key
$secretKey = $_GET['key'] ?? '';
$expectedKey = $_ENV['API_KEY'] ?? 'IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T'; // From .env file

if ($secretKey !== $expectedKey) {
    http_response_code(401);
    die(json_encode([
        'error' => 'Unauthorized - Invalid key',
        'debug' => [
            'key_provided' => substr($secretKey, 0, 20) . '...',
            'key_expected' => substr($expectedKey, 0, 20) . '...',
            'env_loaded' => !empty($_ENV['API_KEY'])
        ]
    ]));
}

// Prevent timeout
set_time_limit(60);
ignore_user_abort(true);

// Load remaining configuration files
$output = [];
$output[] = "[" . date('Y-m-d H:i:s') . "] Schedule Executor Started";

try {
    // Check if required files exist before requiring them
    $requiredFiles = [
        ROOT_PATH . '/config/app.php',
        ROOT_PATH . '/app/Database.php',
        ROOT_PATH . '/app/repositories/DeviceRepository.php',
        ROOT_PATH . '/app/repositories/ScheduleRepository.php',
        ROOT_PATH . '/app/models/Device.php',
        ROOT_PATH . '/app/models/Schedule.php'
    ];
    
    foreach ($requiredFiles as $file) {
        if (!file_exists($file)) {
            throw new Exception("Required file not found: " . basename($file));
        }
    }
    
    require_once ROOT_PATH . '/config/app.php';
    
    // Set timezone from config
    $config = require ROOT_PATH . '/config/app.php';
    date_default_timezone_set($config['timezone']);
    
    require_once ROOT_PATH . '/app/Database.php';
    require_once ROOT_PATH . '/app/repositories/DeviceRepository.php';
    require_once ROOT_PATH . '/app/repositories/ScheduleRepository.php';
    require_once ROOT_PATH . '/app/models/Device.php';
    require_once ROOT_PATH . '/app/models/Schedule.php';
    
} catch (Exception $e) {
    $output[] = "FATAL ERROR: " . $e->getMessage();
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'output' => $output,
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
    exit(1);
}

$output = [];
$output[] = "[" . date('Y-m-d H:i:s') . "] Schedule Executor Started";

try {
    $deviceRepo = new DeviceRepository();
    $scheduleRepo = new ScheduleRepository();
    
    // Mark offline devices first
    $output[] = "Checking for offline devices...";
    $offlineCount = $deviceRepo->markOfflineDevices();
    if ($offlineCount > 0) {
        $output[] = "Marked {$offlineCount} device(s) as offline";
    }
    $output[] = "";
    
    // Get all active schedules
    $schedules = $scheduleRepo->getActive();
    
    if (empty($schedules)) {
        $output[] = "No active schedules found";
        echo json_encode(['success' => true, 'output' => $output]);
        exit(0);
    }
    
    $output[] = "Found " . count($schedules) . " active schedule(s)";
    
    $currentTime = date('H:i:s');
    $currentDay = date('l'); // Monday, Tuesday, etc.
    
    $output[] = "Current time: $currentTime";
    $output[] = "Current day: $currentDay";
    $output[] = "";
    
    $executedCount = 0;
    
    // Track which action to take (latest schedule wins in case of conflicts)
    $deviceActions = []; // device_id => ['action' => 'on'/'off', 'schedule_id' => ID, 'schedule_name' => Name]
    
    foreach ($schedules as $schedule) {
        $output[] = "Checking schedule: {$schedule->schedule_name} (ID: {$schedule->id})";
        $output[] = "  Time ON: {$schedule->time_on}";
        $output[] = "  Time OFF: {$schedule->time_off}";
        $output[] = "  Days: {$schedule->days_of_week}";
        $output[] = "  Repeat: {$schedule->repeat_type}";
        
        // Check if today is in the schedule days
        $scheduleDays = array_map('trim', explode(',', $schedule->days_of_week));
        $dayShortNames = [
            'Monday' => 'Mon',
            'Tuesday' => 'Tue',
            'Wednesday' => 'Wed',
            'Thursday' => 'Thu',
            'Friday' => 'Fri',
            'Saturday' => 'Sat',
            'Sunday' => 'Sun'
        ];
        
        $currentDayShort = $dayShortNames[$currentDay] ?? $currentDay;
        
        if (!in_array($currentDay, $scheduleDays) && !in_array($currentDayShort, $scheduleDays)) {
            $output[] = "  → Skipped (not scheduled for today)";
            $output[] = "";
            continue;
        }
        
        // Get device
        $device = $deviceRepo->findById($schedule->device_id);
        
        if (!$device) {
            $output[] = "  → Error: Device not found";
            $output[] = "";
            continue;
        }
        
        // Skip if device is offline
        if (!$device->is_online) {
            $output[] = "  → Skipped (device is offline)";
            $output[] = "  → Last heartbeat: " . ($device->last_heartbeat ? $device->last_heartbeat : "Never");
            $output[] = "";
            continue;
        }
        
        // Check if control mode is scheduled
        if ($device->control_mode !== 'scheduled') {
            $output[] = "  → Skipped (control mode is '{$device->control_mode}', not 'scheduled')";
            $output[] = "";
            continue;
        }
        
        // Check if it's a one-time schedule
        if ($schedule->repeat_type === 'once') {
            // Check if it was already executed
            if ($schedule->last_executed) {
                $output[] = "  → Skipped (one-time schedule already executed)";
                $output[] = "";
                continue;
            }
            
            // Check if schedule has a specific date
            if (!empty($schedule->schedule_date)) {
                $scheduledDate = date('Y-m-d', strtotime($schedule->schedule_date));
                $todayDate = date('Y-m-d');
                
                if ($scheduledDate !== $todayDate) {
                    $output[] = "  → Skipped (scheduled for {$scheduledDate}, today is {$todayDate})";
                    $output[] = "";
                    continue;
                }
                
                $output[] = "  → One-time schedule for today ({$scheduledDate})";
            }
        }
        
        $timeOn = $schedule->time_on;
        $timeOff = $schedule->time_off;
        
        // Check if it's time to turn ON (1 minute window)
        $timeOnStart = $timeOn;
        $timeOnEnd = date('H:i:s', strtotime($timeOn) + 59);
        
        if ($currentTime >= $timeOnStart && $currentTime <= $timeOnEnd) {
            // Record action (don't execute yet - handle conflicts later)
            $deviceId = $device->id;
            if (!isset($deviceActions[$deviceId]) || $schedule->id > $deviceActions[$deviceId]['schedule_id']) {
                // Higher ID = more recent = wins in conflicts
                $deviceActions[$deviceId] = [
                    'action' => 'on',
                    'schedule_id' => $schedule->id,
                    'schedule_name' => $schedule->schedule_name,
                    'repeat_type' => $schedule->repeat_type
                ];
                $output[] = "  → Will turn ON (priority: schedule ID {$schedule->id})";
            } else {
                $output[] = "  → Would turn ON, but overridden by schedule ID {$deviceActions[$deviceId]['schedule_id']} ('{$deviceActions[$deviceId]['schedule_name']}')";
            }
        }
        // Check if it's time to turn OFF
        elseif ($currentTime >= $timeOff && $currentTime <= date('H:i:s', strtotime($timeOff) + 59)) {
            // Record action (don't execute yet - handle conflicts later)
            $deviceId = $device->id;
            if (!isset($deviceActions[$deviceId]) || $schedule->id > $deviceActions[$deviceId]['schedule_id']) {
                // Higher ID = more recent = wins in conflicts
                $deviceActions[$deviceId] = [
                    'action' => 'off',
                    'schedule_id' => $schedule->id,
                    'schedule_name' => $schedule->schedule_name,
                    'repeat_type' => $schedule->repeat_type
                ];
                $output[] = "  → Will turn OFF (priority: schedule ID {$schedule->id})";
            } else {
                $output[] = "  → Would turn OFF, but overridden by schedule ID {$deviceActions[$deviceId]['schedule_id']} ('{$deviceActions[$deviceId]['schedule_name']}')";
            }
        }
        // Check if we're within the ON period
        elseif ($currentTime >= $timeOn && $currentTime < $timeOff) {
            $output[] = "  → Within ON period";
            if ($device->outlet_status === 'on' && $device->control_mode === 'scheduled') {
                $output[] = "  → Status is correct (already ON)";
            } else {
                $output[] = "  → Not in scheduled mode or manually controlled";
            }
        }
        // Outside schedule time
        else {
            $output[] = "  → Outside schedule time";
        }
        
        $output[] = "";
    }
    
    // Execute collected actions (resolves conflicts)
    $output[] = "=== Executing Actions ===";
    foreach ($deviceActions as $deviceId => $action) {
        $device = $deviceRepo->findById($deviceId);
        if (!$device) continue;
        
        $output[] = "Device: {$device->device_name} (ID: {$deviceId})";
        $output[] = "  Action: Turn {$action['action']}";
        $output[] = "  Schedule: '{$action['schedule_name']}' (ID: {$action['schedule_id']})";
        
        if ($device->outlet_status === $action['action']) {
            $output[] = "  → Already in desired state ({$action['action']})";
        } else {
            $device->outlet_status = $action['action'];
            $device->control_mode = 'scheduled';
            
            if ($deviceRepo->update($device)) {
                $output[] = "  → SUCCESS: Device turned " . strtoupper($action['action']);
                $executedCount++;
                
                // For one-time schedules:
                // - Mark as executed when turning ON (but don't disable yet)
                // - Disable when turning OFF (after schedule completes)
                if ($action['repeat_type'] === 'once') {
                    if ($action['action'] === 'on') {
                        // Just mark as executed, keep enabled so it can turn OFF
                        $stmt = $scheduleRepo->db->prepare(
                            'UPDATE iot_outlet_schedules SET last_executed = NOW() WHERE id = ?'
                        );
                        $stmt->execute([$action['schedule_id']]);
                        $output[] = "  → One-time schedule marked as executed (will disable after turning OFF)";
                    } else {
                        // Disable after turning OFF (schedule complete)
                        $scheduleRepo->disable($action['schedule_id']);
                        $output[] = "  → One-time schedule disabled after completion";
                    }
                }
                
                // Auto-disable repeating schedule after OFF action (optional feature)
                // Uncomment the following lines to auto-disable schedules after they turn OFF:
                /*
                elseif ($action['action'] === 'off' && $action['repeat_type'] === 'repeat') {
                    $schedule = $scheduleRepo->findById($action['schedule_id']);
                    if ($schedule) {
                        $schedule->is_enabled = 0;
                        $scheduleRepo->update($schedule);
                        $output[] = "  → Repeating schedule auto-disabled after turning OFF";
                    }
                }
                */
                
                // Log activity
                logActivity($deviceId, 'outlet_' . $action['action'], "Schedule '{$action['schedule_name']}' turned outlet " . strtoupper($action['action']));
            } else {
                $output[] = "  → ERROR: Failed to update device";
            }
        }
        $output[] = "";
    }
    
    $output[] = "[" . date('Y-m-d H:i:s') . "] Schedule Executor Completed";
    $output[] = "Executed: $executedCount schedule action(s)";
    
    echo json_encode([
        'success' => true,
        'executed' => $executedCount,
        'output' => $output,
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    $output[] = "ERROR: " . $e->getMessage();
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'output' => $output
    ], JSON_PRETTY_PRINT);
    exit(1);
}

/**
 * Log activity to database
 */
function logActivity(int $deviceId, string $actionType, string $details): void
{
    try {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'INSERT INTO iot_outlet_activity_logs (device_id, action_type, action_details, ip_address, status) 
             VALUES (?, ?, ?, ?, ?)'
        );
        
        $stmt->execute([
            $deviceId,
            $actionType,
            $details,
            'CRON',
            'success'
        ]);
    } catch (Exception $e) {
        // Silently fail to not disrupt schedule execution
        error_log('Failed to log activity: ' . $e->getMessage());
    }
}
