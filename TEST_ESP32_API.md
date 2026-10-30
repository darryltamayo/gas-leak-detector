# 🧪 ESP32 API Testing Tool

## 📍 Access the Tester

### **Online Server (Main):**
```
https://isu-documentrequest.online/ac_controller_system/test-esp32-api.php
```

### **Local Testing (XAMPP):**
```
http://localhost/ac_controller_system/public/test-esp32-api.php
```

---

## 🎯 What This Tool Does

This testing tool **simulates exactly what your ESP32 does** when connecting to the server.

It tests:
1. ✅ **Heartbeat** - Device check-in (POST request with JSON)
2. ✅ **Authorized Numbers** - Fetch SIM list (GET request)
3. ✅ **Status** - Get device status (GET request)
4. ✅ **Schedules** - Get active schedules (GET request)
5. ✅ **Database** - Verify device exists with correct API key

---

## 📝 How to Use

### **Step 1: Upload Test Files**

Upload these 2 files to your server:
```
FROM: C:\xampp\htdocs\ac_controller_system\public\test-esp32-api.php
TO:   /public_html/ac_controller_system/public/test-esp32-api.php

FROM: C:\xampp\htdocs\ac_controller_system\public\test-esp32-api-db.php
TO:   /public_html/ac_controller_system/public/test-esp32-api-db.php
```

### **Step 2: Open in Browser**

Visit:
```
https://isu-documentrequest.online/ac_controller_system/test-esp32-api.php
```

### **Step 3: Enter Configuration**

The page will have these fields pre-filled:
- **Device ID:** ESP32_OUTLET_001
- **API Key:** IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T
- **API Base URL:** (auto-detected)

**Verify these match your database!**

### **Step 4: Run Tests**

Click each test button:
1. **Test Heartbeat** - Should return `{"success": true}`
2. **Test Authorized Numbers** - Should return list of SIM numbers
3. **Test Get Status** - Should return device status
4. **Test Get Schedules** - Should return schedule list
5. **Verify Database** - Should find device in database

Or click **"Run All Tests"** to test everything at once.

---

## ✅ Expected Results

### **If Everything Works (HTTP 200):**

```json
// Heartbeat:
{
  "success": true,
  "message": "Heartbeat received",
  "control_mode": "manual",
  "server_time": "2026-07-31 10:30:00"
}

// Authorized Numbers:
{
  "success": true,
  "authorized_numbers": [],
  "count": 0,
  "last_updated": "2026-07-31 10:30:00"
}

// Status:
{
  "success": true,
  "outlet_status": "off",
  "control_mode": "manual",
  "is_online": true,
  "last_updated": "2026-07-31 10:30:00"
}
```

All tests should show **✅ SUCCESS** with green background.

---

## ❌ Common Errors

### **Error 1: HTTP 401 Unauthorized**

**Shown as:**
```json
{
  "error": "Invalid device API key"
}
```

**Causes:**
- API key mismatch between form and database
- Device API key is wrong
- `routes/api.php` not uploaded correctly

**Fix:**
1. Check Device ID and API Key match database
2. Re-upload `routes/api.php`
3. Clear browser cache and retry

---

### **Error 2: HTTP 404 Not Found**

**Shown as:**
```json
{
  "error": "Device not found"
}
```

**Causes:**
- Device doesn't exist in database
- Wrong Device ID entered

**Fix:**
1. Click "Verify Database" button
2. Check if device exists
3. Register device if missing

---

### **Error 3: Database Verification Failed**

**Shown as:**
```json
{
  "success": false,
  "error": "Device not found in database"
}
```

**Fix:**
1. Login to admin panel
2. Go to Devices → Register New Device
3. Copy generated Device ID and API Key
4. Use those in the test form

---

## 🔍 Debugging Steps

### **Step 1: Verify Database**

Click **"Verify Database"** button first.

**Expected Result:**
```json
{
  "success": true,
  "message": "Device found in database",
  "device": {
    "device_id": "ESP32_OUTLET_001",
    "api_key_preview": "IOT_OUTLET..._XJ9KP2M8N5QW7R3T",
    "is_active": true
  }
}
```

If this fails, device is not registered.

---

### **Step 2: Test Heartbeat**

Click **"Test Heartbeat"** button.

**If SUCCESS:**
- ✅ Authentication is working
- ✅ Device API key is correct
- ✅ ESP32 should work too

**If FAILED with 401:**
- ❌ API key mismatch
- ❌ Check if keys match exactly (case-sensitive!)

---

### **Step 3: Compare with ESP32**

**If test page works but ESP32 doesn't:**

Check ESP32 code has exact same values:
```cpp
const char* DEVICE_ID = "ESP32_OUTLET_001";  // Must match exactly
const char* API_KEY = "IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T";  // Must match exactly
const char* SERVER_URL = "https://isu-documentrequest.online";
const char* SERVER_PATH = "/ac_controller_system";
```

---

## 📊 Understanding Results

### **Green Box (Success):**
```
✅ SUCCESS

Request Details:
POST https://isu-documentrequest.online/ac_controller_system/api/heartbeat
Status: 200
Payload: {...}

Response:
{
  "success": true,
  ...
}
```

This means:
- Authentication passed ✅
- Request processed successfully ✅
- ESP32 should work with same credentials ✅

---

### **Red Box (Failed):**
```
❌ FAILED

Request Details:
POST https://isu-documentrequest.online/ac_controller_system/api/heartbeat
Status: 401

Response:
{
  "error": "Invalid device API key"
}
```

This means:
- Authentication failed ❌
- API key doesn't match database ❌
- ESP32 will also fail with same error ❌

---

## 🎯 What to Do Based on Results

### **Scenario 1: All Tests Pass ✅**

**Action:**
- ESP32 authentication is working correctly
- Problem might be in ESP32 code or network
- Check ESP32 Serial Monitor for exact error
- Verify ESP32 has correct WiFi credentials

---

### **Scenario 2: Heartbeat Fails (401) ❌**

**Action:**
1. Click "Verify Database" to check device exists
2. Compare API key in form with database
3. If they match but still 401:
   - Re-upload `routes/api.php` 
   - Clear PHP cache on server
   - Wait 2-3 minutes for cache to clear

---

### **Scenario 3: Database Verification Fails ❌**

**Action:**
1. Device not registered in database
2. Go to: Admin Panel → Devices → Register New Device
3. Fill in device name
4. Copy the generated Device ID and API Key
5. Use those in ESP32 code and test form

---

### **Scenario 4: All Tests Show Network Error**

**Action:**
- Check if API base URL is correct
- Verify `routes/api.php` exists on server
- Check server PHP error logs

---

## 🚀 Next Steps After Testing

### **If Test Page Works:**

1. **Copy exact values to ESP32 code:**
   ```cpp
   const char* DEVICE_ID = "ESP32_OUTLET_001";
   const char* API_KEY = "IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T";
   ```

2. **Upload to ESP32**

3. **Check Serial Monitor:**
   - Should show: `♥ Heartbeat sent successfully`
   - Should NOT show: `⚠ Heartbeat failed: HTTP 401`

---

### **If Test Page Fails:**

1. **Fix the issue on test page first**
2. **Only upload to ESP32 after test page works**
3. **This saves time - no need to keep re-uploading to ESP32**

---

## 💡 Pro Tips

1. **Always test on the web page before uploading to ESP32**
   - Faster to debug
   - Can see exact errors
   - No need to re-upload code to ESP32

2. **Keep the test page open while debugging**
   - Make changes to server
   - Click "Run All Tests"
   - See results immediately

3. **Use "Run All Tests" button**
   - Tests everything in sequence
   - Shows complete picture
   - Easier to spot patterns

4. **Copy Device ID and API Key carefully**
   - They are case-sensitive
   - No extra spaces
   - Must match database exactly

---

## 📁 Files Involved

```
Test Pages (upload to server):
- public/test-esp32-api.php       ← Main test interface
- public/test-esp32-api-db.php    ← Database verification helper

API File (already uploaded):
- routes/api.php                   ← API authentication logic

ESP32 Configuration:
- esp32/universal_outlet_controller.ino  ← Device code
```

---

## 🎉 Summary

**Before this tool:**
- Upload to ESP32 → Test → Get error → Debug → Repeat
- Takes 5+ minutes per test cycle

**With this tool:**
- Test on web page → Get instant results → Fix issue → Verify → Upload once to ESP32
- Takes 30 seconds per test cycle

**The test page simulates ESP32 exactly**, so if it works on the test page, it will work on ESP32!

---

**Access URL:**
```
https://isu-documentrequest.online/ac_controller_system/test-esp32-api.php
```

**Just upload the 2 test files and start debugging!** 🚀
