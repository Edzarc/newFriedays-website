<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');

global $pdo;

if (!isStaff()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$currentServing = getCurrentServing();
$queueOrders = getQueueOrders();

$statuses = ['Pending', 'Preparing', 'Ready', 'Completed', 'Cancelled'];
$ordersByStatus = [];

foreach ($statuses as $status) {
    $stmt = $pdo->prepare("
        SELECT o.*, u.name as user_name
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.status = ?
        ORDER BY o.created_at ASC
    ");
    $stmt->execute([$status]);
    $ordersByStatus[$status] = $stmt->fetchAll();
}

echo json_encode([
    'current_serving' => $currentServing,
    'orders_by_status' => $ordersByStatus
]);
