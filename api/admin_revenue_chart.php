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

$query = "SELECT DATE(created_at) as date, SUM(total_amount) as revenue
          FROM orders
          WHERE DATE(created_at) BETWEEN ? AND ?
          GROUP BY DATE(created_at)
          ORDER BY DATE(created_at)";

$stmt = $pdo->prepare($query);
$stmt->execute([$dateFrom, $dateTo]);
$results = $stmt->fetchAll();

$labels = [];
$values = [];

foreach ($results as $row) {
    $labels[] = date('M d', strtotime($row['date']));
    $values[] = (float)$row['revenue'];
}

echo json_encode(['labels' => $labels, 'values' => $values]);
?>