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
        $deliveryAddressId = isset($_POST['delivery_address_id']) ? intval($_POST['delivery_address_id']) : null;
        if ($deliveryAddressId === 0) {
            $deliveryAddressId = null;
        }

        if (empty($cartItems) || empty($orderType) || empty($paymentMethod)) {
            $error = "All fields are required.";
            include 'views/checkout.php';
            return;
        }

        $deliveryAddress = null;
        if ($orderType === 'Delivery') {
            if (empty($deliveryAddressId)) {
                $error = "Please select a saved delivery address.";
                include 'views/checkout.php';
                return;
            }

            $addressRow = getUserAddressById($deliveryAddressId);
            if (!$addressRow || $addressRow['user_id'] !== $_SESSION['user_id']) {
                $error = "Selected delivery address is invalid.";
                include 'views/checkout.php';
                return;
            }

            $deliveryAddress = $addressRow['address'];
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

        if ($paymentMethod === 'GCash') {
            // Handle GCash payment with PayMongo Checkout
            $pendingOrderId = createPendingOrder($_SESSION['user_id'], $orderType, $paymentMethod, $totalAmount, $cartItems, $deliveryAddressId, $deliveryAddress);
            
            if ($pendingOrderId) {
                $checkout = createPayMongoCheckout($totalAmount, "Friedays Bocaue Order #" . $pendingOrderId, $pendingOrderId);
                
                if ($checkout && !isset($checkout['error'])) {
                    // Store checkout session ID in pending order
                    global $pdo;
                    $stmt = $pdo->prepare("UPDATE pending_orders SET paymongo_source_id = ? WHERE id = ?");
                    $stmt->execute([$checkout['id'], $pendingOrderId]);
                    
                    // Immediately redirect to PayMongo checkout
                    if (!empty($checkout['attributes']['checkout_url'])) {
                        header('Location: ' . $checkout['attributes']['checkout_url']);
                        exit();
                    }

                    $error = "Failed to initialize GCash payment: invalid PayMongo checkout URL.";
                    include 'views/checkout.php';
                    return;
                } else {
                    // Handle PayMongo error
                    $errorMessage = isset($checkout['message']) ? $checkout['message'] : 'Unknown PayMongo error';
                    $error = "Failed to initialize GCash payment: " . $errorMessage;
                    include 'views/checkout.php';
                    return;
                }
            } else {
                $error = "Failed to create pending order. Please try again.";
                include 'views/checkout.php';
                return;
            }
        } else {
            // Handle Cash on Delivery
            $orderId = createOrder($_SESSION['user_id'], $orderType, $paymentMethod, $totalAmount, $cartItems, $deliveryAddressId, $deliveryAddress);

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
    }

    $addresses = getUserAddresses($_SESSION['user_id']);
    include 'views/checkout.php';
}
?>