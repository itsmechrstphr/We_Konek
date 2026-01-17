/**
 * WE_KONEK - Grade Management JavaScript
 * Path: assets/js/grades.js
 */

// DOM Elements
const uploadBtn = document.getElementById('uploadBtn');
const uploadModal = document.getElementById('uploadModal');
const viewGradesModal = document.getElementById('viewGradesModal');
const uploadForm = document.getElementById('uploadForm');
const fileInput = document.getElementById('gradeFile');
const fileUploadArea = document.getElementById('fileUploadArea');
const fileInfo = document.getElementById('fileInfo');
const uploadsList = document.getElementById('uploadsList');
const printBtn = document.getElementById('printBtn');

// Close buttons
const closeBtns = document.querySelectorAll('.close-grades');
const cancelUploadBtn = document.getElementById('cancelUploadBtn');

// Event Listeners
if (uploadBtn) {
    uploadBtn.addEventListener('click', openUploadModal);
}

closeBtns.forEach(btn => {
    btn.addEventListener('click', closeAllModals);
});

if (cancelUploadBtn) {
    cancelUploadBtn.addEventListener('click', closeAllModals);
}

if (uploadForm) {
    uploadForm.addEventListener('submit', handleUpload);
}

if (fileInput) {
    fileInput.addEventListener('change', handleFileSelect);
}

if (fileUploadArea) {
    fileUploadArea.addEventListener('dragover', handleDragOver);
    fileUploadArea.addEventListener('drop', handleDrop);
}

if (printBtn) {
    printBtn.addEventListener('click', () => window.print());
}

window.addEventListener('click', (e) => {
    if (e.target === uploadModal) closeAllModals();
    if (e.target === viewGradesModal) closeAllModals();
});

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    loadUploads();
});

// Functions
function openUploadModal() {
    uploadModal.style.display = 'block';
}

function closeAllModals() {
    uploadModal.style.display = 'none';
    viewGradesModal.style.display = 'none';
    uploadForm.reset();
    fileInfo.classList.remove('active');
    fileInfo.innerHTML = '';
}

function handleFileSelect(e) {
    const file = e.target.files[0];
    if (file) {
        displayFileInfo(file);
    }
}

function handleDragOver(e) {
    e.preventDefault();
    e.stopPropagation();
    fileUploadArea.style.borderColor = '#2c5f2d';
    fileUploadArea.style.backgroundColor = 'rgba(44, 95, 45, 0.05)';
}

function handleDrop(e) {
    e.preventDefault();
    e.stopPropagation();
    fileUploadArea.style.borderColor = '#d0d0d0';
    fileUploadArea.style.backgroundColor = '';
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        fileInput.files = files;
        displayFileInfo(files[0]);
    }
}

function displayFileInfo(file) {
    const ext = file.name.split('.').pop().toLowerCase();
    
    if (!['xls', 'xlsx'].includes(ext)) {
        showError('Please select a valid Excel file (.xls or .xlsx)');
        fileInput.value = '';
        return;
    }
    
    if (file.size > 5 * 1024 * 1024) {
        showError('File size exceeds 5MB limit');
        fileInput.value = '';
        return;
    }
    
    const size = (file.size / 1024).toFixed(2);
    fileInfo.innerHTML = `
        <i class="fas fa-file-excel"></i>
        <strong>${file.name}</strong>
        <span>(${size} KB)</span>
    `;
    fileInfo.classList.add('active');
}

async function handleUpload(e) {
    e.preventDefault();
    
    const submitBtn = uploadForm.querySelector('.btn-submit-grades');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
    submitBtn.disabled = true;
    
    const formData = new FormData(uploadForm);
    
    try {
        const response = await fetch('../../api/grades/upload_grades.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccess(result.message);
            closeAllModals();
            loadUploads();
        } else {
            showError(result.error || 'Upload failed');
        }
    } catch (error) {
        showError('Network error: ' + error.message);
    } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
}

async function loadUploads() {
    try {
        const response = await fetch('../../api/grades/fetch_grades.php');
        const data = await response.json();
        
        if (data.success) {
            renderUploads(data.uploads);
            updateStats(data.uploads);
        } else {
            showError(data.error || 'Failed to load uploads');
        }
    } catch (error) {
        uploadsList.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-exclamation-circle"></i>
                <p>Failed to load uploads</p>
            </div>
        `;
    }
}

function renderUploads(uploads) {
    if (uploads.length === 0) {
        uploadsList.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>No grade uploads yet</p>
                <small>Click "Upload Grades" to get started</small>
            </div>
        `;
        return;
    }
    
    uploadsList.innerHTML = uploads.map(upload => `
        <div class="upload-card">
            <div class="upload-header">
                <div>
                    <h3 class="upload-title">${escapeHtml(upload.subject_name)}</h3>
                    <p class="upload-code">${escapeHtml(upload.subject_code)}</p>
                </div>
                <span class="upload-badge">${escapeHtml(upload.semester)}</span>
            </div>
            <div class="upload-details">
                ${upload.class_section ? `
                    <div class="upload-detail">
                        <i class="fas fa-users"></i>
                        <span>Section: ${escapeHtml(upload.class_section)}</span>
                    </div>
                ` : ''}
                <div class="upload-detail">
                    <i class="fas fa-calendar"></i>
                    <span>${escapeHtml(upload.school_year)}</span>
                </div>
                <div class="upload-detail">
                    <i class="fas fa-user-graduate"></i>
                    <span>${upload.total_students} student${upload.total_students !== 1 ? 's' : ''}</span>
                </div>
                <div class="upload-detail">
                    <i class="fas fa-clock"></i>
                    <span>${formatDate(upload.upload_date)}</span>
                </div>
            </div>
            <div class="upload-actions">
                <button class="btn-view" onclick="viewGrades('${upload.upload_id}')">
                    <i class="fas fa-eye"></i> View
                </button>
                <button class="btn-delete" onclick="deleteUpload('${upload.upload_id}')">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </div>
    `).join('');
}

function updateStats(uploads) {
    const totalUploads = uploads.length;
    const totalStudents = uploads.reduce((sum, u) => sum + parseInt(u.total_students || 0), 0);
    const latestSemester = uploads.length > 0 ? uploads[0].semester + ' ' + uploads[0].school_year : '-';
    
    document.getElementById('totalUploads').textContent = totalUploads;
    document.getElementById('totalStudents').textContent = totalStudents;
    document.getElementById('currentSemester').textContent = latestSemester;
}

async function viewGrades(uploadId) {
    viewGradesModal.style.display = 'block';
    
    const wrapper = document.getElementById('gradesTableWrapper');
    wrapper.innerHTML = '<div class="loading-state">Loading grades...</div>';
    
    try {
        const response = await fetch(`../../api/grades/fetch_grades.php?upload_id=${uploadId}`);
        const data = await response.json();
        
        if (data.success) {
            renderGradesTable(data);
        } else {
            wrapper.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-exclamation-circle"></i>
                    <p>${data.error || 'Failed to load grades'}</p>
                </div>
            `;
        }
    } catch (error) {
        wrapper.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-exclamation-circle"></i>
                <p>Network error</p>
            </div>
        `;
    }
}

function renderGradesTable(data) {
    const { upload, columns, records } = data;
    
    // Update modal header
    document.getElementById('viewTitle').textContent = upload.subject_name;
    document.getElementById('viewSubtitle').textContent = 
        `${upload.subject_code} - ${upload.class_section || ''} | ${upload.semester} ${upload.school_year}`;
    
    // Build table
    let tableHTML = '<table class="grades-table"><thead><tr>';
    
    columns.forEach(col => {
        tableHTML += `<th>${escapeHtml(col)}</th>`;
    });
    
    tableHTML += '</tr></thead><tbody>';
    
    records.forEach(record => {
        tableHTML += '<tr>';
        columns.forEach(col => {
            const value = record.grades[col] ?? '';
            tableHTML += `<td>${escapeHtml(String(value))}</td>`;
        });
        tableHTML += '</tr>';
    });
    
    tableHTML += '</tbody></table>';
    
    document.getElementById('gradesTableWrapper').innerHTML = tableHTML;
}

async function deleteUpload(uploadId) {
    if (!confirm('Are you sure you want to delete this grade upload? This action cannot be undone.')) {
        return;
    }
    
    try {
        const response = await fetch('../../api/grades/delete_grades.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ upload_id: uploadId })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccess(result.message);
            loadUploads();
        } else {
            showError(result.error || 'Delete failed');
        }
    } catch (error) {
        showError('Network error');
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
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function showSuccess(message) {
    alert(message); // Replace with your notification system
}

function showError(message) {
    alert('Error: ' + message); // Replace with your notification system
}