# POST Request Debugging Status

## Problem Summary
ESP32 successfully connects to server and fetches authorized numbers (GET request), but heartbeat POST requests fail with HTTP 401 Unauthorized.

## What We Know
✅ **Working**:
- WiFi connection
- Server connectivity
- GET requests (`/api/test-heartbeat` with query params works)
- Authorized number fetch (GET request) works
- Database queries work
- Device authentication with GET parameters works

❌ **Not Working**:
- POST requests to `/api/heartbeat` return HTTP 401
- POST body may not be reaching authentication logic

## Root Cause Analysis

### Possible Issues:
1. **POST data not being read**: `php://input` may be empty
2. **JSON not being parsed**: Content-Type or encoding issue
3. **API key comparison failing**: Despite correct key in database
4. **Routing issue**: `.htaccess` or routing may strip POST data
5. **ESP32 HTTP library issue**: May not be sending data correctly

## Diagnostic Tools Created

### 1. Interactive Browser Test Suite
**File**: `public/test-heartbeat-post.html`  
**Access**: https://isu-documentrequest.online/ac_controller_system/public/test-heartbeat-post.html

**Purpose**: Test POST requests from browser to isolate server vs ESP32 issues

**Tests**:
- ✅ POST to `/api/heartbeat` (main test)
- ✅ GET to `/api/test-heartbeat` (working comparison)
- ✅ POST to direct PHP file (bypass routing)
- ✅ Wrong API key test (verify auth is checking)

### 2. API Echo Diagnostic
**File**: `public/api-echo.php`  
**Access**: https://isu-documentrequest.online/ac_controller_system/public/api-echo.php

**Purpose**: Show exactly what server receives in POST request

**Browser Console Test**:
```javascript
fetch('https://isu-documentrequest.online/ac_controller_system/public/api-echo.php', {
  method: 'POST',
  headers: {'Content-Type': 'application/json'},
  body: JSON.stringify({
    device_id: 'ESP32_OUTLET_001',
    api_key: 'IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T'
  })
}).then(r=>r.json()).then(console.log)
```

### 3. Direct POST Test
**File**: `public/direct-post-test.php`  
**Purpose**: Test POST data handling without API routing

### 4. Enhanced Server Logging
**File**: `routes/api.php` (updated)

**Added Logging**:
- Request method
- Content-Type header
- Content-Length
- Raw POST body
- JSON decode status
- Extracted device_id and api_key

**View Logs**: cPanel → Error Logs (look for `[Heartbeat]` prefix)

## Testing Instructions

### Quick Test (2 minutes)
1. Open: https://isu-documentrequest.online/ac_controller_system/public/test-heartbeat-post.html
2. Click "Test Heartbeat POST"
3. Check result:
   - **Green (200)**: Server POST works! Issue is ESP32-specific
   - **Red (401)**: Server POST broken. Check logs.

### Detailed Test (5 minutes)
1. Run all 4 tests in test-heartbeat-post.html
2. Open browser console (F12)
3. Run api-echo.php test (copy/paste JavaScript above)
4. Check server error logs in cPanel
5. Document all results

### ESP32 Test
1. Upload code to ESP32
2. Watch Serial Monitor
3. Note exact HTTP error code
4. Check if it's consistent (always 401) or intermittent

## Expected Outcomes

### Scenario A: Browser test succeeds (200 OK)
**Meaning**: Server is working correctly  
**Issue**: ESP32 code or network  
**Next Step**: Check ESP32 Serial Monitor for details, verify API key in ESP32 code

### Scenario B: Browser test fails (401)
**Meaning**: Server POST handling broken  
**Issue**: Routing, PHP configuration, or authentication logic  
**Next Step**: Check server error logs, review routing code

### Scenario C: api-echo.php shows empty POST data
**Meaning**: Server not receiving POST body  
**Issue**: Likely `.htaccess` or PHP configuration  
**Next Step**: Check `.htaccess` rewrite rules, verify `php://input` is accessible

## Next Steps

### For User:
1. ✅ Upload files to server
2. ✅ Run test-heartbeat-post.html
3. ✅ Report results from all 4 tests
4. ✅ Check server error logs (cPanel)
5. ✅ Run ESP32 and report Serial Monitor output

### For Developer (based on results):
- If browser test works → Debug ESP32 code
- If browser test fails → Debug server configuration
- If logs show empty POST → Fix routing/PHP config
- If logs show data but auth fails → Fix authentication logic

## Files Changed/Created

### New Files:
- ✅ `public/test-heartbeat-post.html` - Interactive test suite
- ✅ `public/direct-post-test.php` - Direct POST test
- ✅ `public/api-echo.php` - POST data diagnostic
- ✅ `TESTING_GUIDE.md` - Testing instructions
- ✅ `POST_DEBUGGING_STATUS.md` - This file

### Modified Files:
- ✅ `routes/api.php` - Enhanced logging in heartbeat() function

## Configuration

**Server**: https://isu-documentrequest.online/ac_controller_system  
**Device ID**: ESP32_OUTLET_001  
**API Key**: IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T  
**Database**: u907280979_docrequest  
**Authorized SIM**: 09554778665  
**Device SIM**: 09605682714  

## Important Notes

1. **GET requests work**: This proves connectivity and authentication logic work
2. **POST requests fail**: This suggests POST-specific issue
3. **Same credentials**: GET test-heartbeat works with same device_id and api_key
4. **Conclusion**: Issue is likely in POST body handling, not authentication logic itself

The browser tests will definitively show whether this is a server issue or ESP32 issue.
