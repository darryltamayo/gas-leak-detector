# 📤 How to Upload Fixed File to Server

## ⚠️ YOU MUST DO THIS STEP!

The ESP32 is getting errors because your **online server** still has the **old code**.

Your ESP32 is trying to connect to:
```
https://isu-documentrequest.online/ac_controller_system/api/heartbeat
```

But that server still has the old authentication code!

---

## 📋 Step-by-Step Upload Instructions

### **Step 1: Open File Manager (cPanel or FTP)**

**Option A: cPanel File Manager**
1. Login to your hosting control panel (Hostinger)
2. Go to **File Manager**
3. Navigate to: `/public_html/ac_controller_system/routes/`

**Option B: FTP Client (FileZilla)**
1. Open FileZilla
2. Connect to your server
3. Navigate to: `/public_html/ac_controller_system/routes/`

---

### **Step 2: Backup Old File (IMPORTANT!)**

Before uploading, **backup the current file** in case you need to revert:

**In cPanel:**
1. Right-click on `api.php`
2. Click **Copy**
3. Rename copy to: `api.php.backup.before-fix`

**In FileZilla:**
1. Download `api.php` to your computer
2. Rename downloaded file to: `api.php.backup.before-fix`

---

### **Step 3: Upload Fixed File**

**Upload this file FROM your local computer:**
```
Location: C:\xampp\htdocs\ac_controller_system\routes\api.php
```

**TO this location on server:**
```
/public_html/ac_controller_system/routes/api.php
```

**In cPanel:**
1. Click **Upload** button (top right)
2. Select file: `C:\xampp\htdocs\ac_controller_system\routes\api.php`
3. Click **Overwrite** if asked
4. Wait for upload to complete

**In FileZilla:**
1. In left panel (local): Navigate to `C:\xampp\htdocs\ac_controller_system\routes\`
2. In right panel (server): Navigate to `/public_html/ac_controller_system/routes/`
3. Drag `api.php` from left to right
4. Click **OK** to overwrite

---

### **Step 4: Verify Upload**

**Check file size and date:**
```
Server path: /public_html/ac_controller_system/routes/api.php

File should be:
- Size: ~13-15 KB
- Date: Today's date
- Modified: Just now
```

**In cPanel:**
1. Right-click on `api.php`
2. Click **View** or **Edit**
3. Verify line 38 contains: `$deviceAuthEndpoints = ['heartbeat'`

---

### **Step 5: Test ESP32**

1. **Restart ESP32:**
   - Power cycle or press reset button

2. **Open Serial Monitor:**
   - Set baud rate: 115200
   - Watch for messages

3. **Expected Output:**
   ```
   ✓ WiFi connected!
   📋 Fetching authorized phone numbers from server...
   ✓ Loaded 0 authorized number(s)    ← NO MORE "HTTP -1"
   ✓ Setup complete! Device is ready.
   ♥ Heartbeat sent successfully       ← NO MORE "HTTP 401"
   ```

---

## 🎯 Quick Summary

### **What You're Uploading:**
```
File: routes/api.php
From: C:\xampp\htdocs\ac_controller_system\routes\api.php
To: /public_html/ac_controller_system/routes/api.php
```

### **What Gets Fixed:**
- ✅ HTTP 401 Unauthorized error → FIXED
- ✅ HTTP -1 connection error → FIXED
- ✅ Device authentication → WORKING
- ✅ Heartbeat → SUCCESS

### **Why It Will Work:**
```
OLD CODE (on server now):
- Checks for server API key in header
- ESP32 sends device API key in body
- MISMATCH → 401 ERROR

NEW CODE (after upload):
- Checks for device API key in body
- ESP32 sends device API key in body
- MATCH → SUCCESS ✅
```

---

## 🐛 Troubleshooting

### **Still Getting HTTP 401 After Upload?**

**Check 1: File Actually Uploaded?**
```bash
# View file on server
# Should have today's date and time
```

**Check 2: Clear Cache?**
```bash
# Some servers cache PHP files
# Try: 
# - Restart PHP service in cPanel
# - Or wait 2-3 minutes
```

**Check 3: Correct Path?**
```
File MUST be at:
/public_html/ac_controller_system/routes/api.php

NOT:
/public_html/routes/api.php  ← WRONG!
/ac_controller_system/routes/api.php  ← WRONG!
```

---

### **Still Getting HTTP -1?**

**This is SSL/HTTPS issue, not authentication:**

**Option 1: Check ESP32 Code**
```cpp
// Make sure this line exists:
client.setInsecure();  // Skip SSL verification
```

**Option 2: Check Server URL**
```cpp
const char* SERVER_URL = "https://isu-documentrequest.online";
const char* SERVER_PATH = "/ac_controller_system";
```

**Option 3: Test Direct URL**
Open browser and visit:
```
https://isu-documentrequest.online/ac_controller_system/api/device/authorized-numbers?device_id=ESP32_OUTLET_001&api_key=IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T
```

Should see JSON response with authorized numbers.

---

## ✅ Upload Checklist

- [ ] **Backup old api.php file**
- [ ] **Upload new api.php from local C:\xampp\htdocs\...**
- [ ] **Verify file uploaded (check date/time)**
- [ ] **Verify file size (13-15 KB)**
- [ ] **Restart ESP32**
- [ ] **Check Serial Monitor for success messages**
- [ ] **Verify device shows ONLINE on website**

---

## 🎉 Success Indicators

### **ESP32 Serial Monitor:**
```
♥ Heartbeat sent successfully | Mode: manual
✓ Device status synced
✓ All systems operational
```

### **Website Dashboard:**
```
Device: ESP32_OUTLET_001
Status: 🟢 ONLINE
Last Heartbeat: Just now
Outlet: OFF
```

### **Activity Logs:**
```
✓ Device online
✓ Heartbeat received
✓ Authorized numbers fetched
```

---

## 🚀 After Upload Works

Once heartbeat is working, you can:

1. **Control device from website**
   - Go to Dashboard → Toggle outlet ON/OFF
   - ESP32 will respond to commands

2. **Set up schedules**
   - Create schedules for automatic ON/OFF
   - Device will follow schedule

3. **Monitor device**
   - See real-time status
   - Track activity logs
   - Check connection status

4. **Add more devices**
   - Register new devices
   - Each gets unique Device ID and API Key
   - Each works independently

---

**Need Help?**
If upload doesn't work after following this guide, check:
1. File permissions (should be 644)
2. PHP version (should be 7.4 or higher)
3. Server logs for errors

**File Location Again:**
```
LOCAL:  C:\xampp\htdocs\ac_controller_system\routes\api.php
SERVER: /public_html/ac_controller_system/routes/api.php
```

**Just upload this ONE file and restart ESP32!**
