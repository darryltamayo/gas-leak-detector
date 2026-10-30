# Troubleshooting: Only One Device Executes

## Problem
You scheduled 2 ESP32 devices, but only 1 executes the schedule.

## Common Causes & Solutions

### 1. ✅ **Check: Are Both Schedules Created?**

**Test:**
```
1. Go to Schedules page
2. Count how many schedule cards you see
3. Check device names on each card
```

**Expected:** You should see **2 schedule cards** with different device names

**If you only see 1 card:**
- The schedule wasn't created for the second device
- Try creating the schedule again
- Check browser console for errors

---

### 2. 🌐 **Check: Are Both Devices Online?**

This is the MOST COMMON issue!

**Test:**
1. Go to **Outlet Control** or **Devices** page
2. Check the status of both devices
3. Look for "● Online" vs "○ Offline"

**Solution:**
- ❌ **Offline Device** = Schedule won't execute
- ✅ **Make sure ESP32 is:**
  - Connected to WiFi
  - Sending heartbeat every 30-60 seconds
  - Using correct API URL and credentials

**How to Check Heartbeat:**
- Open Arduino Serial Monitor
- Look for "Heartbeat sent" messages
- Check if it's getting 200 OK response

---

### 3. ⚙️ **Check: Is Control Mode Set to "Scheduled"?**

This is the SECOND MOST COMMON issue!

**Test:**
1. Go to **Outlet Control** page
2. Check the control mode for BOTH devices
3. Should show "Scheduled" mode

**Solution:**
- If mode is "Manual", the schedule won't execute!
- Click the mode dropdown
- Select "Scheduled" mode for BOTH devices

**Why it changes to Manual:**
- When you manually control a device (button/SMS/web)
- It automatically switches to Manual mode
- You must switch it back to Scheduled mode

---

### 4. 🔘 **Check: Are Both Schedules Enabled?**

**Test:**
1. Go to **Schedules** page
2. Look at the toggle switch on each schedule card
3. Should show "● Active" (not "○ Inactive")

**Solution:**
- If toggle is OFF, click it to turn ON
- Inactive schedules don't execute

---

### 5. 📅 **Check: Is Today in the Schedule Days?**

**Test:**
1. Look at the schedule card
2. Check "Days:" field
3. Verify today's day is included

**Example:**
- Today: **Thursday**
- Schedule days: Mon, Tue, Wed, Fri
- Result: **Won't execute** (Thu not included)

---

### 6. ⏰ **Check: Is Current Time Within Schedule Time?**

**Test:**
1. Check current time
2. Check schedule ON/OFF times
3. Cronjob only executes within 1-minute window

**Example:**
- Schedule ON: 08:00 AM
- Current time: 08:05 AM
- Result: **Already missed** (only triggers at 08:00-08:00:59)

**Solution:**
- Wait for next trigger time
- Or create a test schedule for a few minutes from now

---

## 🔍 Debug Tools

### Tool 1: Debug Script

Upload `public/debug-schedules.php` to your server, then access:

```
https://isu-documentrequest.online/ac_controller_system/public/debug-schedules.php
```

This will show you:
- ✅ All schedules and their status
- ✅ All devices and their status
- ✅ Why each schedule might not execute
- ✅ Specific blockers for each device

### Tool 2: Cronjob Output

Access your cronjob URL:

```
https://isu-documentrequest.online/ac_controller_system/public/cron.php?key=IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T
```

Look for lines like:
```json
{
  "output": [
    "Checking schedule: Office Hours (ID: 1)",
    "  Device: ESP32_OUTLET_001",
    "  → Will turn ON",
    "",
    "Checking schedule: Office Hours (ID: 2)",
    "  Device: ESP32_OUTLET_002",
    "  → Skipped (device is offline)",  ← THIS TELLS YOU WHY!
    ""
  ]
}
```

### Tool 3: Activity Logs

1. Go to **Admin** → **Activity Logs**
2. Filter by recent actions
3. Check if both devices show schedule executions

---

## 📋 Checklist: Before Scheduling

Before creating a schedule, verify:

- [ ] **Device 1 is ONLINE** (green dot, recent heartbeat)
- [ ] **Device 2 is ONLINE** (green dot, recent heartbeat)
- [ ] **Device 1 control mode = "Scheduled"**
- [ ] **Device 2 control mode = "Scheduled"**
- [ ] **Current time is BEFORE schedule time** (so you can test)
- [ ] **Today is in the days you're selecting**

Then create the schedule and verify:

- [ ] **2 schedule cards appear** (one per device)
- [ ] **Both schedules show "● Active"** (enabled)
- [ ] **Both show correct device names** (in purple badge)

---

## 🎯 Step-by-Step Testing Procedure

### Test 1: Create Schedule for Future Time

1. **Check current time:** 2:45 PM
2. **Create schedule:**
   - Time ON: 2:50 PM (5 minutes from now)
   - Time OFF: 2:55 PM
   - Select both devices
   - Today only
3. **Verify:** 2 schedule cards created
4. **Set both devices to "Scheduled" mode**
5. **Wait until 2:50 PM**
6. **Check:** Both devices should turn ON

### Test 2: Check Cronjob Output at 2:50 PM

Access cronjob URL at exactly 2:50 PM:
```
https://isu-documentrequest.online/ac_controller_system/public/cron.php?key=IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T
```

Look for output like:
```json
{
  "executed": 2,  ← Should be 2, not 1!
  "output": [
    "=== Executing Actions ===",
    "Device: ESP32_OUTLET_001 (ID: 1)",
    "  Action: Turn on",
    "  → SUCCESS: Device turned ON",
    "",
    "Device: ESP32_OUTLET_002 (ID: 2)",
    "  Action: Turn on",
    "  → SUCCESS: Device turned ON"
  ]
}
```

---

## ❓ FAQ

### Q: I scheduled both but only Device 1 executes
**A:** Device 2 is likely:
- Offline (no heartbeat)
- In Manual mode (not Scheduled)
- Schedule is disabled for Device 2

### Q: Device says "Online" but schedule doesn't execute
**A:** Check control mode! Even if online, it needs to be in "Scheduled" mode

### Q: Schedule worked once, then stopped
**A:** Two possibilities:
1. You manually controlled it (switches to Manual mode)
2. Device went offline (stopped sending heartbeat)

### Q: Both devices online, both scheduled mode, still only one works
**A:** Run the debug script to see detailed blockers:
```
https://isu-documentrequest.online/ac_controller_system/public/debug-schedules.php
```

---

## 🔧 Quick Fixes

### Fix 1: Reset Control Mode
```
1. Go to Outlet Control
2. For EACH device, select "Scheduled" from dropdown
3. Click "Set Mode"
4. Verify it shows "Scheduled" mode
```

### Fix 2: Check ESP32 Heartbeat
```
1. Open Arduino Serial Monitor
2. Should see every 30-60 seconds:
   - "Sending heartbeat..."
   - "Heartbeat response: 200"
3. If not, check WiFi connection and API credentials
```

### Fix 3: Recreate Schedule
```
1. Delete the existing schedules
2. Make sure BOTH devices are:
   - Online (green dot)
   - Scheduled mode
3. Create schedule again
4. Select BOTH devices
5. Verify 2 cards appear
```

---

## 📊 Example Debug Output

**Good Output (Both Will Execute):**
```json
{
  "schedules": [
    {
      "id": 1,
      "name": "Office Hours",
      "device_name": "Office AC",
      "is_enabled": "YES",
      "device_status": {
        "is_online": "ONLINE",
        "control_mode": "scheduled"
      },
      "blockers": ["NONE - Should execute!"]
    },
    {
      "id": 2,
      "name": "Office Hours",
      "device_name": "Classroom AC",
      "is_enabled": "YES",
      "device_status": {
        "is_online": "ONLINE",
        "control_mode": "scheduled"
      },
      "blockers": ["NONE - Should execute!"]
    }
  ]
}
```

**Bad Output (Only One Will Execute):**
```json
{
  "schedules": [
    {
      "id": 1,
      "name": "Office Hours",
      "device_name": "Office AC",
      "blockers": ["NONE - Should execute!"]
    },
    {
      "id": 2,
      "name": "Office Hours",
      "device_name": "Classroom AC",
      "blockers": [
        "Device is OFFLINE",  ← PROBLEM!
        "Control mode is 'manual' (needs 'scheduled')"  ← PROBLEM!
      ]
    }
  ]
}
```

---

## 📁 Files to Upload

Upload these diagnostic tools:
- `public/debug-schedules.php` - Main diagnostic tool

Then access:
```
https://isu-documentrequest.online/ac_controller_system/public/debug-schedules.php
```

This will show you EXACTLY why only one device is executing!
