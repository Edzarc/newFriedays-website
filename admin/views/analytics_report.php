<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Report - <?php echo date('Y-m-d'); ?> - Friedays Bocaue</title>
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
                    <li><a href="index.php?page=admin_products">Products</a></li>
                    <li><a href="index.php?page=admin_analytics" class="active">Analytics</a></li>
                    <li><a href="index.php?page=logout">Logout</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <div class="container report-page">
            <div class="report-header">
                <div>
                    <h2>Analytics Report</h2>
                    <p>Generated on <?php echo date('F j, Y'); ?></p>
                </div>
                <button id="print-report" class="btn btn-primary">Print / Save PDF</button>
            </div>

            <div class="report-metadata">
                <div class="report-card">
                    <h3>Date Range</h3>
                    <p><strong>From:</strong> <?php echo date('F j, Y', strtotime($dateFrom)); ?></p>
                    <p><strong>To:</strong> <?php echo date('F j, Y', strtotime($dateTo)); ?></p>
                </div>
                <div class="report-card">
                    <h3>Summary</h3>
                    <p><strong>Total Revenue:</strong> ₱<?php echo number_format($analytics['total_revenue'], 2); ?></p>
                    <p><strong>Total Orders:</strong> <?php echo $analytics['order_count']; ?></p>
                    <p><strong>Avg Order Value:</strong> ₱<?php echo number_format($analytics['avg_order_value'], 2); ?></p>
                </div>
            </div>

            <div class="report-summary">
                <div class="report-card">
                    <h3>Top Selling Products</h3>
                    <?php foreach ($analytics['top_products'] as $product): ?>
                        <div class="product-rank">
                            <span class="product-name"><?php echo htmlspecialchars($product['name']); ?></span>
                            <span class="product-quantity"><?php echo $product['total_quantity']; ?> sold</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="report-chart-grid">
                <div class="report-card">
                    <h3>Daily Revenue</h3>
                    <canvas id="revenueChart"></canvas>
                </div>
                <div class="report-card">
                    <h3>Orders by Type</h3>
                    <canvas id="orderTypeChart"></canvas>
                    <div class="order-type-summary">
                        <h4>Order Type Values</h4>
                        <ul>
                            <?php foreach ($analytics['order_type_counts'] as $typeCount): ?>
                                <li><?php echo htmlspecialchars($typeCount['order_type']); ?>: <?php echo $typeCount['count']; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Friedays Bocaue. All rights reserved.</p>
        </div>
    </footer>

    <script src="public/js/admin-reports.js"></script>
</body>
</html>
