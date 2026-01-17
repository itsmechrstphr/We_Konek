<?php
/**
 * We Konek - Sidebar Component
 * Reusable sidebar navigation include for all dashboard pages
 * 
 * Path: includes/sidebar.php
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
if (!defined('SIDEBAR_INCLUDED')) {
    define('SIDEBAR_INCLUDED', true);
}

// Ensure config is loaded
if (!defined('CONFIG_INCLUDED')) {
    require_once __DIR__ . '../../config/config.php';
}

// Get user initials from centralized function
$userInitials = getInitials($userName);

// Define navigation menu items based on user role
$navItems = [];

switch ($userRole) {
    case 'super_admin':
        $navItems = [
            ['icon' => 'fa-home', 'text' => 'Dashboard', 'href' => 'super_admin_dashboard.php'],
            ['icon' => 'fa-users-cog', 'text' => 'Manage Users', 'href' => 'manage_users.php'],
            ['icon' => 'fa-check-circle', 'text' => 'Approvals', 'href' => 'approvals.php'],
            ['icon' => 'fa-chalkboard-teacher', 'text' => 'Faculty', 'href' => 'faculty.php'],
            ['icon' => 'fa-calendar-alt', 'text' => 'Schedules', 'href' => 'schedules.php'],
            ['icon' => 'fa-book', 'text' => 'Subjects', 'href' => 'subjects.php'],
            ['icon' => 'fa-chart-bar', 'text' => 'Reports', 'href' => 'reports.php'],
            ['icon' => 'fa-bullhorn', 'text' => 'Announcements', 'href' => 'announcements.php'],
            ['icon' => 'fa-cog', 'text' => 'Settings', 'href' => 'settings.php'],
        ];
        break;
    
    case 'admin':
        $navItems = [
            ['icon' => 'fa-home', 'text' => 'Dashboard', 'href' => 'admin_dashboard.php'],
            ['icon' => 'fa-users-cog', 'text' => 'Manage Users', 'href' => 'manage_users.php'],
            ['icon' => 'fa-graduation-cap', 'text' => 'Students', 'href' => 'students.php'],
            ['icon' => 'fa-calendar-alt', 'text' => 'Schedules', 'href' => 'schedules.php'],
            ['icon' => 'fa-chart-bar', 'text' => 'Reports', 'href' => 'reports.php'],
            ['icon' => 'fa-bullhorn', 'text' => 'Announcements', 'href' => 'announcements.php'],
        ];
        break;
    
    case 'faculty':
        $navItems = [
            ['icon' => 'fa-home', 'text' => 'Dashboard', 'href' => 'faculty_dashboard.php'],
            ['icon' => 'fa-users', 'text' => 'My Classes', 'href' => 'my_classes.php'],
            ['icon' => 'fa-clipboard-list', 'text' => 'Attendance', 'href' => 'attendance.php'],
            ['icon' => 'fa-star', 'text' => 'Grades', 'href' => 'grades.php'],
            ['icon' => 'fa-calendar', 'text' => 'Schedule', 'href' => 'schedule.php'],
        ];
        break;
    
    case 'student':
    default:
        $navItems = [
            ['icon' => 'fa-home', 'text' => 'Dashboard', 'href' => 'student_dashboard.php'],
            ['icon' => 'fa-book', 'text' => 'My Subjects', 'href' => 'my_subjects.php'],
            ['icon' => 'fa-star', 'text' => 'Grades', 'href' => 'my_grades.php'],
            ['icon' => 'fa-calendar', 'text' => 'Schedule', 'href' => 'my_schedule.php'],
            ['icon' => 'fa-bullhorn', 'text' => 'Announcements', 'href' => 'announcements.php'],
        ];
        break;
}
?>

<!-- Sidebar Overlay (Mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar Toggle Button (Mobile) -->
<button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
    <span></span>
    <span></span>
    <span></span>
</button>

<!-- Sidebar Container -->
<aside class="sidebar" id="sidebar" role="navigation" aria-label="Main navigation">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <a href="<?php echo $navItems[0]['href']; ?>" class="sidebar-logo">
            <div class="sidebar-logo-img"></div>
        </a>
        <button class="sidebar-close" id="sidebarClose" aria-label="Close sidebar">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- User Profile Section -->
    <div class="sidebar-profile">
        <div class="profile-avatar">
            <span class="avatar-text"><?php echo htmlspecialchars($userInitials); ?></span>
        </div>
        <div class="profile-info">
            <h3 class="profile-name"><?php echo htmlspecialchars($userName); ?></h3>
            <p class="profile-role"><?php echo htmlspecialchars($roleDisplay); ?></p>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="sidebar-nav">
        <ul class="nav-list">
            <?php foreach ($navItems as $item): ?>
            <li class="nav-item">
                <a href="<?php echo htmlspecialchars($item['href']); ?>" 
                   class="nav-link" 
                   title="<?php echo htmlspecialchars($item['text']); ?>">
                    <span class="nav-icon">
                        <i class="fas <?php echo htmlspecialchars($item['icon']); ?>"></i>
                    </span>
                    <span class="nav-text"><?php echo htmlspecialchars($item['text']); ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <a href="../../auth/logout.php" class="logout-btn" title="Logout">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>