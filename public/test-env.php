<?php
/**
 * Environment Test Script
 * 
 * This script helps diagnose .env loading issues
 * 
 * Access: https://isu-documentrequest.online/ac_controller_system/public/test-env.php
 * 
 * DELETE THIS FILE AFTER FIXING THE ISSUE!
 */

// Define root path
define('ROOT_PATH', dirname(__DIR__));

// Load environment helper
require_once ROOT_PATH . '/app/helpers/EnvironmentHelper.php';

// Load environment
EnvironmentHelper::load(ROOT_PATH . '/.env');

// Load config helper
require_once ROOT_PATH . '/app/helpers/functions.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Environment Test</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .pass { color: green; font-weight: bold; }
        .fail { color: red; font-weight: bold; }
        .info { color: blue; }
        table { background: white; border-collapse: collapse; width: 100%; max-width: 800px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #333; color: white; }
        .warning { background: #fff3cd; padding: 15px; border: 1px solid #ffc107; margin: 20px 0; max-width: 800px; }
    </style>
</head>
<body>
    <h1>🔧 Environment Configuration Test</h1>
    
    <div class="warning">
        <strong>⚠️ SECURITY WARNING:</strong> Delete this file after fixing the issue! 
        This file exposes sensitive configuration information.
    </div>
    
    <h2>1. File System Checks</h2>
    <table>
        <tr>
            <th>Check</th>
            <th>Status</th>
            <th>Details</th>
        </tr>
        <tr>
            <td>ROOT_PATH</td>
            <td class="info">INFO</td>
            <td><?= ROOT_PATH ?></td>
        </tr>
        <tr>
            <td>.env file exists</td>
            <td class="<?= file_exists(ROOT_PATH . '/.env') ? 'pass' : 'fail' ?>">
                <?= file_exists(ROOT_PATH . '/.env') ? '✓ YES' : '✗ NO' ?>
            </td>
            <td><?= ROOT_PATH . '/.env' ?></td>
        </tr>
        <tr>
            <td>.env file readable</td>
            <td class="<?= is_readable(ROOT_PATH . '/.env') ? 'pass' : 'fail' ?>">
                <?= is_readable(ROOT_PATH . '/.env') ? '✓ YES' : '✗ NO' ?>
            </td>
            <td><?= is_readable(ROOT_PATH . '/.env') ? 'Can read file' : 'Permission denied' ?></td>
        </tr>
        <tr>
            <td>config/security.php exists</td>
            <td class="<?= file_exists(ROOT_PATH . '/config/security.php') ? 'pass' : 'fail' ?>">
                <?= file_exists(ROOT_PATH . '/config/security.php') ? '✓ YES' : '✗ NO' ?>
            </td>
            <td><?= ROOT_PATH . '/config/security.php' ?></td>
        </tr>
    </table>
    
    <h2>2. Environment Variables</h2>
    <table>
        <tr>
            <th>Variable</th>
            <th>Loaded</th>
            <th>Value (first 10 chars)</th>
        </tr>
        <tr>
            <td>API_KEY in $_ENV</td>
            <td class="<?= !empty($_ENV['API_KEY']) ? 'pass' : 'fail' ?>">
                <?= !empty($_ENV['API_KEY']) ? '✓ YES' : '✗ NO' ?>
            </td>
            <td><?= !empty($_ENV['API_KEY']) ? substr($_ENV['API_KEY'], 0, 10) . '...' : 'Not loaded' ?></td>
        </tr>
        <tr>
            <td>APP_NAME</td>
            <td class="<?= !empty($_ENV['APP_NAME']) ? 'pass' : 'fail' ?>">
                <?= !empty($_ENV['APP_NAME']) ? '✓ YES' : '✗ NO' ?>
            </td>
            <td><?= $_ENV['APP_NAME'] ?? 'Not loaded' ?></td>
        </tr>
        <tr>
            <td>APP_ENV</td>
            <td class="<?= !empty($_ENV['APP_ENV']) ? 'pass' : 'fail' ?>">
                <?= !empty($_ENV['APP_ENV']) ? '✓ YES' : '✗ NO' ?>
            </td>
            <td><?= $_ENV['APP_ENV'] ?? 'Not loaded' ?></td>
        </tr>
        <tr>
            <td>DB_NAME</td>
            <td class="<?= !empty($_ENV['DB_NAME']) ? 'pass' : 'fail' ?>">
                <?= !empty($_ENV['DB_NAME']) ? '✓ YES' : '✗ NO' ?>
            </td>
            <td><?= $_ENV['DB_NAME'] ?? 'Not loaded' ?></td>
        </tr>
    </table>
    
    <h2>3. Config System</h2>
    <table>
        <tr>
            <th>Config Key</th>
            <th>Loaded</th>
            <th>Value (first 10 chars)</th>
        </tr>
        <tr>
            <td>security.api_key</td>
            <td class="<?= !empty(config('security.api_key')) ? 'pass' : 'fail' ?>">
                <?= !empty(config('security.api_key')) ? '✓ YES' : '✗ NO' ?>
            </td>
            <td><?= !empty(config('security.api_key')) ? substr(config('security.api_key'), 0, 10) . '...' : 'Not loaded' ?></td>
        </tr>
        <tr>
            <td>app.name</td>
            <td class="<?= !empty(config('app.name')) ? 'pass' : 'fail' ?>">
                <?= !empty(config('app.name')) ? '✓ YES' : '✗ NO' ?>
            </td>
            <td><?= config('app.name', 'Not loaded') ?></td>
        </tr>
    </table>
    
    <h2>4. Expected vs Actual</h2>
    <table>
        <tr>
            <th>Item</th>
            <th>Expected Value</th>
            <th>Matches</th>
        </tr>
        <tr>
            <td>API_KEY (first 10 chars)</td>
            <td>IOT_OUTLET...</td>
            <td class="<?= (substr(config('security.api_key', ''), 0, 10) === 'IOT_OUTLET') ? 'pass' : 'fail' ?>">
                <?= (substr(config('security.api_key', ''), 0, 10) === 'IOT_OUTLET') ? '✓ MATCH' : '✗ MISMATCH' ?>
            </td>
        </tr>
        <tr>
            <td>Full API_KEY length</td>
            <td>48 characters</td>
            <td class="<?= (strlen(config('security.api_key', '')) === 48) ? 'pass' : 'fail' ?>">
                <?= strlen(config('security.api_key', '')) ?> characters
                <?= (strlen(config('security.api_key', '')) === 48) ? '✓' : '✗' ?>
            </td>
        </tr>
    </table>
    
    <h2>5. Diagnosis</h2>
    <?php
    $issues = [];
    $allGood = true;
    
    if (!file_exists(ROOT_PATH . '/.env')) {
        $issues[] = ".env file is missing! Upload the .env file to: " . ROOT_PATH;
        $allGood = false;
    }
    
    if (!is_readable(ROOT_PATH . '/.env')) {
        $issues[] = ".env file exists but cannot be read. Check file permissions (should be 644).";
        $allGood = false;
    }
    
    if (empty($_ENV['API_KEY'])) {
        $issues[] = "API_KEY not loaded into \$_ENV. Check if .env file contains: API_KEY=IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T";
        $allGood = false;
    }
    
    if (empty(config('security.api_key'))) {
        $issues[] = "config('security.api_key') is empty. Check config/security.php";
        $allGood = false;
    }
    
    if (strlen(config('security.api_key', '')) !== 48) {
        $issues[] = "API_KEY length is incorrect. Should be exactly 48 characters.";
        $allGood = false;
    }
    
    if ($allGood) {
        echo '<div style="background: #d4edda; padding: 15px; border: 1px solid #28a745; max-width: 800px;">';
        echo '<strong style="color: green;">✓ ALL CHECKS PASSED!</strong><br>';
        echo 'Your .env file is loaded correctly and API_KEY is configured properly.<br><br>';
        echo '<strong>Next steps:</strong><br>';
        echo '1. Test the API endpoint: <a href="/api/debug">Check /api/debug</a><br>';
        echo '2. Upload your code to Hostinger<br>';
        echo '3. Power on your ESP32 and check if it connects<br>';
        echo '4. <strong style="color: red;">DELETE THIS FILE</strong> (test-env.php) for security!';
        echo '</div>';
    } else {
        echo '<div style="background: #f8d7da; padding: 15px; border: 1px solid #dc3545; max-width: 800px;">';
        echo '<strong style="color: red;">✗ ISSUES FOUND:</strong><br><ul>';
        foreach ($issues as $issue) {
            echo '<li>' . htmlspecialchars($issue) . '</li>';
        }
        echo '</ul></div>';
    }
    ?>
    
    <h2>6. Quick Fix Commands</h2>
    <p>If you have SSH/terminal access to Hostinger, run these commands:</p>
    <pre style="background: #f5f5f5; padding: 15px; border: 1px solid #ddd; max-width: 800px;">
# Navigate to your project directory
cd ~/public_html/ac_controller_system

# Check if .env exists
ls -la .env

# If .env is missing, create it
nano .env
# Then paste your configuration and save (Ctrl+X, Y, Enter)

# Fix permissions if needed
chmod 644 .env
chmod 644 config/security.php

# Test
php public/test-env.php
    </pre>
    
    <p style="margin-top: 40px; color: #666;">
        <strong>Timestamp:</strong> <?= date('Y-m-d H:i:s') ?><br>
        <strong>Server:</strong> <?= $_SERVER['SERVER_NAME'] ?? 'Unknown' ?><br>
        <strong>PHP Version:</strong> <?= PHP_VERSION ?>
    </p>
    
</body>
</html>
