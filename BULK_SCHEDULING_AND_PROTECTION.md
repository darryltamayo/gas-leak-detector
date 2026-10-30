# Bulk Schedule Creation & Schedule Protection Implementation

## Overview
Implemented two major features:
1. **Bulk Schedule Creation** - Create schedules for multiple devices at once
2. **Schedule Protection** - Prevent users from interrupting active schedules

---

## Feature 1: Bulk Schedule Creation

### Changes Made

#### 1. Schedule View (`app/views/schedule/index.php`)
**Device Selection Changed:**
- ✅ Changed from single device dropdown to multiple device checkboxes
- ✅ Added "Select All Devices" checkbox with functionality
- ✅ Shows device name, ID, and online/offline status for each device
- ✅ Displays scrollable device list in form

**JavaScript Functions Added:**
```javascript
function toggleAllDevices(checked)        // Select/deselect all devices
function updateSelectAllCheckbox()        // Update "Select All" state based on individual selections
```

**Form Validation Updated:**
- Now checks for `device_ids[]` checkboxes instead of single dropdown
- Validates at least one device is selected

#### 2. Schedule Controller (`app/controllers/ScheduleController.php`)
**store() Method Completely Rewritten:**
- ✅ Now accepts `device_ids[]` array instead of single `device_id`
- ✅ Creates separate schedule for each selected device
- ✅ Logs activity for each device with device name
- ✅ Checks for schedule overlaps per device (warning only)
- ✅ Returns success count: "Schedule created for 3 device(s) successfully"
- ✅ Handles partial failures gracefully

**Example Usage:**
```
User selects 3 devices → Same schedule created for all 3 devices
If 1 fails → Shows "Schedule created for 2 out of 3 device(s)"
```

---

## Feature 2: Schedule Protection

### Changes Made

#### 1. Outlet Controller (`app/controllers/OutletController.php`)

**getActiveSchedule() Method Enhanced:**
```php
private function getActiveSchedule(?int $deviceId = null): ?array
```
- ✅ Now accepts optional `$deviceId` parameter
- ✅ Checks if specific device has active schedule running
- ✅ Validates schedule is within ON-OFF time window
- ✅ Checks if today matches schedule days
- ✅ Validates one-time schedule dates
- ✅ Returns schedule info including creator and end time

**turnOff() Method Updated:**
- ✅ Checks if regular user is trying to turn OFF during active schedule
- ✅ **Admins bypass protection** - can override anytime
- ✅ Returns 403 error with detailed schedule info if blocked
- ✅ Only checks device-specific schedules

**toggle() Method Updated:**
- ✅ Same schedule protection as turnOff()
- ✅ Only blocks when toggling from ON to OFF

**bulkControl() Method Updated:**
- ✅ Checks schedule protection for each device in bulk operation
- ✅ Counts blocked devices separately
- ✅ Returns blocked device names in response
- ✅ Continues with other devices if some are blocked

**New Method: controlDevice()**
```php
public function controlDevice(): void
```
- ✅ Unified endpoint for single device control
- ✅ Used by dashboard and outlet pages
- ✅ Includes schedule protection checks
- ✅ Returns detailed blocking information

#### 2. Router (`app/Router.php`)
**Added Route:**
```php
elseif ($action === 'control-device') {
    $action = 'controlDevice';
}
```

#### 3. Dashboard View (`app/views/dashboard/index.php`)
**controlDeviceOutlet() Function Updated:**
- ✅ Now uses `/outlet/control-device` endpoint
- ✅ Sends `device_id` and `action` in single request
- ✅ Displays schedule protection modal when blocked
- ✅ Shows schedule name, creator, and end time

#### 4. Outlet View (`app/views/outlet/index.php`)
**controlSingleDevice() Function Updated:**
- ✅ Now uses `/outlet/control-device` endpoint
- ✅ Displays schedule protection information
- ✅ Shows detailed blocking message

---

## How Schedule Protection Works

### Protection Rules
1. **Regular Users:**
   - ❌ CANNOT turn OFF outlet during active schedule (within ON-OFF time window)
   - ✅ CAN turn ON anytime
   - Shows modal: "🔒 Schedule Protection Active"

2. **Admin Users:**
   - ✅ CAN override schedules anytime
   - ✅ Full control regardless of active schedules

### Active Schedule Definition
A schedule is considered "active" when ALL conditions are met:
- ✅ Schedule is enabled (`is_enabled = 1`)
- ✅ Current time is between `time_on` and `time_off`
- ✅ Today matches one of the schedule days
- ✅ For one-time schedules: Today matches `schedule_date`
- ✅ Schedule belongs to the specific device being controlled

### Protection Scenarios

**Scenario 1: Regular User, Schedule Running**
```
Time: 10:00 AM
Schedule: 8:00 AM - 5:00 PM (Active)
User: tries to turn OFF
Result: ❌ BLOCKED - Shows protection modal
```

**Scenario 2: Regular User, Outside Schedule Time**
```
Time: 6:00 PM
Schedule: 8:00 AM - 5:00 PM (Inactive)
User: tries to turn OFF
Result: ✅ ALLOWED - No active schedule
```

**Scenario 3: Admin, Schedule Running**
```
Time: 10:00 AM
Schedule: 8:00 AM - 5:00 PM (Active)
Admin: tries to turn OFF
Result: ✅ ALLOWED - Admin override
```

**Scenario 4: Bulk Control, Mixed Devices**
```
Device 1: No active schedule → ✅ Controlled
Device 2: Active schedule (User) → ❌ Blocked
Device 3: Active schedule (Admin) → ✅ Controlled
Result: Shows "2 successful, 1 blocked by active schedule (Device 2)"
```

---

## API Responses

### Success Response
```json
{
  "success": true,
  "message": "✅ Device turned OFF successfully",
  "status": "off",
  "mode": "manual"
}
```

### Blocked by Schedule Response
```json
{
  "success": false,
  "message": "🔒 Schedule Protection Active...",
  "blocked_by_schedule": true,
  "schedule_info": {
    "name": "Office Hours Schedule",
    "created_by": "Admin User",
    "time_off": "5:00 PM"
  }
}
```

### Bulk Control Response
```json
{
  "success": true,
  "message": "Bulk control completed: 2 successful, 1 blocked by active schedule (ESP32_OUTLET_002)",
  "details": {
    "success": 2,
    "offline": 0,
    "blocked": 1,
    "failed": 0,
    "blocked_devices": ["ESP32_OUTLET_002"]
  }
}
```

---

## User Experience

### Schedule Creation Flow
1. User clicks "Create Schedule" button
2. Form shows device list with checkboxes
3. User can select individual devices OR click "Select All"
4. Checkbox count updates dynamically
5. User enters schedule details (name, times, days)
6. Submit → Creates schedule for ALL selected devices
7. Success message: "Schedule created for 3 device(s) successfully"

### Schedule Protection Flow (Regular User)
1. User tries to turn OFF outlet
2. System checks for active schedule
3. **If schedule is active:**
   - Request blocked
   - Modal displays:
     - 🔒 Schedule Protection Active
     - Schedule name
     - Created by whom
     - End time
     - "Only admins can override"
4. **If no active schedule:**
   - Outlet turns OFF normally
   - Switches to Manual mode

### Schedule Protection Flow (Admin)
1. Admin tries to turn OFF outlet
2. System checks role = admin
3. **Bypasses all schedule checks**
4. Outlet turns OFF immediately
5. Logs: "Outlet turned OFF via manual control"

---

## Benefits

### For Users
- ✅ Create schedules for multiple devices at once (saves time)
- ✅ Cannot accidentally interrupt important scheduled operations
- ✅ Clear feedback when blocked by schedule
- ✅ Can see which schedule is blocking them

### For Admins
- ✅ Full override capability for emergencies
- ✅ Users can't bypass automation rules
- ✅ Better activity logging with device names
- ✅ Audit trail of who created schedules

### For System
- ✅ Consistent schedule enforcement
- ✅ Prevents conflicting manual/scheduled operations
- ✅ Better data integrity
- ✅ Device-specific schedule checks

---

## Testing Checklist

### Bulk Schedule Creation
- [ ] Select 1 device → Creates 1 schedule
- [ ] Select 3 devices → Creates 3 schedules
- [ ] "Select All" → Selects all devices
- [ ] Uncheck individual → "Select All" becomes indeterminate
- [ ] Submit without selection → Shows error
- [ ] Partial failure → Shows correct count

### Schedule Protection (Regular User)
- [ ] Turn OFF during schedule → Blocked
- [ ] Turn OFF outside schedule → Allowed
- [ ] Turn ON during schedule → Allowed
- [ ] Different device, no schedule → Allowed
- [ ] Modal shows correct schedule info
- [ ] Bulk control → Correctly blocks affected devices

### Schedule Protection (Admin)
- [ ] Turn OFF during schedule → Allowed
- [ ] Turn ON during schedule → Allowed
- [ ] Override works for all devices
- [ ] Activity logs show admin actions

### Edge Cases
- [ ] One-time schedule on wrong date → Not active
- [ ] Schedule disabled → Not enforced
- [ ] Multiple overlapping schedules → Most recent wins
- [ ] Device offline → Skip schedule check
- [ ] Wrong day of week → Not active

---

## Database Impact

**No schema changes required!**

All features work with existing database structure:
- `iot_outlet_schedules.device_id` - Already supports device-specific schedules
- `iot_outlet_schedules.is_enabled` - Already has enable/disable flag
- `iot_outlet_schedules.time_on` / `time_off` - Used for active time check
- `iot_outlet_schedules.days_of_week` - Used for day validation
- `iot_outlet_schedules.schedule_date` - Used for one-time schedules

---

## Files Modified

1. ✅ `app/views/schedule/index.php` - Bulk device selection UI & JS
2. ✅ `app/controllers/ScheduleController.php` - Bulk schedule creation logic
3. ✅ `app/controllers/OutletController.php` - Schedule protection checks
4. ✅ `app/Router.php` - New controlDevice route
5. ✅ `app/views/dashboard/index.php` - Updated control functions
6. ✅ `app/views/outlet/index.php` - Updated control functions

---

## Summary

✅ **Bulk Schedule Creation**: Users can now create schedules for multiple devices simultaneously, greatly improving efficiency when managing multiple ESP32 outlets.

✅ **Schedule Protection**: Regular users cannot interrupt running schedules by turning OFF outlets during active time windows. Only admins can override. This ensures scheduled operations complete without interruption.

✅ **User-Friendly**: Clear feedback messages, device-specific checks, and admin override capability make the system both secure and practical.

✅ **Backward Compatible**: All changes work with existing database schema and don't break existing functionality.

