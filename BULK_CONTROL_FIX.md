# Bulk Control Error Fix

## Error Message
```
Network error: Unexpected token '<', "<br /> <b>"... is not valid JSON
```

This error appeared when selecting multiple devices to turn ON or OFF.

## Root Cause ✅

**Problem**: The `DeviceRepository::updateById()` method expected ALL device fields to be provided:
- `device_name`
- `device_id`
- `api_key`
- `sim_card_number`
- `is_active`

But bulk control only provided:
- `outlet_status` (e.g., "on" or "off")
- `control_mode` (e.g., "manual")

This caused PHP to throw an error when trying to access undefined array keys like `$data['device_name']`, which resulted in an HTML error page being returned instead of JSON.

## Fix Applied ✅

**File**: `app/repositories/DeviceRepository.php`  
**Method**: `updateById(int $id, array $data): bool`

**Changed from**: Fixed field UPDATE (all fields required)
```php
public function updateById(int $id, array $data): bool
{
    $stmt = $this->db->prepare(
        'UPDATE iot_outlet_devices 
         SET device_name = ?, device_id = ?, api_key = ?, sim_card_number = ?, is_active = ?, updated_at = NOW()
         WHERE id = ?'
    );
    
    return $stmt->execute([
        $data['device_name'],      // ❌ Error if not provided
        $data['device_id'],        // ❌ Error if not provided
        $data['api_key'],          // ❌ Error if not provided
        $data['sim_card_number'] ?? null,
        $data['is_active'] ?? 1,
        $id
    ]);
}
```

**Changed to**: Dynamic UPDATE (only provided fields)
```php
public function updateById(int $id, array $data): bool
{
    // Build dynamic UPDATE query based on provided fields
    $allowedFields = [
        'device_name',
        'device_id',
        'api_key',
        'sim_card_number',
        'is_active',
        'outlet_status',      // ✅ Now supported
        'control_mode',       // ✅ Now supported
        'ip_address',         // ✅ Now supported
        'is_online',          // ✅ Now supported
        'last_heartbeat'      // ✅ Now supported
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
```

## How It Works Now ✅

### Backward Compatible:
The new method supports both:

**1. Full updates** (like editing a device):
```php
$this->deviceRepository->updateById($id, [
    'device_name' => 'New Name',
    'device_id' => 'ESP32_OUTLET_002',
    'api_key' => 'key123',
    'sim_card_number' => '09123456789',
    'is_active' => 1
]); // ✅ Works!
```

**2. Partial updates** (like bulk control):
```php
$this->deviceRepository->updateById($id, [
    'outlet_status' => 'on',
    'control_mode' => 'manual'
]); // ✅ Now works!
```

**3. Any combination**:
```php
$this->deviceRepository->updateById($id, [
    'is_online' => true,
    'last_heartbeat' => date('Y-m-d H:i:s'),
    'ip_address' => '192.168.1.100'
]); // ✅ Works!
```

## Testing

### Test 1: Bulk Turn ON Multiple Devices

1. Go to Outlet Control or Device Management page
2. Select multiple devices (checkboxes)
3. Click "Turn ON All Selected" button
4. Should see success message: "Bulk control completed: X successful"

**Expected**:
- ✅ No error message
- ✅ All selected online devices turn ON
- ✅ Offline devices are skipped with count shown

### Test 2: Bulk Turn OFF Multiple Devices

1. Select multiple devices
2. Click "Turn OFF All Selected" button
3. Should see success message

**Expected**:
- ✅ No error message
- ✅ All selected online devices turn OFF

### Test 3: Edit Single Device (Backward Compatibility Test)

1. Go to Device Management
2. Click "Edit" on any device
3. Change device name
4. Click "Update Device"

**Expected**:
- ✅ Device updates successfully
- ✅ No errors

### Test 4: Bulk Set Mode

1. Select multiple devices
2. Click "Set Mode" → Choose "Scheduled"
3. Should see success message

**Expected**:
- ✅ No error message
- ✅ All devices mode changed to "Scheduled"

## What This Fixes

| Action | Before | After |
|--------|--------|-------|
| Bulk Turn ON/OFF | ❌ JSON error | ✅ Works |
| Bulk Set Mode | ❌ JSON error | ✅ Works |
| Edit Device | ✅ Works | ✅ Still works |
| Single Device Control | ✅ Works | ✅ Still works |

## Files Modified

1. ✅ `app/repositories/DeviceRepository.php` - Updated `updateById()` method

**No other files need changes!**

## Upload Instructions

**For Online Server:**
1. Upload the updated file: `app/repositories/DeviceRepository.php`
2. Replace existing file on server
3. Test bulk control immediately

**File to upload:**
- `app/repositories/DeviceRepository.php`

## Summary

**Problem**: Bulk control crashed because `updateById()` expected all fields  
**Solution**: Made `updateById()` dynamic - accepts any combination of fields  
**Result**: ✅ Bulk control now works  
**Bonus**: ✅ Method is more flexible for future updates  
**Compatibility**: ✅ All existing code still works  

---

**Status**: FIXED ✅  
**Upload**: Update `app/repositories/DeviceRepository.php` on server  
**Test**: Try bulk control again - should work perfectly!
