<?php

/**
 * User Repository
 * 
 * Handles all database operations for users
 */

class UserRepository
{
    private PDO $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Find user by ID
     * 
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User
    {
        $stmt = $this->db->prepare('SELECT * FROM iot_outlet_users WHERE id = ?');
        $stmt->execute([$id]);
        
        $data = $stmt->fetch();
        
        return $data ? User::fromArray($data) : null;
    }
    
    /**
     * Find user by username
     * 
     * @param string $username
     * @return User|null
     */
    public function findByUsername(string $username): ?User
    {
        $stmt = $this->db->prepare('SELECT * FROM iot_outlet_users WHERE username = ?');
        $stmt->execute([$username]);
        
        $data = $stmt->fetch();
        
        return $data ? User::fromArray($data) : null;
    }
    
    /**
     * Find user by username or email (legacy support)
     * 
     * @param string $identifier
     * @return User|null
     */
    public function findByUsernameOrEmail(string $identifier): ?User
    {
        // Since email is removed, just search by username
        return $this->findByUsername($identifier);
    }
    
    /**
     * Create new user
     * 
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO iot_outlet_users (username, password, full_name, first_name, last_name, role, is_active) 
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        
        return $stmt->execute([
            $user->username,
            $user->password,
            $user->full_name,
            $user->first_name,
            $user->last_name,
            $user->role,
            $user->is_active ? 1 : 0
        ]);
    }
    
    /**
     * Update user
     * 
     * @param User $user
     * @return bool
     */
    public function update(User $user): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE iot_outlet_users 
             SET username = ?, full_name = ?, first_name = ?, last_name = ?, role = ?, is_active = ?, updated_at = NOW()
             WHERE id = ?'
        );
        
        return $stmt->execute([
            $user->username,
            $user->full_name,
            $user->first_name,
            $user->last_name,
            $user->role,
            $user->is_active ? 1 : 0,
            $user->id
        ]);
    }
    
    /**
     * Update password
     * 
     * @param int $userId
     * @param string $hashedPassword
     * @return bool
     */
    public function updatePassword(int $userId, string $hashedPassword): bool
    {
        $stmt = $this->db->prepare('UPDATE iot_outlet_users SET password = ?, updated_at = NOW() WHERE id = ?');
        return $stmt->execute([$hashedPassword, $userId]);
    }
    
    /**
     * Update last login timestamp
     * 
     * @param int $userId
     * @return bool
     */
    public function updateLastLogin(int $userId): bool
    {
        $stmt = $this->db->prepare('UPDATE iot_outlet_users SET last_login = NOW() WHERE id = ?');
        return $stmt->execute([$userId]);
    }
    
    /**
     * Delete user
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM iot_outlet_users WHERE id = ?');
        return $stmt->execute([$id]);
    }
    
    /**
     * Get all users
     * 
     * @return array
     */
    public function getAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM iot_outlet_users ORDER BY created_at DESC');
        $users = [];
        
        while ($row = $stmt->fetch()) {
            $users[] = User::fromArray($row);
        }
        
        return $users;
    }
    
    /**
     * Count total users
     * 
     * @return int
     */
    public function count(): int
    {
        $stmt = $this->db->query('SELECT COUNT(*) as total FROM iot_outlet_users');
        return (int)$stmt->fetch()['total'];
    }
    
    /**
     * Get database instance
     * 
     * @return PDO
     */
    public function getDb(): PDO
    {
        return $this->db;
    }
    
    /**
     * Count active users
     * 
     * @return int
     */
    public function countActive(): int
    {
        $stmt = $this->db->query('SELECT COUNT(*) as total FROM iot_outlet_users WHERE is_active = 1');
        return (int)$stmt->fetch()['total'];
    }
}
