// Admin users management JavaScript

// Utility function for alerts
function showAlert(message, type = 'info') {
    // Simple alert for now - could be enhanced with toast notifications
    alert(message);
}

document.addEventListener('DOMContentLoaded', function() {
    // User search
    const searchInput = document.getElementById('user-search');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            document.querySelectorAll('#users-tbody tr').forEach(row => {
                const name = row.cells[1].textContent.toLowerCase();
                const email = row.cells[2].textContent.toLowerCase();
                row.style.display = (name.includes(searchTerm) || email.includes(searchTerm)) ? '' : 'none';
            });
        });
    }

    // Tier change handlers
    document.querySelectorAll('.tier-select').forEach(select => {
        select.addEventListener('change', function() {
            const userId = this.dataset.userId;
            const newTier = this.value;

            updateUserTier(userId, newTier);
        });
    });

    // View orders modal
    const modal = document.getElementById('user-orders-modal');
    const closeBtn = document.querySelector('.close');

    document.querySelectorAll('.view-orders').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;

            loadUserOrders(userId, userName);
            modal.style.display = 'block';
        });
    });

    // Delete user functionality
    document.querySelectorAll('.delete-user').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;

            if (confirm(`Are you sure you want to delete user "${userName}"? This action cannot be undone.`)) {
                deleteUser(userId);
            }
        });
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', () => modal.style.display = 'none');
    }

    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });

    function updateUserTier(userId, tier) {
        fetch('api/admin_update_user_tier.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ user_id: userId, tier: tier })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('User tier updated successfully!');
            } else {
                showAlert('Failed to update user tier.');
            }
        })
        .catch(error => {
            console.error('Error updating user tier:', error);
            showAlert('An error occurred while updating the user tier.');
        });
    }

    function deleteUser(userId) {
        fetch('api/admin_delete_user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ user_id: userId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('User deleted successfully!');
                // Remove the user row from the table
                const userRow = document.querySelector(`tr[data-user-id="${userId}"]`);
                if (userRow) {
                    userRow.remove();
                }
            } else {
                showAlert(data.message || 'Failed to delete user.');
            }
        })
        .catch(error => {
            console.error('Error deleting user:', error);
            showAlert('An error occurred while deleting the user.');
        });
    }

    function loadUserOrders(userId, userName) {
        document.getElementById('modal-user-name').textContent = userName;

        fetch(`../api/admin_user_orders.php?user_id=${userId}`)
            .then(response => response.json())
            .then(data => {
                const content = document.getElementById('user-orders-content');
                content.innerHTML = '';

                if (data.orders.length === 0) {
                    content.innerHTML = '<p>No orders found for this user.</p>';
                    return;
                }

                const table = document.createElement('table');
                table.className = 'orders-table';
                table.innerHTML = `
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.orders.map(order => `
                            <tr>
                                <td>${order.order_number}</td>
                                <td>${new Date(order.created_at).toLocaleDateString()}</td>
                                <td><span class="status status-${order.status.toLowerCase()}">${order.status}</span></td>
                                <td>₱${parseFloat(order.total_amount).toFixed(2)}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                `;

                content.appendChild(table);
            })
            .catch(error => {
                console.error('Error loading user orders:', error);
                document.getElementById('user-orders-content').innerHTML = '<p>Error loading orders.</p>';
            });
    }
});