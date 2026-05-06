<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Friedays Bocaue</title>
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
            <div class="checkout-container">
                <h2>Checkout</h2>
                <?php if (isset($error)): ?>
                    <div class="error-messages">
                        <p><?php echo $error; ?></p>
                    </div>
                <?php endif; ?>

                <form action="index.php?page=checkout" method="post" id="checkout-form">
                    <input type="hidden" name="cart_items" id="cart-items-input">

                    <div class="checkout-section">
                        <h3>Order Summary</h3>
                        <div id="order-summary">
                            <!-- Order summary will be populated by JavaScript -->
                        </div>
                    </div>

                    <div class="checkout-section">
                        <h3>Order Type</h3>
                        <div class="form-group">
                            <label><input type="radio" name="order_type" value="Pickup" required> Pickup</label>
                            <label><input type="radio" name="order_type" value="Dine In" required> Dine In</label>
                            <label><input type="radio" name="order_type" value="Delivery" required> Delivery</label>
                        </div>
                    </div>

                    <div class="checkout-section">
                        <h3>Payment Method</h3>
                        <div class="form-group">
                            <label><input type="radio" name="payment_method" value="Cash on Delivery" required> Cash on Delivery</label>
                            <label><input type="radio" name="payment_method" value="GCash" required> GCash</label>
                        </div>
                    </div>

                    <div class="checkout-section">
                        <h3>Price Summary</h3>
                        <div id="price-summary">
                            <!-- Price summary will be populated by JavaScript -->
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Place Order</button>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Friedays Bocaue. All rights reserved.</p>
        </div>
    </footer>

    <script src="public/js/checkout.js"></script>
</body>
</html>