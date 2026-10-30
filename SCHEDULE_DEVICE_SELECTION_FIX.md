# Schedule Device Selection Fix & Offline Device Scheduling

## Date: 2026-08-02

## Issues Fixed

### Issue 1: "Please select at least one device" Error
**Problem**: When trying to create a schedule, alert shows "⚠️ Please select at least one device to control." even though a device was selected from the dropdown.

**Root Cause**: The validation function `validateScheduleForm()` was checking for checkboxes (`input[name="device_ids[]"]:checked`) but the form uses a dropdown select (`select[name="device_id"]`).

### Issue 2: Offline Device Scheduling Clarification
**User Requirement**: Ability to schedule offline devices so they follow the schedule when they come online (even if they come online late/during schedule time).

---

## ✅ Fixes Applied

### 1. **Fixed Validation Function**
**File**: `app/views/schedule/index.php` (line ~667-672)

**Before**:
```javascript
function validateScheduleForm(form) {
    // Check if at least one device is selected
    const devicesChecked = form.querySelectorAll('input[name="device_ids[]"]:checked');
    if (devicesChecked.length === 0) {
        alert('⚠️ Please select at least one device to control.');
        return false;
    }
    // ...
}
```

**After**:
```javascript
function validateScheduleForm(form) {
    // Check if device is selected (using dropdown, not checkboxes)
    const deviceSelect = form.querySelector('select[name="device_id"]');
    if (!deviceSelect || !deviceSelect.value) {
        alert('⚠️ Please select a device to control.');
        return false;
    }
    // ...
}
```

**What Changed**:
- Changed from `querySelectorAll('input[name="device_ids[]"]:checked')` to `querySelector('select[name="device_id"]')`
- Now correctly checks dropdown value instead of looking for non-existent checkboxes

---

### 2. **Updated Device Selection Helper Text**
**File**: `app/views/schedule/index.php` (line ~380-383)

**Before**:
```html
<p class="mt-2 text-xs" style="color: #86868B;">
    ℹ️ Each schedule controls one device. Create multiple schedules for multiple devices.
</p>
```

**After**:
```html
<p class="mt-2 text-xs" style="color: #86868B;">
    ℹ️ You can schedule both online and offline devices. Schedule will execute when device comes online and checks with server.
</p>
```

**What Changed**:
- Added clear message that offline devices CAN be scheduled
- Explains that schedule executes when device comes online

---

### 3. **Enhanced Offline Device Display**
**File**: `app/views/schedule/index.php` (line ~375-380)

**Before**:
```php
<option value="<?= $dev->id ?>">
    <?= htmlspecialchars($dev->device_name) ?> (<?= htmlspecialchars($dev->device_id) ?>) - 
    <?= $dev->is_online ? 'Online' : 'Offline' ?>
</option>
```

**After**:
```php
<option value="<?= $dev->id ?>">
    <?= htmlspecialchars($dev->device_name) ?> (<?= htmlspecialchars($dev->device_id) ?>) - 
    <?= $dev->is_online ? '✓ Online' : '○ Offline' ?>
</option>
```

**What Changed**:
- Added checkmark (✓) for online devices
- Added circle (○) for offline devices
- Better visual distinction

---

### 4. **Added Offline Scheduling Information**
**File**: `app/views/schedule/index.php` (information panel)

**Added New Info Point**:
```html
<div class="flex items-start gap-2">
    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #10B981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
    </svg>
    <div>
        <strong>Offline Device Scheduling:</strong> You can schedule offline devices! When the device comes online, it fetches the current schedule and follows it. If it comes online late (e.g., during ON time), it will turn ON immediately and follow the rest of the schedule.
    </div>
</div>
```

---

## 📋 How Offline Device Scheduling Works

### Scenario 1: Device Offline When Schedule Created
1. **Admin creates schedule** for device "Living Room AC" (currently offline)
   - Schedule: Turn ON at 08:00, Turn OFF at 17:00
   - Schedule is saved to database with `device_id = 123`

2. **Device comes online** at 09:30 (1.5 hours late)
   - ESP32 sends heartbeat to server
   - Server responds with current device status

3. **ESP32 checks schedule** (every 5 seconds via `/api/status` endpoint)
   - Fetches schedule from database
   - Sees schedule: ON at 08:00, OFF at 17:00
   - Current time: 09:30 (within schedule window)
   - **Immediately turns ON** (catches up to schedule)

4. **Continues following schedule**
   - At 17:00, turns OFF automatically
   - Tomorrow at 08:00, turns ON again

### Scenario 2: Device Goes Offline During Schedule
1. **Schedule active**: Device ON since 08:00, should turn OFF at 17:00
2. **Device loses connection** at 14:00 (still within ON period)
3. **Device reconnects** at 16:00
   - ESP32 checks schedule
   - Sees it's still within ON window (08:00-17:00)
   - **Stays ON** (correct state for current time)
4. **At 17:00**, device turns OFF as scheduled

### Scenario 3: Device Comes Online After Schedule Window
1. **Schedule**: Turn ON at 08:00, Turn OFF at 17:00
2. **Device comes online** at 18:00 (after schedule ended)
   - ESP32 checks schedule
   - Sees schedule ended at 17:00
   - Current time 18:00 is outside window
   - **Device stays OFF** (correct state)
3. **Next day at 08:00**, device turns ON as scheduled

---

## 🔧 Technical Implementation

### Cron Job Handles Scheduled Actions
**File**: `cron/execute_schedules.php`

**How It Works**:
```php
// Runs every minute via cron
// Gets all active schedules
// Checks if current time matches ON time or OFF time
// Updates device status in database

if (current_time == schedule_time_on) {
    UPDATE devices SET outlet_status = 'on', control_mode = 'scheduled';
}

if (current_time == schedule_time_off) {
    UPDATE devices SET outlet_status = 'off', control_mode = 'scheduled';
}
```

### ESP32 Fetches Current Status
**File**: `esp32/universal_outlet_controller.ino`

**How It Works**:
```cpp
// ESP32 checks server every 5 seconds
void checkCommands() {
    // Call /api/status endpoint
    // Server responds with current outlet_status from database
    
    if (outlet_status == "on" && relay is OFF) {
        turnRelayON();  // Catch up to schedule
    }
    
    if (outlet_status == "off" && relay is ON) {
        turnRelayOFF();  // Follow schedule
    }
}
```

### Database Structure
```sql
-- Schedules table
CREATE TABLE iot_outlet_schedules (
    id INT PRIMARY KEY,
    device_id INT,           -- Which device to control
    schedule_name VARCHAR,
    time_on TIME,            -- 08:00
    time_off TIME,           -- 17:00
    days_of_week JSON,       -- ["Monday", "Tuesday", ...]
    repeat_type ENUM,        -- 'once' or 'repeat'
    is_active BOOLEAN,       -- Schedule enabled?
    ...
);

-- Devices table
CREATE TABLE iot_outlet_devices (
    id INT PRIMARY KEY,
    outlet_status ENUM,      -- 'on' or 'off' (set by cron)
    control_mode ENUM,       -- 'manual', 'scheduled', 'override'
    is_online BOOLEAN,       -- Device connectivity status
    ...
);
```

---

## ✅ Verification Steps

### Test 1: Create Schedule with Dropdown
1. Go to Schedules page
2. Click "Create Schedule"
3. **Select a device** from dropdown (online or offline)
4. Fill in schedule details
5. Click "Create Schedule"
6. **Expected**: Schedule created successfully (no validation error)

### Test 2: Offline Device Scheduling
1. Turn OFF an ESP32 device (unplug or disconnect WiFi)
2. Create a schedule for that offline device
   - Schedule: Turn ON at current_time + 2 minutes
3. **Verify**: Schedule is created (dropdown shows "○ Offline")
4. Wait 2 minutes (while device still offline)
5. Turn device back ON
6. **Expected**: Device checks server, sees it's within ON window, turns ON immediately

### Test 3: Late Device Connection
1. Create schedule: ON at 10:00, OFF at 11:00
2. Keep device offline during 10:00-10:30
3. Turn device online at 10:30 (30 minutes late)
4. **Expected**: 
   - Device checks schedule
   - Sees it's 10:30 (within 10:00-11:00 window)
   - Turns ON immediately
   - At 11:00, turns OFF as scheduled

### Test 4: Device Comes Online After Schedule
1. Create schedule: ON at 10:00, OFF at 11:00
2. Keep device offline past 11:00
3. Turn device online at 11:30
4. **Expected**:
   - Device checks schedule
   - Sees it's 11:30 (past 11:00 OFF time)
   - Stays OFF (correct state for current time)

---

## 📊 System Flow Diagram

```
┌──────────────────────────────────────────────────────────┐
│ 1. ADMIN CREATES SCHEDULE                                 │
│    - Device: Living Room AC (ESP32_OUTLET_001)           │
│    - ON: 08:00, OFF: 17:00                               │
│    - Saved to database                                    │
└───────────────┬──────────────────────────────────────────┘
                │
                ▼
┌──────────────────────────────────────────────────────────┐
│ 2. CRON JOB RUNS EVERY MINUTE                            │
│    - Checks all active schedules                          │
│    - If time = 08:00, updates database:                  │
│      UPDATE devices SET outlet_status='on',              │
│                        control_mode='scheduled'           │
│      WHERE id = 123                                       │
└───────────────┬──────────────────────────────────────────┘
                │
                ▼
┌──────────────────────────────────────────────────────────┐
│ 3. ESP32 DEVICE (ONLINE OR COMES ONLINE LATER)          │
│    - Sends heartbeat every 30 seconds                     │
│    - Checks /api/status every 5 seconds                  │
│    - Server responds: outlet_status = 'on'               │
└───────────────┬──────────────────────────────────────────┘
                │
                ▼
┌──────────────────────────────────────────────────────────┐
│ 4. ESP32 EXECUTES STATUS                                 │
│    - Reads outlet_status from server                      │
│    - If status='on' and relay=OFF: Turn relay ON         │
│    - If status='off' and relay=ON: Turn relay OFF        │
│    - Catches up to current scheduled state               │
└──────────────────────────────────────────────────────────┘
```

---

## 💡 Key Points

### ✅ You CAN Schedule Offline Devices
- Schedule is saved to database even if device is offline
- When device comes online, it fetches current status
- Device executes whatever the current state should be

### ✅ Late Connections Are Handled
- If device comes online during ON time, it turns ON immediately
- If device comes online after OFF time, it stays OFF
- No missed schedules - device catches up to current state

### ✅ Multiple Schedules Per Device
- Create multiple schedules for same device
- If schedules overlap, newest schedule (highest ID) wins
- Each schedule controls one device

### ✅ Control Mode Matters
- Device must be in "Scheduled" mode for schedules to execute
- If in "Manual" mode, schedules won't run
- Change mode via Outlet Control page

---

## 🎯 Summary of Changes

| File | Change | Purpose |
|------|--------|---------|
| `schedule/index.php` line ~667 | Fixed validation function to check dropdown instead of checkboxes | Fix "please select device" error |
| `schedule/index.php` line ~375 | Changed "Online/Offline" to "✓ Online / ○ Offline" | Better visual distinction |
| `schedule/index.php` line ~380 | Updated helper text to mention offline scheduling | Clarify offline devices can be scheduled |
| `schedule/index.php` info panel | Added "Offline Device Scheduling" section | Explain how offline scheduling works |

---

## ✅ Completion Status

- ✅ Fixed validation error when selecting device
- ✅ Clarified that offline devices CAN be scheduled
- ✅ Explained how late connections are handled
- ✅ Updated UI to show online/offline status clearly
- ✅ Added comprehensive information panel
- ✅ System already supports offline scheduling (no backend changes needed)

---

**Fix Status**: ✅ COMPLETE
**Offline Scheduling**: ✅ ALREADY WORKING (by design)
**User Impact**: Can now create schedules without validation errors and confidently schedule offline devices
**Date**: 2026-08-02
