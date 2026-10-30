/*
 * ========================================================================
 * Universal IoT Outlet Controller for ESP32 with GSM900A
 * ========================================================================
 * 
 * This single sketch works for ALL your ESP32 devices
 * Each device has: ESP32 + GSM900A Module + Single Relay Module
 * 
 * Just configure the device ID and API key for each device
 * 
 * Created for: ISU Smart AC Outlet Control System
 * Server: https://isu-documentrequest.online/ac_controller_system
 * ========================================================================
 */

// ============================================================================
// DEVICE CONFIGURATION - ⚠️ CHANGE THESE FOR EACH DEVICE
// ============================================================================

// Device Identification (GET FROM WEBSITE AFTER REGISTRATION)
const char* DEVICE_ID = "ESP32_OUTLET_001";    // ⚠️ Device 1: ESP32_OUTLET_001
                                                // ⚠️ Device 2: ESP32_OUTLET_002
                                                // ⚠️ Device 3: ESP32_OUTLET_003

const char* API_KEY = "IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T";     // ⚠️ Paste API key from website

// WiFi Credentials (SAME FOR ALL DEVICES)
const char* WIFI_SSID = "Queenie225  2.4G";
const char* WIFI_PASSWORD = "JGutierrez29";

// ============================================================================
// HARDWARE WIRING DIAGRAM
// ============================================================================
/*
 * COMPLETE WIRING FOR EACH ESP32 DEVICE:
 * 
 * ┌────────────────────────────────────────────────────────────┐
 * │                    POWER SUPPLY (5V 2A)                    │
 * │                         +5V   GND                          │
 * └──────────────────────────┬─────┬──────────────────────────┘
 *                            │     │
 *                            │     │
 * ┌──────────────────────────┴─────┴──────────────────────────┐
 * │                       ESP32 DEV BOARD                      │
 * │                                                            │
 * │  VIN  ← +5V from power supply                             │
 * │  GND  ← GND from power supply (common ground)             │
 * │                                                            │
 * │  GPIO 17 (TX2) → GSM RXD                                  │
 * │  GPIO 16 (RX2) ← GSM TXD                                  │
 * │                                                            │
 * │  GPIO 5 → Relay IN                                        │
 * │                                                            │
 * │  GPIO 2 → LED (optional status indicator)                 │
 * └────────────────────────────────────────────────────────────┘
 *
 *
 * ┌────────────────────────────────────────────────────────────┐
 * │                    GSM900A MODULE                          │
 * │                                                            │
 * │  VCC ← ESP32 5V (or direct from power supply 5V)          │
 * │  GND ← ESP32 GND                                          │
 * │  RXD ← ESP32 GPIO 17 (TX2)                                │
 * │  TXD → ESP32 GPIO 16 (RX2)                                │
 * │                                                            │
 * │  [Antenna] ← Connect GSM antenna (REQUIRED!)              │
 * │  [SIM Slot] ← Insert SIM card (PIN disabled)              │
 * └────────────────────────────────────────────────────────────┘
 *
 *
 * ┌────────────────────────────────────────────────────────────┐
 * │                  SINGLE RELAY MODULE                       │
 * │                                                            │
 * │  VCC ← ESP32 5V (or direct from power supply 5V)          │
 * │  GND ← ESP32 GND                                          │
 * │  IN  ← ESP32 GPIO 13                                      │
 * │                                                            │
 * │  COM ← AC Hot Wire In                                     │
 * │  NO  → AC Hot Wire Out (Normal Open - device OFF by default)
 * │  NC  → Not used                                           │
 * └────────────────────────────────────────────────────────────┘
 *
 * IMPORTANT NOTES:
 * ─────────────────────────────────────────────────────────────
 * • All GND pins must be connected together (common ground)
 * • GSM900A needs 5V (NOT 3.3V!) and draws up to 2A
 * • Use 5V 2A power supply minimum
 * • Connect GSM antenna BEFORE powering on
 * • Disable SIM card PIN using phone first
 * • Relay controls AC power - get electrician help if unsure!
 * • Most relays are ACTIVE HIGH (HIGH = ON, LOW = OFF)
 */

// ============================================================================
// PIN DEFINITIONS - DO NOT CHANGE UNLESS WIRING IS DIFFERENT
// ============================================================================

// GSM900A Serial Communication
#define GSM_RX_PIN 16      // ESP32 GPIO16 (RX2) ← GSM TXD
#define GSM_TX_PIN 17      // ESP32 GPIO17 (TX2) → GSM RXD

// Single Relay Control Pin
const int RELAY_PIN = 5;   // GPIO5 → Relay IN

// Status LED Pin
const int LED_PIN = 2;     // Built-in LED (GPIO2)

// ============================================================================
// OUTLET CONFIGURATION
// ============================================================================

const int NUM_OUTLETS = 1;  // Single outlet per device

// ============================================================================
// SERVER CONFIGURATION - DO NOT CHANGE
// ============================================================================

const char* SERVER_URL = "https://isu-documentrequest.online";
const char* SERVER_PATH = "/ac_controller_system";

// API Endpoints (built at runtime)
String heartbeatEndpoint;
String commandEndpoint;

// ============================================================================
// GSM CONFIGURATION
// ============================================================================

// GSM Serial (using UART2 on ESP32)
HardwareSerial gsmSerial(2);  // UART2: RX=GPIO16, TX=GPIO17

// GSM Settings
const char* GSM_APN = "internet";        // Your SIM card APN
const char* GSM_USER = "";               // Usually empty
const char* GSM_PASS = "";               // Usually empty

// SMS Authorization
// Authorized numbers are fetched from server database
// Users register their phone numbers on the website
// Only numbers in the database with is_active=1 can control the device
String authorizedNumbers[10];  // Cache up to 10 authorized numbers
int numAuthorizedNumbers = 0;
unsigned long lastAuthUpdate = 0;
const unsigned long AUTH_UPDATE_INTERVAL = 300000;  // Update every 5 minutes

// ============================================================================
// TIMING CONSTANTS
// ============================================================================

const unsigned long HEARTBEAT_INTERVAL = 30000;      // 30 seconds
const unsigned long COMMAND_CHECK_INTERVAL = 5000;   // 5 seconds
const unsigned long SMS_CHECK_INTERVAL = 10000;      // 10 seconds
const unsigned long WIFI_TIMEOUT = 20000;            // 20 seconds
const unsigned long GSM_TIMEOUT = 10000;             // 10 seconds

// ============================================================================
// GLOBAL VARIABLES
// ============================================================================

// State tracking
bool outletState = false;                   // Current state of the outlet (true=ON, false=OFF)
String controlMode = "manual";              // Control mode: manual, scheduled, automatic
unsigned long lastHeartbeat = 0;
unsigned long lastCommandCheck = 0;
unsigned long lastSMSCheck = 0;
bool wifiConnected = false;
bool gsmConnected = false;

// ============================================================================
// REQUIRED LIBRARIES
// ============================================================================

#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <WiFiClientSecure.h>

// ============================================================================
// FUNCTION PROTOTYPES
// ============================================================================

// WiFi Functions
void setupWiFi();
bool checkWiFi();

// GSM Functions
void setupGSM();
bool checkGSM();
void checkSMS();
void sendSMS(String number, String message);
void processSMSCommand(String sender, String message);
bool isAuthorizedNumber(String number);
void fetchAuthorizedNumbers();  // Fetch from server database

// Hardware Functions
void setupPins();
void setOutlet(bool state);
void toggleOutlet();
bool getOutletState();
void blinkLED(int times, int delayMs);

// Server Communication Functions
void sendHeartbeat();
void checkCommands();

// Utility Functions
void printStatus();
String getOutletStatusString();


// ============================================================================
// SETUP - Runs once when ESP32 starts
// ============================================================================

void setup() {
  Serial.begin(115200);
  delay(1000);
  
  Serial.println("\n\n");
  Serial.println("========================================================================");
  Serial.println("     Universal IoT Outlet Controller with GSM900A");
  Serial.println("========================================================================");
  Serial.print("Device ID: ");
  Serial.println(DEVICE_ID);
  Serial.print("Number of Outlets: ");
  Serial.println(NUM_OUTLETS);
  Serial.println("========================================================================\n");
  
  // Build API endpoints
  heartbeatEndpoint = String(SERVER_URL) + String(SERVER_PATH) + "/api/heartbeat";
  commandEndpoint = String(SERVER_URL) + String(SERVER_PATH) + "/api/status";
  
  // Setup hardware pins
  setupPins();
  
  // Initialize GSM module
  setupGSM();
  
  // Connect to WiFi
  setupWiFi();
  
  // Fetch authorized numbers from database
  if (wifiConnected) {
    fetchAuthorizedNumbers();
  }
  
  // Initial status
  printStatus();
  
  // Blink LED to indicate successful startup
  blinkLED(3, 200);
  
  Serial.println("\n✓ Setup complete! Device is ready.\n");
}

// ============================================================================
// MAIN LOOP - Runs continuously
// ============================================================================

void loop() {
  unsigned long currentTime = millis();
  
  // Check WiFi connection status
  wifiConnected = checkWiFi();
  
  // If WiFi is available, use it for communication
  if (wifiConnected) {
    // Send heartbeat every 30 seconds
    if (currentTime - lastHeartbeat >= HEARTBEAT_INTERVAL) {
      sendHeartbeat();
      lastHeartbeat = currentTime;
    }
    
    // Check for commands every 5 seconds
    if (currentTime - lastCommandCheck >= COMMAND_CHECK_INTERVAL) {
      checkCommands();
      lastCommandCheck = currentTime;
    }
    
    // Update authorized numbers every 5 minutes
    if (currentTime - lastAuthUpdate >= AUTH_UPDATE_INTERVAL) {
      fetchAuthorizedNumbers();
      lastAuthUpdate = currentTime;
    }
  }
  
  // Check GSM for SMS commands every 10 seconds
  if (currentTime - lastSMSCheck >= SMS_CHECK_INTERVAL) {
    checkSMS();
    lastSMSCheck = currentTime;
  }
  
  delay(100);  // Small delay to prevent watchdog timer issues
}

// ============================================================================
// WiFi Functions
// ============================================================================

void setupWiFi() {
  Serial.print("Connecting to WiFi: ");
  Serial.println(WIFI_SSID);
  
  WiFi.mode(WIFI_STA);
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
  
  unsigned long startAttempt = millis();
  
  while (WiFi.status() != WL_CONNECTED && millis() - startAttempt < WIFI_TIMEOUT) {
    delay(500);
    Serial.print(".");
  }
  
  if (WiFi.status() == WL_CONNECTED) {
    wifiConnected = true;
    Serial.println("\n✓ WiFi connected!");
    Serial.print("IP Address: ");
    Serial.println(WiFi.localIP());
    Serial.print("Signal Strength: ");
    Serial.print(WiFi.RSSI());
    Serial.println(" dBm\n");
  } else {
    wifiConnected = false;
    Serial.println("\n✗ WiFi connection failed!");
    Serial.println("Will use GSM for backup communication\n");
  }
}

bool checkWiFi() {
  if (WiFi.status() == WL_CONNECTED) {
    return true;
  } else {
    if (wifiConnected) {
      Serial.println("⚠ WiFi disconnected! Attempting reconnect...");
      setupWiFi();
    }
    return WiFi.status() == WL_CONNECTED;
  }
}

// ============================================================================
// GSM Functions
// ============================================================================

void setupGSM() {
  Serial.println("Initializing GSM900A module...");
  
  // Initialize GSM serial on UART2 (RX=16, TX=17)
  gsmSerial.begin(9600, SERIAL_8N1, GSM_RX_PIN, GSM_TX_PIN);
  delay(3000);
  
  // Test AT command
  Serial.print("Testing GSM module... ");
  gsmSerial.println("AT");
  delay(1000);
  
  if (gsmSerial.available()) {
    String response = gsmSerial.readString();
    if (response.indexOf("OK") >= 0) {
      Serial.println("✓ GSM module responded");
      gsmConnected = true;
      
      // Set SMS text mode
      gsmSerial.println("AT+CMGF=1");
      delay(500);
      
      // Set SMS notification
      gsmSerial.println("AT+CNMI=1,2,0,0,0");
      delay(500);
      
      // Delete all old SMS
      gsmSerial.println("AT+CMGD=1,4");
      delay(1000);
      
      Serial.println("✓ GSM module configured for SMS\n");
    } else {
      Serial.println("✗ GSM module not responding");
      gsmConnected = false;
    }
  } else {
    Serial.println("✗ No response from GSM module");
    Serial.println("  Check wiring: RX→GPIO16, TX→GPIO17, Power→5V\n");
    gsmConnected = false;
  }
}

bool checkGSM() {
  gsmSerial.println("AT");
  delay(500);
  if (gsmSerial.available()) {
    String response = gsmSerial.readString();
    return (response.indexOf("OK") >= 0);
  }
  return false;
}

void checkSMS() {
  if (!gsmConnected) return;
  
  // Check for new SMS
  while (gsmSerial.available()) {
    String smsData = gsmSerial.readString();
    
    // Check if this is a new SMS notification (+CMT)
    if (smsData.indexOf("+CMT:") >= 0) {
      Serial.println("\n📱 New SMS received!");
      
      // Parse sender number
      int firstQuote = smsData.indexOf('"');
      int secondQuote = smsData.indexOf('"', firstQuote + 1);
      String sender = smsData.substring(firstQuote + 1, secondQuote);
      
      // Parse message (after the header line)
      int messageStart = smsData.indexOf('\n', smsData.indexOf("+CMT:"));
      String message = smsData.substring(messageStart + 1);
      message.trim();
      message.toUpperCase();  // Convert to uppercase for easier matching
      
      Serial.print("From: ");
      Serial.println(sender);
      Serial.print("Message: ");
      Serial.println(message);
      
      // Process the command
      processSMSCommand(sender, message);
    }
  }
}

void processSMSCommand(String sender, String message) {
  // Check if sender is authorized
  if (!isAuthorizedNumber(sender)) {
    Serial.println("✗ Unauthorized number!");
    sendSMS(sender, "Access denied. Number not authorized.");
    return;
  }
  
  // Process commands
  if (message.indexOf("STATUS") >= 0) {
    String status = "Device: " + String(DEVICE_ID) + "\n";
    status += "Outlet: " + String(outletState ? "ON" : "OFF") + "\n";
    status += "Mode: " + controlMode;
    sendSMS(sender, status);
    
  } else if (message.indexOf("ON") >= 0) {
    setOutlet(true);
    controlMode = "manual";  // Switch to manual mode when controlled via SMS
    sendSMS(sender, "Outlet turned ON");
    
    // Immediately update server with new state
    Serial.println("📡 Updating server after SMS ON command...");
    sendHeartbeat();
    
  } else if (message.indexOf("OFF") >= 0) {
    setOutlet(false);
    controlMode = "manual";  // Switch to manual mode when controlled via SMS
    sendSMS(sender, "Outlet turned OFF");
    
    // Immediately update server with new state
    Serial.println("📡 Updating server after SMS OFF command...");
    sendHeartbeat();
    
  } else if (message.indexOf("TOGGLE") >= 0) {
    toggleOutlet();
    controlMode = "manual";  // Switch to manual mode when controlled via SMS
    sendSMS(sender, "Outlet toggled to " + String(outletState ? "ON" : "OFF"));
    
    // Immediately update server with new state
    Serial.println("📡 Updating server after SMS TOGGLE command...");
    sendHeartbeat();
    
  } else {
    String help = "Commands:\n";
    help += "STATUS - Check status\n";
    help += "ON - Turn outlet ON\n";
    help += "OFF - Turn outlet OFF\n";
    help += "TOGGLE - Toggle outlet";
    sendSMS(sender, help);
  }
}

void sendSMS(String number, String message) {
  if (!gsmConnected) return;
  
  Serial.print("📤 Sending SMS to ");
  Serial.println(number);
  
  gsmSerial.println("AT+CMGS=\"" + number + "\"");
  delay(500);
  gsmSerial.print(message);
  delay(100);
  gsmSerial.write(26);  // Ctrl+Z to send
  delay(2000);
  
  Serial.println("✓ SMS sent");
}

void fetchAuthorizedNumbers() {
  if (!wifiConnected) return;
  
  Serial.println("📋 Fetching authorized phone numbers from server...");
  
  WiFiClientSecure client;
  client.setInsecure();
  
  HTTPClient http;
  
  // Build URL to fetch authorized SIM numbers
  String url = String(SERVER_URL) + String(SERVER_PATH) + "/api/device/authorized-numbers";
  url += "?device_id=" + String(DEVICE_ID);
  url += "&api_key=" + String(API_KEY);
  
  http.begin(client, url);
  
  int httpCode = http.GET();
  
  if (httpCode == 200) {
    String response = http.getString();
    
    StaticJsonDocument<1024> doc;
    DeserializationError error = deserializeJson(doc, response);
    
    if (!error) {
      numAuthorizedNumbers = 0;
      
      JsonArray numbers = doc["authorized_numbers"];
      for (JsonObject num : numbers) {
        String phoneNumber = num["phone_number"] | "";
        if (phoneNumber.length() > 0 && numAuthorizedNumbers < 10) {
          authorizedNumbers[numAuthorizedNumbers] = phoneNumber;
          numAuthorizedNumbers++;
        }
      }
      
      Serial.print("✓ Loaded ");
      Serial.print(numAuthorizedNumbers);
      Serial.println(" authorized number(s)");
      
      for (int i = 0; i < numAuthorizedNumbers; i++) {
        Serial.print("  - ");
        Serial.println(authorizedNumbers[i]);
      }
    } else {
      Serial.println("✗ Failed to parse authorized numbers");
    }
  } else {
    Serial.print("✗ Failed to fetch authorized numbers: HTTP ");
    Serial.println(httpCode);
  }
  
  http.end();
}

bool isAuthorizedNumber(String number) {
  for (int i = 0; i < numAuthorizedNumbers; i++) {
    if (number.indexOf(authorizedNumbers[i]) >= 0) {
      return true;
    }
  }
  return false;
}

// ============================================================================
// Hardware Functions
// ============================================================================

void setupPins() {
  Serial.println("Setting up pins...");
  
  // Setup relay pin as output
  pinMode(RELAY_PIN, OUTPUT);
  
  // Initialize relay to OFF state
  // For ACTIVE LOW relays: HIGH = OFF, LOW = ON
  digitalWrite(RELAY_PIN, HIGH);  // HIGH = OFF for ACTIVE LOW relay
  outletState = false;
  
  Serial.println("  Single Relay → GPIO 5 (ACTIVE LOW)");
  
  // Setup LED pin
  pinMode(LED_PIN, OUTPUT);
  digitalWrite(LED_PIN, LOW);
  
  Serial.println("✓ Pins configured\n");
}

void setOutlet(bool state) {
  // Set the relay - ACTIVE LOW (most common relay modules)
  // LOW = Relay ON (energized), HIGH = Relay OFF (de-energized)
  digitalWrite(RELAY_PIN, state ? LOW : HIGH);
  outletState = state;
  
  Serial.print("  Outlet → ");
  Serial.println(state ? "ON ⚡" : "OFF");
  
  // Blink LED to indicate action
  blinkLED(1, 100);
}

void toggleOutlet() {
  setOutlet(!outletState);
}

bool getOutletState() {
  return outletState;
}

void blinkLED(int times, int delayMs) {
  for (int i = 0; i < times; i++) {
    digitalWrite(LED_PIN, HIGH);
    delay(delayMs);
    digitalWrite(LED_PIN, LOW);
    delay(delayMs);
  }
}

// ============================================================================
// Server Communication Functions
// ============================================================================


// ============================================================================
// Send Heartbeat to Server
// ============================================================================

void sendHeartbeat() {
  if (WiFi.status() != WL_CONNECTED) {
    return;
  }
  
  WiFiClientSecure client;
  client.setInsecure();  // Skip SSL certificate validation
  
  HTTPClient http;
  http.begin(client, heartbeatEndpoint);
  http.addHeader("Content-Type", "application/json");
  
  // Build JSON payload
  StaticJsonDocument<512> doc;
  doc["device_id"] = DEVICE_ID;
  doc["api_key"] = API_KEY;
  doc["ip_address"] = WiFi.localIP().toString();
  doc["rssi"] = WiFi.RSSI();
  
  // Add outlet state (single outlet)
  JsonArray outlets = doc.createNestedArray("outlet_states");
  JsonObject outlet = outlets.createNestedObject();
  outlet["outlet"] = 1;
  outlet["state"] = outletState ? "on" : "off";
  
  String jsonPayload;
  serializeJson(doc, jsonPayload);
  
  // Send request
  int httpCode = http.POST(jsonPayload);
  
  if (httpCode > 0) {
    if (httpCode == 200) {
      Serial.print("♥ Heartbeat sent successfully");
      
      String response = http.getString();
      StaticJsonDocument<256> responseDoc;
      DeserializationError error = deserializeJson(responseDoc, response);
      
      if (!error && responseDoc.containsKey("control_mode")) {
        controlMode = responseDoc["control_mode"].as<String>();
        Serial.print(" | Mode: ");
        Serial.print(controlMode);
      }
      Serial.println();
    } else {
      Serial.print("⚠ Heartbeat failed: HTTP ");
      Serial.println(httpCode);
    }
  } else {
    Serial.print("✗ Heartbeat error: ");
    Serial.println(http.errorToString(httpCode));
  }
  
  http.end();
}

// ============================================================================
// Check for Commands from Server
// ============================================================================

void checkCommands() {
  if (WiFi.status() != WL_CONNECTED) {
    return;
  }
  
  WiFiClientSecure client;
  client.setInsecure();
  
  HTTPClient http;
  
  // Build URL with parameters
  String url = commandEndpoint + "?device_id=" + String(DEVICE_ID) + "&api_key=" + String(API_KEY);
  http.begin(client, url);
  
  int httpCode = http.GET();
  
  if (httpCode == 200) {
    String response = http.getString();
    
    StaticJsonDocument<512> doc;
    DeserializationError error = deserializeJson(doc, response);
    
    if (!error && doc.containsKey("success") && doc["success"] == true) {
      // Get the desired outlet status from server
      String desiredStatus = doc["outlet_status"] | "off";
      String serverMode = doc["control_mode"] | "manual";
      
      // Update control mode if changed
      if (serverMode != controlMode) {
        controlMode = serverMode;
        Serial.print("→ Mode changed to: ");
        Serial.println(controlMode);
      }
      
      // Check if outlet state needs to change
      bool desiredState = (desiredStatus == "on");
      
      if (desiredState != outletState) {
        // State change needed
        Serial.print("→ Command from server: Turn ");
        Serial.println(desiredState ? "ON" : "OFF");
        
        setOutlet(desiredState);
        
        // Blink LED to indicate command received
        blinkLED(2, 100);
      }
    }
  } else if (httpCode > 0 && httpCode != 404) {
    // Log errors (404 means no status, which is okay)
    Serial.print("⚠ Command check failed: HTTP ");
    Serial.println(httpCode);
  }
  
  http.end();
}


// ============================================================================
// Outlet Control Functions
// ============================================================================

void setOutlet(int outletNum, bool state) {
  // Validate outlet number (only 1 outlet per device)
  if (outletNum != 1) {
    Serial.print("✗ Invalid outlet number: ");
    Serial.println(outletNum);
    return;
  }
  
  // Set the relay - ACTIVE LOW (most common relay modules)
  // LOW = Relay ON (energized), HIGH = Relay OFF (de-energized)
  digitalWrite(RELAY_PIN, state ? LOW : HIGH);
  outletState = state;
  
  Serial.print("  Outlet ");
  Serial.print(outletNum);
  Serial.print(" -> ");
  Serial.println(state ? "ON" : "OFF");
}

void setAllOutlets(bool state) {
  Serial.print("Setting all outlets to: ");
  Serial.println(state ? "ON" : "OFF");
  
  // Only 1 outlet per device
  setOutlet(1, state);
}

bool getOutletState(int outletNum) {
  // Only 1 outlet per device
  if (outletNum != 1) {
    return false;
  }
  return outletState;
}

// ============================================================================
// ============================================================================
// Utility Functions
// ============================================================================

void printStatus() {
  Serial.println("\n========================================================================");
  Serial.println("DEVICE STATUS");
  Serial.println("========================================================================");
  Serial.print("Device ID: ");
  Serial.println(DEVICE_ID);
  Serial.print("WiFi: ");
  Serial.println(wifiConnected ? "✓ Connected" : "✗ Disconnected");
  if (wifiConnected) {
    Serial.print("  IP: ");
    Serial.println(WiFi.localIP());
  }
  Serial.print("GSM: ");
  Serial.println(gsmConnected ? "✓ Ready" : "✗ Not Ready");
  Serial.print("Control Mode: ");
  Serial.println(controlMode);
  Serial.print("\nOutlet Status: ");
  Serial.println(outletState ? "ON ⚡" : "OFF");
  Serial.println("========================================================================\n");
}

String getOutletStatusString() {
  return String("Outlet: ") + (outletState ? "ON" : "OFF");
}

// ============================================================================
// END OF CODE
// ============================================================================
