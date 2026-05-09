<?php
require_once 'includes/functions.php';

function showOrders() {
    requireAdmin();

    $filters = [];
    if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
        $filters['date_from'] = $_GET['date_from'];
    }
    if (isset($_GET['date_to']) && !empty($_GET['date_to'])) {
        $filters['date_to'] = $_GET['date_to'];
    }
    if (isset($_GET['order_type']) && !empty($_GET['order_type'])) {
        $filters['order_type'] = $_GET['order_type'];
    }
    if (isset($_GET['payment_method']) && !empty($_GET['payment_method'])) {
        $filters['payment_method'] = $_GET['payment_method'];
    }
    if (isset($_GET['status']) && !empty($_GET['status'])) {
        $filters['status'] = $_GET['status'];
    }

    $orders = getAllOrders($filters);

    include 'admin/views/orders.php';
}

