<?php
/**
 * OrganizationModel Class
 * Handles organization related operations
 */

class OrganizationModel {
    private $db;

    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Get organization by ID
     * @param int $id Organization ID
     * @return array|bool Organization data or false if not found
     */
    public function getOrganizationById($id) {
        return $this->db->get('organizations', '*', ['id' => $id]);
    }

    /**
     * Create a new organization
     * @param array $organizationData Organization data
     * @return int|bool Organization ID or false if creation fails
     */
    public function createOrganization($organizationData) {
        $organizationData['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('organizations', $organizationData);
    }

    /**
     * Update organization
     * @param int $id Organization ID
     * @param array $organizationData Organization data
     * @return bool
     */
    public function updateOrganization($id, $organizationData) {
        $organizationData['updated_at'] = date('Y-m-d H:i:s');
        $result = $this->db->update('organizations', $organizationData, ['id' => $id]);
        return $result > 0;
    }

    /**
     * Get departments by organization ID
     * @param int $organizationId Organization ID
     * @return array
     */
    public function getDepartmentsByOrganizationId($organizationId) {
        return $this->db->select('departments', '*', [
            'organization_id' => $organizationId,
            'ORDER' => ['name' => 'ASC']
        ]);
    }

    /**
     * Get department by ID
     * @param int $id Department ID
     * @return array|bool
     */
    public function getDepartmentById($id) {
        return $this->db->get('departments', '*', ['id' => $id]);
    }

    /**
     * Create a new department
     * @param array $departmentData Department data
     * @return int|bool Department ID or false if creation fails
     */
    public function createDepartment($departmentData) {
        $departmentData['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('departments', $departmentData);
    }

    /**
     * Update department
     * @param int $id Department ID
     * @param array $departmentData Department data
     * @return bool
     */
    public function updateDepartment($id, $departmentData) {
        $departmentData['updated_at'] = date('Y-m-d H:i:s');
        $result = $this->db->update('departments', $departmentData, ['id' => $id]);
        return $result > 0;
    }

    /**
     * Delete department
     * @param int $id Department ID
     * @return bool
     */
    public function deleteDepartment($id) {
        // Check if department has users
        $userCount = $this->db->count('users', ['department_id' => $id]);
        if ($userCount > 0) {
            return false; // Can't delete department with users
        }

        $result = $this->db->delete('departments', ['id' => $id]);
        return $result > 0;
    }

    /**
     * Get all roles
     * @return array
     */
    public function getAllRoles() {
        return $this->db->select('roles', '*', ['ORDER' => ['id' => 'ASC']]);
    }

    /**
     * Get role by ID
     * @param int $id Role ID
     * @return array|bool
     */
    public function getRoleById($id) {
        return $this->db->get('roles', '*', ['id' => $id]);
    }

    /**
     * Get role by name
     * @param string $name Role name
     * @return array|bool
     */
    public function getRoleByName($name) {
        return $this->db->get('roles', '*', ['name' => $name]);
    }

    /**
     * Count departments in an organization
     * @param int $organizationId Organization ID
     * @return int
     */
    public function countDepartments($organizationId) {
        return $this->db->count('departments', ['organization_id' => $organizationId]);
    }

    /**
     * Get all organizations
     * @return array
     */
    public function getAllOrganizations() {
        return $this->db->select('organizations', '*', ['ORDER' => ['name' => 'ASC']]);
    }

    /**
     * Count users in an organization
     * @param int $organizationId Organization ID
     * @return int
     */
    public function countOrganizationUsers($organizationId) {
        return $this->db->count('users', ['organization_id' => $organizationId]);
    }

    /**
     * Delete organization
     * @param int $id Organization ID
     * @return bool
     */
    public function deleteOrganization($id) {
        // First delete all departments in the organization
        $this->db->delete('departments', ['organization_id' => $id]);
        
        // Then delete the organization
        return $this->db->delete('organizations', ['id' => $id]) > 0;
    }
} 