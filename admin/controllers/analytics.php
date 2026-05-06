<?php
require_once 'includes/functions.php';

function showAnalytics() {
    requireAdmin();

    $dateFrom = $_GET['date_from'] ?? date('Y-m-d', strtotime('-30 days'));
    $dateTo = $_GET['date_to'] ?? date('Y-m-d');

    $analytics = getAnalyticsData($dateFrom, $dateTo);

    include 'admin/views/analytics.php';
}
?>