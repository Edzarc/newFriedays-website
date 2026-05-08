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
function getAllProducts() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM products ORDER BY category, name");
    return $stmt->fetchAll();
}

function getProductsByCategory($category) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category = ? ORDER BY name");
    $stmt->execute([$category]);
    return $stmt->fetchAll();
}

function getProductById($productId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    return $stmt->fetch();
}

// Order functions
function createOrder($userId, $orderType, $paymentMethod, $totalAmount, $cartItems, $deliveryAddressId = null, $deliveryAddress = null) {
    global $pdo;

    // Generate order number
    $orderNumber = 'ORD' . date('Ymd') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

    // Convert 0 or falsy delivery addr ess ID to NULL to satisfy foreign key constraint
    $deliveryAddressId = !empty($deliveryAddressId) ? $deliveryAddressId : null;

    // Insert order
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, order_number, order_type, payment_method, total_amount, delivery_address_id, delivery_address) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $orderNumber, $orderType, $paymentMethod, $totalAmount, $deliveryAddressId, $deliveryAddress]);
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
        SELECT o.*, u.name as user_name
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.status = 'Preparing'
        ORDER BY o.updated_at DESC LIMIT 1
    ");
    $result = $stmt->fetch();
    return $result ? $result['order_number'] : null;
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
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $orderId]);
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
?>