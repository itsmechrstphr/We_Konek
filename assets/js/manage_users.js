// Manage Users JavaScript
let currentUsers = [];
let isEditMode = false;

// DOM Elements
const modal = document.getElementById('userModal');
const addUserBtn = document.getElementById('addUserBtn');
const cancelBtn = document.getElementById('cancelBtn');
const closeBtn = document.querySelector('.close');
const userForm = document.getElementById('userForm');
const usersTableBody = document.getElementById('usersTableBody');
const searchInput = document.getElementById('searchInput');
const roleFilter = document.getElementById('roleFilter');
const modalTitle = document.getElementById('modalTitle');
const passwordInput = document.getElementById('userPassword');
const passwordHint = document.getElementById('passwordHint');
const statusGroup = document.getElementById('statusGroup');

// Event Listeners
addUserBtn.addEventListener('click', openAddModal);
cancelBtn.addEventListener('click', closeModal);
closeBtn.addEventListener('click', closeModal);
userForm.addEventListener('submit', handleSubmit);
searchInput.addEventListener('input', debounce(fetchUsers, 300));
roleFilter.addEventListener('change', fetchUsers);

window.addEventListener('click', (e) => {
    if (e.target === modal) closeModal();
});

// Fetch Users
async function fetchUsers() {
    const search = searchInput.value;
    const role = roleFilter.value;
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (role) params.append('role', role);
    
    try {
        const response = await fetch(`../../api/users/fetch_users.php?${params}`);
        
        // Log response details
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        const text = await response.text();
        console.log('Raw response:', text);
        
        const data = JSON.parse(text);
        
        if (data.success) {
            currentUsers = data.data;
            renderUsers(data.data);
        } else {
            showError(data.error || 'Failed to fetch users');
        }
    } catch (error) {
        showError('Network error: ' + error.message);
        console.error('Full error:', error);
    }
}

// Render Users Table
function renderUsers(users) {
    if (users.length === 0) {
        usersTableBody.innerHTML = '<tr><td colspan="7" class="text-center">No users found</td></tr>';
        return;
    }
    
    usersTableBody.innerHTML = users.map(user => `
        <tr>
            <td class="user-id-cell">${escapeHtml(user.user_id || 'N/A')}</td>
            <td>${escapeHtml(user.name)}</td>
            <td>${escapeHtml(user.email)}</td>
            <td><span class="role-badge role-${user.role}">${user.role}</span></td>
            <td><span class="status-badge status-${user.status}">${user.status}</span></td>
            <td>${formatDate(user.created_at)}</td>
            <td class="actions-cell">
                ${userPermissions.canEdit ? 
                    `<button class="action-btn-edit" onclick="openEditModal(${user.id})" title="Edit">
                        <i class="fas fa-pen"></i>
                    </button>` 
                    : ''}
                ${userPermissions.canDelete ? 
                    `<button class="action-btn-delete" onclick="deleteUser(${user.id})" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>` 
                    : ''}
                ${!userPermissions.canEdit && !userPermissions.canDelete ? 
                    '<span class="text-muted">View only</span>' 
                    : ''}
            </td>
        </tr>
    `).join('');
}

// Open Add Modal
function openAddModal() {
    isEditMode = false;
    modalTitle.textContent = 'Add User';
    userForm.reset();
    document.getElementById('userId').value = '';
    passwordInput.required = true;
    passwordHint.textContent = '*';
    statusGroup.style.display = 'none';
    modal.style.display = 'block';
}

// Open Edit Modal
function openEditModal(userId) {
    if (!userPermissions.canEdit) {
        alert('You do not have permission to edit users');
        return;
    }
    
    const user = currentUsers.find(u => u.id === userId);
    if (!user) return;
    
    isEditMode = true;
    modalTitle.textContent = 'Edit User';
    
    document.getElementById('userId').value = user.id;
    document.getElementById('userName').value = user.name;
    document.getElementById('userEmail').value = user.email;
    document.getElementById('userRole').value = user.role;
    document.getElementById('userStatus').value = user.status || 'active';
    
    passwordInput.value = '';
    passwordInput.required = false;
    passwordHint.textContent = '(leave blank to keep current)';
    statusGroup.style.display = 'block';
    
    modal.style.display = 'block';
}

// Close Modal
function closeModal() {
    modal.style.display = 'none';
    userForm.reset();
}

// Handle Form Submit
async function handleSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(userForm);
    const data = Object.fromEntries(formData.entries());
    
    // Remove empty password for edit
    if (isEditMode && !data.password) {
        delete data.password;
    }
    
    const endpoint = isEditMode ? 'update_user.php' : 'create_user.php';
    
    try {
        const response = await fetch(`../../api/users/${endpoint}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccess(result.message);
            closeModal();
            fetchUsers();
        } else {
            showError(result.error || 'Operation failed');
        }
    } catch (error) {
        showError('Network error');
        console.error(error);
    }
}

// Delete User
async function deleteUser(userId) {
    if (!userPermissions.canDelete) {
        alert('You do not have permission to delete users');
        return;
    }
    
    if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        return;
    }
    
    try {
        const response = await fetch('../../api/users/delete_user.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: userId })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccess(result.message);
            fetchUsers();
        } else {
            showError(result.error || 'Delete failed');
        }
    } catch (error) {
        showError('Network error');
        console.error(error);
    }
}

// Utility Functions
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
}

function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

function showSuccess(message) {
    alert(message); // Replace with your existing notification system
}

function showError(message) {
    alert('Error: ' + message); // Replace with your existing notification system
}

// Initial load
fetchUsers();