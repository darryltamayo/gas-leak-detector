<?php $pageTitle = 'My Profile - Smart AC Outlet Controller'; ?>
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
            <h2 class="text-3xl font-bold flex items-center gap-3" style="color: #1D1D1F;">
                <span class="w-1 h-8 rounded" style="background: linear-gradient(to bottom, #2D7A5B, #1D583C);"></span>
                My Profile
            </h2>
            <p class="mt-2 text-sm" style="color: #6E6E73;">Manage your account settings and password</p>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
        <div class="mb-6 p-4 rounded-xl" style="background-color: #EDF7F3; border: 1px solid #D4EDDF; color: #2D7A5B;">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <?= e($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
        <div class="mb-6 p-4 rounded-xl" style="background-color: #FEF2F2; border: 1px solid #FEE2E2; color: #DC2626;">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <?= e($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        </div>
        <?php endif; ?>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Info Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl p-6 text-center" style="border: 1px solid #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);">
                    <div class="w-24 h-24 mx-auto rounded-full flex items-center justify-center mb-4" style="background: linear-gradient(135deg, #34D399 0%, #2D7A5B 100%);">
                        <span class="text-4xl font-bold text-white"><?= strtoupper(substr($user['full_name'], 0, 1)) ?></span>
                    </div>
                    <h3 class="text-xl font-bold mb-1" style="color: #1D1D1F;"><?= e($user['full_name']) ?></h3>
                    <p class="text-sm mb-2" style="color: #86868B;">@<?= e($user['username']) ?></p>
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold" style="background: #EDF7F3; color: #2D7A5B;">
                        <?= ucfirst(e($user['role'])) ?>
                    </span>
                    
                    <div class="mt-6 pt-6 space-y-3 text-left" style="border-top: 1px solid #E5E5E7;">
                        <div class="flex items-center gap-2 text-sm">
                            <svg class="w-5 h-5 flex-shrink-0" style="color: #86868B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span style="color: #6E6E73;"><?= e($user['role'] === 'admin' ? 'Administrator' : 'User') ?></span>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <svg class="w-5 h-5 flex-shrink-0" style="color: #86868B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span style="color: #6E6E73;">Joined <?= date('M d, Y', strtotime($user['created_at'])) ?></span>
                        </div>
                        <?php if (!empty($user['last_login'])): ?>
                        <div class="flex items-center gap-2 text-sm">
                            <svg class="w-5 h-5 flex-shrink-0" style="color: #86868B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span style="color: #6E6E73;">Last login: <?= date('M d, Y g:i A', strtotime($user['last_login'])) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Update Profile Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Profile Information (Read-only for regular users) -->
                <div class="bg-white rounded-xl p-6" style="border: 1px solid #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);">
                    <h3 class="text-lg font-bold mb-4 pb-3" style="color: #1D1D1F; border-bottom: 1px solid #E5E5E7;">Profile Information</h3>
                    
                    <?php if ($user['role'] === 'admin'): ?>
                    <!-- Admin can edit their own profile -->
                    <form action="<?= url('/profile/update') ?>" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold mb-2" style="color: #1D1D1F;">Username</label>
                                <input type="text" value="<?= e($user['username']) ?>" disabled
                                       class="w-full px-4 py-3 rounded-xl border" style="border-color: #E5E5E7; background-color: #FAFAFA; color: #86868B;">
                                <p class="mt-1 text-xs" style="color: #86868B;">Username cannot be changed</p>
                            </div>
                            
                            <div>
                                <label for="full_name" class="block text-sm font-semibold mb-2" style="color: #1D1D1F;">Full Name</label>
                                <input type="text" id="full_name" name="full_name" value="<?= e($user['full_name']) ?>" required
                                       class="w-full px-4 py-3 rounded-xl border transition-all outline-none" style="border-color: #E5E5E7; background-color: #FFFFFF; color: #1D1D1F;"
                                       onfocus="this.style.borderColor='#2D7A5B'; this.style.boxShadow='0 0 0 3px rgba(45, 122, 91, 0.1)'" onblur="this.style.borderColor='#E5E5E7'; this.style.boxShadow='none'">
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <button type="submit" 
                                    class="px-6 py-3 text-white rounded-xl font-semibold transition-all duration-200 active:scale-95" style="background: #2D7A5B; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);" onmouseover="this.style.background='#25694B'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)'" onmouseout="this.style.background='#2D7A5B'; this.style.boxShadow='0 1px 3px 0 rgba(0, 0, 0, 0.08)'">
                                Update Profile
                            </button>
                        </div>
                    </form>
                    <?php else: ?>
                    <!-- Regular users: Read-only view -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold mb-2" style="color: #1D1D1F;">Username</label>
                            <input type="text" value="<?= e($user['username']) ?>" disabled
                                   class="w-full px-4 py-3 rounded-xl border" style="border-color: #E5E5E7; background-color: #FAFAFA; color: #86868B;">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold mb-2" style="color: #1D1D1F;">Full Name</label>
                            <input type="text" value="<?= e($user['full_name']) ?>" disabled
                                   class="w-full px-4 py-3 rounded-xl border" style="border-color: #E5E5E7; background-color: #FAFAFA; color: #86868B;">
                        </div>
                    </div>
                    
                    <div class="mt-6 p-4 rounded-xl flex items-start gap-3" style="background-color: #FFF9E6; border: 1px solid #FFE5A3;">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: #F59E0B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-sm font-semibold" style="color: #92400E;">Profile Information is Read-Only</p>
                            <p class="text-xs mt-1" style="color: #92400E;">Only administrators can modify user profile information. Contact an admin to update your name.</p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Change Password -->
                <div class="bg-white rounded-xl p-6" style="border: 1px solid #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);">
                    <h3 class="text-lg font-bold mb-4 pb-3" style="color: #1D1D1F; border-bottom: 1px solid #E5E5E7;">Change Password</h3>
                    
                    <form action="<?= url('/profile/change-password') ?>" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        
                        <div class="space-y-4">
                            <div>
                                <label for="current_password" class="block text-sm font-semibold mb-2" style="color: #1D1D1F;">Current Password</label>
                                <input type="password" id="current_password" name="current_password" required
                                       class="w-full px-4 py-3 rounded-xl border transition-all outline-none" style="border-color: #E5E5E7; background-color: #FFFFFF; color: #1D1D1F;"
                                       onfocus="this.style.borderColor='#2D7A5B'; this.style.boxShadow='0 0 0 3px rgba(45, 122, 91, 0.1)'" onblur="this.style.borderColor='#E5E5E7'; this.style.boxShadow='none'"
                                       placeholder="Enter your current password">
                            </div>
                            
                            <div>
                                <label for="new_password" class="block text-sm font-semibold mb-2" style="color: #1D1D1F;">New Password</label>
                                <input type="password" id="new_password" name="new_password" required minlength="6"
                                       class="w-full px-4 py-3 rounded-xl border transition-all outline-none" style="border-color: #E5E5E7; background-color: #FFFFFF; color: #1D1D1F;"
                                       onfocus="this.style.borderColor='#2D7A5B'; this.style.boxShadow='0 0 0 3px rgba(45, 122, 91, 0.1)'" onblur="this.style.borderColor='#E5E5E7'; this.style.boxShadow='none'"
                                       placeholder="Minimum 6 characters">
                                <p class="mt-1 text-xs" style="color: #86868B;">Password must be at least 6 characters long</p>
                            </div>
                            
                            <div>
                                <label for="confirm_password" class="block text-sm font-semibold mb-2" style="color: #1D1D1F;">Confirm New Password</label>
                                <input type="password" id="confirm_password" name="confirm_password" required minlength="6"
                                       class="w-full px-4 py-3 rounded-xl border transition-all outline-none" style="border-color: #E5E5E7; background-color: #FFFFFF; color: #1D1D1F;"
                                       onfocus="this.style.borderColor='#2D7A5B'; this.style.boxShadow='0 0 0 3px rgba(45, 122, 91, 0.1)'" onblur="this.style.borderColor='#E5E5E7'; this.style.boxShadow='none'"
                                       placeholder="Re-enter your new password">
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <button type="submit" 
                                    class="px-6 py-3 text-white rounded-xl font-semibold transition-all duration-200 active:scale-95" style="background: #D32F2F; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);" onmouseover="this.style.background='#B71C1C'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)'" onmouseout="this.style.background='#D32F2F'; this.style.boxShadow='0 1px 3px 0 rgba(0, 0, 0, 0.08)'">
                                Change Password
                            </button>
                        </div>
                    </form>
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
