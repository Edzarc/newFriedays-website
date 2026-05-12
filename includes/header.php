<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Friedays Bocaue - Restaurant Ordering System'; ?></title>
    <link rel="icon" href="data:,">
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="page-<?php echo isset($_GET['page']) && $_GET['page'] !== '' ? htmlspecialchars($_GET['page']) : 'home'; ?>">
    <header>
        <nav>
            <div class="nav-container">
                <h1 class="logo"><?php echo (isset($_GET['page']) && strpos($_GET['page'], 'admin') === 0) ? 'Friedays Bocaue - Admin' : 'Friedays Bocaue'; ?></h1>
                <button class="mobile-menu-toggle" id="mobile-menu-toggle" aria-label="Toggle navigation menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <ul class="nav-menu" id="nav-menu">
                    <?php if (isset($_GET['page']) && strpos($_GET['page'], 'admin') === 0): ?>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="index.php?page=menu">Menu</a></li>
                        <li><a href="index.php?page=profile">Profile</a></li>
                        <li><a href="index.php?page=admin" <?php echo (isset($_GET['page']) && $_GET['page'] == 'admin') ? 'class="active"' : ''; ?>>Dashboard</a></li>
                        <li><a href="index.php?page=admin_orders" <?php echo (isset($_GET['page']) && $_GET['page'] == 'admin_orders') ? 'class="active"' : ''; ?>>Orders</a></li>
                        <li><a href="index.php?page=admin_users" <?php echo (isset($_GET['page']) && $_GET['page'] == 'admin_users') ? 'class="active"' : ''; ?>>Users</a></li>
                        <li><a href="index.php?page=admin_products" <?php echo (isset($_GET['page']) && $_GET['page'] == 'admin_products') ? 'class="active"' : ''; ?>>Products</a></li>
                        <li><a href="index.php?page=admin_analytics" <?php echo (isset($_GET['page']) && $_GET['page'] == 'admin_analytics') ? 'class="active"' : ''; ?>>Analytics</a></li>
                        <li><a href="index.php?page=logout">Logout</a></li>
                    <?php else: ?>
                        <li><a href="index.php" <?php echo (!isset($_GET['page']) || $_GET['page'] == '') ? 'class="active"' : ''; ?>>Home</a></li>
                        <?php if (isLoggedIn()): ?>
                            <li><a href="index.php?page=menu" <?php echo (isset($_GET['page']) && $_GET['page'] == 'menu') ? 'class="active"' : ''; ?>>Menu</a></li>
                            <li><a href="index.php?page=profile" <?php echo (isset($_GET['page']) && $_GET['page'] == 'profile') ? 'class="active"' : ''; ?>>Profile</a></li>
                            <?php if (isAdmin()): ?>
                                <li><a href="index.php?page=admin" <?php echo (isset($_GET['page']) && $_GET['page'] == 'admin') ? 'class="active"' : ''; ?>>Admin</a></li>
                            <?php elseif (isStaff()): ?>
                                <li><a href="index.php?page=staff" <?php echo (isset($_GET['page']) && $_GET['page'] == 'staff') ? 'class="active"' : ''; ?>>Staff</a></li>
                            <?php endif; ?>
                            <li><a href="index.php?page=logout">Logout</a></li>
                        <?php else: ?>
                            <li><a href="index.php?page=login" <?php echo (isset($_GET['page']) && $_GET['page'] == 'login') ? 'class="active"' : ''; ?>>Login</a></li>
                            <li><a href="index.php?page=register" <?php echo (isset($_GET['page']) && $_GET['page'] == 'register') ? 'class="active"' : ''; ?>>Register</a></li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>
    <button class="floating-cart-btn" id="floating-cart-btn" aria-label="Toggle shopping cart">
        🛒<span class="floating-cart-badge" id="floating-cart-count">0</span>
    </button>