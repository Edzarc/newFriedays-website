<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isStaff()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

global $pdo;

// Complete the earliest currently serving order.
$stmt = $pdo->prepare("SELECT * FROM queue WHERE status = 'Serving' ORDER BY created_at ASC LIMIT 1");
$stmt->execute();
$currentServing = $stmt->fetch();
if ($currentServing) {
    updateOrderStatus($currentServing['order_id'], 'Completed');
    updateQueueStatus($currentServing['id'], 'Completed');
}

// Find the next waiting order
$stmt = $pdo->query("SELECT * FROM queue WHERE status = 'Waiting' ORDER BY created_at ASC LIMIT 1");
$nextOrder = $stmt->fetch();

if ($nextOrder) {
    updateQueueStatus($nextOrder['id'], 'Serving');
    updateOrderStatus($nextOrder['order_id'], 'Preparing');
    echo json_encode(['success' => true, 'queue_number' => $nextOrder['queue_number']]);
} else {
    echo json_encode(['success' => false, 'message' => 'No orders waiting']);
}
