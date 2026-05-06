<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Friedays Bocaue - Restaurant Ordering System</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="nav-container">
                <h1 class="logo">Friedays Bocaue</h1>
                <ul class="nav-menu">
                    <li><a href="index.php">Home</a></li>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="index.php?page=menu">Menu</a></li>
                        <li><a href="index.php?page=dashboard">Dashboard</a></li>
                        <?php if (isAdmin()): ?>
                            <li><a href="index.php?page=admin">Admin</a></li>
                        <?php endif; ?>
                        <li><a href="index.php?page=logout">Logout</a></li>
                    <?php else: ?>
                        <li><a href="index.php?page=login">Login</a></li>
                        <li><a href="index.php?page=register">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <section class="hero">
            <div class="hero-content">
                <h2>Welcome to Friedays Bocaue</h2>
                <p>Delicious fried chicken and more. Order online for pickup, delivery, or dine-in!</p>
                <?php if (!isLoggedIn()): ?>
                    <a href="index.php?page=register" class="btn btn-primary">Get Started</a>
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

    <footer>
        <div class="container">
            <p>&copy; 2024 Friedays Bocaue. All rights reserved.</p>
        </div>
    </footer>

    <script src="public/js/main.js"></script>
</body>
</html>