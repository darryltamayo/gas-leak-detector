<?php

/**
 * User Model
 * 
 * Represents a user in the system
 */

class User
{
    public ?int $id = null;
    public ?string $username = null;
    public ?string $password = null;
    public ?string $full_name = null;
    public ?string $first_name = null;
    public ?string $last_name = null;
    public ?string $role = null;
    public ?bool $is_active = null;
    public ?string $last_login = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;
    
    /**
     * Create User from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $user = new self();
        $user->id = $data['id'] ?? null;
        $user->username = $data['username'] ?? null;
        $user->password = $data['password'] ?? null;
        $user->full_name = $data['full_name'] ?? null;
        $user->first_name = $data['first_name'] ?? null;
        $user->last_name = $data['last_name'] ?? null;
        $user->role = $data['role'] ?? 'user';
        $user->is_active = isset($data['is_active']) ? (bool)$data['is_active'] : true;
        $user->last_login = $data['last_login'] ?? null;
        $user->created_at = $data['created_at'] ?? null;
        $user->updated_at = $data['updated_at'] ?? null;
        
        return $user;
    }
    
    /**
     * Convert to array
     * 
     * @param bool $excludePassword
     * @return array
     */
    public function toArray(bool $excludePassword = true): array
    {
        $data = [
            'id' => $this->id,
            'username' => $this->username,
            'full_name' => $this->full_name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'role' => $this->role,
            'is_active' => $this->is_active,
            'last_login' => $this->last_login,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
        
        if (!$excludePassword) {
            $data['password'] = $this->password;
        }
        
        return $data;
    }
    
    /**
     * Check if user is admin
     * 
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    
    /**
     * Check if user is active
     * 
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->is_active === true;
    }
}
