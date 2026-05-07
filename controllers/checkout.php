<?php
require_once 'includes/functions.php';

function showCheckout() {
    requireLogin();

    $user = getUserById($_SESSION['user_id']);
    $loyaltyTier = getLoyaltyTierByName($user['loyalty_tier']);

    // Calculate preview discount percentage
    $previewDiscountPercentage = $loyaltyTier['discount_percentage'];
    if ($user['loyalty_tier'] === 'Bronze') {
        $userOrders = getUserOrders($_SESSION['user_id']);
        if (count($userOrders) === 0) {
            $previewDiscountPercentage = 5.00;
        } else {
            $previewDiscountPercentage = 0.00;
        }
    }

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
        $discount = calculateDiscount($subtotal, $user['loyalty_tier'], $_SESSION['user_id']);
        $subtotalAfterDiscount = $subtotal - $discount;

        // Add tax
        $tax = $subtotalAfterDiscount * 0.12;

        // Add delivery fee if applicable
        $deliveryFee = 0;
        if ($orderType === 'Delivery') {
            $loyaltyTier = getLoyaltyTierByName($user['loyalty_tier']);
            if ($user['loyalty_tier'] === 'Silver' && $subtotalAfterDiscount > 500) {
                $deliveryFee = 0; // Free delivery for Silver over ₱500
            } elseif (in_array($user['loyalty_tier'], ['Gold', 'Platinum'])) {
                $deliveryFee = 0; // Free delivery for Gold and Platinum
            } else {
                $deliveryFee = 50; // Regular delivery fee
            }
        }

        // Final total
        $totalAmount = $subtotalAfterDiscount + $tax + $deliveryFee;

        // Create order
        $orderId = createOrder($_SESSION['user_id'], $orderType, $paymentMethod, $totalAmount, $cartItems);

        if ($orderId) {
            // Update user spending with original subtotal for loyalty tracking
            updateUserSpending($_SESSION['user_id'], $subtotal);

            // Clear cart (in session/localStorage handled by JS)
            // Note: Since localStorage is client-side, we can't clear it here.
            // The cart should be cleared on the client side after successful order.
            header('Location: index.php?page=queue');
            exit();
        } else {
            $error = "Failed to create order. Please try again.";
            include 'views/checkout.php';
            return;
        }
    }

    include 'views/checkout.php';
}
?>