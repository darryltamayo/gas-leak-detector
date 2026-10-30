<?php

class ScheduleRepository
{
    private PDO $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function findById(int $id): ?Schedule
    {
        $stmt = $this->db->prepare(
            'SELECT s.*, u.username, d.device_name 
             FROM iot_outlet_schedules s
             LEFT JOIN iot_outlet_users u ON s.user_id = u.id
             LEFT JOIN iot_outlet_devices d ON s.device_id = d.id
             WHERE s.id = ?'
        );
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        return $data ? Schedule::fromArray($data) : null;
    }
    
    public function getAll(): array
    {
        $stmt = $this->db->query(
            'SELECT s.*, u.username, d.device_name 
             FROM iot_outlet_schedules s
             LEFT JOIN iot_outlet_users u ON s.user_id = u.id
             LEFT JOIN iot_outlet_devices d ON s.device_id = d.id
             ORDER BY s.created_at DESC'
        );
        $schedules = [];
        while ($row = $stmt->fetch()) {
            $schedules[] = Schedule::fromArray($row);
        }
        return $schedules;
    }
    
    public function getByUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT s.*, u.username, d.device_name 
             FROM iot_outlet_schedules s
             LEFT JOIN iot_outlet_users u ON s.user_id = u.id
             LEFT JOIN iot_outlet_devices d ON s.device_id = d.id
             WHERE s.user_id = ?
             ORDER BY s.created_at DESC'
        );
        $stmt->execute([$userId]);
        $schedules = [];
        while ($row = $stmt->fetch()) {
            $schedules[] = Schedule::fromArray($row);
        }
        return $schedules;
    }
    
    public function getActive(): array
    {
        $stmt = $this->db->query(
            'SELECT s.*, u.username, d.device_name 
             FROM iot_outlet_schedules s
             LEFT JOIN iot_outlet_users u ON s.user_id = u.id
             LEFT JOIN iot_outlet_devices d ON s.device_id = d.id
             WHERE s.is_enabled = 1
             ORDER BY s.time_on'
        );
        $schedules = [];
        while ($row = $stmt->fetch()) {
            $schedules[] = Schedule::fromArray($row);
        }
        return $schedules;
    }
    
    public function create(Schedule $schedule): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO iot_outlet_schedules (user_id, device_id, schedule_name, time_on, time_off, is_enabled, days_of_week, repeat_type, schedule_date) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        return $stmt->execute([
            $schedule->user_id,
            $schedule->device_id,
            $schedule->schedule_name,
            $schedule->time_on,
            $schedule->time_off,
            $schedule->is_enabled ? 1 : 0,
            $schedule->days_of_week,
            $schedule->repeat_type ?? 'repeat',
            $schedule->schedule_date
        ]);
    }
    
    public function update(Schedule $schedule): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE iot_outlet_schedules 
             SET schedule_name = ?, time_on = ?, time_off = ?, is_enabled = ?, days_of_week = ?, repeat_type = ?, schedule_date = ?, updated_at = NOW()
             WHERE id = ?'
        );
        return $stmt->execute([
            $schedule->schedule_name,
            $schedule->time_on,
            $schedule->time_off,
            $schedule->is_enabled ? 1 : 0,
            $schedule->days_of_week,
            $schedule->repeat_type ?? 'repeat',
            $schedule->schedule_date,
            $schedule->id
        ]);
    }
    
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM iot_outlet_schedules WHERE id = ?');
        return $stmt->execute([$id]);
    }
    
    public function count(): int
    {
        $stmt = $this->db->query('SELECT COUNT(*) as total FROM iot_outlet_schedules');
        return (int)$stmt->fetch()['total'];
    }
    
    public function countActive(): int
    {
        $stmt = $this->db->query('SELECT COUNT(*) as total FROM iot_outlet_schedules WHERE is_enabled = 1');
        return (int)$stmt->fetch()['total'];
    }
    
    /**
     * Mark schedule as executed and disable if it's a one-time schedule
     */
    public function markAsExecuted(int $scheduleId): bool
    {
        $schedule = $this->findById($scheduleId);
        if (!$schedule) return false;
        
        // Update last_executed time
        $stmt = $this->db->prepare(
            'UPDATE iot_outlet_schedules 
             SET last_executed = NOW(), 
                 is_enabled = CASE WHEN repeat_type = "once" THEN 0 ELSE is_enabled END,
                 updated_at = NOW()
             WHERE id = ?'
        );
        return $stmt->execute([$scheduleId]);
    }
    
    /**
     * Disable a schedule (turn toggle OFF)
     */
    public function disable(int $scheduleId): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE iot_outlet_schedules 
             SET is_enabled = 0, 
                 updated_at = NOW()
             WHERE id = ?'
        );
        return $stmt->execute([$scheduleId]);
    }
}
