<?php

/**
 * Authentication Controller
 * 
 * Handles authentication related requests
 */

class AuthController
{
    private AuthService $authService;
    
    public function __construct()
    {
        $this->authService = new AuthService();
    }
    
    /**
     * Show login page
     * 
     * @return void
     */
    public function login(): void
    {
        // Check if already logged in
        AuthMiddleware::guest();
        
        // Load view
        $this->view('auth/login');
    }
    
    /**
     * Process login
     * 
     * @return void
     */
    public function processLogin(): void
    {
        // Verify CSRF token
        AuthMiddleware::verifyCsrf();
        
        // Get form data
        $identifier = $_POST['identifier'] ?? '';
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        
        // Attempt login
        $result = $this->authService->login($identifier, $password, $remember);
        
        if ($result['success']) {
            // Redirect to intended page or dashboard
            $redirectUrl = $_SESSION['redirect_url'] ?? url('/dashboard');
            unset($_SESSION['redirect_url']);
            
            $_SESSION['success'] = $result['message'];
            redirect($redirectUrl);
        } else {
            // Store error and old input
            $_SESSION['error'] = $result['message'];
            $_SESSION['old'] = ['identifier' => $identifier];
            
            redirect(url('/auth/login'));
        }
    }
    
    /**
     * Logout
     * 
     * @return void
     */
    public function logout(): void
    {
        $this->authService->logout();
        
        $_SESSION['success'] = 'You have been logged out successfully.';
        redirect(url('/auth/login'));
    }
    
    /**
     * Show register page
     * 
     * @return void
     */
    public function register(): void
    {
        // Check if already logged in
        AuthMiddleware::guest();
        
        // Load view
        $this->view('auth/register');
    }
    
    /**
     * Process registration
     * 
     * @return void
     */
    public function processRegister(): void
    {
        // Verify CSRF token
        AuthMiddleware::verifyCsrf();
        
        // Get form data
        $data = [
            'username' => $_POST['username'] ?? '',
            'full_name' => $_POST['full_name'] ?? '',
            'password' => $_POST['password'] ?? '',
            'password_confirmation' => $_POST['password_confirmation'] ?? ''
        ];
        
        // Attempt registration
        $result = $this->authService->register($data);
        
        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
            redirect(url('/auth/login'));
        } else {
            $_SESSION['error'] = $result['message'];
            $_SESSION['errors'] = $result['errors'];
            $_SESSION['old'] = $data;
            
            redirect(url('/auth/register'));
        }
    }
    
    /**
     * Load view
     * 
     * @param string $view
     * @param array $data
     * @return void
     */
    private function view(string $view, array $data = []): void
    {
        // Extract data
        extract($data);
        
        // Load view file
        $viewFile = ROOT_PATH . '/app/views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            echo "View not found: {$view}";
        }
    }
}
