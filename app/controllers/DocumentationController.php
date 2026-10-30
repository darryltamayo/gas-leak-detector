<?php

/**
 * Documentation Controller
 * 
 * Handles system documentation pages
 */

class DocumentationController
{
    /**
     * Show documentation page
     * 
     * @return void
     */
    public function index(): void
    {
        AuthMiddleware::check();
        
        $this->view('documentation/index', [
            'user' => user()
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
