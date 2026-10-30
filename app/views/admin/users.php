<?php $pageTitle = 'Users Management - Smart AC Outlet Controller'; ?>
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
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-3xl font-bold flex items-center gap-3" style="color: #1D1D1F;">
                        <span class="w-1 h-8 rounded" style="background: linear-gradient(to bottom, #2D7A5B, #1D583C);"></span>
                        Users Management
                    </h2>
                    <p class="mt-2 text-sm" style="color: #6E6E73;">Manage system users and permissions</p>
                </div>
                <a href="<?= url('/admin/create-user') ?>" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 text-white rounded-xl font-semibold transition-all duration-200 active:scale-95 text-sm" style="background: #2D7A5B; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);" onmouseover="this.style.background='#25694B'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)'" onmouseout="this.style.background='#2D7A5B'; this.style.boxShadow='0 1px 3px 0 rgba(0, 0, 0, 0.08)'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add New User
                </a>
            </div>
        </div>
        
        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6 md:mb-8">
            <div class="bg-white rounded-xl p-4 md:p-6 border transition-all duration-300 relative overflow-hidden group" style="border-color: #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.03);" onmouseover="this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.06), 0 2px 4px -2px rgba(0, 0, 0, 0.04)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.03)'; this.style.transform='translateY(0)'">
                <div class="text-xs uppercase tracking-wide font-semibold mb-2 md:mb-3" style="color: #86868B;">Total Users</div>
                <p class="text-2xl md:text-4xl font-bold mt-2" style="color: #2D7A5B;"><?= count($users) ?></p>
            </div>
            <div class="bg-white rounded-xl p-4 md:p-6 transition-all duration-300" style="border: 1px solid #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);" onmouseover="this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.06)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='0 1px 3px 0 rgba(0, 0, 0, 0.04)'; this.style.transform='translateY(0)'">
                <p class="text-xs font-semibold uppercase tracking-wide" style="color: #86868B;">Active Users</p>
                <p class="text-2xl md:text-4xl font-bold mt-2" style="color: #2D7A5B;"><?= count(array_filter($users, fn($u) => $u->is_active)) ?></p>
            </div>
            <div class="bg-white rounded-xl p-4 md:p-6 transition-all duration-300" style="border: 1px solid #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);" onmouseover="this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.06)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='0 1px 3px 0 rgba(0, 0, 0, 0.04)'; this.style.transform='translateY(0)'">
                <p class="text-xs font-semibold uppercase tracking-wide" style="color: #86868B;">Administrators</p>
                <p class="text-2xl md:text-4xl font-bold mt-2" style="color: #2D7A5B;"><?= count(array_filter($users, fn($u) => $u->role === 'admin')) ?></p>
            </div>
            <div class="bg-white rounded-xl p-4 md:p-6 transition-all duration-300" style="border: 1px solid #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);" onmouseover="this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.06)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='0 1px 3px 0 rgba(0, 0, 0, 0.04)'; this.style.transform='translateY(0)'">
                <p class="text-xs font-semibold uppercase tracking-wide" style="color: #86868B;">Regular Users</p>
                <p class="text-2xl md:text-4xl font-bold mt-2" style="color: #2D7A5B;"><?= count(array_filter($users, fn($u) => $u->role === 'user')) ?></p>
            </div>
        </div>
        
        <!-- Users Table -->
        <div class="bg-white rounded-xl overflow-hidden" style="border: 1px solid #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead style="background-color: #FAFAFA;">
                        <tr>
                            <th class="px-4 py-4 text-left font-bold border-b text-xs uppercase tracking-wider" style="border-color: #E5E5E7; color: #6E6E73;">User</th>
                            <th class="px-4 py-4 text-left font-bold border-b text-xs uppercase tracking-wider" style="border-color: #E5E5E7; color: #6E6E73;">Role</th>
                            <th class="px-4 py-4 text-left font-bold border-b text-xs uppercase tracking-wider" style="border-color: #E5E5E7; color: #6E6E73;">Status</th>
                            <th class="px-4 py-4 text-left font-bold border-b text-xs uppercase tracking-wider" style="border-color: #E5E5E7; color: #6E6E73;">Last Login</th>
                            <th class="px-4 py-4 text-left font-bold border-b text-xs uppercase tracking-wider" style="border-color: #E5E5E7; color: #6E6E73;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $userItem): ?>
                        <tr class="border-b transition-colors" style="border-color: #E5E5E7;" onmouseover="this.style.backgroundColor='#FAFAFA';" onmouseout="this.style.backgroundColor='transparent';">
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full flex items-center justify-center text-white font-semibold" style="background: linear-gradient(135deg, #2D7A5B, #25694B);">
                                        <?= strtoupper(substr($userItem->full_name, 0, 1)) ?>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium" style="color: #1D1D1F;"><?= e($userItem->full_name) ?></div>
                                        <div class="text-sm" style="color: #6E6E73;">@<?= e($userItem->username) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $userItem->role === 'admin' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' ?>" style="<?= $userItem->role === 'admin' ? 'background: #EFF6FF; color: #1E40AF;' : 'background: #FAFAFA; color: #6E6E73;' ?>">
                                    <?= ucfirst($userItem->role) ?>
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $userItem->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>" style="<?= $userItem->is_active ? 'background: #EDF7F3; color: #2D7A5B;' : 'background: #FEE2E2; color: #DC2626;' ?>">
                                    <?= $userItem->is_active ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm" style="color: #6E6E73;">
                                <?= $userItem->last_login ? timeAgo($userItem->last_login) : 'Never' ?>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm">
                                <a href="<?= url('/admin/edit-user?id=' . $userItem->id) ?>" 
                                   class="mr-3 font-medium" style="color: #2D7A5B;" onmouseover="this.style.color='#1D583C'" onmouseout="this.style.color='#2D7A5B'">Edit</a>
                                <?php if ($userItem->id !== $user['id']): ?>
                                <button onclick="toggleUserStatus(<?= $userItem->id ?>, <?= $userItem->is_active ? 0 : 1 ?>)" 
                                        class="font-medium" style="color: #EA580C;" onmouseover="this.style.color='#C2410C'" onmouseout="this.style.color='#EA580C'">
                                    <?= $userItem->is_active ? 'Deactivate' : 'Activate' ?>
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
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

async function toggleUserStatus(userId, newStatus) {
    const action = newStatus === 1 ? 'activate' : 'deactivate';
    if (!confirm(`Are you sure you want to ${action} this user?`)) return;
    
    try {
        const response = await fetch('<?= url('/admin/toggle-user-status') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `csrf_token=<?= csrf_token() ?>&user_id=${userId}&status=${newStatus}`
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Show success message
            alert(data.message);
            // Reload page to show updated status
            window.location.reload();
        } else {
            alert(data.message || 'Failed to update user status');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while updating user status');
    }
}
</script>

<?php require_once ROOT_PATH . '/app/views/layouts/footer.php'; ?>
