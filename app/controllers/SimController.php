<?php

/**
 * SIM Controller
 * 
 * Handles SIM number management (Admin only)
 */

class SimController
{
    private SimRepository $simRepository;
    
    public function __construct()
    {
        $this->simRepository = new SimRepository();
    }
    
    /**
     * Show SIM management page
     * 
     * @return void
     */
    public function index(): void
    {
        AuthMiddleware::admin();
        
        $sims = $this->simRepository->getAll();
        
        $this->view('admin/sims', [
            'sims' => $sims,
            'user' => user()
        ]);
    }
    
    /**
     * Show create SIM page
     * 
     * @return void
     */
    public function create(): void
    {
        AuthMiddleware::admin();
        
        $this->view('admin/create-sim', ['user' => user()]);
    }
    
    /**
     * Process create SIM
     * 
     * @return void
     */
    public function store(): void
    {
        AuthMiddleware::admin();
        AuthMiddleware::verifyCsrf();
        
        $data = [
            'phone_number' => $_POST['phone_number'] ?? '',
            'owner_name' => $_POST['owner_name'] ?? '',
            'description' => $_POST['description'] ?? '',
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];
        
        // Validate
        $errors = $this->validateSim($data);
        
        if (!empty($errors)) {
            $_SESSION['error'] = 'Validation failed.';
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            redirect(url('/admin/sims/create'));
            return;
        }
        
        // Create SIM record
        $sim = new AuthorizedSim();
        $sim->phone_number = $this->formatPhoneNumber($data['phone_number']);
        $sim->owner_name = $data['owner_name'];
        $sim->description = $data['description'];
        $sim->is_active = $data['is_active'];
        $sim->created_by = user()['id'];
        
        if ($this->simRepository->create($sim)) {
            // Log activity
            $this->logActivity('sim_registered', "Registered SIM: {$sim->phone_number} ({$sim->owner_name})");
            
            $_SESSION['success'] = 'SIM number registered successfully.';
            redirect(url('/admin/sims'));
        } else {
            $_SESSION['error'] = 'Failed to register SIM number.';
            redirect(url('/admin/sims/create'));
        }
    }
    
    /**
     * Show edit SIM page
     * 
     * @param int $id
     * @return void
     */
    public function edit(int $id): void
    {
        AuthMiddleware::admin();
        
        $sim = $this->simRepository->findById($id);
        
        if (!$sim) {
            $_SESSION['error'] = 'SIM number not found.';
            redirect(url('/admin/sims'));
            return;
        }
        
        $this->view('admin/edit-sim', [
            'sim' => $sim,
            'user' => user()
        ]);
    }
    
    /**
     * Process update SIM
     * 
     * @param int $id
     * @return void
     */
    public function update(int $id): void
    {
        AuthMiddleware::admin();
        AuthMiddleware::verifyCsrf();
        
        $sim = $this->simRepository->findById($id);
        
        if (!$sim) {
            $_SESSION['error'] = 'SIM number not found.';
            redirect(url('/admin/sims'));
            return;
        }
        
        $data = [
            'phone_number' => $_POST['phone_number'] ?? '',
            'owner_name' => $_POST['owner_name'] ?? '',
            'description' => $_POST['description'] ?? '',
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];
        
        // Validate
        $errors = $this->validateSim($data, $id);
        
        if (!empty($errors)) {
            $_SESSION['error'] = 'Validation failed.';
            $_SESSION['errors'] = $errors;
            redirect(url('/admin/sims/edit/' . $id));
            return;
        }
        
        // Update SIM object
        $sim->phone_number = $this->formatPhoneNumber($data['phone_number']);
        $sim->owner_name = $data['owner_name'];
        $sim->description = $data['description'];
        $sim->is_active = $data['is_active'];
        
        if ($this->simRepository->update($sim)) {
            // Log activity
            $this->logActivity('sim_updated', "Updated SIM: {$sim->phone_number} ({$sim->owner_name})");
            
            $_SESSION['success'] = 'SIM number updated successfully.';
        } else {
            $_SESSION['error'] = 'Failed to update SIM number.';
        }
        
        redirect(url('/admin/sims'));
    }
    
    /**
     * Delete SIM
     * 
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        AuthMiddleware::admin();
        AuthMiddleware::verifyCsrf();
        
        $sim = $this->simRepository->findById($id);
        
        if (!$sim) {
            $_SESSION['error'] = 'SIM number not found.';
            redirect(url('/admin/sims'));
            return;
        }
        
        if ($this->simRepository->delete($id)) {
            // Log activity
            $this->logActivity('sim_deleted', "Deleted SIM: {$sim->phone_number} ({$sim->owner_name})");
            
            $_SESSION['success'] = 'SIM number deleted successfully.';
        } else {
            $_SESSION['error'] = 'Failed to delete SIM number.';
        }
        
        redirect(url('/admin/sims'));
    }
    
    /**
     * Toggle SIM status
     * 
     * @param int $id
     * @return void
     */
    public function toggleStatus(int $id): void
    {
        AuthMiddleware::admin();
        AuthMiddleware::verifyCsrf();
        
        $sim = $this->simRepository->findById($id);
        
        if (!$sim) {
            jsonResponse(['success' => false, 'message' => 'SIM not found'], 404);
            return;
        }
        
        $sim->is_active = !$sim->is_active;
        
        if ($this->simRepository->update($sim)) {
            $status = $sim->is_active ? 'enabled' : 'disabled';
            $this->logActivity('sim_updated', "SIM {$status}: {$sim->phone_number}");
            
            jsonResponse([
                'success' => true,
                'message' => "SIM number {$status} successfully.",
                'is_active' => $sim->is_active
            ]);
        } else {
            jsonResponse(['success' => false, 'message' => 'Failed to update status'], 500);
        }
    }
    
    /**
     * Validate SIM data
     * 
     * @param array $data
     * @param int|null $simId
     * @return array
     */
    private function validateSim(array $data, ?int $simId = null): array
    {
        $errors = [];
        
        // Phone number
        if (empty($data['phone_number'])) {
            $errors['phone_number'] = 'Phone number is required.';
        } else {
            // Format and validate
            $formatted = $this->formatPhoneNumber($data['phone_number']);
            
            if (!$this->isValidPhoneNumber($formatted)) {
                $errors['phone_number'] = 'Invalid phone number format. Use +639XXXXXXXXX or 09XXXXXXXXX';
            } else {
                // Check uniqueness
                $existing = $this->simRepository->findByPhoneNumber($formatted);
                if ($existing && (!$simId || $existing->id != $simId)) {
                    $errors['phone_number'] = 'This phone number is already registered.';
                }
            }
        }
        
        // Owner name
        if (empty($data['owner_name'])) {
            $errors['owner_name'] = 'Owner name is required.';
        }
        
        return $errors;
    }
    
    /**
     * Format phone number to standard format
     * 
     * @param string $phone
     * @return string
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Remove spaces, dashes, and parentheses
        $phone = preg_replace('/[\s\-\(\)]/', '', $phone);
        
        // Convert 09XX to +639XX
        if (preg_match('/^09\d{9}$/', $phone)) {
            $phone = '+63' . substr($phone, 1);
        }
        
        // Ensure +63 prefix
        if (preg_match('/^9\d{9}$/', $phone)) {
            $phone = '+63' . $phone;
        }
        
        return $phone;
    }
    
    /**
     * Validate phone number format
     * 
     * @param string $phone
     * @return bool
     */
    private function isValidPhoneNumber(string $phone): bool
    {
        // Philippine mobile number format: +639XXXXXXXXX
        return preg_match('/^\+639\d{9}$/', $phone) === 1;
    }
    
    /**
     * Log activity
     * 
     * @param string $actionType
     * @param string $details
     * @return void
     */
    private function logActivity(string $actionType, string $details): void
    {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare(
                'INSERT INTO iot_outlet_activity_logs (user_id, action_type, action_details, ip_address, user_agent, status) 
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            
            $stmt->execute([
                user()['id'],
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
    
    /**
     * Load view
     * 
     * @param string $view
     * @param array $data
     * @return void
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
