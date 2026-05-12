// Admin users management JavaScript

// Utility function for alerts
function showAlert(message, type = 'info') {
    // Simple alert for now - could be enhanced with toast notifications
    alert(message);
}

document.addEventListener('DOMContentLoaded', function() {
    // User and staff search
    const searchInput = document.getElementById('user-search');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();

            document.querySelectorAll('#users-tbody tr').forEach(row => {
                const name = row.cells[1].textContent.toLowerCase();
                const email = row.cells[2].textContent.toLowerCase();
                const phone = row.cells[3].textContent.toLowerCase();
                const role = row.cells[4].querySelector('select')?.value.toLowerCase() || '';
                row.style.display = (name.includes(searchTerm) || email.includes(searchTerm) || phone.includes(searchTerm) || role.includes(searchTerm)) ? '' : 'none';
            });

            document.querySelectorAll('#staff-tbody tr').forEach(row => {
                const cells = Array.from(row.cells).map(cell => cell.textContent.toLowerCase());
                const match = cells.some(value => value.includes(searchTerm));
                row.style.display = match ? '' : 'none';
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

    // Role change handlers
    document.querySelectorAll('.role-select').forEach(select => {
        select.addEventListener('change', function() {
            const userId = this.dataset.userId;
            const newRole = this.value;

            updateUserRole(userId, newRole);
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

    // Employee modal handlers
    const employeeModal = document.getElementById('employee-modal');
    const employeeForm = document.getElementById('employee-form');
    const createEmployeeBtn = document.getElementById('create-employee-btn');
    const employeeCloseBtn = employeeModal?.querySelector('.close');

    if (createEmployeeBtn) {
        createEmployeeBtn.addEventListener('click', () => {
            // Reset form for create
            employeeForm.reset();
            document.getElementById('staff-id').value = '';
            document.getElementById('employee-modal-title').textContent = 'Create New Employee';
            document.getElementById('password-group').style.display = 'block';
            document.getElementById('employee-password').required = true;
            document.getElementById('employment-status-group').style.display = 'none';
            employeeModal.style.display = 'block';
        });
    }

    // Edit employee buttons
    document.querySelectorAll('.edit-employee').forEach(button => {
        button.addEventListener('click', function() {
            const staffId = this.dataset.staffId;
            loadEmployeeData(staffId);
        });
    });

    // Delete employee buttons
    document.querySelectorAll('.delete-employee').forEach(button => {
        button.addEventListener('click', function() {
            const staffId = this.dataset.staffId;
            const staffName = this.dataset.staffName;

            if (confirm(`Are you sure you want to delete employee "${staffName}"? This action cannot be undone.`)) {
                deleteEmployee(staffId);
            }
        });
    });

    if (employeeCloseBtn) {
        employeeCloseBtn.addEventListener('click', () => employeeModal.style.display = 'none');
    }

    window.addEventListener('click', (event) => {
        if (event.target === employeeModal) {
            employeeModal.style.display = 'none';
        }
    });

    // Employee form submission
    if (employeeForm) {
        employeeForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const staffId = document.getElementById('staff-id').value;
            const formData = {
                name: document.getElementById('employee-name').value,
                email: document.getElementById('employee-email').value,
                phone: document.getElementById('employee-phone').value,
                address: document.getElementById('employee-address').value,
                position: document.getElementById('employee-position').value,
                department: document.getElementById('employee-department').value,
                hire_date: document.getElementById('employee-hire-date').value
            };

            if (!staffId) {
                // Create new employee
                formData.password = document.getElementById('employee-password').value;
                if (!formData.password) {
                    showAlert('Password is required for new employees');
                    return;
                }

                saveNewEmployee(formData);
            } else {
                // Edit existing employee
                formData.staff_id = staffId;
                formData.employment_status = document.getElementById('employee-status').value;
                editEmployee(formData);
            }
        });
    }

    function loadEmployeeData(staffId) {
        // Find the staff row to get employee data
        const row = document.querySelector(`tr[data-staff-id="${staffId}"]`);
        if (!row) return;

        const name = row.dataset.name || '';
        const email = row.dataset.email || '';
        const phone = row.dataset.phone || '';
        const address = row.dataset.address || '';
        const position = row.dataset.position || '';
        const department = row.dataset.department || '';
        const hireDate = row.dataset.hireDate || '';
        const status = row.dataset.employmentStatus || '';

        document.getElementById('staff-id').value = staffId;
        document.getElementById('employee-name').value = name;
        document.getElementById('employee-email').value = email;
        document.getElementById('employee-phone').value = phone;
        document.getElementById('employee-address').value = address;
        document.getElementById('employee-position').value = position;
        document.getElementById('employee-department').value = department;
        document.getElementById('employee-hire-date').value = hireDate;
        document.getElementById('employee-status').value = status;

        document.getElementById('employee-modal-title').textContent = 'Edit Employee';
        document.getElementById('password-group').style.display = 'none';
        document.getElementById('employee-password').required = false;
        document.getElementById('employment-status-group').style.display = 'block';

        employeeModal.style.display = 'block';
    }

    function saveNewEmployee(formData) {
        fetch('api/admin_create_employee.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Employee created successfully!');
                employeeModal.style.display = 'none';
                // Reload the page to show the new employee
                setTimeout(() => location.reload(), 1000);
            } else {
                showAlert('Failed to create employee: ' + (data.error || data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error creating employee:', error);
            showAlert('An error occurred while creating the employee.');
        });
    }

    function editEmployee(formData) {
        fetch('api/admin_edit_employee.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Employee updated successfully!');
                employeeModal.style.display = 'none';
                // Reload the page to show updated data
                setTimeout(() => location.reload(), 1000);
            } else {
                showAlert('Failed to update employee: ' + (data.error || data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error updating employee:', error);
            showAlert('An error occurred while updating the employee.');
        });
    }

    function deleteEmployee(staffId) {
        fetch('api/admin_delete_employee.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ staff_id: staffId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Employee deleted successfully!');
                const staffRow = document.querySelector(`tr[data-staff-id="${staffId}"]`);
                if (staffRow) {
                    staffRow.remove();
                }
            } else {
                showAlert('Failed to delete employee: ' + (data.error || data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error deleting employee:', error);
            showAlert('An error occurred while deleting the employee.');
        });
    }


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

    function updateUserRole(userId, role) {
        fetch('api/admin_update_user_role.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ user_id: userId, role: role })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('User role updated successfully!');
            } else {
                showAlert('Failed to update user role. ' + (data.message || ''));
            }
        })
        .catch(error => {
            console.error('Error updating user role:', error);
            showAlert('An error occurred while updating the user role.');
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

        fetch(`api/admin_user_orders.php?user_id=${userId}`)
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