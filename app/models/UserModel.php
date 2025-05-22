<?php
/**
 * UserModel Class
 * Handles user related operations
 */

class UserModel {
    private $db;

    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Authenticate a user
     * @param string $email User email
     * @param string $password User password
     * @return array|bool User data or false if authentication fails
     */
    public function authenticate($email, $password) {
        // Get user by email
        $user = $this->db->get('users', '*', ['email' => $email]);
        
        // Check if user exists
        if (!$user) {
            return false;
        }
        
        // Check if user is active
        if (!$user['is_active']) {
            return false;
        }
        
        // Verify password
        if (!password_verify($password, $user['password'])) {
            return false;
        }
        
        // Update last login timestamp
        $this->db->update('users', ['last_login' => date('Y-m-d H:i:s')], ['id' => $user['id']]);
        
        return $user;
    }

    /**
     * Get user by ID
     * @param int $id User ID
     * @return array|bool User data or false if user doesn't exist
     */
    public function getUserById($id) {
        return $this->db->get('users', '*', ['id' => $id]);
    }

    /**
     * Check if email already exists
     * @param string $email Email to check
     * @param int $exceptUserId User ID to exclude from check (for updates)
     * @return bool
     */
    public function emailExists($email, $exceptUserId = null) {
        $where = ['email' => $email];
        
        if ($exceptUserId) {
            $where['id[!]'] = $exceptUserId;
        }
        
        return $this->db->has('users', $where);
    }

    /**
     * Register a new user
     * @param array $userData User data
     * @return int|bool User ID or false if registration fails
     */
    public function register($userData) {
        // Check if email already exists
        if ($this->emailExists($userData['email'])) {
            return false;
        }
        
        // Hash password
        $userData['password'] = password_hash($userData['password'], PASSWORD_HASH_ALGO, PASSWORD_HASH_OPTIONS);
        
        // Set defaults
        $userData['is_active'] = true;
        $userData['onboarding_completed'] = false;
        $userData['created_at'] = date('Y-m-d H:i:s');
        
        // Insert user
        $userId = $this->db->insert('users', $userData);
        
        return $userId;
    }

    /**
     * Update user profile
     * @param int $userId User ID
     * @param array $userData User data
     * @return bool
     */
    public function updateProfile($userId, $userData) {
        // Check if we're updating email and if it already exists
        if (isset($userData['email']) && $this->emailExists($userData['email'], $userId)) {
            return false;
        }
        
        // If password is being updated, hash it
        if (isset($userData['password']) && !empty($userData['password'])) {
            $userData['password'] = password_hash($userData['password'], PASSWORD_HASH_ALGO, PASSWORD_HASH_OPTIONS);
        } else {
            // Don't update password if empty
            unset($userData['password']);
        }
        
        // Set updated timestamp
        $userData['updated_at'] = date('Y-m-d H:i:s');
        
        // Update user
        $result = $this->db->update('users', $userData, ['id' => $userId]);
        
        return $result > 0;
    }

    /**
     * Complete user onboarding
     * @param int $userId User ID
     * @return bool
     */
    public function completeOnboarding($userId) {
        return $this->db->update('users', ['onboarding_completed' => true], ['id' => $userId]) > 0;
    }

    /**
     * Get user role
     * @param int $userId User ID
     * @return string Role name
     */
    public function getUserRole($userId) {
        $user = $this->db->get('users', ['role_id'], ['id' => $userId]);
        
        if (!$user) {
            return null;
        }
        
        $role = $this->db->get('roles', ['name'], ['id' => $user['role_id']]);
        
        return $role ? $role['name'] : null;
    }

    /**
     * Check if a user is the first registered user
     * @return bool
     */
    public function isFirstUser() {
        return $this->db->count('users') === 0;
    }
    
    /**
     * Get all users in an organization
     * @param int $organizationId Organization ID
     * @param array $options Additional options like sort, limit, etc.
     * @return array
     */
    public function getUsersByOrganization($organizationId, $options = []) {
        $where = ['organization_id' => $organizationId];
        
        // Add additional filters if specified
        if (isset($options['role_id'])) {
            $where['role_id'] = $options['role_id'];
        }
        
        if (isset($options['department_id'])) {
            $where['department_id'] = $options['department_id'];
        }
        
        if (isset($options['search'])) {
            $search = $options['search'];
            $where['OR'] = [
                'first_name[~]' => $search,
                'last_name[~]' => $search,
                'email[~]' => $search
            ];
        }
        
        // Determine sorting
        $orderBy = isset($options['sort']) ? $options['sort'] : ['id' => 'ASC'];
        
        // Determine pagination
        $limit = isset($options['limit']) ? $options['limit'] : null;
        $offset = isset($options['offset']) ? $options['offset'] : null;
        
        // Get users
        $users = $this->db->select('users', [
            'id',
            'organization_id',
            'department_id',
            'role_id',
            'email',
            'first_name',
            'last_name',
            'phone',
            'profile_image',
            'is_active',
            'onboarding_completed',
            'last_login',
            'created_at',
            'updated_at'
        ], [
            'AND' => $where,
            'ORDER' => $orderBy,
            'LIMIT' => [$offset, $limit]
        ]);
        
        return $users;
    }
    
    /**
     * Get user profile details
     * @param int $userId User ID
     * @return array
     */
    public function getUserProfile($userId) {
        $profile = $this->db->get('user_profiles', '*', ['user_id' => $userId]);
        return $profile ?: [];
    }
    
    /**
     * Save user profile details
     * @param int $userId User ID
     * @param array $profileData Profile data
     * @return bool
     */
    public function saveUserProfile($userId, $profileData) {
        // Check if profile already exists
        $exists = $this->db->has('user_profiles', ['user_id' => $userId]);
        
        if ($exists) {
            // Update existing profile
            $result = $this->db->update('user_profiles', $profileData, ['user_id' => $userId]);
            return $result > 0;
        } else {
            // Create new profile
            $profileData['user_id'] = $userId;
            $result = $this->db->insert('user_profiles', $profileData);
            return $result > 0;
        }
    }
    
    /**
     * Get user family details
     * @param int $userId User ID
     * @return array
     */
    public function getUserFamily($userId) {
        return $this->db->select('user_family', '*', ['user_id' => $userId]);
    }
    
    /**
     * Add a family member
     * @param int $userId User ID
     * @param array $familyData Family member data
     * @return int|bool
     */
    public function addFamilyMember($userId, $familyData) {
        $familyData['user_id'] = $userId;
        return $this->db->insert('user_family', $familyData);
    }
    
    /**
     * Update a family member
     * @param int $id Family member ID
     * @param array $familyData Family member data
     * @return bool
     */
    public function updateFamilyMember($id, $familyData) {
        $result = $this->db->update('user_family', $familyData, ['id' => $id]);
        return $result > 0;
    }
    
    /**
     * Delete a family member
     * @param int $id Family member ID
     * @return bool
     */
    public function deleteFamilyMember($id) {
        $result = $this->db->delete('user_family', ['id' => $id]);
        return $result > 0;
    }
    
    /**
     * Get user education details
     * @param int $userId User ID
     * @return array
     */
    public function getUserEducation($userId) {
        return $this->db->select('user_education', '*', ['user_id' => $userId]);
    }
    
    /**
     * Add an education record
     * @param int $userId User ID
     * @param array $educationData Education data
     * @return int|bool
     */
    public function addEducation($userId, $educationData) {
        $educationData['user_id'] = $userId;
        return $this->db->insert('user_education', $educationData);
    }
    
    /**
     * Update an education record
     * @param int $id Education ID
     * @param array $educationData Education data
     * @return bool
     */
    public function updateEducation($id, $educationData) {
        $result = $this->db->update('user_education', $educationData, ['id' => $id]);
        return $result > 0;
    }
    
    /**
     * Delete an education record
     * @param int $id Education ID
     * @return bool
     */
    public function deleteEducation($id) {
        $result = $this->db->delete('user_education', ['id' => $id]);
        return $result > 0;
    }
    
    /**
     * Get user experience details
     * @param int $userId User ID
     * @return array
     */
    public function getUserExperience($userId) {
        return $this->db->select('user_experience', '*', ['user_id' => $userId]);
    }
    
    /**
     * Add an experience record
     * @param int $userId User ID
     * @param array $experienceData Experience data
     * @return int|bool
     */
    public function addExperience($userId, $experienceData) {
        $experienceData['user_id'] = $userId;
        return $this->db->insert('user_experience', $experienceData);
    }
    
    /**
     * Update an experience record
     * @param int $id Experience ID
     * @param array $experienceData Experience data
     * @return bool
     */
    public function updateExperience($id, $experienceData) {
        $result = $this->db->update('user_experience', $experienceData, ['id' => $id]);
        return $result > 0;
    }
    
    /**
     * Delete an experience record
     * @param int $id Experience ID
     * @return bool
     */
    public function deleteExperience($id) {
        $result = $this->db->delete('user_experience', ['id' => $id]);
        return $result > 0;
    }
    
    /**
     * Get user addresses
     * @param int $userId User ID
     * @return array
     */
    public function getUserAddresses($userId) {
        return $this->db->select('user_addresses', '*', ['user_id' => $userId]);
    }
    
    /**
     * Add an address
     * @param int $userId User ID
     * @param array $addressData Address data
     * @return int|bool
     */
    public function addAddress($userId, $addressData) {
        $addressData['user_id'] = $userId;
        return $this->db->insert('user_addresses', $addressData);
    }
    
    /**
     * Update an address
     * @param int $id Address ID
     * @param array $addressData Address data
     * @return bool
     */
    public function updateAddress($id, $addressData) {
        $result = $this->db->update('user_addresses', $addressData, ['id' => $id]);
        return $result > 0;
    }
    
    /**
     * Delete an address
     * @param int $id Address ID
     * @return bool
     */
    public function deleteAddress($id) {
        $result = $this->db->delete('user_addresses', ['id' => $id]);
        return $result > 0;
    }
    
    /**
     * Get user health information
     * @param int $userId User ID
     * @return array
     */
    public function getUserHealth($userId) {
        return $this->db->get('user_health', '*', ['user_id' => $userId]) ?: [];
    }
    
    /**
     * Save user health information
     * @param int $userId User ID
     * @param array $healthData Health data
     * @return bool
     */
    public function saveUserHealth($userId, $healthData) {
        // Check if health record already exists
        $exists = $this->db->has('user_health', ['user_id' => $userId]);
        
        if ($exists) {
            // Update existing health record
            $result = $this->db->update('user_health', $healthData, ['user_id' => $userId]);
            return $result > 0;
        } else {
            // Create new health record
            $healthData['user_id'] = $userId;
            $result = $this->db->insert('user_health', $healthData);
            return $result > 0;
        }
    }

    /**
     * Count users by organization
     * @param int $organizationId Organization ID
     * @return int
     */
    public function countUsersByOrganization($organizationId) {
        return $this->db->count('users', ['organization_id' => $organizationId]);
    }

    /**
     * Count users by department
     * @param int $departmentId Department ID
     * @return int
     */
    public function countUsersByDepartment($departmentId) {
        return $this->db->count('users', ['department_id' => $departmentId]);
    }
} 