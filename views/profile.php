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
                </div>

                <div class="dashboard-grid">
                    <div class="dashboard-card">
                        <h3>Profile Information</h3>
                        <?php if (!empty($errors)): ?>
                            <div class="error-messages">
                                <?php foreach ($errors as $error): ?>
                                    <p><?php echo htmlspecialchars($error); ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($success)): ?>
                            <div class="success-message" style="background: #dcfce7; border: 1px solid #bbf7d0; color: #166534; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                                <?php echo htmlspecialchars($success); ?>
                            </div>
                        <?php endif; ?>

                        <form method="post" class="profile-form">
                            <input type="hidden" name="action" value="save_profile">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($user['name']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($user['email']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="tel" id="phone" name="phone" required value="<?php echo htmlspecialchars($user['phone']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea id="address" name="address" required><?php echo htmlspecialchars($user['address']); ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
        
                        <h4 style="margin-top: 2rem;">Change Password</h4>
                        <?php if (isset($_SESSION['change_password_token'])): ?>
                            <p>A verification code has been sent to your email. Enter it below along with your new password.</p>
                            <form method="post" class="profile-form">
                                <input type="hidden" name="action" value="confirm_change_password">
                                <div class="form-group">
                                    <label for="otp">Verification Code</label>
                                    <input type="text" id="otp" name="otp" required maxlength="6">
                                </div>
                                <div class="form-group">
                                    <label for="new_password">New Password</label>
                                    <input type="password" id="new_password" name="new_password" required>
                                </div>
                                <div class="form-group">
                                    <label for="confirm_password">Confirm New Password</label>
                                    <input type="password" id="confirm_password" name="confirm_password" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Change Password</button>
                            </form>
                        <?php else: ?>
                            <p>Click the button below to receive a verification code via email to change your password.</p>
                            <form method="post" class="profile-form">
                                <input type="hidden" name="action" value="request_change_password_otp">
                                <button type="submit" class="btn btn-primary">Request Password Change</button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <div class="dashboard-card">
                        <h3>Saved Delivery Addresses</h3>
                        <?php if (empty($addresses)): ?>
                            <p>No saved delivery addresses yet. Add one below to use Delivery checkout.</p>
                        <?php else: ?>
                            <div class="address-list">
                                <?php foreach ($addresses as $addressItem): ?>
                                    <div class="address-card">
                                        <div>
                                            <strong><?php echo htmlspecialchars($addressItem['label']); ?></strong>
                                            <p><?php echo nl2br(htmlspecialchars($addressItem['address'])); ?></p>
                                        </div>
                                        <form method="post" style="margin-top: 0.75rem;">
                                            <input type="hidden" name="action" value="delete_address">
                                            <input type="hidden" name="address_id" value="<?php echo $addressItem['id']; ?>">
                                            <button type="submit" class="btn btn-secondary btn-small" style="background: #f8d7da; color: #842029; border-color: #f5c2c7;">Delete</button>
                                        </form>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <h4 style="margin-top: 1.5rem;">Add New Address</h4>
                        <form method="post" class="profile-form">
                            <input type="hidden" name="action" value="add_address">
                            <div class="form-group">
                                <label for="address_label">Label</label>
                                <input type="text" id="address_label" name="address_label" required placeholder="Home, Work, etc.">
                            </div>
                            <div class="form-group">
                                <label for="address_text">Delivery Address</label>
                                <textarea id="address_text" name="address_text" required placeholder="Street, City, Province, ZIP"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Save Address</button>
                        </form>
                    </div>
                </div>

                <div class="dashboard-grid">
                    <div class="dashboard-card">                        
                        <h3>Quick Actions</h3>
                        <div class="quick-actions">
                            <a href="index.php?page=queue" class="btn btn-secondary">Check Queue</a>
                        </div>
                    </div>
                </div>

                

                <div class="dashboard-card">
                    <h3>Explore Loyalty Tiers</h3>
                    <div class="loyalty-tier-preview">
                        <div class="tier-preview-grid">
                            <?php foreach ($loyaltyTiers as $tier): ?>
                                <div class="tier-preview-card<?php echo $tier['tier_name'] === $user['loyalty_tier'] ? ' current-tier' : ''; ?>">
                                    <div class="tier-badge tier-<?php echo strtolower($tier['tier_name']); ?>">
                                        <?php echo htmlspecialchars($tier['tier_name']); ?>
                                    </div>
                                    <p><strong>Min Spend:</strong> ₱<?php echo number_format($tier['min_spending_threshold'], 2); ?></p>
                                    <p><strong>Discount:</strong> <?php echo $tier['discount_percentage']; ?>%</p>
                                    <p><?php echo htmlspecialchars($tier['benefits']); ?></p>
                                </div>
                            <?php endforeach; ?>
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