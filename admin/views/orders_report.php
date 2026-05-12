<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Report - <?php echo date('Y-m-d'); ?> - Friedays Bocaue</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="nav-container">
                <h1 class="logo">Friedays Bocaue - Admin</h1>
                <ul class="nav-menu">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="index.php?page=menu">Menu</a></li>
                    <li><a href="index.php?page=profile">Profile</a></li>
                    <li><a href="index.php?page=admin">Dashboard</a></li>
                    <li><a href="index.php?page=admin_orders" class="active">Orders</a></li>
                    <li><a href="index.php?page=admin_users">Users</a></li>
                    <li><a href="index.php?page=admin_products">Products</a></li>
                    <li><a href="index.php?page=admin_analytics">Analytics</a></li>
                    <li><a href="index.php?page=logout">Logout</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <div class="container report-page">
            <div class="report-header">
                <div>
                    <h2>Orders Report</h2>
                    <p>Generated on <?php echo date('F j, Y'); ?></p>
                </div>
                <button id="print-report" class="btn btn-primary">Print / Save PDF</button>
            </div>

            <div class="filters-section report-filters">
                <form method="get" action="index.php" class="filters-form">
                    <input type="hidden" name="page" value="admin_orders_report">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label for="date_from">From Date</label>
                            <input type="date" id="date_from" name="date_from" value="<?php echo htmlspecialchars($_GET['date_from'] ?? ''); ?>">
                        </div>
                        <div class="filter-group">
                            <label for="date_to">To Date</label>
                            <input type="date" id="date_to" name="date_to" value="<?php echo htmlspecialchars($_GET['date_to'] ?? ''); ?>">
                        </div>
                        <div class="filter-group">
                            <label for="order_type">Order Type</label>
                            <select id="order_type" name="order_type">
                                <option value="">All Types</option>
                                <option value="Pickup" <?php echo ($_GET['order_type'] ?? '') === 'Pickup' ? 'selected' : ''; ?>>Pickup</option>
                                <option value="Dine In" <?php echo ($_GET['order_type'] ?? '') === 'Dine In' ? 'selected' : ''; ?>>Dine In</option>
                                <option value="Delivery" <?php echo ($_GET['order_type'] ?? '') === 'Delivery' ? 'selected' : ''; ?>>Delivery</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="payment_method">Payment Method</label>
                            <select id="payment_method" name="payment_method">
                                <option value="">All Methods</option>
                                <option value="Cash on Delivery" <?php echo ($_GET['payment_method'] ?? '') === 'Cash on Delivery' ? 'selected' : ''; ?>>Cash on Delivery</option>
                                <option value="GCash" <?php echo ($_GET['payment_method'] ?? '') === 'GCash' ? 'selected' : ''; ?>>GCash</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="status">Status</label>
                            <select id="status" name="status">
                                <option value="">All Statuses</option>
                                <option value="Pending" <?php echo ($_GET['status'] ?? '') === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="Preparing" <?php echo ($_GET['status'] ?? '') === 'Preparing' ? 'selected' : ''; ?>>Preparing</option>
                                <option value="Ready" <?php echo ($_GET['status'] ?? '') === 'Ready' ? 'selected' : ''; ?>>Ready</option>
                                <option value="Completed" <?php echo ($_GET['status'] ?? '') === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="Cancelled" <?php echo ($_GET['status'] ?? '') === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <button type="submit" class="btn btn-secondary">Update Filters</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="report-metadata">
                <div class="report-card">
                    <h3>Report Range</h3>
                    <p><strong>From:</strong> <?php echo !empty($_GET['date_from']) ? date('F j, Y', strtotime($_GET['date_from'])) : 'Any'; ?></p>
                    <p><strong>To:</strong> <?php echo !empty($_GET['date_to']) ? date('F j, Y', strtotime($_GET['date_to'])) : 'Any'; ?></p>
                </div>
                <div class="report-card">
                    <h3>Filter Summary</h3>
                    <p><strong>Type:</strong> <?php echo !empty($_GET['order_type']) ? htmlspecialchars($_GET['order_type']) : 'All'; ?></p>
                    <p><strong>Payment:</strong> <?php echo !empty($_GET['payment_method']) ? htmlspecialchars($_GET['payment_method']) : 'All'; ?></p>
                    <p><strong>Status:</strong> <?php echo !empty($_GET['status']) ? htmlspecialchars($_GET['status']) : 'All'; ?></p>
                </div>
            </div>

            <div class="report-summary">
                <div class="report-card">
                    <h3>Total Orders</h3>
                    <p><?php echo $totalOrders; ?></p>
                </div>
                <div class="report-card">
                    <h3>Total Revenue</h3>
                    <p>₱<?php echo number_format($totalRevenue, 2); ?></p>
                </div>
            </div>

            <table class="report-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Payment</th>
                        <th>Delivery Address</th>
                        <th>Status</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                            <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                            <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                            <td><?php echo htmlspecialchars($order['order_type']); ?></td>
                            <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                            <td><?php echo $order['order_type'] === 'Delivery' ? nl2br(htmlspecialchars($order['delivery_address'])) : '-'; ?></td>
                            <td><?php echo htmlspecialchars($order['status']); ?></td>
                            <td>₱<?php echo number_format($order['total_amount'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Friedays Bocaue. All rights reserved.</p>
        </div>
    </footer>

    <script>
        document.getElementById('print-report').addEventListener('click', function() {
            window.print();
        });
    </script>
</body>
</html>
