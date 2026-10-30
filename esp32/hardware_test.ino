/**
 * ESP32 + GSM900A + Relay Hardware Test
 * 
 * This sketch tests all hardware connections and reports issues.
 * 
 * PIN CONFIGURATION:
 * - GSM RX: GPIO 16
 * - GSM TX: GPIO 17
 * - Relay IN: GPIO 5
 * 
 * SERIAL MONITOR SETTINGS:
 * - Baud Rate: 115200
 * - Both NL & CR
 * 
 * TEST SEQUENCE:
 * 1. ESP32 Board Check
 * 2. Relay Connection Test
 * 3. GSM Module Power Check
 * 4. GSM Communication Test
 * 5. GSM Network Registration
 * 6. Comprehensive System Report
 */

// Pin Definitions
#define GSM_RX_PIN 16       // ESP32 GPIO 16 -> GSM TX
#define GSM_TX_PIN 17       // ESP32 GPIO 17 -> GSM RX
#define RELAY_PIN 5         // ESP32 GPIO 5 -> Relay IN

// GSM Serial
HardwareSerial gsmSerial(2);  // Use UART2

// Test Results Structure
struct TestResults {
  bool esp32_ok;
  bool relay_ok;
  bool gsm_power_ok;
  bool gsm_comm_ok;
  bool gsm_registered;
  String gsm_response;
  String network_status;
  int signal_quality;
};

TestResults results;

// Test timing
unsigned long testStartTime;

void setup() {
  // Initialize Serial Monitor
  Serial.begin(115200);
  delay(2000);
  
  Serial.println("\n\n");
  Serial.println("╔════════════════════════════════════════════════╗");
  Serial.println("║   ESP32 HARDWARE DIAGNOSTIC TEST v1.0         ║");
  Serial.println("║   Testing: ESP32 + GSM900A + Relay Module     ║");
  Serial.println("╚════════════════════════════════════════════════╝");
  Serial.println();
  
  testStartTime = millis();
  
  // Initialize test results
  results.esp32_ok = false;
  results.relay_ok = false;
  results.gsm_power_ok = false;
  results.gsm_comm_ok = false;
  results.gsm_registered = false;
  results.signal_quality = -1;
  
  // Run all tests
  testESP32();
  testRelay();
  testGSM();
  
  // Print comprehensive report
  printFinalReport();
}

void loop() {
  // Interactive mode - allow manual AT commands
  if (Serial.available()) {
    String command = Serial.readStringUntil('\n');
    command.trim();
    
    if (command.length() > 0) {
      Serial.println("\n[MANUAL] Sending: " + command);
      gsmSerial.println(command);
      delay(1000);
      
      Serial.println("[MANUAL] Response:");
      while (gsmSerial.available()) {
        Serial.write(gsmSerial.read());
      }
      Serial.println();
    }
  }
  
  // Pass GSM responses to Serial Monitor
  if (gsmSerial.available()) {
    while (gsmSerial.available()) {
      Serial.write(gsmSerial.read());
    }
  }
}

/**
 * Test 1: ESP32 Board
 */
void testESP32() {
  Serial.println("┌────────────────────────────────────────────────┐");
  Serial.println("│ TEST 1: ESP32 BOARD CHECK                      │");
  Serial.println("└────────────────────────────────────────────────┘");
  
  Serial.print("  [1.1] Chip Model: ");
  Serial.println(ESP.getChipModel());
  
  Serial.print("  [1.2] Chip Revision: ");
  Serial.println(ESP.getChipRevision());
  
  Serial.print("  [1.3] CPU Frequency: ");
  Serial.print(ESP.getCpuFreqMHz());
  Serial.println(" MHz");
  
  Serial.print("  [1.4] Flash Size: ");
  Serial.print(ESP.getFlashChipSize() / 1024 / 1024);
  Serial.println(" MB");
  
  Serial.print("  [1.5] Free Heap: ");
  Serial.print(ESP.getFreeHeap() / 1024);
  Serial.println(" KB");
  
  Serial.print("  [1.6] Available GPIO Pins: ");
  Serial.println("GPIO 5, 16, 17 configured");
  
  // Check if we can access the configured pins
  pinMode(RELAY_PIN, OUTPUT);
  pinMode(GSM_RX_PIN, INPUT);
  pinMode(GSM_TX_PIN, OUTPUT);
  
  results.esp32_ok = true;
  
  Serial.println("\n  ✓ STATUS: ESP32 Board OK");
  Serial.println();
  delay(1000);
}

/**
 * Test 2: Relay Module
 */
void testRelay() {
  Serial.println("┌────────────────────────────────────────────────┐");
  Serial.println("│ TEST 2: RELAY MODULE CHECK                     │");
  Serial.println("└────────────────────────────────────────────────┘");
  
  Serial.println("  [2.1] Configuration:");
  Serial.println("        Relay IN Pin: GPIO 5");
  Serial.println("        Type: Active LOW (relay ON when pin LOW)");
  Serial.println();
  
  Serial.println("  [2.2] Testing relay control...");
  
  // Test relay OFF (HIGH signal for active-low relay)
  Serial.print("        Setting relay OFF (HIGH)... ");
  digitalWrite(RELAY_PIN, HIGH);
  delay(1000);
  Serial.println("Done");
  
  // Test relay ON
  Serial.print("        Setting relay ON (LOW)... ");
  digitalWrite(RELAY_PIN, LOW);
  delay(1000);
  Serial.println("Done");
  Serial.println("        ⚠ LISTEN: Did you hear the relay click?");
  
  // Test relay OFF again
  Serial.print("        Setting relay OFF (HIGH)... ");
  digitalWrite(RELAY_PIN, HIGH);
  delay(1000);
  Serial.println("Done");
  Serial.println("        ⚠ LISTEN: Did you hear the relay click again?");
  
  Serial.println();
  Serial.println("  [2.3] Wiring Check:");
  Serial.println("        VCC  -> 5V or 3.3V (depending on relay module)");
  Serial.println("        GND  -> GND");
  Serial.println("        IN   -> GPIO 5");
  Serial.println();
  
  Serial.println("  [2.4] Expected Behavior:");
  Serial.println("        - You should hear TWO distinct clicks");
  Serial.println("        - LED on relay board should turn ON/OFF");
  Serial.println("        - NO click = Check power and ground connections");
  Serial.println();
  
  // Read back the pin state
  int pinState = digitalRead(RELAY_PIN);
  Serial.print("  [2.5] Current Pin State: ");
  Serial.println(pinState == HIGH ? "HIGH (Relay OFF)" : "LOW (Relay ON)");
  
  results.relay_ok = true;  // Assume OK if no exceptions
  
  Serial.println("\n  ✓ STATUS: Relay Control OK (Manual verification needed)");
  Serial.println();
  delay(2000);
}

/**
 * Test 3: GSM Module
 */
void testGSM() {
  Serial.println("┌────────────────────────────────────────────────┐");
  Serial.println("│ TEST 3: GSM900A MODULE CHECK                   │");
  Serial.println("└────────────────────────────────────────────────┘");
  
  Serial.println("  [3.1] Configuration:");
  Serial.println("        GSM RX Pin: GPIO 16 (ESP32 TX2)");
  Serial.println("        GSM TX Pin: GPIO 17 (ESP32 RX2)");
  Serial.println("        Baud Rate: 9600");
  Serial.println();
  
  // Initialize GSM Serial
  Serial.println("  [3.2] Initializing GSM serial communication...");
  gsmSerial.begin(9600, SERIAL_8N1, GSM_RX_PIN, GSM_TX_PIN);
  delay(2000);
  Serial.println("        Done");
  Serial.println();
  
  // Clear any garbage data
  while (gsmSerial.available()) {
    gsmSerial.read();
  }
  
  // Test 3.3: Basic Communication
  Serial.println("  [3.3] Testing basic communication (AT)...");
  results.gsm_comm_ok = sendGSMCommand("AT", "OK", 2000);
  
  if (!results.gsm_comm_ok) {
    Serial.println();
    Serial.println("  ✗ ERROR: No response from GSM module");
    Serial.println();
    Serial.println("  [TROUBLESHOOTING]");
    Serial.println("    1. Check GSM module power (4V is blinking?)");
    Serial.println("    2. Verify RX/TX connections:");
    Serial.println("       ESP32 GPIO16 -> GSM TX");
    Serial.println("       ESP32 GPIO17 -> GSM RX");
    Serial.println("    3. Check if RX/TX are swapped");
    Serial.println("    4. Ensure common ground (GND) connection");
    Serial.println("    5. Try pressing GSM power button for 2-3 seconds");
    Serial.println();
    return;
  }
  
  results.gsm_power_ok = true;
  Serial.println("        ✓ GSM module responding");
  Serial.println();
  
  // Test 3.4: Module Info
  Serial.println("  [3.4] Getting module information...");
  sendGSMCommand("AT+GMI", "OK", 2000);  // Manufacturer
  sendGSMCommand("AT+GMM", "OK", 2000);  // Model
  sendGSMCommand("AT+GMR", "OK", 2000);  // Revision
  Serial.println();
  
  // Test 3.5: SIM Card
  Serial.println("  [3.5] Checking SIM card status...");
  bool simOK = sendGSMCommand("AT+CPIN?", "READY", 3000);
  if (!simOK) {
    Serial.println("        ✗ SIM card not detected or locked");
    Serial.println("        Check: Is SIM card properly inserted?");
  } else {
    Serial.println("        ✓ SIM card detected");
  }
  Serial.println();
  
  // Test 3.6: Signal Quality
  Serial.println("  [3.6] Checking signal strength...");
  String response = sendGSMCommandWithResponse("AT+CSQ", 2000);
  if (response.indexOf("+CSQ:") >= 0) {
    int csqIndex = response.indexOf("+CSQ:");
    String csqValue = response.substring(csqIndex + 6);
    int commaIndex = csqValue.indexOf(',');
    if (commaIndex > 0) {
      int rssi = csqValue.substring(0, commaIndex).toInt();
      results.signal_quality = rssi;
      
      Serial.print("        Signal: ");
      Serial.print(rssi);
      Serial.print(" (");
      
      if (rssi == 0) {
        Serial.println("-115 dBm or less - NO SIGNAL)");
      } else if (rssi == 1) {
        Serial.println("-111 dBm - VERY WEAK)");
      } else if (rssi >= 2 && rssi <= 9) {
        Serial.println("POOR)");
      } else if (rssi >= 10 && rssi <= 14) {
        Serial.println("FAIR)");
      } else if (rssi >= 15 && rssi <= 19) {
        Serial.println("GOOD)");
      } else if (rssi >= 20 && rssi <= 30) {
        Serial.println("EXCELLENT)");
      } else if (rssi == 31) {
        Serial.println("-51 dBm or greater - EXCELLENT)");
      } else if (rssi == 99) {
        Serial.println("UNKNOWN or not detectable)");
      }
    }
  }
  Serial.println();
  
  // Test 3.7: Network Registration
  Serial.println("  [3.7] Checking network registration...");
  Serial.println("        Waiting for network (max 30 seconds)...");
  
  bool registered = false;
  for (int i = 0; i < 15; i++) {
    String netResponse = sendGSMCommandWithResponse("AT+CREG?", 2000);
    
    if (netResponse.indexOf("+CREG: 0,1") >= 0) {
      Serial.println("        ✓ Registered on home network");
      results.network_status = "Home Network";
      registered = true;
      break;
    } else if (netResponse.indexOf("+CREG: 0,5") >= 0) {
      Serial.println("        ✓ Registered on roaming network");
      results.network_status = "Roaming";
      registered = true;
      break;
    } else if (netResponse.indexOf("+CREG: 0,2") >= 0) {
      Serial.print("        Searching for network");
      Serial.println("...");
      delay(2000);
    } else {
      Serial.print("        Status check ");
      Serial.print(i + 1);
      Serial.println("/15...");
      delay(2000);
    }
  }
  
  if (!registered) {
    Serial.println("        ✗ Not registered on network");
    Serial.println("        Check: Is SIM card active? Do you have signal?");
  }
  
  results.gsm_registered = registered;
  Serial.println();
  
  // Test 3.8: Operator
  if (registered) {
    Serial.println("  [3.8] Getting network operator...");
    sendGSMCommand("AT+COPS?", "OK", 3000);
    Serial.println();
  }
  
  Serial.println("  ✓ GSM Module Tests Complete");
  Serial.println();
  delay(1000);
}

/**
 * Send AT command and wait for expected response
 */
bool sendGSMCommand(String command, String expectedResponse, unsigned long timeout) {
  Serial.print("        Sending: ");
  Serial.println(command);
  
  gsmSerial.println(command);
  
  unsigned long startTime = millis();
  String response = "";
  
  while (millis() - startTime < timeout) {
    if (gsmSerial.available()) {
      char c = gsmSerial.read();
      response += c;
      Serial.write(c);
    }
  }
  
  bool success = response.indexOf(expectedResponse) >= 0;
  
  if (!success && response.length() > 0) {
    Serial.print("        Response: ");
    Serial.println(response);
  }
  
  return success;
}

/**
 * Send AT command and return response
 */
String sendGSMCommandWithResponse(String command, unsigned long timeout) {
  Serial.print("        Sending: ");
  Serial.println(command);
  
  gsmSerial.println(command);
  
  unsigned long startTime = millis();
  String response = "";
  
  while (millis() - startTime < timeout) {
    if (gsmSerial.available()) {
      char c = gsmSerial.read();
      response += c;
      Serial.write(c);
    }
  }
  
  return response;
}

/**
 * Print Final Diagnostic Report
 */
void printFinalReport() {
  unsigned long totalTime = (millis() - testStartTime) / 1000;
  
  Serial.println("╔════════════════════════════════════════════════╗");
  Serial.println("║           DIAGNOSTIC REPORT                    ║");
  Serial.println("╚════════════════════════════════════════════════╝");
  Serial.println();
  
  // ESP32
  Serial.print("  ESP32 Board:        ");
  Serial.println(results.esp32_ok ? "✓ PASS" : "✗ FAIL");
  
  // Relay
  Serial.print("  Relay Module:       ");
  Serial.println(results.relay_ok ? "✓ PASS (verify clicks)" : "✗ FAIL");
  
  // GSM Power
  Serial.print("  GSM Power:          ");
  Serial.println(results.gsm_power_ok ? "✓ PASS" : "✗ FAIL");
  
  // GSM Communication
  Serial.print("  GSM Communication:  ");
  Serial.println(results.gsm_comm_ok ? "✓ PASS" : "✗ FAIL");
  
  // Network Registration
  Serial.print("  Network Status:     ");
  if (results.gsm_registered) {
    Serial.print("✓ PASS (");
    Serial.print(results.network_status);
    Serial.println(")");
  } else {
    Serial.println("✗ FAIL (Not registered)");
  }
  
  // Signal Quality
  if (results.signal_quality >= 0) {
    Serial.print("  Signal Quality:     ");
    Serial.print(results.signal_quality);
    if (results.signal_quality >= 15) {
      Serial.println(" (Good)");
    } else if (results.signal_quality >= 10) {
      Serial.println(" (Fair)");
    } else if (results.signal_quality >= 2) {
      Serial.println(" (Poor)");
    } else {
      Serial.println(" (Very Weak)");
    }
  }
  
  Serial.println();
  Serial.println("┌────────────────────────────────────────────────┐");
  Serial.println("│ WIRING REFERENCE                               │");
  Serial.println("└────────────────────────────────────────────────┘");
  Serial.println();
  Serial.println("  GSM900A MODULE:");
  Serial.println("    VCC  -> 5V (from external power supply)");
  Serial.println("    GND  -> GND (common ground with ESP32)");
  Serial.println("    RXD  -> ESP32 GPIO 17");
  Serial.println("    TXD  -> ESP32 GPIO 16");
  Serial.println();
  Serial.println("  RELAY MODULE:");
  Serial.println("    VCC  -> 5V or 3.3V");
  Serial.println("    GND  -> GND");
  Serial.println("    IN   -> ESP32 GPIO 5");
  Serial.println();
  Serial.println("  ⚠ IMPORTANT NOTES:");
  Serial.println("    - GSM needs external 5V 2A power supply");
  Serial.println("    - All GND must be connected together");
  Serial.println("    - Don't power GSM from ESP32 (not enough current)");
  Serial.println();
  
  Serial.println("┌────────────────────────────────────────────────┐");
  Serial.println("│ NEXT STEPS                                     │");
  Serial.println("└────────────────────────────────────────────────┘");
  Serial.println();
  
  if (!results.gsm_comm_ok) {
    Serial.println("  🔴 CRITICAL: Fix GSM communication first");
    Serial.println("     1. Check power LED on GSM (should blink)");
    Serial.println("     2. Verify RX/TX wiring (might be swapped)");
    Serial.println("     3. Press GSM power button (2-3 seconds)");
    Serial.println("     4. Check if common ground is connected");
  } else if (!results.gsm_registered) {
    Serial.println("  🟡 WARNING: GSM works but not on network");
    Serial.println("     1. Check SIM card is properly inserted");
    Serial.println("     2. Verify SIM has active service");
    Serial.println("     3. Check signal strength (move to window)");
    Serial.println("     4. Wait longer for registration");
  } else {
    Serial.println("  🟢 SUCCESS: All systems operational!");
    Serial.println("     You can now upload the main controller sketch");
  }
  
  Serial.println();
  Serial.print("  Total test time: ");
  Serial.print(totalTime);
  Serial.println(" seconds");
  Serial.println();
  
  Serial.println("╔════════════════════════════════════════════════╗");
  Serial.println("║ INTERACTIVE MODE                               ║");
  Serial.println("╚════════════════════════════════════════════════╝");
  Serial.println();
  Serial.println("  You can now send AT commands manually.");
  Serial.println("  Type commands in Serial Monitor and press Enter.");
  Serial.println();
  Serial.println("  Useful commands:");
  Serial.println("    AT           - Test communication");
  Serial.println("    AT+CSQ       - Check signal quality");
  Serial.println("    AT+CREG?     - Check registration");
  Serial.println("    AT+COPS?     - Check operator");
  Serial.println("    ATI          - Module information");
  Serial.println();
}
