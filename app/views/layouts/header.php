<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Smart AC Outlet Controller Management System">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <title><?= $pageTitle ?? 'Smart AC Outlet Controller' ?></title>
    
    <!-- Force Light Mode Immediately -->
    <script>
        // Force remove dark class before page renders
        document.documentElement.classList.remove('dark');
        localStorage.setItem('darkMode', 'light');
    </script>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Tailwind Config (Exact ISU Colors) -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'brand-green': {
                            50: '#EDF7F3',
                            100: '#D4EDDF',
                            200: '#A8DABF',
                            500: '#2D7A5B',
                            600: '#25694B',
                            700: '#1D583C',
                            800: '#15472D',
                        },
                        primary: {
                            50: '#EDF7F3',
                            100: '#D4EDDF',
                            200: '#A8DABF',
                            300: '#7BC89F',
                            400: '#4FB57F',
                            500: '#2D7A5B',
                            600: '#25694B',
                            700: '#1D583C',
                            800: '#15472D',
                            900: '#0D361E',
                        }
                    },
                    keyframes: {
                        slideDown: {
                            'from': { opacity: '0', transform: 'translateY(-10px)' },
                            'to': { opacity: '1', transform: 'translateY(0)' }
                        },
                        fadeIn: {
                            'from': { opacity: '0', transform: 'translateY(20px)' },
                            'to': { opacity: '1', transform: 'translateY(0)' }
                        }
                    },
                    animation: {
                        slideDown: 'slideDown 0.3s ease-out',
                        fadeIn: 'fadeIn 0.5s ease-out'
                    }
                }
            }
        }
    </script>
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="<?= url('public/assets/css/style.css') ?>">
    
    <style>
        /* ISU Document Request System - Exact Styles */
        
        /* Glassmorphism effect (ISU Style) */
        .glass {
            background: rgba(255, 255, 255, 0.80);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid #E5E5E7;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);
        }
        
        .glass:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.06), 0 2px 4px -2px rgba(0, 0, 0, 0.04);
        }
        
        /* Smooth transitions */
        * {
            transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
        }
        
        /* Custom scrollbar (ISU thin style) */
        ::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 2px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* ISU Color Variables */
        :root {
            --brand-green-50: #EDF7F3;
            --brand-green-100: #D4EDDF;
            --brand-green-500: #2D7A5B;
            --brand-green-600: #25694B;
            --brand-green-700: #1D583C;
            --text-primary: #1D1D1F;
            --text-secondary: #6E6E73;
            --text-muted: #86868B;
            --border-color: #E5E5E7;
            --bg-subtle: #FAFAFA;
            --bg-page: #F5F5F7;
        }
        
        /* Animations */
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-slideDown {
            animation: slideDown 0.3s ease-out;
        }
        
        .animate-fadeIn {
            animation: fadeIn 0.5s ease-out;
        }
    </style>
</head>
<body style="background-color: #F5F5F7;" class="text-gray-900 min-h-screen">
