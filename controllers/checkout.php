<?php
require_once 'includes/functions.php';

function showCheckout() {
    requireLogin();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Process checkout
        $cartItems = json_decode($_POST['cart_items'], true);
        $orderType = sanitizeInput($_POST['order_type']);
        $paymentMethod = sanitizeInput($_POST['payment_method']);

        if (empty($cartItems) || empty($orderType) || empty($paymentMethod)) {
            $error = "All fields are required.";
            include 'views/checkout.php';
            return;
        }

        // Calculate total
        $totalAmount = 0;
        foreach ($cartItems as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
        }

        // Apply loyalty discount
        $user = getUserById($_SESSION['user_id']);
        $discount = calculateDiscount($totalAmount, $user['loyalty_tier']);
        $totalAmount -= $discount;

        // Create order
        $orderId = createOrder($_SESSION['user_id'], $orderType, $paymentMethod, $totalAmount, $cartItems);

        if ($orderId) {
            // Clear cart (in session/localStorage handled by JS)
            header('Location: index.php?page=queue');
            exit();
        } else {
            $error = "Failed to create order. Please try again.";
        }
    }

    include 'views/checkout.php';
}
?>