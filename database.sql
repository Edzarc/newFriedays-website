-- Friedays Bocaue Restaurant Database Schema

-- Create database
CREATE DATABASE IF NOT EXISTS friedays_bocaue;
USE friedays_bocaue;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    role ENUM('customer', 'staff', 'admin') NOT NULL DEFAULT 'customer',
    loyalty_tier ENUM('Bronze', 'Silver', 'Gold', 'Platinum') DEFAULT 'Bronze',
    total_spending DECIMAL(10,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- User addresses table
CREATE TABLE user_addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    label VARCHAR(255) NOT NULL,
    address TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category ENUM('Chicken & Fried Items', 'Sides & Sandwiches', 'Beverages', 'Pasta & Mains') NOT NULL,
    price DECIMAL(8,2) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_number VARCHAR(20) UNIQUE NOT NULL,
    order_type ENUM('Pickup', 'Dine In', 'Delivery') NOT NULL,
    payment_method ENUM('Cash on Delivery', 'GCash') NOT NULL,
    status ENUM('Pending', 'Preparing', 'Ready', 'Completed', 'Cancelled') DEFAULT 'Pending',
    total_amount DECIMAL(10,2) NOT NULL,
    delivery_address_id INT NULL,
    delivery_address TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (delivery_address_id) REFERENCES user_addresses(id) ON DELETE SET NULL
);

-- Order items table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price_at_purchase DECIMAL(8,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Loyalty tiers table
CREATE TABLE loyalty_tiers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tier_name ENUM('Bronze', 'Silver', 'Gold', 'Platinum') NOT NULL,
    discount_percentage DECIMAL(5,2) DEFAULT 0.00,
    benefits TEXT,
    min_spending_threshold DECIMAL(10,2) DEFAULT 0.00
);

-- Queue table
CREATE TABLE queue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    queue_number INT NOT NULL,
    status ENUM('Waiting', 'Serving', 'Completed') DEFAULT 'Waiting',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- Staff table
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
);

-- Insert sample loyalty tiers
INSERT INTO loyalty_tiers (tier_name, discount_percentage, benefits, min_spending_threshold) VALUES
('Bronze', 0.00, 'Welcome discount: 5% off first order', 0.00),
('Silver', 5.00, '5% discount on all orders, Free delivery on orders over ₱500', 1000.00),
('Gold', 10.00, '10% discount on all orders, Free delivery, Priority queue', 5000.00),
('Platinum', 15.00, '15% discount on all orders, Free delivery, Priority queue, Exclusive menu items', 10000.00);

-- Insert sample products
INSERT INTO products (name, category, price, description) VALUES
('Fried Chicken Bucket', 'Chicken & Fried Items', 250.00, 'Crispy fried chicken bucket with 8 pieces'),
('Chicken Nuggets', 'Chicken & Fried Items', 120.00, '12 pieces of golden chicken nuggets'),
('French Fries', 'Sides & Sandwiches', 80.00, 'Crispy golden french fries'),
('Chicken Sandwich', 'Sides & Sandwiches', 150.00, 'Grilled chicken sandwich with lettuce and mayo'),
('Coca Cola', 'Beverages', 45.00, 'Refreshing cola drink'),
('Iced Tea', 'Beverages', 40.00, 'Fresh brewed iced tea'),
('Spaghetti Carbonara', 'Pasta & Mains', 180.00, 'Creamy spaghetti with bacon and cheese'),
('Chicken Alfredo', 'Pasta & Mains', 200.00, 'Fettuccine alfredo with grilled chicken');

-- Create admin user (password: admin123)
INSERT INTO users (name, email, password_hash, phone, address, role, loyalty_tier) VALUES
('Admin', 'admin@friedays.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '09123456789', 'Restaurant Address', 'admin', 'Platinum'),
('Staff', 'staff@friedays.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '09123456789', 'Restaurant Address', 'staff', 'Bronze');