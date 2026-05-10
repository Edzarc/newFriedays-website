<?php
require_once __DIR__ . '/../config/db.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Authentication functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isStaff() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'staff';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: index.php?page=login');
        exit();
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: index.php?page=admin_login');
        exit();
    }
}

function requireStaff() {
    if (!isStaff()) {
        header('Location: index.php?page=login');
        exit();
    }
}

// User functions
function getUserById($userId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch();
}

function getUserByEmail($email) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch();
}

function createEmailVerificationToken($userId) {
    global $pdo;
    $token = bin2hex(random_bytes(32));
    $expiresAt = date('Y-m-d H:i:s', strtotime('+1 day'));
    $stmt = $pdo->prepare("INSERT INTO email_verification_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $token, $expiresAt]);
    return $token;
}

function getEmailVerificationRecord($token) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT evt.*, u.email_verified FROM email_verification_tokens evt JOIN users u ON evt.user_id = u.id WHERE evt.token = ? LIMIT 1");
    $stmt->execute([$token]);
    return $stmt->fetch();
}

function markEmailVerified($tokenId, $userId) {
    global $pdo;
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("UPDATE users SET email_verified = 1, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$userId]);

        $stmt = $pdo->prepare("UPDATE email_verification_tokens SET used_at = NOW() WHERE id = ?");
        $stmt->execute([$tokenId]);

        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

function sendEmail($to, $subject, $htmlBody, $plainTextBody = '') {
    require_once __DIR__ . '/../src/Exception.php';
    require_once __DIR__ . '/../src/PHPMailer.php';
    require_once __DIR__ . '/../src/SMTP.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;
        $mail->SMTPAutoTLS = true;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $htmlBody;
        $mail->AltBody = $plainTextBody ?: strip_tags($htmlBody);

        return $mail->send();
    } catch (PHPMailer\PHPMailer\Exception $exception) {
        error_log('Email send failed: ' . $exception->getMessage());
        return false;
    }
}

function sendVerificationEmail($email, $name, $token) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $basePath = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $verifyUrl = sprintf('%s://%s%s/index.php?page=verify_email&token=%s', $scheme, $host, $basePath, urlencode($token));

    $subject = 'Verify your Friedays account';
    $htmlBody = "<p>Hi " . htmlspecialchars($name) . ",</p>" .
        "<p>Thank you for registering with Friedays Bocaue. Please verify your email address by clicking the button below:</p>" .
        "<p><a href=\"{$verifyUrl}\" style=\"display:inline-block;padding:10px 18px;background:#f06c00;color:#fff;text-decoration:none;border-radius:4px;\">Verify Email</a></p>" .
        "<p>If the button does not work, copy and paste the following link into your browser:</p>" .
        "<p><a href=\"{$verifyUrl}\">{$verifyUrl}</a></p>" .
        "<p>If you did not register, you can ignore this message.</p>";

    $plainTextBody = "Hi {$name},\n\n" .
        "Thank you for registering with Friedays Bocaue. Please verify your email address using the link below:\n" .
        "{$verifyUrl}\n\n" .
        "If you did not register, you can ignore this message.\n";

    return sendEmail($email, $subject, $htmlBody, $plainTextBody);
}

function canResendVerificationEmail($userId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT last_verification_email_sent FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!$user || !$user['last_verification_email_sent']) {
        return true; // Never sent, so can send
    }
    
    $lastSent = strtotime($user['last_verification_email_sent']);
    $now = time();
    return ($now - $lastSent) >= 30; // 30 seconds cooldown
}

function updateLastVerificationEmailSent($userId) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE users SET last_verification_email_sent = NOW() WHERE id = ?");
    return $stmt->execute([$userId]);
}

function getUserAddresses($userId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM user_addresses WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function getUserAddressById($addressId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM user_addresses WHERE id = ?");
    $stmt->execute([$addressId]);
    return $stmt->fetch();
}

function addUserAddress($userId, $label, $address) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO user_addresses (user_id, label, address) VALUES (?, ?, ?)");
    return $stmt->execute([$userId, $label, $address]);
}

function deleteUserAddress($addressId) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM user_addresses WHERE id = ?");
    return $stmt->execute([$addressId]);
}

function deleteUser($userId) {
    global $pdo;
    // First delete related records to maintain referential integrity
    // Delete user addresses
    $stmt = $pdo->prepare("DELETE FROM user_addresses WHERE user_id = ?");
    $stmt->execute([$userId]);

    // Delete user orders (this might be optional depending on business rules)
    // For now, we'll keep orders for audit purposes, but you could add this:
    // $stmt = $pdo->prepare("DELETE FROM orders WHERE user_id = ?");
    // $stmt->execute([$userId]);

    // Finally delete the user
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    return $stmt->execute([$userId]);
}

function updateUserProfile($userId, $name, $email, $phone, $address) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, address = ?, updated_at = NOW() WHERE id = ?");
    $success = $stmt->execute([$name, $email, $phone, $address, $userId]);
    
    if ($success) {
        // Also update the "Home" address in user_addresses if it exists
        $stmt = $pdo->prepare("UPDATE user_addresses SET address = ?, updated_at = NOW() WHERE user_id = ? AND label = 'Home'");
        $stmt->execute([$address, $userId]);
    }
    
    return $success;
}

function updateUserSpending($userId, $amount) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE users SET total_spending = total_spending + ?, updated_at = NOW() WHERE id = ?");
    $stmt->execute([$amount, $userId]);
    updateLoyaltyTier($userId);
}

function updateLoyaltyTier($userId) {
    global $pdo;
    $user = getUserById($userId);
    $tiers = getLoyaltyTiers();

    $currentTier = $user['loyalty_tier'];
    $newTier = 'Bronze';

    foreach ($tiers as $tier) {
        if ($user['total_spending'] >= $tier['min_spending_threshold']) {
            $newTier = $tier['tier_name'];
        }
    }

    if ($currentTier !== $newTier) {
        $stmt = $pdo->prepare("UPDATE users SET loyalty_tier = ? WHERE id = ?");
        $stmt->execute([$newTier, $userId]);
    }
}

// Product functions
function getAllProducts($availableOnly = false) {
    global $pdo;
    $query = "SELECT * FROM products";
    if ($availableOnly) {
        $query .= " WHERE is_available = 1";
    }
    $query .= " ORDER BY category, name";
    $stmt = $pdo->query($query);
    return $stmt->fetchAll();
}

function getProductsByCategory($category, $availableOnly = false) {
    global $pdo;
    $query = "SELECT * FROM products WHERE category = ?";
    $params = [$category];
    if ($availableOnly) {
        $query .= " AND is_available = 1";
    }
    $query .= " ORDER BY name";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getProductById($productId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    return $stmt->fetch();
}

// Product management functions for admin
function addProduct($name, $category, $price, $description, $imageUrl = null) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO products (name, category, price, description, image_url) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $category, $price, $description, $imageUrl]);
    return $pdo->lastInsertId();
}

function updateProduct($productId, $name, $category, $price, $description, $imageUrl = null) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE products SET name = ?, category = ?, price = ?, description = ?, image_url = ? WHERE id = ?");
    $stmt->execute([$name, $category, $price, $description, $imageUrl, $productId]);
    return $stmt->rowCount() > 0;
}

function deleteProduct($productId) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    return $stmt->rowCount() > 0;
}

function toggleProductAvailability($productId, $available) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE products SET is_available = ? WHERE id = ?");
    $stmt->execute([$available ? 1 : 0, $productId]);
    return $stmt->rowCount() > 0;
}

// Order functions
function createOrder($userId, $orderType, $paymentMethod, $totalAmount, $cartItems, $deliveryAddressId = null, $deliveryAddress = null, $paymongoPaymentId = null, $paymongoLinkId = null) {
    global $pdo;

    // Generate order number
    $orderNumber = 'ORD' . date('Ymd') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

    // Convert 0 or falsy delivery address ID to NULL to satisfy foreign key constraint
    $deliveryAddressId = !empty($deliveryAddressId) ? $deliveryAddressId : null;

    $paymentStatus = $paymongoPaymentId ? 'Paid' : 'Pending';

    // Insert order
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, order_number, order_type, payment_method, total_amount, delivery_address_id, delivery_address, payment_status, paymongo_payment_id, paymongo_link_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $orderNumber, $orderType, $paymentMethod, $totalAmount, $deliveryAddressId, $deliveryAddress, $paymentStatus, $paymongoPaymentId, $paymongoLinkId]);
    $orderId = $pdo->lastInsertId();

    // Insert order items
    foreach ($cartItems as $item) {
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)");
        $stmt->execute([$orderId, $item['id'], $item['quantity'], $item['price']]);
    }

    // Add to queue
    addToQueue($orderId);

    return $orderId;
}

function formatCurrency($amount) {
    return '₱' . number_format((float)$amount, 2);
}

function buildOrderItemsTableHtml($orderItems) {
    $html = '<table style="width:100%;border-collapse:collapse;margin-top:10px;">';
    $html .= '<thead><tr><th style="text-align:left;padding:8px;border-bottom:1px solid #ddd;">Item</th><th style="text-align:center;padding:8px;border-bottom:1px solid #ddd;">Qty</th><th style="text-align:right;padding:8px;border-bottom:1px solid #ddd;">Total</th></tr></thead>';
    $html .= '<tbody>';

    foreach ($orderItems as $item) {
        $itemTotal = $item['price_at_purchase'] * $item['quantity'];
        $html .= '<tr>' .
            '<td style="padding:8px;border-bottom:1px solid #eee;">' . htmlspecialchars($item['name']) . '</td>' .
            '<td style="padding:8px;border-bottom:1px solid #eee;text-align:center;">' . intval($item['quantity']) . '</td>' .
            '<td style="padding:8px;border-bottom:1px solid #eee;text-align:right;">' . formatCurrency($itemTotal) . '</td>' .
            '</tr>';
    }

    $html .= '</tbody></table>';
    return $html;
}

function sendOrderConfirmationEmail($orderId) {
    $order = getOrderById($orderId);
    if (!$order) {
        return false;
    }

    $user = getUserById($order['user_id']);
    if (!$user || !filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $orderItems = getOrderItems($orderId);
    $paymentMessage = '';
    if ($order['payment_method'] === 'GCash') {
        $paymentMessage = $order['payment_status'] === 'Paid'
            ? '<p>Your payment via GCash has been successfully received.</p>'
            : '<p>Your payment via GCash is being processed. We will update you when it is confirmed.</p>';
    } else {
        $paymentMessage = '<p>Your order has been placed successfully. Please pay in cash when your order is ready for pickup or delivered.</p>';
    }

    $deliveryAddressSection = '';
    if ($order['order_type'] === 'Delivery' && !empty($order['delivery_address'])) {
        $deliveryAddressSection = '<p><strong>Delivery Address:</strong><br>' . nl2br(htmlspecialchars($order['delivery_address'])) . '</p>';
    }

    $subject = 'Friedays Order Confirmation - ' . htmlspecialchars($order['order_number']);
    $htmlBody = '<p>Hi ' . htmlspecialchars($user['name']) . ',</p>' .
        '<p>Thank you for your order. Here are your order details:</p>' .
        '<p><strong>Order Number:</strong> ' . htmlspecialchars($order['order_number']) . '<br>' .
        '<strong>Order Type:</strong> ' . htmlspecialchars($order['order_type']) . '<br>' .
        '<strong>Payment Method:</strong> ' . htmlspecialchars($order['payment_method']) . '<br>' .
        '<strong>Order Status:</strong> ' . htmlspecialchars($order['status']) . '</p>' .
        $paymentMessage .
        $deliveryAddressSection .
        buildOrderItemsTableHtml($orderItems) .
        '<p style="text-align:right;font-weight:bold;">Total: ' . formatCurrency($order['total_amount']) . '</p>' .
        '<p>If you have any questions, reply to this email or contact our support.</p>' .
        '<p>Thank you for choosing Friedays Bocaue!</p>';

    return sendEmail($user['email'], $subject, $htmlBody);
}

function sendOrderReadyEmail($orderId) {
    $order = getOrderById($orderId);
    if (!$order) {
        return false;
    }

    $user = getUserById($order['user_id']);
    if (!$user || !filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $orderItems = getOrderItems($orderId);
    $deliveryMessage = '';
    if ($order['order_type'] === 'Delivery') {
        $deliveryMessage = '<p>Your order is ready for delivery and will be on its way shortly.</p>';
    } elseif ($order['order_type'] === 'Pickup') {
        $deliveryMessage = '<p>Your order is ready for pickup at the counter.</p>';
    } elseif ($order['order_type'] === 'Dine In') {
        $deliveryMessage = '<p>Your order is ready. Please proceed to your table or the pickup counter.</p>';
    }

    $subject = 'Your Friedays Order is Ready - ' . htmlspecialchars($order['order_number']);
    $htmlBody = '<p>Hi ' . htmlspecialchars($user['name']) . ',</p>' .
        '<p>Your order is now <strong>Ready</strong>.</p>' .
        '<p><strong>Order Number:</strong> ' . htmlspecialchars($order['order_number']) . '<br>' .
        '<strong>Order Type:</strong> ' . htmlspecialchars($order['order_type']) . '<br>' .
        '<strong>Payment Method:</strong> ' . htmlspecialchars($order['payment_method']) . '<br>' .
        '<strong>Order Status:</strong> ' . htmlspecialchars($order['status']) . '</p>' .
        $deliveryMessage .
        '<p><strong>Total:</strong> ' . formatCurrency($order['total_amount']) . '</p>' .
        buildOrderItemsTableHtml($orderItems) .
        '<p>If you have any questions, reply to this email or contact our support.</p>' .
        '<p>Thank you for choosing Friedays Bocaue!</p>';

    return sendEmail($user['email'], $subject, $htmlBody);
}

function getOrderById($orderId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$orderId]);
    return $stmt->fetch();
}

function getOrderItems($orderId) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT oi.*, p.name, p.category
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$orderId]);
    return $stmt->fetchAll();
}

function getUserOrders($userId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

// Queue functions
function addToQueue($orderId) {
    global $pdo;
    $stmt = $pdo->query("SELECT MAX(queue_number) as max_queue FROM queue");
    $result = $stmt->fetch();
    $nextQueue = ($result['max_queue'] ?? 0) + 1;

    $stmt = $pdo->prepare("INSERT INTO queue (order_id, queue_number) VALUES (?, ?)");
    $stmt->execute([$orderId, $nextQueue]);
}

function getCurrentServing() {
    global $pdo;
    $stmt = $pdo->query("
        SELECT q.queue_number, o.order_number, u.name as user_name
        FROM orders o
        JOIN users u ON o.user_id = u.id
        JOIN queue q ON q.order_id = o.id
        WHERE o.status = 'Preparing'
        ORDER BY o.updated_at DESC LIMIT 1
    ");
    $result = $stmt->fetch();
    return $result ?: null;
}

function getUserQueuePosition($userId) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT q.queue_number, q.status
        FROM queue q
        JOIN orders o ON q.order_id = o.id
        WHERE o.user_id = ? AND q.status IN ('Waiting', 'Serving')
        ORDER BY q.created_at DESC LIMIT 1
    ");
    $stmt->execute([$userId]);
    return $stmt->fetch();
}

function updateOrderStatus($orderId, $status) {
    global $pdo;
    $order = getOrderById($orderId);

    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $success = $stmt->execute([$status, $orderId]);

    if ($success && $status === 'Ready' && $order && $order['status'] !== 'Ready') {
        sendOrderReadyEmail($orderId);
    }

    return $success;
}

function getQueueOrders($status = null) {
    global $pdo;
    $query = "
        SELECT q.*, o.order_number, o.order_type, o.status as order_status, u.name as user_name
        FROM queue q
        JOIN orders o ON q.order_id = o.id
        JOIN users u ON o.user_id = u.id
    ";
    $params = [];
    if ($status !== null) {
        $query .= " WHERE q.status = ?";
        $params[] = $status;
    } else {
        $query .= " WHERE q.status IN ('Waiting', 'Serving')";
    }
    $query .= " ORDER BY q.created_at ASC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function updateQueueStatus($queueId, $status) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE queue SET status = ? WHERE id = ?");
    $stmt->execute([$status, $queueId]);
}

// Loyalty functions
function getLoyaltyTiers() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM loyalty_tiers ORDER BY min_spending_threshold");
    return $stmt->fetchAll();
}

function getLoyaltyTierByName($tierName) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM loyalty_tiers WHERE tier_name = ?");
    $stmt->execute([$tierName]);
    return $stmt->fetch();
}

// Admin functions
function getAllUsers() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
    return $stmt->fetchAll();
}

function getAllStaff() {
    global $pdo;
    $stmt = $pdo->query(
        "SELECT s.id AS staff_id, u.id AS user_id, u.name, u.email, u.phone, u.role, s.position, s.department, s.hire_date, s.employment_status, s.created_at AS staff_created_at " .
        "FROM staff s JOIN users u ON s.user_id = u.id ORDER BY s.created_at DESC"
    );
    return $stmt->fetchAll();
}

function getAllOrders($filters = []) {
    global $pdo;
    $query = "SELECT o.*, u.name as user_name FROM orders o JOIN users u ON o.user_id = u.id";
    $params = [];

    if (!empty($filters['date_from'])) {
        $query .= " AND DATE(o.created_at) >= ?";
        $params[] = $filters['date_from'];
    }
    if (!empty($filters['date_to'])) {
        $query .= " AND DATE(o.created_at) <= ?";
        $params[] = $filters['date_to'];
    }
    if (!empty($filters['order_type'])) {
        $query .= " AND o.order_type = ?";
        $params[] = $filters['order_type'];
    }
    if (!empty($filters['payment_method'])) {
        $query .= " AND o.payment_method = ?";
        $params[] = $filters['payment_method'];
    }
    if (!empty($filters['status'])) {
        $query .= " AND o.status = ?";
        $params[] = $filters['status'];
    }

    $query .= " ORDER BY o.created_at DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getAnalyticsData($dateFrom = null, $dateTo = null) {
    global $pdo;

    $orderDateCondition = "";
    $joinDateCondition = "";
    $params = [];
    if ($dateFrom && $dateTo) {
        $orderDateCondition = "WHERE DATE(orders.created_at) BETWEEN ? AND ?";
        $joinDateCondition = "WHERE DATE(o.created_at) BETWEEN ? AND ?";
        $params = [$dateFrom, $dateTo];
    }

    // Total revenue
    $stmt = $pdo->prepare("SELECT SUM(total_amount) as total_revenue FROM orders $orderDateCondition");
    $stmt->execute($params);
    $revenue = $stmt->fetch()['total_revenue'] ?? 0;

    // Order count
    $stmt = $pdo->prepare("SELECT COUNT(*) as order_count FROM orders $orderDateCondition");
    $stmt->execute($params);
    $orderCount = $stmt->fetch()['order_count'] ?? 0;

    // Average order value
    $avgOrderValue = $orderCount > 0 ? $revenue / $orderCount : 0;

    // Top selling products
    $stmt = $pdo->prepare("
        SELECT p.name, SUM(oi.quantity) as total_quantity
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        JOIN orders o ON oi.order_id = o.id
        $joinDateCondition
        GROUP BY p.id, p.name
        ORDER BY total_quantity DESC
        LIMIT 5
    ");
    $stmt->execute($params);
    $topProducts = $stmt->fetchAll();

    return [
        'total_revenue' => $revenue,
        'order_count' => $orderCount,
        'avg_order_value' => $avgOrderValue,
        'top_products' => $topProducts  
    ];
}

// Utility functions
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function generateOrderNumber() {
    return 'ORD' . date('YmdHis') . mt_rand(1000, 9999);
}

function calculateDiscount($totalAmount, $loyaltyTier, $userId = null) {
    $tier = getLoyaltyTierByName($loyaltyTier);

    // Special case: Bronze tier gets 5% off first order only
    if ($loyaltyTier === 'Bronze' && $userId !== null) {
        $userOrders = getUserOrders($userId);
        if (count($userOrders) === 0) {
            // First order for Bronze user
            return $totalAmount * 0.05;
        } else {
            // Not first order, no discount
            return 0;
        }
    }

    return $totalAmount * ($tier['discount_percentage'] / 100);
}

// PayMongo GCash Payment Functions
function createPayMongoGCashSource($amount, $description, $orderId) {
    // Validate amount (minimum 1 PHP = 100 centavos)
    if ($amount < 1) {
        return [
            'error' => true,
            'message' => 'Amount must be at least ₱1.00'
        ];
    }
    
    $url = PAYMONGO_BASE_URL . '/sources';
    
    // Construct proper base URL
    if (php_sapi_name() === 'cli') {
        // For CLI/testing, use localhost
        $baseUrl = 'http://localhost/newFriedays-website';
    } else {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '/newFriedays-website/index.php');
        $baseUrl = $protocol . "://" . $host . $scriptDir;
        $baseUrl = rtrim($baseUrl, '/');
    }
    
    $data = [
        'data' => [
            'attributes' => [
                'amount' => intval($amount * 100), // Convert to centavos
                'currency' => 'PHP',
                'type' => 'gcash',
                'redirect' => [
                    'success' => $baseUrl . '/index.php?page=payment_success&order_id=' . $orderId,
                    'failed' => $baseUrl . '/index.php?page=payment_failed&order_id=' . $orderId
                ],
                'billing' => [
                    'name' => 'Friedays Bocaue',
                    'email' => 'orders@friedaysbocaue.com'
                ]
            ]
        ]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Basic ' . base64_encode(PAYMONGO_SECRET_KEY . ':')
    ]);
    
    // Add timeout and SSL verification
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        return [
            'error' => true,
            'message' => 'Network error: ' . $curlError
        ];
    }

    if ($httpCode === 200 || $httpCode === 201) {
        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'error' => true,
                'message' => 'Invalid JSON response from PayMongo'
            ];
        }
        return $result['data'];
    } else {
        $errorData = json_decode($response, true);
        $errorMessage = isset($errorData['errors'][0]['detail']) ? $errorData['errors'][0]['detail'] : $response;
        
        error_log('PayMongo API Error: HTTP ' . $httpCode . ' - ' . $response);
    }
}

// PayMongo Checkout Payment Functions
function createPayMongoCheckout($amount, $description, $orderId) {
    // Validate amount (minimum 1 PHP = 100 centavos)
    if ($amount < 1) {
        return [
            'error' => true,
            'message' => 'Amount must be at least ₱1.00'
        ];
    }
    
    $url = PAYMONGO_BASE_URL . '/checkout_sessions';
    
    // Construct proper base URL
    if (php_sapi_name() === 'cli') {
        // For CLI/testing, use localhost
        $baseUrl = 'http://localhost/newFriedays-website';
    } else {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '/newFriedays-website/index.php');
        $baseUrl = $protocol . "://" . $host . $scriptDir;
        $baseUrl = rtrim($baseUrl, '/');
    }
    
    $data = [
        'data' => [
            'attributes' => [
                'amount' => intval($amount * 100), // Convert to centavos
                'currency' => 'PHP',
                'description' => $description,
                'line_items' => [
                    [
                        'amount' => intval($amount * 100),
                        'currency' => 'PHP',
                        'description' => $description,
                        'quantity' => 1
                    ]
                ],
                'payment_method_types' => ['gcash'],
                'success_url' => $baseUrl . '/index.php?page=payment_success&order_id=' . $orderId,
                'cancel_url' => $baseUrl . '/index.php?page=payment_failed&order_id=' . $orderId
            ]
        ]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Basic ' . base64_encode(PAYMONGO_SECRET_KEY . ':')
    ]);
    
    // Add timeout and SSL verification
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        return [
            'error' => true,
            'message' => 'Network error: ' . $curlError
        ];
    }

    if ($httpCode === 200 || $httpCode === 201) {
        $result = json_decode($response, true);
        error_log('PayMongo checkout response: ' . $response);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'error' => true,
                'message' => 'Invalid JSON response from PayMongo'
            ];
        }
        return $result['data'];
    } else {
        $errorData = json_decode($response, true);
        error_log('PayMongo checkout error response: ' . $response);
        $errorMessage = $response; // Show full response for debugging
        
        error_log('PayMongo API Error: HTTP ' . $httpCode . ' - ' . $response);
        return [
            'error' => true,
            'http_code' => $httpCode,
            'message' => 'PayMongo API Error: ' . $errorMessage
        ];
    }
}

function getPayMongoCheckoutSession($checkoutSessionId) {
    if (empty($checkoutSessionId)) {
        return [
            'error' => true,
            'message' => 'Missing PayMongo checkout session ID'
        ];
    }

    $url = PAYMONGO_BASE_URL . '/checkout_sessions/' . urlencode($checkoutSessionId);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Basic ' . base64_encode(PAYMONGO_SECRET_KEY . ':')
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        return [
            'error' => true,
            'message' => 'Network error: ' . $curlError
        ];
    }

    if ($httpCode === 200) {
        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'error' => true,
                'message' => 'Invalid JSON response from PayMongo when retrieving checkout session'
            ];
        }

        return $result['data'];
    }

    $errorData = json_decode($response, true);
    $errorMessage = isset($errorData['errors'][0]['detail']) ? $errorData['errors'][0]['detail'] : $response;
    return [
        'error' => true,
        'http_code' => $httpCode,
        'message' => 'PayMongo API Error: ' . $errorMessage
    ];
}

function createPendingOrder($userId, $orderType, $paymentMethod, $totalAmount, $cartItems, $deliveryAddressId = null, $deliveryAddress = null) {
    global $pdo;
    
    $deliveryAddressId = !empty($deliveryAddressId) ? $deliveryAddressId : null;
    $orderNumber = 'ORD' . date('Ymd') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
    
    $stmt = $pdo->prepare("INSERT INTO pending_orders (user_id, order_number, order_type, payment_method, total_amount, cart_items, delivery_address_id, delivery_address, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$userId, $orderNumber, $orderType, $paymentMethod, $totalAmount, json_encode($cartItems), $deliveryAddressId, $deliveryAddress]);
    
    return $pdo->lastInsertId();
}

function getPendingOrder($pendingOrderId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM pending_orders WHERE id = ?");
    $stmt->execute([$pendingOrderId]);
    return $stmt->fetch();
}

function completePendingOrder($pendingOrderId, $paymongoPaymentId = null, $paymongoLinkId = null) {
    global $pdo;
    
    $pendingOrder = getPendingOrder($pendingOrderId);
    if (!$pendingOrder) {
        return false;
    }
    
    // Create the actual order and include PayMongo tracking IDs
    $orderId = createOrder(
        $pendingOrder['user_id'],
        $pendingOrder['order_type'],
        $pendingOrder['payment_method'],
        $pendingOrder['total_amount'],
        json_decode($pendingOrder['cart_items'], true),
        $pendingOrder['delivery_address_id'],
        $pendingOrder['delivery_address'],
        $paymongoPaymentId,
        $paymongoLinkId
    );
    
    if ($orderId) {
        // Delete pending order
        $stmt = $pdo->prepare("DELETE FROM pending_orders WHERE id = ?");
        $stmt->execute([$pendingOrderId]);
        
        // Update user spending
        updateUserSpending($pendingOrder['user_id'], $pendingOrder['total_amount']);
        
        sendOrderConfirmationEmail($orderId);
        return $orderId;
    }
    
    return false;
}

function deletePendingOrder($pendingOrderId) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM pending_orders WHERE id = ?");
    $stmt->execute([$pendingOrderId]);
}
?>