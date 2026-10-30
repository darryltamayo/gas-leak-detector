<?php

/**
 * Authentication Service
 * 
 * Handles all authentication logic
 */

class AuthService
{
    private UserRepository $userRepository;
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOCKOUT_TIME = 900; // 15 minutes in seconds
    
    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }
    
    /**
     * Attempt to login user
     * 
     * @param string $identifier Username or email
     * @param string $password
     * @param bool $remember
     * @return array ['success' => bool, 'message' => string, 'user' => User|null]
     */
    public function login(string $identifier, string $password, bool $remember = false): array
    {
        // Check if user is locked out
        if ($this->isLockedOut($identifier)) {
            return [
                'success' => false,
                'message' => 'Too many failed login attempts. Please try again later.',
                'user' => null
            ];
        }
        
        // Find user
        $user = $this->userRepository->findByUsernameOrEmail($identifier);
        
        if (!$user) {
            $this->recordFailedAttempt($identifier);
            return [
                'success' => false,
                'message' => 'Invalid credentials.',
                'user' => null
            ];
        }
        
        // Verify password
        if (!password_verify($password, $user->password)) {
            $this->recordFailedAttempt($identifier);
            return [
                'success' => false,
                'message' => 'Invalid credentials.',
                'user' => null
            ];
        }
        
        // Check if user is active
        if (!$user->isActive()) {
            return [
                'success' => false,
                'message' => 'Your account has been disabled. Please contact administrator.',
                'user' => null
            ];
        }
        
        // Clear failed attempts
        $this->clearFailedAttempts($identifier);
        
        // Update last login
        $this->userRepository->updateLastLogin($user->id);
        
        // Create session
        $this->createSession($user, $remember);
        
        // Log activity
        $this->logActivity($user->id, 'login', 'User logged in successfully');
        
        return [
            'success' => true,
            'message' => 'Login successful.',
            'user' => $user
        ];
    }
    
    /**
     * Logout user
     * 
     * @return void
     */
    public function logout(): void
    {
        if (isset($_SESSION['user_id'])) {
            $this->logActivity($_SESSION['user_id'], 'logout', 'User logged out');
        }
        
        // Destroy session
        session_unset();
        session_destroy();
        
        // Start new session
        session_start();
        session_regenerate_id(true);
    }
    
    /**
     * Check if user is authenticated
     * 
     * @return bool
     */
    public function check(): bool
    {
        return isset($_SESSION['user_id']) && isset($_SESSION['user']);
    }
    
    /**
     * Get authenticated user
     * 
     * @return User|null
     */
    public function user(): ?User
    {
        if (!$this->check()) {
            return null;
        }
        
        return User::fromArray($_SESSION['user']);
    }
    
    /**
     * Create user session
     * 
     * @param User $user
     * @param bool $remember
     * @return void
     */
    private function createSession(User $user, bool $remember = false): void
    {
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);
        
        // Store user data in session
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user'] = $user->toArray(true);
        $_SESSION['login_time'] = time();
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        // Set remember me cookie
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            setcookie(
                'remember_token',
                $token,
                time() + (86400 * 30), // 30 days
                '/',
                '',
                false, // Set to true if using HTTPS
                true // HTTP only
            );
            $_SESSION['remember_token'] = $token;
        }
    }
    
    /**
     * Check if identifier is locked out
     * 
     * @param string $identifier
     * @return bool
     */
    private function isLockedOut(string $identifier): bool
    {
        $key = 'login_attempts_' . md5($identifier);
        
        if (!isset($_SESSION[$key])) {
            return false;
        }
        
        $attempts = $_SESSION[$key];
        
        if ($attempts['count'] >= self::MAX_LOGIN_ATTEMPTS) {
            $timeSinceLastAttempt = time() - $attempts['last_attempt'];
            
            if ($timeSinceLastAttempt < self::LOCKOUT_TIME) {
                return true;
            }
            
            // Lockout time expired, clear attempts
            unset($_SESSION[$key]);
        }
        
        return false;
    }
    
    /**
     * Record failed login attempt
     * 
     * @param string $identifier
     * @return void
     */
    private function recordFailedAttempt(string $identifier): void
    {
        $key = 'login_attempts_' . md5($identifier);
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [
                'count' => 0,
                'last_attempt' => time()
            ];
        }
        
        $_SESSION[$key]['count']++;
        $_SESSION[$key]['last_attempt'] = time();
    }
    
    /**
     * Clear failed login attempts
     * 
     * @param string $identifier
     * @return void
     */
    private function clearFailedAttempts(string $identifier): void
    {
        $key = 'login_attempts_' . md5($identifier);
        unset($_SESSION[$key]);
    }
    
    /**
     * Register new user
     * 
     * @param array $data
     * @return array ['success' => bool, 'message' => string, 'errors' => array]
     */
    public function register(array $data): array
    {
        $errors = $this->validateRegistration($data);
        
        if (!empty($errors)) {
            return [
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $errors
            ];
        }
        
        // Create user
        $user = new User();
        $user->username = $data['username'];
        $user->password = password_hash($data['password'], PASSWORD_BCRYPT);
        $user->full_name = $data['full_name'];
        $user->role = 'user';
        $user->is_active = true;
        
        if ($this->userRepository->create($user)) {
            return [
                'success' => true,
                'message' => 'Registration successful. You can now login.',
                'errors' => []
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Failed to create user account.',
            'errors' => []
        ];
    }
    
    /**
     * Validate registration data
     * 
     * @param array $data
     * @return array
     */
    private function validateRegistration(array $data): array
    {
        $errors = [];
        
        // Username validation
        if (empty($data['username'])) {
            $errors['username'] = 'Username is required.';
        } elseif (strlen($data['username']) < 3) {
            $errors['username'] = 'Username must be at least 3 characters.';
        } elseif ($this->userRepository->findByUsername($data['username'])) {
            $errors['username'] = 'Username already exists.';
        }
        
        // Full name validation
        if (empty($data['full_name'])) {
            $errors['full_name'] = 'Full name is required.';
        }
        
        // Password validation
        if (empty($data['password'])) {
            $errors['password'] = 'Password is required.';
        } elseif (strlen($data['password']) < 8) {
            $errors['password'] = 'Password must be at least 8 characters.';
        }
        
        // Confirm password
        if (empty($data['password_confirmation'])) {
            $errors['password_confirmation'] = 'Please confirm your password.';
        } elseif ($data['password'] !== $data['password_confirmation']) {
            $errors['password_confirmation'] = 'Passwords do not match.';
        }
        
        return $errors;
    }
    
    /**
     * Log activity
     * 
     * @param int $userId
     * @param string $actionType
     * @param string $details
     * @return void
     */
    private function logActivity(int $userId, string $actionType, string $details): void
    {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare(
                'INSERT INTO iot_outlet_activity_logs (user_id, action_type, action_details, ip_address, user_agent, status) 
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            
            $stmt->execute([
                $userId,
                $actionType,
                $details,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                'success'
            ]);
        } catch (Exception $e) {
            error_log('Failed to log activity: ' . $e->getMessage());
        }
    }
}
