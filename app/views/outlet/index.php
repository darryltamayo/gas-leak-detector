<?php $pageTitle = 'Outlet Control - Smart AC Outlet Controller'; ?>
<?php require_once ROOT_PATH . '/app/views/layouts/header.php'; ?>

<!-- Include Sidebar for ALL users -->
<?php require_once ROOT_PATH . '/app/views/layouts/admin-sidebar.php'; ?>

<!-- Main Content Wrapper -->
<div class="min-h-screen" style="background-color: #F5F5F7;">
    
    <!-- Top Header Bar (Exact ISU Style) -->
    <header class="fixed top-0 left-0 right-0 h-14 sm:h-16 z-50" style="background: rgba(255, 255, 255, 0.80); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border-bottom: 1px solid #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);">
        <div class="h-full flex items-center justify-between px-3 sm:px-4 md:px-6">
            <div class="flex items-center gap-2 sm:gap-3">
                <!-- Premium Hamburger Button (Exact ISU Style) -->
                <button type="button" id="sidebar-toggle" class="w-10 h-10 sm:w-11 sm:h-11 flex items-center justify-center rounded-xl transition-all duration-200 active:scale-95" style="background: #2D7A5B; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);" onmouseover="this.style.background='#25694B'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)'" onmouseout="this.style.background='#2D7A5B'; this.style.boxShadow='0 1px 3px 0 rgba(0, 0, 0, 0.08)'" aria-label="Toggle menu">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 12h16"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 18h16"/>
                    </svg>
                </button>
                <!-- Premium Logo Container -->
                <div class="flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 md:w-10 md:h-10 rounded-xl" style="background: #EDF7F3; border: 1px solid #D4EDDF;">
                    <img src="<?= url('public/assets/images/isu_logo.png') ?>" alt="ISU" class="w-full h-full object-contain p-0.5" onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22%3E%3Ctext y=%22.9em%22 font-size=%2290%22%3E🎓%3C/text%3E%3C/svg%3E';">
                </div>
                <div>
                    <h1 class="text-xs sm:text-sm md:text-base lg:text-lg font-bold leading-tight" style="color: #1D1D1F;">Smart AC Outlet Control</h1>
                    <p class="text-xs leading-none" style="color: #86868B;">Ilagan Campus</p>
                </div>
            </div>
            
            <!-- User Info (Right side) -->
            <div class="flex items-center gap-2">
                <!-- User info removed for cleaner header -->
            </div>
        </div>
    </header>
    
    <!-- Main Content Area (Exact ISU Style) -->
    <main id="main-content" class="transition-all duration-300 ease-in-out mt-14 sm:mt-16 p-4 md:p-6 lg:p-8 min-h-[calc(100vh-3.5rem)] sm:min-h-[calc(100vh-4rem)] lg:ml-64" style="background-color: #F5F5F7;">
        <!-- Page Header (Exact ISU Style) -->
        <div class="mb-8 pb-5" style="border-bottom: 1px solid #E5E5E7;">
            <div class="flex flex-col gap-4">
                <div>
                    <h2 class="text-3xl font-bold flex items-center gap-3" style="color: #1D1D1F;">
                        <span class="w-1 h-8 rounded" style="background: linear-gradient(to bottom, #2D7A5B, #1D583C);"></span>
                        Outlet Control
                    </h2>
                    <p class="mt-2 text-sm" style="color: #6E6E73;">Control one or more outlet devices</p>
                </div>
                
                <!-- Bulk Control Actions Bar -->
                <div id="bulkActionsOutlet" class="hidden flex flex-wrap items-center gap-2 p-3 rounded-lg" style="background-color: #EDF7F3; border: 1px solid #D4EDDF;">
                    <span class="text-sm font-semibold" style="color: #2D7A5B;">
                        <span id="selectedCountOutlet">0</span> selected
                    </span>
                    <div class="flex flex-wrap gap-2 ml-auto">
                        <button onclick="bulkControlOutlet('on')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 active:scale-95" style="background-color: #2D7A5B; color: white; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);" onmouseover="this.style.backgroundColor='#25694B'" onmouseout="this.style.backgroundColor='#2D7A5B'">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Turn ON
                        </button>
                        <button onclick="bulkControlOutlet('off')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 active:scale-95" style="background-color: #DC2626; color: white; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);" onmouseover="this.style.backgroundColor='#B91C1C'" onmouseout="this.style.backgroundColor='#DC2626'">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Turn OFF
                        </button>
                        <button onclick="bulkSetModeOutlet('manual')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 active:scale-95" style="background-color: #7C3AED; color: white; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);" onmouseover="this.style.backgroundColor='#6D28D9'" onmouseout="this.style.backgroundColor='#7C3AED'">
                            Manual Mode
                        </button>
                        <button onclick="bulkSetModeOutlet('scheduled')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 active:scale-95" style="background-color: #1E40AF; color: white; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);" onmouseover="this.style.backgroundColor='#1E3A8A'" onmouseout="this.style.backgroundColor='#1E40AF'">
                            Scheduled Mode
                        </button>
                        <button onclick="clearSelectionOutlet()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200" style="background-color: #F5F5F7; color: #6E6E73;" onmouseover="this.style.backgroundColor='#E5E5E7'" onmouseout="this.style.backgroundColor='#F5F5F7'">
                            Clear
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- All Devices Grid -->
        <?php if (!empty($devices)): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <?php foreach ($devices as $dev): ?>
            <div class="device-card-selectable-outlet bg-white rounded-xl p-6 border-2 transition-all duration-300 relative overflow-hidden cursor-pointer" 
                 style="border-color: #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);" 
                 data-device-id="<?= $dev->id ?>" 
                 data-device-online="<?= $dev->is_online ? '1' : '0' ?>"
                 onclick="toggleDeviceSelectionOutlet(this, <?= $dev->id ?>)">
                
                <!-- Status Bar -->
                <div class="absolute top-0 left-0 right-0 h-1 transition-all duration-300" style="background: <?= $dev->is_online ? 'linear-gradient(to right, #2D7A5B, #25694B)' : 'linear-gradient(to right, #6E6E73, #86868B)' ?>;"></div>
                
                <!-- Selection Indicator - Top Left -->
                <div class="selection-indicator-outlet absolute top-4 left-4 w-8 h-8 rounded-full flex items-center justify-center transition-all duration-300 opacity-0 scale-75" style="background: linear-gradient(135deg, #2D7A5B, #25694B); box-shadow: 0 4px 12px rgba(45, 122, 91, 0.4);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                
                <!-- Hidden checkbox -->
                <input type="checkbox" class="device-checkbox-outlet hidden" data-device-id="<?= $dev->id ?>">
                
                <!-- Device Header -->
                <div class="text-center mb-6 pt-2">
                    <h3 class="text-lg font-bold mb-2" style="color: #1D1D1F;">
                        <?= htmlspecialchars($dev->device_name) ?>
                    </h3>
                    <p class="text-xs font-mono mb-2" style="color: #86868B;">
                        <?= htmlspecialchars($dev->device_id) ?>
                    </p>
                    <?php if ($dev->sim_card_number): ?>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold" style="background-color: #FEF3C7; color: #92400E;">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-5 14H4v-4h11v4zm0-5H4V9h11v4zm5 5h-4V9h4v9z"/>
                        </svg>
                        <?= htmlspecialchars($dev->sim_card_number) ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Status Circle -->
                <div class="flex justify-center mb-6">
                    <div class="relative">
                        <div class="relative flex items-center justify-center rounded-full" style="height: 120px; width: 120px; background: linear-gradient(135deg, <?= $dev->outlet_status === 'on' ? '#34D399' : '#EF4444' ?> 0%, <?= $dev->outlet_status === 'on' ? '#2D7A5B' : '#DC2626' ?> 100%); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);">
                            <svg class="text-white" style="width: 60px; height: 60px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <?php if ($dev->outlet_status === 'on'): ?>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                <?php else: ?>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                <?php endif; ?>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <!-- Status Text -->
                <div class="text-center mb-4">
                    <div class="text-2xl font-bold mb-1" style="color: <?= $dev->outlet_status === 'on' ? '#2D7A5B' : '#DC2626' ?>;">
                        <?= strtoupper($dev->outlet_status) ?>
                    </div>
                    <div class="flex items-center justify-center gap-2">
                        <?php if ($dev->is_online): ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold" style="background-color: #D4EDDF; color: #2D7A5B;">
                                <span class="w-1.5 h-1.5 rounded-full" style="background-color: #2D7A5B;"></span>
                                Online
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold" style="background-color: #FEE2E2; color: #DC2626;">
                                <span class="w-1.5 h-1.5 rounded-full" style="background-color: #DC2626;"></span>
                                Offline
                            </span>
                        <?php endif; ?>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold" style="background-color: #DBEAFE; color: #1E40AF;">
                            <?= ucfirst($dev->control_mode) ?>
                        </span>
                    </div>
                </div>
                
                <!-- Control Buttons -->
                <div class="grid grid-cols-2 gap-2 mb-3">
                    <button onclick="event.stopPropagation(); controlSingleDevice(<?= $dev->id ?>, 'on', <?= $dev->is_online ? 'true' : 'false' ?>)" 
                            <?= (!$dev->is_online || $dev->outlet_status === 'on') ? 'disabled' : '' ?>
                            class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 <?= ($dev->is_online && $dev->outlet_status !== 'on') ? 'active:scale-95' : 'opacity-50 cursor-not-allowed' ?>" 
                            style="background-color: #2D7A5B; color: white; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);" 
                            <?= ($dev->is_online && $dev->outlet_status !== 'on') ? "onmouseover=\"this.style.backgroundColor='#25694B'\" onmouseout=\"this.style.backgroundColor='#2D7A5B'\"" : '' ?>>
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        ON
                    </button>
                    <button onclick="event.stopPropagation(); controlSingleDevice(<?= $dev->id ?>, 'off', <?= $dev->is_online ? 'true' : 'false' ?>)" 
                            <?= (!$dev->is_online || $dev->outlet_status === 'off') ? 'disabled' : '' ?>
                            class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 <?= ($dev->is_online && $dev->outlet_status !== 'off') ? 'active:scale-95' : 'opacity-50 cursor-not-allowed' ?>" 
                            style="background-color: #DC2626; color: white; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);" 
                            <?= ($dev->is_online && $dev->outlet_status !== 'off') ? "onmouseover=\"this.style.backgroundColor='#B91C1C'\" onmouseout=\"this.style.backgroundColor='#DC2626'\"" : '' ?>>
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        OFF
                    </button>
                </div>
                
                <!-- Control Mode Selector -->
                <div class="mb-4 px-1">
                    <label class="block text-xs font-semibold mb-2" style="color: #6E6E73;">Control Mode</label>
                    <select onchange="event.stopPropagation(); changeDeviceMode(<?= $dev->id ?>, this.value)" 
                            <?= !$dev->is_online ? 'disabled' : '' ?>
                            class="w-full px-3 py-2 rounded-lg text-sm font-medium transition-all cursor-pointer <?= $dev->is_online ? '' : 'opacity-50 cursor-not-allowed' ?>" 
                            style="background-color: #F5F5F7; border: 1.5px solid #E5E5E7; color: #1D1D1F;"
                            onfocus="this.style.borderColor='#2D7A5B'; this.style.backgroundColor='#FFFFFF'" 
                            onblur="this.style.borderColor='#E5E5E7'; this.style.backgroundColor='#F5F5F7'">
                        <option value="manual" <?= $dev->control_mode === 'manual' ? 'selected' : '' ?>>Manual</option>
                        <option value="scheduled" <?= $dev->control_mode === 'scheduled' ? 'selected' : '' ?>>Scheduled</option>
                        <option value="automatic" <?= $dev->control_mode === 'automatic' ? 'selected' : '' ?>>Automatic</option>
                    </select>
                    <p class="mt-1.5 text-xs" style="color: #86868B;">
                        <?php if ($dev->control_mode === 'manual'): ?>
                            <span>💡 Control via buttons or SMS</span>
                        <?php elseif ($dev->control_mode === 'scheduled'): ?>
                            <span>⏰ Automatic control by schedule</span>
                        <?php else: ?>
                            <span>🤖 Force control ignores schedule</span>
                        <?php endif; ?>
                    </p>
                </div>
                
                <?php if (!$dev->is_online): ?>
                <!-- Offline Warning -->
                <div class="mb-3 rounded-lg p-3 flex items-center gap-2" style="background-color: #FEF2F2; border: 1px solid #FEE2E2;">
                    <svg class="w-4 h-4 flex-shrink-0" style="color: #DC2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span class="text-xs font-semibold" style="color: #DC2626;">Device offline - Controls disabled</span>
                </div>
                <?php endif; ?>
                
                <!-- Last Heartbeat -->
                <div class="text-center pt-4" style="border-top: 1px solid #E5E5E7;">
                    <p class="text-xs" style="color: #86868B;">
                        Last seen: 
                        <?php if ($dev->last_heartbeat): ?>
                            <span class="font-semibold" style="color: #6E6E73;"><?= date('M d, h:i A', strtotime($dev->last_heartbeat)) ?></span>
                        <?php else: ?>
                            <span class="italic">Never</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <!-- No Devices -->
        <div class="rounded-2xl p-12 text-center" style="background-color: #FFFFFF; border: 1px solid #E5E5E7;">
            <svg class="mx-auto w-16 h-16" style="color: #86868B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
            </svg>
            <h3 class="mt-4 text-xl font-bold" style="color: #1D1D1F;">No Devices Found</h3>
            <p class="mt-2 text-sm" style="color: #6E6E73;">No devices are currently registered in the system.</p>
        </div>
        <?php endif; ?>

    </div>
</div>

<script>
// Sidebar toggle functionality (Exact ISU Style)
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    const overlay = document.getElementById('sidebar-overlay');
    
    if (!sidebar || !mainContent || !overlay) return;
    
    let sidebarOpen = false;
    
    // Initialize sidebar state based on screen size
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
    
    // Toggle sidebar
    function toggleSidebar() {
        const isDesktop = window.innerWidth >= 1024;
        
        if (sidebarOpen) {
            // Close sidebar
            sidebar.style.transform = 'translateX(-100%)';
            if (isDesktop) {
                mainContent.classList.remove('lg:ml-64');
            } else {
                overlay.style.opacity = '0';
                overlay.style.visibility = 'hidden';
            }
            sidebarOpen = false;
        } else {
            // Open sidebar
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
    
    // Toggle button click
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            toggleSidebar();
        });
    }
    
    // Overlay click (mobile)
    if (overlay) {
        overlay.addEventListener('click', function() {
            sidebar.style.transform = 'translateX(-100%)';
            overlay.style.opacity = '0';
            overlay.style.visibility = 'hidden';
            sidebarOpen = false;
        });
    }
    
    // Handle window resize
    window.addEventListener('resize', function() {
        initializeSidebar();
    });
    
    // Initialize on load
    initializeSidebar();
});

// Initialize button states on page load
document.addEventListener('DOMContentLoaded', function() {
    const currentStatus = '<?= $device->outlet_status ?? 'off' ?>';
    updateButtonStates(currentStatus);
});

const csrfToken = '<?= csrf_token() ?>';

// Show offline alert
function showOfflineAlert() {
    alert('⚠️ Device is currently offline. Please check the device connection and try again.');
}

// Turn ON
async function turnOn() {
    const currentMode = document.getElementById('controlMode').value;
    
    // Warning if in scheduled mode
    if (currentMode === 'scheduled') {
        const confirmed = confirm(
            '⚠️ WARNING: Turning ON Manually\n\n' +
            'You are about to turn ON the outlet manually.\n\n' +
            'This will:\n' +
            '• Switch control mode to "Manual"\n' +
            '• Disable automatic schedule execution\n' +
            '• Require you to manually change mode back to "Scheduled" later\n\n' +
            'Do you want to proceed?'
        );
        
        if (!confirmed) return;
    } else {
        if (!confirm('Turn outlet ON?')) return;
    }
    
    const btnOn = document.getElementById('btnOn');
    const btnOff = document.getElementById('btnOff');
    const originalOnHtml = btnOn.innerHTML;
    
    // Set loading state
    btnOn.disabled = true;
    btnOff.disabled = true;
    btnOn.innerHTML = `
        <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
        <span>Turning ON...</span>
    `;
    
    try {
        const response = await fetch('<?= url('/outlet/turn-on') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'csrf_token=' + csrfToken
        });
        
        const data = await response.json();
        
        if (data.success) {
            updateStatus('on', data.mode);
            updateButtonStates('on');
            showToast(data.message, 'success');
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        showToast('Connection error', 'error');
    } finally {
        // Restore button content
        btnOn.innerHTML = originalOnHtml;
        btnOn.disabled = false;
        btnOff.disabled = false;
    }
}

// Turn OFF
async function turnOff() {
    console.log('[DEBUG] turnOff() called');
    const currentMode = document.getElementById('controlMode').value;
    const currentStatus = document.getElementById('outletStatus').innerText.toLowerCase();
    console.log('[DEBUG] currentMode:', currentMode, 'currentStatus:', currentStatus);
    
    // Check if there's an active schedule running
    if (currentMode === 'scheduled' && currentStatus === 'on') {
        console.log('[DEBUG] Checking for active schedule...');
        // Show detailed warning modal instead of simple confirm
        const scheduleWarning = await checkActiveSchedule();
        console.log('[DEBUG] scheduleWarning:', scheduleWarning);
        
        if (scheduleWarning) {
            // There's an active schedule - show detailed modal
            console.log('[DEBUG] Showing schedule warning modal');
            if (!await showScheduleWarningModal(scheduleWarning)) {
                console.log('[DEBUG] User cancelled via modal');
                return; // User cancelled
            }
        } else {
            // No active schedule, just switching to manual
            const confirmed = confirm(
                '⚠️ WARNING: Turning OFF Manually\n\n' +
                'You are about to turn OFF the outlet manually.\n\n' +
                'This will:\n' +
                '• Switch control mode to "Manual"\n' +
                '• Disable automatic schedule execution\n' +
                '• Require you to manually change mode back to "Scheduled" later\n\n' +
                'Do you want to proceed?'
            );
            
            if (!confirmed) {
                console.log('[DEBUG] User cancelled via confirm');
                return;
            }
        }
    } else {
        console.log('[DEBUG] Simple confirm');
        if (!confirm('Turn outlet OFF?')) {
            console.log('[DEBUG] User cancelled');
            return;
        }
    }
    
    console.log('[DEBUG] Proceeding with turn OFF...');
    const btnOn = document.getElementById('btnOn');
    const btnOff = document.getElementById('btnOff');
    const originalOffHtml = btnOff.innerHTML;
    
    // Set loading state
    btnOn.disabled = true;
    btnOff.disabled = true;
    btnOff.innerHTML = `
        <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
        <span>Turning OFF...</span>
    `;
    
    try {
        const response = await fetch('<?= url('/outlet/turn-off') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'csrf_token=' + csrfToken
        });
        
        const data = await response.json();
        
        if (data.success) {
            updateStatus('off', data.mode);
            updateButtonStates('off');
            showToast(data.message, 'success');
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        showToast('Connection error', 'error');
    } finally {
        // Restore button content
        btnOff.innerHTML = originalOffHtml;
        btnOn.disabled = false;
        btnOff.disabled = false;
    }
}

// Toggle
async function toggleOutlet() {
    const btnOn = document.getElementById('btnOn');
    const btnOff = document.getElementById('btnOff');
    
    // Disable all buttons during toggle
    btnOn.disabled = true;
    btnOff.disabled = true;
    
    try {
        const response = await fetch('<?= url('/outlet/toggle') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'csrf_token=' + csrfToken
        });
        
        const data = await response.json();
        
        if (data.success) {
            updateStatus(data.status, data.mode);
            updateButtonStates(data.status);
            showToast(data.message, 'success');
        } else {
            showToast(data.message, 'error');
            // Re-enable buttons on error
            btnOn.disabled = false;
            btnOff.disabled = false;
        }
    } catch (error) {
        showToast('Connection error', 'error');
        // Re-enable buttons on error
        btnOn.disabled = false;
        btnOff.disabled = false;
    }
}

// Change mode
async function changeMode() {
    const mode = document.getElementById('controlMode').value;
    
    try {
        const response = await fetch('<?= url('/outlet/set-mode') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'csrf_token=' + csrfToken + '&mode=' + mode
        });
        
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('modeText').textContent = mode;
            showToast(data.message, 'success');
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        showToast('Connection error', 'error');
    }
}

// Update UI
function updateStatus(status, mode) {
    document.getElementById('statusText').textContent = status.toUpperCase();
    document.getElementById('modeText').textContent = mode;
    document.getElementById('controlMode').value = mode;
    
    const indicator = document.getElementById('statusIndicator');
    const color = status === 'on' ? '#2D7A5B' : '#D32F2F';
    const gradientStart = status === 'on' ? '#34D399' : '#EF4444';
    
    indicator.innerHTML = `
        <div class="absolute inset-0 rounded-full opacity-20 animate-ping" style="background-color: ${color};"></div>
        <div class="relative flex items-center justify-center rounded-full shadow-2xl" style="height: 192px; width: 192px; background: linear-gradient(135deg, ${gradientStart} 0%, ${color} 100%);">
            <svg class="text-white" style="width: 96px; height: 96px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${status === 'on' 
                    ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />'
                    : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />'
                }
            </svg>
        </div>
    `;
    
    // Update button states based on current status
    updateButtonStates(status);
}

// Update button enabled/disabled states based on current outlet status
function updateButtonStates(status) {
    const btnOn = document.getElementById('btnOn');
    const btnOff = document.getElementById('btnOff');
    
    if (status === 'on') {
        // Outlet is ON - disable ON button, enable OFF button
        btnOn.disabled = true;
        btnOn.classList.add('opacity-50', 'cursor-not-allowed');
        
        btnOff.disabled = false;
        btnOff.classList.remove('opacity-50', 'cursor-not-allowed');
    } else {
        // Outlet is OFF - enable ON button, disable OFF button
        btnOn.disabled = false;
        btnOn.classList.remove('opacity-50', 'cursor-not-allowed');
        
        btnOff.disabled = true;
        btnOff.classList.add('opacity-50', 'cursor-not-allowed');
    }
}

// Loading state
function setButtonsLoading(loading) {
    const buttons = [document.getElementById('btnOn'), document.getElementById('btnOff')];
    buttons.forEach(btn => {
        if (btn) {
            btn.disabled = loading;
            if (loading) {
                btn.innerHTML = btn.innerHTML.replace(/Turn (ON|OFF)/, 'Loading...');
            }
        }
    });
}

// Check if there's an active schedule running right now
async function checkActiveSchedule() {
    try {
        const response = await fetch('<?= url('/outlet/check-active-schedule') ?>', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        });
        
        const data = await response.json();
        
        if (data.has_active_schedule) {
            return data.schedule_info;
        }
        return null;
    } catch (error) {
        console.error('Error checking active schedule:', error);
        return null;
    }
}

// Show schedule warning modal
function showScheduleWarningModal(scheduleInfo) {
    return new Promise((resolve) => {
        // Create modal HTML
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
        modal.id = 'scheduleWarningModal';
        
        const isAdmin = <?= json_encode($user['role'] === 'admin') ?>;
        
        modal.innerHTML = `
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <!-- Icon -->
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    
                    <!-- Title -->
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mt-5 text-center">
                        ⚠️ Active Schedule Running
                    </h3>
                    
                    <!-- Content -->
                    <div class="mt-4 px-4 py-3 bg-yellow-50 rounded-md">
                        <p class="text-sm text-gray-700 mb-3">
                            <strong>There is an active schedule currently running:</strong>
                        </p>
                        
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Schedule:</span>
                                <span class="font-semibold">${scheduleInfo.name}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Created by:</span>
                                <span class="font-semibold">${scheduleInfo.created_by}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Scheduled OFF:</span>
                                <span class="font-semibold">${scheduleInfo.time_off}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 p-3 bg-red-50 border-l-4 border-red-500 rounded">
                        <p class="text-sm text-red-800 font-medium">
                            If you turn OFF manually:
                        </p>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside space-y-1">
                            <li>Control mode will switch to "Manual"</li>
                            <li>All automatic schedules will stop</li>
                            <li>The schedule will not turn back ON/OFF automatically</li>
                            <li>You must manually change mode back to "Scheduled" later</li>
                        </ul>
                    </div>
                    
                    ${isAdmin ? 
                        '<p class="mt-3 text-xs text-gray-500 italic">You are an admin and can override this schedule.</p>' : 
                        '<p class="mt-3 text-xs text-gray-500 italic">You are overriding an active schedule.</p>'
                    }
                    
                    <!-- Buttons -->
                    <div class="items-center gap-3 mt-5 flex flex-col-reverse sm:flex-row">
                        <button 
                            onclick="closeScheduleModal(false)" 
                            class="w-full px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400">
                            Cancel
                        </button>
                        <button 
                            onclick="closeScheduleModal(true)" 
                            class="w-full px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            Yes, Turn OFF Anyway
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Store resolve function globally so button can access it
        window.scheduleModalResolve = resolve;
    });
}

// Close schedule modal
function closeScheduleModal(confirmed) {
    const modal = document.getElementById('scheduleWarningModal');
    if (modal) {
        modal.remove();
    }
    if (window.scheduleModalResolve) {
        window.scheduleModalResolve(confirmed);
        delete window.scheduleModalResolve;
    }
}

// ============ BULK CONTROL FUNCTIONS FOR OUTLET PAGE ============

// Toggle device selection (premium card-based for outlet)
function toggleDeviceSelectionOutlet(cardElement, deviceId) {
    // Prevent event bubbling from action buttons
    if (event.target.closest('button')) {
        return;
    }
    
    const checkbox = cardElement.querySelector('.device-checkbox-outlet');
    const isCurrentlySelected = cardElement.classList.contains('selected');
    
    if (isCurrentlySelected) {
        // Deselect
        cardElement.classList.remove('selected');
        checkbox.checked = false;
    } else {
        // Select
        cardElement.classList.add('selected');
        checkbox.checked = true;
    }
    
    updateBulkActionsOutlet();
}

// Update bulk actions visibility
function updateBulkActionsOutlet() {
    const checkboxes = document.querySelectorAll('.device-checkbox-outlet:checked');
    const bulkActions = document.getElementById('bulkActionsOutlet');
    const selectedCount = document.getElementById('selectedCountOutlet');
    
    if (checkboxes.length > 0) {
        bulkActions.classList.remove('hidden');
        bulkActions.classList.add('flex');
        selectedCount.textContent = checkboxes.length;
    } else {
        bulkActions.classList.add('hidden');
        bulkActions.classList.remove('flex');
    }
}

// Clear all selections
function clearSelectionOutlet() {
    // Clear checkboxes
    document.querySelectorAll('.device-checkbox-outlet').forEach(cb => {
        cb.checked = false;
    });
    // Remove selected class from cards
    document.querySelectorAll('.device-card-selectable-outlet').forEach(card => {
        card.classList.remove('selected');
    });
    updateBulkActionsOutlet();
}

// Change device control mode
async function changeDeviceMode(deviceId, mode) {
    try {
        const response = await fetch('<?= url('/outlet/set-mode') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `csrf_token=<?= csrf_token() ?>&device_id=${deviceId}&mode=${mode}`
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast(`Mode changed to ${mode}`, 'success');
            // Optionally reload to update UI
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message || 'Failed to change mode', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('An error occurred', 'error');
    }
}

// Control single device
async function controlSingleDevice(deviceId, action, isOnline) {
    if (!isOnline) {
        showToast('Device is offline', 'error');
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('csrf_token', csrfToken);
        formData.append('device_id', deviceId);
        formData.append('action', action);
        
        const response = await fetch('<?= url("/outlet/control-device") ?>', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else if (data.requires_confirmation && data.is_admin_override) {
            // Admin needs to confirm override
            const scheduleInfo = data.schedule_info;
            const confirmMessage = `⚠️ ADMIN OVERRIDE WARNING\n\n` +
                `An active schedule is currently running and will be interrupted.\n\n` +
                `Schedule: ${scheduleInfo.name}\n` +
                `Created by: ${scheduleInfo.created_by}\n` +
                `Scheduled end time: ${scheduleInfo.time_off}\n\n` +
                `Are you sure you want to override this schedule?\n\n` +
                `This action will:\n` +
                `• Turn OFF the outlet immediately\n` +
                `• Switch mode to Manual\n` +
                `• Interrupt the scheduled operation`;
            
            if (confirm(confirmMessage)) {
                // User confirmed, retry with confirmation flag
                controlSingleDeviceWithConfirm(deviceId, action, isOnline);
            }
        } else {
            // Check if blocked by schedule
            if (data.blocked_by_schedule && data.schedule_info) {
                const scheduleInfo = data.schedule_info;
                const message = `🔒 Schedule Protection Active\n\n` +
                    `Cannot turn OFF while schedule is running.\n\n` +
                    `Schedule: ${scheduleInfo.name}\n` +
                    `Created by: ${scheduleInfo.created_by}\n` +
                    `Ends at: ${scheduleInfo.time_off}\n\n` +
                    `Only admins can override active schedules.`;
                showToast(message, 'error');
            } else {
                showToast(data.message || 'Failed to control outlet', 'error');
            }
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Network error: ' + error.message, 'error');
    }
}

// Control single device with confirmation (for admin override)
async function controlSingleDeviceWithConfirm(deviceId, action, isOnline) {
    if (!isOnline) {
        showToast('Device is offline', 'error');
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('csrf_token', csrfToken);
        formData.append('device_id', deviceId);
        formData.append('action', action);
        formData.append('confirm_override', '1'); // Confirmation flag
        
        const response = await fetch('<?= url("/outlet/control-device") ?>', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('✅ Admin override: ' + data.message, 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(data.message || 'Failed to override', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Network error: ' + error.message, 'error');
    }
}

// Bulk control devices
async function bulkControlOutlet(action) {
    const checkboxes = document.querySelectorAll('.device-checkbox-outlet:checked');
    if (checkboxes.length === 0) {
        showToast('Please select at least one device', 'error');
        return;
    }
    
    const deviceIds = Array.from(checkboxes).map(cb => cb.dataset.deviceId);
    
    // Check for offline devices
    const offlineDevices = Array.from(checkboxes).filter(cb => {
        const card = document.querySelector(`.device-card-outlet[data-device-id="${cb.dataset.deviceId}"]`);
        return card && card.dataset.deviceOnline === '0';
    });
    
    if (offlineDevices.length > 0) {
        const confirmed = confirm(`${offlineDevices.length} of the selected devices are offline. Continue with online devices only?`);
        if (!confirmed) return;
    }
    
    try {
        const formData = new FormData();
        formData.append('csrf_token', csrfToken);
        formData.append('device_ids', JSON.stringify(deviceIds));
        formData.append('action', action);
        
        const response = await fetch('<?= url("/outlet/bulk-control") ?>', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast(data.message || `Devices turned ${action.toUpperCase()} successfully`, 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(data.message || 'Failed to control devices', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Network error: ' + error.message, 'error');
    }
}

// Bulk set mode
async function bulkSetModeOutlet(mode) {
    const checkboxes = document.querySelectorAll('.device-checkbox-outlet:checked');
    if (checkboxes.length === 0) {
        showToast('Please select at least one device', 'error');
        return;
    }
    
    const deviceIds = Array.from(checkboxes).map(cb => cb.dataset.deviceId);
    
    try {
        const formData = new FormData();
        formData.append('csrf_token', csrfToken);
        formData.append('device_ids', JSON.stringify(deviceIds));
        formData.append('mode', mode);
        
        const response = await fetch('<?= url("/outlet/bulk-set-mode") ?>', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast(data.message || `Mode set to ${mode.toUpperCase()} successfully`, 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(data.message || 'Failed to set mode', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Network error: ' + error.message, 'error');
    }
}
</script>

<style>
/* Premium Card Selection Styles for Outlet */
.device-card-selectable-outlet {
    position: relative;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.device-card-selectable-outlet:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px -8px rgba(45, 122, 91, 0.15) !important;
    border-color: #A7D8B8 !important;
}

.device-card-selectable-outlet.selected {
    border-color: #2D7A5B !important;
    background: linear-gradient(135deg, #FFFFFF 0%, #F0FAF5 100%);
    box-shadow: 0 8px 20px -4px rgba(45, 122, 91, 0.3) !important;
    transform: translateY(-2px);
}

.device-card-selectable-outlet.selected:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 32px -8px rgba(45, 122, 91, 0.35) !important;
}

.device-card-selectable-outlet.selected .selection-indicator-outlet {
    opacity: 1 !important;
    scale: 1 !important;
    animation: selection-pop-outlet 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.device-card-selectable-outlet.selected::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border-radius: 0.75rem;
    background: linear-gradient(135deg, rgba(45, 122, 91, 0.03), rgba(37, 105, 75, 0.06));
    pointer-events: none;
    z-index: 0;
}

.device-card-selectable-outlet > * {
    position: relative;
    z-index: 1;
}

.selection-indicator-outlet {
    transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    z-index: 10;
}

@keyframes selection-pop-outlet {
    0% {
        opacity: 0;
        scale: 0;
        rotate: -180deg;
    }
    60% {
        scale: 1.2;
    }
    100% {
        opacity: 1;
        scale: 1;
        rotate: 0deg;
    }
}

.device-card-selectable-outlet.selected .selection-indicator-outlet {
    animation: selection-pop-outlet 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55), 
               selection-pulse-outlet 2s ease-in-out infinite 0.4s;
}

@keyframes selection-pulse-outlet {
    0%, 100% {
        box-shadow: 0 4px 12px rgba(45, 122, 91, 0.4);
    }
    50% {
        box-shadow: 0 4px 20px rgba(45, 122, 91, 0.6);
    }
}

/* Premium Checkbox Styles */
.premium-checkbox-container {
    display: block;
    position: relative;
    cursor: pointer;
    user-select: none;
    width: 24px;
    height: 24px;
}

.premium-checkbox-container input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
}

.premium-checkmark {
    position: absolute;
    top: 0;
    left: 0;
    height: 24px;
    width: 24px;
    background: white;
    border: 2.5px solid #D4EDDF;
    border-radius: 6px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);
}

.premium-checkbox-container:hover .premium-checkmark {
    border-color: #2D7A5B;
    box-shadow: 0 2px 6px 0 rgba(45, 122, 91, 0.15);
    transform: scale(1.05);
}

.premium-checkbox-container input:checked ~ .premium-checkmark {
    background: linear-gradient(135deg, #2D7A5B, #25694B);
    border-color: #2D7A5B;
    box-shadow: 0 4px 8px 0 rgba(45, 122, 91, 0.25);
    transform: scale(1);
}

.premium-checkmark:after {
    content: "";
    position: absolute;
    display: none;
    left: 7px;
    top: 3px;
    width: 5px;
    height: 10px;
    border: solid white;
    border-width: 0 2.5px 2.5px 0;
    transform: rotate(45deg);
}

.premium-checkbox-container input:checked ~ .premium-checkmark:after {
    display: block;
    animation: checkmark-pop 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

@keyframes checkmark-pop {
    0% {
        transform: rotate(45deg) scale(0);
        opacity: 0;
    }
    50% {
        transform: rotate(45deg) scale(1.2);
    }
    100% {
        transform: rotate(45deg) scale(1);
        opacity: 1;
    }
}
</style>

<?php require_once ROOT_PATH . '/app/views/layouts/footer.php'; ?>
