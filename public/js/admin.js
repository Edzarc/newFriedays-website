// Admin dashboard JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Load dashboard stats
    loadDashboardStats();

    // Queue management
    const nextOrderBtn = document.getElementById('next-order-btn');
    const refreshQueueBtn = document.getElementById('refresh-queue-btn');

    if (nextOrderBtn) {
        nextOrderBtn.addEventListener('click', serveNextOrder);
    }

    if (refreshQueueBtn) {
        refreshQueueBtn.addEventListener('click', loadDashboardStats);
    }

    function loadDashboardStats() {
        fetch('../api/admin_stats.php')
            .then(response => response.json())
            .then(data => {
                document.getElementById('total-orders').textContent = data.total_orders;
                document.getElementById('total-revenue').textContent = formatCurrency(data.total_revenue);
                document.getElementById('total-users').textContent = data.total_users;
                document.getElementById('pending-orders').textContent = data.pending_orders;
                document.getElementById('admin-current-serving').textContent = data.current_serving ?
                    `Customer #${data.current_serving.queue_number}` : 'No orders being served';

                // Load recent orders
                loadRecentOrders();
            })
            .catch(error => console.error('Error loading stats:', error));
    }

    function loadRecentOrders() {
        fetch('../api/admin_recent_orders.php')
            .then(response => response.json())
            .then(data => {
                const recentOrders = document.getElementById('recent-orders');
                recentOrders.innerHTML = '';

                data.orders.slice(0, 5).forEach(order => {
                    const orderElement = document.createElement('div');
                    orderElement.className = 'recent-order-item';
                    orderElement.innerHTML = `
                        <div class="order-info">
                            <strong>${order.order_number}</strong> - ${order.user_name}
                        </div>
                        <div class="order-status status-${order.status.toLowerCase()}">
                            ${order.status}
                        </div>
                    `;
                    recentOrders.appendChild(orderElement);
                });
            })
            .catch(error => console.error('Error loading recent orders:', error));
    }

    function serveNextOrder() {
        fetch('../api/admin_serve_next.php', { method: 'POST' })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Next order served successfully!');
                    loadDashboardStats();
                } else {
                    showAlert('No orders waiting to be served.');
                }
            })
            .catch(error => console.error('Error serving next order:', error));
    }
});