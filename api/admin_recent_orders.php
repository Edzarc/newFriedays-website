<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isAdmin()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$orders = getAllOrders();
$orders = array_slice($orders, 0, 10); // Get last 10 orders

echo json_encode(['orders' => $orders]);
?>