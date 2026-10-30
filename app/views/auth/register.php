<?php $pageTitle = 'Register - IoT Smart Outlet System'; ?>
<?php require_once ROOT_PATH . '/app/views/layouts/header.php'; ?>

<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
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
    
    <!-- Register Card -->
    <div class="max-w-md w-full space-y-8 relative z-10">
        <!-- Logo and Title -->
        <div class="text-center">
            <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full bg-gradient-to-br from-primary-400 to-primary-600 shadow-lg">
                <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
            </div>
            <h2 class="mt-6 text-3xl font-extrabold bg-gradient-to-r from-primary-600 to-primary-400 bg-clip-text text-transparent">
                Create Account
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Join us to control your smart outlet
            </p>
        </div>
        
        <!-- Register Form -->
        <div class="glass rounded-2xl shadow-2xl p-8 space-y-6">
            <form action="<?= url('/auth/register') ?>" method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                
                <!-- Full Name -->
                <div class="floating-input">
                    <input 
                        type="text" 
                        name="full_name" 
                        id="full_name" 
                        placeholder=" "
                        value="<?= old('full_name') ?>"
                        required
                        class="w-full px-4 py-3 bg-white border <?= isset($_SESSION['errors']['full_name']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none text-gray-900"
                    >
                    <label for="full_name">Full Name</label>
                    <?php if (isset($_SESSION['errors']['full_name'])): ?>
                        <p class="mt-1 text-sm text-red-500"><?= $_SESSION['errors']['full_name'] ?></p>
                    <?php endif; ?>
                </div>
                
                <!-- Username -->
                <div class="floating-input">
                    <input 
                        type="text" 
                        name="username" 
                        id="username" 
                        placeholder=" "
                        value="<?= old('username') ?>"
                        required
                        class="w-full px-4 py-3 bg-white border <?= isset($_SESSION['errors']['username']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none text-gray-900"
                    >
                    <label for="username">Username</label>
                    <?php if (isset($_SESSION['errors']['username'])): ?>
                        <p class="mt-1 text-sm text-red-500"><?= $_SESSION['errors']['username'] ?></p>
                    <?php endif; ?>
                </div>
                
                <!-- Password -->
                <div class="floating-input">
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        placeholder=" "
                        required
                        class="w-full px-4 py-3 bg-white border <?= isset($_SESSION['errors']['password']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none text-gray-900"
                    >
                    <label for="password">Password (min. 8 characters)</label>
                    <?php if (isset($_SESSION['errors']['password'])): ?>
                        <p class="mt-1 text-sm text-red-500"><?= $_SESSION['errors']['password'] ?></p>
                    <?php endif; ?>
                </div>
                
                <!-- Confirm Password -->
                <div class="floating-input">
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        id="password_confirmation" 
                        placeholder=" "
                        required
                        class="w-full px-4 py-3 bg-white border <?= isset($_SESSION['errors']['password_confirmation']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none text-gray-900"
                    >
                    <label for="password_confirmation">Confirm Password</label>
                    <?php if (isset($_SESSION['errors']['password_confirmation'])): ?>
                        <p class="mt-1 text-sm text-red-500"><?= $_SESSION['errors']['password_confirmation'] ?></p>
                    <?php endif; ?>
                </div>
                
                <!-- Terms Agreement -->
                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        name="terms" 
                        id="terms" 
                        required
                        class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                    >
                    <label for="terms" class="ml-2 block text-sm text-gray-700">
                        I agree to the Terms and Conditions
                    </label>
                </div>
                
                <!-- Submit Button -->
                <button 
                    type="submit"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-primary-600 to-primary-500 hover:from-primary-700 hover:to-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transform hover:scale-[1.02] transition"
                >
                    Create Account
                </button>
            </form>
            
            <!-- Divider -->
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">
                        Already have an account?
                    </span>
                </div>
            </div>
            
            <!-- Login Link -->
            <a 
                href="<?= url('/auth/login') ?>"
                class="w-full flex justify-center py-3 px-4 border-2 border-primary-600 rounded-lg text-sm font-medium text-primary-600 hover:bg-primary-50 transition"
            >
                Sign In
            </a>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/app/views/layouts/footer.php'; ?>
