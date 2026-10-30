<?php

/**
 * SIM Repository
 * 
 * Handles all database operations for authorized SIMs
 */

class SimRepository
{
    private PDO $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Find SIM by ID
     * 
     * @param int $id
     * @return AuthorizedSim|null
     */
    public function findById(int $id): ?AuthorizedSim
    {
        $stmt = $this->db->prepare(
            'SELECT s.*, u.username as creator_username, u.full_name as creator_full_name
             FROM iot_outlet_authorized_sims s
             LEFT JOIN iot_outlet_users u ON s.created_by = u.id
             WHERE s.id = ?'
        );
        $stmt->execute([$id]);
        
        $data = $stmt->fetch();
        
        return $data ? AuthorizedSim::fromArray($data) : null;
    }
    
    /**
     * Find SIM by phone number
     * 
     * @param string $phoneNumber
     * @return AuthorizedSim|null
     */
    public function findByPhoneNumber(string $phoneNumber): ?AuthorizedSim
    {
        $stmt = $this->db->prepare(
            'SELECT s.*, u.username as creator_username, u.full_name as creator_full_name
             FROM iot_outlet_authorized_sims s
             LEFT JOIN iot_outlet_users u ON s.created_by = u.id
             WHERE s.phone_number = ?'
        );
        $stmt->execute([$phoneNumber]);
        
        $data = $stmt->fetch();
        
        return $data ? AuthorizedSim::fromArray($data) : null;
    }
    
    /**
     * Get all SIMs
     * 
     * @return array
     */
    public function getAll(): array
    {
        $stmt = $this->db->query(
            'SELECT s.*, u.username as creator_username, u.full_name as creator_full_name
             FROM iot_outlet_authorized_sims s
             LEFT JOIN iot_outlet_users u ON s.created_by = u.id
             ORDER BY s.created_at DESC'
        );
        
        $sims = [];
        
        while ($row = $stmt->fetch()) {
            $sims[] = AuthorizedSim::fromArray($row);
        }
        
        return $sims;
    }
    
    /**
     * Get active SIMs
     * 
     * @return array
     */
    public function getActive(): array
    {
        $stmt = $this->db->query(
            'SELECT s.*, u.username as creator_username, u.full_name as creator_full_name
             FROM iot_outlet_authorized_sims s
             LEFT JOIN iot_outlet_users u ON s.created_by = u.id
             WHERE s.is_active = 1
             ORDER BY s.created_at DESC'
        );
        
        $sims = [];
        
        while ($row = $stmt->fetch()) {
            $sims[] = AuthorizedSim::fromArray($row);
        }
        
        return $sims;
    }
    
    /**
     * Create new SIM
     * 
     * @param AuthorizedSim $sim
     * @return bool
     */
    public function create(AuthorizedSim $sim): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO iot_outlet_authorized_sims (phone_number, owner_name, first_name, last_name, description, is_active, created_by) 
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        
        return $stmt->execute([
            $sim->phone_number,
            $sim->owner_name,
            $sim->first_name,
            $sim->last_name,
            $sim->description,
            $sim->is_active ? 1 : 0,
            $sim->created_by
        ]);
    }
    
    /**
     * Update SIM
     * 
     * @param AuthorizedSim $sim
     * @return bool
     */
    public function update(AuthorizedSim $sim): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE iot_outlet_authorized_sims 
             SET phone_number = ?, owner_name = ?, first_name = ?, last_name = ?, description = ?, is_active = ?, updated_at = NOW()
             WHERE id = ?'
        );
        
        return $stmt->execute([
            $sim->phone_number,
            $sim->owner_name,
            $sim->first_name,
            $sim->last_name,
            $sim->description,
            $sim->is_active ? 1 : 0,
            $sim->id
        ]);
    }
    
    /**
     * Delete SIM
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM iot_outlet_authorized_sims WHERE id = ?');
        return $stmt->execute([$id]);
    }
    
    /**
     * Count total SIMs
     * 
     * @return int
     */
    public function count(): int
    {
        $stmt = $this->db->query('SELECT COUNT(*) as total FROM iot_outlet_authorized_sims');
        return (int)$stmt->fetch()['total'];
    }
    
    /**
     * Count active SIMs
     * 
     * @return int
     */
    public function countActive(): int
    {
        $stmt = $this->db->query('SELECT COUNT(*) as total FROM iot_outlet_authorized_sims WHERE is_active = 1');
        return (int)$stmt->fetch()['total'];
    }
    
    /**
     * Check if phone number is authorized and active
     * 
     * @param string $phoneNumber
     * @return bool
     */
    public function isAuthorized(string $phoneNumber): bool
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) as total 
             FROM iot_outlet_authorized_sims 
             WHERE phone_number = ? AND is_active = 1'
        );
        $stmt->execute([$phoneNumber]);
        
        return (int)$stmt->fetch()['total'] > 0;
    }
}
