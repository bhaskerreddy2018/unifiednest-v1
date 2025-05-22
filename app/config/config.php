<?php
/**
 * Main configuration file
 */

// Error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Base paths
define('BASE_PATH', dirname(dirname(__DIR__)));
define('APP_PATH', BASE_PATH . '/app');
define('STORAGE_PATH', __DIR__ . '/../../storage');
define('VIEW_PATH', APP_PATH . '/views');

// URL Settings
define('BASE_URL', 'http://localhost/unifiednest-v1'); // Change this to your actual URL

// Database settings
define('DB_HOST', 'localhost');
define('DB_NAME', 'unifiednest_v1');
define('DB_USER', 'root');  // Update with actual database user
define('DB_PASS', '');      // Update with actual database password
define('DB_CHARSET', 'utf8mb4');

// Session settings
define('SESSION_NAME', 'unifiednest_session');
define('SESSION_LIFETIME', 7200); // 2 hours
define('SESSION_PATH', '/');
define('SESSION_DOMAIN', '');
define('SESSION_SECURE', false); // Set to true in production with HTTPS
define('SESSION_HTTPONLY', true);

// Security settings
define('SALT', 'OBq&w8Ry6R1!DU4WKo$j'); // Change this to a random string for security
define('PASSWORD_HASH_ALGO', PASSWORD_DEFAULT);
define('PASSWORD_HASH_OPTIONS', ['cost' => 12]);

// Pagination settings
define('ITEMS_PER_PAGE', 10);

// File upload settings
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,zip,rar');

// Date and time settings
define('DEFAULT_TIMEZONE', 'UTC');
date_default_timezone_set(DEFAULT_TIMEZONE);

// Application settings
define('APP_NAME', 'UnifiedNest ERP');
define('APP_VERSION', '1.0.0'); 