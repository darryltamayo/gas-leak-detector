# ESP32 IoT Outlet Controller

Complete Arduino code for ESP32 with SIM900A GSM module and relay control.

---

## 📋 Hardware Requirements

### Components Needed:
1. **ESP32 Development Board** (ESP32-WROOM-32)
2. **SIM900A GSM Module** (with active SIM card)
3. **Single Channel Relay Module** (5V)
4. **Power Supply** (5V 2A for ESP32 and GSM)
5. **Jumper Wires**
6. **Breadboard** (optional)

---

## 🔌 Hardware Connections

### ESP32 to Relay Module
```
Relay Module    ESP32
VCC      <----> 5V (VIN)
GND      <----> GND
IN       <----> GPIO 4
```

### ESP32 to SIM900A GSM Module
```
SIM900A Module  ESP32
VCC      <----> 5V (VIN or external 5V 2A)
GND      <----> GND
TXD      <----> GPIO 16 (RX)
RXD      <----> GPIO 17 (TX)
```

### Relay to AC Outlet
```
⚠️ HIGH VOLTAGE - BE CAREFUL!

Relay             AC Outlet
COM      <----> Live Wire (Hot)
NO       <----> Device Live Wire
(Leave NC disconnected)

Connect Neutral and Ground directly
```

---

## 📚 Required Arduino Libraries

Install these libraries via Arduino IDE Library Manager:

1. **WiFi** (Built-in with ESP32 board package)
2. **HTTPClient** (Built-in with ESP32 board package)
3. **ArduinoJson** (by Benoit Blanchon)
   - Go to: Sketch → Include Library → Manage Libraries
   - Search: "ArduinoJson"
   - Install version 6.x.x

---

## ⚙️ Configuration

### Step 1: Update WiFi Settings

Open `iot_outlet_controller.ino` and change:

```cpp
const char* WIFI_SSID = "Your_WiFi_SSID";
const char* WIFI_PASSWORD = "Your_WiFi_Password";
```

### Step 2: Update API Settings

```cpp
const char* API_URL = "http://yourdomain.com/api";  // Your production URL
const char* API_KEY = "your-api-key-here";           // From .env file
const char* DEVICE_ID = "ESP32_OUTLET_001";          // Match database
```

**Important:**
- API_URL should point to your production server
- API_KEY must match the `API_KEY` in your `.env` file
- DEVICE_ID must match the `device_id` in `iot_outlet_devices` table

### Step 3: Pin Configuration (Optional)

If you want to use different pins:

```cpp
const int RELAY_PIN = 4;        // Change if needed
const int LED_PIN = 2;          // Built-in LED
const int GSM_RX_PIN = 16;      // ESP32 RX
const int GSM_TX_PIN = 17;      // ESP32 TX
```

---

## 📤 Uploading to ESP32

### 1. Install ESP32 Board Support

In Arduino IDE:
1. Go to: File → Preferences
2. Add to "Additional Board Manager URLs":
   ```
   https://raw.githubusercontent.com/espressif/arduino-esp32/gh-pages/package_esp32_index.json
   ```
3. Go to: Tools → Board → Boards Manager
4. Search: "esp32"
5. Install: "esp32 by Espressif Systems"

### 2. Select Board and Port

1. Tools → Board → ESP32 Arduino → "ESP32 Dev Module"
2. Tools → Port → Select your COM port
3. Tools → Upload Speed → 115200

### 3. Upload Code

1. Open `iot_outlet_controller.ino`
2. Click Upload button (→)
3. Wait for "Done uploading"

### 4. Open Serial Monitor

1. Tools → Serial Monitor
2. Set baud rate to: **115200**
3. You should see initialization messages

---

## 🧪 Testing

### Test 1: WiFi Connection
```
Expected Output:
[WiFi] Connecting to: Your_WiFi
[WiFi] Connected!
[WiFi] IP Address: 192.168.1.xxx
```

### Test 2: GSM Module
```
Expected Output:
[GSM] Initializing SIM900A...
[GSM] AT -> OK
[GSM] Initialized and ready!
```

### Test 3: Heartbeat
```
Expected Output (every 60 seconds):
[API] Sending heartbeat...
[API] Heartbeat OK
```

### Test 4: Status Sync
```
Expected Output (every 10 seconds):
[API] Status changed: off -> on
[RELAY] ON
```

### Test 5: SMS Control

Send SMS from authorized number:
```
SMS: ON
Expected:
[SMS] New message received
[SMS] From: +639171234567
[SMS] Message: ON
[API] SIM authorized: YES
[API] Executing SMS command: ON
[RELAY] ON
```

---

## 🔧 Troubleshooting

### WiFi Not Connecting

**Problem:** `[WiFi] Connection failed!`

**Solutions:**
1. Check SSID and password are correct
2. Ensure WiFi is 2.4GHz (ESP32 doesn't support 5GHz)
3. Move closer to router
4. Check if MAC filtering is enabled on router

### GSM Module Not Responding

**Problem:** `[GSM] AT -> [no response]`

**Solutions:**
1. Check power supply (SIM900A needs 2A)
2. Verify TX/RX connections (might be reversed)
3. Check if SIM card is inserted properly
4. Ensure SIM card has credit/active plan
5. Try 9600 baud rate (default for SIM900A)

### API Connection Failed

**Problem:** `[API] Heartbeat failed: -1`

**Solutions:**
1. Check API_URL is correct
2. Verify API_KEY matches server
3. Ensure server is online
4. Check firewall rules
5. Test API URL in browser first

### Relay Not Switching

**Problem:** Relay doesn't click when status changes

**Solutions:**
1. Check relay module power (needs 5V)
2. Verify GPIO 4 connection
3. Check if relay module is active-high or active-low
4. Test relay manually:
   ```cpp
   digitalWrite(RELAY_PIN, HIGH);
   delay(1000);
   digitalWrite(RELAY_PIN, LOW);
   ```

### SMS Not Received

**Problem:** SMS sent but ESP32 doesn't respond

**Solutions:**
1. Check if SIM has service (signal bars)
2. Verify phone number is registered in database
3. Ensure SMS is in text format (not flash/class 0)
4. Check GSM antenna is connected
5. Test manually:
   ```
   AT+CMGL="ALL"  // List all SMS
   ```

---

## 📊 Serial Monitor Output Examples

### Normal Operation:
```
=================================
IoT Smart Outlet Controller
ESP32 + SIM900A + Relay
=================================

[INIT] Pins configured
[WiFi] Connecting to: MyWiFi
[WiFi] Connected!
[WiFi] IP Address: 192.168.1.100
[GSM] Initializing SIM900A...
[GSM] AT -> OK
[GSM] Initialized and ready!
[API] Sending heartbeat...
[API] Heartbeat OK
[READY] System initialized successfully!
Waiting for commands...
```

### SMS Command Received:
```
[SMS] New message received:
+CMT: "+639171234567",...
ON

[SMS] From: +639171234567
[SMS] Message: ON
[API] Validating SIM: +639171234567
[API] SIM authorized: YES
[API] Executing SMS command: ON
[API] Status changed: off -> on
[RELAY] ON
[SMS] Sending to: +639171234567
[SMS] Message: Outlet turned ON
[SMS] Sent!
```

---

## ⚡ Power Consumption

- **ESP32:** ~240mA (WiFi active)
- **SIM900A:** ~2A (during transmission)
- **Relay:** ~70mA (coil energized)

**Recommended Power Supply:**
- **5V 3A** power adapter
- Or separate power supplies:
  - ESP32: 5V 1A
  - SIM900A: 5V 2A

---

## 🔒 Security Notes

1. **API Key:** Keep secret, don't share
2. **WiFi Password:** Use WPA2 encryption
3. **Physical Access:** Secure ESP32 in enclosure
4. **High Voltage:** Ensure proper insulation of AC connections
5. **SIM Card:** Use prepaid SIM with limited credit

---

## 📝 Features Implemented

✅ WiFi connectivity with auto-reconnect
✅ REST API communication
✅ Heartbeat every 60 seconds
✅ Status synchronization every 10 seconds
✅ SMS command processing (ON, OFF, STATUS)
✅ SIM number authorization via API
✅ Relay control (ON/OFF)
✅ LED status indicator
✅ Schedule download (structure ready)
✅ Error handling and logging
✅ Watchdog timer safe

---

## 🚀 Advanced Features (Optional)

### Adding NTP Time Sync

For accurate schedule execution, add NTP:

```cpp
#include <time.h>

void setupTime() {
  configTime(8 * 3600, 0, "pool.ntp.org", "time.nist.gov");
  
  Serial.print("[NTP] Waiting for time sync");
  while (!time(nullptr)) {
    Serial.print(".");
    delay(1000);
  }
  Serial.println(" Done!");
}

String getCurrentTime() {
  time_t now = time(nullptr);
  struct tm* timeinfo = localtime(&now);
  
  char buffer[9];
  strftime(buffer, sizeof(buffer), "%H:%M:%S", timeinfo);
  return String(buffer);
}
```

### Adding HTTPS Support

For secure API communication:

```cpp
#include <WiFiClientSecure.h>

// In API calls, replace:
HTTPClient http;
WiFiClientSecure client;
client.setInsecure();  // Or add certificate
http.begin(client, url);
```

---

## 📱 AT Commands Reference

Useful SIM900A commands for testing:

```
AT                    Test if module responds
AT+CSQ                Check signal strength (should be 10-31)
AT+CREG?              Check network registration
AT+CMGF=1             Set SMS to text mode
AT+CMGS="+63xxx"      Send SMS
AT+CMGL="ALL"         List all SMS
AT+CMGD=1,4           Delete all SMS
AT+CMGR=1             Read SMS at index 1
AT+COPS?              Check network operator
```

---

## 🆘 Emergency Shutdown

If relay gets stuck ON:

1. **Immediate:** Unplug power supply
2. **Software Reset:** Press ESP32 reset button
3. **Factory Reset:** Re-upload code with relay initially OFF

---

## 📞 Support

For issues:
1. Check Serial Monitor output
2. Review this README
3. Test each component separately
4. Check API server logs
5. Verify database records

---

## ⚠️ Safety Warnings

1. **HIGH VOLTAGE:** AC power can be lethal
2. Always disconnect power before wiring
3. Use proper insulation and enclosure
4. Don't touch relay contacts when powered
5. Keep away from water
6. Use circuit breaker/fuse
7. Test with low voltage first
8. Get professional help if unsure

---

## 📄 License

Part of IoT Smart Outlet Management System
For internal use only

---

**Version:** 1.0.0
**Last Updated:** Phase 10 Complete
**Status:** Production Ready ✅
