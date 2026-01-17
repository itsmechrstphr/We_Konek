<?php
session_start();
require_once '../../config/config.php';

header('Content-Type: application/json');

// Faculty only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty') {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden: Faculty access required']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['upload_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Upload ID required']);
    exit;
}

$upload_id = $input['upload_id'];
$faculty_id = $_SESSION['user_id'];

try {
    // Verify ownership
    $stmt = $pdo->prepare("
        SELECT file_path 
        FROM grade_uploads 
        WHERE upload_id = ? AND faculty_id = ?
    ");
    $stmt->execute([$upload_id, $faculty_id]);
    $upload = $stmt->fetch();
    
    if (!$upload) {
        http_response_code(404);
        echo json_encode(['error' => 'Upload not found or access denied']);
        exit;
    }
    
    // Soft delete (mark as deleted)
    $delete_stmt = $pdo->prepare("
        UPDATE grade_uploads 
        SET status = 'deleted' 
        WHERE upload_id = ?
    ");
    $delete_stmt->execute([$upload_id]);
    
    // Optionally delete physical file
    if (file_exists($upload['file_path'])) {
        unlink($upload['file_path']);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Grade upload deleted successfully'
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?>