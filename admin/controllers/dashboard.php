<?php
require_once 'includes/functions.php';

function showAdminDashboard() {
    requireAdmin();

    include 'admin/views/dashboard.php';
}
?>