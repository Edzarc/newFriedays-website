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
$name = sanitizeInput($input['name'] ?? '');
$email = sanitizeInput($input['email'] ?? '');
$phone = sanitizeInput($input['phone'] ?? '');
$address = sanitizeInput($input['address'] ?? '');
$position = sanitizeInput($input['position'] ?? '');
$department = sanitizeInput($input['department'] ?? '');
$hireDate = sanitizeInput($input['hire_date'] ?? date('Y-m-d'));
$password = $input['password'] ?? '';

// Validation
if (!$name || !$email || !$phone || !$address || !$position || !$department || !$password) {
    echo json_encode(['error' => 'All fields are required']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['error' => 'Invalid email address']);
    exit();
}

global $pdo;

// Check if email already exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    echo json_encode(['error' => 'Email already registered']);
    exit();
}

// Validate hire date
if (strtotime($hireDate) === false) {
    echo json_encode(['error' => 'Invalid hire date']);
    exit();
}

try {
    $pdo->beginTransaction();

    // Create user account with staff role
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare(
        "INSERT INTO users (name, email, password_hash, phone, address, role, email_verified) 
         VALUES (?, ?, ?, ?, ?, 'staff', 1)"
    );
    $stmt->execute([$name, $email, $passwordHash, $phone, $address]);
    $userId = $pdo->lastInsertId();

    // Create staff record
    $stmt = $pdo->prepare(
        "INSERT INTO staff (user_id, position, department, hire_date, employment_status) 
         VALUES (?, ?, ?, ?, 'Active')"
    );
    $stmt->execute([$userId, $position, $department, $hireDate]);

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Employee account created successfully', 'user_id' => $userId]);
} catch (Exception $e) {
    $pdo->rollBack();
    error_log('Error creating employee: ' . $e->getMessage());
    echo json_encode(['error' => 'Failed to create employee account']);
}
?>
