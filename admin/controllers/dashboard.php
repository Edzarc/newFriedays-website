<?php
require_once 'includes\functions.php';

function showAdminDashboard() {
    requireAdmin();

    include 'views/dashboard.php';
}
?>