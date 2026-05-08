<?php
require_once 'includes/functions.php';

function showProducts() {
    requireAdmin();

    $products = getAllProducts();
    $categories = array_unique(array_column($products, 'category'));

    include 'admin/views/products.php';
}
?>