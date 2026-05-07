// Friedays Bocaue - Main JavaScript

// Utility functions
function showAlert(message, type = 'info') {
    // Simple alert for now - could be enhanced with toast notifications
    alert(message);
}

function formatCurrency(amount) {
    return '₱' + parseFloat(amount).toFixed(2);
}

// Cart management using localStorage
class Cart {
    constructor() {
        this.items = JSON.parse(localStorage.getItem('friedays_cart')) || [];
        this.updateUI();
    }

    addItem(productId, name, price) {
        const normalizedId = String(productId);
        const existingItem = this.items.find(item => String(item.id) === normalizedId);
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            this.items.push({
                id: normalizedId,
                name: name,
                price: parseFloat(price),
                quantity: 1
            });
        }
        this.save();
        this.updateUI();
    }

    removeItem(productId) {
        const normalizedId = String(productId);
        this.items = this.items.filter(item => String(item.id) !== normalizedId);
        this.save();
        this.updateUI();
    }

    updateQuantity(productId, quantity) {
        const normalizedId = String(productId);
        if (quantity <= 0) {
            this.removeItem(normalizedId);
            return;
        }
        const item = this.items.find(item => String(item.id) === normalizedId);
        if (item) {
            item.quantity = quantity;
            this.save();
            this.updateUI();
        }
    }

    getTotal() {
        return this.items.reduce((total, item) => total + (item.price * item.quantity), 0);
    }

    getItemCount() {
        return this.items.reduce((count, item) => count + item.quantity, 0);
    }

    clear() {
        this.items = [];
        this.save();
        this.updateUI();
    }

    save() {
        localStorage.setItem('friedays_cart', JSON.stringify(this.items));
    }

    updateUI() {
        // Update cart count
        const cartCount = document.getElementById('cart-count');
        if (cartCount) {
            cartCount.textContent = this.getItemCount();
        }

        // Update cart items
        const cartItems = document.getElementById('cart-items');
        if (cartItems) {
            cartItems.innerHTML = '';
            this.items.forEach(item => {
                const itemElement = document.createElement('div');
                itemElement.className = 'cart-item';
                itemElement.innerHTML = `
                    <div class="cart-item-info">
                        <div class="cart-item-name">${item.name}</div>
                        <div class="cart-item-price">${formatCurrency(item.price)}</div>
                    </div>
                    <div class="cart-item-controls">
                        <button type="button" class="quantity-btn" onclick="cart.updateQuantity('${item.id}', ${item.quantity - 1})">-</button>
                        <span>${item.quantity}</span>
                        <button type="button" class="quantity-btn" onclick="cart.updateQuantity('${item.id}', ${item.quantity + 1})">+</button>
                        <button type="button" class="remove-btn" onclick="cart.removeItem('${item.id}')">&times;</button>
                    </div>
                `;
                cartItems.appendChild(itemElement);
            });
        }

        // Update cart total
        const cartTotal = document.getElementById('cart-total');
        if (cartTotal) {
            cartTotal.textContent = formatCurrency(this.getTotal());
        }

        // Show/hide checkout button
        const checkoutBtn = document.getElementById('checkout-btn');
        if (checkoutBtn) {
            checkoutBtn.style.display = this.items.length > 0 ? 'inline-block' : 'none';
        }
    }

    getItems() {
        return this.items;
    }
}

function shouldClearCart() {
    return new URLSearchParams(window.location.search).get('clear_cart') === '1';
}

function clearCartStorage() {
    localStorage.removeItem('friedays_cart');
    localStorage.removeItem('checkout_cart');
}

if (shouldClearCart()) {
    clearCartStorage();
}

// Initialize cart
const cart = new Cart();

// Make cart available globally
window.cart = cart;

// Form validation
function validateForm(formData) {
    const errors = [];

    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (formData.email && !emailRegex.test(formData.email)) {
        errors.push('Please enter a valid email address.');
    }

    // Password validation
    if (formData.password && formData.password.length < 6) {
        errors.push('Password must be at least 6 characters long.');
    }

    // Confirm password
    if (formData.password && formData.confirm_password && formData.password !== formData.confirm_password) {
        errors.push('Passwords do not match.');
    }

    return errors;
}

// AJAX helper
function makeRequest(url, method = 'GET', data = null) {
    return fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
        },
        body: data ? JSON.stringify(data) : null
    })
    .then(response => response.json())
    .catch(error => {
        console.error('Request failed:', error);
        showAlert('An error occurred. Please try again.');
    });
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Add to cart buttons
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const name = this.dataset.name;
            const price = this.dataset.price;
            cart.addItem(productId, name, price);
        });
    });

    // Search functionality
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            document.querySelectorAll('.product-card').forEach(card => {
                const name = card.querySelector('h3').textContent.toLowerCase();
                card.style.display = name.includes(searchTerm) ? 'block' : 'none';
            });
        });
    }

    // Category filters
    document.querySelectorAll('.category-btn').forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            document.querySelectorAll('.category-btn').forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');

            const category = this.dataset.category;
            document.querySelectorAll('.product-card').forEach(card => {
                if (category === 'all' || card.dataset.category === category) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});