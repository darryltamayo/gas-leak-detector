<!-- Sidebar Navigation (Exact ISU Style) -->
<aside id="sidebar" class="fixed top-14 sm:top-16 left-0 w-64 h-[calc(100vh-3.5rem)] sm:h-[calc(100vh-4rem)] bg-white border-r overflow-y-auto transition-all duration-300 ease-in-out z-40" style="border-color: #E5E5E7; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.06), 0 4px 6px -4px rgba(0, 0, 0, 0.04); transform: translateX(0);">
    <!-- User Info Section (Exact ISU Style) -->
    <div class="p-4 border-b flex items-center justify-center" style="background-color: #FAFAFA; border-color: #E5E5E7;">
        <div class="flex items-center gap-3 w-full">
            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style="background-color: #EDF7F3;">
                <span class="font-bold text-base" style="color: #2D7A5B;"><?= strtoupper(substr($user['full_name'], 0, 1)) ?></span>
            </div>
            <div class="leading-tight min-w-0">
                <div class="font-bold text-sm truncate" style="color: #1D1D1F;"><?= e($user['full_name']) ?></div>
                <div class="text-xs font-semibold" style="color: #6E6E73;"><?= ucfirst(e($user['role'])) ?></div>
            </div>
        </div>
    </div>
        
    <!-- Navigation Links (Exact ISU Style) -->
    <ul class="list-none p-4 space-y-1">
        <li>
            <a href="<?= url('/dashboard') ?>" 
               class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-semibold text-sm transition-all <?php 
               $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
               $dashboardPath = parse_url(url('/dashboard'), PHP_URL_PATH);
               if ($currentPath === $dashboardPath || $currentPath === '/dashboard' || $currentPath === '/') {
                   echo 'border-l-3';
               }
               ?>" style="<?php 
               $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
               $dashboardPath = parse_url(url('/dashboard'), PHP_URL_PATH);
               if ($currentPath === $dashboardPath || $currentPath === '/dashboard' || $currentPath === '/') {
                   echo 'background: #EDF7F3; color: #2D7A5B; border-left: 3px solid #2D7A5B; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);';
               } else {
                   echo 'color: #6E6E73;';
               }
               ?>" onmouseover="if (!this.classList.contains('border-l-3')) { this.style.background='#FAFAFA'; this.style.color='#1D1D1F'; }" onmouseout="if (!this.classList.contains('border-l-3')) { this.style.background='transparent'; this.style.color='#6E6E73'; }">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Dashboard</span>
            </a>
        </li>
                
        <?php if ($user['role'] === 'admin'): ?>
        <li>
            <a href="<?= url('/admin/users') ?>" 
               class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm transition-all <?php 
               $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
               if (strpos($currentPath, '/admin/users') !== false || strpos($currentPath, '/admin/create-user') !== false || strpos($currentPath, '/admin/edit-user') !== false) {
                   echo 'font-semibold border-l-3';
               } else {
                   echo 'font-medium';
               }
               ?>" style="<?php 
               $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
               if (strpos($currentPath, '/admin/users') !== false || strpos($currentPath, '/admin/create-user') !== false || strpos($currentPath, '/admin/edit-user') !== false) {
                   echo 'background: #EDF7F3; color: #2D7A5B; border-left: 3px solid #2D7A5B; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);';
               } else {
                   echo 'color: #6E6E73;';
               }
               ?>" onmouseover="if (!this.classList.contains('border-l-3')) { this.style.background='#FAFAFA'; this.style.color='#1D1D1F'; }" onmouseout="if (!this.classList.contains('border-l-3')) { this.style.background='transparent'; this.style.color='#6E6E73'; }">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span>Users</span>
            </a>
        </li>
        <li>
            <a href="<?= url('/admin/sims') ?>" 
               class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm transition-all <?php 
               $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
               if (strpos($currentPath, '/admin/sims') !== false || strpos($currentPath, '/admin/create-sim') !== false || strpos($currentPath, '/admin/edit-sim') !== false) {
                   echo 'font-semibold border-l-3';
               } else {
                   echo 'font-medium';
               }
               ?>" style="<?php 
               $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
               if (strpos($currentPath, '/admin/sims') !== false || strpos($currentPath, '/admin/create-sim') !== false || strpos($currentPath, '/admin/edit-sim') !== false) {
                   echo 'background: #EDF7F3; color: #2D7A5B; border-left: 3px solid #2D7A5B; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);';
               } else {
                   echo 'color: #6E6E73;';
               }
               ?>" onmouseover="if (!this.classList.contains('border-l-3')) { this.style.background='#FAFAFA'; this.style.color='#1D1D1F'; }" onmouseout="if (!this.classList.contains('border-l-3')) { this.style.background='transparent'; this.style.color='#6E6E73'; }">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span>SIM Cards</span>
            </a>
        </li>
        <li>
            <a href="<?= url('/device') ?>" 
               class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm transition-all <?php 
               $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
               if (strpos($currentPath, '/device') !== false) {
                   echo 'font-semibold border-l-3';
               } else {
                   echo 'font-medium';
               }
               ?>" style="<?php 
               $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
               if (strpos($currentPath, '/device') !== false) {
                   echo 'background: #EDF7F3; color: #2D7A5B; border-left: 3px solid #2D7A5B; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);';
               } else {
                   echo 'color: #6E6E73;';
               }
               ?>" onmouseover="if (!this.classList.contains('border-l-3')) { this.style.background='#FAFAFA'; this.style.color='#1D1D1F'; }" onmouseout="if (!this.classList.contains('border-l-3')) { this.style.background='transparent'; this.style.color='#6E6E73'; }">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                <span>ESP32 Devices</span>
            </a>
        </li>
        <li>
            <a href="<?= url('/admin/activity-logs') ?>" 
               class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm transition-all <?php 
               $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
               if (strpos($currentPath, '/admin/activity-logs') !== false) {
                   echo 'font-semibold border-l-3';
               } else {
                   echo 'font-medium';
               }
               ?>" style="<?php 
               $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
               if (strpos($currentPath, '/admin/activity-logs') !== false) {
                   echo 'background: #EDF7F3; color: #2D7A5B; border-left: 3px solid #2D7A5B; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);';
               } else {
                   echo 'color: #6E6E73;';
               }
               ?>" onmouseover="if (!this.classList.contains('border-l-3')) { this.style.background='#FAFAFA'; this.style.color='#1D1D1F'; }" onmouseout="if (!this.classList.contains('border-l-3')) { this.style.background='transparent'; this.style.color='#6E6E73'; }">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Activity Logs</span>
            </a>
        </li>
        <?php endif; ?>
        
        <li>
            <a href="<?= url('/outlet') ?>" 
               class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm transition-all <?php 
               $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
               if (strpos($currentPath, '/outlet') !== false) {
                   echo 'font-semibold border-l-3';
               } else {
                   echo 'font-medium';
               }
               ?>" style="<?php 
               $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
               if (strpos($currentPath, '/outlet') !== false) {
                   echo 'background: #EDF7F3; color: #2D7A5B; border-left: 3px solid #2D7A5B; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);';
               } else {
                   echo 'color: #6E6E73;';
               }
               ?>" onmouseover="if (!this.classList.contains('border-l-3')) { this.style.background='#FAFAFA'; this.style.color='#1D1D1F'; }" onmouseout="if (!this.classList.contains('border-l-3')) { this.style.background='transparent'; this.style.color='#6E6E73'; }">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <span>Outlets</span>
            </a>
        </li>
        
        <li>
            <a href="<?= url('/schedule') ?>" 
               class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm transition-all <?php 
               $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
               if (strpos($currentPath, '/schedule') !== false) {
                   echo 'font-semibold border-l-3';
               } else {
                   echo 'font-medium';
               }
               ?>" style="<?php 
               $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
               if (strpos($currentPath, '/schedule') !== false) {
                   echo 'background: #EDF7F3; color: #2D7A5B; border-left: 3px solid #2D7A5B; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);';
               } else {
                   echo 'color: #6E6E73;';
               }
               ?>" onmouseover="if (!this.classList.contains('border-l-3')) { this.style.background='#FAFAFA'; this.style.color='#1D1D1F'; }" onmouseout="if (!this.classList.contains('border-l-3')) { this.style.background='transparent'; this.style.color='#6E6E73'; }">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Schedules</span>
            </a>
        </li>
        
        <!-- Documentation -->
        <li>
            <a href="<?= url('/documentation') ?>" 
               class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm transition-all <?php 
               $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
               if (strpos($currentPath, '/documentation') !== false) {
                   echo 'font-semibold border-l-3';
               } else {
                   echo 'font-medium';
               }
               ?>" style="<?php 
               $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
               if (strpos($currentPath, '/documentation') !== false) {
                   echo 'background: #EDF7F3; color: #2D7A5B; border-left: 3px solid #2D7A5B; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);';
               } else {
                   echo 'color: #6E6E73;';
               }
               ?>" onmouseover="if (!this.classList.contains('border-l-3')) { this.style.background='#FAFAFA'; this.style.color='#1D1D1F'; }" onmouseout="if (!this.classList.contains('border-l-3')) { this.style.background='transparent'; this.style.color='#6E6E73'; }">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>Documentation</span>
            </a>
        </li>
    </ul>
    
    <!-- Profile Section -->
    <div class="px-4 py-3 border-t" style="border-color: #E5E5E7;">
        <a href="<?= url('/profile') ?>" 
           class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm transition-all <?php 
           $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
           if (strpos($currentPath, '/profile') !== false) {
               echo 'font-semibold border-l-3';
           } else {
               echo 'font-medium';
           }
           ?>" style="<?php 
           $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
           if (strpos($currentPath, '/profile') !== false) {
               echo 'background: #EDF7F3; color: #2D7A5B; border-left: 3px solid #2D7A5B; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);';
           } else {
               echo 'color: #6E6E73;';
           }
           ?>" onmouseover="if (!this.classList.contains('border-l-3')) { this.style.background='#FAFAFA'; this.style.color='#1D1D1F'; }" onmouseout="if (!this.classList.contains('border-l-3')) { this.style.background='transparent'; this.style.color='#6E6E73'; }">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span>My Profile</span>
        </a>
    </div>
    
    <!-- Premium Logout Button -->
    <div class="p-4 border-t mt-auto" style="border-color: #E5E5E7;">
        <a href="<?= url('/auth/logout') ?>" class="flex items-center justify-center gap-2 w-full px-4 py-3 rounded-xl text-white font-semibold transition-all active:scale-95 text-sm" style="background-color: #D32F2F; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);" onmouseover="this.style.backgroundColor='#B71C1C'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)'" onmouseout="this.style.backgroundColor='#D32F2F'; this.style.boxShadow='0 1px 3px 0 rgba(0, 0, 0, 0.08)'">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            <span>Logout</span>
        </a>
    </div>
</aside>

<!-- Sidebar Overlay for Mobile -->
<div class="fixed inset-0 bg-black bg-opacity-50 z-30 opacity-0 invisible transition-all duration-300" style="top: 56px;" id="sidebar-overlay"></div>
