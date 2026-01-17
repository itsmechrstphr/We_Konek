<?php
session_start();
require_once '../../config/config.php';

// Auth check
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'super_admin'])) {
    header('Location: ../../auth/admin_login.php');
    exit;
}

$page_title = "Manage Users";
$current_role = $_SESSION['role'];
$can_edit = $current_role === 'super_admin';
$can_delete = $current_role === 'super_admin';

// Get user information from session for sidebar
$userRole = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : 'admin';
$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Admin User';

// Get role display name using centralized function
$roleDisplay = getRoleDisplay($userRole);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - WE_KONEK</title>
    <link rel="stylesheet" href="../../assets/css/variables.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/header.css">
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    
    <div class="dashboard-container">
        <?php include '../../includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="management-header">
                <h1 class="management-title">USER MANAGEMENT</h1>
                <div class="management-actions">
                    <button class="action-btn-search" id="searchToggle">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <button class="action-btn-filter" id="filterToggle">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <button class="action-btn-add" id="addUserBtn">
                        <i class="fas fa-plus"></i> Add User
                    </button>
                </div>
            </div>
            
            <div class="filters-panel" id="filtersPanel">
                <div class="filter-group">
                    <input type="text" id="searchInput" placeholder="Search by name or email..." class="search-input">
                    
                    <select id="roleFilter" class="filter-select">
                        <option value="">All Roles</option>
                        <option value="student">Students</option>
                        <option value="faculty">Faculty</option>
                    </select>
                </div>
            </div>
            
            <div class="staff-table-container">
                <table class="staff-table">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">
                        <tr>
                            <td colspan="7" class="text-center">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    
    <!-- Add/Edit User Modal -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add User</h2>
                <span class="close">&times;</span>
            </div>
            <form id="userForm">
                <input type="hidden" id="userId" name="id">
                
                <div class="form-group">
                    <label for="userName">Name *</label>
                    <input type="text" id="userName" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="userEmail">Email *</label>
                    <input type="email" id="userEmail" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="userPassword">Password <span id="passwordHint">*</span></label>
                    <input type="password" id="userPassword" name="password">
                </div>
                
                <div class="form-group">
                    <label for="userRole">Role *</label>
                    <select id="userRole" name="role" required>
                        <option value="">Select Role</option>
                        <option value="student">Student</option>
                        <option value="faculty">Faculty</option>
                    </select>
                </div>
                
                <div class="form-group" id="statusGroup" style="display: none;">
                    <label for="userStatus">Status</label>
                    <select id="userStatus" name="status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="../../assets/js/header.js"></script>
    <script src="../../assets/js/sidebar.js"></script>
    <script src="../../assets/js/dashboard.js"></script>
    <script>
        const userPermissions = {
            canEdit: <?php echo $can_edit ? 'true' : 'false'; ?>,
            canDelete: <?php echo $can_delete ? 'true' : 'false'; ?>
        };
        
        // Toggle filters panel
        document.getElementById('searchToggle').addEventListener('click', function() {
            document.getElementById('filtersPanel').classList.toggle('active');
            document.getElementById('searchInput').focus();
        });
        
        document.getElementById('filterToggle').addEventListener('click', function() {
            document.getElementById('filtersPanel').classList.toggle('active');
            document.getElementById('roleFilter').focus();
        });
    </script>
    <script src="../../assets/js/manage_users.js"></script>
</body>
</html>