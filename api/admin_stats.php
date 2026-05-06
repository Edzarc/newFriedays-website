<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isAdmin()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

global $pdo;

// Total orders
$stmt = $pdo->query("SELECT COUNT(*) as total FROM orders");
$totalOrders = $stmt->fetch()['total'];

// Total revenue
$stmt = $pdo->query("SELECT SUM(total_amount) as revenue FROM orders");
$totalRevenue = $stmt->fetch()['revenue'] ?? 0;

// Total users
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
$totalUsers = $stmt->fetch()['total'];

// Pending orders
$stmt = $pdo->query("SELECT COUNT(*) as pending FROM orders WHERE status = 'Pending'");
$pendingOrders = $stmt->fetch()['pending'];

// Current serving
$currentServing = getCurrentServing();

echo json_encode([
    'total_orders' => $totalOrders,
    'total_revenue' => $totalRevenue,
    'total_users' => $totalUsers,
    'pending_orders' => $pendingOrders,
    'current_serving' => $currentServing
]);
?>