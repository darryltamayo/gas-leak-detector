<?php $pageTitle = 'System Documentation - Smart AC Outlet Controller'; ?>
<?php require_once ROOT_PATH . '/app/views/layouts/header.php'; ?>

<!-- Include Sidebar -->
<?php require_once ROOT_PATH . '/app/views/layouts/admin-sidebar.php'; ?>

<!-- Main Content Wrapper -->
<div class="min-h-screen" style="background-color: #F5F5F7;">
    
    <!-- Top Header Bar -->
    <header class="fixed top-0 left-0 right-0 h-14 sm:h-16 z-50" style="background: rgba(255, 255, 255, 0.80); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border-bottom: 1px solid #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);">
        <div class="h-full flex items-center justify-between px-3 sm:px-4 md:px-6">
            <div class="flex items-center gap-2 sm:gap-3">
                <!-- Hamburger Button -->
                <button type="button" id="sidebar-toggle" class="w-10 h-10 sm:w-11 sm:h-11 flex items-center justify-center rounded-xl transition-all duration-200 active:scale-95" style="background: #2D7A5B; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);" onmouseover="this.style.background='#25694B'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)'" onmouseout="this.style.background='#2D7A5B'; this.style.boxShadow='0 1px 3px 0 rgba(0, 0, 0, 0.08)'" aria-label="Toggle menu">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 12h16"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 18h16"/>
                    </svg>
                </button>
                <!-- Logo Container -->
                <div class="flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 md:w-10 md:h-10 rounded-xl" style="background: #EDF7F3; border: 1px solid #D4EDDF;">
                    <img src="<?= url('public/assets/images/isu_logo.png') ?>" alt="ISU" class="w-full h-full object-contain p-0.5" onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22%3E%3Ctext y=%22.9em%22 font-size=%2290%22%3E🎓%3C/text%3E%3C/svg%3E';">
                </div>
                <div>
                    <h1 class="text-xs sm:text-sm md:text-base lg:text-lg font-bold leading-tight" style="color: #1D1D1F;">Smart AC Outlet Control</h1>
                    <p class="text-xs leading-none" style="color: #86868B;">Ilagan Campus</p>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Main Content Area -->
    <main id="main-content" class="transition-all duration-300 ease-in-out mt-14 sm:mt-16 p-4 md:p-6 lg:p-8 min-h-[calc(100vh-3.5rem)] sm:min-h-[calc(100vh-4rem)] lg:ml-64" style="background-color: #F5F5F7;">
        <!-- Page Header -->
        <div class="mb-8 pb-5" style="border-bottom: 1px solid #E5E5E7;">
            <h2 class="text-3xl font-bold flex items-center gap-3" style="color: #1D1D1F;">
                <span class="w-1 h-8 rounded" style="background: linear-gradient(to bottom, #2D7A5B, #1D583C);"></span>
                📚 System Documentation
            </h2>
            <p class="mt-2 text-sm" style="color: #6E6E73;">Complete guide to using the Smart AC Outlet Control System</p>
        </div>
        
        <!-- Quick Navigation -->
        <div class="bg-white rounded-xl p-6 mb-6" style="border: 1px solid #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);">
            <h3 class="text-lg font-bold mb-4" style="color: #1D1D1F;">Quick Navigation</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <a href="#overview" class="flex items-center gap-2 px-4 py-2 rounded-lg transition-all" style="background: #EDF7F3; color: #2D7A5B;" onmouseover="this.style.background='#D4EDDF'" onmouseout="this.style.background='#EDF7F3'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Overview
                </a>
                <a href="#getting-started" class="flex items-center gap-2 px-4 py-2 rounded-lg transition-all" style="background: #EDF7F3; color: #2D7A5B;" onmouseover="this.style.background='#D4EDDF'" onmouseout="this.style.background='#EDF7F3'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Getting Started
                </a>
                <a href="#schedules" class="flex items-center gap-2 px-4 py-2 rounded-lg transition-all" style="background: #EDF7F3; color: #2D7A5B;" onmouseover="this.style.background='#D4EDDF'" onmouseout="this.style.background='#EDF7F3'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Schedules
                </a>
                <a href="#esp32" class="flex items-center gap-2 px-4 py-2 rounded-lg transition-all" style="background: #EDF7F3; color: #2D7A5B;" onmouseover="this.style.background='#D4EDDF'" onmouseout="this.style.background='#EDF7F3'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                    ESP32 Setup
                </a>
            </div>
        </div>

        
        <!-- System Overview -->
        <div id="overview" class="bg-white rounded-xl p-6 mb-6" style="border: 1px solid #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);">
            <h3 class="text-2xl font-bold mb-4 flex items-center gap-2" style="color: #1D1D1F;">
                <svg class="w-7 h-7" style="color: #2D7A5B;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                System Overview
            </h3>
            <p class="mb-4" style="color: #6E6E73;">The Smart AC Outlet Control System allows you to remotely control air conditioning units through ESP32 devices connected to relay modules. The system supports:</p>
            <ul class="space-y-2 ml-6" style="color: #6E6E73;">
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #2D7A5B;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span><strong>Web-based Control:</strong> Turn devices ON/OFF from dashboard</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #2D7A5B;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span><strong>SMS Control:</strong> Control via text messages from authorized numbers</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #2D7A5B;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span><strong>Automated Schedules:</strong> Set daily/weekly ON/OFF times</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #2D7A5B;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span><strong>Multi-Device Support:</strong> Manage multiple ESP32 devices</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #2D7A5B;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span><strong>Activity Logging:</strong> Track all system actions with timestamps</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #2D7A5B;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span><strong>User Management:</strong> Admin and regular user roles</span>
                </li>
            </ul>
        </div>

        
        <!-- Getting Started -->
        <div id="getting-started" class="bg-white rounded-xl p-6 mb-6" style="border: 1px solid #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);">
            <h3 class="text-2xl font-bold mb-4 flex items-center gap-2" style="color: #1D1D1F;">
                <svg class="w-7 h-7" style="color: #2D7A5B;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Getting Started
            </h3>
            
            <div class="space-y-4">
                <div class="bg-blue-50 rounded-lg p-4" style="border: 1px solid #DBEAFE;">
                    <h4 class="font-bold mb-2" style="color: #1E40AF;">Step 1: Login</h4>
                    <p style="color: #1E3A8A;">Access the system at <code class="px-2 py-1 rounded" style="background: white; color: #DC2626;">https://isu-documentrequest.online/ac_controller_system/</code></p>
                    <p class="mt-2" style="color: #1E3A8A;">Use your assigned username and password</p>
                </div>
                
                <div class="bg-green-50 rounded-lg p-4" style="border: 1px solid #D1FAE5;">
                    <h4 class="font-bold mb-2" style="color: #047857;">Step 2: Navigate Dashboard</h4>
                    <p style="color: #065F46;">After login, you'll see the dashboard showing all registered devices, their status (Online/Offline), and quick control buttons</p>
                </div>
                
                <div class="bg-purple-50 rounded-lg p-4" style="border: 1px solid #E9D5FF;">
                    <h4 class="font-bold mb-2" style="color: #7C3AED;">Step 3: Control Devices</h4>
                    <p style="color: #6B21A8;">Go to <strong>Outlet Control</strong> page to turn devices ON/OFF or change control modes (Manual/Scheduled)</p>
                </div>
                
                <div class="bg-yellow-50 rounded-lg p-4" style="border: 1px solid #FEF3C7;">
                    <h4 class="font-bold mb-2" style="color: #D97706;">Step 4: Create Schedules (Optional)</h4>
                    <p style="color: #92400E;">Go to <strong>Schedules</strong> page to create automatic ON/OFF schedules for your devices</p>
                </div>
            </div>
        </div>

        
        <!-- Schedule System -->
        <div id="schedules" class="bg-white rounded-xl p-6 mb-6" style="border: 1px solid #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);">
            <h3 class="text-2xl font-bold mb-4 flex items-center gap-2" style="color: #1D1D1F;">
                <svg class="w-7 h-7" style="color: #2D7A5B;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Schedule System
            </h3>
            
            <div class="mb-4 p-4 rounded-lg" style="background: #FEF2F2; border: 1px solid #FEE2E2;">
                <h4 class="font-bold mb-2" style="color: #DC2626;">⚠️ Important: Control Mode Must Be "Scheduled"</h4>
                <p style="color: #991B1B;">Schedules only work when the device is set to "Scheduled" mode. Go to Outlet Control page and change mode to "Scheduled"</p>
            </div>
            
            <h4 class="font-bold text-lg mb-3" style="color: #1D1D1F;">How Schedules Work:</h4>
            <div class="space-y-3 mb-4">
                <div class="flex items-start gap-3">
                    <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full text-white text-xs font-bold" style="background: #2D7A5B;">1</span>
                    <p style="color: #6E6E73;"><strong>Create Schedule:</strong> Set Turn ON and Turn OFF times for a device</p>
                </div>
                <div class="flex items-start gap-3">
                    <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full text-white text-xs font-bold" style="background: #2D7A5B;">2</span>
                    <p style="color: #6E6E73;"><strong>Enable Schedule:</strong> Toggle the schedule ON (● Active)</p>
                </div>
                <div class="flex items-start gap-3">
                    <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full text-white text-xs font-bold" style="background: #2D7A5B;">3</span>
                    <p style="color: #6E6E73;"><strong>Set Mode:</strong> Change device control mode to "Scheduled"</p>
                </div>
                <div class="flex items-start gap-3">
                    <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full text-white text-xs font-bold" style="background: #2D7A5B;">4</span>
                    <p style="color: #6E6E73;"><strong>Automatic Control:</strong> Device turns ON/OFF automatically at scheduled times</p>
                </div>
            </div>
            
            <h4 class="font-bold text-lg mb-3" style="color: #1D1D1F;">📱 Offline Device Scheduling:</h4>
            <div class="bg-green-50 rounded-lg p-4" style="border: 1px solid #D1FAE5;">
                <p class="mb-2" style="color: #065F46;"><strong>Yes, you can schedule offline devices!</strong></p>
                <ul class="space-y-2 ml-4" style="color: #047857;">
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #10B981;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Schedule is saved even if device is offline</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #10B981;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>When device comes online, it fetches current schedule</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #10B981;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>If device comes online <strong>during ON time</strong>, it turns ON immediately</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #10B981;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>If device comes online <strong>after schedule</strong>, it stays in correct state</span>
                    </li>
                </ul>
            </div>
        </div>

        
        <!-- ESP32 Setup -->
        <div id="esp32" class="bg-white rounded-xl p-6 mb-6" style="border: 1px solid #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);">
            <h3 class="text-2xl font-bold mb-4 flex items-center gap-2" style="color: #1D1D1F;">
                <svg class="w-7 h-7" style="color: #2D7A5B;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                ESP32 Device Setup
            </h3>
            
            <h4 class="font-bold text-lg mb-3" style="color: #1D1D1F;">🔧 Hardware Requirements:</h4>
            <ul class="space-y-2 ml-6 mb-4" style="color: #6E6E73;">
                <li class="flex items-start gap-2">
                    <span class="flex-shrink-0">•</span>
                    <span>ESP32 Development Board</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="flex-shrink-0">•</span>
                    <span>Relay Module (Active LOW - LOW=ON, HIGH=OFF)</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="flex-shrink-0">•</span>
                    <span>SIM900A GSM Module (for SMS control)</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="flex-shrink-0">•</span>
                    <span>5V Power Supply</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="flex-shrink-0">•</span>
                    <span>Jumper Wires</span>
                </li>
            </ul>
            
            <h4 class="font-bold text-lg mb-3" style="color: #1D1D1F;">📝 Configuration Steps:</h4>
            <div class="space-y-3">
                <div class="bg-gray-50 rounded-lg p-4" style="border: 1px solid #E5E5E7;">
                    <h5 class="font-bold mb-2" style="color: #1D1D1F;">Step 1: Update WiFi Credentials</h5>
                    <p class="text-sm mb-2" style="color: #6E6E73;">Edit these lines in the Arduino code:</p>
                    <pre class="bg-gray-900 text-green-400 p-3 rounded text-xs overflow-x-auto">const char* ssid = "Your_WiFi_Name";
const char* password = "Your_WiFi_Password";</pre>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-4" style="border: 1px solid #E5E5E7;">
                    <h5 class="font-bold mb-2" style="color: #1D1D1F;">Step 2: Set Device ID</h5>
                    <p class="text-sm mb-2" style="color: #6E6E73;">Get the Device ID from the admin panel and update:</p>
                    <pre class="bg-gray-900 text-green-400 p-3 rounded text-xs overflow-x-auto">String DEVICE_ID = "ESP32_OUTLET_001";</pre>
                    <p class="text-xs mt-2" style="color: #86868B;">⚠️ Device ID must match exactly with the one in admin panel</p>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-4" style="border: 1px solid #E5E5E7;">
                    <h5 class="font-bold mb-2" style="color: #1D1D1F;">Step 3: Set API Key</h5>
                    <p class="text-sm mb-2" style="color: #6E6E73;">Copy the API key from device configuration:</p>
                    <pre class="bg-gray-900 text-green-400 p-3 rounded text-xs overflow-x-auto">String API_KEY = "IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T";</pre>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-4" style="border: 1px solid #E5E5E7;">
                    <h5 class="font-bold mb-2" style="color: #1D1D1F;">Step 4: Upload Code</h5>
                    <p class="text-sm" style="color: #6E6E73;">Upload the code to ESP32 using Arduino IDE. If upload fails with "Serial data stream stopped" error:</p>
                    <ul class="mt-2 space-y-1 ml-4 text-sm" style="color: #6E6E73;">
                        <li class="flex items-start gap-2">
                            <span>1.</span>
                            <span>Hold <strong>BOOT</strong> button on ESP32</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span>2.</span>
                            <span>Press <strong>RESET</strong> button briefly</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span>3.</span>
                            <span>Release <strong>BOOT</strong> button</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span>4.</span>
                            <span>Click <strong>Upload</strong> in Arduino IDE</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        
        <!-- SMS Control -->
        <div class="bg-white rounded-xl p-6 mb-6" style="border: 1px solid #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);">
            <h3 class="text-2xl font-bold mb-4 flex items-center gap-2" style="color: #1D1D1F;">
                <svg class="w-7 h-7" style="color: #2D7A5B;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                SMS Control Commands
            </h3>
            
            <div class="bg-purple-50 rounded-lg p-4 mb-4" style="border: 1px solid #E9D5FF;">
                <h4 class="font-bold mb-2" style="color: #7C3AED;">📱 SMS Command Format:</h4>
                <p style="color: #6B21A8;">Send SMS from authorized number to the SIM card in ESP32:</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="border rounded-lg p-4" style="border-color: #E5E5E7;">
                    <h5 class="font-bold mb-2" style="color: #2D7A5B;">Turn ON</h5>
                    <pre class="bg-gray-900 text-green-400 p-3 rounded text-sm">ON</pre>
                    <p class="mt-2 text-xs" style="color: #86868B;">Turns the outlet ON</p>
                </div>
                
                <div class="border rounded-lg p-4" style="border-color: #E5E5E7;">
                    <h5 class="font-bold mb-2" style="color: #DC2626;">Turn OFF</h5>
                    <pre class="bg-gray-900 text-green-400 p-3 rounded text-sm">OFF</pre>
                    <p class="mt-2 text-xs" style="color: #86868B;">Turns the outlet OFF</p>
                </div>
                
                <div class="border rounded-lg p-4" style="border-color: #E5E5E7;">
                    <h5 class="font-bold mb-2" style="color: #3B82F6;">Check Status</h5>
                    <pre class="bg-gray-900 text-green-400 p-3 rounded text-sm">STATUS</pre>
                    <p class="mt-2 text-xs" style="color: #86868B;">Returns current outlet status</p>
                </div>
                
                <div class="border rounded-lg p-4" style="border-color: #E5E5E7;">
                    <h5 class="font-bold mb-2" style="color: #8B5CF6;">Get Info</h5>
                    <pre class="bg-gray-900 text-green-400 p-3 rounded text-sm">INFO</pre>
                    <p class="mt-2 text-xs" style="color: #86868B;">Returns device information</p>
                </div>
            </div>
            
            <div class="mt-4 p-4 rounded-lg" style="background: #FEF2F2; border: 1px solid #FEE2E2;">
                <h5 class="font-bold mb-2" style="color: #DC2626;">⚠️ Important:</h5>
                <ul class="space-y-1 text-sm" style="color: #991B1B;">
                    <li>• Only authorized phone numbers can control via SMS</li>
                    <li>• Register numbers in Admin → Authorized SIMs</li>
                    <li>• SMS commands are case-insensitive (ON, on, On all work)</li>
                    <li>• Dashboard syncs automatically when controlled via SMS</li>
                </ul>
            </div>
        </div>

        
        <!-- Activity Logs -->
        <div class="bg-white rounded-xl p-6 mb-6" style="border: 1px solid #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);">
            <h3 class="text-2xl font-bold mb-4 flex items-center gap-2" style="color: #1D1D1F;">
                <svg class="w-7 h-7" style="color: #2D7A5B;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Activity Logs
            </h3>
            
            <p class="mb-4" style="color: #6E6E73;">The system automatically logs all actions including:</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="border rounded-lg p-4" style="border-color: #E5E5E7;">
                    <h5 class="font-bold mb-2 flex items-center gap-2" style="color: #2D7A5B;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        User Management
                    </h5>
                    <ul class="space-y-1 text-sm" style="color: #6E6E73;">
                        <li>• User creation</li>
                        <li>• User updates</li>
                        <li>• User login/logout</li>
                        <li>• Password changes</li>
                    </ul>
                </div>
                
                <div class="border rounded-lg p-4" style="border-color: #E5E5E7;">
                    <h5 class="font-bold mb-2 flex items-center gap-2" style="color: #2D7A5B;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Device Control
                    </h5>
                    <ul class="space-y-1 text-sm" style="color: #6E6E73;">
                        <li>• Outlet ON/OFF</li>
                        <li>• Mode changes</li>
                        <li>• Bulk control</li>
                        <li>• Shows which device</li>
                    </ul>
                </div>
                
                <div class="border rounded-lg p-4" style="border-color: #E5E5E7;">
                    <h5 class="font-bold mb-2 flex items-center gap-2" style="color: #2D7A5B;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        SIM Management
                    </h5>
                    <ul class="space-y-1 text-sm" style="color: #6E6E73;">
                        <li>• SIM registration</li>
                        <li>• SIM updates</li>
                        <li>• Number authorization</li>
                    </ul>
                </div>
                
                <div class="border rounded-lg p-4" style="border-color: #E5E5E7;">
                    <h5 class="font-bold mb-2 flex items-center gap-2" style="color: #2D7A5B;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Schedule Actions
                    </h5>
                    <ul class="space-y-1 text-sm" style="color: #6E6E73;">
                        <li>• Schedule creation</li>
                        <li>• Schedule updates</li>
                        <li>• Automatic execution</li>
                    </ul>
                </div>
            </div>
            
            <div class="mt-4 bg-blue-50 rounded-lg p-4" style="border: 1px solid #DBEAFE;">
                <h5 class="font-bold mb-2" style="color: #1E40AF;">📊 Where to View:</h5>
                <ul class="space-y-1 text-sm" style="color: #1E3A8A;">
                    <li>• <strong>Dashboard:</strong> Shows last 5 activities (admin only)</li>
                    <li>• <strong>Admin → Activity Logs:</strong> Full history with 50 logs per page</li>
                    <li>• <strong>Includes:</strong> Timestamp, User, Device, Action, Description</li>
                </ul>
            </div>
        </div>

        
        <!-- Troubleshooting -->
        <div class="bg-white rounded-xl p-6 mb-6" style="border: 1px solid #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);">
            <h3 class="text-2xl font-bold mb-4 flex items-center gap-2" style="color: #1D1D1F;">
                <svg class="w-7 h-7" style="color: #2D7A5B;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Troubleshooting Guide
            </h3>
            
            <div class="space-y-4">
                <div class="border-l-4 p-4 rounded" style="border-color: #DC2626; background: #FEF2F2;">
                    <h4 class="font-bold mb-2" style="color: #DC2626;">Device Shows Offline</h4>
                    <p class="text-sm mb-2" style="color: #991B1B;">Possible causes:</p>
                    <ul class="space-y-1 text-sm ml-4" style="color: #7F1D1D;">
                        <li>• Check WiFi connection - ensure ESP32 is connected</li>
                        <li>• Verify power supply - ESP32 needs stable 5V power</li>
                        <li>• Check Device ID and API Key match admin panel</li>
                        <li>• Ensure server URL is correct in ESP32 code</li>
                        <li>• Wait 30 seconds - device sends heartbeat every 30s</li>
                    </ul>
                </div>
                
                <div class="border-l-4 p-4 rounded" style="border-color: #F59E0B; background: #FFFBEB;">
                    <h4 class="font-bold mb-2" style="color: #D97706;">Dashboard Control Not Working</h4>
                    <p class="text-sm mb-2" style="color: #92400E;">Check these:</p>
                    <ul class="space-y-1 text-sm ml-4" style="color: #78350F;">
                        <li>• Device must be Online (green dot)</li>
                        <li>• Check if control mode is correct (Manual for manual control)</li>
                        <li>• Wait 5 seconds - ESP32 checks server every 5s</li>
                        <li>• Check activity logs for errors</li>
                    </ul>
                </div>
                
                <div class="border-l-4 p-4 rounded" style="border-color: #3B82F6; background: #EFF6FF;">
                    <h4 class="font-bold mb-2" style="color: #1E40AF;">Schedule Not Executing</h4>
                    <p class="text-sm mb-2" style="color: #1E3A8A;">Checklist:</p>
                    <ul class="space-y-1 text-sm ml-4" style="color: #1E3A8A;">
                        <li>• ✓ Schedule is enabled (toggle ON)</li>
                        <li>• ✓ Control mode is set to "Scheduled"</li>
                        <li>• ✓ Device is online and connected</li>
                        <li>• ✓ Schedule time is correct</li>
                        <li>• ✓ Days of week are selected (for repeating schedules)</li>
                    </ul>
                </div>
                
                <div class="border-l-4 p-4 rounded" style="border-color: #8B5CF6; background: #F5F3FF;">
                    <h4 class="font-bold mb-2" style="color: #7C3AED;">SMS Control Not Responding</h4>
                    <p class="text-sm mb-2" style="color: #6B21A8;">Troubleshoot:</p>
                    <ul class="space-y-1 text-sm ml-4" style="color: #5B21B6;">
                        <li>• Verify SIM card is inserted in GSM module</li>
                        <li>• Check if sender number is authorized</li>
                        <li>• Ensure GSM module has network signal</li>
                        <li>• Try sending STATUS command first</li>
                        <li>• Check GSM module wiring to ESP32</li>
                    </ul>
                </div>
                
                <div class="border-l-4 p-4 rounded" style="border-color: #10B981; background: #F0FDF4;">
                    <h4 class="font-bold mb-2" style="color: #047857;">Relay Not Switching</h4>
                    <p class="text-sm mb-2" style="color: #065F46;">Check hardware:</p>
                    <ul class="space-y-1 text-sm ml-4" style="color: #064E3B;">
                        <li>• Verify relay wiring to GPIO pin</li>
                        <li>• Check if relay module receives power</li>
                        <li>• Test with multimeter if relay clicks</li>
                        <li>• Ensure relay is ACTIVE LOW type</li>
                        <li>• Check AC wiring through relay contacts</li>
                    </ul>
                </div>
            </div>
        </div>

        
        <!-- FAQ -->
        <div class="bg-white rounded-xl p-6 mb-6" style="border: 1px solid #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);">
            <h3 class="text-2xl font-bold mb-4 flex items-center gap-2" style="color: #1D1D1F;">
                <svg class="w-7 h-7" style="color: #2D7A5B;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Frequently Asked Questions
            </h3>
            
            <div class="space-y-4">
                <div class="border-b pb-4" style="border-color: #E5E5E7;">
                    <h4 class="font-bold mb-2" style="color: #1D1D1F;">Q: Can I control multiple devices at once?</h4>
                    <p class="text-sm" style="color: #6E6E73;">A: Yes! Use the bulk control feature on the dashboard. Select multiple devices and use the bulk control buttons to turn them all ON/OFF or change their mode simultaneously.</p>
                </div>
                
                <div class="border-b pb-4" style="border-color: #E5E5E7;">
                    <h4 class="font-bold mb-2" style="color: #1D1D1F;">Q: What happens if the device loses internet during a schedule?</h4>
                    <p class="text-sm" style="color: #6E6E73;">A: The cron job continues updating the database. When the device reconnects, it immediately checks the current schedule status and adjusts the relay accordingly. If it reconnects during an ON period, it turns ON automatically.</p>
                </div>
                
                <div class="border-b pb-4" style="border-color: #E5E5E7;">
                    <h4 class="font-bold mb-2" style="color: #1D1D1F;">Q: Can regular users override schedules?</h4>
                    <p class="text-sm" style="color: #6E6E73;">A: Regular users can turn devices ON anytime, but cannot turn OFF while a schedule is active. Only admins can override active schedules. This prevents accidental schedule interruptions.</p>
                </div>
                
                <div class="border-b pb-4" style="border-color: #E5E5E7;">
                    <h4 class="font-bold mb-2" style="color: #1D1D1F;">Q: How do I add a new ESP32 device?</h4>
                    <p class="text-sm" style="color: #6E6E73;">A: Go to Admin → Devices → Create Device. The system auto-generates a Device ID (e.g., ESP32_OUTLET_002). Configure the ESP32 with this ID and the provided API key, then upload the code.</p>
                </div>
                
                <div class="border-b pb-4" style="border-color: #E5E5E7;">
                    <h4 class="font-bold mb-2" style="color: #1D1D1F;">Q: What's the difference between Manual and Scheduled mode?</h4>
                    <p class="text-sm mb-2" style="color: #6E6E73;">A:</p>
                    <ul class="text-sm space-y-1 ml-4" style="color: #6E6E73;">
                        <li>• <strong>Manual Mode:</strong> Control only via dashboard/SMS buttons. Schedules are ignored.</li>
                        <li>• <strong>Scheduled Mode:</strong> Automatic control based on active schedules. You can still override if needed.</li>
                    </ul>
                </div>
                
                <div class="pb-4">
                    <h4 class="font-bold mb-2" style="color: #1D1D1F;">Q: Are activity logs stored permanently?</h4>
                    <p class="text-sm" style="color: #6E6E73;">A: Yes, all activity logs are stored in the database indefinitely. You can view full history in Admin → Activity Logs. The dashboard shows only the last 5 activities for quick reference.</p>
                </div>
            </div>
        </div>

        
        <!-- Technical Specifications -->
        <div class="bg-white rounded-xl p-6 mb-6" style="border: 1px solid #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);">
            <h3 class="text-2xl font-bold mb-4 flex items-center gap-2" style="color: #1D1D1F;">
                <svg class="w-7 h-7" style="color: #2D7A5B;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                Technical Specifications
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="border rounded-lg p-4" style="border-color: #E5E5E7;">
                    <h4 class="font-bold mb-3" style="color: #1D1D1F;">System Information</h4>
                    <table class="w-full text-sm">
                        <tr class="border-b" style="border-color: #F5F5F7;">
                            <td class="py-2 font-medium" style="color: #6E6E73;">Platform</td>
                            <td class="py-2" style="color: #1D1D1F;">PHP 8.0+ / MySQL</td>
                        </tr>
                        <tr class="border-b" style="border-color: #F5F5F7;">
                            <td class="py-2 font-medium" style="color: #6E6E73;">Framework</td>
                            <td class="py-2" style="color: #1D1D1F;">Custom MVC</td>
                        </tr>
                        <tr class="border-b" style="border-color: #F5F5F7;">
                            <td class="py-2 font-medium" style="color: #6E6E73;">Database</td>
                            <td class="py-2" style="color: #1D1D1F;">MySQL 5.7+</td>
                        </tr>
                        <tr>
                            <td class="py-2 font-medium" style="color: #6E6E73;">API Key</td>
                            <td class="py-2 text-xs" style="color: #1D1D1F;">IOT_OUTLET_2026...</td>
                        </tr>
                    </table>
                </div>
                
                <div class="border rounded-lg p-4" style="border-color: #E5E5E7;">
                    <h4 class="font-bold mb-3" style="color: #1D1D1F;">ESP32 Configuration</h4>
                    <table class="w-full text-sm">
                        <tr class="border-b" style="border-color: #F5F5F7;">
                            <td class="py-2 font-medium" style="color: #6E6E73;">Heartbeat Interval</td>
                            <td class="py-2" style="color: #1D1D1F;">30 seconds</td>
                        </tr>
                        <tr class="border-b" style="border-color: #F5F5F7;">
                            <td class="py-2 font-medium" style="color: #6E6E73;">Status Check</td>
                            <td class="py-2" style="color: #1D1D1F;">5 seconds</td>
                        </tr>
                        <tr class="border-b" style="border-color: #F5F5F7;">
                            <td class="py-2 font-medium" style="color: #6E6E73;">Relay Type</td>
                            <td class="py-2" style="color: #1D1D1F;">Active LOW</td>
                        </tr>
                        <tr>
                            <td class="py-2 font-medium" style="color: #6E6E73;">GPIO Pin</td>
                            <td class="py-2" style="color: #1D1D1F;">Configurable</td>
                        </tr>
                    </table>
                </div>
                
                <div class="border rounded-lg p-4" style="border-color: #E5E5E7;">
                    <h4 class="font-bold mb-3" style="color: #1D1D1F;">API Endpoints</h4>
                    <table class="w-full text-sm">
                        <tr class="border-b" style="border-color: #F5F5F7;">
                            <td class="py-2 font-medium" style="color: #6E6E73;">/api/heartbeat</td>
                            <td class="py-2" style="color: #1D1D1F;">Device ping</td>
                        </tr>
                        <tr class="border-b" style="border-color: #F5F5F7;">
                            <td class="py-2 font-medium" style="color: #6E6E73;">/api/status</td>
                            <td class="py-2" style="color: #1D1D1F;">Get state</td>
                        </tr>
                        <tr>
                            <td class="py-2 font-medium" style="color: #6E6E73;">/api/authorized-sims</td>
                            <td class="py-2" style="color: #1D1D1F;">Get SIM list</td>
                        </tr>
                    </table>
                </div>
                
                <div class="border rounded-lg p-4" style="border-color: #E5E5E7;">
                    <h4 class="font-bold mb-3" style="color: #1D1D1F;">Schedule System</h4>
                    <table class="w-full text-sm">
                        <tr class="border-b" style="border-color: #F5F5F7;">
                            <td class="py-2 font-medium" style="color: #6E6E73;">Cron Frequency</td>
                            <td class="py-2" style="color: #1D1D1F;">Every minute</td>
                        </tr>
                        <tr class="border-b" style="border-color: #F5F5F7;">
                            <td class="py-2 font-medium" style="color: #6E6E73;">Schedule Types</td>
                            <td class="py-2" style="color: #1D1D1F;">Once / Repeat</td>
                        </tr>
                        <tr>
                            <td class="py-2 font-medium" style="color: #6E6E73;">Devices per Schedule</td>
                            <td class="py-2" style="color: #1D1D1F;">1 device</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-xl p-6 text-center" style="border: 1px solid #D1FAE5;">
            <h3 class="text-xl font-bold mb-2" style="color: #1D1D1F;">Need More Help?</h3>
            <p class="mb-4" style="color: #6E6E73;">Contact your system administrator for additional support</p>
            <div class="flex justify-center gap-3">
                <span class="px-4 py-2 rounded-lg font-semibold" style="background: #2D7A5B; color: white;">📧 Support Email</span>
                <span class="px-4 py-2 rounded-lg font-semibold" style="background: #3B82F6; color: white;">📞 Helpdesk</span>
            </div>
        </div>
        
    </main>
</div>

<script>
// Sidebar toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    
    if (!sidebar || !mainContent) return;
    
    // Toggle sidebar
    function toggleSidebar() {
        const isOpen = !sidebar.classList.contains('-translate-x-full');
        
        if (isOpen) {
            sidebar.classList.add('-translate-x-full');
        } else {
            sidebar.classList.remove('-translate-x-full');
        }
    }
    
    // Initialize sidebar state based on screen size
    function initializeSidebar() {
        if (window.innerWidth >= 1024) {
            sidebar.classList.remove('-translate-x-full');
        } else {
            sidebar.classList.add('-translate-x-full');
        }
    }
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            toggleSidebar();
        });
    }
    
    window.addEventListener('resize', function() {
        initializeSidebar();
    });
    
    initializeSidebar();
    
    // Smooth scroll for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
</script>

<?php require_once ROOT_PATH . '/app/views/layouts/footer.php'; ?>
