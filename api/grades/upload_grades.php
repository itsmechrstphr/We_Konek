<?php
session_start();
require_once '../../config/config.php';
require_once '../../vendor/autoload.php'; // Composer autoload for PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

header('Content-Type: application/json');

// Auth check - Faculty only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty') {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden: Faculty access required']);
    exit;
}

// Validate request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Validate file upload
if (!isset($_FILES['grade_file']) || $_FILES['grade_file']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'No file uploaded or upload error occurred']);
    exit;
}

// Validate form data
$subject_code = trim($_POST['subject_code'] ?? '');
$subject_name = trim($_POST['subject_name'] ?? '');
$class_section = trim($_POST['class_section'] ?? '');
$school_year = trim($_POST['school_year'] ?? '');
$semester = trim($_POST['semester'] ?? '');

if (empty($subject_code) || empty($subject_name) || empty($school_year) || empty($semester)) {
    http_response_code(400);
    echo json_encode(['error' => 'All fields are required']);
    exit;
}

// Validate semester
if (!in_array($semester, ['1st', '2nd', 'Summer'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid semester']);
    exit;
}

$file = $_FILES['grade_file'];
$file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

// Validate file extension
if (!in_array($file_ext, ['xls', 'xlsx'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Only .xls and .xlsx files are allowed']);
    exit;
}

// Validate file size (max 5MB)
if ($file['size'] > 5 * 1024 * 1024) {
    http_response_code(400);
    echo json_encode(['error' => 'File size exceeds 5MB limit']);
    exit;
}

try {
    // Create upload directory if not exists
    $upload_dir = '../../uploads/grades/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Generate unique upload ID
    $upload_id = 'GRD_' . date('Ymd') . '_' . uniqid();
    
    // Generate safe filename
    $safe_filename = $upload_id . '.' . $file_ext;
    $file_path = $upload_dir . $safe_filename;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $file_path)) {
        throw new Exception('Failed to move uploaded file');
    }
    
    // Load Excel file
    $spreadsheet = IOFactory::load($file_path);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();
    
    // Validate minimum rows
    if (count($rows) < 2) {
        unlink($file_path);
        throw new Exception('Excel file must contain at least a header row and one data row');
    }
    
    // Extract headers (first row)
    $headers = array_filter($rows[0], function($value) {
        return !is_null($value) && trim($value) !== '';
    });
    
    if (empty($headers)) {
        unlink($file_path);
        throw new Exception('Excel file must contain column headers in the first row');
    }
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Insert upload record
    $stmt = $pdo->prepare("
        INSERT INTO grade_uploads 
        (upload_id, faculty_id, subject_code, subject_name, class_section, school_year, semester, file_name, file_path, total_students) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $total_students = count($rows) - 1; // Exclude header row
    
    $stmt->execute([
        $upload_id,
        $_SESSION['user_id'],
        $subject_code,
        $subject_name,
        $class_section,
        $school_year,
        $semester,
        $file['name'],
        $file_path,
        $total_students
    ]);
    
    // Store column metadata
    $col_stmt = $pdo->prepare("
        INSERT INTO grade_columns (upload_id, column_name, column_order) 
        VALUES (?, ?, ?)
    ");
    
    $column_order = 0;
    foreach ($headers as $header) {
        $col_stmt->execute([$upload_id, trim($header), $column_order++]);
    }
    
    // Store grade records
    $record_stmt = $pdo->prepare("
        INSERT INTO grade_records (upload_id, student_id, student_name, grade_data, row_order) 
        VALUES (?, ?, ?, ?, ?)
    ");
    
    $row_order = 0;
    for ($i = 1; $i < count($rows); $i++) {
        $row = $rows[$i];
        
        // Skip empty rows
        if (empty(array_filter($row))) {
            continue;
        }
        
        // Build grade data JSON
        $grade_data = [];
        foreach ($headers as $col_index => $header) {
            $value = $row[$col_index] ?? '';
            
            // Handle date values
            if (Date::isDateTime($worksheet->getCellByColumnAndRow($col_index + 1, $i + 1))) {
                $value = Date::excelToDateTimeObject($value)->format('Y-m-d');
            }
            
            $grade_data[trim($header)] = $value;
        }
        
        // Try to identify student ID and name from common column names
        $student_id = null;
        $student_name = null;
        
        foreach ($grade_data as $key => $value) {
            $key_lower = strtolower($key);
            if (strpos($key_lower, 'student id') !== false || strpos($key_lower, 'id') !== false) {
                $student_id = $value;
            }
            if (strpos($key_lower, 'name') !== false || strpos($key_lower, 'student name') !== false) {
                $student_name = $value;
            }
        }
        
        $record_stmt->execute([
            $upload_id,
            $student_id,
            $student_name,
            json_encode($grade_data, JSON_UNESCAPED_UNICODE),
            $row_order++
        ]);
    }
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Grades uploaded successfully',
        'upload_id' => $upload_id,
        'total_students' => $row_order
    ]);
    
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Clean up file on error
    if (isset($file_path) && file_exists($file_path)) {
        unlink($file_path);
    }
    
    http_response_code(500);
    echo json_encode(['error' => 'Upload failed: ' . $e->getMessage()]);
}
?>