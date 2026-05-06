<?php
require_once '../../includes/functions.php';

function showUsers() {
    requireAdmin();

    $users = getAllUsers();

    include 'views/users.php';
}
?>