<?php $pageTitle = 'Create User - Admin'; ?>
<?php require_once ROOT_PATH . '/app/views/layouts/header.php'; ?>
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
        </div>
    </header>
    
    <!-- Main Content Area (Exact ISU Style) -->
    <main id="main-content" class="transition-all duration-300 ease-in-out mt-14 sm:mt-16 p-4 md:p-6 lg:p-8 min-h-[calc(100vh-3.5rem)] sm:min-h-[calc(100vh-4rem)] lg:ml-64" style="background-color: #F5F5F7;">
        <!-- Page Header (Exact ISU Style) -->
        <div class="mb-8 pb-5" style="border-bottom: 1px solid #E5E5E7;">
            <div class="flex items-center gap-3 mb-3">
                <a href="<?= url('/admin/users') ?>" 
                   class="flex items-center justify-center w-10 h-10 rounded-xl transition-all duration-200 active:scale-95" 
                   style="background: #FAFAFA; border: 1px solid #E5E5E7;"
                   onmouseover="this.style.background='#E5E5E7'" 
                   onmouseout="this.style.background='#FAFAFA'">
                    <svg class="w-5 h-5" style="color: #1D1D1F;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h2 class="text-3xl font-bold flex items-center gap-3" style="color: #1D1D1F;">
                    <span class="w-1 h-8 rounded" style="background: linear-gradient(to bottom, #2D7A5B, #1D583C);"></span>
                    Create New User
                </h2>
            </div>
            <p class="mt-2 text-sm" style="color: #6E6E73;">Add a new user to the system</p>
        </div>
        
        <!-- Form Card -->
        <div class="max-w-2xl">
            <div class="bg-white rounded-xl p-6 md:p-8 transition-all duration-200" style="border: 1px solid #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);">
                <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-6 p-4 rounded-xl" style="background-color: #FEF2F2; border: 1px solid #FEE2E2;">
                    <p class="text-sm font-medium" style="color: #DC2626;"><?= e($_SESSION['error']) ?></p>
                    <?php unset($_SESSION['error']); ?>
                </div>
                <?php endif; ?>
                
                <form action="<?= url('/admin/process-create-user') ?>" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    
                    <!-- Username -->
                    <div class="mb-6">
                        <label for="username" class="block text-sm font-medium mb-2" style="color: #1D1D1F;">
                            Username <span style="color: #DC2626;">*</span>
                        </label>
                        <input type="text" 
                               id="username" 
                               name="username" 
                               required
                               class="w-full px-4 py-3 rounded-lg outline-none transition-all text-sm"
                               style="background-color: #FFFFFF; border: 1px solid #E5E5E7; color: #1D1D1F;"
                               onfocus="this.style.borderColor='#2D7A5B'; this.style.boxShadow='0 0 0 3px rgba(45, 122, 91, 0.1)'" 
                               onblur="this.style.borderColor='#E5E5E7'; this.style.boxShadow='none'"
                               placeholder="john_doe">
                        <p class="mt-1 text-xs" style="color: #86868B;">Username must be unique and lowercase</p>
                    </div>
                    
                    <!-- First Name -->
                    <div class="mb-6">
                        <label for="first_name" class="block text-sm font-medium mb-2" style="color: #1D1D1F;">
                            First Name <span style="color: #DC2626;">*</span>
                        </label>
                        <input type="text" 
                               id="first_name" 
                               name="first_name" 
                               required
                               class="w-full px-4 py-3 rounded-lg outline-none transition-all text-sm"
                               style="background-color: #FFFFFF; border: 1px solid #E5E5E7; color: #1D1D1F;"
                               onfocus="this.style.borderColor='#2D7A5B'; this.style.boxShadow='0 0 0 3px rgba(45, 122, 91, 0.1)'" 
                               onblur="this.style.borderColor='#E5E5E7'; this.style.boxShadow='none'"
                               placeholder="John">
                    </div>
                    
                    <!-- Last Name -->
                    <div class="mb-6">
                        <label for="last_name" class="block text-sm font-medium mb-2" style="color: #1D1D1F;">
                            Last Name <span style="color: #DC2626;">*</span>
                        </label>
                        <input type="text" 
                               id="last_name" 
                               name="last_name" 
                               required
                               class="w-full px-4 py-3 rounded-lg outline-none transition-all text-sm"
                               style="background-color: #FFFFFF; border: 1px solid #E5E5E7; color: #1D1D1F;"
                               onfocus="this.style.borderColor='#2D7A5B'; this.style.boxShadow='0 0 0 3px rgba(45, 122, 91, 0.1)'" 
                               onblur="this.style.borderColor='#E5E5E7'; this.style.boxShadow='none'"
                               placeholder="Doe">
                    </div>
                    
                    <!-- Password -->
                    <div class="mb-6">
                        <label for="password" class="block text-sm font-medium mb-2" style="color: #1D1D1F;">
                            Password <span style="color: #DC2626;">*</span>
                        </label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               required
                               minlength="6"
                               class="w-full px-4 py-3 rounded-lg outline-none transition-all text-sm"
                               style="background-color: #FFFFFF; border: 1px solid #E5E5E7; color: #1D1D1F;"
                               onfocus="this.style.borderColor='#2D7A5B'; this.style.boxShadow='0 0 0 3px rgba(45, 122, 91, 0.1)'" 
                               onblur="this.style.borderColor='#E5E5E7'; this.style.boxShadow='none'"
                               placeholder="••••••••">
                        <p class="mt-1 text-xs" style="color: #86868B;">Minimum 6 characters</p>
                    </div>
                    
                    <!-- Role -->
                    <div class="mb-6">
                        <label for="role" class="block text-sm font-medium mb-2" style="color: #1D1D1F;">
                            Role <span style="color: #DC2626;">*</span>
                        </label>
                        <select id="role" 
                                name="role" 
                                required
                                class="w-full px-4 py-3 rounded-lg outline-none transition-all text-sm"
                                style="background-color: #FFFFFF; border: 1px solid #E5E5E7; color: #1D1D1F;"
                                onfocus="this.style.borderColor='#2D7A5B'; this.style.boxShadow='0 0 0 3px rgba(45, 122, 91, 0.1)'" 
                                onblur="this.style.borderColor='#E5E5E7'; this.style.boxShadow='none'">
                            <option value="user">User (Regular Access)</option>
                            <option value="admin">Admin (Full Access)</option>
                        </select>
                        <p class="mt-1 text-xs" style="color: #86868B;">Admin users can manage system settings and other users</p>
                    </div>
                    
                    <!-- Register SIM Card Option -->
                    <div class="mb-8 p-4 rounded-xl" style="background-color: #EDF7F3; border: 1px solid #D4EDDF;">
                        <div class="flex items-start gap-3">
                            <input type="checkbox" 
                                   id="register_sim" 
                                   name="register_sim" 
                                   value="1"
                                   class="mt-1 w-4 h-4 rounded transition-all"
                                   style="border-color: #2D7A5B; color: #2D7A5B;"
                                   onchange="toggleSimFields()">
                            <div class="flex-1">
                                <label for="register_sim" class="block text-sm font-semibold cursor-pointer" style="color: #1D1D1F;">
                                    Also register SIM card for SMS control
                                </label>
                                <p class="mt-1 text-xs" style="color: #6E6E73;">Enable this to authorize a phone number for SMS commands</p>
                            </div>
                        </div>
                        
                        <!-- SIM Card Fields (Hidden by default) -->
                        <div id="sim_fields" style="display: none;" class="mt-4 pt-4" style="border-top: 1px solid #D4EDDF;">
                            <div class="mb-4">
                                <label for="sim_phone_number" class="block text-sm font-semibold mb-2" style="color: #1D1D1F;">
                                    Phone Number <span style="color: #DC2626;">*</span>
                                </label>
                                <input type="text" 
                                       id="sim_phone_number" 
                                       name="sim_phone_number" 
                                       pattern="\+639[0-9]{9}"
                                       class="w-full px-4 py-3 rounded-lg outline-none transition-all text-sm"
                                       style="background-color: #FFFFFF; border: 1px solid #E5E5E7; color: #1D1D1F;"
                                       onfocus="this.style.borderColor='#2D7A5B'; this.style.boxShadow='0 0 0 3px rgba(45, 122, 91, 0.1)'" 
                                       onblur="this.style.borderColor='#E5E5E7'; this.style.boxShadow='none'"
                                       placeholder="+639XXXXXXXXX">
                                <p class="mt-1 text-xs" style="color: #86868B;">Format: +639XXXXXXXXX (Philippines mobile number)</p>
                            </div>
                            
                            <div class="mb-4">
                                <label for="sim_description" class="block text-sm font-semibold mb-2" style="color: #1D1D1F;">
                                    Description (Optional)
                                </label>
                                <textarea id="sim_description" 
                                          name="sim_description" 
                                          rows="2"
                                          class="w-full px-4 py-3 rounded-lg outline-none transition-all text-sm"
                                          style="background-color: #FFFFFF; border: 1px solid #E5E5E7; color: #1D1D1F;"
                                          onfocus="this.style.borderColor='#2D7A5B'; this.style.boxShadow='0 0 0 3px rgba(45, 122, 91, 0.1)'" 
                                          onblur="this.style.borderColor='#E5E5E7'; this.style.boxShadow='none'"
                                          placeholder="Personal mobile number for SMS control"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Buttons -->
                    <div class="flex space-x-4">
                        <button type="submit" 
                                class="flex-1 px-6 py-3 text-white rounded-xl font-semibold transition-all duration-200 active:scale-95 text-sm"
                                style="background: #2D7A5B; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);" 
                                onmouseover="this.style.background='#25694B'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)'" 
                                onmouseout="this.style.background='#2D7A5B'; this.style.boxShadow='0 1px 3px 0 rgba(0, 0, 0, 0.08)'">
                            Create User
                        </button>
                        <a href="<?= url('/admin/users') ?>" 
                           class="flex-1 px-6 py-3 rounded-xl font-semibold transition-all duration-200 text-center text-sm"
                           style="background: #FAFAFA; color: #1D1D1F;" 
                           onmouseover="this.style.background='#E5E5E7'" 
                           onmouseout="this.style.background='#FAFAFA'">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
// Toggle SIM card fields visibility
function toggleSimFields() {
    const checkbox = document.getElementById('register_sim');
    const simFields = document.getElementById('sim_fields');
    const phoneInput = document.getElementById('sim_phone_number');
    
    if (checkbox.checked) {
        simFields.style.display = 'block';
        phoneInput.setAttribute('required', 'required');
    } else {
        simFields.style.display = 'none';
        phoneInput.removeAttribute('required');
        phoneInput.value = '';
        document.getElementById('sim_description').value = '';
    }
}

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

<?php require_once ROOT_PATH . '/app/views/layouts/footer.php'; ?>
