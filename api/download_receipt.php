<?php
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    http_response_code(401);
    die('Unauthorized');
}

$orderId = $_GET['order_id'] ?? null;

if (!$orderId) {
    http_response_code(400);
    die('Order ID required');
}

global $pdo;
$stmt = $pdo->prepare("
    SELECT o.*, u.name as user_name, u.email as user_email, u.phone as user_phone
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.id = ? AND o.user_id = ?
");
$stmt->execute([$orderId, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    http_response_code(404);
    die('Order not found');
}

// Get order items
$stmt = $pdo->prepare("
    SELECT oi.*, p.name as product_name
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->execute([$orderId]);
$orderItems = $stmt->fetchAll();

// Generate HTML receipt
$receiptHTML = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; color: #333; }
        .receipt-container { max-width: 600px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #f97415; padding-bottom: 15px; }
        .header h1 { margin: 0; color: #f97415; }
        .header p { margin: 5px 0; font-size: 0.9em; color: #666; }
        .order-details { margin: 20px 0; }
        .detail-row { display: flex; justify-content: space-between; margin-bottom: 8px; }
        .detail-label { font-weight: bold; }
        .items-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .items-table th, .items-table td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        .items-table th { background: #f97415; color: white; font-weight: bold; }
        .items-table td { font-size: 0.95em; }
        .totals { margin: 20px 0; }
        .total-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 0.95em; }
        .grand-total { font-weight: bold; font-size: 1.1em; border-top: 2px solid #f97415; padding-top: 10px; margin-top: 10px; }
        .footer { text-align: center; margin-top: 30px; padding-top: 15px; border-top: 1px solid #ddd; font-size: 0.85em; color: #666; }
        .status-badge { display: inline-block; padding: 5px 10px; background: #f97415; color: white; border-radius: 3px; font-size: 0.9em; }
        @media print {
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="header">
            <h1>Friedays Bocaue</h1>
            <p>Digital Receipt</p>
        </div>

        <div class="order-details">
            <div class="detail-row">
                <span class="detail-label">Order Number:</span>
                <span>' . htmlspecialchars($order['order_number']) . '</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date & Time:</span>
                <span>' . date('F j, Y g:i A', strtotime($order['created_at'])) . '</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span><span class="status-badge">' . htmlspecialchars($order['status']) . '</span></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Customer:</span>
                <span>' . htmlspecialchars($order['user_name']) . '</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Email:</span>
                <span>' . htmlspecialchars($order['user_email']) . '</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Phone:</span>
                <span>' . htmlspecialchars($order['user_phone']) . '</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Order Type:</span>
                <span>' . htmlspecialchars($order['order_type']) . '</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Payment Method:</span>
                <span>' . htmlspecialchars($order['payment_method']) . '</span>
            </div>
            ' . ($order['order_type'] === 'Delivery' ? '<div class="detail-row"><span class="detail-label">Delivery Address:</span><span>' . nl2br(htmlspecialchars($order['delivery_address'])) . '</span></div>' : '') . '
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>';

$itemsTotal = 0;
foreach ($orderItems as $item) {
    $itemSubtotal = $item['quantity'] * $item['price_at_purchase'];
    $itemsTotal += $itemSubtotal;
    $receiptHTML .= '
                <tr>
                    <td>' . htmlspecialchars($item['product_name']) . '</td>
                    <td>' . $item['quantity'] . '</td>
                    <td>₱' . number_format($item['price_at_purchase'], 2) . '</td>
                    <td>₱' . number_format($itemSubtotal, 2) . '</td>
                </tr>';
}

$receiptHTML .= '
            </tbody>
        </table>

        <div class="totals">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>₱' . number_format($itemsTotal, 2) . '</span>
            </div>
            <div class="total-row">
                <span>Total Amount:</span>
                <span class="grand-total">₱' . number_format($order['total_amount'], 2) . '</span>
            </div>
        </div>

        <div class="footer">
            <p>Thank you for your order!</p>
            <p>Order ID: ' . htmlspecialchars($order['order_number']) . '</p>
            <p style="margin-top: 20px; font-size: 0.8em;">This is an automatically generated receipt. For queries, please contact us.</p>
        </div>
    </div>
</body>
</html>
';

// Set headers for PDF download
header('Content-Type: text/html; charset=utf-8');
header('Content-Disposition: attachment; filename="Receipt_' . htmlspecialchars($order['order_number']) . '.html"');

echo $receiptHTML;
?>
