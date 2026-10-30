<?php

/**
 * Application Entry Point
 * 
 * All requests are routed through this file
 */

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define root path
define('ROOT_PATH', dirname(__DIR__));

// Load environment variables
require_once ROOT_PATH . '/app/helpers/EnvironmentHelper.php';
EnvironmentHelper::load(ROOT_PATH . '/.env');

// Start session
session_start();

// Autoloader
spl_autoload_register(function ($class) {
    $file = ROOT_PATH . '/app/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
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
