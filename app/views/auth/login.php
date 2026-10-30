<?php $pageTitle = 'Login - Smart AC Outlet Controller'; ?>
<?php require_once ROOT_PATH . '/app/views/layouts/header.php'; ?>

<div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8 pb-20">
    <!-- Background Pattern -->
    <div class="absolute inset-0 z-0 opacity-5">
        <svg class="h-full w-full" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                    <path d="M 40 0 L 0 0 0 40" fill="none" stroke="currentColor" stroke-width="1"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#grid)" />
        </svg>
    </div>
    
    <!-- Login Card -->
    <div class="max-w-md w-full relative z-10">
        <!-- Login Form -->
        <div class="glass rounded-2xl shadow-2xl p-6 space-y-4">
            <!-- Logo and Title -->
            <div class="text-center">
                <div class="mx-auto h-16 w-16 flex items-center justify-center">
                    <img src="<?= url('public/assets/images/isu_logo.png') ?>" alt="ISU Logo" class="h-full w-full object-contain">
                </div>
                <h2 class="mt-3 text-xl font-bold text-gray-800">
                    Smart AC Outlet Controller
                </h2>
                <p class="mt-1 text-xs text-gray-500">
                    Sign in to your account
                </p>
            </div>
            
            <form action="<?= url('/auth/login') ?>" method="POST" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                
                <!-- Username -->
                <div>
                    <label for="identifier" class="block text-xs font-medium text-gray-700 mb-1">
                        Username
                    </label>
                    <input 
                        type="text" 
                        name="identifier" 
                        id="identifier" 
                        value="<?= old('identifier') ?>"
                        required
                        class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition"
                        placeholder="Enter your username"
                    >
                </div>
                
                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-medium text-gray-700 mb-1">
                        Password
                    </label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required
                        class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition"
                        placeholder="Enter your password"
                    >
                </div>
                
                <!-- Submit Button -->
                <button 
                    type="submit"
                    class="w-full mt-4 py-2.5 px-4 bg-gradient-to-r from-primary-600 to-primary-500 text-white text-sm font-medium rounded-lg hover:from-primary-700 hover:to-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transform hover:scale-[1.01] transition shadow-md"
                >
                    Sign In
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/app/views/layouts/footer.php'; ?>
