<?php $pageTitle = 'Menu - Friedays Bocaue'; include 'includes/header.php'; ?>

    <main>
        <div class="menu-container">
            <div class="menu-content">
                <div class="menu-header">
                    <h2>Our Menu</h2>
                    <div class="menu-controls">
                        <input type="text" id="search-input" placeholder="Search products...">
                        <div class="category-filters">
                            <button class="category-btn active" data-category="all">All</button>
                            <?php foreach ($categories as $category): ?>
                                <button class="category-btn" data-category="<?php echo htmlspecialchars($category); ?>">
                                    <?php echo htmlspecialchars($category); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="products-grid" id="products-grid">
                    <?php foreach ($products as $product): ?>
                        <div class="product-card" data-category="<?php echo htmlspecialchars($product['category']); ?>" data-name="<?php echo htmlspecialchars(strtolower($product['name'])); ?>">
                        <div class="product-image">
                            <img src="public\images\Gemini_Generated_Image_uq0o0duq0o0duq0o.png" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 100%; height: auto; object-fit: cover;">
                        </div>
                            <div class="product-info">
                                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p class="category"><?php echo htmlspecialchars($product['category']); ?></p>
                                <p class="price">₱<?php echo number_format($product['price'], 2); ?></p>
                                <button class="btn btn-secondary add-to-cart" data-product-id="<?php echo $product['id']; ?>" data-name="<?php echo htmlspecialchars($product['name']); ?>" data-price="<?php echo $product['price']; ?>">
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="cart-sidebar" id="cart-sidebar">
                <div class="cart-header">
                    <h3>Your Cart</h3>
                    <span class="cart-count" id="cart-count">0</span>
                </div>
                <div class="cart-items" id="cart-items">
                    <!-- Cart items will be populated by JavaScript -->
                </div>
                <div class="cart-footer">
                    <div class="cart-total">
                        <strong>Total: <span id="cart-total">0.00</span></strong>
                    </div>
                    <div style="text-align: center;">
                        <a href="index.php?page=checkout" class="btn btn-primary" id="checkout-btn" style="display: none;">Checkout</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>
    <script src="public/js/menu.js"></script>