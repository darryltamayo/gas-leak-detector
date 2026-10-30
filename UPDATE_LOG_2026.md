# 📅 System Update Log - 2026

## Date: July 31, 2026
## Update Type: Year and API Key Update

---

## 🔄 Changes Made

### 1. API Key Updated
All references to the API key have been updated from:
```
OLD: IOT_OUTLET_2024_SECURE_KEY_XJ9KP2M8N5QW7R3T
NEW: IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T
```

### 2. Academic Year Updated
Project year updated from:
```
OLD: 2024-2025
NEW: 2025-2026
```

### 3. Log Timestamps Updated
Sample log entries now show 2026 dates for current year accuracy.

---

## 📁 Files Updated (Total: 21 files)

### Core Configuration Files:
1. ✅ `.env` - Main environment configuration
2. ✅ `database/complete_schema.sql` - Database schema with default device

### ESP32 Code:
3. ✅ `esp32/universal_outlet_controller.ino` - Arduino code
4. ✅ `esp32/ONLINE_SERVER_CONFIG.txt` - ESP32 setup guide

### PHP Application Files:
5. ✅ `public/cron.php` - Cron endpoint
6. ✅ `public/cron-debug.php` - Cron debugging tool
7. ✅ `public/schedule-debug.php` - Schedule debugging tool
8. ✅ `public/test-api.php` - API testing tool
9. ✅ `public/test-env.php` - Environment testing tool
10. ✅ `cron/execute_schedules.php` - Schedule execution script

### Documentation Files:
11. ✅ `README.md` - Main project README
12. ✅ `README_TEST.md` - Testing guide
13. ✅ `ONLINE_DEPLOYMENT_NOTES.md` - Deployment guide
14. ✅ `CORRECT_URLS.md` - URL reference
15. ✅ `FIX_WARNINGS_GUIDE.md` - Troubleshooting guide
16. ✅ `FINAL_DEPLOYMENT_GUIDE.md` - Deployment checklist
17. ✅ `cron/README.md` - Cron job documentation
18. ✅ `database/README.md` - Database documentation
19. ✅ `database/DATABASE_RESET_INSTRUCTIONS.txt` - Reset instructions

---

## ⚠️ IMPORTANT: Action Required!

### For Online Server (Production):

#### 1. Update Database (CRITICAL!)
The API key in your database needs to be updated manually.

**Run this SQL query in phpMyAdmin:**
```sql
-- Update API key for existing device
UPDATE iot_outlet_devices 
SET api_key = 'IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T' 
WHERE device_id = 'ESP32_OUTLET_001';

-- Verify the update
SELECT device_name, device_id, api_key 
FROM iot_outlet_devices 
WHERE device_id = 'ESP32_OUTLET_001';
```

**Expected Result:**
```
device_name: Main Outlet
device_id: ESP32_OUTLET_001
api_key: IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T
```

#### 2. Update .env File on Server
Re-upload the updated `.env` file with the new API key:
```env
API_KEY=IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T
```

#### 3. Update ESP32 Hardware (if connected)
If you have ESP32 hardware connected, update and re-upload the Arduino code with new API key:
```cpp
String API_KEY = "IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T";
```

---

## ✅ Verification Checklist

After updating, verify everything works:

### Database Verification:
- [ ] API key updated in `iot_outlet_devices` table
- [ ] Query confirms new API key is stored

### Server Verification:
- [ ] `.env` file uploaded with new API key
- [ ] Test page shows new API key working
- [ ] No authentication errors in logs

### ESP32 Verification (if applicable):
- [ ] Arduino code updated with new API key
- [ ] Code uploaded to ESP32 hardware
- [ ] Device connects successfully
- [ ] Heartbeat shows in dashboard
- [ ] No 401 Unauthorized errors

### API Testing:
- [ ] Test API endpoint: `https://isu-documentrequest.online/ac_controller_system/public/test-api-connection.php`
- [ ] Manual API call works: `https://isu-documentrequest.online/ac_controller_system/api/heartbeat`
- [ ] All API endpoints respond correctly

---

## 🔍 Testing Instructions

### Test 1: Database API Key
```sql
-- Run in phpMyAdmin
SELECT api_key FROM iot_outlet_devices WHERE device_id = 'ESP32_OUTLET_001';
-- Should show: IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T
```

### Test 2: Environment Variable
```
Open: https://isu-documentrequest.online/ac_controller_system/public/test-env.php
Check: API_KEY shows 2026 version
```

### Test 3: API Connection
```
Open: https://isu-documentrequest.online/ac_controller_system/public/test-api-connection.php
Click: Test Debug Endpoint
Result: Should show API key configuration info
```

### Test 4: Full System Test
```
Open: https://isu-documentrequest.online/ac_controller_system/public/test-system.php
Check: No API key related errors
Check: All tests passing
```

---

## 📊 Impact Analysis

### High Impact (Immediate Action Required):
✅ Database needs manual update (SQL query above)
✅ .env file needs re-upload
✅ ESP32 hardware needs code update (if connected)

### Medium Impact (Will Update Automatically):
✅ Documentation updated for future reference
✅ Test files will use new key

### Low Impact (No Action Needed):
✅ Comment updates in code
✅ Example configurations updated

---

## 🚨 Troubleshooting

### Issue: API calls return 401 Unauthorized
**Cause:** API key mismatch between .env and database

**Solution:**
1. Check `.env` has: `API_KEY=IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T`
2. Check database has same key in `iot_outlet_devices` table
3. Both must match exactly!

### Issue: ESP32 can't connect
**Cause:** Old API key in ESP32 code

**Solution:**
1. Update Arduino code with new API key
2. Re-upload to ESP32
3. Check Serial Monitor for "401 Unauthorized" errors
4. If still failing, verify key matches database

### Issue: Cron job stops working
**Cause:** Cron may be checking old API key

**Solution:**
1. Verify cron script uses: `$_ENV['API_KEY']` (dynamic)
2. Not hardcoded old key
3. Restart cron job in cPanel

---

## 📝 Notes

### Why Update the API Key?
- Keeps keys current with year for security audits
- Makes it easy to identify when keys were generated
- Best practice for key rotation

### Backwards Compatibility:
⚠️ **Breaking Change**: Old API key (2024 version) will no longer work after database update.

### If You Need to Rollback:
Run this SQL to restore old key:
```sql
UPDATE iot_outlet_devices 
SET api_key = 'IOT_OUTLET_2024_SECURE_KEY_XJ9KP2M8N5QW7R3T' 
WHERE device_id = 'ESP32_OUTLET_001';
```
And restore old .env file.

---

## 📅 Timeline

**Local Development:** ✅ Complete (All files updated)
**Online Server:** ⏳ Pending (Requires manual update)
**ESP32 Hardware:** ⏳ Pending (If applicable)

---

## 🎯 Next Steps

1. **Immediate (Critical):**
   - [ ] Run SQL update query in phpMyAdmin
   - [ ] Upload new `.env` file to server
   - [ ] Test API endpoints

2. **Soon (Important):**
   - [ ] Update ESP32 hardware if connected
   - [ ] Run full system test
   - [ ] Verify all functionality

3. **Later (Maintenance):**
   - [ ] Monitor logs for any API key errors
   - [ ] Update any backup/restore scripts
   - [ ] Document in change log

---

## ✅ Completion Checklist

Mark complete when done:

- [ ] SQL query executed in phpMyAdmin
- [ ] Database shows new API key (verified with SELECT)
- [ ] .env file uploaded to server
- [ ] Test page confirms new key working
- [ ] ESP32 code updated (if applicable)
- [ ] ESP32 connects successfully (if applicable)
- [ ] Full system test passes
- [ ] No API errors in logs
- [ ] Team notified of change (if applicable)

---

**Update performed by:** Kiro AI Assistant
**Date:** July 31, 2026
**Status:** ✅ Local files updated, ⏳ Server update pending
