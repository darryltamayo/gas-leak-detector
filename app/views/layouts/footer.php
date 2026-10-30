    <!-- Toast Notification Container -->
    <div id="toast-container" class="fixed bottom-4 right-4 z-50 space-y-2"></div>
    
    <!-- Footer -->
    <footer class="mt-8 py-4 bg-white border-t border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center text-xs text-gray-500">
                <p>© 2026 Arduino Lezzgo. All Rights Reserved.</p>
                <p class="mt-1">This system was designed and developed by Arduino Lezzgo.</p>
                <p class="mt-1 text-gray-400">Version 1.0.0</p>
            </div>
        </div>
    </footer>
    
    <!-- Force Light Mode Script -->
    <script>
        // Remove dark mode class if present
        document.documentElement.classList.remove('dark');
        // Clear dark mode from localStorage
        localStorage.removeItem('darkMode');
        localStorage.setItem('darkMode', 'light');
    </script>
    
    <!-- Custom JavaScript -->
    <script src="<?= url('public/assets/js/app.js') ?>"></script>
    
    <script>
        // Toast notification function
        function showToast(message, type = 'info') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            const colors = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                info: 'bg-blue-500',
                warning: 'bg-yellow-500'
            };
            
            toast.className = `${colors[type]} text-white px-6 py-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-0 opacity-100`;
            toast.textContent = message;
            
            container.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
        
        // Show PHP session messages
        <?php if (isset($_SESSION['success'])): ?>
            showToast(<?= json_encode($_SESSION['success']) ?>, 'success');
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            showToast(<?= json_encode($_SESSION['error']) ?>, 'error');
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </script>
</body>
</html>
<?php
// Clear old input data
unset($_SESSION['old']);
unset($_SESSION['errors']);
?>
