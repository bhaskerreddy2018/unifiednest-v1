<?php
/**
 * ProjectController Class
 * Handles project management operations
 */

require_once APP_PATH . '/core/BaseController.php';
require_once APP_PATH . '/models/ProjectModel.php';
require_once APP_PATH . '/models/OrganizationModel.php';
require_once APP_PATH . '/models/UserModel.php';

class ProjectController extends BaseController {
    private $projectModel;
    private $organizationModel;
    private $userModel;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->projectModel = new ProjectModel();
        $this->organizationModel = new OrganizationModel();
        $this->userModel = new UserModel();
        
        // Require login for all project actions
        $this->requireLogin();
    }

    /**
     * List all projects for the user's organization
     */
    public function index() {
        // Get organization ID from session
        $organizationId = $_SESSION['organization_id'];
        
        // Get query parameters for filtering and pagination
        $status = $this->get('status');
        $departmentId = $this->get('department_id');
        $search = $this->get('search');
        $page = intval($this->get('page', 1));
        $limit = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;
        
        // Set options
        $options = [
            'status' => $status,
            'department_id' => $departmentId,
            'search' => $search,
            'limit' => $limit,
            'offset' => $offset,
            'sort' => ['start_date' => 'DESC']
        ];
        
        // Get projects
        $projects = $this->projectModel->getProjects($organizationId, $options);
        
        // Count total projects for pagination
        $totalProjects = $this->projectModel->countProjects($organizationId, $status);
        $totalPages = ceil($totalProjects / $limit);
        
        // Get departments for filter
        $departments = $this->organizationModel->getDepartmentsByOrganizationId($organizationId);
        
        // Get flash message
        $flash = $this->getFlash();
        
        // Render view
        $this->view('projects/index', [
            'pageTitle' => 'Projects',
            'projects' => $projects,
            'departments' => $departments,
            'currentStatus' => $status,
            'currentDepartment' => $departmentId,
            'search' => $search,
            'pagination' => [
                'page' => $page,
                'totalPages' => $totalPages,
                'totalItems' => $totalProjects
            ],
            'flash' => $flash
        ]);
    }

    /**
     * Show create project form
     */
    public function create() {
        // Get organization ID from session
        $organizationId = $_SESSION['organization_id'];
        
        // Get departments
        $departments = $this->organizationModel->getDepartmentsByOrganizationId($organizationId);
        
        // Get users for project members
        $users = $this->userModel->getUsersByOrganization($organizationId);
        
        // Render view
        $this->view('projects/create', [
            'pageTitle' => 'Create Project',
            'departments' => $departments,
            'users' => $users
        ]);
    }

    /**
     * Process create project form
     */
    public function store() {
        // Get organization ID from session
        $organizationId = $_SESSION['organization_id'];
        
        // Get form data
        $name = $this->post('name');
        $description = $this->post('description');
        $clientName = $this->post('client_name');
        $departmentId = $this->post('department_id');
        $startDate = $this->post('start_date');
        $endDate = $this->post('end_date');
        $status = $this->post('status', 'planning');
        $budget = $this->post('budget');
        $members = $this->post('members', []);
        
        // Validate form data
        if (empty($name)) {
            $this->setFlash('error', 'Project name is required.');
            $this->redirect(BASE_URL . '/projects/create');
        }
        
        if (empty($departmentId)) {
            $this->setFlash('error', 'Department is required.');
            $this->redirect(BASE_URL . '/projects/create');
        }
        
        // Create project
        $projectData = [
            'organization_id' => $organizationId,
            'department_id' => $departmentId,
            'name' => $name,
            'description' => $description,
            'client_name' => $clientName,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $status,
            'budget' => $budget,
            'created_by' => $_SESSION['user_id']
        ];
        
        $projectId = $this->projectModel->createProject($projectData);
        
        if (!$projectId) {
            $this->setFlash('error', 'Failed to create project.');
            $this->redirect(BASE_URL . '/projects/create');
        }
        
        // Keep track of added members to avoid duplicates
        $addedMembers = [];
        
        // Add project creator as a member with manager role (first to ensure priority)
        $currentUserId = $_SESSION['user_id'];
        $creatorData = [
            'project_id' => $projectId,
            'user_id' => $currentUserId,
            'role' => 'manager'
        ];
        $this->projectModel->addProjectMember($creatorData);
        $addedMembers[] = $currentUserId;
        
        // Add other project members
        if (!empty($members)) {
            foreach ($members as $memberId) {
                // Skip if this member is already added (avoid duplicate error)
                if (in_array($memberId, $addedMembers)) {
                    continue;
                }
                
                $memberData = [
                    'project_id' => $projectId,
                    'user_id' => $memberId,
                    'role' => 'member'
                ];
                $this->projectModel->addProjectMember($memberData);
                $addedMembers[] = $memberId;
            }
        }
        
        $this->setFlash('success', 'Project created successfully.');
        $this->redirect(BASE_URL . '/projects/viewProject/' . $projectId);
    }

    /**
     * Show project details
     */
    public function viewProject($id) {
        // Get project
        $project = $this->projectModel->getProjectById($id);
        
        if (!$project) {
            $this->setFlash('error', 'Project not found.');
            $this->redirect(BASE_URL . '/projects');
        }
        
        // Check if user belongs to the project's organization
        if ($project['organization_id'] != $_SESSION['organization_id']) {
            $this->setFlash('error', 'You do not have permission to view this project.');
            $this->redirect(BASE_URL . '/projects');
        }
        
        // Get project members
        $members = $this->projectModel->getProjectMembers($id);
        
        // Get project tasks
        $tasks = $this->projectModel->getProjectTasks($id, [
            'sort' => [
                'status' => 'ASC',
                'priority' => 'DESC',
                'due_date' => 'ASC'
            ]
        ]);
        
        // Get project documents
        $documents = $this->projectModel->getProjectDocuments($id);
        
        // Get department
        $department = $this->organizationModel->getDepartmentById($project['department_id']);
        
        // Get flash message
        $flash = $this->getFlash();
        
        // Render view
        $this->view('projects/view', [
            'pageTitle' => $project['name'],
            'project' => $project,
            'department' => $department,
            'members' => $members,
            'tasks' => $tasks,
            'documents' => $documents,
            'flash' => $flash
        ]);
    }

    /**
     * Show edit project form
     */
    public function edit($id) {
        // Get project
        $project = $this->projectModel->getProjectById($id);
        
        if (!$project) {
            $this->setFlash('error', 'Project not found.');
            $this->redirect(BASE_URL . '/projects');
        }
        
        // Check if user belongs to the project's organization
        if ($project['organization_id'] != $_SESSION['organization_id']) {
            $this->setFlash('error', 'You do not have permission to edit this project.');
            $this->redirect(BASE_URL . '/projects');
        }
        
        // Get organization ID from session
        $organizationId = $_SESSION['organization_id'];
        
        // Get departments
        $departments = $this->organizationModel->getDepartmentsByOrganizationId($organizationId);
        
        // Get users for project members
        $users = $this->userModel->getUsersByOrganization($organizationId);
        
        // Get current project members
        $currentMembers = $this->projectModel->getProjectMembers($id);
        $memberIds = array_column($currentMembers, 'user_id');
        
        // Render view
        $this->view('projects/edit', [
            'pageTitle' => 'Edit Project',
            'project' => $project,
            'departments' => $departments,
            'users' => $users,
            'memberIds' => $memberIds
        ]);
    }

    /**
     * Process edit project form
     */
    public function update($id) {
        // Get project
        $project = $this->projectModel->getProjectById($id);
        
        if (!$project) {
            $this->setFlash('error', 'Project not found.');
            $this->redirect(BASE_URL . '/projects');
        }
        
        // Check if user belongs to the project's organization
        if ($project['organization_id'] != $_SESSION['organization_id']) {
            $this->setFlash('error', 'You do not have permission to edit this project.');
            $this->redirect(BASE_URL . '/projects');
        }
        
        // Get form data
        $name = $this->post('name');
        $description = $this->post('description');
        $clientName = $this->post('client_name');
        $departmentId = $this->post('department_id');
        $startDate = $this->post('start_date');
        $endDate = $this->post('end_date');
        $status = $this->post('status');
        $budget = $this->post('budget');
        $members = $this->post('members', []);
        
        // Validate form data
        if (empty($name)) {
            $this->setFlash('error', 'Project name is required.');
            $this->redirect(BASE_URL . '/projects/edit/' . $id);
        }
        
        if (empty($departmentId)) {
            $this->setFlash('error', 'Department is required.');
            $this->redirect(BASE_URL . '/projects/edit/' . $id);
        }
        
        // Update project
        $projectData = [
            'department_id' => $departmentId,
            'name' => $name,
            'description' => $description,
            'client_name' => $clientName,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $status,
            'budget' => $budget
        ];
        
        $result = $this->projectModel->updateProject($id, $projectData);
        
        if (!$result) {
            $this->setFlash('error', 'Failed to update project.');
            $this->redirect(BASE_URL . '/projects/edit/' . $id);
        }
        
        // Get current project members
        $currentMembers = $this->projectModel->getProjectMembers($id);
        $currentMemberIds = array_column($currentMembers, 'user_id');
        $managerIds = [];
        
        // Identify manager IDs (to preserve their roles)
        foreach ($currentMembers as $member) {
            if ($member['role'] === 'manager') {
                $managerIds[] = $member['user_id'];
            }
        }
        
        // Add new members
        foreach ($members as $memberId) {
            if (!in_array($memberId, $currentMemberIds)) {
                $role = in_array($memberId, $managerIds) ? 'manager' : 'member';
                $memberData = [
                    'project_id' => $id,
                    'user_id' => $memberId,
                    'role' => $role
                ];
                $this->projectModel->addProjectMember($memberData);
            }
        }
        
        // Remove members not in the new list
        foreach ($currentMembers as $member) {
            // Skip managers to retain project ownership
            if ($member['role'] === 'manager') {
                continue;
            }
            
            if (!in_array($member['user_id'], $members)) {
                $this->projectModel->removeProjectMember($id, $member['user_id']);
            }
        }
        
        $this->setFlash('success', 'Project updated successfully.');
        $this->redirect(BASE_URL . '/projects/viewProject/' . $id);
    }

    /**
     * Delete project
     */
    public function delete($id) {
        // Get project
        $project = $this->projectModel->getProjectById($id);
        
        if (!$project) {
            $this->setFlash('error', 'Project not found.');
            $this->redirect(BASE_URL . '/projects');
        }
        
        // Check if user belongs to the project's organization
        if ($project['organization_id'] != $_SESSION['organization_id']) {
            $this->setFlash('error', 'You do not have permission to delete this project.');
            $this->redirect(BASE_URL . '/projects');
        }
        
        // Delete project
        $result = $this->projectModel->deleteProject($id);
        
        if (!$result) {
            $this->setFlash('error', 'Failed to delete project.');
            $this->redirect(BASE_URL . '/projects');
        }
        
        $this->setFlash('success', 'Project deleted successfully.');
        $this->redirect(BASE_URL . '/projects');
    }
    
    /**
     * Add task to project
     */
    public function addTask($projectId) {
        // Get project
        $project = $this->projectModel->getProjectById($projectId);
        
        if (!$project) {
            $this->setFlash('error', 'Project not found.');
            $this->redirect(BASE_URL . '/projects');
        }
        
        // Check if user belongs to the project's organization
        if ($project['organization_id'] != $_SESSION['organization_id']) {
            $this->setFlash('error', 'You do not have permission to add tasks to this project.');
            $this->redirect(BASE_URL . '/projects');
        }
        
        // Get form data
        $title = $this->post('title');
        $description = $this->post('description');
        $status = $this->post('status', 'to_do');
        $priority = $this->post('priority', 'medium');
        $dueDate = $this->post('due_date');
        $assignedTo = $this->post('assigned_to');
        
        // Validate form data
        if (empty($title)) {
            $this->setFlash('error', 'Task title is required.');
            $this->redirect(BASE_URL . '/projects/viewProject/' . $projectId);
        }
        
        // Create task
        $taskData = [
            'project_id' => $projectId,
            'title' => $title,
            'description' => $description,
            'status' => $status,
            'priority' => $priority,
            'due_date' => $dueDate,
            'assigned_to' => $assignedTo,
            'created_by' => $_SESSION['user_id']
        ];
        
        $taskId = $this->projectModel->addProjectTask($taskData);
        
        if (!$taskId) {
            $this->setFlash('error', 'Failed to create task.');
            $this->redirect(BASE_URL . '/projects/viewProject/' . $projectId);
        }
        
        $this->setFlash('success', 'Task added successfully.');
        $this->redirect(BASE_URL . '/projects/viewProject/' . $projectId);
    }
    
    /**
     * Update task status
     */
    public function updateTaskStatus($taskId) {
        // Get task
        $task = $this->db->get('project_tasks', [
            'id',
            'project_id',
            'status'
        ], ['id' => $taskId]);
        
        if (!$task) {
            $this->json(['success' => false, 'message' => 'Task not found.'], 404);
            return;
        }
        
        // Get project
        $project = $this->projectModel->getProjectById($task['project_id']);
        
        // Check if user belongs to the project's organization
        if ($project['organization_id'] != $_SESSION['organization_id']) {
            $this->json(['success' => false, 'message' => 'Permission denied.'], 403);
            return;
        }
        
        // Get new status
        $status = $this->post('status');
        
        // Update task status
        $result = $this->projectModel->updateProjectTask($taskId, ['status' => $status]);
        
        if (!$result) {
            $this->json(['success' => false, 'message' => 'Failed to update task status.'], 500);
            return;
        }
        
        $this->json(['success' => true, 'message' => 'Task status updated.']);
    }
    
    /**
     * Upload document
     */
    public function uploadDocument($projectId) {
        // Get project
        $project = $this->projectModel->getProjectById($projectId);
        
        if (!$project) {
            $this->setFlash('error', 'Project not found.');
            $this->redirect(BASE_URL . '/projects');
        }
        
        // Check if user belongs to the project's organization
        if ($project['organization_id'] != $_SESSION['organization_id']) {
            $this->setFlash('error', 'You do not have permission to upload documents to this project.');
            $this->redirect(BASE_URL . '/projects');
        }
        
        // Check if file was uploaded
        if (!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
            $this->setFlash('error', 'Please select a file to upload.');
            $this->redirect(BASE_URL . '/projects/viewProject/' . $projectId);
        }
        
        // Get form data
        $description = $this->post('description');
        
        // Upload file
        $file = $_FILES['document'];
        
        // Check file size
        if ($file['size'] > MAX_UPLOAD_SIZE) {
            $this->setFlash('error', 'File size exceeds maximum limit (' . (MAX_UPLOAD_SIZE / 1024 / 1024) . 'MB).');
            $this->redirect(BASE_URL . '/projects/viewProject/' . $projectId);
        }
        
        // Get file extension
        $fileInfo = pathinfo($file['name']);
        $extension = strtolower($fileInfo['extension']);
        
        // Check file extension
        $allowedExtensions = explode(',', ALLOWED_EXTENSIONS);
        if (!in_array($extension, $allowedExtensions)) {
            $this->setFlash('error', 'File type not allowed.');
            $this->redirect(BASE_URL . '/projects/viewProject/' . $projectId);
        }
        
        // Create upload directory if it doesn't exist
        $uploadDir = STORAGE_PATH . '/uploads/projects/' . $projectId;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate unique filename
        $filename = uniqid() . '_' . $file['name'];
        $filepath = 'uploads/projects/' . $projectId . '/' . $filename;
        $destination = STORAGE_PATH . '/' . $filepath;
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            $this->setFlash('error', 'Failed to upload file.');
            $this->redirect(BASE_URL . '/projects/viewProject/' . $projectId);
        }
        
        // Save document details to database
        $documentData = [
            'project_id' => $projectId,
            'filename' => $file['name'],
            'file_path' => $filepath,
            'file_size' => $file['size'],
            'file_type' => $file['type'],
            'description' => $description,
            'uploaded_by' => $_SESSION['user_id']
        ];
        
        $documentId = $this->projectModel->addProjectDocument($documentData);
        
        if (!$documentId) {
            $this->setFlash('error', 'Failed to save document details.');
            $this->redirect(BASE_URL . '/projects/viewProject/' . $projectId);
        }
        
        $this->setFlash('success', 'Document uploaded successfully.');
        $this->redirect(BASE_URL . '/projects/viewProject/' . $projectId);
    }
    
    /**
     * Delete document
     */
    public function deleteDocument($documentId) {
        // Get document
        $document = $this->db->get('project_documents', [
            'id',
            'project_id',
            'file_path'
        ], ['id' => $documentId]);
        
        if (!$document) {
            $this->setFlash('error', 'Document not found.');
            $this->redirect(BASE_URL . '/projects');
        }
        
        // Get project
        $project = $this->projectModel->getProjectById($document['project_id']);
        
        // Check if user belongs to the project's organization
        if ($project['organization_id'] != $_SESSION['organization_id']) {
            $this->setFlash('error', 'You do not have permission to delete this document.');
            $this->redirect(BASE_URL . '/projects');
        }
        
        // Delete document
        $result = $this->projectModel->deleteProjectDocument($documentId);
        
        if (!$result) {
            $this->setFlash('error', 'Failed to delete document.');
            $this->redirect(BASE_URL . '/projects/viewProject/' . $document['project_id']);
        }
        
        $this->setFlash('success', 'Document deleted successfully.');
        $this->redirect(BASE_URL . '/projects/viewProject/' . $document['project_id']);
    }
} 