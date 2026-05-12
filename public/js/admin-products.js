// Admin products management JavaScript

// Utility function for alerts
function showAlert(message, type = 'info') {
    alert(message);
}

// Store categories globally
let categoriesList = [];

document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('product-modal');
    const categoryModal = document.getElementById('category-modal');
    const manageCategoriesModal = document.getElementById('manage-categories-modal');
    const form = document.getElementById('product-form');
    const categoryForm = document.getElementById('category-form');
    const searchInput = document.getElementById('product-search');

    loadCategories();

    // Product search functionality
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();

            document.querySelectorAll('#products-tbody tr').forEach(row => {
                const name = row.cells[1].textContent.toLowerCase();
                const category = row.cells[2].textContent.toLowerCase();
                const description = row.cells[5].textContent.toLowerCase();
                const match = name.includes(searchTerm) || category.includes(searchTerm) || description.includes(searchTerm);
                row.style.display = match ? '' : 'none';
            });
        });
    }

    // Add product button
    document.getElementById('add-product-btn').addEventListener('click', function() {
        openModal();
    });

    // Manage categories button
    document.getElementById('manage-categories-btn').addEventListener('click', function() {
        loadCategoriesForManagement();
        manageCategoriesModal.style.display = 'block';
    });

    // Add new category button in product form
    document.getElementById('add-new-category-btn').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('category-modal-title').textContent = 'New Category';
        categoryModal.style.display = 'block';
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

    // Modal close buttons
    document.querySelector('.close').addEventListener('click', closeModal);
    document.querySelector('.category-close').addEventListener('click', closeCategoryModal);
    document.querySelector('.manage-categories-close').addEventListener('click', closeCategoriesModal);

    // Cancel buttons
    document.getElementById('cancel-btn').addEventListener('click', closeModal);
    document.getElementById('category-cancel-btn').addEventListener('click', closeCategoryModal);
    document.getElementById('manage-categories-close-btn').addEventListener('click', closeCategoriesModal);

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        saveProduct();
    });

    // Category form submission
    categoryForm.addEventListener('submit', function(e) {
        e.preventDefault();
        saveNewCategory();
    });

    // Close modals when clicking outside
    window.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
        if (e.target === categoryModal) {
            closeCategoryModal();
        }
        if (e.target === manageCategoriesModal) {
            closeCategoriesModal();
        }
    });
});

async function loadCategories() {
    try {
        const response = await fetch('api/admin_categories.php');
        const result = await response.json();

        if (result.success) {
            categoriesList = result.categories;
            updateCategoryDropdown();
        } else {
            console.error('Failed to load categories');
        }
    } catch (error) {
        console.error('Error loading categories:', error);
    }
}

function updateCategoryDropdown() {
    const categorySelect = document.getElementById('product-category');
    const currentValue = categorySelect.value;

    while (categorySelect.options.length > 1) {
        categorySelect.remove(1);
    }

    categoriesList.forEach(category => {
        const option = document.createElement('option');
        option.value = category.id;
        option.textContent = category.name;
        categorySelect.appendChild(option);
    });

    if (currentValue) {
        categorySelect.value = currentValue;
    }
}

function loadCategoriesForManagement() {
    const categoriesContainer = document.getElementById('categories-list');
    categoriesContainer.innerHTML = '';

    categoriesList.forEach(category => {
        const categoryDiv = document.createElement('div');
        categoryDiv.className = 'category-item';
        categoryDiv.style.cssText = 'display: flex; justify-content: space-between; align-items: center; padding: 10px; border-bottom: 1px solid #eee;';
        
        const nameSpan = document.createElement('span');
        nameSpan.textContent = category.name;
        
        const deleteBtn = document.createElement('button');
        deleteBtn.className = 'btn btn-danger btn-sm';
        deleteBtn.textContent = 'Delete';
        deleteBtn.addEventListener('click', function() {
            if (confirm(`Are you sure you want to delete the category "${category.name}"?`)) {
                deleteCategory(category.id);
            }
        });

        categoryDiv.appendChild(nameSpan);
        categoryDiv.appendChild(deleteBtn);
        categoriesContainer.appendChild(categoryDiv);
    });
}

function openModal(product = null) {
    const modal = document.getElementById('product-modal');
    const form = document.getElementById('product-form');
    const title = document.getElementById('modal-title');

    updateCategoryDropdown();

    if (product) {
        title.textContent = 'Edit Product';
        document.getElementById('product-id').value = product.id;
        document.getElementById('product-name').value = product.name;
        document.getElementById('product-category').value = product.category_id || '';
        document.getElementById('product-price').value = product.price;
        document.getElementById('product-image-url').value = product.image_url || '';
        document.getElementById('product-description').value = product.description || '';
    } else {
        title.textContent = 'Add New Product';
        form.reset();
        document.getElementById('product-id').value = '';
        document.getElementById('product-image-url').value = '';
    }

    modal.style.display = 'block';
}

function closeModal() {
    document.getElementById('product-modal').style.display = 'none';
}

function closeCategoryModal() {
    document.getElementById('category-modal').style.display = 'none';
    document.getElementById('category-form').reset();
}

function closeCategoriesModal() {
    document.getElementById('manage-categories-modal').style.display = 'none';
}

function editProduct(productId) {
    const row = document.querySelector(`tr[data-product-id="${productId}"]`);
    if (!row) return;

    const cells = row.cells;
    const product = {
        id: productId,
        name: cells[1].textContent.trim(),
        category: cells[2].textContent.trim(),
        price: parseFloat(cells[3].textContent.replace('₱', '').replace(',', '')),
        image_url: row.dataset.imageUrl || '',
        description: cells[5].textContent.trim()
    };

    const category = categoriesList.find(c => c.name === product.category);
    if (category) {
        product.category_id = category.id;
    }

    openModal(product);
}

async function saveProduct() {
    const formData = new FormData(document.getElementById('product-form'));
    const data = {
        product_id: formData.get('product_id'),
        name: formData.get('name'),
        category_id: parseInt(formData.get('category_id')),
        price: parseFloat(formData.get('price')),
        image_url: formData.get('image_url'),
        description: formData.get('description')
    };

    if (!data.category_id) {
        showAlert('Please select a category');
        return;
    }

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

async function saveNewCategory() {
    const categoryName = document.getElementById('category-name').value.trim();

    if (!categoryName) {
        showAlert('Please enter a category name');
        return;
    }

    try {
        const response = await fetch('api/admin_categories.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ name: categoryName })
        });

        const result = await response.json();

        if (result.success) {
            showAlert('Category added successfully');
            closeCategoryModal();
            await loadCategories();
            updateCategoryDropdown();
        } else {
            showAlert('Error: ' + result.message);
        }
    } catch (error) {
        showAlert('Error adding category: ' + error.message);
    }
}

async function deleteCategory(categoryId) {
    try {
        const response = await fetch(`api/admin_categories.php?id=${categoryId}`, {
            method: 'DELETE'
        });

        const result = await response.json();

        if (result.success) {
            showAlert('Category deleted successfully');
            await loadCategories();
            loadCategoriesForManagement();
        } else {
            showAlert('Error: ' + result.message);
        }
    } catch (error) {
        showAlert('Error deleting category: ' + error.message);
    }
}

