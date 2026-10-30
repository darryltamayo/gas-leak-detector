<?php

/**
 * Profile Controller
 * 
 * Handles user profile management and password changes
 */

class ProfileController
{
    private UserRepository $userRepository;
    
    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }
    
    /**
     * Show profile page
     */
    public function index(): void
    {
        AuthMiddleware::check();
        
        $user = user();
        
        $this->view('profile/index', [
            'user' => $user
        ]);
    }
    
    /**
     * Update profile information
     */
    public function update(): void
    {
        AuthMiddleware::check();
        AuthMiddleware::verifyCsrf();
        
        $userId = user()['id'];
        $userRole = user()['role'];
        
        // Only admins can update profile information
        if ($userRole !== 'admin') {
            $_SESSION['error'] = 'Only administrators can update profile information';
            redirect(url('/profile'));
            return;
        }
        
        $fullName = $_POST['full_name'] ?? '';
        
        // Validation
        if (empty($fullName)) {
            $_SESSION['error'] = 'Full name is required';
            redirect(url('/profile'));
            return;
        }
        
        // Get current user
        $userModel = $this->userRepository->findById($userId);
        if (!$userModel) {
            $_SESSION['error'] = 'User not found';
            redirect(url('/profile'));
            return;
        }
        
        // Update user information
        $userModel->full_name = $fullName;
        
        if ($this->userRepository->update($userModel)) {
            // Update session data
            $_SESSION['user']['full_name'] = $fullName;
            
            $this->logActivity('profile_updated', 'Updated profile information');
            $_SESSION['success'] = 'Profile updated successfully';
        } else {
            $_SESSION['error'] = 'Failed to update profile';
        }
        
        redirect(url('/profile'));
    }
    
    /**
     * Change password
     */
    public function changePassword(): void
    {
        AuthMiddleware::check();
        AuthMiddleware::verifyCsrf();
        
        $userId = user()['id'];
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validation
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $_SESSION['error'] = 'All password fields are required';
            redirect(url('/profile'));
            return;
        }
        
        if (strlen($newPassword) < 6) {
            $_SESSION['error'] = 'New password must be at least 6 characters long';
            redirect(url('/profile'));
            return;
        }
        
        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = 'New passwords do not match';
            redirect(url('/profile'));
            return;
        }
        
        // Get current user
        $userModel = $this->userRepository->findById($userId);
        if (!$userModel) {
            $_SESSION['error'] = 'User not found';
            redirect(url('/profile'));
            return;
        }
        
        // Verify current password
        if (!password_verify($currentPassword, $userModel->password)) {
            $_SESSION['error'] = 'Current password is incorrect';
            redirect(url('/profile'));
            return;
        }
        
        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        if ($this->userRepository->updatePassword($userId, $hashedPassword)) {
            $this->logActivity('password_changed', 'Changed account password');
            $_SESSION['success'] = 'Password changed successfully';
        } else {
            $_SESSION['error'] = 'Failed to change password';
        }
        
        redirect(url('/profile'));
    }
    
    /**
     * Log activity
     */
    private function logActivity(string $type, string $details): void
    {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare(
                'INSERT INTO iot_outlet_activity_logs (user_id, action_type, action_details, ip_address, status) 
                 VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                user()['id'], 
                $type, 
                $details, 
                $_SERVER['REMOTE_ADDR'] ?? 'unknown', 
                'success'
            ]);
        } catch (Exception $e) {
            error_log('Log failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Load view
     */
    private function view(string $view, array $data = []): void
    {
        extract($data);
        $viewFile = ROOT_PATH . '/app/views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        }
    }
}
