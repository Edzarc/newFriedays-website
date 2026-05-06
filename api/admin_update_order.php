<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isAdmin()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$orderId = $data['order_id'] ?? null;
$status = $data['status'] ?? null;

if (!$orderId || !$status) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit();
}

global $pdo;
$stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
$success = $stmt->execute([$status, $orderId]);

echo json_encode(['success' => $success]);
?>