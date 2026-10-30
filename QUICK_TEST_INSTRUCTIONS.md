# 🧪 Quick Test Instructions - POST Request Issue

## The Problem
Your ESP32 shows: `⚠ Heartbeat failed: HTTP 401`

But GET requests work fine (authorized numbers are fetched successfully).

## Solution: Run These Tests

### Test 1: Browser Test (MOST IMPORTANT) ⭐

1. **Upload these files to your server first:**
   - `public/test-heartbeat-post.html`
   - `public/api-echo.php`
   - `public/direct-post-test.php`
   - `routes/api.php` (updated with enhanced logging)

2. **Open this URL in your browser:**
   ```
   https://isu-documentrequest.online/ac_controller_system/public/test-heartbeat-post.html
   ```

3. **Click the "Test Heartbeat POST" button**

4. **Report the result:**
   - If you see green background with `"success": true` → **SERVER IS WORKING!** Issue is in ESP32
   - If you see red background with HTTP 401 → **SERVER ISSUE** - proceed to Test 2

### Test 2: Echo Test (If Test 1 failed)

1. **Open browser console** (Press F12, click "Console" tab)

2. **Copy and paste this code, then press Enter:**
   ```javascript
   fetch('https://isu-documentrequest.online/ac_controller_system/public/api-echo.php', {
     method: 'POST',
     headers: {'Content-Type': 'application/json'},
     body: JSON.stringify({
       device_id: 'ESP32_OUTLET_001',
       api_key: 'IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T'
     })
   }).then(r=>r.json()).then(d=>console.log(JSON.stringify(d, null, 2)))
   ```

3. **Check the output:**
   - Look for `"verdict"` field
   - If it says `"✅ POST data received correctly!"` → POST is working
   - If it says `"❌ POST data NOT received correctly"` → POST is broken

### Test 3: Check Server Logs

1. **Go to cPanel**
2. **Click "Error Logs"**
3. **Look for lines starting with `[Heartbeat]`**
4. **Copy and send me the log lines**

## What To Report Back

Just tell me:

1. **Test 1 result**: Green (success) or Red (failed) + what the message says
2. **Test 2 result** (if Test 1 failed): Copy the "verdict" line
3. **ESP32 Serial Monitor**: Copy the last few lines showing the error

## Quick FAQ

**Q: Do I need to do all tests?**  
A: No! If Test 1 shows green, just tell me and we'll fix the ESP32 code.

**Q: The test-heartbeat-post.html shows 404**  
A: Make sure you uploaded it to the `public` folder and access it via `/public/test-heartbeat-post.html`

**Q: All tests show 401**  
A: Check the error logs in cPanel - they will tell us exactly what's wrong

**Q: Test 1 works but ESP32 still fails**  
A: The issue is in the ESP32 code. Check if the API key is exactly the same (including no extra spaces or line breaks)

## Expected Timeline

- ⏱️ Upload files: 2 minutes
- ⏱️ Run Test 1: 30 seconds
- ⏱️ Run Test 2 (if needed): 1 minute
- ⏱️ Check logs: 1 minute

**Total: About 5 minutes to diagnose the issue completely!**

---

## After Testing

Once you send me the results, I'll know exactly:
- ✅ Is the server working? (Test 1 result)
- ✅ Is POST data arriving? (Test 2 result)
- ✅ What's the exact error? (Server logs)

Then I can give you the exact fix needed! 🎯
