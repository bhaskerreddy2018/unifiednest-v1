<?php
/**
 * OrganizationController Class
 * Handles organization management operations
 */

require_once APP_PATH . '/core/BaseController.php';
require_once APP_PATH . '/models/OrganizationModel.php';

class OrganizationController extends BaseController {
    private $organizationModel;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->organizationModel = new OrganizationModel();
        
        // Super admin only
        $this->requireRole('super-admin');
    }

    /**
     * List all organizations
     */
    public function index() {
        // Get all organizations
        $organizations = $this->organizationModel->getAllOrganizations();
        
        // Get flash message
        $flash = $this->getFlash();
        
        // Render view
        $this->view('admin/organizations/index', [
            'pageTitle' => 'Organizations',
            'organizations' => $organizations,
            'flash' => $flash
        ]);
    }

    /**
     * Show create organization form
     */
    public function create() {
        // Render view
        $this->view('admin/organizations/create', [
            'pageTitle' => 'Create Organization'
        ]);
    }

    /**
     * Process create organization form
     */
    public function store() {
        // Get form data
        $name = $this->post('name');
        $address = $this->post('address');
        $email = $this->post('email');
        $phone = $this->post('phone');
        $website = $this->post('website');
        
        // Validate form data
        if (empty($name)) {
            $this->setFlash('error', 'Organization name is required.');
            $this->redirect(BASE_URL . '/organizations/create');
        }
        
        // Create organization
        $organizationData = [
            'name' => $name,
            'address' => $address,
            'email' => $email,
            'phone' => $phone,
            'website' => $website
        ];
        
        // Handle logo upload if provided
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $logo = $this->uploadLogo($_FILES['logo']);
            if ($logo) {
                $organizationData['logo'] = $logo;
            }
        }
        
        $organizationId = $this->organizationModel->createOrganization($organizationData);
        
        if (!$organizationId) {
            $this->setFlash('error', 'Failed to create organization.');
            $this->redirect(BASE_URL . '/organizations/create');
        }
        
        // Create default department for the organization
        $departmentId = $this->organizationModel->createDepartment([
            'organization_id' => $organizationId,
            'name' => 'General',
            'description' => 'Default department'
        ]);
        
        if (!$departmentId) {
            $this->setFlash('error', 'Created organization but failed to create default department.');
        }
        
        $this->setFlash('success', 'Organization created successfully.');
        $this->redirect(BASE_URL . '/organizations');
    }

    /**
     * Show edit organization form
     */
    public function edit($id) {
        // Get organization
        $organization = $this->organizationModel->getOrganizationById($id);
        
        if (!$organization) {
            $this->setFlash('error', 'Organization not found.');
            $this->redirect(BASE_URL . '/organizations');
        }
        
        // Render view
        $this->view('admin/organizations/edit', [
            'pageTitle' => 'Edit Organization',
            'organization' => $organization
        ]);
    }

    /**
     * Process edit organization form
     */
    public function update($id) {
        // Get organization
        $organization = $this->organizationModel->getOrganizationById($id);
        
        if (!$organization) {
            $this->setFlash('error', 'Organization not found.');
            $this->redirect(BASE_URL . '/organizations');
        }
        
        // Get form data
        $name = $this->post('name');
        $address = $this->post('address');
        $email = $this->post('email');
        $phone = $this->post('phone');
        $website = $this->post('website');
        
        // Validate form data
        if (empty($name)) {
            $this->setFlash('error', 'Organization name is required.');
            $this->redirect(BASE_URL . '/organizations/edit/' . $id);
        }
        
        // Update organization
        $organizationData = [
            'name' => $name,
            'address' => $address,
            'email' => $email,
            'phone' => $phone,
            'website' => $website
        ];
        
        // Handle logo upload if provided
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $logo = $this->uploadLogo($_FILES['logo']);
            if ($logo) {
                // Remove old logo if exists
                if (!empty($organization['logo'])) {
                    $oldLogoPath = STORAGE_PATH . '/uploads/logos/' . $organization['logo'];
                    if (file_exists($oldLogoPath)) {
                        unlink($oldLogoPath);
                    }
                }
                
                $organizationData['logo'] = $logo;
            }
        }
        
        $result = $this->organizationModel->updateOrganization($id, $organizationData);
        
        if (!$result) {
            $this->setFlash('error', 'Failed to update organization.');
            $this->redirect(BASE_URL . '/organizations/edit/' . $id);
        }
        
        $this->setFlash('success', 'Organization updated successfully.');
        $this->redirect(BASE_URL . '/organizations');
    }

    /**
     * Delete organization
     */
    public function delete($id) {
        // Check for any users associated with this organization
        $userCount = $this->organizationModel->countOrganizationUsers($id);
        
        if ($userCount > 0) {
            $this->setFlash('error', 'Cannot delete organization with associated users.');
            $this->redirect(BASE_URL . '/organizations');
        }
        
        // Get organization
        $organization = $this->organizationModel->getOrganizationById($id);
        
        if (!$organization) {
            $this->setFlash('error', 'Organization not found.');
            $this->redirect(BASE_URL . '/organizations');
        }
        
        // Delete organization
        $result = $this->organizationModel->deleteOrganization($id);
        
        if (!$result) {
            $this->setFlash('error', 'Failed to delete organization.');
            $this->redirect(BASE_URL . '/organizations');
        }
        
        // Delete logo if exists
        if (!empty($organization['logo'])) {
            $logoPath = STORAGE_PATH . '/uploads/logos/' . $organization['logo'];
            if (file_exists($logoPath)) {
                unlink($logoPath);
            }
        }
        
        $this->setFlash('success', 'Organization deleted successfully.');
        $this->redirect(BASE_URL . '/organizations');
    }

    /**
     * Upload logo file
     * @param array $file Logo file data
     * @return string|false Filename or false on failure
     */
    private function uploadLogo($file) {
        // Check file size
        if ($file['size'] > MAX_UPLOAD_SIZE) {
            $this->setFlash('error', 'Logo file is too large. Maximum size is ' . (MAX_UPLOAD_SIZE / 1024 / 1024) . 'MB.');
            return false;
        }
        
        // Check file type
        $allowed = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowed)) {
            $this->setFlash('error', 'Invalid file type. Allowed types: JPG, PNG, GIF.');
            return false;
        }
        
        // Create directory if not exists
        $uploadDir = STORAGE_PATH . '/uploads/logos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate unique filename
        $filename = uniqid() . '_' . $file['name'];
        $destination = $uploadDir . $filename;
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            $this->setFlash('error', 'Failed to upload logo.');
            return false;
        }
        
        return $filename;
    }
} 