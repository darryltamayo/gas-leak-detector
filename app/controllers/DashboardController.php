<?php

/**
 * Dashboard Controller
 * 
 * Handles dashboard pages
 */

class DashboardController
{
    private DashboardService $dashboardService;
    
    public function __construct()
    {
        $this->dashboardService = new DashboardService();
    }
    
    /**
     * Show dashboard
     * 
     * @return void
     */
    public function index(): void
    {
        // Check authentication
        AuthMiddleware::check();
        
        // Get current user
        $user = user();
        
        // Get dashboard data based on role
        if ($user['role'] === 'admin') {
            $data = $this->dashboardService->getAdminDashboardData();
        } else {
            $data = $this->dashboardService->getUserDashboardData($user['id']);
        }
        
        // Load dashboard view
        $this->view('dashboard/index', [
            'user' => $user,
            'stats' => $data
        ]);
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
