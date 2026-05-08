// Admin orders management JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Status change handlers
    document.querySelectorAll('.status-select').forEach(select => {
        select.addEventListener('change', function() {
            const orderId = this.dataset.orderId;
            const newStatus = this.value;

            updateOrderStatus(orderId, newStatus);
        });
    });

    // Export buttons
    const exportCsvBtn = document.getElementById('export-csv');
    const exportPdfBtn = document.getElementById('export-pdf');

    if (exportCsvBtn) {
        exportCsvBtn.addEventListener('click', () => exportOrders('csv'));
    }

    if (exportPdfBtn) {
        exportPdfBtn.addEventListener('click', () => exportOrders('pdf'));
    }

    function updateOrderStatus(orderId, status) {
        fetch('api/admin_update_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ order_id: orderId, status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Order status updated successfully!');
                // Update the status display
                const select = document.querySelector(`[data-order-id="${orderId}"]`);
                const row = select.closest('tr');
                const statusCell = row.querySelector('.status');
                statusCell.className = `status status-${status.toLowerCase()}`;
                statusCell.textContent = status;
            } else {
                showAlert('Failed to update order status.');
            }
        })
        .catch(error => {
            console.error('Error updating order status:', error);
            showAlert('An error occurred while updating the order status.');
        });
    }

    function exportOrders(format) {
        const url = `api/admin_export_orders.php?format=${format}`;
        const filters = new URLSearchParams(window.location.search);
        window.open(url + '&' + filters.toString(), '_blank');
    }
});