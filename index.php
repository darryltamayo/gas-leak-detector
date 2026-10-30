<?php

/**
 * Application Entry Point
 * 
 * All requests are routed through this file
 */

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone to Philippines
date_default_timezone_set('Asia/Manila');

// Define root path
define('ROOT_PATH', __DIR__);

// Load environment variables
require_once ROOT_PATH . '/app/helpers/EnvironmentHelper.php';
EnvironmentHelper::load(ROOT_PATH . '/.env');

// Start session with proper configuration
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_samesite', 'Lax');
    
    session_start();
    
    // Regenerate session ID periodically for security
    if (!isset($_SESSION['created'])) {
        $_SESSION['created'] = time();
    } else if (time() - $_SESSION['created'] > 1800) {
        // Session started more than 30 minutes ago
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
}

// Autoloader
spl_autoload_register(function ($class) {
    // Handle different class types
    $paths = [
        ROOT_PATH . '/app/controllers/' . $class . '.php',
        ROOT_PATH . '/app/models/' . $class . '.php',
        ROOT_PATH . '/app/repositories/' . $class . '.php',
        ROOT_PATH . '/app/services/' . $class . '.php',
        ROOT_PATH . '/app/middleware/' . $class . '.php',
        ROOT_PATH . '/app/' . $class . '.php',
    ];
    
    foreach ($paths as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Initialize application
try {
    $router = new Router();
    $router->dispatch();
} catch (Exception $e) {
    // Log error
    error_log($e->getMessage());
    
    // Show error page
    http_response_code(500);
    if (config('app.debug')) {
        echo '<h1>Application Error</h1>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    } else {
        echo '<h1>500 Internal Server Error</h1>';
        echo '<p>Something went wrong. Please try again later.</p>';
    }
}
