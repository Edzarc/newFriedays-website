<?php $pageTitle = 'Login - Friedays Bocaue'; include 'includes/header.php'; ?>

    <main>
        <div class="container">
            <div class="form-container">
                <h2>Login</h2>
                <?php if (isset($error)): ?>
                    <div class="error-messages">
                        <p><?php echo $error; ?></p>
                    </div>
                <?php endif; ?>
                <form action="index.php?page=login" method="post">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required value="<?php echo $_POST['email'] ?? ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div style="text-align: center; margin-bottom: 10px;">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </form>
                <p><a href="index.php?page=forgot_password">Forgot your password?</a></p>
                <p></p>Don't have an account? <a href="index.php?page=register">Register here</a></p>
            </div>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>