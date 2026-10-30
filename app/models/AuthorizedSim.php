<?php

/**
 * Authorized SIM Model
 * 
 * Represents an authorized phone number for SMS commands
 */

class AuthorizedSim
{
    public ?int $id = null;
    public ?string $phone_number = null;
    public ?string $owner_name = null;
    public ?string $first_name = null;
    public ?string $last_name = null;
    public ?string $description = null;
    public ?bool $is_active = null;
    public ?int $created_by = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;
    
    // Related data
    public ?string $creator_username = null;
    public ?string $creator_full_name = null;
    
    /**
     * Create AuthorizedSim from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $sim = new self();
        $sim->id = $data['id'] ?? null;
        $sim->phone_number = $data['phone_number'] ?? null;
        $sim->owner_name = $data['owner_name'] ?? null;
        $sim->first_name = $data['first_name'] ?? null;
        $sim->last_name = $data['last_name'] ?? null;
        $sim->description = $data['description'] ?? null;
        $sim->is_active = isset($data['is_active']) ? (bool)$data['is_active'] : true;
        $sim->created_by = $data['created_by'] ?? null;
        $sim->created_at = $data['created_at'] ?? null;
        $sim->updated_at = $data['updated_at'] ?? null;
        
        // Related data
        $sim->creator_username = $data['creator_username'] ?? null;
        $sim->creator_full_name = $data['creator_full_name'] ?? null;
        
        return $sim;
    }
    
    /**
     * Convert to array
     * 
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'phone_number' => $this->phone_number,
            'owner_name' => $this->owner_name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
    
    /**
     * Get formatted phone number for display
     * 
     * @return string
     */
    public function getFormattedPhone(): string
    {
        if (!$this->phone_number) {
            return '';
        }
        
        // Format +639123456789 to +63 912 345 6789
        if (preg_match('/^\+63(\d{3})(\d{3})(\d{4})$/', $this->phone_number, $matches)) {
            return "+63 {$matches[1]} {$matches[2]} {$matches[3]}";
        }
        
        return $this->phone_number;
    }
}
