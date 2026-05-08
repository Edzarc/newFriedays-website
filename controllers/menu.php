<?php
require_once 'includes/functions.php';

function showMenu() {
    requireLogin();

    $products = getAllProducts(true); // Get only available products
    $categories = array_unique(array_column($products, 'category'));

    include 'views/menu.php';
}
?>