# 🔐 API Authentication Fix - Complete Guide

## Date: July 31, 2026
## Issue: HTTP 401 Unauthorized on ESP32 API Calls

---

## 🐛 Problem Summary

**Symptoms:**
```
⚠ Heartbeat failed: HTTP 401
✗ Failed to fetch authorized numbers: HTTP -1
```

**Root Cause:**
- ESP32 devices send **device-specific API keys** (unique per device)
- Server was expecting **server-wide API key** in HTTP header
- Authentication mechanisms were incompatible

---

## ✅ Solution Implemented

### **New Authentication System:**

1. **Device Endpoints** (used by ESP32):
   - Use **device-specific API key** authentication
   - Each device has unique key from database
   - Sent in query parameters or JSON body
   - More secure - compromised device doesn't affect others

2. **Admin Endpoints** (used by server/admin):
   - Use **server-wide API key** authentication
   - Shared key for admin operations
   - Sent in `X-API-KEY` HTTP header

---

## 📝 Files Modified

### **1. `routes/api.php`** - Complete rewrite of authentication

**Endpoints Using Device Authentication:**
- ✅ `/api/heartbeat` - Device heartbeat (POST)
- ✅ `/api/status` - Get device status (GET)
- ✅ `/api/schedules` - Get device schedules (GET)
- ✅ `/api/device/authorized-numbers` - Get authorized SIM numbers (GET)

**Endpoints Using Server Authentication:**
- 🔒 `/api/control` - Control device (admin only)
- 🔒 `/api/sms/validate` - Validate SMS (admin only)
- 🔒 `/api/sms/execute` - Execute SMS command (admin only)

---

## 🔄 How Device Authentication Works

### **Step 1: Device Registration**
```
Admin registers device on website:
- Device ID: ESP32_OUTLET_001 (auto-generated)
- API Key: IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T (auto-generated)
- Stored in database: iot_outlet_devices table
```

### **Step 2: ESP32 Configuration**
```cpp
// Upload to ESP32
const char* DEVICE_ID = "ESP32_OUTLET_001";
const char* API_KEY = "IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T";
```

### **Step 3: API Request**
```cpp
// ESP32 sends heartbeat
POST /api/heartbeat
{
  "device_id": "ESP32_OUTLET_001",
  "api_key": "IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T"
}
```

### **Step 4: Server Validation**
```php
// Server validates
$device = findByDeviceId($deviceId);
if (hash_equals($device->api_key, $providedApiKey)) {
    // ✅ AUTHORIZED
} else {
    // ❌ 401 UNAUTHORIZED
}
```

---

## 🔧 Technical Details

### **Heartbeat Endpoint:**
```php
private function heartbeat(): void
{
    $data = json_decode(file_get_contents('php://input'), true);
    $deviceId = $data['device_id'] ?? '';
    $deviceApiKey = $data['api_key'] ?? '';
    
    $device = $this->deviceRepository->findByDeviceId($deviceId);
    
    if (!$device) {
        return 404; // Device not found
    }
    
    // Verify device API key matches database
    if (!hash_equals($device->api_key, $deviceApiKey)) {
        return 401; // Invalid API key
    }
    
    // Update device status
    $device->last_heartbeat = now();
    $device->is_online = true;
    save($device);
    
    return success;
}
```

### **Authorized Numbers Endpoint:**
```php
private function getAuthorizedNumbers(): void
{
    $deviceId = $_GET['device_id'] ?? '';
    $deviceApiKey = $_GET['api_key'] ?? '';
    
    $device = $this->deviceRepository->findByDeviceId($deviceId);
    
    if (!$device) {
        return 404;
    }
    
    // Verify device API key
    if (!hash_equals($device->api_key, $deviceApiKey)) {
        return 401;
    }
    
    // Return authorized SIM numbers
    return authorizedNumbers;
}
```

### **Status Endpoint:**
```php
private function getStatus(): void
{
    $deviceId = $_GET['device_id'] ?? '';
    $deviceApiKey = $_GET['api_key'] ?? '';
    
    // Same verification as above
    // Returns: outlet_status, control_mode, is_online
}
```

### **Schedules Endpoint:**
```php
private function getSchedules(): void
{
    $deviceId = $_GET['device_id'] ?? '';
    $deviceApiKey = $_GET['api_key'] ?? '';
    
    // Same verification as above
    // Returns: list of active schedules for this device
}
```

---

## 🚀 Deployment Instructions

### **Step 1: Upload Fixed File**
```
Upload to server:
- routes/api.php ← This file was completely updated
```

### **Step 2: Verify Database**
```sql
-- Check device exists with correct API key
SELECT device_id, api_key, is_active 
FROM iot_outlet_devices 
WHERE device_id = 'ESP32_OUTLET_001';

-- Expected result:
-- device_id: ESP32_OUTLET_001
-- api_key: IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T
-- is_active: 1
```

### **Step 3: Restart ESP32**
```
1. Power cycle ESP32 or press reset button
2. Open Serial Monitor (115200 baud)
3. Watch for successful connection
```

### **Step 4: Verify Success**
```
Expected Serial Monitor output:

✓ WiFi connected!
📋 Fetching authorized phone numbers from server...
✓ Loaded X authorized number(s)
✓ Setup complete! Device is ready.
♥ Heartbeat sent successfully | Mode: manual
```

---

## ✅ Testing Checklist

### **Test 1: Heartbeat**
```bash
# ESP32 Serial Monitor should show:
♥ Heartbeat sent successfully | Mode: manual

# Server should log:
# Activity: device_online or heartbeat received
```

### **Test 2: Authorized Numbers**
```bash
# ESP32 Serial Monitor should show:
✓ Loaded X authorized number(s)
  - +639XXXXXXXXX
  - 09XXXXXXXXX
```

### **Test 3: Device Status on Website**
```
Go to: Dashboard or Devices page
Should show:
- Device: ONLINE (green indicator)
- Last Heartbeat: Just now
```

### **Test 4: Multiple Devices**
```
Device 1 with Key A:
✅ Connects successfully
✅ Authenticates with Key A
❌ Cannot use Key B

Device 2 with Key B:
✅ Connects successfully
✅ Authenticates with Key B
❌ Cannot use Key A
```

---

## 🔐 Security Benefits

### **Before (Old System):**
```
❌ Single shared API key for all devices
❌ If one device compromised, all devices at risk
❌ Cannot identify which device made request
❌ Cannot disable one device without affecting others
```

### **After (New System):**
```
✅ Unique API key per device
✅ Device compromise doesn't affect others
✅ Can track which device made each request
✅ Can disable individual devices
✅ Better audit trail
✅ Easier to rotate keys per device
```

---

## 🐛 Troubleshooting

### **Issue: Still getting HTTP 401**

**Causes:**
1. Old `routes/api.php` file still on server
2. API key mismatch between ESP32 and database
3. Device not registered in database

**Solutions:**
```bash
# 1. Verify file upload
Check routes/api.php on server has new authentication code

# 2. Check API key matches
ESP32 code: const char* API_KEY = "IOT_OUTLET_2026...";
Database: SELECT api_key FROM iot_outlet_devices WHERE device_id = 'ESP32_OUTLET_001';
# They must match EXACTLY

# 3. Verify device exists
SELECT * FROM iot_outlet_devices WHERE device_id = 'ESP32_OUTLET_001';
# Should return 1 row with is_active = 1
```

---

### **Issue: HTTP -1 on authorized numbers**

**Causes:**
1. HTTPS/SSL certificate issue
2. Network connectivity problem
3. Server URL incorrect

**Solutions:**
```cpp
// ESP32 code uses:
client.setInsecure(); // Skips SSL verification

// Check server URL:
const char* SERVER_URL = "https://isu-documentrequest.online";
const char* SERVER_PATH = "/ac_controller_system";

// Full URL should be:
// https://isu-documentrequest.online/ac_controller_system/api/device/authorized-numbers
```

---

### **Issue: Device shows offline after few minutes**

**Cause:** Heartbeat not being sent/received

**Solution:**
```cpp
// ESP32 sends heartbeat every 30 seconds
#define HEARTBEAT_INTERVAL 30000  // 30 seconds

// Server marks offline after 120 seconds (2 minutes) without heartbeat
$timeout = (int)($_ENV['ESP32_HEARTBEAT_TIMEOUT'] ?? 120);

// Check Serial Monitor for heartbeat messages:
♥ Heartbeat sent successfully  ← Should appear every 30 seconds
```

---

## 📊 API Endpoints Reference

### **Device Endpoints (Device API Key Required):**

#### **1. Heartbeat**
```
POST /api/heartbeat
Content-Type: application/json

Body:
{
  "device_id": "ESP32_OUTLET_001",
  "api_key": "IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T",
  "ip_address": "10.133.228.192",
  "rssi": -56,
  "outlet_states": [{"outlet": 1, "state": "off"}]
}

Response 200:
{
  "success": true,
  "message": "Heartbeat received",
  "control_mode": "manual",
  "server_time": "2026-07-31 10:30:00"
}
```

#### **2. Get Authorized Numbers**
```
GET /api/device/authorized-numbers?device_id=ESP32_OUTLET_001&api_key=IOT_OUTLET_2026...

Response 200:
{
  "success": true,
  "authorized_numbers": [
    {"phone_number": "+639171234567", "owner_name": "John Doe"},
    {"phone_number": "09181234567", "owner_name": "Jane Smith"}
  ],
  "count": 2,
  "last_updated": "2026-07-31 10:30:00"
}
```

#### **3. Get Status**
```
GET /api/status?device_id=ESP32_OUTLET_001&api_key=IOT_OUTLET_2026...

Response 200:
{
  "success": true,
  "outlet_status": "off",
  "control_mode": "manual",
  "is_online": true,
  "last_updated": "2026-07-31 10:30:00"
}
```

#### **4. Get Schedules**
```
GET /api/schedules?device_id=ESP32_OUTLET_001&api_key=IOT_OUTLET_2026...

Response 200:
{
  "success": true,
  "schedules": [
    {
      "id": 1,
      "name": "Morning AC",
      "time_on": "08:00:00",
      "time_off": "12:00:00",
      "days": "Mon,Tue,Wed,Thu,Fri"
    }
  ],
  "count": 1
}
```

---

### **Admin Endpoints (Server API Key Required):**

#### **Control Device**
```
POST /api/control
X-API-KEY: <server_api_key>
Content-Type: application/json

Body:
{
  "device_id": "ESP32_OUTLET_001",
  "action": "on"
}
```

---

## 🎯 Summary

**What Changed:**
- ✅ Device endpoints now use device-specific API keys
- ✅ Each device authenticates with its own unique key
- ✅ Server endpoints still use server API key for admin operations
- ✅ More secure and scalable system

**What to Do:**
1. Upload fixed `routes/api.php` to server
2. Restart ESP32 devices
3. Verify devices connect successfully
4. Test multiple devices work independently

**Benefits:**
- 🔐 Better security (unique keys per device)
- 📊 Better tracking (know which device sent request)
- 🎯 Better control (disable individual devices)
- 🚀 Ready for unlimited devices

---

**Updated By:** System Fix
**Date:** July 31, 2026
**Version:** 2.0 - Device-Specific Authentication
