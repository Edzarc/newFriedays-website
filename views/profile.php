<?php $pageTitle = 'Profile - Friedays Bocaue'; include 'includes/header.php'; ?>

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

<?php include 'includes/footer.php'; ?>