# Cron Job - Multi-Device Support Explanation

## Date: August 1, 2026

## YES - The Cron Job Works with ALL Registered ESP32 Devices! ✅

The cron job (`cron/execute_schedules.php`) is designed to process schedules for **ALL registered ESP32 devices** automatically.

---

## How It Works

### 1. Cron Job Execution (Every Minute)
```
* * * * * php /path/to/execute_schedules.php
```
The cron runs every minute and checks ALL schedules in the system.

### 2. Get All Active Schedules
```php
$schedules = $scheduleRepo->getActive();
```
This retrieves **ALL enabled schedules** from the database, regardless of which device they belong to.

### 3. Process Each Schedule
The cron loops through every active schedule:

```php
foreach ($schedules as $schedule) {
    // Get the device for this schedule
    $device = $deviceRepo->findById($schedule->device_id);
    
    // Check if device is online
    // Check if current time matches schedule
    // Turn ON or OFF accordingly
}
```

---

## Key Features

### ✅ Multi-Device Support
- **One cron job handles ALL devices**
- Each schedule points to a specific device via `device_id`
- All devices are processed in the same run

### ✅ Safety Checks
For each schedule, the cron checks:
1. **Device exists** - Schedule must have a valid device
2. **Device is online** - Only controls online devices (last heartbeat < 2 minutes)
3. **Correct day** - Checks if today is in the schedule's days_of_week
4. **Correct time** - Checks if current time matches time_on or time_off
5. **One-time schedules** - Prevents duplicate execution on same day

### ✅ Device Status Tracking
```php
// Before processing schedules, mark offline devices
$offlineCount = $deviceRepo->markOfflineDevices();
```
- Devices without heartbeat for 2+ minutes are marked offline
- Offline devices are **skipped** (cannot be controlled)

---

## Example Scenario

### You Have 3 ESP32 Devices:
1. **ESP32_OUTLET_001** - Office AC (SIM: 09605682714)
2. **ESP32_OUTLET_002** - Classroom 1 AC (SIM: 09123456789)
3. **ESP32_OUTLET_003** - Classroom 2 AC (SIM: 09987654321)

### You Create These Schedules:
- **Schedule 1**: Office AC - ON at 07:00 AM, OFF at 05:00 PM (Mon-Fri)
- **Schedule 2**: Classroom 1 AC - ON at 08:00 AM, OFF at 04:00 PM (Mon-Sat)
- **Schedule 3**: Classroom 2 AC - ON at 09:00 AM, OFF at 03:00 PM (Mon-Sat)

### What Happens:
Every minute, the cron job:

1. Gets all 3 schedules
2. Checks **ESP32_OUTLET_001**:
   - Is it online? (checks last heartbeat)
   - Is today Mon-Fri?
   - Is it 07:00 AM? → Turn ON
   - Is it 05:00 PM? → Turn OFF

3. Checks **ESP32_OUTLET_002**:
   - Is it online?
   - Is today Mon-Sat?
   - Is it 08:00 AM? → Turn ON
   - Is it 04:00 PM? → Turn OFF

4. Checks **ESP32_OUTLET_003**:
   - Is it online?
   - Is today Mon-Sat?
   - Is it 09:00 AM? → Turn ON
   - Is it 03:00 PM? → Turn OFF

**All 3 devices are controlled independently by the same cron job!**

---

## Database Structure

### Schedule Table
```sql
CREATE TABLE iot_outlet_schedules (
    id INT PRIMARY KEY,
    device_id INT,           -- Links to specific device
    schedule_name VARCHAR,
    time_on TIME,
    time_off TIME,
    days_of_week VARCHAR,    -- Mon,Tue,Wed,etc.
    repeat_type ENUM,        -- 'repeat' or 'once'
    is_enabled TINYINT,      -- 1=active, 0=disabled
    ...
);
```

### Each schedule belongs to ONE device via `device_id`

---

## Important Points

### 1. One Cron Job for Everything
- You don't need separate cron jobs for each device
- One cron job processes all devices and all schedules

### 2. Device Must Be Online
- If ESP32 stops sending heartbeat, it's marked offline
- Offline devices are **skipped** (not controlled)
- This prevents errors when device is disconnected

### 3. Each Device Can Have Multiple Schedules
Example:
```
ESP32_OUTLET_001:
  - Schedule A: 07:00-17:00 (Weekdays)
  - Schedule B: 09:00-12:00 (Weekends)
  - Schedule C: One-time event on specific date
```

### 4. Control Mode Must Be "Scheduled"
- If user manually changes mode to "manual", cron won't control it
- This gives users override capability
- When cron turns device ON/OFF, it sets mode to "scheduled"

### 5. Activity Logging
Every cron action is logged:
```php
logActivity($device->id, 'outlet_on', "Schedule 'Office AC' turned outlet ON");
```
- IP address shows as "CRON"
- Tracks which schedule triggered the action
- Visible in activity logs page

---

## Cron Job Setup

### Option 1: Linux cPanel/cron
```bash
* * * * * /usr/bin/php /path/to/ac_controller_system/cron/execute_schedules.php
```

### Option 2: Windows Task Scheduler
```
Program: C:\xampp\php\php.exe
Arguments: C:\xampp\htdocs\ac_controller_system\cron\execute_schedules.php
Trigger: Every 1 minute
```

### Option 3: URL-based (cPanel or external cron service)
```
https://isu-documentrequest.online/ac_controller_system/cron/execute_schedules.php?api_key=IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T
```

---

## Testing Multi-Device Cron

### Test Manually:
```bash
php cron/execute_schedules.php
```

### Sample Output:
```
[2026-08-01 14:30:00] Schedule Executor Started
Checking for offline devices...
Found 5 active schedule(s)
Current time: 14:30:00
Current day: Saturday

Checking schedule: Office AC Schedule (ID: 1)
  Time ON: 07:00:00
  Time OFF: 17:00:00
  Days: Mon,Tue,Wed,Thu,Fri
  → Skipped (not scheduled for today)

Checking schedule: Classroom 1 Morning (ID: 2)
  Time ON: 08:00:00
  Time OFF: 16:00:00
  Days: Mon,Tue,Wed,Thu,Fri,Sat
  → Within ON period (already ON)

Checking schedule: Classroom 2 Morning (ID: 3)
  Time ON: 09:00:00
  Time OFF: 15:00:00
  Days: Mon,Tue,Wed,Thu,Fri,Sat
  → Within ON period (already ON)

[2026-08-01 14:30:00] Schedule Executor Completed
```

---

## Summary

✅ **YES** - One cron job works for ALL registered ESP32 devices
✅ Each device can have multiple schedules
✅ All schedules are processed every minute
✅ Only online devices are controlled
✅ Each device operates independently
✅ All actions are logged to database

**You can add 10, 20, or 100 ESP32 devices - the same cron job will handle them all!**
