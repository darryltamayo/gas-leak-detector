<?php $pageTitle = 'Edit Device - Smart AC Outlet Controller'; ?>
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
                Edit Device
            </h2>
            <p class="mt-2 text-sm" style="color: #6E6E73;">Update ESP32 device information</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="mb-6 rounded-xl p-4 flex items-start gap-3" style="background-color: #FEF2F2; border: 1px solid #FEE2E2;">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: #DC2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-semibold" style="color: #DC2626;"><?= $error ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left Column - Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl p-6 border" style="border-color: #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);">
                    <div class="mb-6 pb-4" style="border-bottom: 1px solid #E5E5E7;">
                        <h3 class="text-lg font-bold" style="color: #1D1D1F;">Device Information</h3>
                    </div>
                    
                    <form method="POST" action="<?= url('device/process-edit/' . ($device->id ?? 'ERROR_NO_ID')) ?>">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        
                        <?php if (!isset($device->id) || !is_numeric($device->id)): ?>
                            <div class="mb-6 rounded-xl p-4 flex items-start gap-3" style="background-color: #FEF2F2; border: 1px solid #FEE2E2;">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: #DC2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold" style="color: #DC2626;">CRITICAL ERROR: Device ID is missing or invalid! Device ID: <?= var_export($device->id ?? 'NOT SET', true) ?></p>
                                </div>
                            </div>
                        <?php endif; ?>>
                        
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
                                   value="<?= htmlspecialchars($device->device_name) ?>"
                                   required>
                        </div>

                        <!-- Device ID - Read Only (Auto-Generated) -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold mb-2" style="color: #1D1D1F;">
                                Device ID
                            </label>
                            <div class="rounded-lg p-4 flex items-start gap-3" style="background-color: #EFF6FF; border: 1.5px solid #DBEAFE;">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: #1E40AF;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold font-mono mb-1" style="color: #1E40AF;"><?= htmlspecialchars($device->device_id) ?></p>
                                    <p class="text-xs" style="color: #1E40AF;">
                                        Device ID is auto-generated and cannot be changed. This ID is used by the ESP32 for authentication.
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
                                   value="<?= htmlspecialchars($device->sim_card_number ?? '') ?>"
                                   placeholder="e.g., +639171234567 or 09171234567"
                                   pattern="[\+0-9\-\(\)\s]*"
                                   minlength="10">
                            <p class="mt-2 text-xs" style="color: #86868B;">
                                Optional: Phone number of the SIM card installed in this ESP32 device
                            </p>
                        </div>

                        <!-- Is Active Checkbox -->
                        <div class="mb-6 rounded-lg p-4" style="background-color: #F5F5F7; border: 1px solid #E5E5E7;">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" 
                                       class="mt-1 w-5 h-5 rounded transition-all cursor-pointer" 
                                       style="accent-color: #2D7A5B;"
                                       id="is_active" 
                                       name="is_active" 
                                       <?= $device->is_active ? 'checked' : '' ?>>
                                <div class="flex-1">
                                    <span class="text-sm font-semibold block mb-1" style="color: #1D1D1F;">Device Active</span>
                                    <span class="text-xs" style="color: #6E6E73;">
                                        Inactive devices cannot be used but remain in the system
                                    </span>
                                </div>
                            </label>
                        </div>

                        <!-- Info Alert -->
                        <div class="mb-6 rounded-lg p-4 flex items-start gap-3" style="background-color: #EFF6FF; border: 1px solid #DBEAFE;">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: #1E40AF;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm font-semibold mb-1" style="color: #1E40AF;">Note</p>
                                <p class="text-xs" style="color: #1E40AF;">
                                    API key cannot be changed here for security. Use "Regenerate API Key" button on the config page.
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Update Device
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column - Current Status -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl p-6 border" style="border-color: #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);">
                    <div class="mb-4 pb-3" style="border-bottom: 1px solid #E5E5E7;">
                        <h3 class="text-base font-bold" style="color: #1D1D1F;">Current Status</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <!-- Connection Status -->
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
                        
                        <!-- Outlet Status -->
                        <div>
                            <p class="text-xs font-semibold mb-2" style="color: #86868B;">Outlet Status</p>
                            <?php if ($device->outlet_status === 'on'): ?>
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold" style="background-color: #FEF3C7; color: #92400E;">
                                    ON
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold" style="background-color: #F5F5F7; color: #6E6E73;">
                                    OFF
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Control Mode -->
                        <div>
                            <p class="text-xs font-semibold mb-2" style="color: #86868B;">Control Mode</p>
                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold" style="background-color: #DBEAFE; color: #1E40AF;">
                                <?= ucfirst($device->control_mode) ?>
                            </span>
                        </div>
                        
                        <!-- Last Heartbeat -->
                        <div>
                            <p class="text-xs font-semibold mb-2" style="color: #86868B;">Last Heartbeat</p>
                            <p class="text-sm" style="color: #1D1D1F;">
                                <?php if ($device->last_heartbeat): ?>
                                    <?= date('M d, Y h:i A', strtotime($device->last_heartbeat)) ?>
                                <?php else: ?>
                                    <span style="color: #86868B;">Never connected</span>
                                <?php endif; ?>
                            </p>
                        </div>
                        
                        <!-- IP Address -->
                        <div>
                            <p class="text-xs font-semibold mb-2" style="color: #86868B;">IP Address</p>
                            <p class="text-sm font-mono" style="color: #1D1D1F;">
                                <?= $device->ip_address ? htmlspecialchars($device->ip_address) : '<span style="color: #86868B;">N/A</span>' ?>
                            </p>
                        </div>
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

<?php require_once ROOT_PATH . '/app/views/layouts/footer.php'; ?>
