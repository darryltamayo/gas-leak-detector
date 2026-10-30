<?php $pageTitle = 'Schedules - Smart AC Outlet Controller'; ?>
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
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-3xl font-bold flex items-center gap-3" style="color: #1D1D1F;">
                        <span class="w-1 h-8 rounded" style="background: linear-gradient(to bottom, #2D7A5B, #1D583C);"></span>
                        Schedules
                    </h2>
                    <p class="mt-2 text-sm" style="color: #6E6E73;">Manage automatic ON/OFF schedules</p>
                </div>
                <button onclick="openCreateModal()" 
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-white rounded-xl font-semibold transition-all duration-200 active:scale-95 text-sm" style="background: #2D7A5B; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);" onmouseover="this.style.background='#25694B'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)'" onmouseout="this.style.background='#2D7A5B'; this.style.boxShadow='0 1px 3px 0 rgba(0, 0, 0, 0.08)'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create Schedule
                </button>
            </div>
        </div>
        
        <!-- Information Panel - How Schedules Work -->
        <div class="mb-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6" style="border: 1px solid #E0E7FF;">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8" style="color: #4F46E5;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold mb-3" style="color: #1D1D1F;">
                        📋 How Schedule System Works
                    </h3>
                    <div class="space-y-3 text-sm" style="color: #4B5563;">
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #2D7A5B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <strong>Control Mode Must Be "Scheduled":</strong> Go to Outlet Control page and set mode to "Scheduled". If mode is "Manual", schedules won't execute.
                            </div>
                        </div>
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #2D7A5B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <strong>Enable Schedules (Toggle ON):</strong> Only schedules with the toggle switched ON (● Active) will execute. Inactive schedules are ignored.
                            </div>
                        </div>
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #2D7A5B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <strong>Schedule Conflicts:</strong> If multiple schedules overlap, the most recently created schedule (highest ID) takes priority.
                            </div>
                        </div>
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #F59E0B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div>
                                <strong>Manual Override:</strong> If you turn ON/OFF manually via buttons or SMS, control mode switches to "Manual" and schedules stop executing until you change mode back to "Scheduled".
                            </div>
                        </div>
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #DC2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <div>
                                <strong>Active Schedule Protection:</strong> Regular users cannot turn OFF the outlet while a schedule is active (within ON-OFF time window). Only admins can override.
                            </div>
                        </div>
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #8B5CF6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <strong>Auto-Disable:</strong> One-time schedules automatically disable after execution. Repeating schedules stay active and run daily/weekly.
                            </div>
                        </div>
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #10B981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <div>
                                <strong>Offline Device Scheduling:</strong> You can schedule offline devices! When the device comes online, it fetches the current schedule and follows it. If it comes online late (e.g., during ON time), it will turn ON immediately and follow the rest of the schedule.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Schedules List -->
        <?php if (empty($schedules)): ?>
        <div class="bg-white rounded-xl p-12 text-center" style="border: 1px solid #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);">
            <svg class="mx-auto h-16 w-16" style="color: #86868B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <h3 class="mt-4 text-xl font-semibold" style="color: #1D1D1F;">No Schedules Yet</h3>
            <p class="mt-2" style="color: #6E6E73;">Create your first schedule to automate your AC control</p>
            <button onclick="openCreateModal()" 
                    class="mt-6 inline-flex items-center px-6 py-3 text-white rounded-xl font-semibold transition-all duration-200" style="background: #2D7A5B; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);" onmouseover="this.style.background='#25694B'" onmouseout="this.style.background='#2D7A5B'">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create Schedule
            </button>
        </div>
        <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($schedules as $schedule): ?>
            <div class="bg-white rounded-xl p-6 transition-all duration-300" style="border: 1px solid #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);" onmouseover="this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.06)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='0 1px 3px 0 rgba(0, 0, 0, 0.04)'; this.style.transform='translateY(0)'">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-semibold" style="color: #1D1D1F;"><?= e($schedule->schedule_name) ?></h3>
                        <div class="flex gap-2 mt-2 flex-wrap">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?= $schedule->is_enabled ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>" style="<?= $schedule->is_enabled ? 'background: #EDF7F3; color: #2D7A5B;' : 'background: #FAFAFA; color: #6E6E73;' ?>">
                                <?= $schedule->is_enabled ? '● Active' : '○ Inactive' ?>
                            </span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" style="<?= ($schedule->repeat_type ?? 'repeat') === 'once' ? 'background: #FEF3C7; color: #92400E;' : 'background: #E0E7FF; color: #3730A3;' ?>">
                                <?= ($schedule->repeat_type ?? 'repeat') === 'once' ? '⚡ Once Only' : '🔁 Repeating' ?>
                            </span>
                            <?php if ($schedule->device_name): ?>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" style="background: #F3E8FF; color: #6B21A8;">
                                🖥️ <?= e($schedule->device_name) ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        <?php if ($schedule->username): ?>
                        <div class="mt-2 flex items-center gap-1 text-xs" style="color: #86868B;">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>Created by: <strong style="color: #6E6E73;"><?= e($schedule->username) ?></strong></span>
                        </div>
                        <?php endif; ?>
                        <?php if (($schedule->repeat_type ?? 'repeat') === 'once' && $schedule->last_executed): ?>
                        <div class="mt-1 flex items-center gap-1 text-xs" style="color: #86868B;">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Executed: <?= date('M d, Y g:i A', strtotime($schedule->last_executed)) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php if ($user['role'] === 'admin' || $schedule->user_id == $user['id']): ?>
                    <button onclick="toggleSchedule(<?= $schedule->id ?>, <?= $schedule->is_enabled ?>)" 
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none <?= $schedule->is_enabled ? 'bg-brand-green-500' : 'bg-gray-200' ?>" style="background: <?= $schedule->is_enabled ? '#2D7A5B' : '#E5E5E7' ?>;">
                        <span class="translate-x-<?= $schedule->is_enabled ? '5' : '0' ?> inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                    </button>
                    <?php else: ?>
                    <div class="flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium" style="background: #FAFAFA; color: #86868B;">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Read-only
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="space-y-3">
                    <div class="flex items-center text-sm" style="color: #1D1D1F;">
                        <svg class="w-5 h-5 mr-2" style="color: #2D7A5B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="font-medium">Turn ON:</span>
                        <span class="ml-2"><?= date('g:i A', strtotime($schedule->time_on)) ?></span>
                    </div>
                    
                    <div class="flex items-center text-sm" style="color: #1D1D1F;">
                        <svg class="w-5 h-5 mr-2" style="color: #D32F2F;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span class="font-medium">Turn OFF:</span>
                        <span class="ml-2"><?= date('g:i A', strtotime($schedule->time_off)) ?></span>
                    </div>
                    
                    <div class="flex items-center text-sm" style="color: #1D1D1F;">
                        <svg class="w-5 h-5 mr-2" style="color: #1E40AF;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="font-medium">
                            <?php if (($schedule->repeat_type ?? 'repeat') === 'once' && !empty($schedule->schedule_date)): ?>
                                Date:
                            <?php else: ?>
                                Days:
                            <?php endif; ?>
                        </span>
                        <span class="ml-2">
                            <?php if (($schedule->repeat_type ?? 'repeat') === 'once' && !empty($schedule->schedule_date)): ?>
                                <?= date('M d, Y', strtotime($schedule->schedule_date)) ?>
                            <?php else: ?>
                                <?= formatDays($schedule->days_of_week) ?>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
                
                <?php if ($user['role'] === 'admin' || $schedule->user_id == $user['id']): ?>
                <div class="mt-4 pt-4 flex space-x-3" style="border-top: 1px solid #E5E5E7;">
                    <button onclick="openEditModal(<?= $schedule->id ?>, '<?= e($schedule->schedule_name) ?>', '<?= e($schedule->time_on) ?>', '<?= e($schedule->time_off) ?>', '<?= e($schedule->days_of_week) ?>', <?= $schedule->is_enabled ? 'true' : 'false' ?>, '<?= e($schedule->repeat_type ?? 'repeat') ?>', '<?= e($schedule->schedule_date ?? '') ?>')" 
                            class="flex-1 inline-flex items-center justify-center space-x-2 px-4 py-2.5 text-white text-sm font-semibold rounded-xl transition-all duration-200 active:scale-95" style="background: #1E40AF; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);" onmouseover="this.style.background='#1E3A8A'" onmouseout="this.style.background='#1E40AF'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>Edit</span>
                    </button>
                    <button onclick="deleteSchedule(<?= $schedule->id ?>)" 
                            class="flex-1 inline-flex items-center justify-center space-x-2 px-4 py-2.5 text-white text-sm font-semibold rounded-xl transition-all duration-200 active:scale-95" style="background: #D32F2F; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);" onmouseover="this.style.background='#B71C1C'" onmouseout="this.style.background='#D32F2F'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Delete</span>
                    </button>
                </div>
                <?php else: ?>
                <div class="mt-4 pt-4 text-center text-xs" style="border-top: 1px solid #E5E5E7; color: #86868B;">
                    <svg class="w-4 h-4 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    View only - You can only edit your own schedules
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
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
</script>

<!-- Create Schedule Modal -->
<div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl w-full max-w-md flex flex-col" style="max-height: 85vh; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
        <!-- Fixed Header -->
        <div class="flex justify-between items-center p-6 border-b flex-shrink-0" style="border-color: #E5E5E7;">
            <h2 class="text-2xl font-bold" style="color: #1D1D1F;">Create Schedule</h2>
            <button type="button" onclick="closeCreateModal()" style="color: #86868B;" onmouseover="this.style.color='#1D1D1F'" onmouseout="this.style.color='#86868B'">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <!-- Scrollable Content -->
        <div class="overflow-y-auto flex-1 p-6">
            <form id="createScheduleForm" action="<?= url('/schedule/store') ?>" method="POST" class="space-y-4" onsubmit="return validateScheduleForm(this)">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                
                <!-- Device Selection (MULTIPLE devices) -->
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: #1D1D1F;">
                        Select Devices to Control
                    </label>
                    <div class="border rounded-lg p-4 max-h-48 overflow-y-auto" style="border-color: #E5E5E7; background: #FAFAFA;">
                        <?php if (!empty($devices)): ?>
                            <div class="space-y-2">
                                <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white transition-colors cursor-pointer">
                                    <input type="checkbox" id="selectAllDevices" 
                                           class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                           onchange="toggleAllDevices(this.checked)">
                                    <span class="text-sm font-bold" style="color: #2D7A5B;">Select All Devices</span>
                                </label>
                                <div class="border-t pt-2" style="border-color: #E5E5E7;"></div>
                                <?php foreach ($devices as $dev): ?>
                                <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white transition-colors cursor-pointer">
                                    <input type="checkbox" name="device_ids[]" value="<?= $dev->id ?>" 
                                           class="device-checkbox rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                           onchange="updateSelectAllCheckbox()">
                                    <div class="flex-1">
                                        <div class="text-sm font-medium" style="color: #1D1D1F;">
                                            <?= htmlspecialchars($dev->device_name) ?>
                                        </div>
                                        <div class="text-xs" style="color: #86868B;">
                                            <?= htmlspecialchars($dev->device_id) ?> - 
                                            <span style="color: <?= $dev->is_online ? '#2D7A5B' : '#6E6E73' ?>;">
                                                <?= $dev->is_online ? '✓ Online' : '○ Offline' ?>
                                            </span>
                                        </div>
                                    </div>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-sm text-center py-4" style="color: #86868B;">No devices available</p>
                        <?php endif; ?>
                    </div>
                    <p class="mt-2 text-xs" style="color: #86868B;">
                        ℹ️ Select one or more devices. Same schedule will apply to all selected devices.
                    </p>
                </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Schedule Name</label>
                <input type="text" name="schedule_name" required 
                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                       placeholder="e.g., Weekday Schedule">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Schedule Type</label>
                <div class="flex gap-4">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" name="repeat_type" value="repeat" checked 
                               class="rounded-full border-gray-300 text-primary-600 focus:ring-primary-500"
                               onchange="toggleRepeatFields(this.value, 'create')">
                        <span class="text-sm font-medium" style="color: #1D1D1F;">Repeat Daily</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" name="repeat_type" value="once" 
                               class="rounded-full border-gray-300 text-primary-600 focus:ring-primary-500"
                               onchange="toggleRepeatFields(this.value, 'create')">
                        <span class="text-sm font-medium" style="color: #1D1D1F;">Once Only</span>
                    </label>
                </div>
                <p class="mt-1 text-xs" style="color: #86868B;">Choose "Once Only" to run the schedule one time and automatically disable it</p>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Turn ON Time</label>
                    <input type="time" name="time_on" required 
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Turn OFF Time</label>
                    <input type="time" name="time_off" required 
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
            </div>
            
            <div id="create_days_container">
                <label class="block text-sm font-medium text-gray-700 mb-2">Repeat On</label>
                <div class="grid grid-cols-4 gap-2">
                    <?php 
                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                    foreach ($days as $day): 
                    ?>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="days_of_week[]" value="<?= $day ?>" 
                               class="create-day-checkbox rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm"><?= substr($day, 0, 3) ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div id="create_date_container" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Date</label>
                <input type="date" id="create_schedule_date" name="schedule_date" 
                       min="<?= date('Y-m-d') ?>"
                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                <p class="mt-1 text-xs" style="color: #86868B;">This schedule will run once on the selected date</p>
            </div>
            
            <div class="flex items-center">
                <input type="checkbox" name="is_enabled" id="is_enabled" checked
                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                <label for="is_enabled" class="ml-2 text-sm text-gray-700">Enable schedule immediately</label>
            </div>
            </form>
        </div>
        
        <!-- Fixed Footer with Buttons -->
        <div class="flex space-x-3 p-6 border-t flex-shrink-0" style="border-color: #E5E5E7;">
            <button type="submit" form="createScheduleForm"
                    class="flex-1 px-6 py-3 text-white rounded-xl font-semibold transition-all duration-200" style="background: #2D7A5B;" onmouseover="this.style.background='#25694B'" onmouseout="this.style.background='#2D7A5B'">
                Create
            </button>
            <button type="button" onclick="closeCreateModal()"
                    class="flex-1 px-6 py-3 rounded-xl font-semibold transition-all duration-200" style="background: #FAFAFA; color: #1D1D1F;" onmouseover="this.style.background='#E5E5E7'" onmouseout="this.style.background='#FAFAFA'">
                Cancel
            </button>
        </div>
    </div>
</div>

<!-- Edit Schedule Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl w-full max-w-md flex flex-col" style="max-height: 85vh; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
        <!-- Fixed Header -->
        <div class="flex justify-between items-center p-6 border-b flex-shrink-0" style="border-color: #E5E5E7;">
            <h2 class="text-2xl font-bold" style="color: #1D1D1F;">Edit Schedule</h2>
            <button type="button" onclick="closeEditModal()" style="color: #86868B;" onmouseover="this.style.color='#1D1D1F'" onmouseout="this.style.color='#86868B'">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <!-- Scrollable Content -->
        <div class="overflow-y-auto flex-1 p-6">
            <form id="editForm" method="POST" class="space-y-4" onsubmit="return validateScheduleForm(this)">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" id="edit_schedule_id" name="schedule_id" value="">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Schedule Name</label>
                <input type="text" id="edit_schedule_name" name="schedule_name" required 
                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                       placeholder="e.g., Weekday Schedule">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Schedule Type</label>
                <div class="flex gap-4">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" id="edit_repeat_type_repeat" name="repeat_type" value="repeat" checked 
                               class="rounded-full border-gray-300 text-primary-600 focus:ring-primary-500"
                               onchange="toggleRepeatFields(this.value, 'edit')">
                        <span class="text-sm font-medium" style="color: #1D1D1F;">Repeat Daily</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" id="edit_repeat_type_once" name="repeat_type" value="once" 
                               class="rounded-full border-gray-300 text-primary-600 focus:ring-primary-500"
                               onchange="toggleRepeatFields(this.value, 'edit')">
                        <span class="text-sm font-medium" style="color: #1D1D1F;">Once Only</span>
                    </label>
                </div>
                <p class="mt-1 text-xs" style="color: #86868B;">Choose "Once Only" to run the schedule one time and automatically disable it</p>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Turn ON Time</label>
                    <input type="time" id="edit_time_on" name="time_on" required 
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Turn OFF Time</label>
                    <input type="time" id="edit_time_off" name="time_off" required 
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
            </div>
            
            <div id="edit_days_container">
                <label class="block text-sm font-medium text-gray-700 mb-2">Repeat On</label>
                <div class="grid grid-cols-4 gap-2">
                    <?php 
                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                    foreach ($days as $day): 
                    ?>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="days_of_week[]" value="<?= $day ?>" class="edit-day-checkbox rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm"><?= substr($day, 0, 3) ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div id="edit_date_container" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Date</label>
                <input type="date" id="edit_schedule_date" name="schedule_date" 
                       min="<?= date('Y-m-d') ?>"
                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                <p class="mt-1 text-xs" style="color: #86868B;">This schedule will run once on the selected date</p>
            </div>
            
            <div class="flex items-center">
                <input type="checkbox" name="is_enabled" id="edit_is_enabled"
                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                <label for="edit_is_enabled" class="ml-2 text-sm text-gray-700">Schedule is enabled</label>
            </div>
            </form>
        </div>
        
        <!-- Fixed Footer with Buttons -->
        <div class="flex space-x-3 p-6 border-t flex-shrink-0" style="border-color: #E5E5E7;">
            <button type="submit" form="editForm"
                    class="flex-1 px-6 py-3 text-white rounded-xl font-semibold transition-all duration-200" style="background: #2D7A5B;" onmouseover="this.style.background='#25694B'" onmouseout="this.style.background='#2D7A5B'">
                Update
            </button>
            <button type="button" onclick="closeEditModal()"
                    class="flex-1 px-6 py-3 rounded-xl font-semibold transition-all duration-200" style="background: #FAFAFA; color: #1D1D1F;" onmouseover="this.style.background='#E5E5E7'" onmouseout="this.style.background='#FAFAFA'">
                Cancel
            </button>
        </div>
    </div>
</div>

<script>
function openCreateModal() {
    document.getElementById('createModal').classList.remove('hidden');
    document.getElementById('createModal').classList.add('flex');
}

function closeCreateModal() {
    document.getElementById('createModal').classList.add('hidden');
    document.getElementById('createModal').classList.remove('flex');
}

function openEditModal(id, name, timeOn, timeOff, days, isEnabled, repeatType, scheduleDate) {
    // Set form action
    document.getElementById('editForm').action = `<?= url('/schedule/update/') ?>${id}`;
    
    // Set values
    document.getElementById('edit_schedule_id').value = id;
    document.getElementById('edit_schedule_name').value = name;
    document.getElementById('edit_time_on').value = timeOn.substring(0, 5); // HH:MM format
    document.getElementById('edit_time_off').value = timeOff.substring(0, 5); // HH:MM format
    document.getElementById('edit_is_enabled').checked = isEnabled;
    
    // Set repeat type
    repeatType = repeatType || 'repeat'; // Default to repeat if not set
    if (repeatType === 'once') {
        document.getElementById('edit_repeat_type_once').checked = true;
    } else {
        document.getElementById('edit_repeat_type_repeat').checked = true;
    }
    
    // Set schedule date if provided
    if (scheduleDate) {
        document.getElementById('edit_schedule_date').value = scheduleDate;
    }
    
    // Clear all checkboxes first
    document.querySelectorAll('.edit-day-checkbox').forEach(cb => cb.checked = false);
    
    // Set day checkboxes
    const selectedDays = days.split(',');
    document.querySelectorAll('.edit-day-checkbox').forEach(checkbox => {
        if (selectedDays.includes(checkbox.value)) {
            checkbox.checked = true;
        }
    });
    
    // Toggle days field visibility based on repeat type
    toggleRepeatFields(repeatType, 'edit');
    
    // Show modal
    document.getElementById('editModal').classList.remove('hidden');
    document.getElementById('editModal').classList.add('flex');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    document.getElementById('editModal').classList.remove('flex');
}

async function toggleSchedule(id, currentStatus) {
    try {
        const response = await fetch(`<?= url('/schedule/toggle/') ?>${id}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'csrf_token=<?= csrf_token() ?>'
        });
        const data = await response.json();
        if (data.success) {
            window.location.reload();
        }
    } catch (error) {
        alert('Failed to toggle schedule');
    }
}

function deleteSchedule(id) {
    if (confirm('Are you sure you want to delete this schedule?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `<?= url('/schedule/delete/') ?>${id}`;
        form.innerHTML = `<input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">`;
        document.body.appendChild(form);
        form.submit();
    }
}

// Validate schedule form - ensure Turn ON time is before Turn OFF time
function validateScheduleForm(form) {
    // Check if at least one device is selected (checkboxes)
    const deviceCheckboxes = form.querySelectorAll('input[name="device_ids[]"]:checked');
    if (deviceCheckboxes.length === 0) {
        alert('⚠️ Please select at least one device to control.');
        return false;
    }
    
    const timeOn = form.querySelector('input[name="time_on"]').value;
    const timeOff = form.querySelector('input[name="time_off"]').value;
    const repeatType = form.querySelector('input[name="repeat_type"]:checked').value;
    
    if (!timeOn || !timeOff) {
        alert('⚠️ Please enter both Turn ON and Turn OFF times.');
        return false;
    }
    
    // Compare times
    if (timeOn >= timeOff) {
        alert('⚠️ Turn ON time must be earlier than Turn OFF time.\n\nExample:\nTurn ON: 08:00\nTurn OFF: 17:00');
        return false;
    }
    
    // Check if at least one day is selected (only for repeat type)
    if (repeatType === 'repeat') {
        const daysChecked = form.querySelectorAll('input[name="days_of_week[]"]:checked');
        if (daysChecked.length === 0) {
            alert('⚠️ Please select at least one day for the repeating schedule.');
            return false;
        }
    } else {
        // For "once" type, check if date is selected
        const scheduleDateInput = form.querySelector('input[name="schedule_date"]');
        const scheduleDate = scheduleDateInput ? scheduleDateInput.value : '';
        
        if (!scheduleDate) {
            alert('⚠️ Please select a date for the one-time schedule.');
            return false;
        }
        
        // Validate date is not in the past
        const selectedDate = new Date(scheduleDate);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (selectedDate < today) {
            alert('⚠️ Please select a date that is today or in the future.');
            return false;
        }
        
        // Convert the selected date to day of week and auto-select it
        const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        const selectedDayName = dayNames[selectedDate.getDay()];
        
        // Uncheck all days first
        const allDayCheckboxes = form.querySelectorAll('input[name="days_of_week[]"]');
        allDayCheckboxes.forEach(cb => cb.checked = false);
        
        // Check only the selected day (for backend processing)
        allDayCheckboxes.forEach(cb => {
            if (cb.value === selectedDayName) {
                cb.checked = true;
            }
        });
    }
    
    return true;
}

// Toggle repeat fields based on schedule type
function toggleRepeatFields(repeatType, modalType) {
    const daysContainer = document.getElementById(modalType + '_days_container');
    const dateContainer = document.getElementById(modalType + '_date_container');
    
    if (repeatType === 'once') {
        // Hide days selection, show date picker for one-time schedules
        daysContainer.style.display = 'none';
        dateContainer.style.display = 'block';
        
        // Set minimum date to today
        const dateInput = document.getElementById(modalType + '_schedule_date');
        if (dateInput) {
            const today = new Date().toISOString().split('T')[0];
            dateInput.setAttribute('min', today);
            
            // Set default value to today if not already set
            if (!dateInput.value) {
                dateInput.value = today;
            }
        }
    } else {
        // Show days selection, hide date picker for repeating schedules
        daysContainer.style.display = 'block';
        dateContainer.style.display = 'none';
    }
}

// Toggle all device checkboxes
function toggleAllDevices(checked) {
    const checkboxes = document.querySelectorAll('.device-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = checked;
    });
}

// Update "Select All" checkbox state based on individual device checkboxes
function updateSelectAllCheckbox() {
    const selectAllCheckbox = document.getElementById('selectAllDevices');
    const deviceCheckboxes = document.querySelectorAll('.device-checkbox');
    const checkedCount = document.querySelectorAll('.device-checkbox:checked').length;
    
    if (selectAllCheckbox) {
        if (checkedCount === 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        } else if (checkedCount === deviceCheckboxes.length) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        }
    }
}
</script>

<?php
function formatDays($days) {
    if ($days === 'Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday') return 'Every Day';
    if ($days === 'Monday,Tuesday,Wednesday,Thursday,Friday') return 'Weekdays';
    if ($days === 'Saturday,Sunday') return 'Weekends';
    $dayArray = explode(',', $days);
    return implode(', ', array_map(fn($d) => substr($d, 0, 3), $dayArray));
}
?>

<style>
/* Premium Checkbox Styles for Schedule */
.premium-checkbox-container-schedule {
    display: inline-block;
    position: relative;
    cursor: pointer;
    user-select: none;
    width: 22px;
    height: 22px;
    flex-shrink: 0;
}

.premium-checkbox-container-schedule input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
}

.premium-checkmark-schedule {
    position: absolute;
    top: 0;
    left: 0;
    height: 22px;
    width: 22px;
    background: white;
    border: 2.5px solid #D4EDDF;
    border-radius: 6px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);
}

.premium-checkbox-container-schedule:hover .premium-checkmark-schedule {
    border-color: #2D7A5B;
    box-shadow: 0 2px 6px 0 rgba(45, 122, 91, 0.15);
    transform: scale(1.08);
}

.premium-checkbox-container-schedule input:checked ~ .premium-checkmark-schedule {
    background: linear-gradient(135deg, #2D7A5B, #25694B);
    border-color: #2D7A5B;
    box-shadow: 0 4px 8px 0 rgba(45, 122, 91, 0.25);
    transform: scale(1);
}

.premium-checkmark-schedule:after {
    content: "";
    position: absolute;
    display: none;
    left: 6px;
    top: 2px;
    width: 5px;
    height: 10px;
    border: solid white;
    border-width: 0 2.5px 2.5px 0;
    transform: rotate(45deg);
}

.premium-checkbox-container-schedule input:checked ~ .premium-checkmark-schedule:after {
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
