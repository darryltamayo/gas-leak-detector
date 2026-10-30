<?php
/**
 * Debug Script - Check what key the cron.php expects
 * 
 * Upload this file and access it to see the expected key
 * URL: https://isu-documentrequest.online/ac_controller_system/public/check-cron-key.php
 */

header('Content-Type: application/json');

echo json_encode([
    'message' => 'This tells you what key cron.php is expecting',
    'instructions' => [
        '1. Look at line 19 in public/cron.php',
        '2. Find the $expectedKey variable',
        '3. Use that exact key in your cronjob URL',
    ],
    'correct_key_should_be' => 'IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T',
    'your_server_file' => 'Check what key is set in your server\'s cron.php file',
    'test_urls' => [
        'with_2026_key' => 'https://isu-documentrequest.online/ac_controller_system/public/cron.php?key=IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T',
        'with_2024_key' => 'https://isu-documentrequest.online/ac_controller_system/public/cron.php?key=IOT_OUTLET_2024_SECURE_xFY_XJ9KP2M8N5QW7R3T',
    ],
    'note' => 'Make sure the cron.php file on your server matches the local version!'
], JSON_PRETTY_PRINT);
