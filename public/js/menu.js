// Menu page specific JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Cart is already initialized in main.js

    // Add to cart functionality is handled in main.js
    // Search and filter functionality is handled in main.js

    // Checkout button
    const checkoutBtn = document.getElementById('checkout-btn');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function() {
            // Store cart items in session/localStorage for checkout
            localStorage.setItem('checkout_cart', JSON.stringify(cart.getItems()));
            window.location.href = 'index.php?page=checkout';
        });
    }
});