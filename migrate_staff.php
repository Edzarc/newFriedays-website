<?php
require_once 'config/db.php';

echo "Starting migration for staff table...\n";

try {
    // Check if staff table exists
    $checkTable = $pdo->query("SHOW TABLES LIKE 'staff'");
    $staffTableExists = $checkTable->rowCount() > 0;

    if (!$staffTableExists) {
        echo "Creating staff table...\n";
        
        $createTableSQL = "
            CREATE TABLE staff (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL UNIQUE,
                position VARCHAR(255) NOT NULL,
                department VARCHAR(255),
                hire_date DATE NOT NULL,
                employment_status ENUM('Active', 'Inactive', 'On Leave') DEFAULT 'Active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ";
        
        if ($pdo->exec($createTableSQL)) {
            echo "Staff table created successfully.\n";
        } else {
            echo "Failed to create staff table.\n";
            exit(1);
        }
    } else {
        echo "Staff table already exists.\n";
    }

    // Migrate existing staff users
    echo "Migrating existing staff users to staff table...\n";
    
    $stmt = $pdo->prepare("
        SELECT u.id, u.name
        FROM users u
        LEFT JOIN staff s ON u.id = s.user_id
        WHERE u.role = 'staff'
        AND s.id IS NULL
    ");
    $stmt->execute();
    $staffUsers = $stmt->fetchAll();

    echo "Found " . count($staffUsers) . " staff users to migrate.\n";

    $migrated = 0;
    foreach ($staffUsers as $staffUser) {
        $insertStmt = $pdo->prepare("
            INSERT INTO staff (user_id, position, department, hire_date, employment_status)
            VALUES (?, 'Staff Member', 'General', CURDATE(), 'Active')
        ");
        if ($insertStmt->execute([$staffUser['id']])) {
            $migrated++;
            echo "Migrated staff for user ID: {$staffUser['id']} ({$staffUser['name']})\n";
        } else {
            echo "Failed to migrate staff for user ID: {$staffUser['id']}\n";
        }
    }

    echo "\nMigration completed. Migrated $migrated staff records.\n";

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
