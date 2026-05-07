<?php $pageTitle = 'Queue Status - Friedays Bocaue'; include 'includes/header.php'; ?>

    <main>
        <div class="container">
            <div class="queue-container">
                <h2>Queue Status</h2>

                <div class="queue-status">
                    <div class="current-serving">
                        <h3>Now Serving</h3>
                        <div class="serving-number" id="current-serving">
                            <?php if ($currentServing): ?>
                                Customer #<?php echo $currentServing['queue_number']; ?>
                            <?php else: ?>
                                No orders being served
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="your-position">
                        <h3>Your Order</h3>
                        <div class="position-number" id="your-position">
                            <?php if ($userQueue): ?>
                                #<?php echo $userQueue['queue_number']; ?> - <?php echo ucfirst($userQueue['status']); ?>
                            <?php else: ?>
                                No active orders
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="queue-info">
                    <p>Your order is being prepared. We'll notify you when it's ready!</p>
                    <p><strong>Note:</strong> Queue updates automatically every 5 seconds.</p>
                </div>

                <div class="queue-actions">
                    <a href="index.php?page=menu" class="btn btn-secondary">Order More</a>
                    <a href="index.php?page=profile" class="btn btn-primary">View Profile</a>
                </div>
            </div>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>
    <script src="public/js/queue.js"></script>