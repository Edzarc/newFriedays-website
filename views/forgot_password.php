<?php $pageTitle = 'Forgot Password - Friedays Bocaue'; include 'includes/header.php'; ?>

    <main>
        <div class="container">
            <div class="form-container">
                <h2>Forgot Password</h2>

                <?php if (!empty($errors)): ?>
                    <div class="error-messages">
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo htmlspecialchars($error); ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($message)): ?>
                    <div class="success-messages">
                        <p><?php echo htmlspecialchars($message); ?></p>
                    </div>
                <?php endif; ?>

                <?php if (empty($showOtpForm)): ?>
                    <p>Enter the email address for your account and we'll send you a one-time code to reset your password.</p>
                    <form action="index.php?page=forgot_password" method="post">
                        <input type="hidden" name="action" value="send_otp">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email ?? ''); ?>">
                        </div>
                        <div style="text-align: center; margin-bottom: 10px;">
                            <button type="submit" class="btn btn-primary">Send Reset Code</button>
                        </div>
                    </form>
                <?php else: ?>
                    <p>An OTP has been sent to <strong><?php echo htmlspecialchars($email); ?></strong>. Enter it below along with your new password.</p>
                    <form action="index.php?page=forgot_password" method="post">
                        <input type="hidden" name="action" value="reset_password">
                        <input type="hidden" name="reset_token" value="<?php echo htmlspecialchars($resetToken); ?>">
                        <div class="form-group">
                            <label for="otp">OTP Code</label>
                            <input type="text" id="otp" name="otp" required value="<?php echo htmlspecialchars($_POST['otp'] ?? ''); ?>" maxlength="6">
                        </div>
                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>
                        <div style="text-align: center; margin-bottom: 10px;">
                            <button type="submit" class="btn btn-primary">Change Password</button>
                        </div>
                    </form>
                <?php endif; ?>

                <p><a href="index.php?page=login">Return to Login</a></p>
            </div>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>
