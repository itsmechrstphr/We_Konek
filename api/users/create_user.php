<?php
session_start();
require_once '../../config/config.php';

header('Content-Type: application/json');

// Auth check
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'super_admin'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

// Validate
$required = ['name', 'email', 'password', 'role'];
foreach ($required as $field) {
    if (empty($input[$field])) {
        http_response_code(400);
        echo json_encode(['error' => "Field {$field} is required"]);
        exit;
    }
}

// Validate role
if (!in_array($input['role'], ['student', 'faculty'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid role']);
    exit;
}

// Validate email
if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email format']);
    exit;
}

try {
    // Check if email exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$input['email']]);
    
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode(['error' => 'Email already exists']);
        exit;
    }
    
    // Insert user
    $stmt = $pdo->prepare("
        INSERT INTO users (name, email, password, role, status, created_at) 
        VALUES (?, ?, ?, ?, 'active', NOW())
    ");
    
    $hashed_password = password_hash($input['password'], PASSWORD_DEFAULT);
    
    $stmt->execute([
        $input['name'],
        $input['email'],
        $hashed_password,
        $input['role']
    ]);
    
    $new_id = $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'User created successfully',
        'user_id' => $new_id
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?>