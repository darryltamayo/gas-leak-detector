<?php

/**
 * Authentication Middleware
 * 
 * Checks if user is authenticated before allowing access
 */

class AuthMiddleware
{
    /**
     * Check if user is authenticated
     * 
     * @return void
     */
    public static function check(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'] ?? '/';
            redirect(url('/auth/login'));
        }
    }
    
    /**
     * Check if user is guest (not authenticated)
     * 
     * @return void
     */
    public static function guest(): void
    {
        if (isset($_SESSION['user_id'])) {
            redirect(url('/dashboard'));
        }
    }
    
    /**
     * Check if user is admin
     * 
     * @return void
     */
    public static function admin(): void
    {
        self::check();
        
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            http_response_code(403);
            echo '<h1>403 Forbidden</h1>';
            echo '<p>You do not have permission to access this page.</p>';
            exit;
        }
    }
    
    /**
     * Verify CSRF token
     * 
     * @return void
     */
    public static function verifyCsrf(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            
            if (!csrf_verify($token)) {
                // Log debug information
                error_log('CSRF Validation Failed');
                error_log('Session CSRF Token: ' . ($_SESSION['csrf_token'] ?? 'NOT SET'));
                error_log('Submitted Token: ' . $token);
                error_log('Session ID: ' . session_id());
                
                http_response_code(403);
                echo '<h1>403 Forbidden</h1>';
                echo '<p>CSRF token validation failed.</p>';
                
                // Show debug info if in debug mode
                if (config('app.debug')) {
                    echo '<h2>Debug Information</h2>';
                    echo '<p><strong>Session Token:</strong> ' . ($_SESSION['csrf_token'] ?? 'NOT SET') . '</p>';
                    echo '<p><strong>Submitted Token:</strong> ' . ($token ?: 'EMPTY') . '</p>';
                    echo '<p><strong>Session ID:</strong> ' . session_id() . '</p>';
                    echo '<p><strong>Possible causes:</strong></p>';
                    echo '<ul>';
                    echo '<li>Browser cached an old form</li>';
                    echo '<li>Cookies are disabled</li>';
                    echo '<li>Multiple tabs/windows with different sessions</li>';
                    echo '<li>Session expired</li>';
                    echo '</ul>';
                    echo '<p><a href="' . url('/auth/login') . '">Try Again</a> | <a href="' . url('/debug-csrf.php') . '">Debug CSRF</a></p>';
                }
                exit;
            }
        }
    }
    
    /**
     * Verify CSRF token for JSON API requests
     * Returns JSON error instead of HTML
     * 
     * @return void
     */
    public static function verifyCsrfJson(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            
            if (!csrf_verify($token)) {
                // Log debug information
                error_log('CSRF Validation Failed (JSON)');
                error_log('Session CSRF Token: ' . ($_SESSION['csrf_token'] ?? 'NOT SET'));
                error_log('Submitted Token: ' . $token);
                error_log('Session ID: ' . session_id());
                
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'CSRF token validation failed. Please refresh the page and try again.'
                ]);
                exit;
            }
        }
    }
}
