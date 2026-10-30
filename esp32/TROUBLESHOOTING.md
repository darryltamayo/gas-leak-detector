# ESP32 IoT Outlet Controller - Troubleshooting Guide

Comprehensive solutions to common problems and error messages.

---

## 🔍 Quick Problem Finder

**Jump to your issue:**
- [WiFi Problems](#wifi-problems)
- [GSM/SIM900A Issues](#gsmsim900a-issues)
- [Relay Problems](#relay-problems)
- [API Connection Issues](#api-connection-issues)
- [SMS Problems](#sms-problems)
- [Power Issues](#power-issues)
- [Upload/Programming Issues](#uploadprogramming-issues)
- [ESP32 Boot Problems](#esp32-boot-problems)
- [Sensor/Hardware Issues](#sensorhardware-issues)
- [Database/Server Issues](#databaseserver-issues)

---

## WiFi Problems

### Problem: WiFi Connection Failed

**Symptoms:**
```
[WiFi] Connecting to: MyWiFi
...................
[WiFi] Connection failed!
```

**Solutions:**

1. **Check SSID and Password:**
   ```cpp
   const char* WIFI_SSID = "Your_WiFi_SSID";     // Must match exactly
   const char* WIFI_PASSWORD = "Your_WiFi_Password"; // Case-sensitive
   ```
   - Copy-paste to avoid typos
   - Check for extra spaces

2. **Verify WiFi is 2.4GHz:**
   - ESP32 only supports 2.4GHz
   - Check router settings
   - Many routers have separate 2.4GHz and 5GHz networks

3. **Check WiFi Signal:**
   ```cpp
   Serial.print("Signal: ");
   Serial.println(WiFi.RSSI());
   ```
   - Should be better than -70 dBm
   - Move closer to router
   - Remove obstacles

4. **Check Router Settings:**
   - Disable MAC address filtering (or whitelist ESP32)
   - Ensure DHCP is enabled
   - Check if maximum clients limit reached
   - Try WPA2-PSK (not WPA3)

5. **Reset WiFi:**
   ```cpp
   WiFi.disconnect(true);
   delay(1000);
   WiFi.begin(ssid, password);
   ```

---

### Problem: WiFi Keeps Disconnecting

**Symptoms:**
```
[WiFi] Connected!
... (after some time)
[WiFi] Connecting to: MyWiFi
```

**Solutions:**

1. **Add Watchdog:**
   ```cpp
   void loop() {
     if (WiFi.status() != WL_CONNECTED) {
       connectWiFi();
     }
     // rest of code
   }
   ```

2. **Check Power Supply:**
   - WiFi uses high current
   - Brownouts cause disconnections
   - Use 5V 1A minimum

3. **Reduce Power Saving:**
   ```cpp
   WiFi.setSleep(false);  // Disable WiFi sleep
   ```

4. **Check Router Logs:**
   - Look for "client disconnected" messages
   - Check DHCP lease time

---

### Problem: Can't Get IP Address

**Symptoms:**
```
[WiFi] Connected!
[WiFi] IP Address: 0.0.0.0
```

**Solutions:**

1. **Wait Longer for DHCP:**
   ```cpp
   while (WiFi.status() != WL_CONNECTED) { delay(500); }
   delay(2000);  // Wait for IP assignment
   ```

2. **Use Static IP:**
   ```cpp
   IPAddress local_IP(192, 168, 1, 100);
   IPAddress gateway(192, 168, 1, 1);
   IPAddress subnet(255, 255, 255, 0);
   
   if (!WiFi.config(local_IP, gateway, subnet)) {
     Serial.println("STA Failed to configure");
   }
   WiFi.begin(ssid, password);
   ```

3. **Check Router DHCP Pool:**
   - Ensure DHCP range has available IPs
   - Check for IP conflicts

---

## GSM/SIM900A Issues

### Problem: GSM Module Not Responding

**Symptoms:**
```
[GSM] Initializing SIM900A...
[GSM] AT -> [no response]
```

**Solutions:**

1. **Check Power Supply:**
   - SIM900A needs **2A** current
   - Use separate power supply
   - Check voltage (4.8-5.2V)
   ```
   Multimeter on VCC pin:
   Should read 5.0V ±0.2V
   ```

2. **Verify TX/RX Connections:**
   - **ESP32 TX (GPIO 17) → GSM RX**
   - **ESP32 RX (GPIO 16) ← GSM TX**
   - Try swapping if not working

3. **Check Baud Rate:**
   ```cpp
   gsmSerial.begin(9600);  // Try 9600
   // or
   gsmSerial.begin(115200); // Try 115200
   ```

4. **Add Longer Delay:**
   ```cpp
   gsmSerial.begin(9600, SERIAL_8N1, 16, 17);
   delay(5000);  // Wait 5 seconds for module to boot
   ```

5. **Test with USB-Serial:**
   - Connect GSM to computer via USB-serial adapter
   - Use serial terminal (PuTTY, Arduino Serial Monitor)
   - Send AT commands manually
   - If works with computer but not ESP32 → wiring issue

---

### Problem: SIM Card Not Detected

**Symptoms:**
```
[GSM] AT+CPIN?
+CME ERROR: 10  (SIM not inserted)
```

**Solutions:**

1. **Check SIM Card:**
   - Remove and reinsert
   - Check correct orientation (chip facing down usually)
   - Clean contacts with eraser
   - Try SIM in phone first

2. **Check SIM Holder:**
   - Ensure SIM clicks into place
   - Check for bent pins
   - Some modules need push-push action

3. **Verify SIM Active:**
   - Test in phone
   - Check if activated by carrier
   - Ensure not locked to specific device

---

### Problem: No Network Signal

**Symptoms:**
```
[GSM] AT+CSQ
+CSQ: 99,99  (No signal)

[GSM] AT+CREG?
+CREG: 0,2  (Searching)
```

**Solutions:**

1. **Check Antenna:**
   - Connect antenna to correct connector
   - Try different antenna
   - Move to window/outdoors

2. **Wait Longer:**
   - Network registration can take 1-2 minutes
   - Add delays:
   ```cpp
   delay(10000);  // Wait 10 seconds
   ```

3. **Check SIM:**
   - Test in phone at same location
   - Check carrier coverage
   - Verify SIM plan is active

4. **Check Network Operator:**
   ```cpp
   gsmSerial.println("AT+COPS?");
   delay(2000);
   // Should show operator name
   ```

5. **Manual Network Selection:**
   ```cpp
   gsmSerial.println("AT+COPS=1,2,\"51010\"");  // Your carrier code
   ```

---

### Problem: SMS Not Sending

**Symptoms:**
```
[SMS] Sending to: +639171234567
ERROR
```

**Solutions:**

1. **Check SIM Credit:**
   - Verify SIM has credit/plan
   - Test sending from phone

2. **Verify Phone Number Format:**
   ```cpp
   // CORRECT (with country code):
   "+639171234567"  // Philippines
   "+14155551234"   // USA
   
   // WRONG:
   "09171234567"    // Missing country code
   "639171234567"   // Missing +
   ```

3. **Check SMS Mode:**
   ```cpp
   gsmSerial.println("AT+CMGF=1");  // Text mode
   delay(1000);
   // Verify response is OK
   ```

4. **Add More Delays:**
   ```cpp
   gsmSerial.println("AT+CMGS=\"+639171234567\"");
   delay(1000);  // Wait for >
   gsmSerial.print("Test message");
   delay(500);   // Wait before Ctrl+Z
   gsmSerial.write(26);
   delay(5000);  // Wait for send
   ```

5. **Check Error Codes:**
   ```
   +CMS ERROR: 500 = SIM not ready
   +CMS ERROR: 302 = Operation not allowed
   +CMS ERROR: 305 = No service
   +CMS ERROR: 330 = Destination address required
   ```

---

### Problem: SMS Not Receiving

**Symptoms:**
- Send SMS to SIM but ESP32 doesn't detect it

**Solutions:**

1. **Check SMS Mode:**
   ```cpp
   gsmSerial.println("AT+CMGF=1");       // Text mode
   delay(1000);
   gsmSerial.println("AT+CNMI=1,2,0,0,0"); // Direct notification
   delay(1000);
   ```

2. **Check for Stored SMS:**
   ```cpp
   gsmSerial.println("AT+CMGL=\"ALL\"");  // List all SMS
   delay(2000);
   ```

3. **Delete Old SMS:**
   ```cpp
   gsmSerial.println("AT+CMGD=1,4");  // Delete all SMS
   delay(2000);
   ```

4. **Check SIM Memory:**
   ```cpp
   gsmSerial.println("AT+CPMS?");  // Check memory status
   ```

5. **Verify Number:**
   - Test sending from another phone
   - Check if number blocked by carrier

---

## Relay Problems

### Problem: Relay Doesn't Click

**Symptoms:**
- GPIO 4 goes HIGH but relay doesn't activate

**Solutions:**

1. **Check Power:**
   ```
   Multimeter on relay VCC:
   Should read 5.0V when relay should be ON
   ```

2. **Check GPIO Output:**
   ```cpp
   Serial.print("GPIO 4 state: ");
   Serial.println(digitalRead(4));
   
   // Measure with multimeter:
   // HIGH = 3.3V
   // LOW = 0V
   ```

3. **Check Relay Trigger Level:**
   - Most relays are **active-HIGH** (HIGH = ON)
   - Some are **active-LOW** (LOW = ON)
   - Try inverting:
   ```cpp
   digitalWrite(RELAY_PIN, !relayState);  // Invert
   ```

4. **Check Relay Input Voltage:**
   - Some relays need 5V input
   - ESP32 outputs 3.3V
   - Solution: Use transistor or relay with 3.3V input

5. **Test Relay Manually:**
   ```cpp
   // Force relay ON:
   pinMode(4, OUTPUT);
   digitalWrite(4, HIGH);
   delay(5000);  // Hold for 5 seconds
   ```

---

### Problem: Relay Stuck ON

**Symptoms:**
- Relay turns ON but won't turn OFF

**Solutions:**

1. **Check Code:**
   ```cpp
   // Ensure you're setting LOW:
   digitalWrite(RELAY_PIN, LOW);
   
   // Verify with serial:
   Serial.println(relayState);
   ```

2. **Check for Relay Welding:**
   - Contacts may be welded (common with high current)
   - Replace relay module
   - Use relay rated for your load

3. **Power Cycle:**
   - Disconnect power completely
   - Wait 10 seconds
   - Reconnect

4. **Emergency Reset:**
   ```cpp
   void setup() {
     pinMode(4, OUTPUT);
     digitalWrite(4, LOW);  // Force OFF immediately
   }
   ```

---

### Problem: Relay Switches But Load Doesn't

**Symptoms:**
- Relay clicks, LED lights up, but device doesn't turn ON

**Solutions:**

1. **Check AC Wiring:**
   ```
   Verify:
   - Live wire to COM
   - NO to device
   - Not using NC by mistake
   ```

2. **Test with Multimeter:**
   ```
   Relay ON:
   COM to NO = 0Ω (continuity)
   
   Relay OFF:
   COM to NO = infinite (no continuity)
   ```

3. **Check Load:**
   - Test device with direct connection
   - Verify device works

4. **Check Neutral/Ground:**
   - Ensure neutral connected
   - Check ground connection

---

## API Connection Issues

### Problem: Heartbeat Failed with Code -1

**Symptoms:**
```
[API] Heartbeat failed: -1
```

**Meaning:** Can't connect to server

**Solutions:**

1. **Check API URL:**
   ```cpp
   const char* API_URL = "http://yourdomain.com/api";
   
   // Must be complete URL
   // Must start with http:// or https://
   // No trailing slash after /api
   ```

2. **Test URL in Browser:**
   - Open `http://yourdomain.com/api/heartbeat` in browser
   - Should see error about missing API key (means endpoint exists)

3. **Check WiFi:**
   - Ensure ESP32 connected to internet
   - Test with simple HTTP request to google.com

4. **Check Firewall:**
   - Server may block ESP32 IP
   - Check server firewall rules
   - Whitelist ESP32 IP if needed

5. **Check DNS:**
   ```cpp
   // If domain doesn't resolve, try IP:
   const char* API_URL = "http://192.168.1.100/api";
   ```

---

### Problem: Heartbeat Failed with Code 401

**Symptoms:**
```
[API] Heartbeat failed: 401
```

**Meaning:** Unauthorized (wrong API key)

**Solutions:**

1. **Check API Key:**
   ```cpp
   const char* API_KEY = "your-api-key-here";
   ```

2. **Check .env File:**
   ```
   API_KEY=your-api-key-here
   ```
   - Must match exactly
   - Case-sensitive
   - No spaces

3. **Regenerate Key:**
   - Create new random key
   - Update both .env and ESP32 code

---

### Problem: Heartbeat Failed with Code 404

**Symptoms:**
```
[API] Heartbeat failed: 404
```

**Meaning:** Endpoint not found

**Solutions:**

1. **Check URL Path:**
   ```cpp
   // Correct:
   http://yourdomain.com/api
   
   // Wrong:
   http://yourdomain.com/api/
   http://yourdomain.com
   ```

2. **Check .htaccess:**
   - Verify URL rewriting works
   - Test directly: `yourdomain.com/index.php?route=api&endpoint=heartbeat`

3. **Check Route Configuration:**
   - Verify `routes/api.php` exists
   - Check route registration in `public/index.php`

---

### Problem: Heartbeat Failed with Code 500

**Symptoms:**
```
[API] Heartbeat failed: 500
```

**Meaning:** Server error

**Solutions:**

1. **Check PHP Logs:**
   ```
   In XAMPP:
   xampp/php/logs/php_error_log
   
   Look for errors near timestamp
   ```

2. **Check Database:**
   - Verify MySQL running
   - Test database connection
   - Check table exists

3. **Enable Debug Mode:**
   ```php
   // In config/app.php:
   'debug' => true,
   ```

4. **Test API Manually:**
   ```bash
   curl -X POST http://yourdomain.com/api/heartbeat \
     -H "X-API-KEY: your-api-key" \
     -H "Content-Type: application/json" \
     -d '{"device_id":"ESP32_OUTLET_001"}'
   ```

---

## SMS Problems

### Problem: Unauthorized SIM

**Symptoms:**
```
[API] SIM authorized: NO
[SMS] Sending to: +639171234567
[SMS] Message: Unauthorized. This number is not registered.
```

**Solutions:**

1. **Check Database:**
   ```sql
   SELECT * FROM iot_outlet_authorized_sims 
   WHERE phone_number = '+639171234567';
   ```

2. **Add Number to Database:**
   - Log into admin panel
   - Go to SIM Management
   - Add authorized number

3. **Check Phone Format:**
   - Database: `+639171234567`
   - SMS from: `+639171234567`
   - Must match EXACTLY (including +)

4. **Check Status:**
   ```sql
   SELECT phone_number, is_active 
   FROM iot_outlet_authorized_sims;
   ```
   - Ensure `is_active = 1`

---

### Problem: SMS Commands Not Working

**Symptoms:**
```
[SMS] Message: ON
[API] Executing SMS command: ON
[RELAY] OFF  (doesn't turn ON)
```

**Solutions:**

1. **Check Response:**
   - Look at full API response
   - Check for error messages

2. **Check Database Status:**
   ```sql
   SELECT outlet_status, control_mode 
   FROM iot_outlet_devices 
   WHERE device_id = 'ESP32_OUTLET_001';
   ```

3. **Check Control Mode:**
   - If mode is "scheduled", manual commands may not work
   - Set mode to "manual" first

4. **Check Activity Logs:**
   ```sql
   SELECT * FROM iot_outlet_activity_logs 
   ORDER BY created_at DESC 
   LIMIT 10;
   ```

---

## Power Issues

### Problem: ESP32 Keeps Rebooting

**Symptoms:**
```
=================================
IoT Smart Outlet Controller
...
=================================
IoT Smart Outlet Controller
...
```

**Solutions:**

1. **Check Power Supply:**
   - Needs 5V 1A minimum
   - Check voltage under load
   - Use USB power meter

2. **Check for Brownouts:**
   ```
   Look for:
   Brownout detector was triggered
   ```
   - Add capacitor (1000µF) near VIN
   - Use better power supply

3. **Check WiFi Power:**
   ```cpp
   WiFi.setTxPower(WIFI_POWER_11dBm);  // Reduce power
   ```

4. **Check GSM Power:**
   - GSM uses 2A peaks
   - Use separate power supply

5. **Disable Watchdog:**
   ```cpp
   #include "esp_task_wdt.h"
   
   void setup() {
     esp_task_wdt_init(30, false);  // Disable auto-reset
   }
   ```

---

### Problem: GSM Module Reboots ESP32

**Symptoms:**
- ESP32 resets when GSM transmits

**Solutions:**

1. **Separate Power Supplies:**
   ```
   5V 1A → ESP32
   5V 2A → SIM900A
   Common GND
   ```

2. **Add Capacitors:**
   - 1000µF near SIM900A VCC
   - 100µF near ESP32 VIN

3. **Check Wiring:**
   - Use thick wires for GSM power (18 AWG)
   - Short connections reduce voltage drop

---

## Upload/Programming Issues

### Problem: Failed to Connect to ESP32

**Symptoms:**
```
A fatal error occurred: Failed to connect to ESP32
```

**Solutions:**

1. **Check USB Cable:**
   - Must be data cable (not charge-only)
   - Try different cable

2. **Check COM Port:**
   - Verify correct port selected
   - Windows: Device Manager → Ports
   - Unplug/replug to see which port appears

3. **Install Drivers:**
   - CH340 driver (for CH340 USB chip)
   - CP2102 driver (for CP2102 USB chip)
   - Download from manufacturer

4. **Hold BOOT Button:**
   - Hold BOOT button
   - Press and release EN button
   - Release BOOT button
   - Try upload

5. **Check Board Selection:**
   - Tools → Board → ESP32 Dev Module
   - Upload Speed: 115200

---

### Problem: Compilation Errors

**Symptoms:**
```
error: 'WiFi' was not declared in this scope
```

**Solutions:**

1. **Install ESP32 Board:**
   - File → Preferences
   - Add URL: `https://raw.githubusercontent.com/espressif/arduino-esp32/gh-pages/package_esp32_index.json`
   - Tools → Board → Boards Manager
   - Search and install "esp32"

2. **Install Libraries:**
   - ArduinoJson: Library Manager → Search "ArduinoJson" → Install

3. **Check Board Selected:**
   - Must have ESP32 board selected (not Arduino Uno, etc.)

---

## ESP32 Boot Problems

### Problem: Boot Loop

**Symptoms:**
```
rst:0x10 (RTCWDT_RTC_RESET),boot:0x13 (SPI_FAST_FLASH_BOOT)
...
rst:0x10 (RTCWDT_RTC_RESET),boot:0x13 (SPI_FAST_FLASH_BOOT)
```

**Solutions:**

1. **Flash Erase:**
   - Tools → Erase Flash → All Flash Contents
   - Upload code again

2. **Check Power:**
   - Insufficient power causes boot loops
   - Use 5V 1A minimum

3. **Remove Peripherals:**
   - Disconnect all external components
   - Upload code
   - Reconnect one by one

---

### Problem: Can't Enter Programming Mode

**Symptoms:**
- Upload fails immediately

**Solutions:**

1. **Manual Boot Mode:**
   - Hold BOOT button
   - Press EN button
   - Release EN
   - Release BOOT
   - Try upload within 5 seconds

2. **Check Connections:**
   - Nothing connected to GPIO 0
   - Nothing connected to GPIO 2

---

## Database/Server Issues

### Problem: Device Not in Database

**Symptoms:**
```
[API] Heartbeat failed: 404
Device not found
```

**Solutions:**

1. **Add Device:**
   ```sql
   INSERT INTO iot_outlet_devices 
   (device_id, device_name, user_id, outlet_status, control_mode) 
   VALUES 
   ('ESP32_OUTLET_001', 'Main Outlet', 1, 'off', 'manual');
   ```

2. **Check Device ID:**
   - ESP32 code: `DEVICE_ID`
   - Database: `device_id` column
   - Must match exactly

---

### Problem: Database Connection Failed

**Symptoms:**
- Web dashboard shows errors
- API returns 500

**Solutions:**

1. **Check MySQL Running:**
   - XAMPP Control Panel → MySQL → Start

2. **Check Credentials:**
   ```env
   DB_HOST=localhost
   DB_DATABASE=iot_outlet_system
   DB_USERNAME=root
   DB_PASSWORD=
   ```

3. **Test Connection:**
   ```php
   <?php
   $db = new PDO(
     "mysql:host=localhost;dbname=iot_outlet_system",
     "root",
     ""
   );
   echo "Connected!";
   ```

---

## 🔧 Advanced Debugging

### Enable Verbose Logging

Add to code:
```cpp
#define DEBUG 1

void debugLog(String message) {
  #ifdef DEBUG
    Serial.println("[DEBUG] " + message);
  #endif
}

// Usage:
debugLog("WiFi signal: " + String(WiFi.RSSI()));
```

---

### Monitor All Serial Traffic

```cpp
void loop() {
  // ESP32 to GSM
  if (Serial.available()) {
    gsmSerial.write(Serial.read());
  }
  
  // GSM to ESP32
  if (gsmSerial.available()) {
    Serial.write(gsmSerial.read());
  }
}
```

---

### Check Memory Usage

```cpp
void printMemory() {
  Serial.print("Free heap: ");
  Serial.println(ESP.getFreeHeap());
  Serial.print("Heap size: ");
  Serial.println(ESP.getHeapSize());
}
```

---

### Test API Endpoints Manually

```bash
# Heartbeat
curl -X POST http://localhost/ac_controller_system/api/heartbeat \
  -H "X-API-KEY: your-key" \
  -H "Content-Type: application/json" \
  -d '{"device_id":"ESP32_OUTLET_001"}'

# Status
curl "http://localhost/ac_controller_system/api/status?device_id=ESP32_OUTLET_001&api_key=your-key"

# Validate SIM
curl -X POST http://localhost/ac_controller_system/api/sms/validate \
  -H "X-API-KEY: your-key" \
  -H "Content-Type: application/json" \
  -d '{"phone_number":"+639171234567"}'
```

---

## 📞 Last Resort

If nothing works:

1. **Factory Reset ESP32:**
   - Tools → Erase Flash → All Flash Contents
   - Upload code again

2. **Test with Minimal Code:**
   - Start with just WiFi
   - Add features one by one
   - Find what breaks

3. **Check Hardware:**
   - Replace ESP32
   - Replace GSM module
   - Replace relay
   - Test with known working components

4. **Fresh Database:**
   - Drop and recreate database
   - Run schema.sql again
   - Run seeds.sql again

---

## 🆘 Emergency Contacts

**Hardware on Fire:**
- 🔥 **Unplug power immediately**
- Use fire extinguisher (not water!)
- Call emergency services if needed

**Electric Shock:**
- ⚠️ **Turn off power at breaker**
- Do not touch victim while power on
- Call emergency services

**Data Loss:**
- Check database backups
- Review activity logs
- Contact system administrator

---

**Remember:** 99% of problems are:
- Wrong configuration (SSID, password, API key, pins)
- Bad connections (loose wires, wrong pins)
- Insufficient power (use proper power supply)

**Double-check the basics before assuming hardware failure!**

---

**Version:** 1.0.0  
**Last Updated:** Phase 10 Complete  
**Status:** Production Ready ✅
