<?php
require_once __DIR__ . '/../../includes/functions.php';

function showStaffDashboard() {
    requireStaff();
    include __DIR__ . '/../views/dashboard.php';
}
