<?php
/**
 * We Konek - Centralized Configuration
 * Single source of truth for shared functions and variables
 * 
 * Path: includes/config.php
 */

// Prevent direct access
if (!defined('CONFIG_INCLUDED')) {
    define('CONFIG_INCLUDED', true);
}

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