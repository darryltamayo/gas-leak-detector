<?php

/**
 * Dashboard Service
 * 
 * Handles dashboard data aggregation
 */

class DashboardService
{
    private UserRepository $userRepository;
    private DeviceRepository $deviceRepository;
    private ActivityLogRepository $activityLogRepository;
    
    private SimRepository $simRepository;
    
    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->deviceRepository = new DeviceRepository();
        $this->activityLogRepository = new ActivityLogRepository();
        $this->simRepository = new SimRepository();
    }
    
    /**
     * Get admin dashboard data
     * 
     * @return array
     */
    public function getAdminDashboardData(): array
    {
        // Mark offline devices before fetching data
        $this->deviceRepository->markOfflineDevices();
        
        $devices = $this->deviceRepository->getAllWithOfflineCheck();
        $device = !empty($devices) ? $devices[0] : null;
        
        return [
            'outlet_status' => $device ? $device->outlet_status : 'off',
            'device_online' => $device ? $device->is_online : false,
            'device_name' => $device ? $device->device_name : 'No Device',
            'control_mode' => $device ? $device->control_mode : 'manual',
            'last_heartbeat' => $device ? $device->last_heartbeat : null,
            'total_users' => $this->userRepository->count(),
            'active_users' => $this->userRepository->countActive(),
            'total_devices' => $this->deviceRepository->count(),
            'total_sims' => $this->simRepository->count(),
            'active_sims' => $this->simRepository->countActive(),
            'recent_logs' => $this->activityLogRepository->getRecent(5),
            'all_devices' => $devices // Add all devices
        ];
    }
    
    /**
     * Get user dashboard data
     * 
     * @param int $userId
     * @return array
     */
    public function getUserDashboardData(int $userId): array
    {
        // Mark offline devices before fetching data
        $this->deviceRepository->markOfflineDevices();
        
        $devices = $this->deviceRepository->getAllWithOfflineCheck();
        $device = !empty($devices) ? $devices[0] : null;
        
        return [
            'outlet_status' => $device ? $device->outlet_status : 'off',
            'device_online' => $device ? $device->is_online : false,
            'device_name' => $device ? $device->device_name : 'No Device',
            'control_mode' => $device ? $device->control_mode : 'manual',
            'last_heartbeat' => $device ? $device->last_heartbeat : null,
            'total_devices' => $this->deviceRepository->count(),
            'total_sims' => $this->simRepository->count(),
            'active_sims' => $this->simRepository->countActive(),
            'recent_logs' => $this->activityLogRepository->getRecentByUser($userId, 5),
            'all_devices' => $devices // Add all devices
        ];
    }
}
