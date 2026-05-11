<?php $pageTitle = 'Admin Dashboard - Friedays Bocaue'; include 'includes/header.php'; ?>

    <main>
        <div class="container">
            <div class="admin-dashboard">
                <h2>Admin Dashboard</h2>

                <div class="dashboard-grid">
                    <div class="dashboard-card">
                        <h3>Overall Quick Stats</h3>
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
                        <h3>Today's Quick Stats</h3>
                        <div class="stats-grid">
                            <div class="stat-item">
                                <span class="stat-number" id="today-orders">0</span>
                                <span class="stat-label">Today's Orders</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="today-revenue">₱0.00</span>
                                <span class="stat-label">Today's Revenue</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="today-users">0</span>
                                <span class="stat-label">New Users Today</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="today-pending-orders">0</span>
                                <span class="stat-label">Pending Orders Today</span>
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


            </div>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>
    <script src="public/js/admin.js"></script>