<?php
/**
 * AuthController Class
 * Handles authentication related operations
 */

require_once APP_PATH . '/core/BaseController.php';
require_once APP_PATH . '/models/UserModel.php';
require_once APP_PATH . '/models/OrganizationModel.php';

class AuthController extends BaseController {
    private $userModel;
    private $organizationModel;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->userModel = new UserModel();
        $this->organizationModel = new OrganizationModel();
    }

    /**
     * Show login page
     */
    public function login() {
        // Redirect to dashboard if already logged in
        if ($this->isLoggedIn()) {
            $this->redirect(BASE_URL . '/dashboard');
        }

        // Get flash message
        $flash = $this->getFlash();

        // Render login page
        $this->view('auth/login', [
            'flash' => $flash
        ]);
    }

    /**
     * Process login form
     */
    public function processLogin() {
        // Redirect to dashboard if already logged in
        if ($this->isLoggedIn()) {
            $this->redirect(BASE_URL . '/dashboard');
        }

        // Get form data
        $email = $this->post('email');
        $password = $this->post('password');
        $remember = $this->post('remember') ? true : false;

        // Validate form data
        if (empty($email) || empty($password)) {
            $this->setFlash('error', 'Please enter email and password.');
            $this->redirect(BASE_URL . '/login');
        }

        // Authenticate user
        $user = $this->userModel->authenticate($email, $password);

        if (!$user) {
            $this->setFlash('error', 'Invalid email or password.');
            $this->redirect(BASE_URL . '/login');
        }

        // Set session data
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['organization_id'] = $user['organization_id'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $this->userModel->getUserRole($user['id']);

        // Handle remember me
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $expires = time() + (30 * 24 * 60 * 60); // 30 days
            setcookie('remember_token', $token, $expires, '/', '', false, true);

            // Store token in database (implementation needed)
            // $this->userModel->storeRememberToken($user['id'], $token, $expires);
        }

        // Check if onboarding is completed
        if (!$user['onboarding_completed']) {
            $this->redirect(BASE_URL . '/onboarding');
        }

        // Redirect to dashboard
        $this->redirect(BASE_URL . '/dashboard');
    }

    /**
     * Show registration page
     */
    public function register() {
        // If there are already users and the user is not logged in as super admin,
        // redirect to login page as registration should be disabled after first user
        if (!$this->userModel->isFirstUser() && (!$this->isLoggedIn() || !$this->hasRole('super-admin'))) {
            $this->setFlash('warning', 'Registration is disabled. Please contact administrator.');
            $this->redirect(BASE_URL . '/login');
        }

        // Get flash message
        $flash = $this->getFlash();

        // Render register page
        $this->view('auth/register', [
            'flash' => $flash,
            'isFirstUser' => $this->userModel->isFirstUser()
        ]);
    }

    /**
     * Process registration form
     */
    public function processRegister() {
        // Check if registration should be allowed - if first user, allow it.
        // Otherwise, only allow super-admin to create users.
        $isFirstUser = $this->userModel->isFirstUser();
        if (!$isFirstUser && (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'super-admin')) {
            $this->setFlash('error', 'Registration is not allowed. Please contact administrator.');
            $this->redirect(BASE_URL . '/login');
        }
        
        // Get form data
        $first_name = $this->post('first_name');
        $last_name = $this->post('last_name');
        $email = $this->post('email');
        $password = $this->post('password');
        $confirm_password = $this->post('confirm_password');
        
        // Validate data
        if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($confirm_password)) {
            $this->setFlash('error', 'All fields are required.');
            $this->redirect(BASE_URL . '/register');
        }
        
        if ($password !== $confirm_password) {
            $this->setFlash('error', 'Passwords do not match.');
            $this->redirect(BASE_URL . '/register');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setFlash('error', 'Invalid email format.');
            $this->redirect(BASE_URL . '/register');
        }
        
        // Check if email already exists
        if ($this->userModel->emailExists($email)) {
            $this->setFlash('error', 'Email already registered. Please login instead.');
            $this->redirect(BASE_URL . '/login');
        }

        // Determine role for the user
        $organizationModel = new OrganizationModel();

        if ($isFirstUser) {
            // First user is super-admin
            $role = 'super-admin';
            $roleId = 1; // Assuming role_id for super-admin is 1
        } else {
            // Default role for new users
            $role = 'employee';
            $roleId = 5; // Assuming role_id for employee is 5
        }
        
        // Register user
        $userData = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'password' => $password,
            'role_id' => $roleId,
            'organization_id' => null, // Will be set after onboarding for first user
            'department_id' => null  // Will be set after onboarding
        ];
        
        $userId = $this->userModel->register($userData);
        
        if ($userId) {
            // If first user, log them in directly and redirect to onboarding
            if ($isFirstUser) {
                $user = $this->userModel->getUserById($userId);
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['user_role'] = $role;
                
                $this->redirect(BASE_URL . '/onboarding');
            } else {
                $this->setFlash('success', 'Registration successful. You can now login.');
                $this->redirect(BASE_URL . '/login');
            }
        } else {
            $this->setFlash('error', 'Registration failed. Please try again.');
            $this->redirect(BASE_URL . '/register');
        }
    }

    /**
     * Logout user
     */
    public function logout() {
        // Clear session data
        session_unset();
        session_destroy();

        // Clear remember me cookie if exists
        if (isset($_COOKIE['remember_token'])) {
            // Remove token from database (implementation needed)
            // $this->userModel->removeRememberToken($_COOKIE['remember_token']);

            setcookie('remember_token', '', time() - 3600, '/');
        }

        // Redirect to login page
        $this->redirect(BASE_URL . '/login');
    }

    /**
     * Show onboarding page
     */
    public function onboarding() {
        // Require login
        $this->requireLogin();

        // Get user
        $user = $this->getUser();

        // Redirect to dashboard if onboarding is already completed
        if ($user['onboarding_completed']) {
            $this->redirect(BASE_URL . '/dashboard');
        }

        // Get flash message
        $flash = $this->getFlash();

        // Render onboarding page
        $this->view('auth/onboarding', [
            'flash' => $flash,
            'user' => $user
        ]);
    }

    /**
     * Process onboarding form
     */
    public function processOnboarding() {
        $this->requireLogin();
        
        // Get form data
        $organizationName = $this->post('organization_name');
        $organizationEmail = $this->post('organization_email');
        $organizationPhone = $this->post('organization_phone');
        $departmentName = $this->post('department_name');
        
        // Validate data
        if (empty($organizationName)) {
            $this->setFlash('error', 'Organization name is required.');
            $this->redirect(BASE_URL . '/onboarding');
        }
        
        // Create organization
        $organizationModel = new OrganizationModel();
        $organizationData = [
            'name' => $organizationName,
            'email' => $organizationEmail,
            'phone' => $organizationPhone
        ];
        
        $organizationId = $organizationModel->createOrganization($organizationData);
        
        if (!$organizationId) {
            $this->setFlash('error', 'Failed to create organization.');
            $this->redirect(BASE_URL . '/onboarding');
        }
        
        // Create default department if not specified
        if (empty($departmentName)) {
            $departmentName = 'General';
        }
        
        $departmentData = [
            'organization_id' => $organizationId,
            'name' => $departmentName,
            'description' => 'Default department'
        ];
        
        $departmentId = $organizationModel->createDepartment($departmentData);
        
        if (!$departmentId) {
            $this->setFlash('error', 'Failed to create department.');
            $this->redirect(BASE_URL . '/onboarding');
        }
        
        // Update user with organization and department
        $userId = $_SESSION['user_id'];
        $userData = [
            'organization_id' => $organizationId,
            'department_id' => $departmentId,
            'onboarding_completed' => true
        ];
        
        $this->userModel->updateProfile($userId, $userData);
        
        // Redirect to dashboard
        $this->setFlash('success', 'Onboarding completed successfully!');
        $this->redirect(BASE_URL . '/dashboard');
    }
} 