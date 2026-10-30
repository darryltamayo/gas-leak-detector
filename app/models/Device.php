<?php

/**
 * Device Model
 * 
 * Represents an ESP32 device
 */

class Device
{
    public ?int $id = null;
    public ?string $device_name = null;
    public ?string $device_id = null;
    public ?string $api_key = null;
    public ?string $sim_card_number = null;
    public ?string $outlet_status = null;
    public ?string $control_mode = null;
    public ?bool $is_online = null;
    public ?string $last_heartbeat = null;
    public ?string $firmware_version = null;
    public ?string $ip_address = null;
    public ?bool $is_active = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;
    
    /**
     * Create Device from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $device = new self();
        $device->id = $data['id'] ?? null;
        $device->device_name = $data['device_name'] ?? null;
        $device->device_id = $data['device_id'] ?? null;
        $device->api_key = $data['api_key'] ?? null;
        $device->sim_card_number = $data['sim_card_number'] ?? null;
        $device->outlet_status = $data['outlet_status'] ?? 'off';
        $device->control_mode = $data['control_mode'] ?? 'manual';
        $device->is_online = isset($data['is_online']) ? (bool)$data['is_online'] : false;
        $device->last_heartbeat = $data['last_heartbeat'] ?? null;
        $device->firmware_version = $data['firmware_version'] ?? null;
        $device->ip_address = $data['ip_address'] ?? null;
        $device->is_active = isset($data['is_active']) ? (bool)$data['is_active'] : true;
        $device->created_at = $data['created_at'] ?? null;
        $device->updated_at = $data['updated_at'] ?? null;
        
        return $device;
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
            'device_name' => $this->device_name,
            'device_id' => $this->device_id,
            'sim_card_number' => $this->sim_card_number,
            'outlet_status' => $this->outlet_status,
            'control_mode' => $this->control_mode,
            'is_online' => $this->is_online,
            'last_heartbeat' => $this->last_heartbeat,
            'firmware_version' => $this->firmware_version,
            'ip_address' => $this->ip_address,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
