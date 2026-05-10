// Admin dashboard JavaScript

console.log('Admin.js loaded');

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOMContentLoaded fired');
    console.log('formatCurrency function available:', typeof formatCurrency);
    console.log('showAlert function available:', typeof showAlert);
    
    // Load dashboard stats
    console.log('Calling loadDashboardStats');
    loadDashboardStats();

    // Queue management
    const nextOrderBtn = document.getElementById('next-order-btn');
    const refreshQueueBtn = document.getElementById('refresh-queue-btn');
    
    console.log('Next order button found:', !!nextOrderBtn);
    console.log('Refresh queue button found:', !!refreshQueueBtn);

    if (nextOrderBtn) {
        nextOrderBtn.addEventListener('click', serveNextOrder);
    }

    if (refreshQueueBtn) {
        refreshQueueBtn.addEventListener('click', loadDashboardStats);
    }

    function loadDashboardStats() {
        console.log('loadDashboardStats called');
        const url = 'api/admin_stats.php';
        console.log('Fetching from:', url);
        
        fetch(url)
            .then(response => {
                console.log('Response received, status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Data received:', data);
                
                const totalOrdersEl = document.getElementById('total-orders');
                const totalRevenueEl = document.getElementById('total-revenue');
                const totalUsersEl = document.getElementById('total-users');
                const pendingOrdersEl = document.getElementById('pending-orders');
                const todayOrdersEl = document.getElementById('today-orders');
                const todayRevenueEl = document.getElementById('today-revenue');
                const todayUsersEl = document.getElementById('today-users');
                const todayPendingOrdersEl = document.getElementById('today-pending-orders');
                
                console.log('Elements found:', {
                    totalOrders: !!totalOrdersEl,
                    totalRevenue: !!totalRevenueEl,
                    totalUsers: !!totalUsersEl,
                    pendingOrders: !!pendingOrdersEl,
                    todayOrders: !!todayOrdersEl,
                    todayRevenue: !!todayRevenueEl,
                    todayUsers: !!todayUsersEl,
                    todayPendingOrders: !!todayPendingOrdersEl
                });
                
                if (totalOrdersEl) totalOrdersEl.textContent = data.total_orders;
                if (totalRevenueEl) totalRevenueEl.textContent = formatCurrency(data.total_revenue);
                if (totalUsersEl) totalUsersEl.textContent = data.total_users;
                if (pendingOrdersEl) pendingOrdersEl.textContent = data.pending_orders;
                if (todayOrdersEl) todayOrdersEl.textContent = data.today_orders;
                if (todayRevenueEl) todayRevenueEl.textContent = formatCurrency(data.today_revenue);
                if (todayUsersEl) todayUsersEl.textContent = data.today_users;
                if (todayPendingOrdersEl) todayPendingOrdersEl.textContent = data.today_pending_orders;
                
                const servingEl = document.getElementById('admin-current-serving');
                if (servingEl) {
                    servingEl.textContent = data.current_serving ?
                        `Customer #${data.current_serving.queue_number}` : 'No orders being served';
                }

                // Load recent orders
                console.log('Calling loadRecentOrders');
                loadRecentOrders();
            })
            .catch(error => console.error('Error loading stats:', error));
    }

    function loadRecentOrders() {
        console.log('loadRecentOrders called');
        const url = 'api/admin_recent_orders.php';
        console.log('Fetching from:', url);
        
        fetch(url)
            .then(response => {
                console.log('Response received, status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Recent orders data received:', data);
                const recentOrders = document.getElementById('recent-orders');
                console.log('Recent orders element found:', !!recentOrders);
                
                if (recentOrders) {
                    recentOrders.innerHTML = '';

                    if (data.orders && Array.isArray(data.orders)) {
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
                        console.log('Added', Math.min(data.orders.length, 5), 'recent orders');
                    }
                }
            })
            .catch(error => console.error('Error loading recent orders:', error));
    }

    function serveNextOrder() {
        console.log('serveNextOrder called');
        const url = 'api/admin_serve_next.php';
        console.log('Fetching from:', url);
        
        fetch(url, { method: 'POST' })
            .then(response => {
                console.log('Response received, status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Serve next order response:', data);
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