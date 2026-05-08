// Menu page specific JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Cart is already initialized in main.js

    // Add to cart functionality is handled in main.js
    // Search and filter functionality is handled in main.js

    // Checkout button
    const checkoutBtn = document.getElementById('checkout-btn');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Checkout button clicked, cart items:', cart.items);
            // Store cart items in localStorage for checkout
            localStorage.setItem('checkout_cart', JSON.stringify(cart.items));
            console.log('Set checkout_cart to localStorage');
            window.location.href = 'index.php?page=checkout';
        });
    }
});