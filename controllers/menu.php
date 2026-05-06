<?php
require_once 'includes/functions.php';

function showMenu() {
    requireLogin();

    $products = getAllProducts();
    $categories = array_unique(array_column($products, 'category'));

    include 'views/menu.php';
}
?>