<?php

/**
 * Activity Log Repository
 * 
 * Handles all database operations for activity logs
 */

class ActivityLogRepository
{
    private PDO $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get recent activity logs
     * 
     * @param int $limit
     * @return array
     */
    public function getRecent(int $limit = 10): array
    {
        $stmt = $this->db->prepare(
            'SELECT al.*, u.username, u.full_name, u.role,
                    d.device_name, d.device_id as device_identifier
             FROM iot_outlet_activity_logs al 
             LEFT JOIN iot_outlet_users u ON al.user_id = u.id 
             LEFT JOIN iot_outlet_devices d ON al.device_id = d.id
             ORDER BY al.created_at DESC 
             LIMIT ?'
        );
        $stmt->execute([$limit]);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get recent logs by user
     * 
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getRecentByUser(int $userId, int $limit = 10): array
    {
        $stmt = $this->db->prepare(
            'SELECT al.*, u.username, u.full_name, u.role,
                    d.device_name, d.device_id as device_identifier
             FROM iot_outlet_activity_logs al 
             LEFT JOIN iot_outlet_users u ON al.user_id = u.id 
             LEFT JOIN iot_outlet_devices d ON al.device_id = d.id
             WHERE al.user_id = ?
             ORDER BY al.created_at DESC 
             LIMIT ?'
        );
        $stmt->execute([$userId, $limit]);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Count total logs
     * 
     * @return int
     */
    public function count(): int
    {
        $stmt = $this->db->query('SELECT COUNT(*) as total FROM iot_outlet_activity_logs');
        return (int)$stmt->fetch()['total'];
    }
    
    /**
     * Log an activity
     * 
     * @param int $userId
     * @param int|null $deviceId
     * @param string $actionType
     * @param string $actionDetails
     * @param string $status
     * @return bool
     */
    public function log(
        int $userId,
        ?int $deviceId,
        string $actionType,
        string $actionDetails,
        string $status = 'success'
    ): bool {
        $stmt = $this->db->prepare(
            'INSERT INTO iot_outlet_activity_logs 
             (user_id, device_id, action_type, action_details, status, created_at) 
             VALUES (?, ?, ?, ?, ?, NOW())'
        );
        
        return $stmt->execute([$userId, $deviceId, $actionType, $actionDetails, $status]);
    }
}
