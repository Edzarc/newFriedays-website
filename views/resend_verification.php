<?php $pageTitle = 'Resend Verification Email - Friedays Bocaue'; include 'includes/header.php'; ?>

    <main>
        <div class="container">
            <div class="form-container">
                <h2>Resend Verification Email</h2>
                <p>Please enter your email address to resend the verification email.</p>
                <?php if (!empty($error)): ?>
                    <div class="error-messages">
                        <p><?php echo htmlspecialchars($error); ?></p>
                    </div>
                <?php endif; ?>
                <?php if (!empty($message)): ?>
                    <div class="success-message">
                        <p><?php echo htmlspecialchars($message); ?></p>
                    </div>
                <?php endif; ?>
                <form action="index.php?page=resend_verification" method="post">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Resend Verification Email</button>
                </form>
                <p><a href="index.php?page=login">Back to Login</a></p>
            </div>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>