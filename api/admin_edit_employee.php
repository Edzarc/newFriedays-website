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
$name = sanitizeInput($input['name'] ?? '');
$email = sanitizeInput($input['email'] ?? '');
$phone = sanitizeInput($input['phone'] ?? '');
$address = sanitizeInput($input['address'] ?? '');
$position = sanitizeInput($input['position'] ?? '');
$department = sanitizeInput($input['department'] ?? '');
$hireDate = sanitizeInput($input['hire_date'] ?? '');
$employmentStatus = sanitizeInput($input['employment_status'] ?? 'Active');

// Validation
if (!$staffId || !$name || !$email || !$phone || !$address || !$position || !$department || !$hireDate) {
    echo json_encode(['error' => 'All fields are required']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['error' => 'Invalid email address']);
    exit();
}

// Validate hire date
if (strtotime($hireDate) === false) {
    echo json_encode(['error' => 'Invalid hire date']);
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

    // Check if email is being changed and if new email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $userId]);
    if ($stmt->fetch()) {
        echo json_encode(['error' => 'Email already registered']);
        exit();
    }

    // Update user
    $stmt = $pdo->prepare(
        "UPDATE users SET name = ?, email = ?, phone = ?, address = ?, updated_at = NOW() WHERE id = ?"
    );
    $stmt->execute([$name, $email, $phone, $address, $userId]);

    // Update staff
    $stmt = $pdo->prepare(
        "UPDATE staff SET position = ?, department = ?, hire_date = ?, employment_status = ?, updated_at = NOW() WHERE id = ?"
    );
    $stmt->execute([$position, $department, $hireDate, $employmentStatus, $staffId]);

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Employee updated successfully']);
} catch (Exception $e) {
    $pdo->rollBack();
    error_log('Error updating employee: ' . $e->getMessage());
    echo json_encode(['error' => 'Failed to update employee']);
}
?>
