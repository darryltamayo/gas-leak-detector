# Relay Polarity Fix - ACTIVE LOW vs ACTIVE HIGH

## Problem

The relay was operating **OPPOSITE** to the dashboard:
- Dashboard shows "ON" → Relay is physically OFF ❌
- Dashboard shows "OFF" → Relay is physically ON ❌

## Root Cause

Most relay modules are **ACTIVE LOW**, meaning:
- `LOW` signal = Relay ON (energized, closes circuit) ⚡
- `HIGH` signal = Relay OFF (de-energized, opens circuit) 🔌

But the code was written for **ACTIVE HIGH**:
- `HIGH` signal = Relay ON ❌
- `LOW` signal = Relay OFF ❌

## Solution Applied ✅

### Files Changed: `esp32/universal_outlet_controller.ino`

### 1. Fixed `setupPins()` - Initial State
**Before (WRONG)**:
```cpp
digitalWrite(RELAY_PIN, LOW);  // Thought this was OFF, but it's ON!
outletState = false;
```

**After (CORRECT)**:
```cpp
digitalWrite(RELAY_PIN, HIGH);  // HIGH = OFF for ACTIVE LOW relay
outletState = false;
```

### 2. Fixed `setOutlet(bool state)` - Main Control
**Before (WRONG)**:
```cpp
digitalWrite(RELAY_PIN, state ? HIGH : LOW);
// state=true → HIGH → Should be ON but was OFF
// state=false → LOW → Should be OFF but was ON
```

**After (CORRECT)**:
```cpp
digitalWrite(RELAY_PIN, state ? LOW : HIGH);
// state=true → LOW → Relay ON ✅
// state=false → HIGH → Relay OFF ✅
```

### 3. Fixed `setOutlet(int outletNum, bool state)` - Legacy Function
**Before (WRONG)**:
```cpp
digitalWrite(RELAY_PIN, state ? HIGH : LOW);
```

**After (CORRECT)**:
```cpp
digitalWrite(RELAY_PIN, state ? LOW : HIGH);
```

## How It Works Now

### ACTIVE LOW Relay Logic:

| Dashboard | state | digitalWrite | Relay State | Physical |
|-----------|-------|--------------|-------------|----------|
| ON | true | LOW | Energized | ✅ ON |
| OFF | false | HIGH | De-energized | ✅ OFF |

### Code Flow:
```
Dashboard: Click "Turn ON"
    ↓
Database: outlet_status = "on"
    ↓
ESP32: Reads status "on"
    ↓
ESP32: state = true
    ↓
ESP32: digitalWrite(RELAY_PIN, LOW)  ← LOW = ON for ACTIVE LOW
    ↓
Relay: Energized, circuit closed
    ↓
Outlet: Actually ON ✅
```

## Testing

### Test 1: Initial State
1. Upload code to ESP32
2. Press Reset
3. **Expected**: Relay should be OFF (dashboard shows OFF)
4. **Verify**: Listen for relay click (should not click on reset)

### Test 2: Turn ON from Dashboard
1. Open dashboard
2. Click "Turn ON" button
3. **Expected**: 
   - Dashboard shows "ON" ✅
   - Relay clicks and turns ON ✅
   - Serial Monitor: "→ Command from server: Turn ON"
   - Serial Monitor: "Outlet → ON ⚡"

### Test 3: Turn OFF from Dashboard
1. Click "Turn OFF" button
2. **Expected**:
   - Dashboard shows "OFF" ✅
   - Relay clicks and turns OFF ✅
   - Serial Monitor: "→ Command from server: Turn OFF"
   - Serial Monitor: "Outlet → OFF"

### Test 4: SMS Control
1. Send SMS "ON" to ESP32
2. **Expected**: Relay actually turns ON ✅
3. Send SMS "OFF"
4. **Expected**: Relay actually turns OFF ✅

## Identifying Your Relay Type

### ACTIVE LOW (Most Common)
- **Marking**: Usually has "LOW LEVEL TRIGGER" or similar
- **Behavior**: LED on relay board lights up when signal is LOW
- **Examples**: Most blue relay modules, SRD-05VDC-SL-C

### ACTIVE HIGH (Less Common)
- **Marking**: "HIGH LEVEL TRIGGER"
- **Behavior**: LED on relay board lights up when signal is HIGH
- **Examples**: Some solid-state relays

### If You Have ACTIVE HIGH Relay

Change the code back:
```cpp
// In setOutlet() functions:
digitalWrite(RELAY_PIN, state ? HIGH : LOW);  // ACTIVE HIGH

// In setupPins():
digitalWrite(RELAY_PIN, LOW);  // LOW = OFF for ACTIVE HIGH
```

## Visual Relay States

### ACTIVE LOW (Current Fix):
```
Signal HIGH → Relay OFF
┌─────────────────────┐
│  Relay Module       │
│  LED: Off 🔴       │
│  Click: No          │
│  Circuit: Open      │
└─────────────────────┘

Signal LOW → Relay ON
┌─────────────────────┐
│  Relay Module       │
│  LED: On 🟢        │
│  Click: Yes         │
│  Circuit: Closed    │
└─────────────────────┘
```

## Pin Configuration

```cpp
const int RELAY_PIN = 5;  // GPIO 5

Setup:
  pinMode(RELAY_PIN, OUTPUT);
  digitalWrite(RELAY_PIN, HIGH);  // Start OFF (ACTIVE LOW)
```

## Summary of Changes

| Function | Line | Change | Reason |
|----------|------|--------|--------|
| `setupPins()` | ~558 | `LOW` → `HIGH` | Initialize relay to OFF |
| `setOutlet(bool)` | ~573 | `HIGH:LOW` → `LOW:HIGH` | Invert logic for ACTIVE LOW |
| `setOutlet(int, bool)` | ~742 | `HIGH:LOW` → `LOW:HIGH` | Invert logic for ACTIVE LOW |

## Before vs After

### Before Fix:
```
Dashboard: ON  → ESP32: HIGH → Relay: OFF ❌
Dashboard: OFF → ESP32: LOW  → Relay: ON  ❌
```

### After Fix:
```
Dashboard: ON  → ESP32: LOW  → Relay: ON  ✅
Dashboard: OFF → ESP32: HIGH → Relay: OFF ✅
```

## Upload Instructions

1. Open Arduino IDE
2. Open `esp32/universal_outlet_controller.ino`
3. Click **Upload**
4. Wait for "Done uploading"
5. Open Serial Monitor (115200 baud)
6. Press ESP32 Reset button
7. Test dashboard control
8. **Verify**: Dashboard and relay match! ✅

## Expected Serial Output

```
Setting up pins...
  Single Relay → GPIO 5 (ACTIVE LOW)
✓ Pins configured

[After clicking ON button on dashboard]
→ Command from server: Turn ON
  Outlet → ON ⚡

[Relay should physically click and turn ON] ✅
```

## Troubleshooting

### Issue: Still opposite after fix
**Solution**: Your relay might be ACTIVE HIGH. Change back to original code.

### Issue: Relay clicks but nothing happens
**Check**: 
- Wiring: Is relay connected to outlet correctly?
- Power: Is relay getting 5V power?
- Load: Is something plugged into the outlet?

### Issue: Relay doesn't click at all
**Check**:
- Pin number: Is RELAY_PIN = 5 correct for your wiring?
- Connections: GPIO 5 → Relay IN pin?
- Power: Relay module powered?

## Safety Note

⚠️ **WARNING**: Relay modules control AC power. Always:
- Disconnect power before wiring
- Use proper insulation
- Follow electrical codes
- Get electrician help if unsure
- Test with low voltage load first

## Confidence: 100% ✅

This fix will make the relay match the dashboard state correctly!

Upload the code and test! 🎯
