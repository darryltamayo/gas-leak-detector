<?php

/**
 * Admin Controller
 * 
 * Handles admin-specific pages and operations
 */

class AdminController
{
    private UserRepository $userRepository;
    private DeviceRepository $deviceRepository;
    private ActivityLogRepository $activityLogRepository;
    private SimRepository $simRepository;
    
    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->deviceRepository = new DeviceRepository();
        $this->activityLogRepository = new ActivityLogRepository();
        $this->simRepository = new SimRepository();
    }
    
    /**
     * Activity Logs Page
     */
    public function activityLogs(): void
    {
        AuthMiddleware::admin();
        
        $page = $_GET['page'] ?? 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;
        
        $logs = $this->activityLogRepository->getRecent($limit);
        $totalLogs = $this->activityLogRepository->count();
        $totalPages = ceil($totalLogs / $limit);
        
        $this->view('admin/activity-logs', [
            'user' => user(),
            'logs' => $logs,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalLogs' => $totalLogs
        ]);
    }
    
    /**
     * Users Management Page
     */
    public function users(): void
    {
        AuthMiddleware::admin();
        
        $users = $this->userRepository->getAll();
        
        $this->view('admin/users', [
            'user' => user(),
            'users' => $users
        ]);
    }
    
    /**
     * Create User Page
     */
    public function createUser(): void
    {
        AuthMiddleware::admin();
        
        $this->view('admin/create-user', [
            'user' => user()
        ]);
    }
    
    /**
     * Process Create User
     */
    public function processCreateUser(): void
    {
        AuthMiddleware::admin();
        AuthMiddleware::verifyCsrf();
        
        $username = $_POST['username'] ?? '';
        $firstName = $_POST['first_name'] ?? '';
        $lastName = $_POST['last_name'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'user';
        
        // SIM card registration
        $registerSim = isset($_POST['register_sim']) && $_POST['register_sim'] == '1';
        $simPhoneNumber = $_POST['sim_phone_number'] ?? '';
        $simDescription = $_POST['sim_description'] ?? '';
        
        // Validation
        if (empty($username) || empty($firstName) || empty($lastName) || empty($password)) {
            $_SESSION['error'] = 'All fields are required';
            redirect(url('/admin/create-user'));
            return;
        }
        
        // Check if username exists
        if ($this->userRepository->findByUsername($username)) {
            $_SESSION['error'] = 'Username already exists';
            redirect(url('/admin/create-user'));
            return;
        }
        
        // Validate SIM card if registering
        if ($registerSim) {
            if (empty($simPhoneNumber)) {
                $_SESSION['error'] = 'Phone number is required when registering SIM card';
                redirect(url('/admin/create-user'));
                return;
            }
            
            // Check if phone number already exists
            $simRepo = new SimRepository();
            if ($simRepo->findByPhoneNumber($simPhoneNumber)) {
                $_SESSION['error'] = 'This phone number is already registered';
                redirect(url('/admin/create-user'));
                return;
            }
        }
        
        // Create user
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        require_once ROOT_PATH . '/app/models/User.php';
        
        $newUser = new User();
        $newUser->username = $username;
        $newUser->password = $hashedPassword;
        $newUser->first_name = $firstName;
        $newUser->last_name = $lastName;
        $newUser->full_name = $firstName . ' ' . $lastName; // Combine for compatibility
        $newUser->role = $role;
        $newUser->is_active = true;
        
        if ($this->userRepository->create($newUser)) {
            // Log user creation to activity logs
            try {
                $db = $this->userRepository->getDb();
                $stmt = $db->prepare(
                    'INSERT INTO iot_outlet_activity_logs (user_id, action_type, action_details, ip_address, status) 
                     VALUES (?, ?, ?, ?, ?)'
                );
                $stmt->execute([
                    user()['id'],
                    'user_created',
                    "Created user: {$username} ({$firstName} {$lastName}) - Role: {$role}",
                    $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                    'success'
                ]);
            } catch (Exception $e) {
                error_log('Failed to log user creation: ' . $e->getMessage());
            }
            
            // If SIM registration is enabled, create SIM card
            if ($registerSim) {
                require_once ROOT_PATH . '/app/models/AuthorizedSim.php';
                
                $sim = new AuthorizedSim();
                $sim->phone_number = $simPhoneNumber;
                $sim->first_name = $firstName;
                $sim->last_name = $lastName;
                $sim->owner_name = $firstName . ' ' . $lastName; // Combine for compatibility
                $sim->description = $simDescription;
                $sim->is_active = true;
                $sim->created_by = user()['id'];
                
                $simRepo = new SimRepository();
                if (!$simRepo->create($sim)) {
                    $_SESSION['warning'] = 'User created successfully, but failed to register SIM card';
                    redirect(url('/admin/users'));
                    return;
                } else {
                    // Log SIM registration
                    try {
                        $stmt = $db->prepare(
                            'INSERT INTO iot_outlet_activity_logs (user_id, action_type, action_details, ip_address, status) 
                             VALUES (?, ?, ?, ?, ?)'
                        );
                        $stmt->execute([
                            user()['id'],
                            'sim_registered',
                            "Registered SIM: {$simPhoneNumber} for {$firstName} {$lastName}",
                            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                            'success'
                        ]);
                    } catch (Exception $e) {
                        error_log('Failed to log SIM registration: ' . $e->getMessage());
                    }
                }
            }
            
            $message = 'User created successfully';
            if ($registerSim) {
                $message .= ' and SIM card registered';
            }
            $_SESSION['success'] = $message;
            redirect(url('/admin/users'));
        } else {
            $_SESSION['error'] = 'Failed to create user';
            redirect(url('/admin/create-user'));
        }
    }
    
    /**
     * Edit User Page
     */
    public function editUser(): void
    {
        AuthMiddleware::admin();
        
        $id = $_GET['id'] ?? 0;
        $userToEdit = $this->userRepository->findById($id);
        
        if (!$userToEdit) {
            $_SESSION['error'] = 'User not found';
            redirect(url('/admin/users'));
            return;
        }
        
        $this->view('admin/edit-user', [
            'user' => user(),
            'userToEdit' => $userToEdit
        ]);
    }
    
    /**
     * Authorized SIMs Page
     */
    public function sims(): void
    {
        AuthMiddleware::admin();
        
        $sims = $this->simRepository->getAll();
        
        $this->view('admin/sims', [
            'user' => user(),
            'sims' => $sims
        ]);
    }
    
    /**
     * Create SIM Page
     */
    public function createSim(): void
    {
        AuthMiddleware::admin();
        
        $this->view('admin/create-sim', [
            'user' => user()
        ]);
    }
    
    /**
     * Process Create SIM
     */
    public function processCreateSim(): void
    {
        AuthMiddleware::admin();
        AuthMiddleware::verifyCsrf();
        
        $phoneNumber = $_POST['phone_number'] ?? '';
        $firstName = $_POST['first_name'] ?? '';
        $lastName = $_POST['last_name'] ?? '';
        $description = $_POST['description'] ?? '';
        $isActive = $_POST['is_active'] ?? '1';
        
        // Validation
        if (empty($phoneNumber) || empty($firstName) || empty($lastName)) {
            $_SESSION['error'] = 'Phone number, first name, and last name are required';
            redirect(url('/admin/create-sim'));
            return;
        }
        
        // Check if phone number exists
        if ($this->simRepository->findByPhoneNumber($phoneNumber)) {
            $_SESSION['error'] = 'This phone number is already registered';
            redirect(url('/admin/create-sim'));
            return;
        }
        
        // Create SIM
        require_once ROOT_PATH . '/app/models/AuthorizedSim.php';
        
        $sim = new AuthorizedSim();
        $sim->phone_number = $phoneNumber;
        $sim->first_name = $firstName;
        $sim->last_name = $lastName;
        $sim->owner_name = $firstName . ' ' . $lastName; // Combine for compatibility
        $sim->description = $description;
        $sim->is_active = (bool)$isActive;
        $sim->created_by = user()['id'];
        
        if ($this->simRepository->create($sim)) {
            $_SESSION['success'] = 'SIM card added successfully';
            redirect(url('/admin/sims'));
        } else {
            $_SESSION['error'] = 'Failed to add SIM card';
            redirect(url('/admin/create-sim'));
        }
    }
    
    /**
     * Edit SIM Page
     */
    public function editSim(): void
    {
        AuthMiddleware::admin();
        
        $id = $_GET['id'] ?? 0;
        $simToEdit = $this->simRepository->findById($id);
        
        if (!$simToEdit) {
            $_SESSION['error'] = 'SIM card not found';
            redirect(url('/admin/sims'));
            return;
        }
        
        $this->view('admin/edit-sim', [
            'user' => user(),
            'simToEdit' => $simToEdit
        ]);
    }
    
    /**
     * Process Edit SIM
     */
    public function processEditSim(): void
    {
        AuthMiddleware::admin();
        AuthMiddleware::verifyCsrf();
        
        $id = $_POST['id'] ?? 0;
        $phoneNumber = $_POST['phone_number'] ?? '';
        $firstName = $_POST['first_name'] ?? '';
        $lastName = $_POST['last_name'] ?? '';
        $description = $_POST['description'] ?? '';
        $isActive = $_POST['is_active'] ?? '1';
        
        // Get existing SIM
        $sim = $this->simRepository->findById($id);
        
        if (!$sim) {
            $_SESSION['error'] = 'SIM card not found';
            redirect(url('/admin/sims'));
            return;
        }
        
        // Validation
        if (empty($phoneNumber) || empty($firstName) || empty($lastName)) {
            $_SESSION['error'] = 'Phone number, first name, and last name are required';
            redirect(url('/admin/edit-sim?id=' . $id));
            return;
        }
        
        // Check if phone number is taken by another SIM
        $existingSim = $this->simRepository->findByPhoneNumber($phoneNumber);
        if ($existingSim && $existingSim->id !== $id) {
            $_SESSION['error'] = 'This phone number is already registered to another SIM';
            redirect(url('/admin/edit-sim?id=' . $id));
            return;
        }
        
        // Update SIM
        $sim->phone_number = $phoneNumber;
        $sim->first_name = $firstName;
        $sim->last_name = $lastName;
        $sim->owner_name = $firstName . ' ' . $lastName; // Update for compatibility
        $sim->description = $description;
        $sim->is_active = (bool)$isActive;
        
        if ($this->simRepository->update($sim)) {
            $_SESSION['success'] = 'SIM card updated successfully';
            redirect(url('/admin/sims'));
        } else {
            $_SESSION['error'] = 'Failed to update SIM card';
            redirect(url('/admin/edit-sim?id=' . $id));
        }
    }
    
    /**
     * Toggle SIM Status (AJAX)
     */
    public function toggleSimStatus(): void
    {
        AuthMiddleware::admin();
        AuthMiddleware::verifyCsrf();
        
        header('Content-Type: application/json');
        
        $simId = $_POST['sim_id'] ?? 0;
        $isActive = $_POST['is_active'] ?? 1;
        
        $sim = $this->simRepository->findById($simId);
        
        if (!$sim) {
            echo json_encode(['success' => false, 'message' => 'SIM card not found']);
            return;
        }
        
        $sim->is_active = (bool)$isActive;
        
        if ($this->simRepository->update($sim)) {
            echo json_encode(['success' => true, 'message' => 'SIM status updated']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update SIM status']);
        }
    }
    
    /**
     * Delete SIM (AJAX)
     */
    public function deleteSim(): void
    {
        AuthMiddleware::admin();
        AuthMiddleware::verifyCsrf();
        
        header('Content-Type: application/json');
        
        $simId = $_POST['sim_id'] ?? 0;
        
        $sim = $this->simRepository->findById($simId);
        
        if (!$sim) {
            echo json_encode(['success' => false, 'message' => 'SIM card not found']);
            return;
        }
        
        if ($this->simRepository->delete($simId)) {
            echo json_encode(['success' => true, 'message' => 'SIM card deleted']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete SIM card']);
        }
    }
    
    /**
     * Process Edit User
     */
    public function processEditUser(): void
    {
        AuthMiddleware::admin();
        AuthMiddleware::verifyCsrf();
        
        $id = $_POST['id'] ?? 0;
        $firstName = $_POST['first_name'] ?? '';
        $lastName = $_POST['last_name'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'user';
        $isActive = $_POST['is_active'] ?? '1';
        
        // Get existing user
        $userToEdit = $this->userRepository->findById($id);
        
        if (!$userToEdit) {
            $_SESSION['error'] = 'User not found';
            redirect(url('/admin/users'));
            return;
        }
        
        // Validation
        if (empty($firstName) || empty($lastName)) {
            $_SESSION['error'] = 'First name and last name are required';
            redirect(url('/admin/edit-user?id=' . $id));
            return;
        }
        
        // Update user fields
        $userToEdit->first_name = $firstName;
        $userToEdit->last_name = $lastName;
        $userToEdit->full_name = $firstName . ' ' . $lastName; // Update full_name for compatibility
        $userToEdit->role = $role;
        $userToEdit->is_active = (bool)$isActive;
        
        // Update password if provided
        if (!empty($password)) {
            $this->userRepository->updatePassword($id, password_hash($password, PASSWORD_BCRYPT));
        }
        
        // Update user
        if ($this->userRepository->update($userToEdit)) {
            // Log user update to activity logs
            try {
                $db = $this->userRepository->getDb();
                $stmt = $db->prepare(
                    'INSERT INTO iot_outlet_activity_logs (user_id, action_type, action_details, ip_address, status) 
                     VALUES (?, ?, ?, ?, ?)'
                );
                $details = "Updated user: {$userToEdit->username} ({$firstName} {$lastName}) - Role: {$role}";
                if (!empty($password)) {
                    $details .= " (password changed)";
                }
                $stmt->execute([
                    user()['id'],
                    'user_updated',
                    $details,
                    $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                    'success'
                ]);
            } catch (Exception $e) {
                error_log('Failed to log user update: ' . $e->getMessage());
            }
            
            $_SESSION['success'] = 'User updated successfully';
            redirect(url('/admin/users'));
        } else {
            $_SESSION['error'] = 'Failed to update user';
            redirect(url('/admin/edit-user?id=' . $id));
        }
    }
    
    /**
     * Toggle User Status (AJAX)
     */
    public function toggleUserStatus(): void
    {
        AuthMiddleware::admin();
        AuthMiddleware::verifyCsrf();
        
        header('Content-Type: application/json');
        
        $userId = $_POST['user_id'] ?? 0;
        $status = $_POST['status'] ?? 1;
        $currentUser = user();
        
        // Prevent user from deactivating themselves
        if ($userId == $currentUser['id']) {
            echo json_encode(['success' => false, 'message' => 'You cannot deactivate your own account']);
            return;
        }
        
        $userToToggle = $this->userRepository->findById($userId);
        
        if (!$userToToggle) {
            echo json_encode(['success' => false, 'message' => 'User not found']);
            return;
        }
        
        $userToToggle->is_active = (bool)$status;
        
        if ($this->userRepository->update($userToToggle)) {
            $statusText = $status ? 'activated' : 'deactivated';
            
            // Log the activity
            try {
                $stmt = $this->userRepository->getDb()->prepare(
                    'INSERT INTO iot_outlet_activity_logs (user_id, action_type, action_details, status) 
                     VALUES (?, ?, ?, ?)'
                );
                $stmt->execute([
                    $currentUser['id'],
                    'user_updated',
                    "User '{$userToToggle->full_name}' {$statusText}",
                    'success'
                ]);
            } catch (Exception $e) {
                // Log error but don't fail the operation
                error_log("Failed to log activity: " . $e->getMessage());
            }
            
            echo json_encode([
                'success' => true, 
                'message' => "User {$statusText} successfully"
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update user status']);
        }
    }
    
    /**
     * System Settings Page
     */
    public function settings(): void
    {
        AuthMiddleware::admin();
        
        $devices = $this->deviceRepository->getAll();
        $device = !empty($devices) ? $devices[0] : null;
        
        $this->view('admin/settings', [
            'user' => user(),
            'device' => $device
        ]);
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
        } else {
            echo "View not found: {$view}";
        }
    }
}
