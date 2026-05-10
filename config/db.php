<?php
// Database configuration
// Use Philippines local time for both PHP and MySQL.
date_default_timezone_set('Asia/Manila');

// Local development database settings
$localDb = [
    'host' => 'localhost',
    'name' => 'friedays_bocaue',
    'user' => 'root',
    'pass' => 'admin',
];

// InfinityFree production database settings
$productionDb = [
    'host' => 'sql211.infinityfree.com',
    'name' => 'if0_41848840_friedays_bocaue',
    'user' => 'if0_41848840',
    'pass' => 'PoMDzVdx0PW1vcy',
];

// Allow environment variables to override the default config.
$dbHost = getenv('DB_HOST');
$dbName = getenv('DB_NAME');
$dbUser = getenv('DB_USER');
$dbPass = getenv('DB_PASS');

if (!$dbHost || !$dbName || !$dbUser || !$dbPass) {
    $serverHost = strtolower($_SERVER['HTTP_HOST'] ?? gethostname());

    if (strpos($serverHost, 'localhost') !== false || strpos($serverHost, '127.0.0.1') !== false || php_sapi_name() === 'cli') {
        $selectedDb = $localDb;
    } else {
        $selectedDb = $productionDb;
    }

    $dbHost = $selectedDb['host'];
    $dbName = $selectedDb['name'];
    $dbUser = $selectedDb['user'];
    $dbPass = $selectedDb['pass'];
}

define('DB_HOST', $dbHost);
define('DB_NAME', $dbName);
define('DB_USER', $dbUser);
define('DB_PASS', $dbPass);

// SMTP / Email configuration for Brevo relay
define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp-relay.brevo.com');
define('SMTP_PORT', getenv('SMTP_PORT') ?: 587);
define('SMTP_USERNAME', getenv('SMTP_USERNAME') ?: '');
define('SMTP_PASSWORD', getenv('SMTP_PASSWORD') ?: '');
define('SMTP_FROM_EMAIL', getenv('SMTP_FROM_EMAIL') ?: 'edzel0arciga@gmail.com');
define('SMTP_FROM_NAME', getenv('SMTP_FROM_NAME') ?: 'Friedays Bocaue');

// PayMongo API Configuration
define('PAYMONGO_SECRET_KEY', getenv('PAYMONGO_SECRET_KEY') ?: '');
define('PAYMONGO_PUBLIC_KEY', 'pk_test_7fb19JTL7YkYSWYmcBx6iGmG');
define('PAYMONGO_BASE_URL', 'https://api.paymongo.com/v1');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec("SET time_zone = '+08:00'");
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>