<?php $pageTitle = 'Activity Logs - Smart AC Outlet Controller'; ?>
<?php require_once ROOT_PATH . '/app/views/layouts/header.php'; ?>

<!-- Include Admin Sidebar if Admin -->
<?php if ($user['role'] === 'admin'): ?>
    <?php require_once ROOT_PATH . '/app/views/layouts/admin-sidebar.php'; ?>
<?php endif; ?>

<!-- Main Content Wrapper -->
<div class="min-h-screen" style="background-color: #F5F5F7;">
    
    <!-- Top Header Bar (Exact ISU Style) -->
    <header class="fixed top-0 left-0 right-0 h-14 sm:h-16 z-50" style="background: rgba(255, 255, 255, 0.80); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border-bottom: 1px solid #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);">
        <div class="h-full flex items-center justify-between px-3 sm:px-4 md:px-6">
            <div class="flex items-center gap-2 sm:gap-3">
                <?php if ($user['role'] === 'admin'): ?>
                <!-- Premium Hamburger Button (Exact ISU Style) -->
                <button type="button" id="sidebar-toggle" class="w-10 h-10 sm:w-11 sm:h-11 flex items-center justify-center rounded-xl transition-all duration-200 active:scale-95" style="background: #2D7A5B; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);" onmouseover="this.style.background='#25694B'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)'" onmouseout="this.style.background='#2D7A5B'; this.style.boxShadow='0 1px 3px 0 rgba(0, 0, 0, 0.08)'" aria-label="Toggle menu">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 12h16"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 18h16"/>
                    </svg>
                </button>
                <?php endif; ?>
                <!-- Premium Logo Container -->
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
    <!-- Main Content Area (Exact ISU Style) -->
    <main id="main-content" class="transition-all duration-300 ease-in-out mt-14 sm:mt-16 p-4 md:p-6 lg:p-8 min-h-[calc(100vh-3.5rem)] sm:min-h-[calc(100vh-4rem)] <?= $user['role'] === 'admin' ? 'lg:ml-64' : '' ?>" style="background-color: #F5F5F7;">
        <!-- Page Header (Exact ISU Style) -->
        <div class="mb-8 pb-5" style="border-bottom: 1px solid #E5E5E7;">
            <h2 class="text-3xl font-bold flex items-center gap-3" style="color: #1D1D1F;">
                <span class="w-1 h-8 rounded" style="background: linear-gradient(to bottom, #2D7A5B, #1D583C);"></span>
                Activity Logs
            </h2>
            <p class="mt-2 text-sm" style="color: #6E6E73;">Complete history of all system activities</p>
        </div>
        
        <!-- Stats Cards (ISU Style) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="rounded-xl p-4 md:p-6 transition-all duration-200" style="background-color: #FFFFFF; border: 1px solid #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);" onmouseover="this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.06)'" onmouseout="this.style.boxShadow='0 1px 3px 0 rgba(0, 0, 0, 0.04)'">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium" style="color: #6E6E73;">Total Logs</p>
                        <p class="text-2xl font-bold mt-1" style="color: #1D1D1F;"><?= number_format($totalLogs) ?></p>
                    </div>
                    <div class="flex items-center justify-center rounded-full flex-shrink-0" style="width: 48px; height: 48px; background-color: #EFF6FF;">
                        <svg class="w-6 h-6" style="color: #3B82F6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="rounded-xl p-4 md:p-6 transition-all duration-200" style="background-color: #FFFFFF; border: 1px solid #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);" onmouseover="this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.06)'" onmouseout="this.style.boxShadow='0 1px 3px 0 rgba(0, 0, 0, 0.04)'">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium" style="color: #6E6E73;">Today's Activities</p>
                        <p class="text-2xl font-bold mt-1" style="color: #1D1D1F;"><?= count(array_filter($logs, fn($log) => date('Y-m-d', strtotime($log['created_at'])) === date('Y-m-d'))) ?></p>
                    </div>
                    <div class="flex items-center justify-center rounded-full flex-shrink-0" style="width: 48px; height: 48px; background-color: #EDF7F3;">
                        <svg class="w-6 h-6" style="color: #2D7A5B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="rounded-xl p-4 md:p-6 transition-all duration-200" style="background-color: #FFFFFF; border: 1px solid #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);" onmouseover="this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.06)'" onmouseout="this.style.boxShadow='0 1px 3px 0 rgba(0, 0, 0, 0.04)'">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium" style="color: #6E6E73;">Success Rate</p>
                        <p class="text-2xl font-bold mt-1" style="color: #1D1D1F;">
                            <?php 
                            $successCount = count(array_filter($logs, fn($log) => $log['status'] === 'success'));
                            $successRate = count($logs) > 0 ? round(($successCount / count($logs)) * 100) : 0;
                            echo $successRate . '%';
                            ?>
                        </p>
                    </div>
                    <div class="flex items-center justify-center rounded-full flex-shrink-0" style="width: 48px; height: 48px; background-color: #F5F3FF;">
                        <svg class="w-6 h-6" style="color: #8B5CF6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Activity Table (ISU Style) -->
        <div class="rounded-xl overflow-hidden transition-all duration-200" style="background-color: #FFFFFF; border: 1px solid #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead style="background-color: #FAFAFA;">
                        <tr style="border-bottom: 1px solid #E5E5E7;">
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #6E6E73;">ID</th>
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #6E6E73;">User</th>
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #6E6E73;">Device</th>
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #6E6E73;">Action</th>
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #6E6E73;">Details</th>
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #6E6E73;">IP Address</th>
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #6E6E73;">Time</th>
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #6E6E73;">Status</th>
                        </tr>
                    </thead>
                    <tbody style="background-color: #FFFFFF;">
                        <?php foreach ($logs as $log): ?>
                        <tr class="transition-all duration-150" style="border-bottom: 1px solid #F5F5F7;" onmouseover="this.style.backgroundColor='#FAFAFA'" onmouseout="this.style.backgroundColor='#FFFFFF'">
                            <td class="px-4 py-4 whitespace-nowrap text-sm" style="color: #86868B;">#<?= $log['id'] ?></td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center rounded-full flex-shrink-0" style="width: 32px; height: 32px; background: linear-gradient(135deg, #34D399 0%, #2D7A5B 100%);">
                                        <span class="text-white text-xs font-bold"><?= strtoupper(substr($log['username'] ?? 'S', 0, 1)) ?></span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold" style="color: #1D1D1F;"><?= e($log['username'] ?? 'System') ?></p>
                                        <p class="text-xs" style="color: #86868B;"><?= e($log['role'] ?? 'N/A') ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <?php if (!empty($log['device_name'])): ?>
                                    <div>
                                        <p class="text-sm font-semibold" style="color: #2D7A5B;"><?= e($log['device_name']) ?></p>
                                        <p class="text-xs" style="color: #86868B;"><?= e($log['device_identifier']) ?></p>
                                    </div>
                                <?php else: ?>
                                    <span class="text-sm" style="color: #86868B;">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm">
                                <div class="flex items-center gap-2">
                                    <?php
                                    $actionType = $log['action_type'];
                                    $iconColor = 'text-gray-600';
                                    $iconPath = '';
                                    
                                    switch($actionType) {
                                        case 'outlet_on':
                                            $iconColor = 'text-green-600';
                                            $iconPath = 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z';
                                            break;
                                        case 'outlet_off':
                                            $iconColor = 'text-red-600';
                                            $iconPath = 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z';
                                            break;
                                        case 'login':
                                            $iconColor = 'text-blue-600';
                                            $iconPath = 'M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1';
                                            break;
                                        case 'logout':
                                            $iconColor = 'text-gray-600';
                                            $iconPath = 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1';
                                            break;
                                        case 'sms_command':
                                            $iconColor = 'text-purple-600';
                                            $iconPath = 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z';
                                            break;
                                        case 'manual_control':
                                            $iconColor = 'text-indigo-600';
                                            $iconPath = 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4';
                                            break;
                                        case 'schedule_created':
                                        case 'schedule_updated':
                                        case 'schedule_deleted':
                                        case 'schedule_enabled':
                                        case 'schedule_disabled':
                                            $iconColor = 'text-blue-600';
                                            $iconPath = 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z';
                                            break;
                                        case 'user_created':
                                        case 'user_updated':
                                        case 'user_deleted':
                                            $iconColor = 'text-green-600';
                                            $iconPath = 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z';
                                            break;
                                        case 'sim_registered':
                                        case 'sim_updated':
                                        case 'sim_deleted':
                                            $iconColor = 'text-teal-600';
                                            $iconPath = 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z';
                                            break;
                                        default:
                                            $iconColor = 'text-gray-600';
                                            $iconPath = 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
                                    }
                                    ?>
                                    <svg class="w-5 h-5 flex-shrink-0" style="color: <?= match(true) {
                                        str_starts_with($actionType, 'outlet_on') => '#2D7A5B',
                                        str_starts_with($actionType, 'outlet_off') => '#D32F2F',
                                        str_starts_with($actionType, 'login') => '#3B82F6',
                                        str_starts_with($actionType, 'logout') => '#86868B',
                                        str_starts_with($actionType, 'sms') => '#8B5CF6',
                                        str_starts_with($actionType, 'manual') => '#6366F1',
                                        str_contains($actionType, 'schedule') => '#3B82F6',
                                        str_contains($actionType, 'user') => '#2D7A5B',
                                        str_contains($actionType, 'sim') => '#14B8A6',
                                        default => '#86868B'
                                    }; ?>;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $iconPath ?>" />
                                    </svg>
                                    <span style="color: #1D1D1F;"><?= !empty($log['action_type']) ? ucfirst(str_replace('_', ' ', $log['action_type'])) : '<span style="color: #DC2626;">No action recorded</span>' ?></span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm max-w-md truncate" style="color: #6E6E73;"><?= e($log['action_details']) ?></td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm" style="color: #86868B;"><?= e($log['ip_address'] ?? 'N/A') ?></td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm">
                                <div style="color: #1D1D1F;"><?= date('M d, Y', strtotime($log['created_at'])) ?></div>
                                <div class="text-xs" style="color: #86868B;"><?= date('h:i A', strtotime($log['created_at'])) ?></div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full" style="<?= $log['status'] === 'success' ? 'background-color: #EDF7F3; color: #2D7A5B;' : 'background-color: #FEE2E2; color: #D32F2F;' ?>">
                                    <?= ucfirst($log['status']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination (ISU Style) -->
            <?php if ($totalPages > 1): ?>
            <div class="px-4 py-4" style="background-color: #FFFFFF; border-top: 1px solid #E5E5E7;">
                <div class="flex items-center justify-between">
                    <div class="text-sm" style="color: #6E6E73;">
                        Showing <span class="font-semibold" style="color: #1D1D1F;"><?= count($logs) ?></span> of <span class="font-semibold" style="color: #1D1D1F;"><?= $totalLogs ?></span> results
                    </div>
                    <div class="flex gap-2">
                        <?php if ($currentPage > 1): ?>
                        <a href="?page=<?= $currentPage - 1 ?>" class="px-4 py-2 text-sm font-semibold rounded-xl transition-all" style="background-color: #FFFFFF; border: 1px solid #E5E5E7; color: #1D1D1F;" onmouseover="this.style.backgroundColor='#FAFAFA'" onmouseout="this.style.backgroundColor='#FFFFFF'">
                            Previous
                        </a>
                        <?php endif; ?>
                        
                        <?php if ($currentPage < $totalPages): ?>
                        <a href="?page=<?= $currentPage + 1 ?>" class="px-4 py-2 text-sm font-semibold rounded-xl text-white transition-all" style="background-color: #2D7A5B; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);" onmouseover="this.style.backgroundColor='#25694B'" onmouseout="this.style.backgroundColor='#2D7A5B'">
                            Next
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<script>
// Sidebar toggle functionality (Exact ISU Style)
<?php if ($user['role'] === 'admin'): ?>
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
<?php endif; ?>
</script>

<?php require_once ROOT_PATH . '/app/views/layouts/footer.php'; ?>
