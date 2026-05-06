<?php
require_once '../includes/functions.php';

if (!isAdmin()) {
    die('Unauthorized');
}

$format = $_GET['format'] ?? 'csv';
$filters = $_GET;
unset($filters['format']);

$orders = getAllOrders($filters);

if ($format === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="orders_export.csv"');

    $output = fopen('php://output', 'w');

    // CSV headers
    fputcsv($output, ['Order #', 'Customer', 'Date', 'Type', 'Payment', 'Status', 'Total']);

    // CSV data
    foreach ($orders as $order) {
        fputcsv($output, [
            $order['order_number'],
            $order['user_name'],
            $order['created_at'],
            $order['order_type'],
            $order['payment_method'],
            $order['status'],
            $order['total_amount']
        ]);
    }

    fclose($output);
} elseif ($format === 'pdf') {
    // For PDF export, you'd need a PDF library like TCPDF or FPDF
    // For now, just output as CSV
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="orders_export.csv"');
    echo "PDF export not implemented yet. Use CSV instead.\n";
}
?>