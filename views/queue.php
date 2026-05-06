<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue Status - Friedays Bocaue</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="nav-container">
                <h1 class="logo">Friedays Bocaue</h1>
                <ul class="nav-menu">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="index.php?page=menu">Menu</a></li>
                    <li><a href="index.php?page=dashboard">Dashboard</a></li>
                    <li><a href="index.php?page=logout">Logout</a></li>
                </ul>
            </div>
        </nav>
    </header>

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
                    <a href="index.php?page=dashboard" class="btn btn-primary">View Dashboard</a>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Friedays Bocaue. All rights reserved.</p>
        </div>
    </footer>

    <script src="public/js/queue.js"></script>
</body>
</html>