<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');

global $pdo;

if (!isStaff()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$queueIds = $input['queue_ids'] ?? [];

if (!is_array($queueIds) || empty($queueIds)) {
    echo json_encode(['error' => 'No orders selected']);
    exit();
}

$placeholders = implode(',', array_fill(0, count($queueIds), '?'));
$stmt = $pdo->prepare("SELECT * FROM queue WHERE id IN ($placeholders) AND status = 'Waiting'");
$stmt->execute($queueIds);
$orders = $stmt->fetchAll();

if (!$orders) {
    echo json_encode(['error' => 'No waiting orders were selected']);
    exit();
}

foreach ($orders as $order) {
    updateQueueStatus($order['id'], 'Serving');
    updateOrderStatus($order['order_id'], 'Preparing');
}

echo json_encode(['success' => true, 'message' => 'Selected orders are now being processed']);
