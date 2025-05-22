<?php
/**
 * DashboardController Class
 * Handles dashboard related operations
 */

require_once APP_PATH . '/core/BaseController.php';
require_once APP_PATH . '/models/UserModel.php';
require_once APP_PATH . '/models/OrganizationModel.php';

class DashboardController extends BaseController {
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
     * Display dashboard page
     */
    public function index() {
        // Require login to access dashboard
        $this->requireLogin();

        // Get current user
        $user = $this->getCurrentUser();
        $organization = $this->organizationModel->getOrganizationById($user['organization_id']);
        $departmentCount = $this->organizationModel->countDepartments($user['organization_id']);
        $userCount = $this->userModel->countUsersByOrganization($user['organization_id']);
        
        // Example stats data for dashboard
        $stats = [
            'total_departments' => $departmentCount,
            'total_users' => $userCount,
            'ongoing_tasks' => 3, // This will be replaced by actual data when task module is implemented
            'pending_leaves' => 1 // This will be replaced by actual data when leave module is implemented
        ];

        // Get ongoing tasks
        $ongoingTasks = $this->getOngoingTasks($user['id']);
        
        // Get notifications
        $notifications = $this->getNotifications($user['id']);

        // Render dashboard view
        $this->view('dashboard/index', [
            'pageTitle' => 'Dashboard',
            'user' => $user,
            'organization' => $organization,
            'stats' => $stats,
            'ongoingTasks' => $ongoingTasks,
            'notifications' => $notifications
        ]);
    }
    
    /**
     * Display user profile page
     */
    public function profile() {
        // Require login to access profile
        $this->requireLogin();
        
        // Get current user
        $user = $this->getUser();
        
        // Get user profile data
        $profile = $this->userModel->getUserProfile($user['id']);
        
        // Get user's organization
        $organization = $this->organizationModel->getOrganizationById($user['organization_id']);
        
        // Get user's department
        $department = $this->organizationModel->getDepartmentById($user['department_id']);
        
        // Get user's role
        $role = $this->organizationModel->getRoleById($user['role_id']);
        
        // Get flash message
        $flash = $this->getFlash();
        
        // Render profile view
        $this->view('dashboard/profile', [
            'user' => $user,
            'profile' => $profile,
            'organization' => $organization,
            'department' => $department,
            'role' => $role,
            'flash' => $flash
        ]);
    }
    
    /**
     * Process profile update
     */
    public function updateProfile() {
        // Require login to update profile
        $this->requireLogin();
        
        // Get current user
        $user = $this->getUser();
        
        // Get form data
        $profileData = [
            'first_name' => $this->post('first_name'),
            'last_name' => $this->post('last_name'),
            'email' => $this->post('email'),
            'phone' => $this->post('phone'),
            'address' => $this->post('address'),
            'city' => $this->post('city'),
            'state' => $this->post('state'),
            'country' => $this->post('country'),
            'postal_code' => $this->post('postal_code'),
        ];
        
        // Update password if provided
        $password = $this->post('password');
        $confirmPassword = $this->post('confirm_password');
        
        if (!empty($password)) {
            // Validate password
            if ($password !== $confirmPassword) {
                $this->setFlash('error', 'Passwords do not match.');
                $this->redirect(BASE_URL . '/dashboard/profile');
            }
            
            if (strlen($password) < 8) {
                $this->setFlash('error', 'Password must be at least 8 characters long.');
                $this->redirect(BASE_URL . '/dashboard/profile');
            }
            
            $profileData['password'] = $password;
        }
        
        // Update profile
        $result = $this->userModel->updateProfile($user['id'], $profileData);
        
        if ($result) {
            $this->setFlash('success', 'Profile updated successfully.');
        } else {
            $this->setFlash('error', 'Failed to update profile.');
        }
        
        $this->redirect(BASE_URL . '/dashboard/profile');
    }

    /**
     * Get ongoing tasks for user
     * @param int $userId User ID
     * @return array
     */
    private function getOngoingTasks($userId) {
        // This is a placeholder until the task module is implemented
        // In the future, this will query the tasks table for assigned tasks
        return [
            [
                'id' => 1,
                'title' => 'Create new project tasks',
                'status' => 'in_progress',
                'priority' => 'medium',
                'due_date' => date('Y-m-d', strtotime('+2 days'))
            ],
            [
                'id' => 2,
                'title' => 'Team meeting preparation',
                'status' => 'to_do',
                'priority' => 'high',
                'due_date' => date('Y-m-d', strtotime('+1 day'))
            ],
            [
                'id' => 3,
                'title' => 'Complete project documentation',
                'status' => 'to_do',
                'priority' => 'low',
                'due_date' => date('Y-m-d', strtotime('+5 days'))
            ]
        ];
    }
    
    /**
     * Get notifications for user
     * @param int $userId User ID
     * @return array
     */
    private function getNotifications($userId) {
        // This is a placeholder until the notifications module is implemented
        return [
            [
                'id' => 1,
                'title' => '1 pending leave request needs approval',
                'type' => 'info',
                'time' => time() - 120 // 2 minutes ago
            ],
            [
                'id' => 2,
                'title' => 'Project milestone due in 2 days',
                'type' => 'warning',
                'time' => time() - 3600 // 1 hour ago
            ],
            [
                'id' => 3,
                'title' => 'Team David\'s birthday tomorrow',
                'type' => 'primary',
                'time' => time() - 18000 // 5 hours ago
            ]
        ];
    }
} 