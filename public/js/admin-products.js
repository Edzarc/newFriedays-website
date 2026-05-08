// Admin products management JavaScript

// Utility function for alerts
function showAlert(message, type = 'info') {
    alert(message);
}

document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('product-modal');
    const form = document.getElementById('product-form');
    const searchInput = document.getElementById('product-search');

    // Product search functionality
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();

            document.querySelectorAll('#products-tbody tr').forEach(row => {
                const name = row.cells[1].textContent.toLowerCase();
                const category = row.cells[2].textContent.toLowerCase();
                const description = row.cells[4].textContent.toLowerCase();
                const match = name.includes(searchTerm) || category.includes(searchTerm) || description.includes(searchTerm);
                row.style.display = match ? '' : 'none';
            });
        });
    }

    // Add product button
    document.getElementById('add-product-btn').addEventListener('click', function() {
        openModal();
    });

    // Toggle availability buttons
    document.querySelectorAll('.toggle-availability').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const currentAvailable = parseInt(this.dataset.available);
            const newAvailable = 1 - currentAvailable; // Toggle between 0 and 1
            toggleProductAvailability(productId, newAvailable);
        });
    });

    // Edit product buttons
    document.querySelectorAll('.edit-product').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            editProduct(productId);
        });
    });

    // Delete product buttons
    document.querySelectorAll('.delete-product').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            if (confirm('Are you sure you want to delete this product?')) {
                deleteProduct(productId);
            }
        });
    });

    // Modal close button
    document.querySelector('.close').addEventListener('click', closeModal);

    // Cancel button
    document.getElementById('cancel-btn').addEventListener('click', closeModal);

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        saveProduct();
    });

    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });
});

function openModal(product = null) {
    const modal = document.getElementById('product-modal');
    const form = document.getElementById('product-form');
    const title = document.getElementById('modal-title');

    if (product) {
        title.textContent = 'Edit Product';
        document.getElementById('product-id').value = product.id;
        document.getElementById('product-name').value = product.name;
        document.getElementById('product-category').value = product.category;
        document.getElementById('product-price').value = product.price;
        document.getElementById('product-description').value = product.description || '';
    } else {
        title.textContent = 'Add New Product';
        form.reset();
        document.getElementById('product-id').value = '';
    }

    modal.style.display = 'block';
}

function closeModal() {
    document.getElementById('product-modal').style.display = 'none';
}

function editProduct(productId) {
    // Get product data from the table row
    const row = document.querySelector(`tr[data-product-id="${productId}"]`);
    if (!row) return;

    const cells = row.cells;
    const product = {
        id: productId,
        name: cells[1].textContent.trim(),
        category: cells[2].textContent.trim(),
        price: parseFloat(cells[3].textContent.replace('₱', '').replace(',', '')),
        description: cells[4].textContent.trim()
    };

    openModal(product);
}

async function saveProduct() {
    const formData = new FormData(document.getElementById('product-form'));
    const data = {
        product_id: formData.get('product_id'),
        name: formData.get('name'),
        category: formData.get('category'),
        price: parseFloat(formData.get('price')),
        description: formData.get('description')
    };

    const isEdit = data.product_id !== '';
    const method = isEdit ? 'PUT' : 'POST';

    try {
        const response = await fetch('api/admin_manage_products.php', {
            method: method,
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            showAlert(result.message);
            closeModal();
            location.reload(); // Refresh to show updated data
        } else {
            showAlert('Error: ' + result.message);
        }
    } catch (error) {
        showAlert('Error saving product: ' + error.message);
    }
}

async function deleteProduct(productId) {
    try {
        const response = await fetch(`api/admin_manage_products.php?id=${productId}`, {
            method: 'DELETE'
        });

        const result = await response.json();

        if (result.success) {
            showAlert(result.message);
            // Remove row from table
            const row = document.querySelector(`tr[data-product-id="${productId}"]`);
            if (row) row.remove();
        } else {
            showAlert('Error: ' + result.message);
        }
    } catch (error) {
        showAlert('Error deleting product: ' + error.message);
    }
}

async function toggleProductAvailability(productId, isAvailable) {
    try {
        const response = await fetch('api/admin_manage_products.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                product_id: productId,
                is_available: isAvailable,
                toggle_availability: true
            })
        });

        const result = await response.json();

        if (result.success) {
            showAlert(result.message);
            // Update the button and status badge
            const row = document.querySelector(`tr[data-product-id="${productId}"]`);
            if (row) {
                const button = row.querySelector('.toggle-availability');
                const statusBadge = row.querySelector('.status-badge');
                
                if (isAvailable) {
                    button.textContent = 'Mark Unavailable';
                    button.dataset.available = '1';
                    statusBadge.textContent = 'Available';
                    statusBadge.classList.remove('unavailable');
                    statusBadge.classList.add('available');
                } else {
                    button.textContent = 'Mark Available';
                    button.dataset.available = '0';
                    statusBadge.textContent = 'Unavailable';
                    statusBadge.classList.remove('available');
                    statusBadge.classList.add('unavailable');
                }
            }
        } else {
            showAlert('Error: ' + result.message);
        }
    } catch (error) {
        showAlert('Error updating product availability: ' + error.message);
    }
}