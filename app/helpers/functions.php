<?php

/**
 * Global Helper Functions
 */

/**
 * Get configuration value
 * 
 * @param string $key Configuration key (e.g., 'app.name' or 'database.host')
 * @param mixed $default Default value if key not found
 * @return mixed
 */
function config(string $key, $default = null)
{
    static $loadedConfigs = [];
    
    $keys = explode('.', $key);
    $file = array_shift($keys);
    
    // Cache loaded config files
    if (!isset($loadedConfigs[$file])) {
        $configPath = ROOT_PATH . '/config/' . $file . '.php';
        
        if (!file_exists($configPath)) {
            return $default;
        }
        
        $loadedConfigs[$file] = require $configPath;
    }
    
    $config = $loadedConfigs[$file];
    
    foreach ($keys as $segment) {
        if (!is_array($config) || !array_key_exists($segment, $config)) {
            return $default;
        }
        $config = $config[$segment];
    }
    
    // Return the value, but if it's empty string return default
    return $config !== '' ? $config : $default;
}

/**
 * Redirect to URL
 * 
 * @param string $url
 * @return void
 */
function redirect(string $url): void
{
    // If URL doesn't start with http/https, prepend base URL
    if (!preg_match('/^https?:\/\//', $url)) {
        $url = url($url);
    }
    header("Location: $url");
    exit;
}

/**
 * Get base URL
 * 
 * @param string $path
 * @return string
 */
function url(string $path = ''): string
{
    $baseUrl = config('app.url');
    
    // If APP_URL is not configured or is just localhost, auto-detect
    if (!$baseUrl || $baseUrl === 'http://localhost') {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        
        // Get the script directory relative to document root
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $basePath = str_replace('\\', '/', dirname($scriptName));
        $basePath = $basePath === '/' ? '' : $basePath;
        
        $baseUrl = $protocol . '://' . $host . $basePath;
    }
    
    $baseUrl = rtrim($baseUrl, '/');
    $path = ltrim($path, '/');
    
    return $baseUrl . ($path ? '/' . $path : '');
}

/**
 * Get base path (for redirects)
 * 
 * @return string
 */
function basePath(): string
{
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $basePath = str_replace('\\', '/', dirname($scriptName));
    return $basePath === '/' ? '' : $basePath;
}

/**
 * Escape HTML output
 * 
 * @param string $string
 * @return string
 */
function e(?string $string): string
{
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF token
 * 
 * @return string
 */
function csrf_token(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * 
 * @param string $token
 * @return bool
 */
function csrf_verify(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get old input value
 * 
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function old(string $key, $default = '')
{
    return $_SESSION['old'][$key] ?? $default;
}

/**
 * Check if user is authenticated
 * 
 * @return bool
 */
function auth(): bool
{
    return isset($_SESSION['user_id']);
}

/**
 * Get authenticated user
 * 
 * @return array|null
 */
function user(): ?array
{
    return $_SESSION['user'] ?? null;
}

/**
 * Check if user has role
 * 
 * @param string $role
 * @return bool
 */
function hasRole(string $role): bool
{
    $user = user();
    return $user && $user['role'] === $role;
}

/**
 * Format date
 * 
 * @param string $date
 * @param string $format
 * @return string
 */
function formatDate(?string $date, string $format = 'Y-m-d H:i:s'): string
{
    if (!$date) {
        return 'N/A';
    }
    
    $dateTime = new DateTime($date);
    return $dateTime->format($format);
}

/**
 * Time ago format
 * 
 * @param string $datetime
 * @return string
 */
function timeAgo(?string $datetime): string
{
    if (!$datetime) {
        return 'Never';
    }
    
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return $diff . ' seconds ago';
    } elseif ($diff < 3600) {
        return floor($diff / 60) . ' minutes ago';
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . ' hours ago';
    } elseif ($diff < 604800) {
        return floor($diff / 86400) . ' days ago';
    } else {
        return date('M d, Y', $timestamp);
    }
}

/**
 * JSON response
 * 
 * @param mixed $data
 * @param int $status
 * @return void
 */
function jsonResponse($data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * dd - Dump and Die (for debugging)
 * 
 * @param mixed ...$vars
 * @return void
 */
function dd(...$vars): void
{
    echo '<pre>';
    foreach ($vars as $var) {
        var_dump($var);
    }
    echo '</pre>';
    die();
}
