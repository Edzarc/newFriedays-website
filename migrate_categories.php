<?php
/**
 * Migration: Add categories table and update products table
 * This migration converts categories from ENUM to a foreign key relationship
 */

require_once 'config/db.php';

try {
    // Create categories table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Check if products table still uses ENUM (needs migration)
    $checkColumn = $pdo->query("SHOW COLUMNS FROM products LIKE 'category'");
    $categoryColumn = $checkColumn->fetch();
    
    if ($categoryColumn && strpos($categoryColumn['Type'], 'enum') !== false) {
        // Get existing categories from ENUM
        preg_match("/enum\((.*)\)/i", $categoryColumn['Type'], $matches);
        $existingCategories = str_getcsv($matches[1], ",", "'");
        
        // Insert existing categories into new table
        $stmt = $pdo->prepare("INSERT IGNORE INTO categories (name) VALUES (?)");
        foreach ($existingCategories as $cat) {
            $stmt->execute([trim($cat)]);
        }
        
        // Add category_id column to products if it doesn't exist
        $checkCategoryId = $pdo->query("SHOW COLUMNS FROM products LIKE 'category_id'");
        if (!$checkCategoryId->fetch()) {
            $pdo->exec("ALTER TABLE products ADD COLUMN category_id INT DEFAULT NULL");
        }
        
        // Populate category_id based on existing category names
        foreach ($existingCategories as $cat) {
            $catName = trim($cat);
            $stmt = $pdo->prepare("
                UPDATE products 
                SET category_id = (SELECT id FROM categories WHERE name = ?)
                WHERE category = ?
            ");
            $stmt->execute([$catName, $catName]);
        }
        
        // Add foreign key constraint
        $pdo->exec("
            ALTER TABLE products 
            ADD CONSTRAINT fk_products_category 
            FOREIGN KEY (category_id) REFERENCES categories(id)
        ");
        
        // Drop the old category ENUM column
        $pdo->exec("ALTER TABLE products DROP COLUMN category");
        
        // Rename category_id to category (optional, or keep as category_id)
        // For now, we'll keep it as category_id for clarity
    }
    
    echo json_encode(['success' => true, 'message' => 'Migration completed successfully']);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Migration failed: ' . $e->getMessage()]);
}
?>
