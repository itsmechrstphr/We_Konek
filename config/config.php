<?php
/**
 * We Konek - Centralized Configuration
 * Single source of truth for shared functions and variables
 * 
 * Path: config/config.php
 */

// Prevent direct access
if (!defined('CONFIG_INCLUDED')) {
    define('CONFIG_INCLUDED', true);
}

// ==========================================
// DATABASE CONFIGURATION
// ==========================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'we_konek');  // CHANGE THIS to your database name
define('DB_USER', 'root');       // CHANGE THIS to your MySQL username
define('DB_PASS', '');           // CHANGE THIS to your MySQL password (empty for XAMPP default)

// Create PDO Database Connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    // Log error (in production, don't expose details)
    error_log("Database connection failed: " . $e->getMessage());
    
    // Development error message
    if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE === true) {
        die("Database Error: " . $e->getMessage());
    } else {
        die("Database connection failed. Please contact the administrator.");
    }
}

// ==========================================
// HELPER FUNCTIONS
// ==========================================

/**
 * Get user initials from full name
 * Centralized function to avoid duplication
 * 
 * @param string $name - Full name
 * @return string - Initials (max 2 characters)
 */
function getInitials($name) {
    $words = explode(' ', trim($name));
    if (count($words) >= 2) {
        return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
    }
    return strtoupper(substr($name, 0, 2));
}

/**
 * Get first name from full name
 * 
 * @param string $name - Full name
 * @return string - First name
 */
function getFirstName($name) {
    return explode(' ', trim($name))[0];
}

/**
 * Role display names mapping
 * 
 * @return array - Role name mappings
 */
function getRoleNames() {
    return [
        'super_admin' => 'Super Admin',
        'admin' => 'Admin',
        'faculty' => 'Faculty',
        'student' => 'Student',
    ];
}

/**
 * Get formatted role display name
 * 
 * @param string $role - Role key
 * @return string - Formatted role name
 */
function getRoleDisplay($role) {
    $roleNames = getRoleNames();
    return isset($roleNames[$role]) ? $roleNames[$role] : 'User';
}
?>