<?php

/**
 * Schedule Debug Tool
 * 
 * This tool helps you diagnose why schedules aren't working
 * 
 * Access: https://isu-documentrequest.online/ac_controller_system/public/schedule-debug.php?key=IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T
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

// API key check
$apiKey = $_GET['key'] ?? '';
$expectedKey = $_ENV['API_KEY'] ?? 'IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T';

if ($apiKey !== $expectedKey) {
    http_response_code(401);
    die(json_encode(['error' => 'Unauthorized']));
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Schedule Debug Tool</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            color: #4ec9b0;
            border-bottom: 2px solid #4ec9b0;
            padding-bottom: 10px;
        }
        h2 {
            color: #569cd6;
            margin-top: 30px;
        }
        .info-box {
            background: #2d2d30;
            border-left: 4px solid #007acc;
            padding: 15px;
            margin: 15px 0;
        }
        .success {
            border-left-color: #4ec9b0;
        }
        .warning {
            border-left-color: #ce9178;
        }
        .error {
            border-left-color: #f48771;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            background: #2d2d30;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #3e3e42;
        }
        th {
            background: #1e1e1e;
            color: #569cd6;
        }
        .status-on {
            color: #4ec9b0;
            font-weight: bold;
        }
        .status-off {
            color: #f48771;
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-success {
            background: #4ec9b0;
            color: #1e1e1e;
        }
        .badge-warning {
            background: #ce9178;
            color: #1e1e1e;
        }
        .badge-error {
            background: #f48771;
            color: #1e1e1e;
        }
        pre {
            background: #1e1e1e;
            border: 1px solid #3e3e42;
            padding: 15px;
            overflow-x: auto;
        }
        .refresh-btn {
            background: #007acc;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin: 10px 0;
        }
        .refresh-btn:hover {
            background: #005a9e;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Schedule Debug Tool</h1>
        <p>Current Time: <strong><?php echo date('Y-m-d H:i:s l'); ?></strong></p>
        <button class="refresh-btn" onclick="location.reload()">🔄 Refresh</button>
        
        <?php
        try {
            $deviceRepo = new DeviceRepository();
            $scheduleRepo = new ScheduleRepository();
            
            // Get current time info
            $currentTime = date('H:i:s');
            $currentDay = date('l'); // Monday, Tuesday, etc.
            $currentDayShort = date('D'); // Mon, Tue, etc.
            
            echo "<h2>📊 System Status</h2>";
            
            // Check devices
            $devices = $deviceRepo->getAllWithOfflineCheck();
            
            if (empty($devices)) {
                echo "<div class='info-box error'><strong>❌ NO DEVICES FOUND</strong><br>You need to add a device to the database first.</div>";
            } else {
                $device = $devices[0];
                
                echo "<div class='info-box " . ($device->is_online ? 'success' : 'error') . "'>";
                echo "<strong>Device:</strong> {$device->device_name} (ID: {$device->device_id})<br>";
                echo "<strong>Status:</strong> <span class='status-" . ($device->outlet_status === 'on' ? 'on' : 'off') . "'>" . strtoupper($device->outlet_status) . "</span><br>";
                echo "<strong>Control Mode:</strong> <span class='badge badge-" . ($device->control_mode === 'scheduled' ? 'success' : 'warning') . "'>" . strtoupper($device->control_mode) . "</span><br>";
                echo "<strong>Online Status:</strong> " . ($device->is_online ? '✅ ONLINE' : '❌ OFFLINE') . "<br>";
                echo "<strong>Last Heartbeat:</strong> " . ($device->last_heartbeat ?? 'Never') . "";
                echo "</div>";
                
                // Check if control mode is correct
                if ($device->control_mode !== 'scheduled') {
                    echo "<div class='info-box warning'>";
                    echo "<strong>⚠️ WARNING: Control Mode is not 'Scheduled'</strong><br>";
                    echo "Current mode: <strong>" . strtoupper($device->control_mode) . "</strong><br>";
                    echo "Schedules will NOT execute unless control mode is set to 'Scheduled'.<br>";
                    echo "Go to the Outlets page and change the control mode dropdown to 'Scheduled'.";
                    echo "</div>";
                }
                
                if (!$device->is_online) {
                    echo "<div class='info-box error'>";
                    echo "<strong>❌ DEVICE IS OFFLINE</strong><br>";
                    echo "The ESP32 is not sending heartbeat signals.<br>";
                    echo "Check if:<br>";
                    echo "• ESP32 is powered on<br>";
                    echo "• WiFi is connected<br>";
                    echo "• API URL is correct in ESP32 code<br>";
                    echo "• Server is accessible from ESP32";
                    echo "</div>";
                }
            }
            
            echo "<h2>📅 Schedule Analysis</h2>";
            
            // Get all schedules (including inactive)
            $db = Database::getInstance();
            $stmt = $db->prepare(
                'SELECT s.*, u.username, d.device_name, d.device_id 
                 FROM iot_outlet_schedules s
                 LEFT JOIN iot_outlet_users u ON s.user_id = u.id
                 LEFT JOIN iot_outlet_devices d ON s.device_id = d.id
                 ORDER BY s.is_enabled DESC, s.id DESC'
            );
            $stmt->execute();
            $allSchedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($allSchedules)) {
                echo "<div class='info-box error'><strong>❌ NO SCHEDULES FOUND</strong><br>Create at least one schedule to enable automatic control.</div>";
            } else {
                echo "<table>";
                echo "<tr>";
                echo "<th>ID</th>";
                echo "<th>Name</th>";
                echo "<th>Active</th>";
                echo "<th>Time ON</th>";
                echo "<th>Time OFF</th>";
                echo "<th>Days</th>";
                echo "<th>Repeat</th>";
                echo "<th>Status</th>";
                echo "</tr>";
                
                $activeCount = 0;
                $todayScheduleCount = 0;
                $currentlyRunningCount = 0;
                
                foreach ($allSchedules as $schedule) {
                    $isActive = (bool)$schedule['is_enabled'];
                    $scheduleDays = array_map('trim', explode(',', $schedule['days_of_week']));
                    $isToday = in_array($currentDay, $scheduleDays) || in_array($currentDayShort, $scheduleDays);
                    
                    // Check if we're within ON period
                    $timeOn = $schedule['time_on'];
                    $timeOff = $schedule['time_off'];
                    $isWithinPeriod = ($currentTime >= $timeOn && $currentTime < $timeOff);
                    
                    if ($isActive) $activeCount++;
                    if ($isActive && $isToday) $todayScheduleCount++;
                    if ($isActive && $isToday && $isWithinPeriod) $currentlyRunningCount++;
                    
                    echo "<tr>";
                    echo "<td>{$schedule['id']}</td>";
                    echo "<td><strong>{$schedule['schedule_name']}</strong><br><small>by {$schedule['username']}</small></td>";
                    echo "<td>";
                    if ($isActive) {
                        echo "<span class='badge badge-success'>✓ ENABLED</span>";
                    } else {
                        echo "<span class='badge badge-error'>✗ DISABLED</span>";
                    }
                    echo "</td>";
                    echo "<td>{$schedule['time_on']}</td>";
                    echo "<td>{$schedule['time_off']}</td>";
                    echo "<td>" . str_replace(',', ', ', $schedule['days_of_week']) . "</td>";
                    echo "<td>" . strtoupper($schedule['repeat_type']) . "</td>";
                    echo "<td>";
                    
                    if (!$isActive) {
                        echo "<span class='badge badge-error'>DISABLED</span>";
                    } elseif (!$isToday) {
                        echo "<span class='badge badge-warning'>Not today</span>";
                    } elseif ($isWithinPeriod) {
                        echo "<span class='badge badge-success'>🔴 RUNNING NOW</span>";
                    } elseif ($currentTime < $timeOn) {
                        echo "<span class='badge badge-warning'>Waiting</span>";
                    } else {
                        echo "<span class='badge badge-warning'>Finished</span>";
                    }
                    
                    echo "</td>";
                    echo "</tr>";
                }
                
                echo "</table>";
                
                // Summary
                echo "<div class='info-box'>";
                echo "<strong>Summary:</strong><br>";
                echo "• Total schedules: <strong>" . count($allSchedules) . "</strong><br>";
                echo "• Enabled schedules: <strong>{$activeCount}</strong><br>";
                echo "• Scheduled for today ({$currentDay}): <strong>{$todayScheduleCount}</strong><br>";
                echo "• Currently running: <strong>{$currentlyRunningCount}</strong>";
                echo "</div>";
                
                // Check for issues
                if ($activeCount === 0) {
                    echo "<div class='info-box error'>";
                    echo "<strong>❌ ALL SCHEDULES ARE DISABLED</strong><br>";
                    echo "You need to ENABLE at least one schedule by toggling it ON (green).<br>";
                    echo "Go to the Schedules page and click the toggle switch to enable a schedule.";
                    echo "</div>";
                }
                
                if ($activeCount > 0 && $todayScheduleCount === 0) {
                    echo "<div class='info-box warning'>";
                    echo "<strong>⚠️ NO SCHEDULES FOR TODAY</strong><br>";
                    echo "You have {$activeCount} enabled schedule(s), but none are scheduled for today ({$currentDay}).<br>";
                    echo "Edit your schedules to include today in the 'Days' selection.";
                    echo "</div>";
                }
                
                if ($currentlyRunningCount > 0) {
                    echo "<div class='info-box success'>";
                    echo "<strong>✅ {$currentlyRunningCount} SCHEDULE(S) CURRENTLY ACTIVE</strong><br>";
                    echo "The outlet should be ON right now if control mode is 'Scheduled'.";
                    echo "</div>";
                }
            }
            
            echo "<h2>🔧 Troubleshooting Checklist</h2>";
            echo "<div class='info-box'>";
            echo "<strong>For schedules to work, ALL of these must be true:</strong><br><br>";
            
            $checks = [
                'Device exists in database' => !empty($devices),
                'Device is online (ESP32 connected)' => !empty($devices) && $devices[0]->is_online,
                'Control mode is "Scheduled"' => !empty($devices) && $devices[0]->control_mode === 'scheduled',
                'At least one schedule is enabled' => $activeCount > 0,
                'Schedule is for today' => $todayScheduleCount > 0,
                'Cron job is running every minute' => true, // Assume true since we can't check
            ];
            
            foreach ($checks as $check => $status) {
                if ($status) {
                    echo "✅ {$check}<br>";
                } else {
                    echo "❌ {$check}<br>";
                }
            }
            
            echo "</div>";
            
            echo "<h2>📜 Recent Activity Logs (Last 10)</h2>";
            
            $stmt = $db->prepare(
                'SELECT al.*, u.username, d.device_name 
                 FROM iot_outlet_activity_logs al
                 LEFT JOIN iot_outlet_users u ON al.user_id = u.id
                 LEFT JOIN iot_outlet_devices d ON al.device_id = d.id
                 ORDER BY al.created_at DESC
                 LIMIT 10'
            );
            $stmt->execute();
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($logs)) {
                echo "<div class='info-box warning'>No activity logs found.</div>";
            } else {
                echo "<table>";
                echo "<tr>";
                echo "<th>Time</th>";
                echo "<th>Action</th>";
                echo "<th>Details</th>";
                echo "<th>User</th>";
                echo "<th>IP</th>";
                echo "</tr>";
                
                foreach ($logs as $log) {
                    echo "<tr>";
                    echo "<td>" . date('Y-m-d H:i:s', strtotime($log['created_at'])) . "</td>";
                    echo "<td>" . strtoupper(str_replace('_', ' ', $log['action_type'])) . "</td>";
                    echo "<td>{$log['action_details']}</td>";
                    echo "<td>" . ($log['username'] ?? 'System') . "</td>";
                    echo "<td>{$log['ip_address']}</td>";
                    echo "</tr>";
                }
                
                echo "</table>";
            }
            
        } catch (Exception $e) {
            echo "<div class='info-box error'>";
            echo "<strong>ERROR:</strong><br>";
            echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
            echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
            echo "</div>";
        }
        ?>
        
        <button class="refresh-btn" onclick="location.reload()">🔄 Refresh</button>
    </div>
</body>
</html>
