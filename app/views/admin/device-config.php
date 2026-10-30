<?php $pageTitle = 'Device Configuration - Smart AC Outlet Controller'; ?>
<?php require_once ROOT_PATH . '/app/views/layouts/header.php'; ?>

<!-- Include Sidebar -->
<?php require_once ROOT_PATH . '/app/views/layouts/admin-sidebar.php'; ?>

<!-- Main Content Wrapper -->
<div class="min-h-screen" style="background-color: #F5F5F7;">
    
    <!-- Top Header Bar -->
    <header class="fixed top-0 left-0 right-0 h-14 sm:h-16 z-50" style="background: rgba(255, 255, 255, 0.80); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border-bottom: 1px solid #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);">
        <div class="h-full flex items-center justify-between px-3 sm:px-4 md:px-6">
            <div class="flex items-center gap-2 sm:gap-3">
                <button type="button" id="sidebar-toggle" class="w-10 h-10 sm:w-11 sm:h-11 flex items-center justify-center rounded-xl transition-all duration-200 active:scale-95" style="background: #2D7A5B; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);" onmouseover="this.style.background='#25694B'" onmouseout="this.style.background='#2D7A5B'">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div class="flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 md:w-10 md:h-10 rounded-xl" style="background: #EDF7F3; border: 1px solid #D4EDDF;">
                    <img src="<?= url('public/assets/images/isu_logo.png') ?>" alt="ISU" class="w-full h-full object-contain p-0.5">
                </div>
                <div>
                    <h1 class="text-xs sm:text-sm md:text-base lg:text-lg font-bold leading-tight" style="color: #1D1D1F;">Smart AC Outlet Control</h1>
                    <p class="text-xs leading-none" style="color: #86868B;">Ilagan Campus</p>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Main Content Area -->
    <main id="main-content" class="transition-all duration-300 ease-in-out mt-14 sm:mt-16 p-4 md:p-6 lg:p-8 min-h-[calc(100vh-3.5rem)] lg:ml-64" style="background-color: #F5F5F7;">
        
        <!-- Page Header -->
        <div class="mb-6 pb-5" style="border-bottom: 1px solid #E5E5E7;">
            <h2 class="text-3xl font-bold flex items-center gap-3" style="color: #1D1D1F;">
                <span class="w-1 h-8 rounded" style="background: linear-gradient(to bottom, #2D7A5B, #1D583C);"></span>
                Device Configuration
            </h2>
            <p class="mt-2 text-sm" style="color: #6E6E73;">ESP32 configuration for <?= htmlspecialchars($device->device_name) ?></p>
        </div>

        <?php if ($show_api_key): ?>
            <div class="mb-6 rounded-xl p-4 flex items-start gap-3" style="background-color: #FEF3C7; border: 1px solid #FDE68A;">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: #92400E;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-semibold mb-1" style="color: #92400E;">IMPORTANT</p>
                    <p class="text-xs" style="color: #92400E;">Save your API key now! It will not be shown again for security reasons.</p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left Column - Configuration -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl p-6 border" style="border-color: #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);">
                    <div class="mb-6 pb-4 flex items-center gap-3" style="border-bottom: 1px solid #E5E5E7;">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #2D7A5B, #25694B);">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold" style="color: #1D1D1F;">ESP32 Configuration</h3>
                    </div>
                    
                    <h4 class="text-sm font-bold mb-4" style="color: #1D1D1F;">Device Information</h4>
                    
                    <!-- Device Name -->
                    <div class="mb-4">
                        <label class="block text-xs font-semibold mb-2" style="color: #86868B;">Device Name</label>
                        <div class="flex gap-2">
                            <input type="text" class="flex-1 px-4 py-2.5 rounded-lg text-sm" style="background-color: #F5F5F7; border: 1px solid #E5E5E7; color: #1D1D1F;" value="<?= htmlspecialchars($device->device_name) ?>" readonly>
                            <button onclick="copyToClipboard('<?= htmlspecialchars(addslashes($device->device_name)) ?>')" class="px-4 py-2.5 rounded-lg text-sm font-semibold transition-all active:scale-95" style="background-color: #F5F5F7; color: #1D1D1F; border: 1px solid #E5E5E7;" onmouseover="this.style.backgroundColor='#E5E5E7'" onmouseout="this.style.backgroundColor='#F5F5F7'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Device ID -->
                    <div class="mb-4">
                        <label class="block text-xs font-semibold mb-2" style="color: #86868B;">Device ID</label>
                        <div class="flex gap-2">
                            <input type="text" class="flex-1 px-4 py-2.5 rounded-lg text-sm font-mono" style="background-color: #F5F5F7; border: 1px solid #E5E5E7; color: #1D1D1F;" value="<?= htmlspecialchars($device->device_id) ?>" readonly>
                            <button onclick="copyToClipboard('<?= htmlspecialchars(addslashes($device->device_id)) ?>')" class="px-4 py-2.5 rounded-lg text-sm font-semibold transition-all active:scale-95" style="background-color: #F5F5F7; color: #1D1D1F; border: 1px solid #E5E5E7;" onmouseover="this.style.backgroundColor='#E5E5E7'" onmouseout="this.style.backgroundColor='#F5F5F7'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- API Key -->
                    <?php if ($show_api_key && $api_key): ?>
                        <div class="mb-6">
                            <label class="block text-xs font-semibold mb-2" style="color: #DC2626;">API Key (Save this now!)</label>
                            <div class="flex gap-2">
                                <input type="text" class="flex-1 px-4 py-2.5 rounded-lg text-sm font-mono" style="background-color: #FEF2F2; border: 1.5px solid #FEE2E2; color: #DC2626;" value="<?= htmlspecialchars($api_key) ?>" readonly>
                                <button onclick="copyToClipboard('<?= htmlspecialchars(addslashes($api_key)) ?>')" class="px-4 py-2.5 rounded-lg text-sm font-semibold transition-all active:scale-95" style="background-color: #DC2626; color: white;" onmouseover="this.style.backgroundColor='#B91C1C'" onmouseout="this.style.backgroundColor='#DC2626'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                            </div>
                            <p class="mt-2 text-xs flex items-center gap-1.5" style="color: #DC2626;">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                This is the only time you'll see this key. Save it securely!
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="mb-6">
                            <label class="block text-xs font-semibold mb-2" style="color: #86868B;">API Key</label>
                            <div class="flex gap-2">
                                <input type="password" class="flex-1 px-4 py-2.5 rounded-lg text-sm font-mono" style="background-color: #F5F5F7; border: 1px solid #E5E5E7; color: #1D1D1F;" value="••••••••••••••••" readonly>
                                <form method="POST" action="<?= url('device/regenerate-api-key/' . $device->id) ?>" class="inline" onsubmit="return confirm('Are you sure? This will invalidate the current API key and your ESP32 will stop working until updated.')">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <button type="submit" class="px-4 py-2.5 rounded-lg text-sm font-semibold transition-all active:scale-95" style="background-color: #FEF3C7; color: #92400E; border: 1px solid #FDE68A;" onmouseover="this.style.backgroundColor='#FDE68A'" onmouseout="this.style.backgroundColor='#FEF3C7'">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                            <p class="mt-2 text-xs" style="color: #86868B;">
                                API key is hidden for security. Click regenerate if you need a new key.
                            </p>
                        </div>
                    <?php endif; ?>

                    <hr class="my-6" style="border-color: #E5E5E7;">

                    <!-- ESP32 Code Configuration -->
                    <h4 class="text-sm font-bold mb-2" style="color: #1D1D1F;">ESP32 Code Configuration</h4>
                    <p class="text-xs mb-4" style="color: #6E6E73;">Copy and paste these values into your ESP32 Arduino code:</p>

                    <div class="rounded-lg overflow-hidden" style="background-color: #1D1D1F; border: 1px solid #2D2D2D;">
                        <pre class="p-4 overflow-x-auto text-xs"><code id="esp32Code" style="color: #E5E5E7; font-family: 'Courier New', monospace;">// WiFi Configuration
const char* ssid = "YOUR_WIFI_SSID";          // Change this
const char* password = "YOUR_WIFI_PASSWORD";  // Change this

// Server Configuration
const char* serverURL = "<?= htmlspecialchars($_ENV['APP_URL'] ?? 'https://isu-documentrequest.online/ac_controller_system') ?>";
const char* deviceId = "<?= htmlspecialchars($device->device_id) ?>";
<?php if ($show_api_key && $api_key): ?>
const char* apiKey = "<?= htmlspecialchars($api_key) ?>";
<?php else: ?>
const char* apiKey = "YOUR_API_KEY_HERE";  // Use your saved API key
<?php endif; ?>

// API Endpoints
String heartbeatEndpoint = String(serverURL) + "/api/device/heartbeat";
String statusEndpoint = String(serverURL) + "/api/device/status";
String commandEndpoint = String(serverURL) + "/api/device/command";</code></pre>
                    </div>
                    <button onclick="copyCode()" class="mt-3 inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-all active:scale-95" style="background-color: #2D7A5B; color: white;" onmouseover="this.style.backgroundColor='#25694B'" onmouseout="this.style.backgroundColor='#2D7A5B'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        Copy Code
                    </button>

                    <hr class="my-6" style="border-color: #E5E5E7;">

                    <!-- Device Status -->
                    <h4 class="text-sm font-bold mb-4" style="color: #1D1D1F;">Device Status</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-semibold mb-2" style="color: #86868B;">Connection</p>
                            <?php if ($device->is_online): ?>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold" style="background-color: #D4EDDF; color: #2D7A5B;">
                                    <span class="w-2 h-2 rounded-full" style="background-color: #2D7A5B;"></span>
                                    Online
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold" style="background-color: #FEE2E2; color: #DC2626;">
                                    <span class="w-2 h-2 rounded-full" style="background-color: #DC2626;"></span>
                                    Offline
                                </span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <p class="text-xs font-semibold mb-2" style="color: #86868B;">Outlet Status</p>
                            <?php if ($device->outlet_status === 'on'): ?>
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold" style="background-color: #FEF3C7; color: #92400E;">ON</span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold" style="background-color: #F5F5F7; color: #6E6E73;">OFF</span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <p class="text-xs font-semibold mb-2" style="color: #86868B;">Last Heartbeat</p>
                            <p class="text-xs" style="color: #1D1D1F;">
                                <?php if ($device->last_heartbeat): ?>
                                    <?= date('M d, Y h:i A', strtotime($device->last_heartbeat)) ?>
                                <?php else: ?>
                                    <span style="color: #86868B;">Never connected</span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold mb-2" style="color: #86868B;">IP Address</p>
                            <p class="text-xs font-mono" style="color: #1D1D1F;">
                                <?= $device->ip_address ? htmlspecialchars($device->ip_address) : '<span style="color: #86868B;">N/A</span>' ?>
                            </p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 justify-between mt-6 pt-6" style="border-top: 1px solid #E5E5E7;">
                        <a href="<?= url('/device') ?>" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-lg text-sm font-semibold transition-all duration-200 active:scale-95" style="background-color: #F5F5F7; color: #1D1D1F; border: 1px solid #E5E5E7;" onmouseover="this.style.backgroundColor='#E5E5E7'" onmouseout="this.style.backgroundColor='#F5F5F7'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to Devices
                        </a>
                        <a href="<?= url('/device/edit/' . $device->id) ?>" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-lg text-sm font-semibold transition-all duration-200 active:scale-95" style="background-color: #2D7A5B; color: white; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);" onmouseover="this.style.backgroundColor='#25694B'" onmouseout="this.style.backgroundColor='#2D7A5B'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Device
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Column - Instructions -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Next Steps Card -->
                <div class="bg-white rounded-xl p-6 border" style="border-color: #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);">
                    <div class="mb-4 pb-3" style="border-bottom: 1px solid #E5E5E7;">
                        <h3 class="text-base font-bold" style="color: #1D1D1F;">Next Steps</h3>
                    </div>
                    
                    <ol class="space-y-4">
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold" style="background-color: #EDF7F3; color: #2D7A5B;">1</span>
                            <div class="flex-1">
                                <p class="text-sm font-semibold mb-1" style="color: #1D1D1F;">Copy the configuration</p>
                                <p class="text-xs" style="color: #6E6E73;">
                                    Copy the code above into your ESP32 Arduino sketch
                                </p>
                            </div>
                        </li>
                        
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold" style="background-color: #EDF7F3; color: #2D7A5B;">2</span>
                            <div class="flex-1">
                                <p class="text-sm font-semibold mb-1" style="color: #1D1D1F;">Update WiFi credentials</p>
                                <p class="text-xs" style="color: #6E6E73;">
                                    Replace YOUR_WIFI_SSID and YOUR_WIFI_PASSWORD with your actual WiFi credentials
                                </p>
                            </div>
                        </li>
                        
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold" style="background-color: #EDF7F3; color: #2D7A5B;">3</span>
                            <div class="flex-1">
                                <p class="text-sm font-semibold mb-1" style="color: #1D1D1F;">Upload to ESP32</p>
                                <p class="text-xs" style="color: #6E6E73;">
                                    Upload the code to your ESP32 device using Arduino IDE
                                </p>
                            </div>
                        </li>
                        
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold" style="background-color: #EDF7F3; color: #2D7A5B;">4</span>
                            <div class="flex-1">
                                <p class="text-sm font-semibold mb-1" style="color: #1D1D1F;">Verify connection</p>
                                <p class="text-xs" style="color: #6E6E73;">
                                    Check the device status on this page or the devices list
                                </p>
                            </div>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
        
    </main>
</div>

<script>
// Copy to clipboard function
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('Copied to clipboard!', 'success');
    }).catch(err => {
        console.error('Failed to copy:', err);
        showToast('Failed to copy', 'error');
    });
}

// Copy code function
function copyCode() {
    const code = document.getElementById('esp32Code').textContent;
    navigator.clipboard.writeText(code).then(() => {
        showToast('Code copied to clipboard!', 'success');
    }).catch(err => {
        console.error('Failed to copy:', err);
        showToast('Failed to copy code', 'error');
    });
}

// Sidebar toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    const overlay = document.getElementById('sidebar-overlay');
    
    if (!sidebar || !mainContent || !overlay) return;
    
    let sidebarOpen = false;
    
    function initializeSidebar() {
        const isDesktop = window.innerWidth >= 1024;
        if (isDesktop) {
            sidebar.style.transform = 'translateX(0)';
            mainContent.classList.add('lg:ml-64');
            overlay.style.opacity = '0';
            overlay.style.visibility = 'hidden';
            sidebarOpen = true;
        } else {
            sidebar.style.transform = 'translateX(-100%)';
            mainContent.classList.remove('lg:ml-64');
            overlay.style.opacity = '0';
            overlay.style.visibility = 'hidden';
            sidebarOpen = false;
        }
    }
    
    function toggleSidebar() {
        const isDesktop = window.innerWidth >= 1024;
        
        if (sidebarOpen) {
            sidebar.style.transform = 'translateX(-100%)';
            if (isDesktop) {
                mainContent.classList.remove('lg:ml-64');
            } else {
                overlay.style.opacity = '0';
                overlay.style.visibility = 'hidden';
            }
            sidebarOpen = false;
        } else {
            sidebar.style.transform = 'translateX(0)';
            if (isDesktop) {
                mainContent.classList.add('lg:ml-64');
            } else {
                overlay.style.opacity = '1';
                overlay.style.visibility = 'visible';
            }
            sidebarOpen = true;
        }
    }
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            toggleSidebar();
        });
    }
    
    if (overlay) {
        overlay.addEventListener('click', function() {
            sidebar.style.transform = 'translateX(-100%)';
            overlay.style.opacity = '0';
            overlay.style.visibility = 'hidden';
            sidebarOpen = false;
        });
    }
    
    window.addEventListener('resize', function() {
        initializeSidebar();
    });
    
    initializeSidebar();
});
</script>

<?php require_once ROOT_PATH . '/app/views/layouts/footer.php'; ?>
