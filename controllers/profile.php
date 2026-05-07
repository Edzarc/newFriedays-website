<?php
require_once 'includes/functions.php';

function showProfile() {
    requireLogin();

    $user = getUserById($_SESSION['user_id']);
    $orders = getUserOrders($_SESSION['user_id']);
    $loyaltyTier = getLoyaltyTierByName($user['loyalty_tier']);
    $loyaltyTiers = getLoyaltyTiers();

    include 'views/profile.php';
}
?>