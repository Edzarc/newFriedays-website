<?php
require_once 'includes/functions.php';

function showUsers() {
    requireAdmin();

    $users = getAllUsers();
    $staffMembers = getAllStaff();

    include 'admin/views/users.php';
}
?>