<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isAdmin()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$dateFrom = $_GET['date_from'] ?? date('Y-m-d', strtotime('-30 days'));
$dateTo = $_GET['date_to'] ?? date('Y-m-d');

global $pdo;

$query = "SELECT order_type, COUNT(*) as count
          FROM orders
          WHERE DATE(created_at) BETWEEN ? AND ?
          GROUP BY order_type";

$stmt = $pdo->prepare($query);
$stmt->execute([$dateFrom, $dateTo]);
$results = $stmt->fetchAll();

$labels = [];
$values = [];

foreach ($results as $row) {
    $labels[] = $row['order_type'];
    $values[] = (int)$row['count'];
}

echo json_encode(['labels' => $labels, 'values' => $values]);
?>