<?php

/**
 * Schedule Controller
 * 
 * Handles schedule management
 */

class ScheduleController
{
    private ScheduleRepository $scheduleRepository;
    private DeviceRepository $deviceRepository;
    
    public function __construct()
    {
        $this->scheduleRepository = new ScheduleRepository();
        $this->deviceRepository = new DeviceRepository();
    }
    
    /**
     * Show schedules page
     */
    public function index(): void
    {
        AuthMiddleware::check();
        
        // Show all schedules to everyone (admin and regular users)
        // This helps avoid time conflicts between different users
        $schedules = $this->scheduleRepository->getAll();
        
        $devices = $this->deviceRepository->getAll();
        
        $this->view('schedule/index', [
            'schedules' => $schedules,
            'devices' => $devices,
            'user' => user()
        ]);
    }
    
    /**
     * Store new schedule (supports multiple devices)
     */
    public function store(): void
    {
        AuthMiddleware::check();
        AuthMiddleware::verifyCsrf();
        
        $data = [
            'schedule_name' => $_POST['schedule_name'] ?? '',
            'device_ids' => $_POST['device_ids'] ?? [], // Now supports multiple device IDs
            'time_on' => $_POST['time_on'] ?? '',
            'time_off' => $_POST['time_off'] ?? '',
            'days_of_week' => $_POST['days_of_week'] ?? [],
            'is_enabled' => isset($_POST['is_enabled']) ? 1 : 0,
            'repeat_type' => $_POST['repeat_type'] ?? 'repeat',
            'schedule_date' => $_POST['schedule_date'] ?? null
        ];
        
        // Validate device selection
        if (empty($data['device_ids']) || !is_array($data['device_ids'])) {
            $_SESSION['error'] = 'Please select at least one device.';
            redirect(url('/schedule'));
            return;
        }
        
        $errors = $this->validateSchedule($data);
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode(' ', $errors);
            redirect(url('/schedule'));
            return;
        }
        
        $successCount = 0;
        $allWarnings = [];
        
        // Create schedule for each selected device
        foreach ($data['device_ids'] as $deviceId) {
            $deviceId = (int)$deviceId;
            
            // Get device info for schedule name
            $device = $this->deviceRepository->findById($deviceId);
            if (!$device) {
                continue;
            }
            
            $schedule = new Schedule();
            $schedule->user_id = user()['id'];
            $schedule->device_id = $deviceId;
            $schedule->schedule_name = $data['schedule_name'];
            $schedule->time_on = $data['time_on'];
            $schedule->time_off = $data['time_off'];
            $schedule->days_of_week = is_array($data['days_of_week']) 
                ? implode(',', $data['days_of_week']) 
                : $data['days_of_week'];
            $schedule->is_enabled = $data['is_enabled'];
            $schedule->repeat_type = $data['repeat_type'];
            $schedule->schedule_date = !empty($data['schedule_date']) ? $data['schedule_date'] : null;
            
            // Check for overlaps (warning only, doesn't block)
            $warnings = $this->checkScheduleOverlap(
                $schedule->device_id,
                $schedule->time_on,
                $schedule->time_off,
                $schedule->days_of_week
            );
            
            if ($this->scheduleRepository->create($schedule)) {
                $this->logActivity('schedule_created', "Created schedule: {$schedule->schedule_name} for device: {$device->device_name}");
                $successCount++;
                
                if (!empty($warnings)) {
                    $allWarnings[] = "Device '{$device->device_name}': " . implode(' ', $warnings);
                }
            }
        }
        
        // Set success/warning message
        if ($successCount > 0) {
            $deviceCount = count($data['device_ids']);
            if ($successCount === $deviceCount) {
                if (!empty($allWarnings)) {
                    $_SESSION['warning'] = "Schedule created for {$successCount} device(s) successfully. Note: " . implode(' | ', $allWarnings) . ' The most recently created schedule will take priority during conflicts.';
                } else {
                    $_SESSION['success'] = "Schedule created for {$successCount} device(s) successfully.";
                }
            } else {
                $_SESSION['warning'] = "Schedule created for {$successCount} out of {$deviceCount} device(s). Some devices failed.";
            }
        } else {
            $_SESSION['error'] = 'Failed to create schedule for any device.';
        }
        
        redirect(url('/schedule'));
    }
    
    /**
     * Update schedule
     */
    public function update(int $id): void
    {
        AuthMiddleware::check();
        AuthMiddleware::verifyCsrf();
        
        $schedule = $this->scheduleRepository->findById($id);
        
        if (!$schedule || ($schedule->user_id != user()['id'] && user()['role'] !== 'admin')) {
            $_SESSION['error'] = 'Schedule not found or access denied.';
            redirect(url('/schedule'));
            return;
        }
        
        $data = [
            'schedule_name' => $_POST['schedule_name'] ?? $schedule->schedule_name,
            'time_on' => $_POST['time_on'] ?? $schedule->time_on,
            'time_off' => $_POST['time_off'] ?? $schedule->time_off,
            'days_of_week' => $_POST['days_of_week'] ?? explode(',', $schedule->days_of_week),
            'is_enabled' => isset($_POST['is_enabled']) ? 1 : 0,
            'repeat_type' => $_POST['repeat_type'] ?? $schedule->repeat_type,
            'schedule_date' => $_POST['schedule_date'] ?? $schedule->schedule_date
        ];
        
        $schedule->schedule_name = $data['schedule_name'];
        $schedule->time_on = $data['time_on'];
        $schedule->time_off = $data['time_off'];
        $schedule->days_of_week = is_array($data['days_of_week']) 
            ? implode(',', $data['days_of_week']) 
            : $data['days_of_week'];
        $schedule->is_enabled = $data['is_enabled'];
        $schedule->repeat_type = $data['repeat_type'];
        $schedule->schedule_date = !empty($data['schedule_date']) ? $data['schedule_date'] : null;
        
        if ($this->scheduleRepository->update($schedule)) {
            $this->logActivity('schedule_updated', "Updated schedule: {$schedule->schedule_name}");
            $_SESSION['success'] = 'Schedule updated successfully.';
        } else {
            $_SESSION['error'] = 'Failed to update schedule.';
        }
        
        redirect(url('/schedule'));
    }
    
    /**
     * Delete schedule
     */
    public function delete(int $id): void
    {
        AuthMiddleware::check();
        AuthMiddleware::verifyCsrf();
        
        $schedule = $this->scheduleRepository->findById($id);
        
        if (!$schedule || ($schedule->user_id != user()['id'] && user()['role'] !== 'admin')) {
            $_SESSION['error'] = 'Schedule not found or access denied.';
            redirect(url('/schedule'));
            return;
        }
        
        $name = $schedule->schedule_name;
        $deviceId = $schedule->device_id;
        
        // Check if device is currently ON and in scheduled mode
        $deviceRepo = new DeviceRepository();
        $device = $deviceRepo->findById($deviceId);
        
        $shouldTurnOff = false;
        if ($device && $device->outlet_status === 'on' && $device->control_mode === 'scheduled') {
            // Check if we're currently within this schedule's ON period
            $currentTime = date('H:i:s');
            $timeOn = $schedule->time_on;
            $timeOff = $schedule->time_off;
            
            // If current time is between ON and OFF time, we should turn off
            if ($currentTime >= $timeOn && $currentTime < $timeOff) {
                // Check if today is in the schedule days
                $currentDay = date('l'); // Monday, Tuesday, etc.
                $scheduleDays = explode(',', $schedule->days_of_week);
                
                // Also check short day names
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
                
                if (in_array($currentDay, $scheduleDays) || in_array($currentDayShort, $scheduleDays)) {
                    // For one-time schedules, also check the date
                    if ($schedule->repeat_type === 'once' && !empty($schedule->schedule_date)) {
                        $scheduledDate = date('Y-m-d', strtotime($schedule->schedule_date));
                        $todayDate = date('Y-m-d');
                        
                        if ($scheduledDate === $todayDate) {
                            $shouldTurnOff = true;
                        }
                    } else {
                        // Repeating schedule - turn off
                        $shouldTurnOff = true;
                    }
                }
            }
        }
        
        // Delete the schedule
        if ($this->scheduleRepository->delete($id)) {
            $this->logActivity('schedule_deleted', "Deleted schedule: {$name}");
            
            // Turn off the outlet if needed
            if ($shouldTurnOff && $device) {
                $device->outlet_status = 'off';
                $device->control_mode = 'manual'; // Switch to manual mode
                
                if ($deviceRepo->update($device)) {
                    $this->logActivity('outlet_off', "Outlet turned OFF automatically after deleting active schedule '{$name}'");
                    $_SESSION['success'] = "Schedule deleted and outlet turned OFF automatically.";
                } else {
                    $_SESSION['success'] = 'Schedule deleted, but failed to turn off outlet. Please turn off manually.';
                }
            } else {
                $_SESSION['success'] = 'Schedule deleted successfully.';
            }
        } else {
            $_SESSION['error'] = 'Failed to delete schedule.';
        }
        
        redirect(url('/schedule'));
    }
    
    /**
     * Toggle schedule status
     */
    public function toggleStatus(int $id): void
    {
        AuthMiddleware::check();
        AuthMiddleware::verifyCsrf();
        
        $schedule = $this->scheduleRepository->findById($id);
        
        if (!$schedule || ($schedule->user_id != user()['id'] && user()['role'] !== 'admin')) {
            jsonResponse(['success' => false, 'message' => 'Not found'], 404);
            return;
        }
        
        $wasEnabled = $schedule->is_enabled;
        $schedule->is_enabled = !$schedule->is_enabled;
        
        // Check if we're disabling a schedule that currently has the outlet ON
        $shouldTurnOff = false;
        $device = null;
        
        if ($wasEnabled && !$schedule->is_enabled) {
            // We're disabling the schedule
            $deviceRepo = new DeviceRepository();
            $device = $deviceRepo->findById($schedule->device_id);
            
            if ($device && $device->outlet_status === 'on' && $device->control_mode === 'scheduled') {
                // Check if we're currently within this schedule's ON period
                $currentTime = date('H:i:s');
                $timeOn = $schedule->time_on;
                $timeOff = $schedule->time_off;
                
                if ($currentTime >= $timeOn && $currentTime < $timeOff) {
                    // Check if today is in the schedule days
                    $currentDay = date('l');
                    $scheduleDays = explode(',', $schedule->days_of_week);
                    
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
                    
                    if (in_array($currentDay, $scheduleDays) || in_array($currentDayShort, $scheduleDays)) {
                        // For one-time schedules, also check the date
                        if ($schedule->repeat_type === 'once' && !empty($schedule->schedule_date)) {
                            $scheduledDate = date('Y-m-d', strtotime($schedule->schedule_date));
                            $todayDate = date('Y-m-d');
                            
                            if ($scheduledDate === $todayDate) {
                                $shouldTurnOff = true;
                            }
                        } else {
                            $shouldTurnOff = true;
                        }
                    }
                }
            }
        }
        
        if ($this->scheduleRepository->update($schedule)) {
            $status = $schedule->is_enabled ? 'enabled' : 'disabled';
            $this->logActivity('schedule_' . $status, "Schedule {$status}: {$schedule->schedule_name}");
            
            // Turn off the outlet if needed
            if ($shouldTurnOff && $device) {
                $device->outlet_status = 'off';
                $device->control_mode = 'manual';
                
                $deviceRepo = new DeviceRepository();
                if ($deviceRepo->update($device)) {
                    $this->logActivity('outlet_off', "Outlet turned OFF automatically after disabling active schedule '{$schedule->schedule_name}'");
                }
            }
            
            jsonResponse([
                'success' => true,
                'is_enabled' => $schedule->is_enabled,
                'turned_off' => $shouldTurnOff
            ]);
        } else {
            jsonResponse(['success' => false, 'message' => 'Failed'], 500);
        }
    }
    
    private function validateSchedule(array $data): array
    {
        $errors = [];
        
        if (empty($data['schedule_name'])) {
            $errors[] = 'Schedule name is required.';
        }
        
        if (empty($data['time_on']) || empty($data['time_off'])) {
            $errors[] = 'Both ON and OFF times are required.';
        }
        
        if ($data['time_on'] === $data['time_off']) {
            $errors[] = 'ON and OFF times must be different.';
        }
        
        // Validate that Turn ON time is before Turn OFF time
        if (!empty($data['time_on']) && !empty($data['time_off'])) {
            $timeOn = strtotime($data['time_on']);
            $timeOff = strtotime($data['time_off']);
            
            if ($timeOn >= $timeOff) {
                $errors[] = 'Turn ON time must be earlier than Turn OFF time.';
            }
        }
        
        return $errors;
    }
    
    /**
     * Check for overlapping schedules and add warning (not blocking)
     */
    private function checkScheduleOverlap(int $deviceId, string $timeOn, string $timeOff, string $daysOfWeek, ?int $excludeScheduleId = null): array
    {
        $warnings = [];
        
        // Get all active schedules for this device
        $allSchedules = $this->scheduleRepository->getActive();
        
        foreach ($allSchedules as $schedule) {
            // Skip if it's not for the same device
            if ($schedule->device_id != $deviceId) {
                continue;
            }
            
            // Skip if it's the schedule we're currently editing
            if ($excludeScheduleId && $schedule->id == $excludeScheduleId) {
                continue;
            }
            
            // Check if days overlap
            $newDays = array_map('trim', explode(',', $daysOfWeek));
            $existingDays = array_map('trim', explode(',', $schedule->days_of_week));
            $commonDays = array_intersect($newDays, $existingDays);
            
            if (empty($commonDays)) {
                continue; // No overlap in days
            }
            
            // Check if times overlap
            $newStart = strtotime($timeOn);
            $newEnd = strtotime($timeOff);
            $existingStart = strtotime($schedule->time_on);
            $existingEnd = strtotime($schedule->time_off);
            
            // Check for overlap: (start1 < end2) AND (start2 < end1)
            if ($newStart < $existingEnd && $existingStart < $newEnd) {
                $daysList = implode(', ', $commonDays);
                $warnings[] = "⚠️ Overlaps with '{$schedule->schedule_name}' on {$daysList} ({$schedule->time_on} - {$schedule->time_off})";
            }
        }
        
        return $warnings;
    }
    
    private function logActivity(string $type, string $details): void
    {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare(
                'INSERT INTO iot_outlet_activity_logs (user_id, action_type, action_details, ip_address, status) 
                 VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->execute([user()['id'], $type, $details, $_SERVER['REMOTE_ADDR'] ?? 'unknown', 'success']);
        } catch (Exception $e) {
            error_log('Log failed: ' . $e->getMessage());
        }
    }
    
    private function view(string $view, array $data = []): void
    {
        extract($data);
        $viewFile = ROOT_PATH . '/app/views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        }
    }
}
