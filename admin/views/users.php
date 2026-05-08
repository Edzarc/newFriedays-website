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
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th>Staff ID</th>
                                <th>User ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Position</th>
                                <th>Department</th>
                                <th>Hire Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="staff-tbody">
                            <?php if (!empty($staffMembers)): ?>
                                <?php foreach ($staffMembers as $staff): ?>
                                    <tr>
                                        <td><?php echo $staff['staff_id']; ?></td>
                                        <td><?php echo $staff['user_id']; ?></td>
                                        <td><?php echo htmlspecialchars($staff['name']); ?></td>
                                        <td><?php echo htmlspecialchars($staff['email']); ?></td>
                                        <td><?php echo htmlspecialchars($staff['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($staff['position']); ?></td>
                                        <td><?php echo htmlspecialchars($staff['department']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($staff['hire_date'])); ?></td>
                                        <td><?php echo htmlspecialchars($staff['employment_status']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9">No staff members found.</td>
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