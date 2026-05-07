<?php include 'includes/header.php'; ?>

    <main>
        <section class="hero">
            <div class="hero-content">
                <h2>Welcome to Friedays Bocaue</h2>
                <p>Delicious fried chicken and more. Order online for pickup, delivery, or dine-in!</p>
                <?php if (!isLoggedIn()): ?>
                    <a href="index.php?page=login" class="btn btn-primary">Get Started</a>
                <?php else: ?>
                    <a href="index.php?page=menu" class="btn btn-primary">Order Now</a>
                <?php endif; ?>
            </div>
        </section>

        <section class="features">
            <div class="container">
                <h3>Why Choose Friedays?</h3>
                <div class="features-grid">
                    <div class="feature-card">
                        <h4>Fast & Fresh</h4>
                        <p>Freshly prepared meals ready in minutes</p>
                    </div>
                    <div class="feature-card">
                        <h4>Loyalty Program</h4>
                        <p>Earn rewards with every order</p>
                    </div>
                    <div class="feature-card">
                        <h4>Easy Ordering</h4>
                        <p>Simple online ordering with real-time queue updates</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

<?php include 'includes/footer.php'; ?>