// Checkout page specific JavaScript

function formatCurrency(amount) {
    return '₱' + parseFloat(amount).toFixed(2);
}

// Load cart items from localStorage
const cartItems = JSON.parse(localStorage.getItem('checkout_cart') || '[]');
const cartInput = document.getElementById('cart-items-input');

if (cartItems.length === 0) {
    showAlert('Your cart is empty. Please add items to your cart first.');
    window.location.href = 'index.php?page=menu';
    return;
}

// Populate order summary
const orderSummary = document.getElementById('order-summary');
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
const deliveryFee = 50; // Fixed delivery fee
const total = subtotal + tax + deliveryFee;

// Populate price summary
const priceSummary = document.getElementById('price-summary');
priceSummary.innerHTML = `
    <div class="checkout-item">
        <span>Subtotal:</span>
        <span>${formatCurrency(subtotal)}</span>
    </div>
    <div class="checkout-item">
        <span>Tax (12%):</span>
        <span>${formatCurrency(tax)}</span>
    </div>
    <div class="checkout-item">
        <span>Delivery Fee:</span>
        <span>${formatCurrency(deliveryFee)}</span>
    </div>
    <div class="checkout-item checkout-total">
        <span>Total:</span>
        <span>${formatCurrency(total)}</span>
    </div>
`;

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
    checkoutForm.submit();
});