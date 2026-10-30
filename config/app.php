<?php

/**
 * Application Configuration
 * 
 * This file contains general application settings
 */

return [
    'name' => $_ENV['APP_NAME'] ?? 'IoT Smart Outlet System',
    'env' => $_ENV['APP_ENV'] ?? 'development',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? true, FILTER_VALIDATE_BOOLEAN),
    'url' => $_ENV['APP_URL'] ?? 'http://localhost',
    'timezone' => $_ENV['APP_TIMEZONE'] ?? 'UTC',
    
    'session' => [
        'lifetime' => (int)($_ENV['SESSION_LIFETIME'] ?? 7200),
        'name' => $_ENV['SESSION_NAME'] ?? 'IOT_OUTLET_SESSION',
        'secure' => false, // Set to true in production with HTTPS
        'httponly' => true,
        'samesite' => 'Lax'
    ],
    
    'security' => [
        'api_key' => $_ENV['API_KEY'] ?? '',
        'csrf_token_length' => (int)($_ENV['CSRF_TOKEN_LENGTH'] ?? 32),
    ],
    
    'esp32' => [
        'heartbeat_timeout' => (int)($_ENV['ESP32_HEARTBEAT_TIMEOUT'] ?? 120),
    ],
    
    'logging' => [
        'level' => $_ENV['LOG_LEVEL'] ?? 'debug',
        'path' => $_ENV['LOG_PATH'] ?? '../storage/logs/',
    ],
    
    'pagination' => [
        'per_page' => 20,
    ]
];
