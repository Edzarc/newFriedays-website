<?php
require_once 'includes/functions.php';

function showDashboard() {
    requireLogin();

    $user = getUserById($_SESSION['user_id']);
    $orders = getUserOrders($_SESSION['user_id']);
    $loyaltyTier = getLoyaltyTierByName($user['loyalty_tier']);

    include 'views/dashboard.php';
}
?>