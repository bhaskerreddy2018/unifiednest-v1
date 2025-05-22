<?php
/**
 * Script to check if the project tables exist
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

// Check tables
$tables = ['projects', 'project_members', 'project_tasks', 'project_documents'];

foreach ($tables as $table) {
    echo "Checking table: {$table}\n";
    try {
        $result = $db->query("SHOW COLUMNS FROM {$table}");
        if (empty($result)) {
            echo "  Table {$table} exists but has no columns.\n\n";
            continue;
        }
        
        echo "  Table {$table} exists with columns:\n";
        foreach ($result as $column) {
            echo "  - {$column['Field']} ({$column['Type']})\n";
        }
        echo "\n";
    } catch (Exception $e) {
        echo "  Error: " . $e->getMessage() . "\n\n";
    }
}

echo "Check completed!\n"; 