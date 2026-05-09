<?php
require_once 'includes/functions.php';

function handlePaymentSuccess() {
    requireLogin();
    
    $pendingOrderId = $_GET['order_id'] ?? null;
    
    if (!$pendingOrderId) {
        $error = "Invalid payment request.";
        include 'views/payment_result.php';
        return;
    }
    
    $pendingOrder = getPendingOrder($pendingOrderId);
    if (!$pendingOrder || $pendingOrder['user_id'] !== $_SESSION['user_id']) {
        $error = "Order not found.";
        include 'views/payment_result.php';
        return;
    }
    
    // Complete the pending order
    $orderId = completePendingOrder($pendingOrderId);
    
    if ($orderId) {
        $success = "Payment successful! Your order has been placed.";
        include 'views/payment_result.php';
    } else {
        $error = "Failed to complete order. Please contact support.";
        include 'views/payment_result.php';
    }
}

function handlePaymentFailed() {
    requireLogin();
    
    $pendingOrderId = $_GET['order_id'] ?? null;
    
    if (!$pendingOrderId) {
        $error = "Invalid payment request.";
        include 'views/payment_result.php';
        return;
    }
    
    $pendingOrder = getPendingOrder($pendingOrderId);
    if (!$pendingOrder || $pendingOrder['user_id'] !== $_SESSION['user_id']) {
        $error = "Order not found.";
        include 'views/payment_result.php';
        return;
    }
    
    // Delete the pending order
    deletePendingOrder($pendingOrderId);
    
    $error = "Payment failed or was cancelled. Please try again.";
    include 'views/payment_result.php';
}
?>