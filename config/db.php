<?php
// Database configuration
// Use Philippines local time for both PHP and MySQL.
date_default_timezone_set('Asia/Manila');
define('DB_HOST', 'localhost');
define('DB_NAME', 'friedays_bocaue');
define('DB_USER', 'root'); // Change this to your MySQL username
define('DB_PASS', 'admin'); // Change this to your MySQL password

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec("SET time_zone = '+08:00'");
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>