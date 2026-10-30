<?php

/**
 * Device Repository
 * 
 * Handles all database operations for devices
 */

class DeviceRepository
{
    private PDO $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Find device by ID
     * 
     * @param int $id
     * @return Device|null
     */
    public function findById(int $id): ?Device
    {
        $stmt = $this->db->prepare('SELECT * FROM iot_outlet_devices WHERE id = ?');
        $stmt->execute([$id]);
        
        $data = $stmt->fetch();
        
        return $data ? Device::fromArray($data) : null;
    }
    
    /**
     * Find device by device ID
     * 
     * @param string $deviceId
     * @return Device|null
     */
    public function findByDeviceId(string $deviceId): ?Device
    {
        $stmt = $this->db->prepare('SELECT * FROM iot_outlet_devices WHERE device_id = ?');
        $stmt->execute([$deviceId]);
        
        $data = $stmt->fetch();
        
        return $data ? Device::fromArray($data) : null;
    }
    
    /**
     * Get all devices
     * 
     * @return array
     */
    public function getAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM iot_outlet_devices ORDER BY created_at DESC');
        $devices = [];
        
        while ($row = $stmt->fetch()) {
            $devices[] = Device::fromArray($row);
        }
        
        return $devices;
    }
    
    /**
     * Create new device
     * 
     * @param array $data
     * @return int|false Device ID or false on failure
     */
    public function create(array $data)
    {
        $stmt = $this->db->prepare(
            'INSERT INTO iot_outlet_devices 
             (device_name, device_id, api_key, sim_card_number, outlet_status, control_mode, is_active, is_online, firmware_version) 
             VALUES (?, ?, ?, ?, ?, ?, ?, 0, ?)'
        );
        
        $result = $stmt->execute([
            $data['device_name'],
            $data['device_id'],
            $data['api_key'],
            $data['sim_card_number'] ?? null,
            $data['outlet_status'] ?? 'off',
            $data['control_mode'] ?? 'manual',
            $data['is_active'] ?? 1,
            $data['firmware_version'] ?? null
        ]);
        
        return $result ? (int)$this->db->lastInsertId() : false;
    }
    
    /**
     * Update device
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateById(int $id, array $data): bool
    {
        // Build dynamic UPDATE query based on provided fields
        $allowedFields = [
            'device_name',
            'device_id',
            'api_key',
            'sim_card_number',
            'is_active',
            'outlet_status',
            'control_mode',
            'ip_address',
            'is_online',
            'last_heartbeat'
        ];
        
        $updateFields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $updateFields[] = "$key = ?";
                $values[] = $value;
            }
        }
        
        if (empty($updateFields)) {
            return false; // No valid fields to update
        }
        
        // Add updated_at
        $updateFields[] = "updated_at = NOW()";
        
        // Add ID to values
        $values[] = $id;
        
        $sql = 'UPDATE iot_outlet_devices SET ' . implode(', ', $updateFields) . ' WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($values);
    }
    
    /**
     * Delete device
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM iot_outlet_devices WHERE id = ?');
        return $stmt->execute([$id]);
    }
    
    /**
     * Check if device_id already exists
     * 
     * @param string $deviceId
     * @param int|null $excludeId
     * @return bool
     */
    public function deviceIdExists(string $deviceId, ?int $excludeId = null): bool
    {
        if ($excludeId) {
            $stmt = $this->db->prepare('SELECT COUNT(*) as count FROM iot_outlet_devices WHERE device_id = ? AND id != ?');
            $stmt->execute([$deviceId, $excludeId]);
        } else {
            $stmt = $this->db->prepare('SELECT COUNT(*) as count FROM iot_outlet_devices WHERE device_id = ?');
            $stmt->execute([$deviceId]);
        }
        
        return (int)$stmt->fetch()['count'] > 0;
    }
    
    /**
     * Generate unique API key
     * 
     * @return string
     */
    public function generateApiKey(): string
    {
        return bin2hex(random_bytes(32));
    }
    
    /**
     * Generate unique device ID
     * Format: ESP32_OUTLET_XXX (where XXX is sequential number)
     * 
     * @return string
     */
    public function generateDeviceId(): string
    {
        // Get the highest device number
        $stmt = $this->db->query(
            "SELECT device_id FROM iot_outlet_devices 
             WHERE device_id REGEXP '^ESP32_OUTLET_[0-9]+$' 
             ORDER BY CAST(SUBSTRING(device_id, 14) AS UNSIGNED) DESC 
             LIMIT 1"
        );
        
        $result = $stmt->fetch();
        
        if ($result) {
            // Extract the number from ESP32_OUTLET_XXX
            preg_match('/ESP32_OUTLET_(\d+)/', $result['device_id'], $matches);
            $nextNumber = isset($matches[1]) ? (int)$matches[1] + 1 : 1;
        } else {
            // No devices yet, start with 001
            $nextNumber = 1;
        }
        
        // Format with leading zeros (e.g., 001, 002, etc.)
        return 'ESP32_OUTLET_' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
    
    /**
     * Count total devices
     * 
     * @return int
     */
    public function count(): int
    {
        $stmt = $this->db->query('SELECT COUNT(*) as total FROM iot_outlet_devices');
        return (int)$stmt->fetch()['total'];
    }
    
    /**
     * Update device
     * 
     * @param Device $device
     * @return bool
     */
    public function update(Device $device): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE iot_outlet_devices 
             SET device_name = ?, outlet_status = ?, control_mode = ?, 
                 is_online = ?, last_heartbeat = ?, ip_address = ?, updated_at = NOW()
             WHERE id = ?'
        );
        
        return $stmt->execute([
            $device->device_name,
            $device->outlet_status,
            $device->control_mode,
            $device->is_online ? 1 : 0,
            $device->last_heartbeat,
            $device->ip_address,
            $device->id
        ]);
    }
    
    /**
     * Check and mark devices offline if no heartbeat for configured timeout
     * Also marks devices offline if they've never sent a heartbeat
     * 
     * @return int Number of devices marked offline
     */
    public function markOfflineDevices(): int
    {
        // Get heartbeat timeout from config (default 120 seconds = 2 minutes)
        $timeout = (int)($_ENV['ESP32_HEARTBEAT_TIMEOUT'] ?? 120);
        
        // Mark offline devices that have exceeded heartbeat timeout
        $stmt = $this->db->prepare(
            'UPDATE iot_outlet_devices 
             SET is_online = 0, updated_at = NOW()
             WHERE is_online = 1 
             AND last_heartbeat IS NOT NULL 
             AND TIMESTAMPDIFF(SECOND, last_heartbeat, NOW()) > ?'
        );
        
        $stmt->execute([$timeout]);
        $count = $stmt->rowCount();
        
        // Also mark offline devices that have NEVER sent a heartbeat (new devices)
        $stmt2 = $this->db->prepare(
            'UPDATE iot_outlet_devices 
             SET is_online = 0, updated_at = NOW()
             WHERE is_online = 1 
             AND last_heartbeat IS NULL'
        );
        
        $stmt2->execute();
        $count += $stmt2->rowCount();
        
        return $count;
    }
    
    /**
     * Get device with offline check
     * Automatically checks if device should be marked offline based on heartbeat
     * 
     * @param int $id
     * @return Device|null
     */
    public function findByIdWithOfflineCheck(int $id): ?Device
    {
        // First mark any offline devices
        $this->markOfflineDevices();
        
        // Then get the device
        return $this->findById($id);
    }
    
    /**
     * Get all devices with offline check
     * 
     * @return array
     */
    public function getAllWithOfflineCheck(): array
    {
        // First mark any offline devices
        $this->markOfflineDevices();
        
        // Then get all devices
        return $this->getAll();
    }
    
    /**
     * Update device control mode
     * 
     * @param int $id
     * @param string $mode
     * @return bool
     */
    public function updateControlMode(int $id, string $mode): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE iot_outlet_devices 
             SET control_mode = ?, updated_at = NOW() 
             WHERE id = ?'
        );
        
        return $stmt->execute([$mode, $id]);
    }
}
