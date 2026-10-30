# 🔧 Fix All 4 Warnings - Quick Guide

## Current Status
Your test found **4 warnings** that need attention. Here's how to fix them all:

---

## ✅ Warning 1: APP_URL Mismatch (FIXED!)

**Issue:** Configured as `/ac_controller_system` but detected as root domain

**Solution:** ✓ Already fixed in `.env` file
```env
APP_URL=https://isu-documentrequest.online
```

**To verify:** Refresh the test page, this warning should be gone.

---

## ⚠️ Warning 2 & 3: Device Offline

**Issue:** 
- No devices are currently online
- Device 'Main Outlet' last heartbeat was 4573 seconds ago (over 1 hour)

**Root Cause:** ESP32 hardware is not connected to your online server

### 🔌 Quick Fix Steps:

**1. Update ESP32 Code**
Open: `esp32/universal_outlet_controller.ino`

Change these lines:
```cpp
// WiFi Credentials
const char* ssid = "YOUR_WIFI_NAME";        // ← Your actual WiFi name
const char* password = "YOUR_WIFI_PASSWORD"; // ← Your actual WiFi password

// Server Configuration (IMPORTANT!)
const char* serverUrl = "isu-documentrequest.online";  // NO http:// or https://
const int serverPort = 443;                             // 443 for HTTPS
const bool useHTTPS = true;                             // true for HTTPS

// API Credentials (from database)
String API_KEY = "IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T";
String DEVICE_ID = "ESP32_OUTLET_001";
```

**2. Upload to ESP32**
- Connect ESP32 via USB
- Open Arduino IDE
- Select Board: "ESP32 Dev Module"  
- Click Upload
- Open Serial Monitor (115200 baud) to see connection status

**3. Verify Connection**
In Serial Monitor you should see:
```
✓ WiFi connected! IP: 192.168.x.x
✓ Server connected!
✓ Heartbeat sent successfully
```

**4. Check Dashboard**
- Go to: https://isu-documentrequest.online/public/
- Device should show: ✓ ONLINE (green)
- Last Heartbeat: "Just now"

### 📖 Detailed Guide
See: `esp32/ONLINE_SERVER_CONFIG.txt` for complete instructions and troubleshooting

---

## ⚠️ Warning 4: Cron Job Not Configured

**Issue:** No schedule executions found in logs

**Root Cause:** Automatic schedule execution (cron job) is not set up

### ⏰ Quick Fix Steps:

**1. Login to cPanel**
- Go to your hosting control panel
- Find "Cron Jobs" section

**2. Add New Cron Job**

**Frequency:** Every minute (or select: `* * * * *`)

**Command:** 
```bash
/usr/bin/php /home/u907280979/public_html/cron/execute_schedules.php
```

*Note: Path may vary. If this doesn't work, try:*
```bash
/usr/local/bin/php /home/u907280979/public_html/cron/execute_schedules.php
```

**3. Save and Wait**
- Save the cron job
- Wait 2-3 minutes
- Refresh your test page
- Warning should disappear

**4. Verify It's Working**
Create a test schedule:
1. Login to system
2. Go to Schedules
3. Create schedule for current time + 2 minutes
4. Wait and check Activity Logs
5. Should see automatic execution

### 📖 Detailed Guide
See: `cron/CPANEL_CRON_SETUP.txt` for complete cPanel setup instructions

### 🚨 Important!
Without cron job:
- ❌ Schedules won't execute automatically
- ❌ Outlets won't turn ON/OFF at scheduled times
- ✓ Manual control still works
- ✓ Web interface still works

---

## 🎯 Verification Checklist

After fixing all warnings:

### Test System Results
- [ ] APP_URL warning gone
- [ ] Device shows ONLINE
- [ ] Heartbeat is recent (< 2 minutes ago)
- [ ] Schedule execution logs appear

### Dashboard Check
- [ ] Can login successfully
- [ ] Device status shows green (ONLINE)
- [ ] Can manually control outlet
- [ ] Schedules page loads

### ESP32 Check (if hardware available)
- [ ] ESP32 connected to WiFi
- [ ] Serial monitor shows successful connection
- [ ] Heartbeat being sent every minute
- [ ] Can see heartbeat in activity logs

### Cron Job Check
- [ ] Cron job created in cPanel
- [ ] Test schedule executes on time
- [ ] Activity logs show "Schedule executed"
- [ ] Outlets turn ON/OFF automatically

---

## 🆘 Still Have Issues?

### If ESP32 Won't Connect:
1. Check `esp32/ONLINE_SERVER_CONFIG.txt` troubleshooting section
2. Try accessing API manually: https://isu-documentrequest.online/public/api.php?action=heartbeat
3. Check Serial Monitor for exact error message
4. Verify SSL certificate is valid (or try HTTP instead)

### If Cron Won't Work:
1. Check `cron/CPANEL_CRON_SETUP.txt` troubleshooting section
2. Run manually via SSH: `php cron/execute_schedules.php`
3. Check PHP error logs in cPanel
4. Contact hosting support for help

### Test API Manually:
Open in browser:
```
https://isu-documentrequest.online/public/api.php?action=heartbeat
```

Should show JSON response (even if error, proves API is reachable)

---

## 📊 Priority Order

If you can only fix some now, do them in this order:

1. **APP_URL** (Already fixed! ✓)
2. **Cron Job** - Critical for schedules to work
3. **ESP32 Device** - Needed for actual hardware control

Note: System works partially without ESP32 hardware, but you need the cron job for schedules to execute automatically.

---

## ✨ After All Fixes

Your test page should show:
```
Overall Status: Excellent
Total Tests: XX
Passed: XX (100%)
Failed: 0
Warnings: 0
```

All green! System fully operational! 🚀

---

**Need more help?** Check the detailed guides:
- `esp32/ONLINE_SERVER_CONFIG.txt` - ESP32 setup
- `cron/CPANEL_CRON_SETUP.txt` - Cron job setup
- `ONLINE_DEPLOYMENT_NOTES.md` - Complete deployment guide
