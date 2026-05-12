<?php $pageTitle = 'Verify Email - Friedays Bocaue'; include 'includes/header.php'; ?>

    <main>
        <div class="container">
            <div class="form-container">
                <h2>Email Verification</h2>
                <?php if (!empty($verificationSuccess)): ?>
                    <div class="success-message">
                        <p>Your email has been verified successfully. You can now <a href="index.php?page=login">log in</a>.</p>
                    </div>
                <?php else: ?>
                    <div class="error-messages">
                        <p><?php echo htmlspecialchars($verificationError ?: 'Unable to verify your email.'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>