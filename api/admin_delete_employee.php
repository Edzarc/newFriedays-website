<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isAdmin()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$staffId = intval($input['staff_id'] ?? 0);

if (!$staffId) {
    echo json_encode(['error' => 'Staff ID is required']);
    exit();
}

global $pdo;

try {
    $pdo->beginTransaction();

    // Get staff record to find user_id
    $stmt = $pdo->prepare("SELECT user_id FROM staff WHERE id = ?");
    $stmt->execute([$staffId]);
    $staff = $stmt->fetch();

    if (!$staff) {
        echo json_encode(['error' => 'Staff member not found']);
        exit();
    }

    $userId = $staff['user_id'];

    // Delete staff record
    $stmt = $pdo->prepare("DELETE FROM staff WHERE id = ?");
    $stmt->execute([$staffId]);

    // Delete user account
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$userId]);

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Employee deleted successfully']);
} catch (Exception $e) {
    $pdo->rollBack();
    error_log('Error deleting employee: ' . $e->getMessage());
    echo json_encode(['error' => 'Failed to delete employee']);
}
?>
