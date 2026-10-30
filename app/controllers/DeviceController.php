<?php

/**
 * Device Controller
 * 
 * Handles ESP32 device management for admins
 */

class DeviceController
{
    private DeviceRepository $deviceRepository;
    
    public function __construct()
    {
        AuthMiddleware::admin(); // This checks both auth and admin role
        
        $this->deviceRepository = new DeviceRepository();
    }
    
    /**
     * Display list of all devices
     * 
     * @return void
     */
    public function index(): void
    {
        $devices = $this->deviceRepository->getAllWithOfflineCheck();
        
        $this->view('admin/devices', [
            'devices' => $devices,
            'title' => 'ESP32 Devices',
            'message' => $_SESSION['device_message'] ?? null,
            'error' => $_SESSION['device_error'] ?? null
        ]);
        
        unset($_SESSION['device_message'], $_SESSION['device_error']);
    }
    
    /**
     * Show create device form
     * 
     * @return void
     */
    public function create(): void
    {
        $this->view('admin/create-device', [
            'title' => 'Register New ESP32 Device'
        ]);
    }
    
    /**
     * Process create device
     * 
     * @return void
     */
    public function processCreate(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('device');
            return;
        }
        
        // Validation
        $errors = [];
        
        $deviceName = trim($_POST['device_name'] ?? '');
        $simCardNumber = trim($_POST['sim_card_number'] ?? '');
        $firmwareVersion = trim($_POST['firmware_version'] ?? '1.0.0');
        
        if (empty($deviceName)) {
            $errors[] = 'Device name is required';
        }
        
        // Validate SIM card number format (optional field)
        if (!empty($simCardNumber)) {
            // Remove any non-digit characters for validation
            $cleanNumber = preg_replace('/[^0-9+]/', '', $simCardNumber);
            if (strlen($cleanNumber) < 10) {
                $errors[] = 'SIM card number must be at least 10 digits';
            }
        }
        
        if (!empty($errors)) {
            $_SESSION['device_error'] = implode('<br>', $errors);
            $_SESSION['form_data'] = $_POST;
            redirect('device/create');
            return;
        }
        
        // Auto-generate device ID
        $deviceId = $this->deviceRepository->generateDeviceId();
        
        // Generate unique API key
        $apiKey = $this->deviceRepository->generateApiKey();
        
        // Create device
        $deviceData = [
            'device_name' => $deviceName,
            'device_id' => $deviceId,
            'api_key' => $apiKey,
            'sim_card_number' => !empty($simCardNumber) ? $simCardNumber : null,
            'outlet_status' => 'off',
            'control_mode' => 'manual',
            'is_active' => 1,
            'firmware_version' => $firmwareVersion
        ];
        
        $devicePk = $this->deviceRepository->create($deviceData);
        
        if ($devicePk) {
            // Log activity
            $activityRepo = new ActivityLogRepository();
            $activityRepo->log(
                $_SESSION['user_id'],
                $devicePk,
                'device_registered',
                "New device registered: $deviceName ($deviceId)" . (!empty($simCardNumber) ? " - SIM: $simCardNumber" : ""),
                'success'
            );
            
            $_SESSION['device_message'] = "Device registered successfully! Device ID: $deviceId";
            $_SESSION['new_device_api_key'] = $apiKey;
            $_SESSION['new_device_id'] = $deviceId;
            $_SESSION['new_device_name'] = $deviceName;
            redirect('device/view-config/' . $devicePk);
        } else {
            $_SESSION['device_error'] = 'Failed to register device. Please try again.';
            redirect('device/create');
        }
    }
    
    /**
     * View device configuration
     * 
     * @param int $id
     * @return void
     */
    public function viewConfig(int $id): void
    {
        $device = $this->deviceRepository->findById($id);
        
        if (!$device) {
            $_SESSION['device_error'] = 'Device not found.';
            redirect('device');
            return;
        }
        
        // Only show API key for newly created devices in this session
        $showApiKey = isset($_SESSION['new_device_api_key']) 
                      && isset($_SESSION['new_device_id']) 
                      && $_SESSION['new_device_id'] === $device->device_id;
        
        $apiKey = $showApiKey ? $_SESSION['new_device_api_key'] : null;
        
        // Clear session data after displaying
        if ($showApiKey) {
            unset($_SESSION['new_device_api_key'], $_SESSION['new_device_id'], $_SESSION['new_device_name']);
        }
        
        $this->view('admin/device-config', [
            'device' => $device,
            'api_key' => $apiKey,
            'show_api_key' => $showApiKey,
            'title' => 'Device Configuration'
        ]);
    }
    
    /**
     * Show edit device form
     * 
     * @param int $id
     * @return void
     */
    public function edit(int $id): void
    {
        $device = $this->deviceRepository->findById($id);
        
        if (!$device) {
            $_SESSION['device_error'] = 'Device not found.';
            redirect('device');
            return;
        }
        
        $this->view('admin/edit-device', [
            'device' => $device,
            'title' => 'Edit Device',
            'error' => $_SESSION['device_error'] ?? null
        ]);
        
        unset($_SESSION['device_error']);
    }
    
    /**
     * Process edit device
     * 
     * @param int $id
     * @return void
     */
    public function processEdit(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('device');
            return;
        }
        
        $device = $this->deviceRepository->findById($id);
        
        if (!$device) {
            $_SESSION['device_error'] = 'Device not found.';
            redirect('device');
            return;
        }
        
        // Validation
        $errors = [];
        
        $deviceName = trim($_POST['device_name'] ?? '');
        $simCardNumber = trim($_POST['sim_card_number'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        
        if (empty($deviceName)) {
            $errors[] = 'Device name is required';
        }
        
        // Validate SIM card number format (optional field)
        if (!empty($simCardNumber)) {
            $cleanNumber = preg_replace('/[^0-9+]/', '', $simCardNumber);
            if (strlen($cleanNumber) < 10) {
                $errors[] = 'SIM card number must be at least 10 digits';
            }
        }
        
        if (!empty($errors)) {
            $_SESSION['device_error'] = implode('<br>', $errors);
            redirect('device/edit/' . $id);
            return;
        }
        
        // Update device (device_id is NOT updated - it's auto-generated and permanent)
        $updateData = [
            'device_name' => $deviceName,
            'sim_card_number' => !empty($simCardNumber) ? $simCardNumber : null,
            'is_active' => $isActive
        ];
        
        if ($this->deviceRepository->updateById($id, $updateData)) {
            // Log activity
            $activityRepo = new ActivityLogRepository();
            $activityRepo->log(
                $_SESSION['user_id'],
                $id,
                'device_updated',
                "Device updated: $deviceName (" . $device->device_id . ")" . (!empty($simCardNumber) ? " - SIM: $simCardNumber" : ""),
                'success'
            );
            
            $_SESSION['device_message'] = 'Device updated successfully!';
            redirect('device');
        } else {
            $_SESSION['device_error'] = 'Failed to update device. Please try again.';
            redirect('device/edit/' . $id);
        }
    }
    
    /**
     * Delete device
     * 
     * @return void
     */
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('device');
            return;
        }
        
        $deviceId = (int)($_POST['device_id'] ?? 0);
        
        if (!$deviceId) {
            $_SESSION['device_error'] = 'Invalid device.';
            redirect('device');
            return;
        }
        
        $device = $this->deviceRepository->findById($deviceId);
        
        if (!$device) {
            $_SESSION['device_error'] = 'Device not found.';
            redirect('device');
            return;
        }
        
        // Check if device has active schedules
        $scheduleRepo = new ScheduleRepository();
        // We'll need to add a method to check this, but for now proceed with delete
        
        if ($this->deviceRepository->delete($deviceId)) {
            // Log activity
            $activityRepo = new ActivityLogRepository();
            $activityRepo->log(
                $_SESSION['user_id'],
                null,
                'device_deleted',
                "Device deleted: {$device->device_name} ({$device->device_id})",
                'success'
            );
            
            $_SESSION['device_message'] = 'Device deleted successfully!';
        } else {
            $_SESSION['device_error'] = 'Failed to delete device. Please try again.';
        }
        
        redirect('device');
    }
    
    /**
     * Regenerate API key for device
     * 
     * @param int $id
     * @return void
     */
    public function regenerateApiKey(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('device');
            return;
        }
        
        $device = $this->deviceRepository->findById($id);
        
        if (!$device) {
            $_SESSION['device_error'] = 'Device not found.';
            redirect('device');
            return;
        }
        
        // Generate new API key
        $newApiKey = $this->deviceRepository->generateApiKey();
        
        $stmt = Database::getInstance()->prepare(
            'UPDATE iot_outlet_devices SET api_key = ?, updated_at = NOW() WHERE id = ?'
        );
        
        if ($stmt->execute([$newApiKey, $id])) {
            // Log activity
            $activityRepo = new ActivityLogRepository();
            $activityRepo->log(
                $_SESSION['user_id'],
                $id,
                'device_updated',
                "API key regenerated for device: {$device->device_name}",
                'success'
            );
            
            $_SESSION['device_message'] = 'API key regenerated successfully!';
            $_SESSION['new_device_api_key'] = $newApiKey;
            $_SESSION['new_device_id'] = $device->device_id;
            redirect('device/view-config/' . $id);
        } else {
            $_SESSION['device_error'] = 'Failed to regenerate API key.';
            redirect('device');
        }
    }
    
    /**
     * Render view
     * 
     * @param string $view
     * @param array $data
     * @return void
     */
    private function view(string $view, array $data = []): void
    {
        // Get user data for sidebar and header
        $user = user();
        $pageTitle = $data['title'] ?? 'IoT Outlet System';
        
        extract($data);
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/' . $view . '.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }
}
