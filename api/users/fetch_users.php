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

try {
    $role_filter = isset($_GET['role']) ? $_GET['role'] : null;
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    
    $sql = "SELECT id, name, email, role, created_at, status 
            FROM users 
            WHERE role IN ('student', 'faculty')";
    
    if ($role_filter && in_array($role_filter, ['student', 'faculty'])) {
        $sql .= " AND role = :role";
    }
    
    if ($search) {
        $sql .= " AND (name LIKE :search OR email LIKE :search)";
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    
    if ($role_filter && in_array($role_filter, ['student', 'faculty'])) {
        $stmt->bindValue(':role', $role_filter);
    }
    
    if ($search) {
        $stmt->bindValue(':search', "%{$search}%");
    }
    
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $users,
        'permissions' => [
            'can_edit' => $_SESSION['role'] === 'super_admin',
            'can_delete' => $_SESSION['role'] === 'super_admin',
            'can_add' => true
        ]
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?>  