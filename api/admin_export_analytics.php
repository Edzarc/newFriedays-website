<?php
require_once '../includes/functions.php';

if (!isAdmin()) {
    die('Unauthorized');
}

$format = $_GET['format'] ?? 'csv';
$dateFrom = $_GET['date_from'] ?? date('Y-m-d', strtotime('-30 days'));
$dateTo = $_GET['date_to'] ?? date('Y-m-d');

$analytics = getAnalyticsData($dateFrom, $dateTo);

if ($format === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="analytics_export.csv"');

    $output = fopen('php://output', 'w');

    // Analytics summary
    fputcsv($output, ['Metric', 'Value']);
    fputcsv($output, ['Total Revenue', $analytics['total_revenue']]);
    fputcsv($output, ['Total Orders', $analytics['order_count']]);
    fputcsv($output, ['Average Order Value', $analytics['avg_order_value']]);
    fputcsv($output, ['', '']);

    // Top products
    fputcsv($output, ['Top Selling Products']);
    fputcsv($output, ['Product', 'Quantity Sold']);
    foreach ($analytics['top_products'] as $product) {
        fputcsv($output, [$product['name'], $product['total_quantity']]);
    }

    fclose($output);
} elseif ($format === 'pdf') {
    // For PDF export, you'd need a PDF library
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="analytics_export.txt"');
    echo "PDF export not implemented yet. Use CSV instead.\n";
    echo "Analytics Summary:\n";
    echo "Total Revenue: ₱" . number_format($analytics['total_revenue'], 2) . "\n";
    echo "Total Orders: " . $analytics['order_count'] . "\n";
    echo "Average Order Value: ₱" . number_format($analytics['avg_order_value'], 2) . "\n";
}
?>