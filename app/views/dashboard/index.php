<?php $pageTitle = 'Dashboard - Smart AC Outlet Controller'; ?>
<?php require_once ROOT_PATH . '/app/views/layouts/header.php'; ?>

<!-- Include Sidebar for ALL users (Admin and Regular) -->
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
            <h2 class="text-3xl font-bold flex items-center gap-3" style="color: #1D1D1F;">
                <span class="w-1 h-8 rounded" style="background: linear-gradient(to bottom, #2D7A5B, #1D583C);"></span>
                <?= $user['role'] === 'admin' ? 'Administrator Dashboard' : 'User Dashboard' ?>
            </h2>
        </div>
        <!-- Statistics Cards Grid (Exact ISU Style) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6 md:mb-8">
            <?php if ($user['role'] === 'admin'): ?>
            <!-- Total Users -->
            <div class="bg-white rounded-xl p-4 md:p-6 border transition-all duration-300 relative overflow-hidden group" style="border-color: #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.03);" onmouseover="this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.06), 0 2px 4px -2px rgba(0, 0, 0, 0.04)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.boxShadow='0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.03)'; this.style.transform='translateY(0)';">
                <div class="absolute top-3 md:top-5 right-3 md:right-5 opacity-30 group-hover:scale-110 transition-transform" style="color: #D4EDDF;">
                    <svg class="w-8 h-8 md:w-12 md:h-12" fill="currentColor" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div class="text-xs uppercase tracking-wide font-semibold mb-2 md:mb-3" style="color: #86868B;">Total Users</div>
                <div class="text-2xl md:text-4xl font-bold mb-1 md:mb-2" style="color: #2D7A5B;"><?= $stats['total_users'] ?? 0 ?></div>
                <div class="text-xs md:text-sm font-semibold" style="color: #6E6E73;">System Accounts</div>
                <div class="absolute top-0 left-0 right-0 h-1 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left" style="background: linear-gradient(to right, #2D7A5B, #25694B);"></div>
            </div>
            
            <!-- Active Users -->
            <div class="bg-white rounded-xl p-4 md:p-6 border transition-all duration-300 relative overflow-hidden group" style="border-color: #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.03);" onmouseover="this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.06), 0 2px 4px -2px rgba(0, 0, 0, 0.04)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.boxShadow='0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.03)'; this.style.transform='translateY(0)';">
                <div class="absolute top-3 md:top-5 right-3 md:right-5 opacity-30 group-hover:scale-110 transition-transform" style="color: #D4EDDF;">
                    <svg class="w-8 h-8 md:w-12 md:h-12" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                </div>
                <div class="text-xs uppercase tracking-wide font-semibold mb-2 md:mb-3" style="color: #86868B;">Active Users</div>
                <div class="text-2xl md:text-4xl font-bold mb-1 md:mb-2" style="color: #2D7A5B;"><?= $stats['active_users'] ?? 0 ?></div>
                <div class="text-xs md:text-sm font-semibold" style="color: #6E6E73;">User Accounts</div>
                <div class="absolute top-0 left-0 right-0 h-1 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left" style="background: linear-gradient(to right, #2D7A5B, #25694B);"></div>
            </div>
            
            <!-- Total SIMs -->
            <div class="bg-white rounded-xl p-4 md:p-6 border transition-all duration-300 relative overflow-hidden group" style="border-color: #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.03);" onmouseover="this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.06), 0 2px 4px -2px rgba(0, 0, 0, 0.04)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.boxShadow='0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.03)'; this.style.transform='translateY(0)';">
                <div class="absolute top-3 md:top-5 right-3 md:right-5 opacity-30 group-hover:scale-110 transition-transform" style="color: #D4EDDF;">
                    <svg class="w-8 h-8 md:w-12 md:h-12" fill="currentColor" viewBox="0 0 24 24"><path d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <div class="text-xs uppercase tracking-wide font-semibold mb-2 md:mb-3" style="color: #86868B;">Authorized SIMs</div>
                <div class="text-2xl md:text-4xl font-bold mb-1 md:mb-2" style="color: #2D7A5B;"><?= $stats['total_sims'] ?? 0 ?></div>
                <div class="text-xs md:text-sm font-semibold" style="color: #6E6E73;">Registered Numbers</div>
                <div class="absolute top-0 left-0 right-0 h-1 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left" style="background: linear-gradient(to right, #2D7A5B, #25694B);"></div>
            </div>
            
            <!-- Active SIMs -->
            <div class="bg-white rounded-xl p-4 md:p-6 border transition-all duration-300 relative overflow-hidden group" style="border-color: #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.03);" onmouseover="this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.06), 0 2px 4px -2px rgba(0, 0, 0, 0.04)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.boxShadow='0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.03)'; this.style.transform='translateY(0)';">
                <div class="absolute top-3 md:top-5 right-3 md:right-5 opacity-30 group-hover:scale-110 transition-transform" style="color: #D4EDDF;">
                    <svg class="w-8 h-8 md:w-12 md:h-12" fill="currentColor" viewBox="0 0 24 24"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                </div>
                <div class="text-xs uppercase tracking-wide font-semibold mb-2 md:mb-3" style="color: #86868B;">Active SIMs</div>
                <div class="text-2xl md:text-4xl font-bold mb-1 md:mb-2" style="color: #2D7A5B;"><?= $stats['active_sims'] ?? 0 ?></div>
                <div class="text-xs md:text-sm font-semibold" style="color: #6E6E73;">Active Numbers</div>
                <div class="absolute top-0 left-0 right-0 h-1 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left" style="background: linear-gradient(to right, #2D7A5B, #25694B);"></div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- All Devices Status Section (Exact ISU Style) -->
        <?php if (!empty($stats['all_devices'])): ?>
        <div class="bg-white rounded-xl p-6 border mb-6 md:mb-8" style="border-color: #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.03);">
            <div class="mb-5 pb-3" style="border-bottom: 1px solid #E5E5E7;">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-bold flex items-center gap-2" style="color: #1D1D1F;">
                        <svg class="w-5 h-5" style="color: #2D7A5B;" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        All Devices Status
                    </h3>
                    <span class="text-xs font-semibold px-3 py-1 rounded-full" style="background-color: #EDF7F3; color: #2D7A5B;">
                        <?= count($stats['all_devices']) ?> Device<?= count($stats['all_devices']) !== 1 ? 's' : '' ?>
                    </span>
                </div>
                
                <!-- Bulk Control Actions -->
                <div id="bulkActions" class="hidden flex flex-wrap items-center gap-2 p-3 rounded-lg" style="background-color: #EDF7F3; border: 1px solid #D4EDDF;">
                    <span class="text-sm font-semibold" style="color: #2D7A5B;">
                        <span id="selectedCount">0</span> selected
                    </span>
                    <div class="flex flex-wrap gap-2 ml-auto">
                        <button onclick="bulkControl('on')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 active:scale-95" style="background-color: #2D7A5B; color: white; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);" onmouseover="this.style.backgroundColor='#25694B'" onmouseout="this.style.backgroundColor='#2D7A5B'">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Turn ON
                        </button>
                        <button onclick="bulkControl('off')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 active:scale-95" style="background-color: #DC2626; color: white; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);" onmouseover="this.style.backgroundColor='#B91C1C'" onmouseout="this.style.backgroundColor='#DC2626'">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Turn OFF
                        </button>
                        <button onclick="bulkSetMode('manual')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 active:scale-95" style="background-color: #7C3AED; color: white; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);" onmouseover="this.style.backgroundColor='#6D28D9'" onmouseout="this.style.backgroundColor='#7C3AED'">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                            Set Manual
                        </button>
                        <button onclick="bulkSetMode('scheduled')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 active:scale-95" style="background-color: #1E40AF; color: white; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);" onmouseover="this.style.backgroundColor='#1E3A8A'" onmouseout="this.style.backgroundColor='#1E40AF'">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Set Scheduled
                        </button>
                        <button onclick="clearSelection()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200" style="background-color: #F5F5F7; color: #6E6E73;" onmouseover="this.style.backgroundColor='#E5E5E7'" onmouseout="this.style.backgroundColor='#F5F5F7'">
                            Clear
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($stats['all_devices'] as $device): ?>
                <div class="device-card-selectable bg-white rounded-xl p-5 border-2 transition-all duration-300 relative overflow-hidden group cursor-pointer" 
                     style="border-color: #E5E5E7;" 
                     data-device-id="<?= $device->id ?>" 
                     data-device-online="<?= $device->is_online ? '1' : '0' ?>"
                     onclick="toggleDeviceSelection(this, <?= $device->id ?>)">
                    
                    <!-- Device Status Indicator Bar -->
                    <div class="absolute top-0 left-0 right-0 h-1 transition-all duration-300" style="background: <?= $device->is_online ? 'linear-gradient(to right, #2D7A5B, #25694B)' : 'linear-gradient(to right, #6E6E73, #86868B)' ?>;"></div>
                    
                    <!-- Selection Indicator - Top Right -->
                    <div class="selection-indicator absolute top-3 right-3 w-8 h-8 rounded-full flex items-center justify-center transition-all duration-300 opacity-0 scale-75" style="background: linear-gradient(135deg, #2D7A5B, #25694B); box-shadow: 0 4px 12px rgba(45, 122, 91, 0.4);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    
                    <!-- Hidden checkbox for form functionality -->
                    <input type="checkbox" class="device-checkbox hidden" data-device-id="<?= $device->id ?>">
                    
                    <!-- Device Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h4 class="text-base font-bold mb-1" style="color: #1D1D1F;">
                                <?= htmlspecialchars($device->device_name) ?>
                            </h4>
                            <p class="text-xs mb-1" style="color: #86868B;">
                                ID: <?= htmlspecialchars($device->device_id) ?>
                            </p>
                            <?php if ($device->sim_card_number): ?>
                            <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg text-xs font-semibold" style="background-color: #FEF3C7; color: #92400E;">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-5 14H4v-4h11v4zm0-5H4V9h11v4zm5 5h-4V9h4v9z"/>
                                </svg>
                                <?= htmlspecialchars($device->sim_card_number) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Online/Offline Badge -->
                        <div class="flex flex-col items-end gap-1">
                            <?php if ($device->is_online): ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold" style="background-color: #D4EDDF; color: #2D7A5B;">
                                    <span class="w-1.5 h-1.5 rounded-full" style="background-color: #2D7A5B;"></span>
                                    Online
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold" style="background-color: #F5F5F7; color: #6E6E73;">
                                    <span class="w-1.5 h-1.5 rounded-full" style="background-color: #6E6E73;"></span>
                                    Offline
                                </span>
                            <?php endif; ?>
                            
                            <!-- Active/Inactive Badge -->
                            <?php if ($device->is_active): ?>
                                <span class="text-xs px-2 py-0.5 rounded" style="background-color: #EDF7F3; color: #2D7A5B;">
                                    Active
                                </span>
                            <?php else: ?>
                                <span class="text-xs px-2 py-0.5 rounded" style="background-color: #FEF2F2; color: #DC2626;">
                                    Inactive
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Device Stats Grid -->
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <!-- Outlet Status -->
                        <div class="rounded-lg p-3" style="background-color: <?= $device->outlet_status === 'on' ? '#D4EDDF' : '#F5F5F7' ?>;">
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="w-4 h-4" style="color: <?= $device->outlet_status === 'on' ? '#2D7A5B' : '#6E6E73' ?>;" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <span class="text-xs font-semibold" style="color: #86868B;">Outlet</span>
                            </div>
                            <div class="text-lg font-bold" style="color: <?= $device->outlet_status === 'on' ? '#2D7A5B' : '#6E6E73' ?>;">
                                <?= strtoupper($device->outlet_status) ?>
                            </div>
                        </div>
                        
                        <!-- Control Mode -->
                        <div class="rounded-lg p-3" style="background-color: #F5F5F7;">
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="w-4 h-4" style="color: #6E6E73;" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                </svg>
                                <span class="text-xs font-semibold" style="color: #86868B;">Mode</span>
                            </div>
                            <div class="text-sm font-bold" style="color: #1D1D1F;">
                                <?= ucfirst($device->control_mode) ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Last Heartbeat -->
                    <div class="flex items-center gap-2 mb-4 pb-4" style="border-bottom: 1px solid #E5E5E7;">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: #86868B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold mb-0.5" style="color: #86868B;">Last Heartbeat</p>
                            <p class="text-xs truncate" style="color: #1D1D1F;">
                                <?php if ($device->last_heartbeat): ?>
                                    <?= date('M d, Y h:i A', strtotime($device->last_heartbeat)) ?>
                                <?php else: ?>
                                    <span style="color: #86868B;">Never connected</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="viewDeviceDetails(<?= $device->id ?>)" 
                                class="flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold transition-all duration-200 active:scale-95" 
                                style="background-color: #EDF7F3; color: #2D7A5B; border: 1px solid #D4EDDF;" 
                                onmouseover="this.style.backgroundColor='#D4EDDF'" 
                                onmouseout="this.style.backgroundColor='#EDF7F3'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            View
                        </button>
                        
                        <button onclick="controlDeviceOutlet(<?= $device->id ?>, '<?= $device->outlet_status === 'on' ? 'off' : 'on' ?>', <?= $device->is_online ? 'true' : 'false' ?>)" 
                                <?= !$device->is_online ? 'disabled' : '' ?>
                                class="flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold transition-all duration-200 <?= $device->is_online ? 'active:scale-95' : 'opacity-50 cursor-not-allowed' ?>" 
                                style="background-color: <?= $device->outlet_status === 'on' ? '#FEE2E2' : '#D4EDDF' ?>; color: <?= $device->outlet_status === 'on' ? '#DC2626' : '#2D7A5B' ?>; border: 1px solid <?= $device->outlet_status === 'on' ? '#FECACA' : '#A7D8B8' ?>;" 
                                <?= $device->is_online ? "onmouseover=\"this.style.backgroundColor='" . ($device->outlet_status === 'on' ? '#FECACA' : '#A7D8B8') . "'\" onmouseout=\"this.style.backgroundColor='" . ($device->outlet_status === 'on' ? '#FEE2E2' : '#D4EDDF') . "'\"" : '' ?>>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Turn <?= $device->outlet_status === 'on' ? 'OFF' : 'ON' ?>
                        </button>
                    </div>
                    
                    <?php if (!$device->is_online): ?>
                    <!-- Offline Warning -->
                    <div class="mt-3 rounded-lg p-2 flex items-center gap-2" style="background-color: #FEF2F2; border: 1px solid #FEE2E2;">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" style="color: #DC2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span class="text-xs font-semibold" style="color: #DC2626;">Device offline</span>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Recent System Activity (Exact ISU Style) -->
        <?php if ($user['role'] === 'admin' && !empty($stats['recent_logs'])): ?>
        <div class="bg-white rounded-xl p-7 border" style="border-color: #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.03);">
            <h3 class="text-lg font-bold mb-5 pb-3" style="border-bottom: 1px solid #E5E5E7; color: #1D1D1F;">
                Recent System Activity
            </h3>
            <div class="overflow-x-auto bg-white rounded-xl border" style="border-color: #E5E5E7;">
                <table class="w-full">
                    <thead style="background-color: #FAFAFA;">
                        <tr>
                            <th class="px-4 py-4 text-left font-bold border-b text-xs uppercase tracking-wider" style="border-color: #E5E5E7; color: #6E6E73;">Timestamp</th>
                            <th class="px-4 py-4 text-left font-bold border-b text-xs uppercase tracking-wider" style="border-color: #E5E5E7; color: #6E6E73;">User</th>
                            <th class="px-4 py-4 text-left font-bold border-b text-xs uppercase tracking-wider" style="border-color: #E5E5E7; color: #6E6E73;">Device</th>
                            <th class="px-4 py-4 text-left font-bold border-b text-xs uppercase tracking-wider" style="border-color: #E5E5E7; color: #6E6E73;">Action</th>
                            <th class="px-4 py-4 text-left font-bold border-b text-xs uppercase tracking-wider" style="border-color: #E5E5E7; color: #6E6E73;">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats['recent_logs'] as $log): ?>
                        <tr class="border-b transition-colors" style="border-color: #E5E5E7;" onmouseover="this.style.backgroundColor='#FAFAFA';" onmouseout="this.style.backgroundColor='transparent';">
                            <td class="px-4 py-4 text-sm" style="color: #1D1D1F;">
                                <?= date('Y-m-d h:i A', strtotime($log['created_at'])) ?>
                            </td>
                            <td class="px-4 py-4 text-sm font-medium" style="color: #1D1D1F;">
                                <?= e($log['username'] ?? 'System') ?>
                                <div class="text-xs" style="color: #86868B;"><?= e($log['role'] ?? 'ADMIN') ?></div>
                            </td>
                            <td class="px-4 py-4 text-sm" style="color: #1D1D1F;">
                                <?php if (!empty($log['device_name'])): ?>
                                    <div class="font-medium" style="color: #2D7A5B;"><?= e($log['device_name']) ?></div>
                                    <div class="text-xs" style="color: #86868B;"><?= e($log['device_identifier']) ?></div>
                                <?php else: ?>
                                    <span style="color: #86868B;">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-4 text-sm" style="color: #1D1D1F;">
                                <?= !empty($log['action_type']) ? ucfirst(str_replace('_', ' ', $log['action_type'])) : '<span style="color: #DC2626;">No action recorded</span>' ?>
                            </td>
                            <td class="px-4 py-4 text-sm" style="color: #1D1D1F;">
                                <?= e($log['action_details']) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </main>
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
        
        // Check current state by looking at actual transform value
        const currentTransform = window.getComputedStyle(sidebar).transform;
        const isCurrentlyHidden = currentTransform === 'none' ? false : sidebar.style.transform === 'translateX(-100%)';
        
        if (!isCurrentlyHidden) {
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
            toggleSidebar(); // Use the toggle function for consistency
        });
    }
    
    // Handle window resize
    window.addEventListener('resize', function() {
        initializeSidebar();
    });
    
    // Initialize on load
    initializeSidebar();
});

// Show device offline alert
function showDeviceOfflineAlert() {
    alert('⚠️ Device is currently offline. Please check the device connection and try again.');
}

// Control outlet function
async function controlOutlet(action) {
    // Check for active schedule before turning OFF
    if (action === 'off') {
        const controlMode = '<?= $stats['control_mode'] ?? "" ?>';
        const outletStatus = '<?= $stats['outlet_status'] ?? "" ?>';
        
        if (controlMode === 'scheduled' && outletStatus === 'on') {
            // Check if there's an active schedule
            const scheduleInfo = await checkActiveSchedule();
            
            if (scheduleInfo) {
                // Show warning modal
                const confirmed = await showScheduleWarningModal(scheduleInfo);
                if (!confirmed) return; // User cancelled
            }
        }
    }
    
    const button = action === 'on' ? document.getElementById('btnTurnOn') : document.getElementById('btnTurnOff');
    const originalText = button.innerHTML;
    
    // Disable button and show loading
    button.disabled = true;
    button.innerHTML = `
        <svg class="animate-spin h-5 w-5 text-white mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    `;
    
    try {
        // Create form data
        const formData = new FormData();
        formData.append('csrf_token', '<?= csrf_token() ?>');
        
        const endpoint = action === 'on' ? '<?= url("/outlet/turn-on") ?>' : '<?= url("/outlet/turn-off") ?>';
        
        const response = await fetch(endpoint, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Show success message
            showToast(data.message, 'success');
            
            // Reload page after 1 second to show updated status
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showToast(data.message || 'Failed to control outlet', 'error');
            button.disabled = false;
            button.innerHTML = originalText;
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Network error: ' + error.message, 'error');
        button.disabled = false;
        button.innerHTML = originalText;
    }
}

// showToast() is now defined in footer.php and used consistently across all pages

// Auto-refresh status every 30 seconds
setInterval(() => {
    window.location.reload();
}, 30000);

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

// View device details - redirect to outlet page with device filter
function viewDeviceDetails(deviceId) {
    window.location.href = '<?= url('/outlet') ?>?device=' + deviceId;
}

// Control specific device outlet
async function controlDeviceOutlet(deviceId, action, isOnline) {
    if (!isOnline) {
        showToast('This device is currently offline. Please check the device connection and try again.', 'error');
        return;
    }
    
    // Find the button to show loading state
    const button = event.currentTarget;
    const originalText = button.innerHTML;
    
    // Disable button and show loading
    button.disabled = true;
    button.innerHTML = `
        <svg class="animate-spin h-4 w-4 text-current mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    `;
    
    try {
        // Create form data
        const formData = new FormData();
        formData.append('csrf_token', '<?= csrf_token() ?>');
        formData.append('device_id', deviceId);
        formData.append('action', action);
        
        const response = await fetch('<?= url("/outlet/control-device") ?>', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Show success message
            showToast(data.message, 'success');
            
            // Reload page after 1 second to show updated status
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else if (data.requires_confirmation && data.is_admin_override) {
            // Admin needs to confirm override
            button.disabled = false;
            button.innerHTML = originalText;
            
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
                controlDeviceOutletWithConfirm(deviceId, action, isOnline);
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
            button.disabled = false;
            button.innerHTML = originalText;
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Network error: ' + error.message, 'error');
        button.disabled = false;
        button.innerHTML = originalText;
    }
}

// Control device with confirmation (for admin override)
async function controlDeviceOutletWithConfirm(deviceId, action, isOnline) {
    if (!isOnline) {
        showToast('Device is offline', 'error');
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('csrf_token', '<?= csrf_token() ?>');
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
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showToast(data.message || 'Failed to override', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Network error: ' + error.message, 'error');
    }
}

// Toggle device selection (premium card-based)
function toggleDeviceSelection(cardElement, deviceId) {
    // Prevent event bubbling from action buttons
    if (event.target.closest('button')) {
        return;
    }
    
    const checkbox = cardElement.querySelector('.device-checkbox');
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
    
    updateBulkActions();
}

// Update bulk actions visibility
function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.device-checkbox:checked');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');
    
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
function clearSelection() {
    // Clear checkboxes
    document.querySelectorAll('.device-checkbox').forEach(cb => {
        cb.checked = false;
    });
    // Remove selected class from cards
    document.querySelectorAll('.device-card-selectable').forEach(card => {
        card.classList.remove('selected');
    });
    updateBulkActions();
}

// Bulk control devices
async function bulkControl(action) {
    const checkboxes = document.querySelectorAll('.device-checkbox:checked');
    if (checkboxes.length === 0) {
        showToast('Please select at least one device', 'error');
        return;
    }
    
    const deviceIds = Array.from(checkboxes).map(cb => cb.dataset.deviceId);
    
    // Check if any offline devices are selected
    const offlineDevices = Array.from(checkboxes).filter(cb => {
        const card = document.querySelector(`.device-card[data-device-id="${cb.dataset.deviceId}"]`);
        return card && card.dataset.deviceOnline === '0';
    });
    
    if (offlineDevices.length > 0) {
        const confirmed = confirm(`${offlineDevices.length} of the selected devices are offline. Continue with online devices only?`);
        if (!confirmed) return;
    }
    
    try {
        const formData = new FormData();
        formData.append('csrf_token', '<?= csrf_token() ?>');
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
async function bulkSetMode(mode) {
    const checkboxes = document.querySelectorAll('.device-checkbox:checked');
    if (checkboxes.length === 0) {
        showToast('Please select at least one device', 'error');
        return;
    }
    
    const deviceIds = Array.from(checkboxes).map(cb => cb.dataset.deviceId);
    
    try {
        const formData = new FormData();
        formData.append('csrf_token', '<?= csrf_token() ?>');
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
/* Premium Card Selection Styles */
.device-card-selectable {
    position: relative;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);
}

.device-card-selectable:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px -8px rgba(45, 122, 91, 0.15);
    border-color: #A7D8B8 !important;
}

.device-card-selectable.selected {
    border-color: #2D7A5B !important;
    background: linear-gradient(135deg, #FFFFFF 0%, #F0FAF5 100%);
    box-shadow: 0 8px 20px -4px rgba(45, 122, 91, 0.3);
    transform: translateY(-2px);
}

.device-card-selectable.selected:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 32px -8px rgba(45, 122, 91, 0.35);
}

.device-card-selectable.selected .selection-indicator {
    opacity: 1 !important;
    scale: 1 !important;
    animation: selection-pop 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.device-card-selectable.selected::before {
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

.device-card-selectable > * {
    position: relative;
    z-index: 1;
}

.selection-indicator {
    transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

@keyframes selection-pop {
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

/* Pulse animation for selected state */
.device-card-selectable.selected .selection-indicator {
    animation: selection-pop 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55), 
               selection-pulse 2s ease-in-out infinite 0.4s;
}

@keyframes selection-pulse {
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
