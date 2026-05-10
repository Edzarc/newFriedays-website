<?php $pageTitle = 'Resend Verification Email - Friedays Bocaue'; include 'includes/header.php'; ?>

    <main>
        <div class="container">
            <div class="form-container">
                <h2>Resend Verification Email</h2>
                <?php if (!empty($error)): ?>
                    <div class="error-messages">
                        <p><?php echo htmlspecialchars($error); ?></p>
                    </div>
                <?php endif; ?>
                <?php if (!empty($message)): ?>
                    <div class="success-messages">
                        <p><?php echo $message; ?></p>
                    </div>
                <?php endif; ?>
                <?php if (!empty($email)): ?>
                    <p>A verification email will be sent to <strong><?php echo htmlspecialchars($email); ?></strong>.</p>
                    <form action="index.php?page=resend_verification" method="post">
                        <button type="submit" class="btn btn-primary">Resend Verification Email</button>
                    </form>
                <?php else: ?>
                    <p>Please register or log in again to start email verification.</p>
                <?php endif; ?>
                <p><a href="index.php?page=login">Back to Login</a></p>
            </div>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>