<?php

/**
 * Security Configuration
 */

return [
    'api_key' => $_ENV['API_KEY'] ?? '',
    'csrf_token_length' => (int)($_ENV['CSRF_TOKEN_LENGTH'] ?? 32),
];
