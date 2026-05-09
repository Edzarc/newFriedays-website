<?php
require_once 'config/db.php';

echo "Creating pending_orders table...\n";

try {
    $sql = "
    CREATE TABLE IF NOT EXISTS pending_orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        order_number VARCHAR(20) NOT NULL,
        order_type ENUM('Pickup','Dine In','Delivery') NOT NULL,
        payment_method ENUM('Cash on Delivery','GCash') NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        cart_items JSON NOT NULL,
        delivery_address_id INT DEFAULT NULL,
        delivery_address TEXT,
        paymongo_source_id VARCHAR(255) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (delivery_address_id) REFERENCES user_addresses(id) ON DELETE SET NULL
    );
    ";

    $pdo->exec($sql);
    echo "pending_orders table created successfully!\n";

} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage() . "\n";
}
?>