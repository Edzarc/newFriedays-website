<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Friedays Bocaue</title>
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="nav-container">
                <h1 class="logo">Friedays Bocaue - Admin</h1>
                <ul class="nav-menu">
                    <li><a href="index.php?page=admin" class="active">Dashboard</a></li>
                    <li><a href="../index.php?page=admin_orders">Orders</a></li>
                    <li><a href="../index.php?page=admin_users">Users</a></li>
                    <li><a href="../index.php?page=admin_analytics">Analytics</a></li>
                    <li><a href="../index.php?page=logout">Logout</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <div class="container">
            <div class="admin-dashboard">
                <h2>Admin Dashboard</h2>

                <div class="dashboard-grid">
                    <div class="dashboard-card">
                        <h3>Quick Stats</h3>
                        <div class="stats-grid">
                            <div class="stat-item">
                                <span class="stat-number" id="total-orders">0</span>
                                <span class="stat-label">Total Orders</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="total-revenue">₱0.00</span>
                                <span class="stat-label">Total Revenue</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="total-users">0</span>
                                <span class="stat-label">Registered Users</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="pending-orders">0</span>
                                <span class="stat-label">Pending Orders</span>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <h3>Recent Orders</h3>
                        <div id="recent-orders">
                            <!-- Recent orders will be loaded via AJAX -->
                        </div>
                    </div>
                </div>

                <div class="dashboard-card">
                    <h3>Queue Management</h3>
                    <div class="queue-management">
                        <div class="current-serving-admin">
                            <h4>Now Serving</h4>
                            <div class="serving-display" id="admin-current-serving">No orders being served</div>
                        </div>
                        <div class="queue-actions">
                            <button class="btn btn-secondary" id="next-order-btn">Serve Next Order</button>
                            <button class="btn btn-primary" id="refresh-queue-btn">Refresh Queue</button>
                        </div>
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

    <script src="../public/js/admin.js"></script>
</body>
</html>