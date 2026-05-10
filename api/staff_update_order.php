<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isStaff()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$orderId = $input['order_id'] ?? null;
$newStatus = $input['status'] ?? null;

if (!$orderId || !$newStatus) {
    echo json_encode(['error' => 'Order ID and status are required']);
    exit();
}

$validStatuses = ['Pending', 'Preparing', 'Ready', 'Completed', 'Cancelled'];
if (!in_array($newStatus, $validStatuses)) {
    echo json_encode(['error' => 'Invalid status']);
    exit();
}

$success = updateOrderStatus($orderId, $newStatus);

if ($success) {
    echo json_encode(['success' => true, 'message' => 'Order status updated successfully']);
} else {
    echo json_encode(['error' => 'Failed to update order status']);
}
