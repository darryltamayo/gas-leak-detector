<?php $pageTitle = 'Register New Device - Smart AC Outlet Controller'; ?>
<?php require_once ROOT_PATH . '/app/views/layouts/header.php'; ?>

<!-- Include Sidebar -->
<?php require_once ROOT_PATH . '/app/views/layouts/admin-sidebar.php'; ?>

<!-- Main Content Wrapper -->
<div class="min-h-screen" style="background-color: #F5F5F7;">
    
    <!-- Top Header Bar (Exact ISU Style) -->
    <header class="fixed top-0 left-0 right-0 h-14 sm:h-16 z-50" style="background: rgba(255, 255, 255, 0.80); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border-bottom: 1px solid #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);">
        <div class="h-full flex items-center justify-between px-3 sm:px-4 md:px-6">
            <div class="flex items-center gap-2 sm:gap-3">
                <!-- Premium Hamburger Button -->
                <button type="button" id="sidebar-toggle" class="w-10 h-10 sm:w-11 sm:h-11 flex items-center justify-center rounded-xl transition-all duration-200 active:scale-95" style="background: #2D7A5B; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);" onmouseover="this.style.background='#25694B'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)'" onmouseout="this.style.background='#2D7A5B'; this.style.boxShadow='0 1px 3px 0 rgba(0, 0, 0, 0.08)'" aria-label="Toggle menu">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <!-- Logo -->
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
        <div class="mb-6 pb-5" style="border-bottom: 1px solid #E5E5E7;">
            <h2 class="text-3xl font-bold flex items-center gap-3" style="color: #1D1D1F;">
                <span class="w-1 h-8 rounded" style="background: linear-gradient(to bottom, #2D7A5B, #1D583C);"></span>
                Register New ESP32 Device
            </h2>
            <p class="mt-2 text-sm" style="color: #6E6E73;">Add a new ESP32 outlet controller to the system</p>
        </div>

        <?php if (isset($_SESSION['device_error'])): ?>
            <div class="mb-6 rounded-xl p-4 flex items-start gap-3" style="background-color: #FEF2F2; border: 1px solid #FEE2E2;">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: #DC2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-semibold" style="color: #DC2626;"><?= $_SESSION['device_error'] ?></p>
                </div>
            </div>
            <?php unset($_SESSION['device_error']); ?>
        <?php endif; ?>

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left Column - Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl p-6 border" style="border-color: #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);">
                    <div class="mb-6 pb-4" style="border-bottom: 1px solid #E5E5E7;">
                        <h3 class="text-lg font-bold" style="color: #1D1D1F;">Device Information</h3>
                    </div>
                    
                    <form method="POST" action="<?= url('device/process-create') ?>">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        
                        <!-- Device Name -->
                        <div class="mb-6">
                            <label for="device_name" class="block text-sm font-semibold mb-2" style="color: #1D1D1F;">
                                Device Name <span style="color: #DC2626;">*</span>
                            </label>
                            <input type="text" 
                                   class="w-full px-4 py-3 rounded-lg text-sm transition-all" 
                                   style="background-color: #F5F5F7; border: 1.5px solid #E5E5E7; color: #1D1D1F;"
                                   onfocus="this.style.borderColor='#2D7A5B'; this.style.backgroundColor='#FFFFFF'" 
                                   onblur="this.style.borderColor='#E5E5E7'; this.style.backgroundColor='#F5F5F7'"
                                   id="device_name" 
                                   name="device_name" 
                                   value="<?= htmlspecialchars($_SESSION['form_data']['device_name'] ?? '') ?>"
                                   placeholder="e.g., Living Room Outlet, Bedroom AC"
                                   required>
                            <p class="mt-2 text-xs" style="color: #86868B;">
                                A friendly name to identify this device
                            </p>
                        </div>

                        <!-- Device ID - Auto Generated Notice -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold mb-2" style="color: #1D1D1F;">
                                Device ID
                            </label>
                            <div class="rounded-lg p-4 flex items-start gap-3" style="background-color: #EFF6FF; border: 1.5px solid #DBEAFE;">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: #1E40AF;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold mb-1" style="color: #1E40AF;">Auto-Generated</p>
                                    <p class="text-xs" style="color: #1E40AF;">
                                        Device ID will be automatically generated (e.g., ESP32_OUTLET_001, ESP32_OUTLET_002, etc.)
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- SIM Card Number -->
                        <div class="mb-6">
                            <label for="sim_card_number" class="block text-sm font-semibold mb-2" style="color: #1D1D1F;">
                                SIM Card Number
                            </label>
                            <input type="text" 
                                   class="w-full px-4 py-3 rounded-lg text-sm transition-all" 
                                   style="background-color: #F5F5F7; border: 1.5px solid #E5E5E7; color: #1D1D1F;"
                                   onfocus="this.style.borderColor='#2D7A5B'; this.style.backgroundColor='#FFFFFF'" 
                                   onblur="this.style.borderColor='#E5E5E7'; this.style.backgroundColor='#F5F5F7'"
                                   id="sim_card_number" 
                                   name="sim_card_number" 
                                   value="<?= htmlspecialchars($_SESSION['form_data']['sim_card_number'] ?? '') ?>"
                                   placeholder="e.g., +639171234567 or 09171234567"
                                   pattern="[\+0-9\-\(\)\s]*"
                                   minlength="10">
                            <p class="mt-2 text-xs" style="color: #86868B;">
                                Optional: Phone number of the SIM card installed in this ESP32 device for SMS control
                            </p>
                        </div>

                        <!-- Firmware Version -->
                        <div class="mb-6">
                            <label for="firmware_version" class="block text-sm font-semibold mb-2" style="color: #1D1D1F;">
                                Firmware Version
                            </label>
                            <input type="text" 
                                   class="w-full px-4 py-3 rounded-lg text-sm transition-all" 
                                   style="background-color: #F5F5F7; border: 1.5px solid #E5E5E7; color: #1D1D1F;"
                                   onfocus="this.style.borderColor='#2D7A5B'; this.style.backgroundColor='#FFFFFF'" 
                                   onblur="this.style.borderColor='#E5E5E7'; this.style.backgroundColor='#F5F5F7'"
                                   id="firmware_version" 
                                   name="firmware_version" 
                                   value="<?= htmlspecialchars($_SESSION['form_data']['firmware_version'] ?? '1.0.0') ?>"
                                   placeholder="e.g., 1.0.0">
                            <p class="mt-2 text-xs" style="color: #86868B;">
                                Optional: Firmware version of the ESP32 device
                            </p>
                        </div>

                        <!-- Info Alert -->
                        <div class="mb-6 rounded-lg p-4 flex items-start gap-3" style="background-color: #EFF6FF; border: 1px solid #DBEAFE;">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: #1E40AF;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm font-semibold mb-1" style="color: #1E40AF;">Note</p>
                                <p class="text-xs" style="color: #1E40AF;">
                                    After registering, you'll receive a unique API key that must be configured in your ESP32 code.
                                </p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3 justify-between">
                            <a href="<?= url('/device') ?>" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-lg text-sm font-semibold transition-all duration-200 active:scale-95" style="background-color: #F5F5F7; color: #1D1D1F; border: 1px solid #E5E5E7;" onmouseover="this.style.backgroundColor='#E5E5E7'" onmouseout="this.style.backgroundColor='#F5F5F7'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Back
                            </a>
                            <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-lg text-sm font-semibold transition-all duration-200 active:scale-95" style="background-color: #2D7A5B; color: white; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);" onmouseover="this.style.backgroundColor='#25694B'" onmouseout="this.style.backgroundColor='#2D7A5B'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Register Device
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column - Instructions & Examples -->
            <div class="lg:col-span-1">
                <!-- Instructions Card -->
                <div class="bg-white rounded-xl p-6 border mb-6" style="border-color: #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);">
                    <div class="mb-4 pb-3" style="border-bottom: 1px solid #E5E5E7;">
                        <h3 class="text-base font-bold" style="color: #1D1D1F;">Instructions</h3>
                    </div>
                    
                    <ol class="space-y-4">
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold" style="background-color: #EDF7F3; color: #2D7A5B;">1</span>
                            <div class="flex-1">
                                <p class="text-sm font-semibold mb-1" style="color: #1D1D1F;">Choose a descriptive name</p>
                                <p class="text-xs" style="color: #6E6E73;">
                                    Use a name that describes the location or purpose (e.g., "Office AC", "Bedroom Outlet")
                                </p>
                            </div>
                        </li>
                        
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold" style="background-color: #EDF7F3; color: #2D7A5B;">2</span>
                            <div class="flex-1">
                                <p class="text-sm font-semibold mb-1" style="color: #1D1D1F;">Device ID auto-generated</p>
                                <p class="text-xs" style="color: #6E6E73;">
                                    System will automatically assign a unique Device ID (ESP32_OUTLET_001, ESP32_OUTLET_002, etc.)
                                </p>
                            </div>
                        </li>
                        
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold" style="background-color: #EDF7F3; color: #2D7A5B;">3</span>
                            <div class="flex-1">
                                <p class="text-sm font-semibold mb-1" style="color: #1D1D1F;">Add SIM card number (optional)</p>
                                <p class="text-xs" style="color: #6E6E73;">
                                    Enter the phone number of the SIM card installed in the ESP32 for SMS control feature
                                </p>
                            </div>
                        </li>
                        
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold" style="background-color: #EDF7F3; color: #2D7A5B;">4</span>
                            <div class="flex-1">
                                <p class="text-sm font-semibold mb-1" style="color: #1D1D1F;">Save the API key</p>
                                <p class="text-xs" style="color: #6E6E73;">
                                    You'll receive an API key and Device ID after registration. Save them securely!
                                </p>
                            </div>
                        </li>
                        
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold" style="background-color: #EDF7F3; color: #2D7A5B;">5</span>
                            <div class="flex-1">
                                <p class="text-sm font-semibold mb-1" style="color: #1D1D1F;">Configure your ESP32</p>
                                <p class="text-xs" style="color: #6E6E73;">
                                    Use the provided Device ID and API key in your ESP32 Arduino code
                                </p>
                            </div>
                        </li>
                    </ol>
                </div>

                <!-- Examples Card -->
                <div class="bg-white rounded-xl p-6 border" style="border-color: #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);">
                    <div class="mb-4 pb-3" style="border-bottom: 1px solid #E5E5E7;">
                        <h3 class="text-base font-bold" style="color: #1D1D1F;">Examples</h3>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-sm font-semibold mb-2" style="color: #1D1D1F;">Device Name:</p>
                        <ul class="space-y-1.5">
                            <li class="flex items-center gap-2 text-xs" style="color: #6E6E73;">
                                <span class="w-1 h-1 rounded-full" style="background-color: #2D7A5B;"></span>
                                Living Room AC
                            </li>
                            <li class="flex items-center gap-2 text-xs" style="color: #6E6E73;">
                                <span class="w-1 h-1 rounded-full" style="background-color: #2D7A5B;"></span>
                                Bedroom Outlet
                            </li>
                            <li class="flex items-center gap-2 text-xs" style="color: #6E6E73;">
                                <span class="w-1 h-1 rounded-full" style="background-color: #2D7A5B;"></span>
                                Office Equipment
                            </li>
                        </ul>
                    </div>
                    
                    <div>
                        <p class="text-sm font-semibold mb-2" style="color: #1D1D1F;">Device ID:</p>
                        <ul class="space-y-1.5">
                            <li class="flex items-center gap-2 text-xs font-mono" style="color: #6E6E73;">
                                <span class="w-1 h-1 rounded-full" style="background-color: #2D7A5B;"></span>
                                ESP32_OUTLET_001
                            </li>
                            <li class="flex items-center gap-2 text-xs font-mono" style="color: #6E6E73;">
                                <span class="w-1 h-1 rounded-full" style="background-color: #2D7A5B;"></span>
                                ESP32_LIVINGROOM
                            </li>
                            <li class="flex items-center gap-2 text-xs font-mono" style="color: #6E6E73;">
                                <span class="w-1 h-1 rounded-full" style="background-color: #2D7A5B;"></span>
                                ESP32_OFFICE_AC
                            </li>
                        </ul>
                    </div>
                </div>
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

<?php unset($_SESSION['form_data']); ?>

<?php require_once ROOT_PATH . '/app/views/layouts/footer.php'; ?>
