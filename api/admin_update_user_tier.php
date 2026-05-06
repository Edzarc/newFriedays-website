<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isAdmin()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['user_id'] ?? null;
$tier = $data['tier'] ?? null;

if (!$userId || !$tier) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit();
}

global $pdo;
$stmt = $pdo->prepare("UPDATE users SET loyalty_tier = ? WHERE id = ?");
$success = $stmt->execute([$tier, $userId]);

echo json_encode(['success' => $success]);
?>