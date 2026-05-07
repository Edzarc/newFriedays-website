<?php $pageTitle = 'Checkout - Friedays Bocaue'; include 'includes/header.php'; ?>

    <main>
        <div class="container">
            <div class="checkout-container">
                <h2>Checkout</h2>
                <?php if (isset($error)): ?>
                    <div class="error-messages">
                        <p><?php echo $error; ?></p>
                    </div>
                <?php endif; ?>

                <form action="index.php?page=checkout" method="post" id="checkout-form">
                    <input type="hidden" name="cart_items" id="cart-items-input">
                    <input type="hidden" id="loyalty-discount" value="<?php echo isset($previewDiscountPercentage) ? $previewDiscountPercentage : 0; ?>">
                    <input type="hidden" id="loyalty-tier" value="<?php echo isset($user) ? htmlspecialchars($user['loyalty_tier']) : ''; ?>">
                    <input type="hidden" id="free-delivery-threshold" value="<?php echo isset($user) && $user['loyalty_tier'] === 'Silver' ? 500 : 0; ?>">

                    <div class="checkout-grid">
                        <div class="checkout-card summary-card">
                            <h3>Order Summary</h3>
                            <?php if (isset($user) && isset($loyaltyTier)): ?>
                                <div class="loyalty-info">
                                    <p><strong>Your Loyalty Tier:</strong> <?php echo htmlspecialchars($user['loyalty_tier']); ?></p>
                                    <?php if ($previewDiscountPercentage > 0): ?>
                                        <p><strong>Discount:</strong> <?php echo $previewDiscountPercentage; ?>% off subtotal</p>
                                    <?php endif; ?>
                                    <?php if ($user['loyalty_tier'] === 'Silver'): ?>
                                        <p><strong>Free Delivery:</strong> On orders over ₱500</p>
                                    <?php elseif (in_array($user['loyalty_tier'], ['Gold', 'Platinum'])): ?>
                                        <p><strong>Free Delivery:</strong> On all orders</p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <div id="order-summary">
                                <!-- Order summary will be populated by JavaScript -->
                            </div>
                            <div class="price-summary-card">
                                <h3>Price Summary</h3>
                                <div id="price-summary">
                                    <!-- Price summary will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>

                        <div class="checkout-card choices-card">
                            <h3>Order Options</h3>
                            <div class="choice-group">
                                <div class="choice-block">
                                    <h4>Order Type</h4>
                                    <div class="radio-grid">
                                        <label class="radio-pill"><input type="radio" name="order_type" value="Pickup" required> Pickup</label>
                                        <label class="radio-pill"><input type="radio" name="order_type" value="Dine In" required> Dine In</label>
                                        <label class="radio-pill"><input type="radio" name="order_type" value="Delivery" required> Delivery</label>
                                    </div>
                                </div>

                                <div class="choice-block">
                                    <h4>Payment Method</h4>
                                    <div class="radio-grid">
                                        <label class="radio-pill"><input type="radio" name="payment_method" value="Cash on Delivery" required> Cash on Delivery</label>
                                        <label class="radio-pill"><input type="radio" name="payment_method" value="GCash" required> GCash</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="checkout-actions">
                        <button type="submit" class="btn btn-primary btn-lg">Place Order</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>
    <script src="public/js/checkout.js"></script>