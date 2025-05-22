<?php
/**
 * Script to add department_id column to projects table
 */

// Load configuration
require_once __DIR__ . '/app/config/config.php';
require_once APP_PATH . '/core/Database.php';

// Display all errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get database instance
$db = Database::getInstance();

// Add department_id column to projects table
try {
    echo "Adding department_id column to projects table...\n";
    
    // First check if the column already exists
    $columns = $db->query("SHOW COLUMNS FROM projects LIKE 'department_id'");
    if (!empty($columns)) {
        echo "Column department_id already exists.\n";
        exit;
    }
    
    // Add the column
    $sql = "ALTER TABLE projects ADD COLUMN department_id INT(11) AFTER organization_id";
    $db->query($sql);
    
    // Add foreign key constraint
    $sql = "ALTER TABLE projects ADD CONSTRAINT fk_projects_department 
            FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE";
    $db->query($sql);
    
    echo "Department_id column added successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 