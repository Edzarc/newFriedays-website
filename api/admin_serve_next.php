<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isAdmin()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

global $pdo;

// Mark current serving as completed
$stmt = $pdo->prepare("UPDATE queue SET status = 'Completed' WHERE status = 'Serving'");
$stmt->execute();

// Find the next waiting order
$stmt = $pdo->query("SELECT * FROM queue WHERE status = 'Waiting' ORDER BY created_at ASC LIMIT 1");
$nextOrder = $stmt->fetch();

if ($nextOrder) {
    // Update queue status to serving
    updateQueueStatus($nextOrder['id'], 'Serving');

    // Update order status to preparing
    $stmt = $pdo->prepare("UPDATE orders SET status = 'Preparing' WHERE id = ?");
    $stmt->execute([$nextOrder['order_id']]);

    echo json_encode(['success' => true, 'queue_number' => $nextOrder['queue_number']]);
} else {
    echo json_encode(['success' => false, 'message' => 'No orders waiting']);
}
?>