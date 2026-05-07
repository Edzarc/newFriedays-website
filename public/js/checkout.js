// Checkout page specific JavaScript

function formatCurrency(amount) {
    return '₱' + parseFloat(amount).toFixed(2);
}

document.addEventListener('DOMContentLoaded', function() {
    // Load cart items from localStorage
    const cartItems = JSON.parse(localStorage.getItem('checkout_cart') || '[]');
    console.log('Loaded cart items:', cartItems);
    const cartInput = document.getElementById('cart-items-input');

    if (cartItems.length === 0) {
        showAlert('Your cart is empty. Please add items to your cart first.');
        window.location.href = 'index.php?page=menu';
        return;
    }

    console.log('Populating order summary...');
    // Populate order summary
    const orderSummary = document.getElementById('order-summary');
    console.log('Order summary element:', orderSummary);
    let subtotal = 0;

    cartItems.forEach(item => {
        const itemTotal = item.price * item.quantity;
        subtotal += itemTotal;

        const itemElement = document.createElement('div');
        itemElement.className = 'checkout-item';
        itemElement.innerHTML = `
            <span>${item.name} x ${item.quantity}</span>
            <span>${formatCurrency(itemTotal)}</span>
        `;
        orderSummary.appendChild(itemElement);
    });

    // Calculate totals (you might want to get user tier from server)
    const tax = subtotal * 0.12; // 12% VAT
    let deliveryFee = 0; // Initialize delivery fee to 0
    let total = subtotal + tax + deliveryFee;

    // Function to update price summary
    function updatePriceSummary() {
        const priceSummary = document.getElementById('price-summary');
        let summaryHTML = `
            <div class="checkout-item">
                <span>Subtotal:</span>
                <span>${formatCurrency(subtotal)}</span>
            </div>
            <div class="checkout-item">
                <span>Tax (12%):</span>
                <span>${formatCurrency(tax)}</span>
            </div>`;

        if (deliveryFee > 0) {
            summaryHTML += `
            <div class="checkout-item">
                <span>Delivery Fee:</span>
                <span>${formatCurrency(deliveryFee)}</span>
            </div>`;
        }

        summaryHTML += `
            <div class="checkout-item checkout-total">
                <span>Total:</span>
                <span>${formatCurrency(total)}</span>
            </div>`;

        priceSummary.innerHTML = summaryHTML;
    }

    // Initial price summary update
    updatePriceSummary();

    // Add event listeners to order type radio buttons
    const orderTypeRadios = document.querySelectorAll('input[name="order_type"]');
    orderTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            deliveryFee = this.value === 'Delivery' ? 50 : 0;
            total = subtotal + tax + deliveryFee;
            updatePriceSummary();
        });
    });

    // Set cart items in hidden input
    cartInput.value = JSON.stringify(cartItems);

    // Form submission
    const checkoutForm = document.getElementById('checkout-form');
    checkoutForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(checkoutForm);
        const orderType = formData.get('order_type');
        const paymentMethod = formData.get('payment_method');

        if (!orderType || !paymentMethod) {
            showAlert('Please select order type and payment method.');
            return;
        }

        // Submit form
        localStorage.removeItem('friedays_cart');
        localStorage.removeItem('checkout_cart');
        checkoutForm.submit();
    });
});