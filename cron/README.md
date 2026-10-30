# Schedule Execution Setup

This folder contains the cron job script that executes schedules automatically.

## How Scheduling Works

1. **You create a schedule** in the web dashboard (e.g., Turn ON at 8:00 AM, OFF at 5:00 PM)
2. **Cron job runs every minute** and checks if it's time to execute any schedule
3. **If time matches**, cron updates database (turns ON/OFF)
4. **ESP32 syncs every 10 seconds** and sees the database change
5. **ESP32 updates relay** to match database

## Setup Options

### Option 1: Hostinger Cron Job (RECOMMENDED)

1. Login to Hostinger Control Panel (hPanel)
2. Go to **Advanced → Cron Jobs**
3. Click **"Create Cron Job"**
4. Set schedule: `* * * * *` (every minute)
5. Command: 
   ```
   /usr/bin/php /home/u907280979/public_html/ac_controller_system/cron/execute_schedules.php
   ```
6. Click **"Create"**

### Option 2: URL Trigger (Alternative)

If you can't set up cron jobs, you can call the script via URL:

1. Use a free cron service like:
   - [cron-job.org](https://cron-job.org)
   - [EasyCron](https://www.easycron.com)
   - [SetCronJob](https://www.setcronjob.com)

2. Set up to call this URL every minute:
   ```
   https://isu-documentrequest.online/ac_controller_system/cron/execute_schedules.php?api_key=IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T
   ```

3. The API key protects the endpoint from unauthorized access

### Option 3: Local Testing (Development)

On your local XAMPP, open Command Prompt and run:

```cmd
cd C:\xampp\htdocs\ac_controller_system\cron
C:\xampp\php\php.exe execute_schedules.php
```

You should see output like:
```
[2026-01-15 08:00:05] Schedule Executor Started
Found 2 active schedule(s)
Current time: 08:00:05
Current day: Monday

Checking schedule: Weekday Morning (ID: 1)
  Time ON: 08:00:00
  Time OFF: 17:00:00
  Days: Mon,Tue,Wed,Thu,Fri
  Repeat: repeat
  → ACTION: Turning ON (scheduled time)
  → SUCCESS: Device turned ON

[2026-01-15 08:00:05] Schedule Executor Completed
```

## Testing Schedules

### Test 1: Create a schedule for 2 minutes from now

1. Go to web dashboard → Schedules
2. Create schedule:
   - Time ON: [Current time + 2 minutes]
   - Time OFF: [Current time + 5 minutes]
   - Days: Select today
   - Enabled: Yes
3. Wait 2 minutes
4. Run cron manually or wait for cron to execute
5. Check if outlet turns ON
6. Wait 3 more minutes (5 minutes total)
7. Check if outlet turns OFF

### Test 2: Run cron manually

```cmd
C:\xampp\php\php.exe C:\xampp\htdocs\ac_controller_system\cron\execute_schedules.php
```

You'll see detailed output of what the cron is doing.

## Troubleshooting

### Schedules not executing?

1. **Check if cron is running:**
   - On Hostinger: Check Cron Jobs page, look for "Last Run" time
   - Should update every minute

2. **Check schedule settings:**
   - Schedule must be Enabled
   - Today must be in selected days
   - Device control_mode should be "scheduled"

3. **Check cron output:**
   - Run manually to see what's happening
   - Look for error messages

4. **Check time zone:**
   - Server time must match your local time
   - Check in web dashboard: Dashboard shows current server time

### Manual testing:

```cmd
# Run cron manually
C:\xampp\php\php.exe execute_schedules.php

# Check database directly
SELECT * FROM iot_outlet_schedules WHERE is_enabled = 1;
SELECT * FROM iot_outlet_devices;
```

## Important Notes

- **Cron runs every minute** - schedules execute at the specified minute
- **1-minute execution window** - if schedule is 08:00:00, it triggers between 08:00:00-08:00:59
- **One-time schedules** - execute once and won't repeat
- **ESP32 syncs every 10 seconds** - relay updates within 10 seconds of database change
- **Manual mode overrides schedules** - SMS or manual control switches to "manual" mode

## Security

- URL access requires API key
- Only authenticated users can create schedules in web dashboard
- ESP32 API requires API key
- Cron runs with server privileges

## Files

- `execute_schedules.php` - Main cron script
- `README.md` - This file
