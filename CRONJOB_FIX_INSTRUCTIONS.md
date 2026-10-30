# Cronjob Fix Instructions

## Problem
The cronjob at cron-job.org is failing with "HTTP error" (401 Unauthorized) because the key verification is failing.

## Solution
The `public/cron.php` file has been updated to:
1. Load the API key from `.env` file instead of hardcoding it
2. Provide debug information when authentication fails

## What Changed
**File:** `public/cron.php`

**Changes:**
- Now loads `.env` file at the beginning
- Uses `$_ENV['API_KEY']` for authentication
- Provides debug information on authentication failure

## Steps to Deploy

### 1. Upload Updated Files
Upload these files to your server:
- `public/cron.php` (REQUIRED - main fix)
- `public/check-cron-key.php` (OPTIONAL - debug helper)
- `.env` (Make sure it's uploaded and contains correct API_KEY)

### 2. Verify .env on Server
Make sure your server's `.env` file contains:
```
API_KEY=IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T
```

### 3. Test the Cronjob URL
Access this URL in your browser:
```
https://isu-documentrequest.online/ac_controller_system/public/cron.php?key=IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T
```

**Expected Response (Success):**
```json
{
  "success": true,
  "executed": 0,
  "output": [
    "[2026-08-06 XX:XX:XX] Schedule Executor Started",
    "Checking for offline devices...",
    "No active schedules found"
  ],
  "timestamp": "2026-08-06 XX:XX:XX"
}
```

**If Still Failing (Debug):**
```json
{
  "error": "Unauthorized - Invalid key",
  "debug": {
    "key_provided": "IOT_OUTLET_2026_SECU...",
    "key_expected": "IOT_OUTLET_2026_SECU...",
    "env_loaded": true
  }
}
```

The debug info will tell you:
- What key you provided
- What key the server expects
- Whether the .env file loaded successfully

### 4. Update Cron-Job.org
Once the URL works in your browser, update your cronjob at cron-job.org:

**URL to use:**
```
https://isu-documentrequest.online/ac_controller_system/public/cron.php?key=IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T
```

**Schedule:** Every 1 minute (`* * * * *`)

## Troubleshooting

### Issue: Still getting 401 error
**Causes:**
1. `.env` file not uploaded to server
2. `.env` file has wrong API_KEY value
3. File permissions prevent reading `.env`

**Solutions:**
1. Re-upload `.env` file
2. Check that API_KEY line is: `API_KEY=IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T`
3. Set `.env` file permissions to 644 (readable)

### Issue: Empty response or PHP error
**Causes:**
1. PHP syntax error
2. Missing required files
3. Database connection issue

**Solutions:**
1. Check PHP error logs on server
2. Make sure all files in `/app/` are uploaded
3. Verify database credentials in `.env`

## Testing Checklist

- [ ] Upload `public/cron.php` to server
- [ ] Verify `.env` exists on server with correct API_KEY
- [ ] Test URL in browser - should return JSON (not 401 error)
- [ ] Update cron-job.org with working URL
- [ ] Wait 1 minute and check cron-job.org dashboard for success
- [ ] Create a test schedule and verify it executes

## How to Create a Test Schedule

1. Login to admin panel
2. Go to **Devices** and note your device ID
3. Go to **Schedules** → **Create Schedule**
4. Fill in:
   - Device: Select your ESP32 device
   - Schedule Name: "Test Schedule"
   - Time ON: Current time + 2 minutes
   - Time OFF: Current time + 5 minutes
   - Days: Today
   - Repeat: Once
5. Enable the schedule (toggle ON)
6. Go to **Outlet Control** and set control mode to "Scheduled"
7. Wait for the scheduled time and check if device turns on

## Important Notes

✅ **Key must match exactly** - No extra spaces, correct case
✅ **File must be uploaded** - Changes in local files don't affect live server
✅ **.env must be readable** - Check file permissions
✅ **Cronjob runs every minute** - Results show up in cron-job.org dashboard

## Files Modified
- `public/cron.php` - Main cronjob script
- `public/check-cron-key.php` - Debug helper (new file)
