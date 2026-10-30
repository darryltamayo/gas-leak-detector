# Dashboard Control & SMS Fixes

## ✅ HEARTBEAT FIXED!
```
♥ Heartbeat sent successfully | Mode: manual
♥ Heartbeat sent successfully | Mode: manual
```

Great! Now let's fix dashboard control and SMS.

---

## Problem 1: Dashboard Control Not Working ❌

### Root Cause:
ESP32 `checkCommands()` function was looking for wrong data structure:
- Expected: `{has_commands: true, commands: [{action: "turn_on"}]}`
- Server returns: `{success: true, outlet_status: "on", control_mode: "manual"}`

### Fix Applied ✅:
Updated `checkCommands()` to read `outlet_status` directly from `/api/status` endpoint.

**File**: `esp32/universal_outlet_controller.ino`  
**Function**: `checkCommands()` (Line ~670)

**How it works now**:
1. ESP32 checks `/api/status?device_id=ESP32_OUTLET_001&api_key=...` every 5 seconds
2. Server returns current `outlet_status` from database ("on" or "off")
3. ESP32 compares with current relay state
4. If different, ESP32 changes relay to match server
5. Dashboard updates database → ESP32 detects change → Relay switches ✅

---

## Problem 2: SMS Control Not Working ❌

### Diagnostic Questions:

#### 1. Is GSM module initialized?
**Check Serial Monitor for:**
```
✓ GSM module responded
✓ GSM module configured for SMS
```

If you see `✗ GSM module not responding`, the GSM module isn't working.

#### 2. Is authorized number loaded?
**Check Serial Monitor for:**
```
✓ Loaded 1 authorized number(s) - 09554778665
```

If it says `✓ Loaded 0 authorized number(s)`, the number isn't in the database.

#### 3. Is SMS being received?
**When you send SMS, Serial Monitor should show:**
```
📱 New SMS received!
From: +639554778665
Message: ON
→ Command: Turn ON outlet
📤 Sending SMS to +639554778665
✓ SMS sent
```

If nothing appears, GSM module isn't receiving SMS.

---

## Testing Steps

### Step 1: Upload Fixed ESP32 Code ✅

1. Open Arduino IDE
2. Open `esp32/universal_outlet_controller.ino`
3. Click **Upload**
4. Wait for "Done uploading"

### Step 2: Test Dashboard Control

1. Open Serial Monitor (115200 baud)
2. Press ESP32 Reset button
3. Wait for heartbeat messages
4. Open website dashboard
5. Click **Turn ON** button
6. **Watch Serial Monitor** - should show within 5 seconds:
   ```
   → Command from server: Turn ON
   ```

7. Click **Turn OFF** button
8. **Watch Serial Monitor** - should show:
   ```
   → Command from server: Turn OFF
   ```

**Expected Result**: Outlet should turn ON/OFF when you click dashboard buttons! ✅

### Step 3: Test SMS Control

#### Check GSM Status:
Look for these lines in Serial Monitor after reset:
```
✓ GSM module responded
✓ GSM module configured for SMS
✓ Loaded 1 authorized number(s) - 09554778665
```

#### Send Test SMS:
From phone number **09554778665**, send SMS to ESP32 SIM (**09605682714**):

**SMS Commands:**
- `ON` - Turn outlet ON
- `OFF` - Turn outlet OFF
- `STATUS` - Check outlet status
- `TOGGLE` - Toggle outlet state

**Expected Response**: You should receive SMS reply confirming action.

**Watch Serial Monitor** for:
```
📱 New SMS received!
From: +639554778665
Message: ON
→ Command: Turn ON outlet
📤 Sending SMS to +639554778665
✓ SMS sent
```

---

## Common Issues & Solutions

### Issue 1: Dashboard control doesn't work

**Symptoms**: Click button, nothing happens on ESP32

**Check**:
1. Is heartbeat working? (Should see `♥ Heartbeat sent successfully`)
2. Is device shown as "Online" on dashboard?
3. Open browser console (F12) - any errors?

**Solution**: 
- Make sure you uploaded the fixed code
- Check if ESP32 shows: `→ Command from server: Turn ON/OFF`
- If not appearing, check `/api/status` endpoint manually

### Issue 2: SMS not working

**Symptoms**: Send SMS, no response

**Possible Causes**:

#### A. GSM module not initialized
**Serial Monitor shows**: `✗ GSM module not responding`

**Solutions**:
- Check GSM900A wiring (RX→GPIO16, TX→GPIO17)
- Check GSM power (needs 5V 2A)
- Check antenna is connected
- Check SIM card is inserted correctly
- Check SIM card PIN is disabled

#### B. Number not authorized
**Serial Monitor shows**: `✓ Loaded 0 authorized number(s)`

**Solution**: Number 09554778665 should already be in database. Check database:
```sql
SELECT * FROM iot_outlet_authorized_sims;
```

#### C. SMS format not recognized
**Serial Monitor shows**: SMS received but no command executed

**Solution**: 
- SMS must be in UPPERCASE: `ON`, `OFF`, `STATUS`
- No extra spaces or characters
- Just the command word

#### D. GSM not receiving SMS
**Serial Monitor shows**: Nothing when you send SMS

**Solutions**:
- Check SIM card has signal
- Check SIM card can receive SMS (test with another phone first)
- Check AT+CMGF=1 command succeeded (should show in setup)
- Try sending `AT+CMGL="ALL"` via Serial Monitor to list SMS

---

## How It Works Now

### Dashboard Control Flow:
```
User clicks ON/OFF
    ↓
OutletController updates database (outlet_status = "on")
    ↓
ESP32 checks /api/status every 5 seconds
    ↓
ESP32 reads outlet_status = "on"
    ↓
ESP32 compares with current state (was "off")
    ↓
ESP32 switches relay to ON
    ↓
Serial Monitor: "→ Command from server: Turn ON"
```

### SMS Control Flow:
```
User sends "ON" SMS to 09605682714
    ↓
GSM900A receives SMS
    ↓
ESP32 reads SMS via checkSMS()
    ↓
ESP32 validates sender (09554778665) is authorized
    ↓
ESP32 processes command "ON"
    ↓
ESP32 switches relay to ON
    ↓
ESP32 sends confirmation SMS back
    ↓
Serial Monitor: "📱 New SMS received! → Command: Turn ON outlet"
```

---

## What Changed in Code

### File: `esp32/universal_outlet_controller.ino`

**Function `checkCommands()` - Line ~670:**

**BEFORE (❌ Wrong)**:
```cpp
if (hasCommands) {
  JsonArray commands = doc["commands"];
  for (JsonObject cmd : commands) {
    String action = cmd["action"];
    // Process action...
  }
}
```

**AFTER (✅ Correct)**:
```cpp
String desiredStatus = doc["outlet_status"] | "off";
bool desiredState = (desiredStatus == "on");

if (desiredState != outletState) {
  Serial.print("→ Command from server: Turn ");
  Serial.println(desiredState ? "ON" : "OFF");
  setOutlet(desiredState);
  blinkLED(2, 100);
}
```

---

## Expected Serial Monitor Output

### After Upload:
```
========================================================================
     Universal IoT Outlet Controller with GSM900A
========================================================================
Device ID: ESP32_OUTLET_001
Number of Outlets: 1
========================================================================

✓ GSM module responded
✓ GSM module configured for SMS
Connecting to WiFi: Queenie225  2.4G ...
✓ WiFi connected!
IP Address: 192.168.x.x
Signal Strength: -55 dBm
📋 Fetching authorized phone numbers from server...
✓ Loaded 1 authorized number(s) - 09554778665
========================================================================
DEVICE STATUS
========================================================================
Device ID: ESP32_OUTLET_001
WiFi: ✓ Connected
IP: 192.168.x.x
GSM: ✓ Ready
Control Mode: manual
Outlet Status: OFF
========================================================================
✓ Setup complete! Device is ready.
♥ Heartbeat sent successfully | Mode: manual
```

### When Dashboard Button Clicked:
```
→ Command from server: Turn ON
```

### When SMS Received:
```
📱 New SMS received!
From: +639554778665
Message: ON
→ Command: Turn ON outlet
📤 Sending SMS to +639554778665
✓ SMS sent
```

---

## Summary

| Feature | Status | Notes |
|---------|--------|-------|
| Heartbeat | ✅ Working | Fixed endpoint issue |
| Dashboard Control | ✅ Fixed | Updated checkCommands() to read outlet_status |
| SMS Control | ⏳ Should work | Code is correct, need to test GSM module |
| Authorized Numbers | ✅ Working | Loads from database |
| WiFi Connection | ✅ Working | Connects successfully |

**Next**: Upload code and test! 🚀
