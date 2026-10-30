<?php

/**
 * Schedule Executor (Cron Job)
 * 
 * This script should run every minute via cron job:
 * * * * * * php /path/to/execute_schedules.php
 * 
 * Or on Windows Task Scheduler:
 * C:\xampp\php\php.exe C:\xampp\htdocs\ac_controller_system\cron\execute_schedules.php
 * 
 * Or called via URL (with API key protection):
 * https://your-domain.com/cron/execute_schedules.php?api_key=YOUR_KEY
 */

// Define root path
define('ROOT_PATH', dirname(__DIR__));

// Load environment and configuration
require_once ROOT_PATH . '/app/helpers/EnvironmentHelper.php';
EnvironmentHelper::load(ROOT_PATH . '/.env');

require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/app/Database.php';
require_once ROOT_PATH . '/app/repositories/DeviceRepository.php';
require_once ROOT_PATH . '/app/repositories/ScheduleRepository.php';
require_once ROOT_PATH . '/app/models/Device.php';
require_once ROOT_PATH . '/app/models/Schedule.php';

// Set timezone from config
$config = require ROOT_PATH . '/config/app.php';
date_default_timezone_set($config['timezone']);

// Simple API key check for URL access
if (php_sapi_name() !== 'cli') {
    $apiKey = $_GET['api_key'] ?? '';
    $expectedKey = $_ENV['API_KEY'] ?? 'IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T';
    
    if ($apiKey !== $expectedKey) {
        http_response_code(401);
        die(json_encode(['error' => 'Unauthorized']));
    }
}

echo "[" . date('Y-m-d H:i:s') . "] Schedule Executor Started\n";

try {
    $deviceRepo = new DeviceRepository();
    $scheduleRepo = new ScheduleRepository();
    
    // Mark offline devices first (devices without heartbeat for 2+ minutes)
    echo "Checking for offline devices...\n";
    $offlineCount = $deviceRepo->markOfflineDevices();
    if ($offlineCount > 0) {
        echo "Marked {$offlineCount} device(s) as offline\n";
    }
    echo "\n";
    
    // Get all active schedules
    $schedules = $scheduleRepo->getActive();
    
    if (empty($schedules)) {
        echo "No active schedules found\n";
        exit(0);
    }
    
    echo "Found " . count($schedules) . " active schedule(s)\n";
    
    $currentTime = date('H:i:s');
    $currentDay = date('l'); // Monday, Tuesday, etc.
    $currentDateTime = date('Y-m-d H:i:s');
    
    echo "Current time: $currentTime\n";
    echo "Current day: $currentDay\n\n";
    
    foreach ($schedules as $schedule) {
        echo "Checking schedule: {$schedule->schedule_name} (ID: {$schedule->id})\n";
        echo "  Time ON: {$schedule->time_on}\n";
        echo "  Time OFF: {$schedule->time_off}\n";
        echo "  Days: {$schedule->days_of_week}\n";
        echo "  Repeat: {$schedule->repeat_type}\n";
        
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
            echo "  → Skipped (not scheduled for today)\n\n";
            continue;
        }
        
        // Get device
        $device = $deviceRepo->findById($schedule->device_id);
        
        if (!$device) {
            echo "  → Error: Device not found\n\n";
            continue;
        }
        
        // Skip if device is offline
        if (!$device->is_online) {
            echo "  → Skipped (device is offline - no heartbeat)\n";
            echo "  → Last heartbeat: " . ($device->last_heartbeat ? $device->last_heartbeat : "Never") . "\n\n";
            continue;
        }
        
        // Check if it's a one-time schedule that was already executed today
        if ($schedule->repeat_type === 'once' && $schedule->last_executed) {
            $lastExecutedDate = date('Y-m-d', strtotime($schedule->last_executed));
            $todayDate = date('Y-m-d');
            
            if ($lastExecutedDate === $todayDate) {
                echo "  → Skipped (one-time schedule already executed today)\n\n";
                continue;
            }
        }
        
        $timeOn = $schedule->time_on;
        $timeOff = $schedule->time_off;
        
        // Check if it's time to turn ON
        // Allow 1 minute window (e.g., if schedule is 08:00:00, trigger between 08:00:00 and 08:00:59)
        $timeOnStart = $timeOn;
        $timeOnEnd = date('H:i:s', strtotime($timeOn) + 59); // +59 seconds
        
        if ($currentTime >= $timeOnStart && $currentTime <= $timeOnEnd) {
            if ($device->outlet_status !== 'on' || $device->control_mode !== 'scheduled') {
                echo "  → ACTION: Turning ON (scheduled time)\n";
                
                $device->outlet_status = 'on';
                $device->control_mode = 'scheduled';
                
                if ($deviceRepo->update($device)) {
                    echo "  → SUCCESS: Device turned ON\n";
                    
                    // Update last_executed for one-time schedules (but DON'T disable yet)
                    if ($schedule->repeat_type === 'once') {
                        $stmt = $scheduleRepo->db->prepare(
                            'UPDATE iot_outlet_schedules SET last_executed = NOW() WHERE id = ?'
                        );
                        $stmt->execute([$schedule->id]);
                        echo "  → One-time schedule marked as executed (will disable after turning OFF)\n";
                    }
                    
                    // Log activity
                    logActivity($device->id, 'outlet_on', "Schedule '{$schedule->schedule_name}' turned outlet ON");
                } else {
                    echo "  → ERROR: Failed to update device\n";
                }
            } else {
                echo "  → Already ON\n";
            }
        }
        // Check if it's time to turn OFF
        elseif ($currentTime >= $timeOff && $currentTime <= date('H:i:s', strtotime($timeOff) + 59)) {
            if ($device->outlet_status !== 'off' && $device->control_mode === 'scheduled') {
                echo "  → ACTION: Turning OFF (scheduled time)\n";
                
                $device->outlet_status = 'off';
                $device->control_mode = 'scheduled';
                
                if ($deviceRepo->update($device)) {
                    echo "  → SUCCESS: Device turned OFF\n";
                    
                    // NOW disable one-time schedules after turning OFF
                    if ($schedule->repeat_type === 'once') {
                        $scheduleRepo->disable($schedule->id);
                        echo "  → One-time schedule disabled after completion\n";
                    }
                    
                    // Log activity
                    logActivity($device->id, 'outlet_off', "Schedule '{$schedule->schedule_name}' turned outlet OFF");
                } else {
                    echo "  → ERROR: Failed to update device\n";
                }
            } else {
                echo "  → Already OFF or not in scheduled mode\n";
            }
        }
        // Check if we're within the ON period
        elseif ($currentTime >= $timeOn && $currentTime < $timeOff) {
            echo "  → Within ON period (";
            if ($device->outlet_status === 'on' && $device->control_mode === 'scheduled') {
                echo "already ON)\n";
            } else {
                echo "but not in scheduled mode or manually controlled)\n";
            }
        }
        // Outside schedule time
        else {
            echo "  → Outside schedule time\n";
        }
        
        echo "\n";
    }
    
    echo "[" . date('Y-m-d H:i:s') . "] Schedule Executor Completed\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
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
        echo "Failed to log activity: " . $e->getMessage() . "\n";
    }
}
