<?php
session_start();
require_once '../../config/config.php';

header('Content-Type: application/json');

// Auth check
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['faculty', 'student'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

try {
    // Get specific upload
    if (isset($_GET['upload_id'])) {
        $upload_id = $_GET['upload_id'];
        
        // Fetch upload details
        $upload_query = "
            SELECT 
                gu.*,
                u.name as faculty_name
            FROM grade_uploads gu
            JOIN users u ON gu.faculty_id = u.id
            WHERE gu.upload_id = ? AND gu.status = 'active'
        ";
        
        // Faculty can only see their own uploads
        if ($role === 'faculty') {
            $upload_query .= " AND gu.faculty_id = ?";
            $stmt = $pdo->prepare($upload_query);
            $stmt->execute([$upload_id, $user_id]);
        } else {
            $stmt = $pdo->prepare($upload_query);
            $stmt->execute([$upload_id]);
        }
        
        $upload = $stmt->fetch();
        
        if (!$upload) {
            http_response_code(404);
            echo json_encode(['error' => 'Upload not found']);
            exit;
        }
        
        // Fetch columns
        $col_stmt = $pdo->prepare("
            SELECT column_name, column_order 
            FROM grade_columns 
            WHERE upload_id = ? 
            ORDER BY column_order
        ");
        $col_stmt->execute([$upload_id]);
        $columns = $col_stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Fetch grade records
        $record_query = "
            SELECT student_id, student_name, grade_data, row_order 
            FROM grade_records 
            WHERE upload_id = ?
        ";
        
        // Students can only see their own grades
        if ($role === 'student') {
            // Get student's user_id from session
            $student_user_id = $_SESSION['user_id'] ?? '';
            $record_query .= " AND student_id = ?";
            $record_stmt = $pdo->prepare($record_query . " ORDER BY row_order");
            $record_stmt->execute([$upload_id, $student_user_id]);
        } else {
            $record_stmt = $pdo->prepare($record_query . " ORDER BY row_order");
            $record_stmt->execute([$upload_id]);
        }
        
        $records = [];
        while ($row = $record_stmt->fetch()) {
            $grade_data = json_decode($row['grade_data'], true);
            $records[] = [
                'student_id' => $row['student_id'],
                'student_name' => $row['student_name'],
                'grades' => $grade_data,
                'row_order' => $row['row_order']
            ];
        }
        
        echo json_encode([
            'success' => true,
            'upload' => $upload,
            'columns' => $columns,
            'records' => $records
        ]);
        
    } else {
        // List all uploads
        $list_query = "
            SELECT 
                gu.upload_id,
                gu.subject_code,
                gu.subject_name,
                gu.class_section,
                gu.school_year,
                gu.semester,
                gu.file_name,
                gu.upload_date,
                gu.total_students,
                u.name as faculty_name
            FROM grade_uploads gu
            JOIN users u ON gu.faculty_id = u.id
            WHERE gu.status = 'active'
        ";
        
        if ($role === 'faculty') {
            $list_query .= " AND gu.faculty_id = ?";
            $list_stmt = $pdo->prepare($list_query . " ORDER BY gu.upload_date DESC");
            $list_stmt->execute([$user_id]);
        } else {
            $list_stmt = $pdo->prepare($list_query . " ORDER BY gu.upload_date DESC");
            $list_stmt->execute();
        }
        
        $uploads = $list_stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'uploads' => $uploads
        ]);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?>