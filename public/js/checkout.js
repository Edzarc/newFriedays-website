// Checkout page specific JavaScript

function formatCurrency(amount) {
    return '₱' + parseFloat(amount).toFixed(2);
}

document.addEventListener('DOMContentLoaded', function() {
    // Load cart items from localStorage. checkout_cart is set when navigating from the menu.
    // If it is not available, fall back to the main friedays_cart storage.
    const rawCheckoutCart = localStorage.getItem('checkout_cart');
    const rawMainCart = localStorage.getItem('friedays_cart');
    const cartItems = JSON.parse(rawCheckoutCart || rawMainCart || '[]');
    console.log('Loaded cart items:', cartItems, 'checkout_cart present:', !!rawCheckoutCart, 'friedays_cart present:', !!rawMainCart);
    const cartInput = document.getElementById('cart-items-input');

    if (cartItems.length === 0) {
        showAlert('Your cart is empty. Please add items to your cart first.');
        window.location.href = 'index.php?page=menu';
        return;
    }

    console.log('Populating order summary...');
    // Populate order summary
    const orderSummary = document.getElementById('order-summary');
    const priceSummary = document.getElementById('price-summary');
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
    const discountPercentage = parseFloat(document.getElementById('loyalty-discount').value) || 0;
    const loyaltyTier = document.getElementById('loyalty-tier').value;
    const freeDeliveryThreshold = parseFloat(document.getElementById('free-delivery-threshold').value) || 0;
    const discount = subtotal * (discountPercentage / 100);
    const subtotalAfterDiscount = subtotal - discount;
    const tax = subtotalAfterDiscount * 0.12; // 12% VAT
    let deliveryFee = 0;
    let total = 0;

    // Function to calculate delivery fee
    function calculateDeliveryFee(orderType) {
        if (orderType !== 'Delivery') return 0;

        if (loyaltyTier === 'Gold' || loyaltyTier === 'Platinum') {
            return 0; // Free delivery for Gold and Platinum
        }

        if (loyaltyTier === 'Silver' && subtotalAfterDiscount > freeDeliveryThreshold) {
            return 0; // Free delivery for Silver over threshold
        }

        return 50; // Regular delivery fee
    }

    // Function to update price summary
    function updatePriceSummary(orderType) {
        deliveryFee = calculateDeliveryFee(orderType);
        total = subtotalAfterDiscount + tax + deliveryFee;

        let summaryHTML = `
            <div class="checkout-item">
                <span>Subtotal:</span>
                <span>${formatCurrency(subtotal)}</span>
            </div>`;

        if (discount > 0) {
            summaryHTML += `
            <div class="checkout-item">
                <span>Loyalty Discount (${discountPercentage}%):</span>
                <span>-${formatCurrency(discount)}</span>
            </div>
            <div class="checkout-item">
                <span>Subtotal after discount:</span>
                <span>${formatCurrency(subtotalAfterDiscount)}</span>
            </div>`;
        }

        summaryHTML += `
            <div class="checkout-item">
                <span>Tax (12%):</span>
                <span>${formatCurrency(tax)}</span>
            </div>`;

        if (orderType === 'Delivery') {
            summaryHTML += `
            <div class="checkout-item">
                <span>Delivery Fee:</span>
                <span>${deliveryFee > 0 ? formatCurrency(deliveryFee) : 'Free'}</span>
            </div>`;
        } else if (deliveryFee > 0) {
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
    const selectedOrderTypeElement = document.querySelector('input[name="order_type"]:checked');
    const initialOrderType = selectedOrderTypeElement ? selectedOrderTypeElement.value : '';
    updatePriceSummary(initialOrderType);

    const deliveryAddressSection = document.querySelector('.address-selection');
    const deliveryAddressSelect = document.getElementById('delivery_address_id');

    function updateDeliveryAddressField(orderType) {
        if (orderType === 'Delivery') {
            if (deliveryAddressSection) {
                deliveryAddressSection.style.display = 'block';
            }
            if (deliveryAddressSelect) {
                deliveryAddressSelect.required = true;
            }
        } else {
            if (deliveryAddressSection) {
                deliveryAddressSection.style.display = 'none';
            }
            if (deliveryAddressSelect) {
                deliveryAddressSelect.required = false;
            }
        }
    }

    function updatePaymentMethods(orderType) {
        const cashInput = document.querySelector('input[name="payment_method"]:not([value="GCash"])');
        const cashLabel = cashInput ? cashInput.parentElement : null;
        if (cashLabel) {
            if (orderType === 'Delivery') {
                cashInput.value = 'Cash on Delivery';
                cashLabel.childNodes[1].textContent = ' Cash on Delivery';
            } else {
                cashInput.value = 'Cash';
                cashLabel.childNodes[1].textContent = ' Cash';
            }
        }
    }

    updateDeliveryAddressField(initialOrderType);
    updatePaymentMethods(initialOrderType);

    // Add event listeners to order type radio buttons
    const orderTypeRadios = document.querySelectorAll('input[name="order_type"]');
    orderTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            updatePriceSummary(this.value);
            updateDeliveryAddressField(this.value);
            updatePaymentMethods(this.value);
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

        // For GCash payments, don't clear cart yet - it will be cleared after successful payment
        if (paymentMethod !== 'GCash') {
            localStorage.removeItem('friedays_cart');
            localStorage.removeItem('checkout_cart');
        }

        // Submit form
        checkoutForm.submit();
    });
});