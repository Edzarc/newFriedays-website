<?php $pageTitle = 'Staff Dashboard - Friedays Bocaue'; include __DIR__ . '/../../includes/header.php'; ?>

    <main>
        <div class="container">
            <div class="admin-dashboard">
                <h2 style="font-size: 3rem; text-align: center;">Staff Queue Management</h2>

                <div class="dashboard-card">
                    <h3>Order Status Management</h3>
                    <div class="queue-management">
                        <div class="current-serving-admin">
                            <h4>Now Serving</h4>
                            <div class="serving-display" id="staff-current-serving">No orders being served</div>
                        </div>
                        <div class="queue-actions">
                            <button class="btn btn-secondary" id="next-order-btn">Serve Next Order</button>
                            <button class="btn btn-primary" id="refresh-queue-btn">Refresh Queue</button>
                        </div>
                    </div>
                    <div style="margin-top:10px";>
                        <h4>Pending Orders</h4>
                        <div class="table-actions">
                            <button class="btn btn-success" id="process-selected-btn">Process Selected Orders</button>
                            <button class="btn btn-danger btn-small" id="cancel-selected-btn">Cancel Selected</button>
                            <button class="btn btn-secondary" id="select-all-btn">Toggle Select All</button>
                        </div>

                        <div class="table-container">
                            <table class="orders-table" id="staff-queue-table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Order #</th>
                                        <th>Customer</th>
                                        <th>Order Type</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="staff-queue-body">
                                    <tr>
                                        <td colspan="6">Loading pending orders...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                
                    <div style="margin-top:10px";>
                    	<div class="status-table">
                            <h4>Preparing Orders</h4>
                            <div class="table-actions">
                                <button class="btn btn-success btn-small bulk-process-preparing">Process Selected to Ready</button>
                                <button class="btn btn-danger btn-small bulk-cancel-preparing">Cancel Selected</button>
                                <button class="btn btn-secondary btn-small select-all-preparing">Select All</button>
                            </div>
                            <div class="table-container">
                                <table class="orders-table" id="preparing-orders-table">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Order #</th>
                                            <th>Customer</th>
                                            <th>Order Type</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="preparing-orders-body">
                                        <tr>
                                            <td colspan="6">Loading...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="status-tables-grid">
                        <div class="status-table">
                            <h4>Ready Orders</h4>
                            <div class="table-actions">
                                <button class="btn btn-success btn-small bulk-process-ready">Mark Selected as Completed</button>
                                <button class="btn btn-danger btn-small bulk-cancel-ready">Cancel Selected</button>
                                <button class="btn btn-secondary btn-small select-all-ready">Select All</button>
                            </div>
                            <div class="table-container">
                                <table class="orders-table" id="ready-orders-table">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Order #</th>
                                            <th>Customer</th>
                                            <th>Order Type</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ready-orders-body">
                                        <tr>
                                            <td colspan="6">Loading...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="status-table">
                            <h4>Completed Orders</h4>
                            <div class="table-container">
                                <table class="orders-table" id="completed-orders-table">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Order #</th>
                                            <th>Customer</th>
                                            <th>Order Type</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="completed-orders-body">
                                        <tr>
                                            <td colspan="6">Loading...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="status-table">
                            <h4>Cancelled Orders</h4>
                            <div class="table-container">
                                <table class="orders-table" id="cancelled-orders-table">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Order #</th>
                                            <th>Customer</th>
                                            <th>Order Type</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cancelled-orders-body">
                                        <tr>
                                            <td colspan="6">Loading...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
<script src="public/js/staff.js"></script>
