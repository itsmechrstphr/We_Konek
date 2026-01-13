<?php
/**
 * We Konek - Header Component
 * Reusable header include for all dashboard pages
 * 
 * Path: includes/header.php
 * 
 * IMPORTANT: This is an include file, not a standalone page.
 * Do not add <html>, <head>, or <body> tags here.
 * 
 * Required variables from parent page:
 * - $userName (string): Full name of the logged-in user
 * - $userRole (string): User role (super_admin, admin, faculty, student)
 * - $roleDisplay (string): Formatted role name for display
 * 
 * Required files to be included before this:
 * - includes/config.php (for shared functions)
 */

// Ensure this file is not accessed directly
if (!defined('HEADER_INCLUDED')) {
    define('HEADER_INCLUDED', true);
}

// Ensure config is loaded
if (!defined('CONFIG_INCLUDED')) {
    require_once __DIR__ . '../../config/config.php';
}

// Get user initials from centralized function
$userInitials = getInitials($userName);

// Get first name for greeting
$firstName = getFirstName($userName);
?>

<header class="site-header">
    <div class="header-container">
        <div class="header-left">
            <div class="welcome-greeting">
                <h2>Welcome, <?php echo htmlspecialchars($firstName); ?>!</h2>
            </div>
        </div>

        <div class="header-right">
            <div class="user-profile">
                <div class="profile-avatar" role="button" tabindex="0" aria-label="User profile menu">
                    <span class="avatar-text"><?php echo htmlspecialchars($userInitials); ?></span>
                </div>
                <div class="profile-info">
                    <h3 class="profile-name"><?php echo htmlspecialchars($userName); ?></h3>
                    <p class="profile-role"><?php echo htmlspecialchars($roleDisplay); ?></p>
                </div>
                <div class="user-actions">
                    <button class="profile-btn" aria-label="Toggle profile menu" aria-expanded="false">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu" role="menu" aria-label="User menu">
                        <a href="#profile" class="dropdown-item" role="menuitem">
                            <i class="fas fa-user"></i> Profile
                        </a>
                        <a href="#settings" class="dropdown-item" role="menuitem">
                            <i class="fas fa-cog"></i> Settings
                        </a>
                        <a href="../../auth/logout.php" class="dropdown-item" role="menuitem">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>