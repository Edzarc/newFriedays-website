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
                        <div class="position-number" id="your-position" data-status="<?php echo htmlspecialchars($userQueue['order_status'] ?? $userQueue['status'] ?? 'none'); ?>">
                            <?php echo getQueuePositionHtml($userQueue); ?>
                        </div>
                    </div>
                </div>

                <div class="queue-info">
                    <?php echo getQueueInfoHtml($userQueue); ?>
                </div>

                <div class="queue-actions">
                    <a href="index.php?page=menu" class="btn btn-primary">Order More</a>
                    <a href="index.php?page=profile" class="btn btn-secondary">View Profile</a>
                </div>
            </div>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>
    <script src="public/js/queue.js"></script>