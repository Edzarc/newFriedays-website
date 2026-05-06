<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Friedays Bocaue</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="nav-container">
                <h1 class="logo">Friedays Bocaue</h1>
                <ul class="nav-menu">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="index.php?page=menu">Menu</a></li>
                    <li><a href="index.php?page=dashboard" class="active">Dashboard</a></li>
                    <?php if (isAdmin()): ?>
                        <li><a href="index.php?page=admin">Admin</a></li>
                    <?php endif; ?>
                    <li><a href="index.php?page=logout">Logout</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <div class="container">
            <div class="dashboard-container">
                <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h2>

                <div class="dashboard-grid">
                    <div class="dashboard-card">
                        <h3>Loyalty Status</h3>
                        <div class="loyalty-info">
                            <div class="tier-badge tier-<?php echo strtolower($user['loyalty_tier']); ?>">
                                <?php echo htmlspecialchars($user['loyalty_tier']); ?>
                            </div>
                            <p><strong>Total Spending:</strong> ₱<?php echo number_format($user['total_spending'], 2); ?></p>
                            <p><strong>Benefits:</strong> <?php echo htmlspecialchars($loyaltyTier['benefits']); ?></p>
                            <p><strong>Discount:</strong> <?php echo $loyaltyTier['discount_percentage']; ?>%</p>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <h3>Quick Actions</h3>
                        <div class="quick-actions">
                            <a href="index.php?page=menu" class="btn btn-primary">Order Now</a>
                            <a href="index.php?page=queue" class="btn btn-secondary">Check Queue</a>
                        </div>
                    </div>
                </div>

                <div class="dashboard-card">
                    <h3>Order History</h3>
                    <?php if (empty($orders)): ?>
                        <p>No orders yet. <a href="index.php?page=menu">Start ordering!</a></p>
                    <?php else: ?>
                        <div class="orders-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Payment</th>
                                        <th>Status</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                                            <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                                            <td><?php echo htmlspecialchars($order['order_type']); ?></td>
                                            <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                                            <td><span class="status status-<?php echo strtolower($order['status']); ?>"><?php echo htmlspecialchars($order['status']); ?></span></td>
                                            <td>₱<?php echo number_format($order['total_amount'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Friedays Bocaue. All rights reserved.</p>
        </div>
    </footer>

    <script src="public/js/main.js"></script>
</body>
</html>