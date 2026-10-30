<?php

class Schedule
{
    public ?int $id = null;
    public ?int $user_id = null;
    public ?int $device_id = null;
    public ?string $schedule_name = null;
    public ?string $time_on = null;
    public ?string $time_off = null;
    public ?bool $is_enabled = null;
    public ?string $days_of_week = null;
    public ?string $repeat_type = 'repeat'; // 'repeat' or 'once'
    public ?string $schedule_date = null; // Specific date for one-time schedules
    public ?string $last_executed = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;
    
    // Related data
    public ?string $username = null;
    public ?string $device_name = null;
    
    public static function fromArray(array $data): self
    {
        $schedule = new self();
        $schedule->id = $data['id'] ?? null;
        $schedule->user_id = $data['user_id'] ?? null;
        $schedule->device_id = $data['device_id'] ?? null;
        $schedule->schedule_name = $data['schedule_name'] ?? null;
        $schedule->time_on = $data['time_on'] ?? null;
        $schedule->time_off = $data['time_off'] ?? null;
        $schedule->is_enabled = isset($data['is_enabled']) ? (bool)$data['is_enabled'] : true;
        $schedule->days_of_week = $data['days_of_week'] ?? 'Mon,Tue,Wed,Thu,Fri,Sat,Sun';
        $schedule->repeat_type = $data['repeat_type'] ?? 'repeat';
        $schedule->schedule_date = $data['schedule_date'] ?? null;
        $schedule->last_executed = $data['last_executed'] ?? null;
        $schedule->created_at = $data['created_at'] ?? null;
        $schedule->updated_at = $data['updated_at'] ?? null;
        $schedule->username = $data['username'] ?? null;
        $schedule->device_name = $data['device_name'] ?? null;
        return $schedule;
    }
    
    public function getDaysArray(): array
    {
        return $this->days_of_week ? explode(',', $this->days_of_week) : [];
    }
}
