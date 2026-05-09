<?php $pageTitle = isset($success) ? 'Payment Successful' : 'Payment Failed'; include 'includes/header.php'; ?>

<main>
    <div class="container">
        <div class="payment-result-container">
            <?php if (isset($success)): ?>
                <div class="success-message">
                    <h2>✅ Payment Successful!</h2>
                    <p><?php echo htmlspecialchars($success); ?></p>
                    <p>You can track your order status in the <a href="index.php?page=queue">queue</a>.</p>
                    <a href="index.php?page=queue" class="btn btn-primary">View Order Status</a>
                </div>
                <script>
                    // Clear cart after successful payment
                    localStorage.removeItem('friedays_cart');
                    localStorage.removeItem('checkout_cart');
                </script>
            <?php elseif (isset($error)): ?>
                <div class="error-message">
                    <h2>❌ Payment Failed</h2>
                    <p><?php echo htmlspecialchars($error); ?></p>
                    <a href="index.php?page=menu" class="btn btn-primary">Back to Menu</a>
                    <a href="index.php?page=checkout" class="btn btn-secondary">Try Again</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<style>
.payment-result-container {
    max-width: 600px;
    margin: 2rem auto;
    padding: 2rem;
    text-align: center;
}

.success-message, .error-message {
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.success-message {
    background-color: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}

.error-message {
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}

.success-message h2, .error-message h2 {
    margin-bottom: 1rem;
    font-size: 1.5rem;
}

.success-message p, .error-message p {
    margin-bottom: 1.5rem;
    font-size: 1.1rem;
}

.btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    margin: 0.5rem;
    text-decoration: none;
    border-radius: 4px;
    font-weight: 500;
    transition: background-color 0.3s;
}

.btn-primary {
    background-color: #007bff;
    color: white;
    border: 1px solid #007bff;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
    border: 1px solid #6c757d;
}

.btn-secondary:hover {
    background-color: #545b62;
    border-color: #545b62;
}
</style>

<?php include 'includes/footer.php'; ?>