<?php
/**
 * DepartmentController Class
 * Handles department management operations
 */

require_once APP_PATH . '/core/BaseController.php';
require_once APP_PATH . '/models/OrganizationModel.php';
require_once APP_PATH . '/models/UserModel.php';

class DepartmentController extends BaseController {
    private $organizationModel;
    private $userModel;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->organizationModel = new OrganizationModel();
        $this->userModel = new UserModel();
        
        // Only organization owners and super admins can manage departments
        $this->requireRole(['organization-owner', 'super-admin']);
    }

    /**
     * List all departments for an organization
     */
    public function index() {
        // Get organization ID from session or request
        $organizationId = isset($_GET['organization_id']) 
            ? intval($_GET['organization_id']) 
            : $_SESSION['organization_id'];
            
        // Super admin can view any organization's departments
        // Organization owner can only view their own organization's departments
        if ($_SESSION['user_role'] !== 'super-admin' && $_SESSION['organization_id'] !== $organizationId) {
            $this->setFlash('error', 'You do not have permission to view this organization\'s departments.');
            $this->redirect(BASE_URL . '/dashboard');
        }
        
        // Get organization
        $organization = $this->organizationModel->getOrganizationById($organizationId);
        
        if (!$organization) {
            $this->setFlash('error', 'Organization not found.');
            $this->redirect(BASE_URL . '/dashboard');
        }
        
        // Get departments
        $departments = $this->organizationModel->getDepartmentsByOrganizationId($organizationId);
        
        // Get flash message
        $flash = $this->getFlash();
        
        // Render view
        $this->view('admin/departments/index', [
            'pageTitle' => 'Departments - ' . $organization['name'],
            'organization' => $organization,
            'departments' => $departments,
            'flash' => $flash
        ]);
    }

    /**
     * Show create department form
     */
    public function create() {
        // Get organization ID from request
        $organizationId = isset($_GET['organization_id']) 
            ? intval($_GET['organization_id']) 
            : $_SESSION['organization_id'];
            
        // Super admin can create departments for any organization
        // Organization owner can only create departments for their own organization
        if ($_SESSION['user_role'] !== 'super-admin' && $_SESSION['organization_id'] !== $organizationId) {
            $this->setFlash('error', 'You do not have permission to create departments for this organization.');
            $this->redirect(BASE_URL . '/dashboard');
        }
        
        // Get organization
        $organization = $this->organizationModel->getOrganizationById($organizationId);
        
        if (!$organization) {
            $this->setFlash('error', 'Organization not found.');
            $this->redirect(BASE_URL . '/dashboard');
        }
        
        // Render view
        $this->view('admin/departments/create', [
            'pageTitle' => 'Create Department',
            'organization' => $organization
        ]);
    }

    /**
     * Process create department form
     */
    public function store() {
        // Get form data
        $organizationId = $this->post('organization_id');
        $name = $this->post('name');
        $description = $this->post('description');
        
        // Super admin can create departments for any organization
        // Organization owner can only create departments for their own organization
        if ($_SESSION['user_role'] !== 'super-admin' && $_SESSION['organization_id'] != $organizationId) {
            $this->setFlash('error', 'You do not have permission to create departments for this organization.');
            $this->redirect(BASE_URL . '/dashboard');
        }
        
        // Validate form data
        if (empty($name)) {
            $this->setFlash('error', 'Department name is required.');
            $this->redirect(BASE_URL . '/departments/create?organization_id=' . $organizationId);
        }
        
        // Create department
        $departmentData = [
            'organization_id' => $organizationId,
            'name' => $name,
            'description' => $description
        ];
        
        $departmentId = $this->organizationModel->createDepartment($departmentData);
        
        if (!$departmentId) {
            $this->setFlash('error', 'Failed to create department.');
            $this->redirect(BASE_URL . '/departments/create?organization_id=' . $organizationId);
        }
        
        $this->setFlash('success', 'Department created successfully.');
        $this->redirect(BASE_URL . '/departments?organization_id=' . $organizationId);
    }

    /**
     * Show edit department form
     */
    public function edit($id) {
        // Get department
        $department = $this->organizationModel->getDepartmentById($id);
        
        if (!$department) {
            $this->setFlash('error', 'Department not found.');
            $this->redirect(BASE_URL . '/departments');
        }
        
        // Super admin can edit any department
        // Organization owner can only edit departments in their own organization
        if ($_SESSION['user_role'] !== 'super-admin' && $_SESSION['organization_id'] != $department['organization_id']) {
            $this->setFlash('error', 'You do not have permission to edit this department.');
            $this->redirect(BASE_URL . '/dashboard');
        }
        
        // Get organization
        $organization = $this->organizationModel->getOrganizationById($department['organization_id']);
        
        // Render view
        $this->view('admin/departments/edit', [
            'pageTitle' => 'Edit Department',
            'department' => $department,
            'organization' => $organization
        ]);
    }

    /**
     * Process edit department form
     */
    public function update($id) {
        // Get department
        $department = $this->organizationModel->getDepartmentById($id);
        
        if (!$department) {
            $this->setFlash('error', 'Department not found.');
            $this->redirect(BASE_URL . '/departments');
        }
        
        // Super admin can edit any department
        // Organization owner can only edit departments in their own organization
        if ($_SESSION['user_role'] !== 'super-admin' && $_SESSION['organization_id'] != $department['organization_id']) {
            $this->setFlash('error', 'You do not have permission to edit this department.');
            $this->redirect(BASE_URL . '/dashboard');
        }
        
        // Get form data
        $name = $this->post('name');
        $description = $this->post('description');
        
        // Validate form data
        if (empty($name)) {
            $this->setFlash('error', 'Department name is required.');
            $this->redirect(BASE_URL . '/departments/edit/' . $id);
        }
        
        // Update department
        $departmentData = [
            'name' => $name,
            'description' => $description
        ];
        
        $result = $this->organizationModel->updateDepartment($id, $departmentData);
        
        if (!$result) {
            $this->setFlash('error', 'Failed to update department.');
            $this->redirect(BASE_URL . '/departments/edit/' . $id);
        }
        
        $this->setFlash('success', 'Department updated successfully.');
        $this->redirect(BASE_URL . '/departments?organization_id=' . $department['organization_id']);
    }

    /**
     * Delete department
     */
    public function delete($id) {
        // Get department
        $department = $this->organizationModel->getDepartmentById($id);
        
        if (!$department) {
            $this->setFlash('error', 'Department not found.');
            $this->redirect(BASE_URL . '/departments');
        }
        
        // Super admin can delete any department
        // Organization owner can only delete departments in their own organization
        if ($_SESSION['user_role'] !== 'super-admin' && $_SESSION['organization_id'] != $department['organization_id']) {
            $this->setFlash('error', 'You do not have permission to delete this department.');
            $this->redirect(BASE_URL . '/dashboard');
        }
        
        // Check if it's the default/only department
        $departmentCount = $this->organizationModel->countDepartments($department['organization_id']);
        if ($departmentCount <= 1) {
            $this->setFlash('error', 'Cannot delete the only department in an organization.');
            $this->redirect(BASE_URL . '/departments?organization_id=' . $department['organization_id']);
        }
        
        // Check for users in department
        $usersInDepartment = $this->userModel->countUsersByDepartment($department['id']);
        if ($usersInDepartment > 0) {
            $this->setFlash('error', 'Cannot delete department with associated users.');
            $this->redirect(BASE_URL . '/departments?organization_id=' . $department['organization_id']);
        }
        
        // Delete department
        $result = $this->organizationModel->deleteDepartment($id);
        
        if (!$result) {
            $this->setFlash('error', 'Failed to delete department.');
            $this->redirect(BASE_URL . '/departments?organization_id=' . $department['organization_id']);
        }
        
        $this->setFlash('success', 'Department deleted successfully.');
        $this->redirect(BASE_URL . '/departments?organization_id=' . $department['organization_id']);
    }
} 