<?php

/**
 * Outlet Controller
 * 
 * Handles outlet control operations
 */

class OutletController
{
    private DeviceRepository $deviceRepository;
    private ScheduleRepository $scheduleRepository;
    
    public function __construct()
    {
        $this->deviceRepository = new DeviceRepository();
        $this->scheduleRepository = new ScheduleRepository();
    }
    
    /**
     * Check if there's an active schedule currently running (within its ON time) for a specific device
     * 
     * @param int|null $deviceId Optional device ID to check. If null, checks all schedules
     * @return array|null Returns schedule info if active, null otherwise
     */
    private function getActiveSchedule(?int $deviceId = null): ?array
    {
        $schedules = $this->scheduleRepository->getActive();
        $currentTime = date('H:i:s');
        $currentDay = date('l'); // Monday, Tuesday, etc.
        $todayDate = date('Y-m-d');
        
        foreach ($schedules as $schedule) {
            // If device ID is specified, only check schedules for that device
            if ($deviceId !== null && $schedule->device_id != $deviceId) {
                continue;
            }
            
            // Check if today is in the schedule days
            $scheduleDays = explode(',', $schedule->days_of_week);
            $scheduleDays = array_map('trim', $scheduleDays);
            
            if (!in_array($currentDay, $scheduleDays)) {
                continue;
            }
            
            // For one-time schedules, also check the date
            if ($schedule->repeat_type === 'once' && !empty($schedule->schedule_date)) {
                $scheduledDate = date('Y-m-d', strtotime($schedule->schedule_date));
                if ($scheduledDate !== $todayDate) {
                    continue; // Not today
                }
            }
            
            // Check if current time is within the ON period
            $timeOn = $schedule->time_on;
            $timeOff = $schedule->time_off;
            
            // If current time is between time_on and time_off, schedule is active
            if ($currentTime >= $timeOn && $currentTime < $timeOff) {
                return [
                    'schedule' => $schedule,
                    'is_active' => true
                ];
            }
        }
        
        return null;
    }
    
    /**
     * Show outlet control page
     * 
     * @return void
     */
    public function index(): void
    {
        AuthMiddleware::check();
        
        // Get all active devices
        $devices = $this->deviceRepository->getAllWithOfflineCheck();
        
        // Get device ID from query parameter or use first device
        $selectedDeviceId = (int)($_GET['device_id'] ?? 0);
        
        if ($selectedDeviceId && !empty($devices)) {
            $device = null;
            foreach ($devices as $d) {
                if ($d->id === $selectedDeviceId) {
                    $device = $d;
                    break;
                }
            }
            // If not found, use first device
            if (!$device) {
                $device = $devices[0];
            }
        } else {
            $device = !empty($devices) ? $devices[0] : null;
        }
        
        $this->view('outlet/index', [
            'devices' => $devices,
            'device' => $device,
            'user' => user()
        ]);
    }
    
    /**
     * Turn outlet ON
     * 
     * @return void
     */
    public function turnOn(): void
    {
        AuthMiddleware::check();
        AuthMiddleware::verifyCsrfJson();
        
        $devices = $this->deviceRepository->getAll();
        $device = !empty($devices) ? $devices[0] : null;
        
        if (!$device) {
            jsonResponse(['success' => false, 'message' => 'No device found'], 404);
            return;
        }
        
        // Check if device is online
        if (!$device->is_online) {
            jsonResponse([
                'success' => false, 
                'message' => '⚠️ Device is offline. Cannot control outlet. Please check ESP32 connection.',
                'device_status' => 'offline'
            ], 503);
            return;
        }
        
        // Update device status
        $device->outlet_status = 'on';
        $device->control_mode = 'manual';
        
        if ($this->deviceRepository->update($device)) {
            // Log activity
            $this->logActivity($device->id, 'outlet_on', 'Outlet turned ON via manual control');
            
            jsonResponse([
                'success' => true,
                'message' => '✅ Outlet turned ON successfully',
                'status' => 'on',
                'mode' => 'manual'
            ]);
        } else {
            jsonResponse(['success' => false, 'message' => 'Failed to turn ON outlet'], 500);
        }
    }
    
    /**
     * Turn outlet OFF
     * 
     * @return void
     */
    public function turnOff(): void
    {
        AuthMiddleware::check();
        AuthMiddleware::verifyCsrfJson();
        
        $devices = $this->deviceRepository->getAll();
        $device = !empty($devices) ? $devices[0] : null;
        
        if (!$device) {
            jsonResponse(['success' => false, 'message' => 'No device found'], 404);
            return;
        }
        
        // Check if device is online
        if (!$device->is_online) {
            jsonResponse([
                'success' => false, 
                'message' => '⚠️ Device is offline. Cannot control outlet. Please check ESP32 connection.',
                'device_status' => 'offline'
            ], 503);
            return;
        }
        
        // Check if there's an active schedule
        $user = user();
        $isAdmin = $user['role'] === 'admin';
        
        if ($device->outlet_status === 'on') {
            // Check for active schedule for THIS specific device
            $activeSchedule = $this->getActiveSchedule($device->id);
            
            if ($activeSchedule !== null) {
                $scheduleName = $activeSchedule['schedule']->schedule_name;
                $createdBy = $activeSchedule['schedule']->username ?? 'Unknown';
                $timeOff = date('g:i A', strtotime($activeSchedule['schedule']->time_off));
                
                if (!$isAdmin) {
                    // Regular user - block completely
                    jsonResponse([
                        'success' => false,
                        'message' => "🔒 Access Denied\n\nYou cannot turn OFF the outlet while a schedule is active.\n\nActive Schedule: {$scheduleName}\nCreated by: {$createdBy}\nScheduled OFF time: {$timeOff}\n\nOnly admins can override active schedules.",
                        'blocked_by_schedule' => true,
                        'schedule_info' => [
                            'name' => $scheduleName,
                            'created_by' => $createdBy,
                            'time_off' => $timeOff
                        ]
                    ], 403);
                    return;
                } else {
                    // Admin - require confirmation
                    $requireConfirmation = $_POST['confirm_override'] ?? false;
                    
                    if (!$requireConfirmation) {
                        jsonResponse([
                            'success' => false,
                            'requires_confirmation' => true,
                            'message' => "⚠️ Admin Override Required\n\nAn active schedule is currently running.\n\nActive Schedule: {$scheduleName}\nCreated by: {$createdBy}\nScheduled OFF time: {$timeOff}\n\nAre you sure you want to override this schedule?",
                            'schedule_info' => [
                                'name' => $scheduleName,
                                'created_by' => $createdBy,
                                'time_off' => $timeOff
                            ]
                        ], 409);
                        return;
                    }
                    // If confirmation provided, proceed with override
                }
            }
        }
        
        // Update device status
        $device->outlet_status = 'off';
        $device->control_mode = 'manual';
        
        if ($this->deviceRepository->update($device)) {
            // Log activity
            $this->logActivity($device->id, 'outlet_off', 'Outlet turned OFF via manual control');
            
            jsonResponse([
                'success' => true,
                'message' => '✅ Outlet turned OFF successfully',
                'status' => 'off',
                'mode' => 'manual'
            ]);
        } else {
            jsonResponse(['success' => false, 'message' => 'Failed to turn OFF outlet'], 500);
        }
    }
    
    /**
     * Toggle outlet status
     * 
     * @return void
     */
    public function toggle(): void
    {
        AuthMiddleware::check();
        AuthMiddleware::verifyCsrfJson();
        
        $devices = $this->deviceRepository->getAll();
        $device = !empty($devices) ? $devices[0] : null;
        
        if (!$device) {
            jsonResponse(['success' => false, 'message' => 'No device found'], 404);
            return;
        }
        
        // Determine new status
        $newStatus = $device->outlet_status === 'on' ? 'off' : 'on';
        
        // Check if trying to turn OFF while a schedule is active
        $user = user();
        $isAdmin = $user['role'] === 'admin';
        
        if ($newStatus === 'off' && $device->outlet_status === 'on') {
            // Check for active schedule for THIS specific device
            $activeSchedule = $this->getActiveSchedule($device->id);
            
            if ($activeSchedule !== null) {
                $scheduleName = $activeSchedule['schedule']->schedule_name;
                $createdBy = $activeSchedule['schedule']->username ?? 'Unknown';
                $timeOff = date('g:i A', strtotime($activeSchedule['schedule']->time_off));
                
                if (!$isAdmin) {
                    // Regular user - block completely
                    jsonResponse([
                        'success' => false,
                        'message' => "🔒 Access Denied\n\nYou cannot turn OFF the outlet while a schedule is active.\n\nActive Schedule: {$scheduleName}\nCreated by: {$createdBy}\nScheduled OFF time: {$timeOff}\n\nOnly admins can override active schedules.",
                        'blocked_by_schedule' => true,
                        'schedule_info' => [
                            'name' => $scheduleName,
                            'created_by' => $createdBy,
                            'time_off' => $timeOff
                        ]
                    ], 403);
                    return;
                } else {
                    // Admin - require confirmation
                    $requireConfirmation = $_POST['confirm_override'] ?? false;
                    
                    if (!$requireConfirmation) {
                        jsonResponse([
                            'success' => false,
                            'requires_confirmation' => true,
                            'message' => "⚠️ Admin Override Required\n\nAn active schedule is currently running.\n\nActive Schedule: {$scheduleName}\nCreated by: {$createdBy}\nScheduled OFF time: {$timeOff}\n\nAre you sure you want to override this schedule?",
                            'schedule_info' => [
                                'name' => $scheduleName,
                                'created_by' => $createdBy,
                                'time_off' => $timeOff
                            ]
                        ], 409);
                        return;
                    }
                    // If confirmation provided, proceed with override
                }
            }
        }
        
        // Toggle status
        $device->outlet_status = $newStatus;
        $device->control_mode = 'manual';
        
        if ($this->deviceRepository->update($device)) {
            // Log activity
            $action = $newStatus === 'on' ? 'outlet_on' : 'outlet_off';
            $this->logActivity($device->id, $action, "Outlet toggled to " . strtoupper($newStatus) . " via manual control");
            
            jsonResponse([
                'success' => true,
                'message' => "Outlet turned " . strtoupper($newStatus) . " successfully",
                'status' => $newStatus,
                'mode' => 'manual'
            ]);
        } else {
            jsonResponse(['success' => false, 'message' => 'Failed to toggle outlet'], 500);
        }
    }
    
    /**
     * Set control mode
     * 
     * @return void
     */
    public function setMode(): void
    {
        AuthMiddleware::check();
        AuthMiddleware::verifyCsrfJson();
        
        $mode = $_POST['mode'] ?? '';
        $deviceId = isset($_POST['device_id']) ? (int)$_POST['device_id'] : null;
        
        if (!in_array($mode, ['manual', 'scheduled', 'automatic'])) {
            jsonResponse(['success' => false, 'message' => 'Invalid control mode'], 400);
            return;
        }
        
        // If device_id is provided, update specific device, otherwise update first device (legacy)
        if ($deviceId) {
            $device = $this->deviceRepository->findById($deviceId);
        } else {
            $devices = $this->deviceRepository->getAll();
            $device = !empty($devices) ? $devices[0] : null;
        }
        
        if (!$device) {
            jsonResponse(['success' => false, 'message' => 'No device found'], 404);
            return;
        }
        
        // Update control mode
        $updated = $this->deviceRepository->updateControlMode($device->id, $mode);
        
        if ($updated) {
            // Log activity
            $this->logActivity($device->id, 'mode_changed', "Control mode changed to: " . strtoupper($mode) . " for device: {$device->device_name}");
            
            jsonResponse([
                'success' => true,
                'message' => "Control mode set to " . strtoupper($mode),
                'mode' => $mode
            ]);
        } else {
            jsonResponse(['success' => false, 'message' => 'Failed to set control mode'], 500);
        }
    }
    
    /**
     * Get current outlet status
     * 
     * @return void
     */
    public function getStatus(): void
    {
        AuthMiddleware::check();
        
        // Mark offline devices first
        $this->deviceRepository->markOfflineDevices();
        
        $devices = $this->deviceRepository->getAll();
        $device = !empty($devices) ? $devices[0] : null;
        
        if (!$device) {
            jsonResponse(['success' => false, 'message' => 'No device found'], 404);
            return;
        }
        
        jsonResponse([
            'success' => true,
            'status' => $device->outlet_status,
            'mode' => $device->control_mode,
            'is_online' => $device->is_online,
            'last_heartbeat' => $device->last_heartbeat,
            'device_name' => $device->device_name
        ]);
    }
    
    /**
     * Check if there's an active schedule running right now
     * 
     * @return void
     */
    public function checkActiveSchedule(): void
    {
        AuthMiddleware::check();
        
        $activeSchedule = $this->getActiveSchedule();
        
        if ($activeSchedule !== null) {
            jsonResponse([
                'success' => true,
                'has_active_schedule' => true,
                'schedule_info' => [
                    'name' => $activeSchedule['schedule']->schedule_name,
                    'created_by' => $activeSchedule['schedule']->username ?? 'Unknown',
                    'time_off' => date('g:i A', strtotime($activeSchedule['schedule']->time_off))
                ]
            ]);
        } else {
            jsonResponse([
                'success' => true,
                'has_active_schedule' => false
            ]);
        }
    }
    
    /**
     * Control single device (used in dashboard and outlet pages)
     * 
     * @return void
     */
    public function controlDevice(): void
    {
        AuthMiddleware::check();
        AuthMiddleware::verifyCsrfJson();
        
        $deviceId = isset($_POST['device_id']) ? (int)$_POST['device_id'] : 0;
        $action = $_POST['action'] ?? '';
        
        if (!$deviceId || !in_array($action, ['on', 'off'])) {
            jsonResponse(['success' => false, 'message' => 'Invalid device ID or action'], 400);
            return;
        }
        
        $device = $this->deviceRepository->findById($deviceId);
        
        if (!$device) {
            jsonResponse(['success' => false, 'message' => 'Device not found'], 404);
            return;
        }
        
        // Check if device is online
        if (!$device->is_online) {
            jsonResponse([
                'success' => false,
                'message' => '⚠️ Device is offline. Cannot control outlet.',
                'device_status' => 'offline'
            ], 503);
            return;
        }
        
        // Check schedule protection
        $user = user();
        $isAdmin = $user['role'] === 'admin';
        
        if ($action === 'off' && $device->outlet_status === 'on') {
            $activeSchedule = $this->getActiveSchedule($deviceId);
            
            if ($activeSchedule !== null) {
                $scheduleName = $activeSchedule['schedule']->schedule_name;
                $createdBy = $activeSchedule['schedule']->username ?? 'Unknown';
                $timeOff = date('g:i A', strtotime($activeSchedule['schedule']->time_off));
                
                if (!$isAdmin) {
                    // Regular user - block completely
                    jsonResponse([
                        'success' => false,
                        'message' => "🔒 Schedule Protection Active\n\nCannot turn OFF while schedule is running.\n\nSchedule: {$scheduleName}\nCreated by: {$createdBy}\nEnds at: {$timeOff}\n\nOnly admins can override.",
                        'blocked_by_schedule' => true,
                        'schedule_info' => [
                            'name' => $scheduleName,
                            'created_by' => $createdBy,
                            'time_off' => $timeOff
                        ]
                    ], 403);
                    return;
                } else {
                    // Admin - require confirmation
                    $requireConfirmation = $_POST['confirm_override'] ?? false;
                    
                    if (!$requireConfirmation) {
                        jsonResponse([
                            'success' => false,
                            'requires_confirmation' => true,
                            'is_admin_override' => true,
                            'message' => "⚠️ Admin Override Required\n\nAn active schedule is currently running.\n\nSchedule: {$scheduleName}\nCreated by: {$createdBy}\nEnds at: {$timeOff}\n\nAre you sure you want to override?",
                            'schedule_info' => [
                                'name' => $scheduleName,
                                'created_by' => $createdBy,
                                'time_off' => $timeOff
                            ]
                        ], 409);
                        return;
                    }
                    // If confirmation provided, proceed with override
                }
            }
        }
        
        // Update device
        $device->outlet_status = $action;
        $device->control_mode = 'manual';
        
        if ($this->deviceRepository->update($device)) {
            $this->logActivity(
                $deviceId,
                'outlet_' . $action,
                "Device '{$device->device_name}' turned " . strtoupper($action) . " via manual control"
            );
            
            jsonResponse([
                'success' => true,
                'message' => "✅ Device turned " . strtoupper($action) . " successfully",
                'status' => $action,
                'mode' => 'manual'
            ]);
        } else {
            jsonResponse(['success' => false, 'message' => 'Failed to control device'], 500);
        }
    }
    
    /**
     * Override schedule (admin only)
     * 
     * @return void
     */
    public function override(): void
    {
        AuthMiddleware::admin();
        AuthMiddleware::verifyCsrfJson();
        
        $action = $_POST['action'] ?? '';
        
        if (!in_array($action, ['on', 'off'])) {
            jsonResponse(['success' => false, 'message' => 'Invalid action'], 400);
            return;
        }
        
        $devices = $this->deviceRepository->getAll();
        $device = !empty($devices) ? $devices[0] : null;
        
        if (!$device) {
            jsonResponse(['success' => false, 'message' => 'No device found'], 404);
            return;
        }
        
        $device->outlet_status = $action;
        $device->control_mode = 'override';
        
        if ($this->deviceRepository->update($device)) {
            // Log activity
            $actionType = $action === 'on' ? 'outlet_on' : 'outlet_off';
            $this->logActivity($device->id, $actionType, "Override: Outlet forced " . strtoupper($action));
            
            jsonResponse([
                'success' => true,
                'message' => "Override: Outlet forced " . strtoupper($action),
                'status' => $action,
                'mode' => 'override'
            ]);
        } else {
            jsonResponse(['success' => false, 'message' => 'Failed to override'], 500);
        }
    }
    
    /**
     * Bulk control multiple outlets
     * 
     * @return void
     */
    public function bulkControl(): void
    {
        AuthMiddleware::check();
        AuthMiddleware::verifyCsrfJson();
        
        $deviceIdsJson = $_POST['device_ids'] ?? '';
        $action = $_POST['action'] ?? '';
        
        if (empty($deviceIdsJson) || empty($action)) {
            jsonResponse(['success' => false, 'message' => 'Device IDs and action are required']);
            return;
        }
        
        $deviceIds = json_decode($deviceIdsJson, true);
        if (!is_array($deviceIds) || empty($deviceIds)) {
            jsonResponse(['success' => false, 'message' => 'Invalid device IDs']);
            return;
        }
        
        if (!in_array($action, ['on', 'off'])) {
            jsonResponse(['success' => false, 'message' => 'Invalid action']);
            return;
        }
        
        $successCount = 0;
        $failedCount = 0;
        $offlineCount = 0;
        $blockedCount = 0;
        $blockedDevices = [];
        
        // Check if user is admin
        $user = user();
        $isAdmin = $user['role'] === 'admin';
        
        foreach ($deviceIds as $deviceId) {
            $device = $this->deviceRepository->findById((int)$deviceId);
            
            if (!$device) {
                $failedCount++;
                continue;
            }
            
            if (!$device->is_online) {
                $offlineCount++;
                continue;
            }
            
            // Check schedule protection for regular users trying to turn OFF
            if (!$isAdmin && $action === 'off' && $device->outlet_status === 'on') {
                $activeSchedule = $this->getActiveSchedule($device->id);
                
                if ($activeSchedule !== null) {
                    $blockedCount++;
                    $blockedDevices[] = $device->device_name;
                    continue; // Skip this device
                }
            }
            
            // Update outlet status
            $updated = $this->deviceRepository->updateById((int)$deviceId, [
                'outlet_status' => $action,
                'control_mode' => 'manual' // Switch to manual when manually controlled
            ]);
            
            if ($updated) {
                // Log activity
                $this->logActivity(
                    (int)$deviceId,
                    'outlet_' . $action,
                    "Outlet turned " . strtoupper($action) . " via bulk control"
                );
                $successCount++;
            } else {
                $failedCount++;
            }
        }
        
        $message = "Bulk control completed: $successCount successful";
        if ($offlineCount > 0) {
            $message .= ", $offlineCount offline (skipped)";
        }
        if ($blockedCount > 0) {
            $message .= ", $blockedCount blocked by active schedule (" . implode(', ', $blockedDevices) . ")";
        }
        if ($failedCount > 0) {
            $message .= ", $failedCount failed";
        }
        
        jsonResponse([
            'success' => $successCount > 0,
            'message' => $message,
            'details' => [
                'success' => $successCount,
                'offline' => $offlineCount,
                'blocked' => $blockedCount,
                'failed' => $failedCount,
                'blocked_devices' => $blockedDevices
            ]
        ]);
    }
    
    /**
     * Bulk set mode for multiple devices
     * 
     * @return void
     */
    public function bulkSetMode(): void
    {
        AuthMiddleware::check();
        AuthMiddleware::verifyCsrfJson();
        
        $deviceIdsJson = $_POST['device_ids'] ?? '';
        $mode = $_POST['mode'] ?? '';
        
        if (empty($deviceIdsJson) || empty($mode)) {
            jsonResponse(['success' => false, 'message' => 'Device IDs and mode are required']);
            return;
        }
        
        $deviceIds = json_decode($deviceIdsJson, true);
        if (!is_array($deviceIds) || empty($deviceIds)) {
            jsonResponse(['success' => false, 'message' => 'Invalid device IDs']);
            return;
        }
        
        if (!in_array($mode, ['manual', 'scheduled', 'automatic'])) {
            jsonResponse(['success' => false, 'message' => 'Invalid mode']);
            return;
        }
        
        $successCount = 0;
        $failedCount = 0;
        
        foreach ($deviceIds as $deviceId) {
            $device = $this->deviceRepository->findById((int)$deviceId);
            
            if (!$device) {
                $failedCount++;
                continue;
            }
            
            // Update control mode
            $updated = $this->deviceRepository->updateById((int)$deviceId, [
                'control_mode' => $mode
            ]);
            
            if ($updated) {
                // Log activity
                $this->logActivity(
                    (int)$deviceId,
                    'mode_changed',
                    "Control mode changed to " . ucfirst($mode) . " via bulk control"
                );
                $successCount++;
            } else {
                $failedCount++;
            }
        }
        
        $message = "Bulk mode change completed: $successCount successful";
        if ($failedCount > 0) {
            $message .= ", $failedCount failed";
        }
        
        jsonResponse([
            'success' => $successCount > 0,
            'message' => $message,
            'details' => [
                'success' => $successCount,
                'failed' => $failedCount
            ]
        ]);
    }
    
    /**
     * Log activity
     * 
     * @param int $deviceId
     * @param string $actionType
     * @param string $details
     * @return void
     */
    private function logActivity(int $deviceId, string $actionType, string $details): void
    {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare(
                'INSERT INTO iot_outlet_activity_logs (user_id, device_id, action_type, action_details, ip_address, user_agent, status) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            
            $stmt->execute([
                user()['id'],
                $deviceId,
                $actionType,
                $details,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                'success'
            ]);
        } catch (Exception $e) {
            error_log('Failed to log activity: ' . $e->getMessage());
        }
    }
    
    /**
     * Load view
     * 
     * @param string $view
     * @param array $data
     * @return void
     */
    private function view(string $view, array $data = []): void
    {
        extract($data);
        
        $viewFile = ROOT_PATH . '/app/views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            echo "View not found: {$view}";
        }
    }
}
