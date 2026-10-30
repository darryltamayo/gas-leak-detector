# ✅ CORRECT URLS - Quick Reference

## 🎯 Your System URLs

### Installation Details
- **Server:** isu-documentrequest.online
- **Installation Path:** `/ac_controller_system`
- **Full Base URL:** `https://isu-documentrequest.online/ac_controller_system`

---

## 🌐 Web Application URLs

### Main Pages
```
Home/Login:
https://isu-documentrequest.online/ac_controller_system/

Dashboard (after login):
https://isu-documentrequest.online/ac_controller_system/dashboard

Outlet Control:
https://isu-documentrequest.online/ac_controller_system/outlet

Schedules:
https://isu-documentrequest.online/ac_controller_system/schedule

Profile:
https://isu-documentrequest.online/ac_controller_system/profile
```

### Admin Pages (admin only)
```
Users Management:
https://isu-documentrequest.online/ac_controller_system/admin/users

Devices Management:
https://isu-documentrequest.online/ac_controller_system/device

SIM Cards Management:
https://isu-documentrequest.online/ac_controller_system/sim

Activity Logs:
https://isu-documentrequest.online/ac_controller_system/admin/activity-logs
```

### Testing Tools
```
Full System Test:
https://isu-documentrequest.online/ac_controller_system/public/test-system.php

API Connection Test:
https://isu-documentrequest.online/ac_controller_system/public/test-api-connection.php
```

---

## 🔌 API Endpoints (for ESP32)

### Base Configuration
```
Server Domain: isu-documentrequest.online
Base Path: /ac_controller_system
Port: 443 (HTTPS)
```

### API URLs
```
Heartbeat:
https://isu-documentrequest.online/ac_controller_system/api/heartbeat

Device Status:
https://isu-documentrequest.online/ac_controller_system/api/status

Control:
https://isu-documentrequest.online/ac_controller_system/api/control

Schedules:
https://isu-documentrequest.online/ac_controller_system/api/schedules

Authorized Numbers:
https://isu-documentrequest.online/ac_controller_system/api/device/authorized-numbers

SMS Validate:
https://isu-documentrequest.online/ac_controller_system/api/sms/validate

SMS Execute:
https://isu-documentrequest.online/ac_controller_system/api/sms/execute
```

---

## ⚙️ Configuration Files

### .env File (APP_URL)
```env
APP_URL=https://isu-documentrequest.online/ac_controller_system
```

### ESP32 Arduino Code
```cpp
// Server Configuration
const char* serverUrl = "isu-documentrequest.online";
const int serverPort = 443;
const bool useHTTPS = true;

// IMPORTANT: Base path for subdirectory installation
const char* basePath = "/ac_controller_system";

// Then in your HTTP requests:
String endpoint = String(basePath) + "/api/heartbeat";
```

---

## 🚫 Wrong URLs (Don't Use These)

❌ `https://isu-documentrequest.online/dashboard`
✅ `https://isu-documentrequest.online/ac_controller_system/dashboard`

❌ `https://isu-documentrequest.online/public/test-system.php`
✅ `https://isu-documentrequest.online/ac_controller_system/public/test-system.php`

❌ `https://isu-documentrequest.online/api/heartbeat`
✅ `https://isu-documentrequest.online/ac_controller_system/api/heartbeat`

---

## 🔐 Login Credentials

### Default Admin Account
```
URL: https://isu-documentrequest.online/ac_controller_system/auth/login
Username: admin
Password: password
```

**⚠️ IMPORTANT:** Change password immediately after first login!

---

## 📁 Server File Structure

Your files are located at:
```
/home/u907280979/domains/isu-documentrequest.online/public_html/ac_controller_system/
```

Directory structure:
```
ac_controller_system/
├── app/
├── config/
├── cron/
├── database/
├── esp32/
├── public/
├── routes/
├── storage/
├── .env          ← Make sure APP_URL includes /ac_controller_system
├── .htaccess
└── index.php
```

---

## 🧪 Quick Tests

### Test 1: Check if site is accessible
```
Open: https://isu-documentrequest.online/ac_controller_system/
Expected: See login page
```

### Test 2: Run system diagnostics
```
Open: https://isu-documentrequest.online/ac_controller_system/public/test-system.php
Expected: See test results page
```

### Test 3: Test API
```
Open: https://isu-documentrequest.online/ac_controller_system/public/test-api-connection.php
Expected: See API test page
```

### Test 4: Manual API call
```
Open: https://isu-documentrequest.online/ac_controller_system/api/heartbeat
Expected: JSON response (even if error, shows API is working)
```

---

## 🔧 Troubleshooting

### Issue: Redirects to wrong URL (missing /ac_controller_system)
**Solution:** 
1. Check `.env` file has: `APP_URL=https://isu-documentrequest.online/ac_controller_system`
2. Re-upload `.env` to server
3. Clear browser cache

### Issue: 404 Not Found on all pages
**Solution:**
1. Verify `.htaccess` is uploaded
2. Check if mod_rewrite is enabled on server
3. Contact hosting support

### Issue: Can't access subdirectory pages
**Solution:**
- Always include `/ac_controller_system` in the URL
- Use correct URL format shown above

---

## 📱 ESP32 Configuration

When updating your ESP32 code, use:

```cpp
const char* ssid = "YOUR_WIFI_NAME";
const char* password = "YOUR_WIFI_PASSWORD";

const char* serverUrl = "isu-documentrequest.online";
const int serverPort = 443;
const bool useHTTPS = true;

// CRITICAL: Include the base path
const char* basePath = "/ac_controller_system";

String API_KEY = "IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T";
String DEVICE_ID = "ESP32_OUTLET_001";

// In your requests:
String heartbeatUrl = String(basePath) + "/api/heartbeat";
String statusUrl = String(basePath) + "/api/status";
String controlUrl = String(basePath) + "/api/control";
```

---

## 📋 Checklist

After uploading `.env` with correct URL:

- [ ] Can access: `https://isu-documentrequest.online/ac_controller_system/`
- [ ] Can login with admin credentials
- [ ] Dashboard shows: `https://isu-documentrequest.online/ac_controller_system/dashboard`
- [ ] All menu links work correctly
- [ ] Test system shows no APP_URL mismatch warning
- [ ] API test page loads
- [ ] ESP32 can connect (if hardware available)

---

## 🎉 Quick Actions

**Bookmark These URLs:**
1. Login: https://isu-documentrequest.online/ac_controller_system/auth/login
2. Dashboard: https://isu-documentrequest.online/ac_controller_system/dashboard
3. Test System: https://isu-documentrequest.online/ac_controller_system/public/test-system.php

**Share With Team:**
- Main URL: https://isu-documentrequest.online/ac_controller_system/
- Everyone gets their own account (admin creates users)
- Default admin can create additional users

---

**✅ The key is: ALWAYS include `/ac_controller_system` in your URLs!**
