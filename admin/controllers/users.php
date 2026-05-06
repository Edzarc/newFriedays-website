<?php
require_once 'includes/functions.php';

function showUsers() {
    requireAdmin();

    $users = getAllUsers();

    include 'admin/views/users.php';
}
?>