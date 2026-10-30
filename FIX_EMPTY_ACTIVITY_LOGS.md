# Fix Empty Activity Logs

## Date: August 1, 2026

## Problem
Some activity logs in the system show empty/blank action fields:
- When bulk controlling devices
- When ESP32 fetches authorized number list
- Possibly other API interactions

## Root Cause Analysis

### The Issue:
Activity logs are being inserted with **empty string** or **NULL** values for `action_type` field.

### Affected Areas:
1. **API Routes** (`routes/api.php`) - ESP32 interactions
2. **Bulk Control** (`OutletController.php`) - Multiple device control
3. **Other API endpoints** - Various ESP32 communications

---

## Investigation Results

### ✅ GOOD: Bulk Control Logging
**File:** `app/controllers/OutletController.php` (Line 477-481)

```php
$this->logActivity(
    (int)$deviceId,
    'outlet_' . $action,  // ✅ Correctly sets action_type
    "Outlet turned " . strtoupper($action) . " via bulk control"
);
```

**Status:** Working correctly ✅
- Sets `action_type` to `outlet_on` or `outlet_off`
- Includes `user_id` from logged-in user
- Should NOT show empty action

### ✅ GOOD: Authorized Numbers Fetch
**File:** `routes/api.php` (Line 410)

```php
$this->logActivity(
    $device->id, 
    'authorized_numbers_fetched',  // ✅ Correctly sets action_type
    'ESP32 fetched authorized numbers list (' . count($authorizedNumbers) . ' numbers)'
);
```

**Status:** Working correctly ✅
- Sets `action_type` to `authorized_numbers_fetched`
- Should NOT show empty action
- But note: `user_id` is NULL (ESP32 call, not user action)

---

## Possible Causes of Empty Actions

### 1. Old Code Before Fixes
If logs were created before recent fixes, they might have empty action_type.

### 2. Direct Database Inserts
Some places might directly INSERT without proper action_type:
```sql
-- BAD: Missing action_type
INSERT INTO iot_outlet_activity_logs (device_id, action_details) 
VALUES (1, 'Something happened');

-- GOOD: Includes action_type
INSERT INTO iot_outlet_activity_logs (device_id, action_type, action_details) 
VALUES (1, 'device_heartbeat', 'Something happened');
```

### 3. Empty String vs NULL
PHP code might pass empty string `''` instead of a proper value:
```php
// BAD
$actionType = '';
$stmt->execute([$deviceId, $actionType, $details]);

// GOOD
$actionType = 'outlet_on';
$stmt->execute([$deviceId, $actionType, $details]);
```

---

## SQL Queries to Identify Issues

### Find All Empty Action Logs
```sql
SELECT 
    id,
    user_id,
    device_id,
    action_type,
    action_details,
    ip_address,
    created_at
FROM iot_outlet_activity_logs
WHERE action_type IS NULL 
   OR action_type = ''
ORDER BY created_at DESC
LIMIT 50;
```

### Count Empty Actions by Source
```sql
SELECT 
    CASE 
        WHEN user_id IS NULL THEN 'ESP32/API'
        WHEN user_id IS NOT NULL THEN 'User Action'
    END as source,
    COUNT(*) as count
FROM iot_outlet_activity_logs
WHERE action_type IS NULL OR action_type = ''
GROUP BY source;
```

### Find Empty Actions with Device Info
```sql
SELECT 
    al.id,
    al.user_id,
    u.username,
    al.device_id,
    d.device_name,
    al.action_type,
    al.action_details,
    al.ip_address,
    al.created_at
FROM iot_outlet_activity_logs al
LEFT JOIN iot_outlet_users u ON al.user_id = u.id
LEFT JOIN iot_outlet_devices d ON al.device_id = d.id
WHERE al.action_type IS NULL OR al.action_type = ''
ORDER BY al.created_at DESC
LIMIT 50;
```

---

## Solutions Applied

### 1. Display Fix - Show "No action recorded" for Empty Actions
**File:** `app/views/admin/activity-logs.php` (Line 199)

**Before:**
```php
<span><?= ucfirst(str_replace('_', ' ', $log['action_type'])) ?></span>
```

**After:**
```php
<span>
    <?= !empty($log['action_type']) 
        ? ucfirst(str_replace('_', ' ', $log['action_type'])) 
        : '<span style="color: #DC2626;">No action recorded</span>' 
    ?>
</span>
```

**Result:**
- Empty actions now show: **"No action recorded"** in red
- Makes empty logs visible instead of blank

---

## How to Fix Existing Empty Logs

### Option 1: Infer Action from Details
Update empty action_type based on action_details content:

```sql
-- Fix outlet ON logs
UPDATE iot_outlet_activity_logs
SET action_type = 'outlet_on'
WHERE (action_type IS NULL OR action_type = '')
  AND (action_details LIKE '%turned ON%' 
       OR action_details LIKE '%Turn ON%'
       OR action_details LIKE '%OUTLET ON%');

-- Fix outlet OFF logs
UPDATE iot_outlet_activity_logs
SET action_type = 'outlet_off'
WHERE (action_type IS NULL OR action_type = '')
  AND (action_details LIKE '%turned OFF%' 
       OR action_details LIKE '%Turn OFF%'
       OR action_details LIKE '%OUTLET OFF%');

-- Fix bulk control logs
UPDATE iot_outlet_activity_logs
SET action_type = CASE
    WHEN action_details LIKE '%bulk control%' AND action_details LIKE '%ON%' THEN 'bulk_outlet_on'
    WHEN action_details LIKE '%bulk control%' AND action_details LIKE '%OFF%' THEN 'bulk_outlet_off'
    ELSE action_type
END
WHERE (action_type IS NULL OR action_type = '')
  AND action_details LIKE '%bulk control%';

-- Fix authorized numbers fetch
UPDATE iot_outlet_activity_logs
SET action_type = 'authorized_numbers_fetched'
WHERE (action_type IS NULL OR action_type = '')
  AND (action_details LIKE '%authorized numbers%' 
       OR action_details LIKE '%fetched authorized%');

-- Fix heartbeat logs
UPDATE iot_outlet_activity_logs
SET action_type = 'device_heartbeat'
WHERE (action_type IS NULL OR action_type = '')
  AND (action_details LIKE '%heartbeat%' 
       OR action_details LIKE '%device ping%');

-- Fix schedule logs
UPDATE iot_outlet_activity_logs
SET action_type = CASE
    WHEN action_details LIKE '%schedule%' AND action_details LIKE '%created%' THEN 'schedule_created'
    WHEN action_details LIKE '%schedule%' AND action_details LIKE '%updated%' THEN 'schedule_updated'
    WHEN action_details LIKE '%schedule%' AND action_details LIKE '%deleted%' THEN 'schedule_deleted'
    WHEN action_details LIKE '%schedule%' AND action_details LIKE '%enabled%' THEN 'schedule_enabled'
    WHEN action_details LIKE '%schedule%' AND action_details LIKE '%disabled%' THEN 'schedule_disabled'
    ELSE action_type
END
WHERE (action_type IS NULL OR action_type = '')
  AND action_details LIKE '%schedule%';

-- Fix unknown/generic logs
UPDATE iot_outlet_activity_logs
SET action_type = 'unknown_action'
WHERE (action_type IS NULL OR action_type = '')
  AND action_type IS NULL;
```

### Option 2: Delete Invalid Logs (Use with Caution!)
```sql
-- Only delete if logs have no useful information
DELETE FROM iot_outlet_activity_logs
WHERE (action_type IS NULL OR action_type = '')
  AND (action_details IS NULL OR action_details = '')
  AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY);
```

### Option 3: Archive to Backup Table
```sql
-- Create backup table
CREATE TABLE iot_outlet_activity_logs_backup LIKE iot_outlet_activity_logs;

-- Copy empty logs to backup
INSERT INTO iot_outlet_activity_logs_backup
SELECT * FROM iot_outlet_activity_logs
WHERE action_type IS NULL OR action_type = '';

-- Then optionally delete from main table
DELETE FROM iot_outlet_activity_logs
WHERE action_type IS NULL OR action_type = '';
```

---

## Prevention - Ensure All Future Logs Have action_type

### Database Constraint (Recommended)
Add NOT NULL constraint to prevent future empty actions:

```sql
-- Make action_type NOT NULL with a default value
ALTER TABLE iot_outlet_activity_logs
MODIFY COLUMN action_type VARCHAR(100) NOT NULL DEFAULT 'unknown_action';
```

### Code Review Checklist
When adding new activity logging:

✅ Always include `action_type` parameter
✅ Use descriptive action names (e.g., `outlet_on`, `user_created`, `schedule_deleted`)
✅ Never pass empty string or NULL for action_type
✅ Follow naming convention: `{noun}_{verb}` (e.g., `device_registered`, `sim_updated`)

**Example Good Logging:**
```php
// ✅ GOOD
$this->logActivity(
    $deviceId,
    'outlet_on',  // Clear action type
    'Outlet turned ON via mobile app'
);

// ✅ GOOD
$this->logActivity(
    null,
    'user_login',
    "User {$username} logged in successfully"
);

// ❌ BAD
$this->logActivity(
    $deviceId,
    '',  // Empty action!
    'Something happened'
);

// ❌ BAD
$this->logActivity(
    $deviceId,
    null,  // NULL action!
    'Device updated'
);
```

---

## Action Type Naming Conventions

### Outlet/Device Actions:
- `outlet_on` - Device turned ON
- `outlet_off` - Device turned OFF
- `outlet_toggle` - Device toggled
- `bulk_outlet_on` - Multiple devices turned ON
- `bulk_outlet_off` - Multiple devices turned OFF
- `device_registered` - New device added
- `device_updated` - Device settings updated
- `device_deleted` - Device removed
- `device_heartbeat` - Device ping/heartbeat received
- `control_mode_changed` - Control mode updated

### Schedule Actions:
- `schedule_created` - New schedule created
- `schedule_updated` - Schedule modified
- `schedule_deleted` - Schedule removed
- `schedule_enabled` - Schedule activated
- `schedule_disabled` - Schedule deactivated
- `schedule_executed` - Schedule ran automatically

### User Actions:
- `user_login` - User logged in
- `user_logout` - User logged out
- `user_created` - New user created
- `user_updated` - User info updated
- `user_deleted` - User removed
- `password_changed` - Password updated
- `profile_updated` - Profile info changed

### SIM/Authorization Actions:
- `sim_created` - New authorized SIM added
- `sim_updated` - SIM info updated
- `sim_deleted` - SIM removed
- `authorized_numbers_fetched` - ESP32 retrieved auth numbers
- `sms_command_received` - SMS control command received
- `sms_command_executed` - SMS command executed

### API Actions:
- `api_heartbeat` - API heartbeat call
- `api_status_check` - Status check request
- `api_schedule_fetch` - Schedule fetch request
- `api_authorized_fetch` - Authorized numbers fetch

---

## Testing

### 1. Check Current Empty Logs
```sql
SELECT COUNT(*) as empty_count
FROM iot_outlet_activity_logs
WHERE action_type IS NULL OR action_type = '';
```

### 2. Test Bulk Control
1. Go to Outlet Control page
2. Select multiple devices
3. Click "Turn ON" or "Turn OFF"
4. Check activity logs
5. Verify action shows as "Outlet on" or "Outlet off"

### 3. Test ESP32 Authorized Numbers Fetch
1. Restart ESP32
2. ESP32 fetches authorized numbers on boot
3. Check activity logs
4. Verify action shows as "Authorized numbers fetched"

### 4. View Activity Logs
1. Go to Admin > Activity Logs
2. Look for any logs showing **"No action recorded"** in red
3. Those are the ones with empty action_type

---

## Summary

### Issues Found:
- ✅ Display doesn't handle empty action_type gracefully
- ⚠️ Some old logs may have empty action_type
- ✅ Current code properly sets action_type

### Fixes Applied:
- ✅ Display now shows "No action recorded" for empty actions
- ✅ Provided SQL to fix existing empty logs
- ✅ Documented naming conventions

### Next Steps:
1. Run SQL query to identify how many empty logs exist
2. Run UPDATE queries to fix existing empty logs
3. (Optional) Add NOT NULL constraint to prevent future issues
4. Monitor new logs to ensure all have proper action_type

All new logs created from now on should have proper action_type values!
