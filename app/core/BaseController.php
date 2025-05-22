<?php
/**
 * BaseController Class
 * All controllers will extend this class
 */

require_once APP_PATH . '/models/UserModel.php';
require_once APP_PATH . '/models/OrganizationModel.php';

class BaseController {
    protected $db;
    protected $data = [];

    /**
     * Constructor
     */
    public function __construct() {
        // Initialize database connection
        $this->db = Database::getInstance();
        
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_start();
        }
    }
    
    /**
     * Render a view with data
     * @param string $view Path to the view file
     * @param array $data Data to pass to the view
     * @param bool $return Whether to return the view as string
     * @return string|void
     */
    protected function view($view, $data = [], $return = false) {
        // Merge controller data with view data
        $this->data = array_merge($this->data, $data);
        
        // Extract data to make it accessible in the view
        extract($this->data);
        
        // Start output buffering
        ob_start();
        
        // Include the view file
        $viewPath = VIEW_PATH . '/' . $view . '.php';
        
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            throw new Exception("View file '{$view}.php' not found.");
        }
        
        // Get the buffer contents
        $content = ob_get_clean();
        
        if ($return) {
            return $content;
        }
        
        echo $content;
    }
    
    /**
     * Redirect to a URL
     * @param string $url URL to redirect to
     * @return void
     */
    protected function redirect($url) {
        header('Location: ' . $url);
        exit;
    }
    
    /**
     * Set flash message
     * @param string $type Message type (success, error, info, warning)
     * @param string $message Message content
     * @return void
     */
    protected function setFlash($type, $message) {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }
    
    /**
     * Get flash message and remove it from session
     * @return array|null Flash message
     */
    protected function getFlash() {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
    
    /**
     * Check if user is logged in
     * @return bool
     */
    protected function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Get current logged in user data
     * @return array|null User data
     */
    protected function getUser() {
        if ($this->isLoggedIn()) {
            return $this->db->get('users', '*', ['id' => $_SESSION['user_id']]);
        }
        return null;
    }
    
    /**
     * Ensure user is authenticated
     */
    protected function requireLogin() {
        if (!isset($_SESSION['user_id'])) {
            $this->setFlash('error', 'Please login to continue.');
            $this->redirect(BASE_URL . '/login');
        }
    }
    
    /**
     * Ensure user has specific role
     * @param string|array $roles Required role(s)
     */
    protected function requireRole($roles) {
        $this->requireLogin();
        
        // Convert single role to array
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        
        // Check if user has one of the required roles
        if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], $roles)) {
            $this->setFlash('error', 'You do not have permission to access this area.');
            $this->redirect(BASE_URL . '/dashboard');
        }
    }
    
    /**
     * Get current authenticated user
     * @return array|bool User data or false if not authenticated
     */
    protected function getCurrentUser() {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        $userModel = new UserModel();
        return $userModel->getUserById($_SESSION['user_id']);
    }
    
    /**
     * Check if user has role
     * @param string|array $roles Role(s) to check
     * @return bool
     */
    protected function hasRole($roles) {
        if (!isset($_SESSION['user_role'])) {
            return false;
        }
        
        // Convert single role to array
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        
        return in_array($_SESSION['user_role'], $roles);
    }
    
    /**
     * Get POST data
     * @param string $key POST data key
     * @param mixed $default Default value if key doesn't exist
     * @return mixed
     */
    protected function post($key, $default = null) {
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }
    
    /**
     * Get GET data
     * @param string $key GET data key
     * @param mixed $default Default value if key doesn't exist
     * @return mixed
     */
    protected function get($key, $default = null) {
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }
    
    /**
     * Return JSON response
     * @param array $data Data to encode as JSON
     * @param int $statusCode HTTP status code
     * @return void
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
} 