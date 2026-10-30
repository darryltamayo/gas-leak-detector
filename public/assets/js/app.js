/**
 * Main JavaScript for IoT Smart Outlet System
 */

// Initialize app
document.addEventListener('DOMContentLoaded', function() {
    console.log('IoT Smart Outlet System Initialized');
});

// Helper function for AJAX requests
async function ajax(url, options = {}) {
    const defaults = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    };
    
    const config = { ...defaults, ...options };
    
    try {
        const response = await fetch(url, config);
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('AJAX Error:', error);
        throw error;
    }
}
