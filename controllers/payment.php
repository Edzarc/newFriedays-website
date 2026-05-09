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

    $checkoutSessionId = $pendingOrder['paymongo_source_id'] ?? null;
    if (empty($checkoutSessionId)) {
        $error = "Unable to verify payment: missing PayMongo checkout session ID.";
        include 'views/payment_result.php';
        return;
    }

    $checkoutSession = getPayMongoCheckoutSession($checkoutSessionId);
    if (!$checkoutSession || isset($checkoutSession['error'])) {
        $errorMsg = isset($checkoutSession['message']) ? $checkoutSession['message'] : 'Unknown error while verifying PayMongo payment.';
        $error = "Unable to verify payment: " . $errorMsg;
        include 'views/payment_result.php';
        return;
    }

    $paymentIntent = $checkoutSession['attributes']['payment_intent'] ?? null;
    $paymentStatus = $paymentIntent['attributes']['status'] ?? null;

    if (empty($paymentIntent) || empty($paymentIntent['id']) || $paymentStatus !== 'succeeded') {
        $error = "Payment has not been confirmed yet. Current status: " . ($paymentStatus ?? 'unknown') . ". Please contact support if your payment was already completed.";
        include 'views/payment_result.php';
        return;
    }

    $paymongoPaymentId = $paymentIntent['id'];
    $orderId = completePendingOrder($pendingOrderId, $paymongoPaymentId, $checkoutSessionId);
    
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