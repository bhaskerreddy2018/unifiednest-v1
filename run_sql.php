<?php
/**
 * Script to run the SQL statements from projects_tables.sql
 */

// Load configuration
require_once __DIR__ . '/app/config/config.php';
require_once APP_PATH . '/core/Database.php';

// Read the SQL file
$sql = file_get_contents(__DIR__ . '/db/projects_tables.sql');

// Split into individual statements
$statements = array_filter(
    array_map('trim', explode(';', $sql)),
    function($statement) {
        return !empty($statement);
    }
);

// Get database instance
$db = Database::getInstance();

// Execute each statement
echo "Running SQL statements...\n";
foreach ($statements as $statement) {
    try {
        // Use the query method for executing raw SQL
        $db->query($statement);
        echo "Success: " . substr($statement, 0, 50) . "...\n";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage() . "\n";
        echo "Statement: " . $statement . "\n";
    }
}

echo "SQL execution completed!\n"; 