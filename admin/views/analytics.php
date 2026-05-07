<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - Friedays Bocaue</title>
    <link rel="stylesheet" href="public/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                    <li><a href="index.php?page=admin_orders">Orders</a></li>
                    <li><a href="index.php?page=admin_users">Users</a></li>
                    <li><a href="index.php?page=admin_analytics" class="active">Analytics</a></li>
                    <li><a href="index.php?page=logout">Logout</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <div class="container">
            <div class="admin-analytics">
                <h2>Analytics & Reports</h2>

                <div class="analytics-filters">
                    <form action="index.php" method="get" class="filters-form">
                        <input type="hidden" name="page" value="admin_analytics">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label for="date_from">From Date</label>
                                <input type="date" id="date_from" name="date_from" value="<?php echo $dateFrom; ?>">
                            </div>
                            <div class="filter-group">
                                <label for="date_to">To Date</label>
                                <input type="date" id="date_to" name="date_to" value="<?php echo $dateTo; ?>">
                            </div>
                            <div class="filter-group">
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="analytics-grid">
                    <div class="analytics-card">
                        <h3>Key Metrics</h3>
                        <div class="metrics-grid">
                            <div class="metric-item">
                                <div class="metric-value">₱<?php echo number_format($analytics['total_revenue'], 2); ?></div>
                                <div class="metric-label">Total Revenue</div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-value"><?php echo $analytics['order_count']; ?></div>
                                <div class="metric-label">Total Orders</div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-value">₱<?php echo number_format($analytics['avg_order_value'], 2); ?></div>
                                <div class="metric-label">Avg Order Value</div>
                            </div>
                        </div>
                    </div>

                    <div class="analytics-card">
                        <h3>Top Selling Products</h3>
                        <div class="top-products">
                            <?php foreach ($analytics['top_products'] as $product): ?>
                                <div class="product-rank">
                                    <span class="product-name"><?php echo htmlspecialchars($product['name']); ?></span>
                                    <span class="product-quantity"><?php echo $product['total_quantity']; ?> sold</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="analytics-charts">
                    <div class="chart-container">
                        <h3>Daily Revenue</h3>
                        <canvas id="revenueChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h3>Orders by Type</h3>
                        <canvas id="orderTypeChart"></canvas>
                    </div>
                </div>

                <div class="export-actions">
                    <button class="btn btn-primary" id="export-analytics-csv">Export Analytics CSV</button>
                    <button class="btn btn-primary" id="export-analytics-pdf">Export Analytics PDF</button>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Friedays Bocaue. All rights reserved.</p>
        </div>
    </footer>

    <script src="public/js/admin-analytics.js"></script>
</body>
</html>