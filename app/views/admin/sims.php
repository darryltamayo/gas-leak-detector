<?php $pageTitle = 'Authorized SIMs - Smart AC Outlet Controller'; ?>
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
        <div class="mb-8 pb-5 flex justify-between items-center" style="border-bottom: 1px solid #E5E5E7;">
            <div>
                <h2 class="text-3xl font-bold flex items-center gap-3" style="color: #1D1D1F;">
                    <span class="w-1 h-8 rounded" style="background: linear-gradient(to bottom, #2D7A5B, #1D583C);"></span>
                    Authorized SIM Cards
                </h2>
                <p class="mt-2 text-sm" style="color: #6E6E73;">Manage phone numbers authorized for SMS control</p>
            </div>
            <a href="<?= url('/admin/create-sim') ?>" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-white font-semibold transition-all active:scale-95 text-sm" style="background: #2D7A5B; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);" onmouseover="this.style.background='#25694B'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)'" onmouseout="this.style.background='#2D7A5B'; this.style.boxShadow='0 1px 3px 0 rgba(0, 0, 0, 0.08)'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add New SIM
            </a>
        </div>
        
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 mb-6 md:mb-8">
            <div class="bg-white rounded-xl p-4 md:p-6 border transition-all duration-300 relative overflow-hidden group" style="border-color: #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.03);" onmouseover="this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.06), 0 2px 4px -2px rgba(0, 0, 0, 0.04)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.boxShadow='0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.03)'; this.style.transform='translateY(0)';">
                <div class="absolute top-3 md:top-5 right-3 md:right-5 opacity-30 group-hover:scale-110 transition-transform" style="color: #D4EDDF;">
                    <svg class="w-8 h-8 md:w-12 md:h-12" fill="currentColor" viewBox="0 0 24 24"><path d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <div class="text-xs uppercase tracking-wide font-semibold mb-2 md:mb-3" style="color: #86868B;">Total SIMs</div>
                <div class="text-2xl md:text-4xl font-bold mb-1 md:mb-2" style="color: #2D7A5B;"><?= count($sims) ?></div>
                <div class="text-xs md:text-sm font-semibold" style="color: #6E6E73;">Registered Numbers</div>
                <div class="absolute top-0 left-0 right-0 h-1 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left" style="background: linear-gradient(to right, #2D7A5B, #25694B);"></div>
            </div>
            <div class="bg-white rounded-xl p-4 md:p-6 border transition-all duration-300 relative overflow-hidden group" style="border-color: #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.03);" onmouseover="this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.06), 0 2px 4px -2px rgba(0, 0, 0, 0.04)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.boxShadow='0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.03)'; this.style.transform='translateY(0)';">
                <div class="absolute top-3 md:top-5 right-3 md:right-5 opacity-30 group-hover:scale-110 transition-transform" style="color: #D4EDDF;">
                    <svg class="w-8 h-8 md:w-12 md:h-12" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                </div>
                <div class="text-xs uppercase tracking-wide font-semibold mb-2 md:mb-3" style="color: #86868B;">Active SIMs</div>
                <div class="text-2xl md:text-4xl font-bold mb-1 md:mb-2" style="color: #2D7A5B;">
                    <?= count(array_filter($sims, fn($s) => $s->is_active)) ?>
                </div>
                <div class="text-xs md:text-sm font-semibold" style="color: #6E6E73;">Active Numbers</div>
                <div class="absolute top-0 left-0 right-0 h-1 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left" style="background: linear-gradient(to right, #2D7A5B, #25694B);"></div>
            </div>
            <div class="bg-white rounded-xl p-4 md:p-6 border transition-all duration-300 relative overflow-hidden group" style="border-color: #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.03);" onmouseover="this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.06), 0 2px 4px -2px rgba(0, 0, 0, 0.04)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.boxShadow='0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.03)'; this.style.transform='translateY(0)';">
                <div class="absolute top-3 md:top-5 right-3 md:right-5 opacity-30 group-hover:scale-110 transition-transform" style="color: #FEE2E2;">
                    <svg class="w-8 h-8 md:w-12 md:h-12" fill="currentColor" viewBox="0 0 24 24"><path d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                </div>
                <div class="text-xs uppercase tracking-wide font-semibold mb-2 md:mb-3" style="color: #86868B;">Inactive SIMs</div>
                <div class="text-2xl md:text-4xl font-bold mb-1 md:mb-2" style="color: #D32F2F;">
                    <?= count(array_filter($sims, fn($s) => !$s->is_active)) ?>
                </div>
                <div class="text-xs md:text-sm font-semibold" style="color: #6E6E73;">Inactive Numbers</div>
                <div class="absolute top-0 left-0 right-0 h-1 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left" style="background: linear-gradient(to right, #D32F2F, #B71C1C);"></div>
            </div>
        </div>
        
        <!-- SIMs Table (ISU Style) -->
        <div class="bg-white rounded-xl overflow-hidden transition-all duration-200" style="border: 1px solid #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);">
            <?php if (empty($sims)): ?>
            <div class="p-8 md:p-12 text-center">
                <svg class="mx-auto w-12 h-12 md:w-16 md:h-16" style="color: #86868B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <h3 class="mt-4 text-lg font-bold" style="color: #1D1D1F;">No SIMs registered</h3>
                <p class="mt-2 text-sm" style="color: #6E6E73;">Get started by adding a new authorized SIM card.</p>
                <div class="mt-6">
                    <a href="<?= url('/admin/create-sim') ?>" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-white font-semibold transition-all active:scale-95 text-sm" style="background: #2D7A5B; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);" onmouseover="this.style.background='#25694B'" onmouseout="this.style.background='#2D7A5B'">
                        Add New SIM
                    </a>
                </div>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead style="background-color: #FAFAFA;">
                        <tr style="border-bottom: 1px solid #E5E5E7;">
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #6E6E73;">Phone Number</th>
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #6E6E73;">Owner</th>
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #6E6E73;">Description</th>
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #6E6E73;">Status</th>
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #6E6E73;">Added By</th>
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #6E6E73;">Date Added</th>
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #6E6E73;">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="background-color: #FFFFFF;">
                        <?php foreach ($sims as $sim): ?>
                        <tr class="transition-all duration-150" style="border-bottom: 1px solid #F5F5F7;" onmouseover="this.style.backgroundColor='#FAFAFA'" onmouseout="this.style.backgroundColor='#FFFFFF'">
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center rounded-full flex-shrink-0" style="width: 40px; height: 40px; background: linear-gradient(135deg, #34D399 0%, #2D7A5B 100%);">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                    </div>
                                    <div class="text-sm font-bold" style="color: #1D1D1F;"><?= e($sim->phone_number) ?></div>
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold" style="color: #1D1D1F;"><?= e($sim->owner_name) ?></td>
                            <td class="px-4 py-4 text-sm max-w-xs truncate" style="color: #6E6E73;"><?= e($sim->description ?? '-') ?></td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full" style="<?= $sim->is_active ? 'background-color: #EDF7F3; color: #2D7A5B;' : 'background-color: #FEE2E2; color: #D32F2F;' ?>">
                                    <?= $sim->is_active ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm" style="color: #86868B;">
                                <?= e($sim->creator_full_name ?? 'Unknown') ?>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm" style="color: #86868B;">
                                <?= date('M d, Y', strtotime($sim->created_at)) ?>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm flex gap-3">
                                <a href="<?= url('/admin/edit-sim?id=' . $sim->id) ?>" 
                                   class="font-semibold transition-colors" style="color: #2D7A5B;" onmouseover="this.style.color='#1D583C'" onmouseout="this.style.color='#2D7A5B'">Edit</a>
                                <button onclick="toggleSimStatus(<?= $sim->id ?>, <?= $sim->is_active ? 0 : 1 ?>)" 
                                        class="font-semibold transition-colors" style="color: #F59E0B;" onmouseover="this.style.color='#D97706'" onmouseout="this.style.color='#F59E0B'">
                                    <?= $sim->is_active ? 'Deactivate' : 'Activate' ?>
                                </button>
                                <button onclick="deleteSim(<?= $sim->id ?>)" 
                                        class="font-semibold transition-colors" style="color: #D32F2F;" onmouseover="this.style.color='#B71C1C'" onmouseout="this.style.color='#D32F2F'">Delete</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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

function toggleSimStatus(simId, newStatus) {
    const actionText = newStatus === 1 ? 'activate' : 'deactivate';
    if (!confirm(`Are you sure you want to ${actionText} this SIM?`)) return;
    
    const csrfToken = '<?= csrf_token() ?>';
    
    fetch('<?= url('/admin/toggle-sim-status') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `csrf_token=${csrfToken}&sim_id=${simId}&is_active=${newStatus}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Failed to update SIM status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating SIM status');
    });
}

function deleteSim(simId) {
    if (!confirm('Are you sure you want to delete this SIM? This action cannot be undone.')) return;
    
    const csrfToken = '<?= csrf_token() ?>';
    
    fetch('<?= url('/admin/delete-sim') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `csrf_token=${csrfToken}&sim_id=${simId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Failed to delete SIM');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting SIM');
    });
}
</script>

<?php require_once ROOT_PATH . '/app/views/layouts/footer.php'; ?>
