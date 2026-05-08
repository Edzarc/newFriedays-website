document.addEventListener('DOMContentLoaded', function() {
    const nextOrderBtn = document.getElementById('next-order-btn');
    const refreshQueueBtn = document.getElementById('refresh-queue-btn');
    const processSelectedBtn = document.getElementById('process-selected-btn');
    const selectAllBtn = document.getElementById('select-all-btn');
    const queueBody = document.getElementById('staff-queue-body');
    const currentServingEl = document.getElementById('staff-current-serving');

    function loadStaffQueue() {
        fetch('api/staff_stats.php')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    showAlert(data.error, 'error');
                    return;
                }

                currentServingEl.textContent = data.current_serving ?
                    `Order #${data.current_serving}` : 'No orders being prepared';

                // Load queue table with Pending orders
                queueBody.innerHTML = '';
                const pendingOrders = data.orders_by_status.Pending || [];
                if (pendingOrders.length > 0) {
                    pendingOrders.forEach(order => {
                        const row = document.createElement('tr');
                        const nextStatus = getNextStatus('Pending');
                        const actionButton = nextStatus ? `<button class="btn btn-small btn-primary proceed-status" data-order-id="${order.id}" data-status="${nextStatus}">→ ${nextStatus}</button>` : '';

                        row.innerHTML = `
                            <td><input type="checkbox" class="queue-select" value="${order.id}"></td>
                            <td>${order.order_number}</td>
                            <td>${order.user_name}</td>
                            <td>${order.order_type}</td>
                            <td><span class="status status-pending">Pending</span></td>
                            <td>${actionButton}</td>
                        `;
                        queueBody.appendChild(row);
                    });
                } else {
                    const emptyRow = document.createElement('tr');
                    emptyRow.innerHTML = '<td colspan="6">No pending orders.</td>';
                    queueBody.appendChild(emptyRow);
                }

                // Load status tables (excluding Pending)
                loadStatusTables(data.orders_by_status);
            })
            .catch(error => {
                console.error('Error loading staff queue:', error);
                showAlert('Unable to load queue data. Please try again.', 'error');
            });
    }

    function loadStatusTables(ordersByStatus) {
        const statusTables = {
            'Preparing': 'preparing-orders-body',
            'Ready': 'ready-orders-body',
            'Completed': 'completed-orders-body',
            'Cancelled': 'cancelled-orders-body'
        };

        Object.keys(statusTables).forEach(status => {
            const tbody = document.getElementById(statusTables[status]);
            tbody.innerHTML = '';

            const orders = ordersByStatus[status] || [];
            if (orders.length > 0) {
                orders.forEach(order => {
                    const row = document.createElement('tr');
                    const nextStatus = getNextStatus(status);
                    const actionButton = nextStatus ? `<button class="btn btn-small btn-primary proceed-status" data-order-id="${order.id}" data-status="${nextStatus}">→ ${nextStatus}</button>` : '';

                    row.innerHTML = `
                        <td><input type="checkbox" class="status-select status-select-${status.toLowerCase()}" value="${order.id}"></td>
                        <td>${order.order_number}</td>
                        <td>${order.user_name}</td>
                        <td>${order.order_type}</td>
                        <td><span class="status status-${status.toLowerCase()}">${status}</span></td>
                        <td>${actionButton}</td>
                    `;
                    tbody.appendChild(row);
                });
            } else {
                const emptyRow = document.createElement('tr');
                emptyRow.innerHTML = `<td colspan="6">No ${status.toLowerCase()} orders.</td>`;
                tbody.appendChild(emptyRow);
            }
        });

        // Add event listeners for proceed buttons
        document.querySelectorAll('.proceed-status').forEach(btn => {
            btn.addEventListener('click', function() {
                const orderId = this.getAttribute('data-order-id');
                const newStatus = this.getAttribute('data-status');
                updateOrderStatus(orderId, newStatus);
            });
        });
    }

    function getNextStatus(currentStatus) {
        const statusFlow = {
            'Pending': 'Preparing',
            'Preparing': 'Ready',
            'Ready': 'Completed'
        };
        return statusFlow[currentStatus] || null;
    }

    function updateOrderStatus(orderId, newStatus) {
        fetch('api/staff_update_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ order_id: orderId, status: newStatus })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message || 'Order status updated successfully!', 'success');
                    loadStaffQueue();
                } else {
                    showAlert(data.error || 'Failed to update order status.', 'error');
                }
            })
            .catch(error => {
                console.error('Error updating order status:', error);
                showAlert('Error updating order status. Please try again.', 'error');
            });
    }

    function serveNextOrder() {
        fetch('api/staff_stats.php')
            .then(response => response.json())
            .then(data => {
                const pendingOrders = data.orders_by_status.Pending || [];
                if (pendingOrders.length === 0) {
                    showAlert('No pending orders to serve.', 'info');
                    return;
                }

                const nextOrder = pendingOrders[0]; // Get the first pending order
                updateOrderStatus(nextOrder.id, 'Preparing');
            })
            .catch(error => {
                console.error('Error serving next order:', error);
                showAlert('Error serving next order. Please try again.', 'error');
            });
    }

    function processSelectedOrders() {
        const selected = Array.from(document.querySelectorAll('.queue-select:checked')).map(input => input.value);
        if (selected.length === 0) {
            showAlert('Select at least one pending order to process.', 'info');
            return;
        }

        // Process selected orders by changing their status to Preparing
        Promise.all(selected.map(orderId => 
            fetch('api/staff_update_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ order_id: orderId, status: 'Preparing' })
            }).then(response => response.json())
        ))
        .then(results => {
            const successCount = results.filter(result => result.success).length;
            if (successCount > 0) {
                showAlert(`${successCount} order(s) moved to Preparing.`, 'success');
                loadStaffQueue();
            } else {
                showAlert('Unable to process selected orders.', 'error');
            }
        })
        .catch(error => {
            console.error('Error processing selected orders:', error);
            showAlert('Error processing selected orders. Please try again.', 'error');
        });
    }

    function cancelSelectedOrders() {
        const selected = Array.from(document.querySelectorAll('.queue-select:checked')).map(input => input.value);
        if (selected.length === 0) {
            showAlert('Select at least one pending order to cancel.', 'info');
            return;
        }

        // Cancel selected orders
        Promise.all(selected.map(orderId => 
            fetch('api/staff_update_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ order_id: orderId, status: 'Cancelled' })
            }).then(response => response.json())
        ))
        .then(results => {
            const successCount = results.filter(result => result.success).length;
            if (successCount > 0) {
                showAlert(`${successCount} order(s) cancelled.`, 'success');
                loadStaffQueue();
            } else {
                showAlert('Unable to cancel selected orders.', 'error');
            }
        })
        .catch(error => {
            console.error('Error cancelling selected orders:', error);
            showAlert('Error cancelling selected orders. Please try again.', 'error');
        });
    }

    function toggleSelectAll(selector = '.queue-select') {
        const checkboxes = Array.from(document.querySelectorAll(selector));
        if (checkboxes.length === 0) {
            return;
        }

        const allChecked = checkboxes.every(input => input.checked);
        checkboxes.forEach(input => input.checked = !allChecked);
    }

    function bulkProcessOrders(selector, newStatus, successMessage) {
        const selected = Array.from(document.querySelectorAll(selector + ':checked')).map(input => input.value);
        if (selected.length === 0) {
            showAlert('Select at least one order to process.', 'info');
            return;
        }

        Promise.all(selected.map(orderId => 
            fetch('api/staff_update_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ order_id: orderId, status: newStatus })
            }).then(response => response.json())
        ))
        .then(results => {
            const successCount = results.filter(result => result.success).length;
            if (successCount > 0) {
                showAlert(`${successCount} order(s) ${successMessage}.`, 'success');
                loadStaffQueue();
            } else {
                showAlert('Unable to process selected orders.', 'error');
            }
        })
        .catch(error => {
            console.error('Error processing selected orders:', error);
            showAlert('Error processing selected orders. Please try again.', 'error');
        });
    }

    if (nextOrderBtn) nextOrderBtn.addEventListener('click', serveNextOrder);
    if (refreshQueueBtn) refreshQueueBtn.addEventListener('click', loadStaffQueue);
    if (processSelectedBtn) processSelectedBtn.addEventListener('click', processSelectedOrders);
    if (selectAllBtn) selectAllBtn.addEventListener('click', toggleSelectAll);

    // Get references to new buttons
    const cancelSelectedBtn = document.getElementById('cancel-selected-btn');

    if (cancelSelectedBtn) cancelSelectedBtn.addEventListener('click', cancelSelectedOrders);

    // Bulk action event listeners
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('bulk-process-preparing')) {
            bulkProcessOrders('.status-select-preparing', 'Ready', 'moved to Ready');
        } else if (e.target.classList.contains('bulk-process-ready')) {
            bulkProcessOrders('.status-select-ready', 'Completed', 'marked as Completed');
        } else if (e.target.classList.contains('bulk-cancel-preparing')) {
            bulkProcessOrders('.status-select-preparing', 'Cancelled', 'cancelled');
        } else if (e.target.classList.contains('bulk-cancel-ready')) {
            bulkProcessOrders('.status-select-ready', 'Cancelled', 'cancelled');
        } else if (e.target.classList.contains('select-all-preparing')) {
            toggleSelectAll('.status-select-preparing');
        } else if (e.target.classList.contains('select-all-ready')) {
            toggleSelectAll('.status-select-ready');
        }
    });

    loadStaffQueue();
});
