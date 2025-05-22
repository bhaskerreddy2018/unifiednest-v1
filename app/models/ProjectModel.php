<?php
/**
 * ProjectModel Class
 * Handles project related operations
 */

class ProjectModel {
    private $db;

    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Get all projects for an organization
     * @param int $organizationId Organization ID
     * @param array $options Filter options (limit, offset, status, etc.)
     * @return array
     */
    public function getProjects($organizationId, $options = []) {
        $where = ['organization_id' => $organizationId];
        
        // Apply filters
        if (isset($options['status']) && !empty($options['status'])) {
            $where['status'] = $options['status'];
        }
        
        if (isset($options['department_id']) && !empty($options['department_id'])) {
            $where['department_id'] = $options['department_id'];
        }
        
        if (isset($options['search']) && !empty($options['search'])) {
            $where['OR'] = [
                'name[~]' => $options['search'],
                'description[~]' => $options['search'],
                'client_name[~]' => $options['search']
            ];
        }
        
        // Determine sorting
        $orderBy = isset($options['sort']) ? $options['sort'] : ['start_date' => 'DESC'];
        
        // Determine pagination
        $limit = isset($options['limit']) ? $options['limit'] : null;
        $offset = isset($options['offset']) ? $options['offset'] : null;
        
        // Get projects
        $projects = $this->db->select('projects', [
            '[>]departments' => ['department_id' => 'id']
        ], [
            'projects.id',
            'projects.name',
            'projects.description',
            'projects.status',
            'projects.client_name',
            'projects.start_date',
            'projects.end_date',
            'projects.budget',
            'projects.department_id',
            'departments.name(department_name)'
        ], [
            'AND' => $where,
            'ORDER' => $orderBy,
            'LIMIT' => [$offset, $limit]
        ]);
        
        return $projects;
    }
    
    /**
     * Get project by ID
     * @param int $id Project ID
     * @return array|bool Project data or false if not found
     */
    public function getProjectById($id) {
        return $this->db->get('projects', '*', ['id' => $id]);
    }
    
    /**
     * Create a new project
     * @param array $projectData Project data
     * @return int|bool Project ID or false on failure
     */
    public function createProject($projectData) {
        $projectData['created_at'] = date('Y-m-d H:i:s');
        $projectData['updated_at'] = date('Y-m-d H:i:s');
        
        return $this->db->insert('projects', $projectData);
    }
    
    /**
     * Update project
     * @param int $id Project ID
     * @param array $projectData Project data
     * @return bool Success/failure
     */
    public function updateProject($id, $projectData) {
        $projectData['updated_at'] = date('Y-m-d H:i:s');
        $result = $this->db->update('projects', $projectData, ['id' => $id]);
        return $result > 0;
    }
    
    /**
     * Delete project
     * @param int $id Project ID
     * @return bool Success/failure
     */
    public function deleteProject($id) {
        // First delete project-related data
        $this->db->delete('project_members', ['project_id' => $id]);
        $this->db->delete('project_tasks', ['project_id' => $id]);
        $this->db->delete('project_documents', ['project_id' => $id]);
        
        // Then delete the project
        $result = $this->db->delete('projects', ['id' => $id]);
        return $result > 0;
    }
    
    /**
     * Count projects in organization
     * @param int $organizationId Organization ID
     * @param string $status Optional status filter
     * @return int
     */
    public function countProjects($organizationId, $status = null) {
        $where = ['organization_id' => $organizationId];
        
        if ($status) {
            $where['status'] = $status;
        }
        
        return $this->db->count('projects', $where);
    }
    
    /**
     * Get project members
     * @param int $projectId Project ID
     * @return array
     */
    public function getProjectMembers($projectId) {
        return $this->db->select('project_members', [
            '[>]users' => ['user_id' => 'id']
        ], [
            'project_members.id',
            'project_members.user_id',
            'project_members.role',
            'project_members.created_at',
            'users.first_name',
            'users.last_name',
            'users.email',
            'users.profile_image'
        ], [
            'project_members.project_id' => $projectId
        ]);
    }
    
    /**
     * Add project member
     * @param array $memberData Member data
     * @return int|bool Member ID or false on failure
     */
    public function addProjectMember($memberData) {
        $memberData['created_at'] = date('Y-m-d H:i:s');
        $memberData['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('project_members', $memberData);
    }
    
    /**
     * Remove project member
     * @param int $projectId Project ID
     * @param int $userId User ID
     * @return bool Success/failure
     */
    public function removeProjectMember($projectId, $userId) {
        $result = $this->db->delete('project_members', [
            'AND' => [
                'project_id' => $projectId,
                'user_id' => $userId
            ]
        ]);
        return $result > 0;
    }
    
    /**
     * Get project tasks
     * @param int $projectId Project ID
     * @param array $options Filter options
     * @return array
     */
    public function getProjectTasks($projectId, $options = []) {
        $where = ['project_id' => $projectId];
        
        // Apply filters
        if (isset($options['status']) && !empty($options['status'])) {
            $where['status'] = $options['status'];
        }
        
        if (isset($options['assigned_to']) && !empty($options['assigned_to'])) {
            $where['assigned_to'] = $options['assigned_to'];
        }
        
        // Determine sorting
        $orderBy = isset($options['sort']) ? $options['sort'] : [
            'priority' => 'DESC',
            'due_date' => 'ASC'
        ];
        
        // Get tasks
        $tasks = $this->db->select('project_tasks', [
            '[>]users' => ['assigned_to' => 'id']
        ], [
            'project_tasks.id',
            'project_tasks.title',
            'project_tasks.description',
            'project_tasks.status',
            'project_tasks.priority',
            'project_tasks.due_date',
            'project_tasks.assigned_to',
            'project_tasks.created_at',
            'users.first_name',
            'users.last_name'
        ], [
            'AND' => $where,
            'ORDER' => $orderBy
        ]);
        
        return $tasks;
    }
    
    /**
     * Add project task
     * @param array $taskData Task data
     * @return int|bool Task ID or false on failure
     */
    public function addProjectTask($taskData) {
        $taskData['created_at'] = date('Y-m-d H:i:s');
        $taskData['updated_at'] = date('Y-m-d H:i:s');
        
        return $this->db->insert('project_tasks', $taskData);
    }
    
    /**
     * Update project task
     * @param int $taskId Task ID
     * @param array $taskData Task data
     * @return bool Success/failure
     */
    public function updateProjectTask($taskId, $taskData) {
        $taskData['updated_at'] = date('Y-m-d H:i:s');
        $result = $this->db->update('project_tasks', $taskData, ['id' => $taskId]);
        return $result > 0;
    }
    
    /**
     * Delete project task
     * @param int $taskId Task ID
     * @return bool Success/failure
     */
    public function deleteProjectTask($taskId) {
        $result = $this->db->delete('project_tasks', ['id' => $taskId]);
        return $result > 0;
    }
    
    /**
     * Get project documents
     * @param int $projectId Project ID
     * @return array
     */
    public function getProjectDocuments($projectId) {
        return $this->db->select('project_documents', [
            '[>]users' => ['uploaded_by' => 'id']
        ], [
            'project_documents.id',
            'project_documents.filename',
            'project_documents.file_path',
            'project_documents.file_size',
            'project_documents.file_type',
            'project_documents.description',
            'project_documents.uploaded_by',
            'project_documents.uploaded_at',
            'users.first_name',
            'users.last_name'
        ], [
            'project_documents.project_id' => $projectId,
            'ORDER' => ['project_documents.uploaded_at' => 'DESC']
        ]);
    }
    
    /**
     * Add project document
     * @param array $documentData Document data
     * @return int|bool Document ID or false on failure
     */
    public function addProjectDocument($documentData) {
        $documentData['uploaded_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('project_documents', $documentData);
    }
    
    /**
     * Delete project document
     * @param int $documentId Document ID
     * @return bool Success/failure
     */
    public function deleteProjectDocument($documentId) {
        // First get the document to delete file
        $document = $this->db->get('project_documents', ['file_path'], ['id' => $documentId]);
        
        if ($document && !empty($document['file_path'])) {
            $filePath = STORAGE_PATH . '/' . $document['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        
        $result = $this->db->delete('project_documents', ['id' => $documentId]);
        return $result > 0;
    }
} 