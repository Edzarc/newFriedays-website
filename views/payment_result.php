<?php $pageTitle = isset($success) ? 'Payment Successful' : 'Payment Failed'; include 'includes/header.php'; ?>

<main>
    <div class="container">
        <div class="payment-result-container">
            <?php if (isset($success)): ?>
                <div class="success-message">
                    <h2>Payment Successful!</h2>
                    <p><?php echo htmlspecialchars($success); ?></p>
                    <p>You can track your order status in the queue.</p>
                    <a href="index.php?page=queue" class="btn btn-primary">View Order Status</a>
                </div>
                <script>
                    // Clear cart after successful payment
                    localStorage.removeItem('friedays_cart');
                    localStorage.removeItem('checkout_cart');

                    // Auto-download receipt if requested
                    <?php if (isset($_SESSION['download_receipt']) && $_SESSION['download_receipt']): ?>
                        const urlParams = new URLSearchParams(window.location.search);
                        const orderId = '<?php echo isset($orderId) ? htmlspecialchars($orderId) : ''; ?>' || urlParams.get('order_id');
                        if (orderId) {
                            setTimeout(function() {
                                const receiptLink = document.createElement('a');
                                receiptLink.href = 'api/download_receipt.php?order_id=' + encodeURIComponent(orderId);
                                receiptLink.click();
                            }, 500);
                        }
                        <?php unset($_SESSION['download_receipt']); ?>
                    <?php endif; ?>
                </script>
            <?php elseif (isset($error)): ?>
                <div class="error-message">
                    <h2>Payment Failed</h2>
                    <p><?php echo htmlspecialchars($error); ?></p>
                    <a href="index.php?page=menu" class="btn btn-primary">Back to Menu</a>
                    <a href="index.php?page=checkout" class="btn btn-secondary">Try Again</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>


<?php include 'includes/footer.php'; ?>