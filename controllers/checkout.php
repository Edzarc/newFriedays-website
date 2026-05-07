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
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        // Apply loyalty discount
        $user = getUserById($_SESSION['user_id']);
        $discount = calculateDiscount($subtotal, $user['loyalty_tier']);
        $subtotalAfterDiscount = $subtotal - $discount;

        // Add tax
        $tax = $subtotalAfterDiscount * 0.12;

        // Add delivery fee if applicable
        $deliveryFee = ($orderType === 'Delivery') ? 50 : 0;

        // Final total
        $totalAmount = $subtotalAfterDiscount + $tax + $deliveryFee;

        // Create order
        $orderId = createOrder($_SESSION['user_id'], $orderType, $paymentMethod, $totalAmount, $cartItems);

        if ($orderId) {
            // Clear cart (in session/localStorage handled by JS)
            // Note: Since localStorage is client-side, we can't clear it here.
            // The cart should be cleared on the client side after successful order.
            header('Location: index.php?page=queue');
            exit();
        } else {
            $error = "Failed to create order. Please try again.";
        }
    }

    include 'views/checkout.php';
}
?>