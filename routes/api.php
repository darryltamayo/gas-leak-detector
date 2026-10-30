<?php

/**
 * REST API Routes
 * 
 * All API endpoints for ESP32 communication
 */

// Load required classes
require_once ROOT_PATH . '/app/Database.php';
require_once ROOT_PATH . '/app/models/Device.php';
require_once ROOT_PATH . '/app/models/Schedule.php';
require_once ROOT_PATH . '/app/models/AuthorizedSim.php';
require_once ROOT_PATH . '/app/repositories/DeviceRepository.php';
require_once ROOT_PATH . '/app/repositories/SimRepository.php';
require_once ROOT_PATH . '/app/repositories/ScheduleRepository.php';
require_once ROOT_PATH . '/app/repositories/ActivityLogRepository.php';
require_once ROOT_PATH . '/app/helpers/functions.php';

class ApiRouter
{
    private DeviceRepository $deviceRepository;
    private SimRepository $simRepository;
    private ScheduleRepository $scheduleRepository;
    
    public function __construct()
    {
        // Ensure environment is loaded (in case API is called directly)
        if (empty($_ENV['API_KEY'])) {
            if (defined('ROOT_PATH') && file_exists(ROOT_PATH . '/.env')) {
                EnvironmentHelper::load(ROOT_PATH . '/.env');
            }
        }
        
        $this->deviceRepository = new DeviceRepository();
        $this->simRepository = new SimRepository();
        $this->scheduleRepository = new ScheduleRepository();
    }
    
    /**
     * Route API requests
     */
    public function route(string $endpoint, string $method): void
    {
        // Simple test endpoint that returns JSON (no auth required)
        if ($endpoint === 'test') {
            $this->jsonResponse([
                'success' => true,
                'message' => 'API is working',
                'timestamp' => date('Y-m-d H:i:s'),
                'endpoint' => $endpoint,
                'method' => $method,
                'server' => $_SERVER['HTTP_HOST'] ?? 'unknown',
                'php_version' => PHP_VERSION
            ]);
            return;
        }
        
        // Test heartbeat with GET (for browser testing)
        if ($endpoint === 'test-heartbeat' && $method === 'GET') {
            $deviceId = $_GET['device_id'] ?? '';
            $apiKey = $_GET['api_key'] ?? '';
            
            if (empty($deviceId) || empty($apiKey)) {
                $this->jsonResponse([
                    'error' => 'Missing parameters',
                    'required' => ['device_id', 'api_key'],
                    'example' => '/api/test-heartbeat?device_id=ESP32_OUTLET_001&api_key=YOUR_API_KEY'
                ], 400);
                return;
            }
            
            $device = $this->deviceRepository->findByDeviceId($deviceId);
            
            if (!$device) {
                $this->jsonResponse([
                    'success' => false,
                    'error' => 'Device not found',
                    'device_id' => $deviceId
                ], 404);
                return;
            }
            
            if (!hash_equals($device->api_key, $apiKey)) {
                $this->jsonResponse([
                    'success' => false,
                    'error' => 'Invalid API key',
                    'device_id' => $deviceId,
                    'api_key_provided' => substr($apiKey, 0, 10) . '...',
                    'api_key_expected' => substr($device->api_key, 0, 10) . '...'
                ], 401);
                return;
            }
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Authentication successful! ESP32 will work.',
                'device' => [
                    'id' => $device->id,
                    'device_id' => $device->device_id,
                    'device_name' => $device->device_name,
                    'is_active' => $device->is_active,
                    'is_online' => $device->is_online,
                    'outlet_status' => $device->outlet_status,
                    'control_mode' => $device->control_mode
                ]
            ]);
            return;
        }
        
        // Debug endpoint (no auth required)
        if ($endpoint === 'debug') {
            $this->debugEndpoint();
            return;
        }
        
        // These endpoints use device-specific API key verification
        $deviceAuthEndpoints = ['heartbeat', 'device/authorized-numbers', 'status', 'schedules'];
        
        if (in_array($endpoint, $deviceAuthEndpoints)) {
            // Device authentication handled within each endpoint
            switch ($endpoint) {
                case 'heartbeat':
                    $this->heartbeat();
                    break;
                case 'device/authorized-numbers':
                    $this->getAuthorizedNumbers();
                    break;
                case 'status':
                    $this->getStatus();
                    break;
                case 'schedules':
                    $this->getSchedules();
                    break;
            }
            return;
        }
        
        // All other endpoints require server API key
        if (!$this->verifyApiKey()) {
            // Enhanced error message for debugging
            $providedKey = $_SERVER['HTTP_X_API_KEY'] ?? $_GET['api_key'] ?? 'NONE';
            $expectedKey = config('security.api_key', 'NOT_LOADED');
            
            $this->jsonResponse([
                'error' => 'Unauthorized',
                'debug' => [
                    'provided_key' => substr($providedKey, 0, 10) . '...', // Show first 10 chars only
                    'expected_key_loaded' => !empty($expectedKey) && $expectedKey !== 'NOT_LOADED',
                    'env_loaded' => !empty($_ENV['API_KEY']),
                    'hint' => 'Check if .env file exists and API_KEY is set'
                ]
            ], 401);
            return;
        }
        
        // Route to appropriate handler
        switch ($endpoint) {
            case 'control':
                $this->control();
                break;
            case 'sms/validate':
                $this->validateSms();
                break;
            case 'sms/execute':
                $this->executeSmsCommand();
                break;
            default:
                $this->jsonResponse(['error' => 'Not found'], 404);
        }
    }
    
    /**
     * Debug endpoint - Shows configuration status (REMOVE AFTER FIXING!)
     */
    private function debugEndpoint(): void
    {
        $this->jsonResponse([
            'status' => 'debug_info',
            'env_file_exists' => file_exists(ROOT_PATH . '/.env'),
            'api_key_in_env' => !empty($_ENV['API_KEY']),
            'api_key_from_config' => !empty(config('security.api_key')),
            'api_key_first_10_chars' => substr(config('security.api_key', ''), 0, 10),
            'root_path' => ROOT_PATH,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Heartbeat endpoint - ESP32 sends every minute
     */
    private function heartbeat(): void
    {
        // Log request details
        error_log('[Heartbeat] ===== NEW HEARTBEAT REQUEST =====');
        error_log('[Heartbeat] Request Method: ' . $_SERVER['REQUEST_METHOD']);
        error_log('[Heartbeat] Content-Type: ' . ($_SERVER['CONTENT_TYPE'] ?? 'not set'));
        error_log('[Heartbeat] Content-Length: ' . ($_SERVER['CONTENT_LENGTH'] ?? '0'));
        
        // Get raw input for debugging
        $rawInput = file_get_contents('php://input');
        error_log('[Heartbeat] Raw input length: ' . strlen($rawInput));
        error_log('[Heartbeat] Raw input: ' . $rawInput);
        
        $data = json_decode($rawInput, true);
        error_log('[Heartbeat] JSON decode result: ' . ($data === null ? 'NULL (failed)' : 'SUCCESS'));
        error_log('[Heartbeat] JSON decode error: ' . json_last_error_msg());
        
        $deviceId = $data['device_id'] ?? '';
        $deviceApiKey = $data['api_key'] ?? '';
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        error_log('[Heartbeat] Device ID: ' . ($deviceId ?: 'EMPTY'));
        error_log('[Heartbeat] API Key present: ' . (!empty($deviceApiKey) ? 'YES (' . strlen($deviceApiKey) . ' chars)' : 'NO'));
        
        // Find device by ID
        $device = $this->deviceRepository->findByDeviceId($deviceId);
        
        if (!$device) {
            error_log('[Heartbeat] Device not found: ' . $deviceId);
            $this->jsonResponse([
                'error' => 'Device not found',
                'device_id' => $deviceId
            ], 404);
            return;
        }
        
        error_log('[Heartbeat] Device found: ' . $device->device_name);
        
        // Verify device API key
        if (!hash_equals($device->api_key, $deviceApiKey)) {
            error_log('[Heartbeat] API key mismatch');
            error_log('[Heartbeat] Provided: ' . substr($deviceApiKey, 0, 20));
            error_log('[Heartbeat] Expected: ' . substr($device->api_key, 0, 20));
            $this->jsonResponse([
                'error' => 'Invalid device API key',
                'device_id' => $deviceId,
                'debug' => [
                    'provided_key_prefix' => substr($deviceApiKey, 0, 20),
                    'expected_key_prefix' => substr($device->api_key, 0, 20),
                    'key_length_provided' => strlen($deviceApiKey),
                    'key_length_expected' => strlen($device->api_key)
                ]
            ], 401);
            return;
        }
        
        error_log('[Heartbeat] Authentication successful');
        
        // Extract outlet state from ESP32 data (if provided)
        $outletStates = $data['outlet_states'] ?? [];
        $newOutletStatus = null;
        
        if (!empty($outletStates) && isset($outletStates[0]['state'])) {
            $newOutletStatus = $outletStates[0]['state']; // "on" or "off"
            error_log('[Heartbeat] Outlet state received from ESP32: ' . $newOutletStatus);
        }
        
        // Update device status
        $device->last_heartbeat = date('Y-m-d H:i:s');
        $device->is_online = true;
        $device->ip_address = $ipAddress;
        
        // Update outlet status if provided by ESP32
        if ($newOutletStatus !== null && in_array($newOutletStatus, ['on', 'off'])) {
            $oldStatus = $device->outlet_status;
            $device->outlet_status = $newOutletStatus;
            
            // Log if status changed
            if ($oldStatus !== $newOutletStatus) {
                error_log('[Heartbeat] Outlet status changed: ' . $oldStatus . ' -> ' . $newOutletStatus);
                
                // Log the status change to activity logs
                $this->logActivity(
                    $device->id,
                    'outlet_' . $newOutletStatus,
                    'Outlet turned ' . strtoupper($newOutletStatus) . ' (reported by ESP32)'
                );
            }
        }
        
        $this->deviceRepository->update($device);
        
        error_log('[Heartbeat] Device updated successfully');
        
        $this->jsonResponse([
            'success' => true,
            'message' => 'Heartbeat received',
            'control_mode' => $device->control_mode,
            'server_time' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Get current outlet status
     */
    private function getStatus(): void
    {
        $deviceId = $_GET['device_id'] ?? '';
        $deviceApiKey = $_GET['api_key'] ?? '';
        
        $device = $this->deviceRepository->findByDeviceId($deviceId);
        
        if (!$device) {
            $this->jsonResponse(['error' => 'Device not found'], 404);
            return;
        }
        
        // Verify device API key
        if (!hash_equals($device->api_key, $deviceApiKey)) {
            $this->jsonResponse(['error' => 'Invalid device API key'], 401);
            return;
        }
        
        $this->jsonResponse([
            'success' => true,
            'outlet_status' => $device->outlet_status,
            'control_mode' => $device->control_mode,
            'is_online' => $device->is_online,
            'last_updated' => $device->updated_at
        ]);
    }
    
    /**
     * Control outlet (for ESP32 to check if it should change state)
     */
    private function control(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $deviceId = $data['device_id'] ?? '';
        $action = $data['action'] ?? '';
        
        $device = $this->deviceRepository->findByDeviceId($deviceId);
        
        if (!$device) {
            $this->jsonResponse(['error' => 'Device not found'], 404);
            return;
        }
        
        if (!in_array($action, ['on', 'off'])) {
            $this->jsonResponse(['error' => 'Invalid action'], 400);
            return;
        }
        
        $device->outlet_status = $action;
        $this->deviceRepository->update($device);
        
        $this->logActivity($device->id, 'outlet_' . $action, 'ESP32 executed: ' . strtoupper($action));
        
        $this->jsonResponse([
            'success' => true,
            'status' => $action,
            'message' => 'Status updated'
        ]);
    }
    
    /**
     * Get active schedules
     */
    private function getSchedules(): void
    {
        $deviceId = $_GET['device_id'] ?? '';
        $deviceApiKey = $_GET['api_key'] ?? '';
        
        $device = $this->deviceRepository->findByDeviceId($deviceId);
        
        if (!$device) {
            $this->jsonResponse(['error' => 'Device not found'], 404);
            return;
        }
        
        // Verify device API key
        if (!hash_equals($device->api_key, $deviceApiKey)) {
            $this->jsonResponse(['error' => 'Invalid device API key'], 401);
            return;
        }
        
        $schedules = $this->scheduleRepository->getActive();
        $formatted = [];
        
        foreach ($schedules as $schedule) {
            if ($schedule->device_id == $device->id) {
                $formatted[] = [
                    'id' => $schedule->id,
                    'name' => $schedule->schedule_name,
                    'time_on' => $schedule->time_on,
                    'time_off' => $schedule->time_off,
                    'days' => $schedule->days_of_week
                ];
            }
        }
        
        $this->jsonResponse([
            'success' => true,
            'schedules' => $formatted,
            'count' => count($formatted)
        ]);
    }
    
    /**
     * Get authorized phone numbers for ESP32 devices
     * Endpoint: GET /api/device/authorized-numbers
     */
    private function getAuthorizedNumbers(): void
    {
        $deviceId = $_GET['device_id'] ?? '';
        $deviceApiKey = $_GET['api_key'] ?? '';
        
        // Find device and verify API key
        $device = $this->deviceRepository->findByDeviceId($deviceId);
        
        if (!$device) {
            $this->jsonResponse(['error' => 'Device not found'], 404);
            return;
        }
        
        // Verify device API key
        if (!hash_equals($device->api_key, $deviceApiKey)) {
            $this->jsonResponse(['error' => 'Invalid device API key'], 401);
            return;
        }
        
        // Get all active SIM cards from database
        $activeSims = $this->simRepository->getActive();
        
        // Format phone numbers for ESP32
        $authorizedNumbers = [];
        
        foreach ($activeSims as $sim) {
            $authorizedNumbers[] = [
                'phone_number' => $sim->phone_number,
                'owner_name' => $sim->owner_name
            ];
        }
        
        // Log this request for monitoring
        $this->logActivity($device->id, 'authorized_numbers_fetched', 'ESP32 fetched authorized numbers list (' . count($authorizedNumbers) . ' numbers)');
        
        $this->jsonResponse([
            'success' => true,
            'authorized_numbers' => $authorizedNumbers,
            'count' => count($authorizedNumbers),
            'last_updated' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Validate SIM number
     */
    private function validateSms(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $phoneNumber = $data['phone_number'] ?? '';
        
        $isAuthorized = $this->simRepository->isAuthorized($phoneNumber);
        
        $this->jsonResponse([
            'success' => true,
            'authorized' => $isAuthorized,
            'phone_number' => $phoneNumber
        ]);
    }
    
    /**
     * Execute SMS command
     */
    private function executeSmsCommand(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $phoneNumber = $data['phone_number'] ?? '';
        $command = strtoupper($data['command'] ?? '');
        $deviceId = $data['device_id'] ?? '';
        
        // Validate SIM and get owner info
        if (!$this->simRepository->isAuthorized($phoneNumber)) {
            $this->logActivity(null, 'unauthorized_sms', "Unauthorized SMS from: {$phoneNumber}");
            $this->jsonResponse(['success' => false, 'message' => 'Unauthorized'], 403);
            return;
        }
        
        // Get SIM card details to get owner name
        $simCard = $this->simRepository->findByPhoneNumber($phoneNumber);
        $ownerName = $simCard ? $simCard->owner_name : 'Unknown';
        
        $device = $this->deviceRepository->findByDeviceId($deviceId);
        
        if (!$device) {
            $this->jsonResponse(['error' => 'Device not found'], 404);
            return;
        }
        
        $response = '';
        
        switch ($command) {
            case 'ON':
                $device->outlet_status = 'on';
                $device->control_mode = 'manual';
                $this->deviceRepository->update($device);
                $response = 'Outlet turned ON';
                $this->logActivity($device->id, 'sms_command', "SMS command ON from {$ownerName} ({$phoneNumber})");
                break;
                
            case 'OFF':
                $device->outlet_status = 'off';
                $device->control_mode = 'manual';
                $this->deviceRepository->update($device);
                $response = 'Outlet turned OFF';
                $this->logActivity($device->id, 'sms_command', "SMS command OFF from {$ownerName} ({$phoneNumber})");
                break;
                
            case 'STATUS':
                $status = strtoupper($device->outlet_status);
                $response = "Outlet is currently {$status}";
                $this->logActivity($device->id, 'sms_command', "SMS command STATUS from {$ownerName} ({$phoneNumber})");
                break;
                
            default:
                $response = 'Invalid command. Use: ON, OFF, or STATUS';
        }
        
        $this->jsonResponse([
            'success' => true,
            'response' => $response,
            'status' => $device->outlet_status
        ]);
    }
    
    /**
     * Verify API key
     */
    private function verifyApiKey(): bool
    {
        // Get API key from headers or query string
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? $_GET['api_key'] ?? '';
        
        // Get expected key from config
        $expectedKey = config('security.api_key');
        
        // Debug logging (will help diagnose issues)
        error_log('[API] Verifying API key...');
        error_log('[API] Provided key (first 10 chars): ' . substr($apiKey, 0, 10));
        error_log('[API] Expected key (first 10 chars): ' . substr($expectedKey, 0, 10));
        error_log('[API] Expected key loaded: ' . (!empty($expectedKey) ? 'YES' : 'NO'));
        
        // Check if keys match
        if (empty($apiKey) || empty($expectedKey)) {
            error_log('[API] Key verification failed: Empty key');
            return false;
        }
        
        $isValid = hash_equals($expectedKey, $apiKey);
        error_log('[API] Key verification result: ' . ($isValid ? 'VALID' : 'INVALID'));
        
        return $isValid;
    }
    
    /**
     * Log activity
     */
    private function logActivity(?int $deviceId, string $type, string $details): void
    {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare(
                'INSERT INTO iot_outlet_activity_logs (device_id, action_type, action_details, ip_address, status) 
                 VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->execute([$deviceId, $type, $details, $_SERVER['REMOTE_ADDR'] ?? 'unknown', 'success']);
        } catch (Exception $e) {
            error_log('Log failed: ' . $e->getMessage());
        }
    }
    
    /**
     * JSON response
     */
    private function jsonResponse(array $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
