<?php
/**
 * Script to update project_documents table to match the ProjectModel
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

// Update project_documents table
try {
    echo "Updating project_documents table...\n";
    
    // Check if columns need to be updated
    $columns = $db->query("SHOW COLUMNS FROM project_documents");
    $columnNames = array_column($columns, 'Field');
    
    // Check and rename user_id to uploaded_by if needed
    if (in_array('user_id', $columnNames) && !in_array('uploaded_by', $columnNames)) {
        echo "Renaming user_id to uploaded_by...\n";
        $db->query("ALTER TABLE project_documents CHANGE COLUMN user_id uploaded_by INT(11)");
    }
    
    // Check and rename title to filename if needed
    if (in_array('title', $columnNames) && !in_array('filename', $columnNames)) {
        echo "Renaming title to filename...\n";
        $db->query("ALTER TABLE project_documents CHANGE COLUMN title filename VARCHAR(255)");
    }
    
    // Add description column if it doesn't exist
    if (!in_array('description', $columnNames)) {
        echo "Adding description column...\n";
        $db->query("ALTER TABLE project_documents ADD COLUMN description TEXT AFTER file_size");
    }
    
    // Rename timestamp columns if needed
    if (in_array('created_at', $columnNames) && !in_array('uploaded_at', $columnNames)) {
        echo "Renaming created_at to uploaded_at...\n";
        $db->query("ALTER TABLE project_documents CHANGE COLUMN created_at uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP");
    }
    
    // Drop updated_at if it exists (not used in our model)
    if (in_array('updated_at', $columnNames)) {
        echo "Dropping updated_at column...\n";
        $db->query("ALTER TABLE project_documents DROP COLUMN updated_at");
    }
    
    echo "project_documents table updated successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 