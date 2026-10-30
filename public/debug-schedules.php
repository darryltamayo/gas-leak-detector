<?php
/**
 * Debug Script - Check Why Only One Device Executes
 * 
 * URL: https://isu-documentrequest.online/ac_controller_system/public/debug-schedules.php
 */

// Define ROOT_PATH
define('ROOT_PATH', dirname(__DIR__));

// Load environment and dependencies
require_once ROOT_PATH . '/app/helpers/EnvironmentHelper.php';
EnvironmentHelper::load(ROOT_PATH . '/.env');

require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/app/Database.php';
require_once ROOT_PATH . '/app/repositories/DeviceRepository.php';
require_once ROOT_PATH . '/app/repositories/ScheduleRepository.php';
require_once ROOT_PATH . '/app/models/Device.php';
require_once ROOT_PATH . '/app/models/Schedule.php';

header('Content-Type: application/json');

$deviceRepo = new DeviceRepository();
$scheduleRepo = new ScheduleRepository();

$debug = [];
$debug['timestamp'] = date('Y-m-d H:i:s');
$debug['current_day'] = date('l');
$debug['current_time'] = date('H:i:s');

// Get all schedules
$allSchedules = $scheduleRepo->getAll();
$activeSchedules = $scheduleRepo->getActive();

$debug['total_schedules'] = count($allSchedules);
$debug['active_schedules'] = count($activeSchedules);

// Get all devices
$allDevices = $deviceRepo->getAll();
$debug['total_devices'] = count($allDevices);

// Detailed schedule info
$debug['schedules'] = [];
foreach ($allSchedules as $schedule) {
    $device = $deviceRepo->findById($schedule->device_id);
    
    $scheduleInfo = [
        'id' => $schedule->id,
        'name' => $schedule->schedule_name,
        'device_id' => $schedule->device_id,
        'device_name' => $device ? $device->device_name : 'NOT FOUND',
        'time_on' => $schedule->time_on,
        'time_off' => $schedule->time_off,
        'days' => $schedule->days_of_week,
        'repeat_type' => $schedule->repeat_type,
        'is_enabled' => $schedule->is_enabled ? 'YES' : 'NO',
        'last_executed' => $schedule->last_executed ?? 'Never',
        'device_status' => null
    ];
    
    if ($device) {
        $scheduleInfo['device_status'] = [
            'is_online' => $device->is_online ? 'ONLINE' : 'OFFLINE',
            'control_mode' => $device->control_mode,
            'outlet_status' => $device->outlet_status,
            'last_heartbeat' => $device->last_heartbeat ?? 'Never',
        ];
        
        // Check why it might not execute
        $blockers = [];
        
        if (!$schedule->is_enabled) {
            $blockers[] = 'Schedule is DISABLED';
        }
        
        if (!$device->is_online) {
            $blockers[] = 'Device is OFFLINE';
        }
        
        if ($device->control_mode !== 'scheduled') {
            $blockers[] = "Control mode is '{$device->control_mode}' (needs 'scheduled')";
        }
        
        // Check if today is in schedule days
        $scheduleDays = array_map('trim', explode(',', $schedule->days_of_week));
        $currentDay = date('l');
        $dayShortNames = [
            'Monday' => 'Mon', 'Tuesday' => 'Tue', 'Wednesday' => 'Wed',
            'Thursday' => 'Thu', 'Friday' => 'Fri', 'Saturday' => 'Sat', 'Sunday' => 'Sun'
        ];
        $currentDayShort = $dayShortNames[$currentDay] ?? $currentDay;
        
        if (!in_array($currentDay, $scheduleDays) && !in_array($currentDayShort, $scheduleDays)) {
            $blockers[] = "Today ({$currentDay}) not in schedule days";
        }
        
        // Check if it's time to execute
        $currentTime = date('H:i:s');
        $timeOn = $schedule->time_on;
        $timeOff = $schedule->time_off;
        $timeOnEnd = date('H:i:s', strtotime($timeOn) + 59);
        $timeOffEnd = date('H:i:s', strtotime($timeOff) + 59);
        
        if ($currentTime >= $timeOn && $currentTime <= $timeOnEnd) {
            $scheduleInfo['should_execute_now'] = 'YES - Turn ON';
        } elseif ($currentTime >= $timeOff && $currentTime <= $timeOffEnd) {
            $scheduleInfo['should_execute_now'] = 'YES - Turn OFF';
        } elseif ($currentTime >= $timeOn && $currentTime < $timeOff) {
            $scheduleInfo['should_execute_now'] = 'Within ON period (already ON)';
        } else {
            $scheduleInfo['should_execute_now'] = 'NO - Outside schedule time';
        }
        
        if ($schedule->repeat_type === 'once' && $schedule->last_executed) {
            $blockers[] = 'One-time schedule already executed';
        }
        
        $scheduleInfo['blockers'] = empty($blockers) ? ['NONE - Should execute!'] : $blockers;
    } else {
        $scheduleInfo['blockers'] = ['CRITICAL: Device not found in database!'];
    }
    
    $debug['schedules'][] = $scheduleInfo;
}

// Device details
$debug['devices'] = [];
foreach ($allDevices as $device) {
    $debug['devices'][] = [
        'id' => $device->id,
        'device_id' => $device->device_id,
        'device_name' => $device->device_name,
        'is_online' => $device->is_online ? 'ONLINE' : 'OFFLINE',
        'control_mode' => $device->control_mode,
        'outlet_status' => $device->outlet_status,
        'last_heartbeat' => $device->last_heartbeat ?? 'Never',
        'ip_address' => $device->ip_address ?? 'Unknown'
    ];
}

// Summary of issues
$debug['summary'] = [];

$offlineDevices = array_filter($allDevices, fn($d) => !$d->is_online);
if (!empty($offlineDevices)) {
    $debug['summary'][] = '⚠️ ' . count($offlineDevices) . ' device(s) are OFFLINE';
}

$manualModeDevices = array_filter($allDevices, fn($d) => $d->control_mode !== 'scheduled');
if (!empty($manualModeDevices)) {
    $names = implode(', ', array_map(fn($d) => $d->device_name, $manualModeDevices));
    $debug['summary'][] = '⚠️ ' . count($manualModeDevices) . " device(s) in MANUAL mode (not scheduled): {$names}";
}

$disabledSchedules = array_filter($allSchedules, fn($s) => !$s->is_enabled);
if (!empty($disabledSchedules)) {
    $debug['summary'][] = '⚠️ ' . count($disabledSchedules) . ' schedule(s) are DISABLED';
}

if (empty($debug['summary'])) {
    $debug['summary'][] = '✅ No obvious issues found!';
}

// Recommendations
$debug['recommendations'] = [
    '1. Check that BOTH devices are ONLINE (sending heartbeat)',
    '2. Check that BOTH devices have control_mode = "scheduled"',
    '3. Check that BOTH schedules are is_enabled = true',
    '4. Check that the schedule time matches current time',
    '5. Check that today is in the schedule days',
    '6. Access the cronjob URL and check the output for details'
];

echo json_encode($debug, JSON_PRETTY_PRINT);
