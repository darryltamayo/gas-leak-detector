<?php

/**
 * Router Class
 * 
 * Handles URL routing and request dispatching
 */

class Router
{
    private array $routes = [];
    private array $middleware = [];
    
    /**
     * Dispatch request
     * 
     * @return void
     */
    public function dispatch(): void
    {
        // Load helper functions
        require_once ROOT_PATH . '/app/helpers/functions.php';
        
        // Parse URL
        $url = $_GET['url'] ?? '';
        $url = rtrim($url, '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $url = $url ?: 'home';
        
        // Split URL into parts
        $urlParts = explode('/', $url);
        
        // Check if this is an API request
        if ($urlParts[0] === 'api') {
            $this->handleApiRequest($urlParts);
            return;
        }
        
        // Get HTTP method
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Determine controller and action
        $controllerName = ucfirst($urlParts[0]) . 'Controller';
        $action = $urlParts[1] ?? 'index';
        $params = array_slice($urlParts, 2);
        
        // For POST requests to auth/login, route to processLogin
        if ($method === 'POST' && $controllerName === 'AuthController' && $action === 'login') {
            $action = 'processLogin';
        }
        
        // For POST requests to auth/register, route to processRegister
        if ($method === 'POST' && $controllerName === 'AuthController' && $action === 'register') {
            $action = 'processRegister';
        }
        
        // For POST requests to outlet, route to appropriate action
        if ($method === 'POST' && $controllerName === 'OutletController') {
            if ($action === 'turn-on') {
                $action = 'turnOn';
            } elseif ($action === 'turn-off') {
                $action = 'turnOff';
            } elseif ($action === 'set-mode') {
                $action = 'setMode';
            } elseif ($action === 'bulk-control') {
                $action = 'bulkControl';
            } elseif ($action === 'bulk-set-mode') {
                $action = 'bulkSetMode';
            } elseif ($action === 'control-device') {
                $action = 'controlDevice';
            }
        }
        
        // For GET requests to outlet/status and outlet/check-active-schedule
        if ($method === 'GET' && $controllerName === 'OutletController') {
            if ($action === 'status') {
                $action = 'getStatus';
            } elseif ($action === 'check-active-schedule') {
                $action = 'checkActiveSchedule';
            }
        }
        
        // Schedule routes
        if ($controllerName === 'ScheduleController') {
            // POST routes
            if ($method === 'POST') {
                if ($action === 'store') {
                    $action = 'store';
                } elseif ($action === 'toggle' && !empty($params[0])) {
                    $action = 'toggleStatus';
                    $params = [$params[0]];
                } elseif ($action === 'delete' && !empty($params[0])) {
                    $action = 'delete';
                    $params = [$params[0]];
                } elseif ($action === 'update' && !empty($params[0])) {
                    $action = 'update';
                    $params = [$params[0]];
                }
            }
        }
        
        // Admin routes
        if ($controllerName === 'AdminController') {
            // POST routes
            if ($method === 'POST' && $action === 'process-create-user') {
                $action = 'processCreateUser';
            } elseif ($method === 'POST' && $action === 'process-edit-user') {
                $action = 'processEditUser';
            } elseif ($method === 'POST' && $action === 'toggle-user-status') {
                $action = 'toggleUserStatus';
            } elseif ($method === 'POST' && $action === 'process-create-sim') {
                $action = 'processCreateSim';
            } elseif ($method === 'POST' && $action === 'process-edit-sim') {
                $action = 'processEditSim';
            } elseif ($method === 'POST' && $action === 'toggle-sim-status') {
                $action = 'toggleSimStatus';
            } elseif ($method === 'POST' && $action === 'delete-sim') {
                $action = 'deleteSim';
            }
            
            // GET routes
            if ($method === 'GET') {
                if ($action === 'activity-logs') {
                    $action = 'activityLogs';
                } elseif ($action === 'create-user') {
                    $action = 'createUser';
                } elseif ($action === 'edit-user') {
                    $action = 'editUser';
                } elseif ($action === 'create-sim') {
                    $action = 'createSim';
                } elseif ($action === 'edit-sim') {
                    $action = 'editSim';
                }
            }
        }
        
        // Device routes
        if ($controllerName === 'DeviceController') {
            // POST routes
            if ($method === 'POST') {
                if ($action === 'process-create') {
                    $action = 'processCreate';
                } elseif ($action === 'process-edit' && !empty($params[0])) {
                    // Validate that the parameter is numeric
                    if (!is_numeric($params[0])) {
                        $this->notFound();
                        return;
                    }
                    $action = 'processEdit';
                    $params = [(int)$params[0]];
                } elseif ($action === 'delete') {
                    $action = 'delete';
                } elseif ($action === 'regenerate-api-key' && !empty($params[0])) {
                    // Validate that the parameter is numeric
                    if (!is_numeric($params[0])) {
                        $this->notFound();
                        return;
                    }
                    $action = 'regenerateApiKey';
                    $params = [(int)$params[0]];
                }
            }
            
            // GET routes
            if ($method === 'GET') {
                if ($action === 'create') {
                    $action = 'create';
                } elseif ($action === 'edit' && !empty($params[0])) {
                    // Validate that the parameter is numeric
                    if (!is_numeric($params[0])) {
                        $this->notFound();
                        return;
                    }
                    $action = 'edit';
                    $params = [(int)$params[0]];
                } elseif ($action === 'view-config' && !empty($params[0])) {
                    // Validate that the parameter is numeric
                    if (!is_numeric($params[0])) {
                        $this->notFound();
                        return;
                    }
                    $action = 'viewConfig';
                    $params = [(int)$params[0]];
                }
            }
        }
        
        // Profile routes
        if ($controllerName === 'ProfileController') {
            if ($method === 'POST' && $action === 'update') {
                $action = 'update';
            } elseif ($method === 'POST' && $action === 'change-password') {
                $action = 'changePassword';
            }
        }
        
        // Controller path
        $controllerFile = ROOT_PATH . '/app/controllers/' . $controllerName . '.php';
        
        if (!file_exists($controllerFile)) {
            $this->notFound();
            return;
        }
        
        require_once $controllerFile;
        
        if (!class_exists($controllerName)) {
            $this->notFound();
            return;
        }
        
        $controller = new $controllerName();
        
        if (!method_exists($controller, $action)) {
            $this->notFound();
            return;
        }
        
        // Call controller action
        call_user_func_array([$controller, $action], $params);
    }
    
    /**
     * Handle API requests
     * 
     * @param array $urlParts
     * @return void
     */
    private function handleApiRequest(array $urlParts): void
    {
        // Load API router
        $apiRouterFile = ROOT_PATH . '/routes/api.php';
        
        if (!file_exists($apiRouterFile)) {
            $this->jsonResponse(['error' => 'API not found'], 404);
            return;
        }
        
        require_once $apiRouterFile;
        
        // Get endpoint (everything after 'api/')
        $endpoint = implode('/', array_slice($urlParts, 1));
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Create API router instance
        $apiRouter = new ApiRouter();
        $apiRouter->route($endpoint, $method);
    }
    
    /**
     * JSON response
     * 
     * @param array $data
     * @param int $code
     * @return void
     */
    private function jsonResponse(array $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Show 404 page
     * 
     * @return void
     */
    private function notFound(): void
    {
        http_response_code(404);
        $requestedUrl = $_SERVER['REQUEST_URI'] ?? '';
        
        echo '<!DOCTYPE html>';
        echo '<html lang="en">';
        echo '<head>';
        echo '<meta charset="UTF-8">';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
        echo '<title>404 Not Found</title>';
        echo '<style>';
        echo 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif; background: #F5F5F7; padding: 40px; }';
        echo '.container { max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }';
        echo 'h1 { color: #DC2626; margin: 0 0 20px 0; }';
        echo 'p { color: #6E6E73; line-height: 1.6; }';
        echo 'code { background: #F5F5F7; padding: 2px 6px; border-radius: 4px; font-family: monospace; }';
        echo 'a { color: #2D7A5B; text-decoration: none; font-weight: 600; }';
        echo 'a:hover { text-decoration: underline; }';
        echo '.error-url { background: #FEF2F2; border: 1px solid #FEE2E2; padding: 15px; border-radius: 8px; margin: 20px 0; word-break: break-all; }';
        echo '</style>';
        echo '</head>';
        echo '<body>';
        echo '<div class="container">';
        echo '<h1>404 Not Found</h1>';
        echo '<p>The page you are looking for could not be found.</p>';
        echo '<div class="error-url"><strong>Requested URL:</strong> ' . htmlspecialchars($requestedUrl) . '</div>';
        echo '<p>This error commonly occurs when:</p>';
        echo '<ul style="color: #6E6E73;">';
        echo '<li>The URL contains invalid characters or parameters</li>';
        echo '<li>A required ID parameter is missing or invalid</li>';
        echo '<li>The resource has been moved or deleted</li>';
        echo '</ul>';
        echo '<p><a href="' . url('/') . '">← Return to Home</a></p>';
        echo '</div>';
        echo '</body>';
        echo '</html>';
        exit;
    }
}
