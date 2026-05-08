<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isAdmin()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['user_id'] ?? null;
$role = $data['role'] ?? null;

if (!$userId || !$role) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit();
}

// Validate role
$validRoles = ['customer', 'staff', 'admin'];
if (!in_array($role, $validRoles)) {
    echo json_encode(['success' => false, 'message' => 'Invalid role']);
    exit();
}

try {
    global $pdo;
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Update user role
    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
    $success = $stmt->execute([$role, $userId]);
    
    if ($success && $role === 'staff') {
        // Check if staff record exists
        $checkStmt = $pdo->prepare("SELECT id FROM staff WHERE user_id = ?");
        $checkStmt->execute([$userId]);
        $staffExists = $checkStmt->fetch();
        
        // If promoting to staff and no staff record exists, create one
        if (!$staffExists) {
            $staffStmt = $pdo->prepare("
                INSERT INTO staff (user_id, position, department, hire_date, employment_status)
                VALUES (?, ?, ?, NOW(), 'Active')
            ");
            $staffStmt->execute([$userId, 'Staff Member', 'General']);
        }
    } elseif ($success && $role !== 'staff') {
        // If removing staff role, you can optionally remove the staff record
        // Uncomment if you want to delete staff record when role changes
        // $deleteStmt = $pdo->prepare("DELETE FROM staff WHERE user_id = ?");
        // $deleteStmt->execute([$userId]);
    }
    
    $pdo->commit();
    
    echo json_encode(['success' => $success, 'message' => 'User role updated successfully']);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
