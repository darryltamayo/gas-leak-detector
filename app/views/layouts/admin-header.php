<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="IoT Smart Outlet Management System">
    <title><?= $pageTitle ?? 'IoT Smart Outlet System' ?></title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            200: '#a7f3d0',
                            300: '#6ee7b7',
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
    
    <style>
        .glass {
            background: #ffffff;
            backdrop-filter: blur(10px);
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .glass:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        * {
            transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900">
    
<!-- Admin Navigation -->
<nav class="glass border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo and Navigation -->
            <div class="flex items-center space-x-8">
                <a href="<?= url('/dashboard') ?>" class="flex items-center">
                    <div class="h-10 w-10 flex items-center justify-center rounded-lg bg-gradient-to-br from-primary-400 to-primary-600">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <span class="ml-3 text-xl font-bold bg-gradient-to-r from-primary-600 to-primary-400 bg-clip-text text-transparent">
                        IoT Outlet
                    </span>
                </a>
                
                <!-- Admin Navigation Links -->
                <div class="hidden md:flex space-x-4">
                    <a href="<?= url('/dashboard') ?>" 
                       class="px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-primary-600 transition">
                        Dashboard
                    </a>
                    <a href="<?= url('/outlet') ?>" 
                       class="px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-primary-600 transition">
                        Outlet Control
                    </a>
                    <a href="<?= url('/admin/users') ?>" 
                       class="px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-primary-600 transition">
                        Users
                    </a>
                    <a href="<?= url('/admin/sims') ?>" 
                       class="px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-primary-600 transition">
                        SIM Numbers
                    </a>
                </div>
            </div>
            
            <!-- Right Menu -->
            <div class="flex items-center space-x-4">
                <!-- User Menu -->
                <div class="flex items-center space-x-3">
                    <div class="text-right hidden md:block">
                        <p class="text-sm font-medium text-gray-900"><?= e(user()['full_name']) ?></p>
                        <p class="text-xs text-gray-500 capitalize"><?= e(user()['role']) ?></p>
                    </div>
                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-semibold">
                        <?= strtoupper(substr(user()['full_name'], 0, 1)) ?>
                    </div>
                </div>
                
                <!-- Logout Button -->
                <a href="<?= url('/auth/logout') ?>" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="hidden md:inline">Logout</span>
                </a>
            </div>
        </div>
    </div>
</nav>
