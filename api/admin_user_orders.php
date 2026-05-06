<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isAdmin()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$userId = $_GET['user_id'] ?? null;

if (!$userId) {
    echo json_encode(['orders' => []]);
    exit();
}

$orders = getUserOrders($userId);

echo json_encode(['orders' => $orders]);
?>