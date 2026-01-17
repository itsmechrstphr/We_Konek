<?php
session_start();
require_once '../../config/config.php';

// Auth check - Faculty only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty') {
    header('Location: ../../auth/login.php');
    exit;
}

$page_title = "Grade Management";
$userRole = 'faculty';
$userName = $_SESSION['user_name'] ?? 'Faculty';
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
    <link rel="stylesheet" href="../../assets/css/grades.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    
    <div class="dashboard-container">
        <?php include '../../includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="grades-header">
                <h1 class="grades-title">GRADE MANAGEMENT</h1>
                <button class="btn-upload" id="uploadBtn">
                    <i class="fas fa-file-excel"></i> Upload Grades
                </button>
            </div>
            
            <div class="grades-stats">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-file-upload"></i>
                    </div>
                    <div class="stat-info">
                        <h3 id="totalUploads">0</h3>
                        <p>Total Uploads</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3 id="totalStudents">0</h3>
                        <p>Total Students</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="stat-info">
                        <h3 id="currentSemester">-</h3>
                        <p>Current Semester</p>
                    </div>
                </div>
            </div>
            
            <div class="uploads-container">
                <h2 class="section-title">Grade Uploads</h2>
                <div id="uploadsList" class="uploads-grid">
                    <div class="loading-state">Loading grade uploads...</div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Upload Modal -->
    <div id="uploadModal" class="modal-grades">
        <div class="modal-content-grades">
            <div class="modal-header-grades">
                <h2>Upload Grade Sheet</h2>
                <span class="close-grades">&times;</span>
            </div>
            <form id="uploadForm" enctype="multipart/form-data">
                <div class="form-group-grades">
                    <label for="subjectCode">Subject Code *</label>
                    <input type="text" id="subjectCode" name="subject_code" required placeholder="e.g., CS101">
                </div>
                
                <div class="form-group-grades">
                    <label for="subjectName">Subject Name *</label>
                    <input type="text" id="subjectName" name="subject_name" required placeholder="e.g., Introduction to Programming">
                </div>
                
                <div class="form-group-grades">
                    <label for="classSection">Class Section</label>
                    <input type="text" id="classSection" name="class_section" placeholder="e.g., CS-1A">
                </div>
                
                <div class="form-row">
                    <div class="form-group-grades">
                        <label for="schoolYear">School Year *</label>
                        <input type="text" id="schoolYear" name="school_year" required placeholder="e.g., 2024-2025">
                    </div>
                    
                    <div class="form-group-grades">
                        <label for="semester">Semester *</label>
                        <select id="semester" name="semester" required>
                            <option value="">Select Semester</option>
                            <option value="1st">1st Semester</option>
                            <option value="2nd">2nd Semester</option>
                            <option value="Summer">Summer</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group-grades">
                    <label for="gradeFile">Excel File (.xls or .xlsx) *</label>
                    <div class="file-upload-area" id="fileUploadArea">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Click to browse or drag and drop</p>
                        <small>Maximum file size: 5MB</small>
                        <input type="file" id="gradeFile" name="grade_file" accept=".xls,.xlsx" required>
                    </div>
                    <div id="fileInfo" class="file-info"></div>
                </div>
                
                <div class="modal-actions-grades">
                    <button type="button" class="btn-cancel-grades" id="cancelUploadBtn">Cancel</button>
                    <button type="submit" class="btn-submit-grades">
                        <i class="fas fa-upload"></i> Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- View Grades Modal -->
    <div id="viewGradesModal" class="modal-grades">
        <div class="modal-content-grades modal-large">
            <div class="modal-header-grades">
                <div>
                    <h2 id="viewTitle">Grade Sheet</h2>
                    <p id="viewSubtitle" class="modal-subtitle"></p>
                </div>
                <div class="modal-actions-header">
                    <button class="btn-print" id="printBtn">
                        <i class="fas fa-print"></i> Print
                    </button>
                    <span class="close-grades">&times;</span>
                </div>
            </div>
            <div class="grades-table-wrapper" id="gradesTableWrapper">
                <div class="loading-state">Loading grades...</div>
            </div>
        </div>
    </div>
    
    <script src="../../assets/js/header.js"></script>
    <script src="../../assets/js/sidebar.js"></script>
    <script src="../../assets/js/dashboard.js"></script>
    <script src="../../assets/js/grades.js"></script>
</body>
</html>