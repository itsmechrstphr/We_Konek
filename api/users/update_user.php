<?php
session_start();
require_once '../../config/config.php';

header('Content-Type: application/json');

// SUPER ADMIN ONLY
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'super_admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden: Super admin access required']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'User ID required']);
    exit;
}

$user_id = $input['id'];

try {
    // Verify user exists and is student/faculty
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if (!$user) {
        http_response_code(404);
        echo json_encode(['error' => 'User not found']);
        exit;
    }
    
    if (!in_array($user['role'], ['student', 'faculty'])) {
        http_response_code(403);
        echo json_encode(['error' => 'Cannot edit admin users']);
        exit;
    }
    
    // Build update query
    $fields = [];
    $values = [];
    
    if (!empty($input['name'])) {
        $fields[] = "name = ?";
        $values[] = $input['name'];
    }
    
    if (!empty($input['email'])) {
        if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid email format']);
            exit;
        }
        
        // Check email uniqueness
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$input['email'], $user_id]);
        if ($stmt->fetch()) {
            http_response_code(409);
            echo json_encode(['error' => 'Email already exists']);
            exit;
        }
        
        $fields[] = "email = ?";
        $values[] = $input['email'];
    }
    
    if (!empty($input['password'])) {
        $fields[] = "password = ?";
        $values[] = password_hash($input['password'], PASSWORD_DEFAULT);
    }
    
    if (isset($input['status']) && in_array($input['status'], ['active', 'inactive'])) {
        $fields[] = "status = ?";
        $values[] = $input['status'];
    }
    
    if (empty($fields)) {
        http_response_code(400);
        echo json_encode(['error' => 'No fields to update']);
        exit;
    }
    
    $values[] = $user_id;
    $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    
    echo json_encode([
        'success' => true,
        'message' => 'User updated successfully'
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?>