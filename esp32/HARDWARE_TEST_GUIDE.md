# ESP32 Hardware Test Guide

## Quick Start

This test sketch will diagnose your ESP32, GSM900A, and Relay connections.

### 1. Upload the Test Sketch

1. Open `hardware_test.ino` in Arduino IDE
2. Select your ESP32 board (Tools > Board > ESP32 Dev Module)
3. Select the correct COM port (Tools > Port)
4. Click Upload

### 2. Open Serial Monitor

1. Click the Serial Monitor icon (top-right)
2. Set baud rate to **115200**
3. Set line ending to "Both NL & CR"

### 3. Read the Test Results

The test will automatically run and show:
- ✓ PASS - Component working correctly
- ✗ FAIL - Component has issues

## Pin Configuration

```
GSM900A Module:
├── VCC  → 5V (External Power Supply - 2A minimum)
├── GND  → GND (Common with ESP32)
├── RXD  → ESP32 GPIO 17
└── TXD  → ESP32 GPIO 16

Relay Module:
├── VCC  → 5V or 3.3V
├── GND  → GND
└── IN   → ESP32 GPIO 5

ESP32:
└── GND  → Common ground for all components
```

## Test Sequence

### Test 1: ESP32 Board Check
- Verifies ESP32 is working
- Shows chip model, memory, and pins
- **Expected**: Always passes if code uploads

### Test 2: Relay Module Check
- Tests relay switching ON/OFF
- **Expected**: You should hear TWO distinct clicks
- **Verify**: LED on relay board turns ON and OFF

**If no clicks:**
- Check VCC and GND connections
- Verify relay is powered (3.3V or 5V depending on module)
- Check GPIO 5 connection to IN pin

### Test 3: GSM Module Check
Runs multiple sub-tests:

#### 3.1-3.2: Initialization
- Sets up serial communication
- **Expected**: No errors

#### 3.3: Basic Communication (AT)
- Sends "AT" command to GSM
- **Expected**: Receives "OK" response

**If FAIL:**
- ❌ GSM module not powered
- ❌ RX/TX wires swapped
- ❌ No common ground
- ❌ GSM not turned on (press power button 2-3 seconds)

#### 3.4: Module Information
- Shows manufacturer, model, version
- **Expected**: Displays GSM module info

#### 3.5: SIM Card Status
- Checks if SIM card is inserted and recognized
- **Expected**: "READY" status

**If FAIL:**
- Insert SIM card properly
- Check if SIM is locked (needs PIN)
- Try different SIM card

#### 3.6: Signal Strength
- Measures cellular signal quality (0-31)
- **Signal Quality Guide:**
  - 0-1: No signal
  - 2-9: Poor
  - 10-14: Fair
  - 15-19: Good
  - 20-31: Excellent

**If poor signal:**
- Move near window
- Check antenna connection
- Try different location

#### 3.7: Network Registration
- Waits up to 30 seconds for network
- **Expected**: Registered on home or roaming network

**If FAIL:**
- Wait longer (can take 1-2 minutes)
- Check SIM has active service
- Verify SIM is not expired

#### 3.8: Network Operator
- Shows which network you're connected to
- **Expected**: Displays operator name

## Understanding Results

### All Tests Pass ✓
```
ESP32 Board:        ✓ PASS
Relay Module:       ✓ PASS (verify clicks)
GSM Power:          ✓ PASS
GSM Communication:  ✓ PASS
Network Status:     ✓ PASS (Home Network)
Signal Quality:     20 (Good)
```
**Action**: System ready! Upload the main controller sketch.

### GSM Communication Fails ✗
```
GSM Power:          ✗ FAIL
GSM Communication:  ✗ FAIL
```

**Troubleshooting Steps:**

1. **Check GSM Power LED**
   - Should blink slowly (every 3 seconds) when on network
   - Should blink fast when not registered
   - If no blink: GSM is off or not powered

2. **Verify Power Supply**
   - GSM needs 5V with 2A capacity
   - Cannot be powered from ESP32 (not enough current)
   - Use external power adapter or breadboard power supply

3. **Check RX/TX Wiring**
   ```
   Common mistake: RX-to-RX, TX-to-TX (WRONG!)
   Correct wiring: RX-to-TX, TX-to-RX
   
   ESP32 GPIO 16 → GSM TXD
   ESP32 GPIO 17 → GSM RXD
   ```

4. **Verify Common Ground**
   - ESP32 GND and GSM GND must be connected
   - All grounds must be common

5. **Turn On GSM Module**
   - Press and hold power button for 2-3 seconds
   - Wait for power LED to start blinking
   - Try test again

### Network Registration Fails ✗
```
GSM Communication:  ✓ PASS
Network Status:     ✗ FAIL (Not registered)
Signal Quality:     2 (Poor)
```

**Troubleshooting Steps:**

1. **Improve Signal**
   - Move device near window
   - Check antenna is properly connected
   - Try different location

2. **Check SIM Card**
   - Is it properly inserted?
   - Is it activated?
   - Does it have active service?
   - Try SIM in a phone first

3. **Wait Longer**
   - Network registration can take 1-2 minutes
   - Reset ESP32 and run test again

4. **Check SIM Compatibility**
   - Ensure SIM is 2G compatible
   - Some new SIMs are 4G-only

## Interactive Mode

After tests complete, you can send AT commands manually:

```
Type in Serial Monitor:
AT           → Test communication
AT+CSQ       → Check signal
AT+CREG?     → Check registration
AT+COPS?     → Check operator
ATI          → Module info
AT+CMGF=1    → Set SMS text mode
```

## Common Wiring Issues

### Issue 1: ESP32 Reboots When Calling
**Cause**: GSM power surge during transmission
**Solution**: 
- Use external 5V 2A power supply
- Add 100-1000µF capacitor across GSM VCC-GND

### Issue 2: AT Commands Don't Work
**Cause**: Wrong baud rate or RX/TX swapped
**Solution**:
- Swap GPIO 16 and 17 connections
- Try different baud rates: 9600, 19200, 38400, 115200

### Issue 3: Relay Clicks but AC Device Doesn't Switch
**Cause**: Relay contacts not wired to AC properly
**Solution**:
- Wire AC device through relay's COM and NO (Normally Open)
- For AC: Line → COM, NO → Device, Device → Neutral

### Issue 4: GSM Works Then Stops
**Cause**: Insufficient power supply
**Solution**:
- Use higher capacity power supply (2A minimum)
- Check all power connections are solid

## Safety Checklist

Before running test:
- [ ] ESP32 powered via USB or regulated power
- [ ] GSM has separate 5V 2A power supply
- [ ] All GND connections are common
- [ ] No exposed wires that can short
- [ ] Relay module matches voltage (3.3V or 5V)
- [ ] If testing with AC, ensure proper insulation

## What Each Wire Does

### ESP32 GPIO Pins
- **GPIO 16 (RX)**: Receives data FROM GSM's TX
- **GPIO 17 (TX)**: Sends data TO GSM's RX
- **GPIO 5**: Controls relay (LOW=ON, HIGH=OFF)

### GSM Module Pins
- **VCC**: 5V power input (high current required)
- **GND**: Ground reference
- **TXD**: Transmits data to ESP32 RX
- **RXD**: Receives data from ESP32 TX

### Relay Module Pins
- **VCC**: Power (3.3V or 5V depending on module)
- **GND**: Ground
- **IN**: Control signal from ESP32 GPIO 5
- **COM**: Common (connects to AC line)
- **NO**: Normally Open (connects to device)
- **NC**: Normally Closed (not used)

## Expected Serial Output

```
╔════════════════════════════════════════════════╗
║   ESP32 HARDWARE DIAGNOSTIC TEST v1.0         ║
║   Testing: ESP32 + GSM900A + Relay Module     ║
╚════════════════════════════════════════════════╝

┌────────────────────────────────────────────────┐
│ TEST 1: ESP32 BOARD CHECK                      │
└────────────────────────────────────────────────┘
  [1.1] Chip Model: ESP32-D0WDQ6
  [1.2] Chip Revision: 1
  [1.3] CPU Frequency: 240 MHz
  [1.4] Flash Size: 4 MB
  [1.5] Free Heap: 298 KB
  [1.6] Available GPIO Pins: GPIO 5, 16, 17 configured

  ✓ STATUS: ESP32 Board OK

┌────────────────────────────────────────────────┐
│ TEST 2: RELAY MODULE CHECK                     │
└────────────────────────────────────────────────┘
  [2.1] Configuration:
        Relay IN Pin: GPIO 5
        Type: Active LOW (relay ON when pin LOW)

  [2.2] Testing relay control...
        Setting relay OFF (HIGH)... Done
        Setting relay ON (LOW)... Done
        ⚠ LISTEN: Did you hear the relay click?
        Setting relay OFF (HIGH)... Done
        ⚠ LISTEN: Did you hear the relay click again?
...
```

## Next Steps After Testing

1. **All tests pass**: Upload `universal_outlet_controller.ino`
2. **GSM fails**: Fix wiring and power before proceeding
3. **Network fails**: Improve signal or check SIM
4. **Relay fails**: Check power and connections

## Need Help?

If tests still fail after troubleshooting:
1. Take photo of wiring
2. Copy Serial Monitor output
3. Note which test fails
4. Check for loose connections
5. Verify component specifications match expected values
