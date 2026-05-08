<?php $pageTitle = 'Products Management - Friedays Bocaue'; include 'includes/header.php'; ?>

    <main>
        <div class="container">
            <div class="admin-products">
                <h2>Product Management</h2>

                <div class="product-actions">
                    <button class="btn btn-primary" id="add-product-btn">Add New Product</button>
                    <div class="search-container">
                        <input type="text" id="product-search" placeholder="Search products...">
                    </div>
                </div>

                <div class="products-table-container">
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="products-tbody">
                            <?php foreach ($products as $product): ?>
                            <tr data-product-id="<?php echo $product['id']; ?>">
                                <td><?php echo $product['id']; ?></td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars($product['category']); ?></td>
                                <td>₱<?php echo number_format($product['price'], 2); ?></td>
                                <td><?php echo htmlspecialchars($product['description'] ?? ''); ?></td>
                                <td>
                                    <span class="status-badge <?php echo ($product['is_available'] ?? 1) ? 'available' : 'unavailable'; ?>">
                                        <?php echo ($product['is_available'] ?? 1) ? 'Available' : 'Unavailable'; ?>
                                    </span>
                                </td>
                                <td class="actions">
                                    <button class="btn btn-secondary btn-sm toggle-availability" data-product-id="<?php echo $product['id']; ?>" data-available="<?php echo ($product['is_available'] ?? 1) ? 1 : 0; ?>">
                                        <?php echo ($product['is_available'] ?? 1) ? 'Mark Unavailable' : 'Mark Available'; ?>
                                    </button>
                                    <button class="btn btn-secondary btn-sm edit-product" data-product-id="<?php echo $product['id']; ?>">Edit</button>
                                    <button class="btn btn-danger btn-sm delete-product" data-product-id="<?php echo $product['id']; ?>">Delete</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Add/Edit Product Modal -->
    <div id="product-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-title">Add New Product</h3>
                <span class="close">&times;</span>
            </div>
            <form id="product-form">
                <input type="hidden" id="product-id" name="product_id">

                <div class="form-group">
                    <label for="product-name">Product Name *</label>
                    <input type="text" id="product-name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="product-category">Category *</label>
                    <select id="product-category" name="category" required>
                        <option value="">Select Category</option>
                        <option value="Chicken & Fried Items">Chicken & Fried Items</option>
                        <option value="Sides & Sandwiches">Sides & Sandwiches</option>
                        <option value="Beverages">Beverages</option>
                        <option value="Pasta & Mains">Pasta & Mains</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="product-price">Price *</label>
                    <input type="number" id="product-price" name="price" step="0.01" min="0" required>
                </div>

                <div class="form-group">
                    <label for="product-description">Description</label>
                    <textarea id="product-description" name="description" rows="3"></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save Product</button>
                    <button type="button" class="btn btn-secondary" id="cancel-btn">Cancel</button>
                </div>
            </form>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>
    <script src="public/js/admin-products.js"></script>