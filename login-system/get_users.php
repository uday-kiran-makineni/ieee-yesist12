<?php
header('Content-Type: application/json');
require_once 'config.php';

try {
    $pdo = getDBConnection();
    
    // Get all users from the database
    $stmt = $pdo->query("SELECT id, email, full_name, phone, is_verified, created_at FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get total count
    $countStmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $total = $countStmt->fetch()['total'];
    
    echo json_encode([
        'success' => true,
        'total_users' => $total,
        'users' => $users,
        'message' => "Found $total users in database"
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>