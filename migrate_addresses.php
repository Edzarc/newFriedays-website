<?php
require_once 'config/db.php';

echo "Starting migration of user addresses...\n";

try {
    // Get all users who have an address but no entries in user_addresses
    $stmt = $pdo->prepare("
        SELECT u.id, u.address
        FROM users u
        LEFT JOIN user_addresses ua ON u.id = ua.user_id
        WHERE u.address IS NOT NULL 
        AND u.address != ''
        AND ua.id IS NULL
    ");
    $stmt->execute();
    $users = $stmt->fetchAll();

    echo "Found " . count($users) . " users to migrate.\n";

    $migrated = 0;
    foreach ($users as $user) {
        $insertStmt = $pdo->prepare("INSERT INTO user_addresses (user_id, label, address) VALUES (?, 'Home', ?)");
        if ($insertStmt->execute([$user['id'], $user['address']])) {
            $migrated++;
            echo "Migrated address for user ID: {$user['id']}\n";
        } else {
            echo "Failed to migrate address for user ID: {$user['id']}\n";
        }
    }

    echo "Migration completed. Migrated $migrated addresses.\n";

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
?>