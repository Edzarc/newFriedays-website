<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management - Friedays Bocaue</title>
    <link rel="stylesheet" href="public/css/style.css">
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
                    <li><a href="index.php?page=admin_users" class="active">Users</a></li>
                    <li><a href="index.php?page=admin_products">Products</a></li>
                    <li><a href="index.php?page=admin_analytics">Analytics</a></li>
                    <li><a href="index.php?page=logout">Logout</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <div class="container">
            <div class="admin-users">
                <h1>Search Users or Staff</h1>
                    <div class="table-actions">
                        <input type="text" id="user-search" placeholder="🔍︎ Search users or staff...">
                    </div>
                <h2>Staff Management</h2>
                <div class="staff-table-container" style="margin-top: 2rem;">
                    <div class="table-actions" style="margin-bottom: 1rem;">
                        <button class="btn btn-primary" id="create-employee-btn">+ Create New Employee</button>
                    </div>
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th>Staff ID</th>
                                <th>User ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Position</th>
                                <th>Department</th>
                                <th>Hire Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="staff-tbody">
                            <?php if (!empty($staffMembers)): ?>
                                <?php foreach ($staffMembers as $staff): ?>
                                    <tr
                                        data-staff-id="<?php echo $staff['staff_id']; ?>"
                                        data-name="<?php echo htmlspecialchars($staff['name'], ENT_QUOTES); ?>"
                                        data-email="<?php echo htmlspecialchars($staff['email'], ENT_QUOTES); ?>"
                                        data-phone="<?php echo htmlspecialchars($staff['phone'], ENT_QUOTES); ?>"
                                        data-address="<?php echo htmlspecialchars($staff['address'], ENT_QUOTES); ?>"
                                        data-role="<?php echo htmlspecialchars($staff['role'], ENT_QUOTES); ?>"
                                        data-position="<?php echo htmlspecialchars($staff['position'], ENT_QUOTES); ?>"
                                        data-department="<?php echo htmlspecialchars($staff['department'], ENT_QUOTES); ?>"
                                        data-hire-date="<?php echo htmlspecialchars(date('Y-m-d', strtotime($staff['hire_date'])), ENT_QUOTES); ?>"
                                        data-employment-status="<?php echo htmlspecialchars($staff['employment_status'], ENT_QUOTES); ?>"
                                    >
                                        <td><?php echo $staff['staff_id']; ?></td>
                                        <td><?php echo $staff['user_id']; ?></td>
                                        <td><?php echo htmlspecialchars($staff['name']); ?></td>
                                        <td><?php echo htmlspecialchars($staff['email']); ?></td>
                                        <td><?php echo htmlspecialchars($staff['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($staff['role']); ?></td>
                                        <td><?php echo htmlspecialchars($staff['position']); ?></td>
                                        <td><?php echo htmlspecialchars($staff['department']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($staff['hire_date'])); ?></td>
                                        <td><?php echo htmlspecialchars($staff['employment_status']); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-secondary edit-employee" id="btn-edit-employee" data-staff-id="<?php echo $staff['staff_id']; ?>">Edit</button>
                                            <button class="btn btn-sm btn-danger delete-employee" data-staff-id="<?php echo $staff['staff_id']; ?>" data-staff-name="<?php echo htmlspecialchars($staff['name'], ENT_QUOTES); ?>" style="margin-left: 5px;">Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>    
                            <?php else: ?>
                                <tr>
                                    <td colspan="10">No staff members found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <h2>Users Management</h2>

                <div class="users-table-container">
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Loyalty Tier</th>
                                <th>Total Spending</th>
                                <th>Registered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="users-tbody">
                            <?php foreach ($users as $user): ?>
                                <tr data-user-id="<?php echo $user['id']; ?>">
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                    <td>
                                        <select class="role-select" data-user-id="<?php echo $user['id']; ?>">
                                            <option value="customer" <?php echo $user['role'] === 'customer' ? 'selected' : ''; ?>>Customer</option>
                                            <option value="staff" <?php echo $user['role'] === 'staff' ? 'selected' : ''; ?>>Staff</option>
                                            <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="tier-select" data-user-id="<?php echo $user['id']; ?>">
                                            <option value="Bronze" <?php echo $user['loyalty_tier'] === 'Bronze' ? 'selected' : ''; ?>>Bronze</option>
                                            <option value="Silver" <?php echo $user['loyalty_tier'] === 'Silver' ? 'selected' : ''; ?>>Silver</option>
                                            <option value="Gold" <?php echo $user['loyalty_tier'] === 'Gold' ? 'selected' : ''; ?>>Gold</option>
                                            <option value="Platinum" <?php echo $user['loyalty_tier'] === 'Platinum' ? 'selected' : ''; ?>>Platinum</option>
                                        </select>
                                    </td>
                                    <td>₱<?php echo number_format($user['total_spending'], 2); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <button class="btn btn-small btn-secondary view-orders" data-user-id="<?php echo $user['id']; ?>" data-user-name="<?php echo htmlspecialchars($user['name']); ?>">
                                            View Orders
                                        </button>
                                        <button class="btn btn-small btn-danger delete-user" data-user-id="<?php echo $user['id']; ?>" data-user-name="<?php echo htmlspecialchars($user['name']); ?>" style="margin-left: 5px;">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>


                <!-- User Orders Modal -->
                <div id="user-orders-modal" class="modal">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <h3>Orders for <span id="modal-user-name"></span></h3>
                        <div id="user-orders-content">
                            <!-- User orders will be loaded here -->
                        </div>
                    </div>
                </div>

                <!-- Employee Create/Edit Modal -->
                <div id="employee-modal" class="modal">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <h3 id="employee-modal-title">Create New Employee</h3>
                        <form id="employee-form">
                            <input type="hidden" id="staff-id" value="">
                            
                            <div class="form-group">
                                <label for="employee-name">Full Name *</label>
                                <input type="text" id="employee-name" name="name" required>
                            </div>

                            <div class="form-group">
                                <label for="employee-email">Email *</label>
                                <input type="email" id="employee-email" name="email" required>
                            </div>

                            <div class="form-group">
                                <label for="employee-phone">Phone *</label>
                                <input type="tel" id="employee-phone" name="phone" required>
                            </div>

                            <div class="form-group">
                                <label for="employee-address">Address *</label>
                                <textarea id="employee-address" name="address" required></textarea>
                            </div>

                            <div class="form-group">
                                <label for="employee-position">Position *</label>
                                <input type="text" id="employee-position" name="position" required>
                            </div>

                            <div class="form-group">
                                <label for="employee-department">Department *</label>
                                <select id="employee-department" name="department" required>
                                    <option value="">Select Department</option>
                                    <option value="Kitchen">Kitchen</option>
                                    <option value="Cashier">Cashier</option>
                                    <option value="Delivery">Delivery</option>
                                    <option value="Management">Management</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="employee-hire-date">Hire Date *</label>
                                <input type="date" id="employee-hire-date" name="hire_date" required>
                            </div>

                            <div class="form-group" id="employment-status-group" style="display: none;">
                                <label for="employee-status">Employment Status *</label>
                                <select id="employee-status" name="employment_status">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                    <option value="On Leave">On Leave</option>
                                </select>
                            </div>

                            <div class="form-group" id="password-group">
                                <label for="employee-password">Password *</label>
                                <input type="password" id="employee-password" name="password" required>
                                <small>Leave blank to keep current password when editing</small>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Save Employee</button>
                                <button type="button" class="btn btn-secondary" onclick="document.getElementById('employee-modal').style.display = 'none';">Cancel</button>
                            </div>
                        </form>
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

    <script src="public/js/admin-users.js"></script>
</body>
</html>