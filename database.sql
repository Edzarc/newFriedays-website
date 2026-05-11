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
    email_verified TINYINT(1) DEFAULT 0,
    last_verification_email_sent TIMESTAMP DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Email verification tokens table
CREATE TABLE email_verification_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(128) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    used_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Password reset tokens table
CREATE TABLE password_reset_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(128) NOT NULL UNIQUE,
    otp VARCHAR(6) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
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

-- Categories table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
        CREATE TABLE products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            category_id INT NOT NULL,
            price DECIMAL(8,2) NOT NULL,
            description TEXT,
            image_url VARCHAR(1024) DEFAULT NULL,
            is_available BOOLEAN DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES categories(id)
        );

-- Orders table
CREATE TABLE orders (
  id int NOT NULL AUTO_INCREMENT,
  user_id int NOT NULL,f
  order_number varchar(20) NOT NULL,
  order_type enum('Pickup','Dine In','Delivery') NOT NULL,
  payment_method enum('Cash','Cash on Delivery','GCash') NOT NULL,
  status enum('Pending','Preparing','Ready','Completed','Cancelled') DEFAULT 'Pending',
  payment_status enum('Pending','Paid','Failed','Cancelled') DEFAULT 'Pending',
  paymongo_payment_id varchar(255) DEFAULT NULL,
  paymongo_link_id varchar(255) DEFAULT NULL,
  total_amount decimal(10,2) NOT NULL,
  delivery_address_id int DEFAULT NULL,
  delivery_address text,
  created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY order_number (order_number),
  KEY user_id (user_id),
  KEY fk_orders_delivery_address (delivery_address_id),
  CONSTRAINT fk_orders_delivery_address FOREIGN KEY (delivery_address_id) REFERENCES user_addresses (id) ON DELETE SET NULL,
  CONSTRAINT orders_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) 
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

-- Pending orders table (for PayMongo payments)
CREATE TABLE pending_orders (
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

-- Insert sample categories
INSERT INTO categories (name) VALUES
('Chicken & Fried Items'),
('Sides & Sandwiches'),
('Beverages'),
('Pasta & Mains');

-- Insert sample products
INSERT INTO products (name, category_id, price, description, image_url) VALUES
('Fried Chicken Bucket', 1, 250.00, 'Crispy fried chicken bucket with 8 pieces', NULL),
('Chicken Nuggets', 1, 120.00, '12 pieces of golden chicken nuggets', NULL),
('French Fries', 2, 80.00, 'Crispy golden french fries', NULL),
('Chicken Sandwich', 2, 150.00, 'Grilled chicken sandwich with lettuce and mayo', NULL),
('Coca Cola', 3, 45.00, 'Refreshing cola drink', NULL),
('Iced Tea', 3, 40.00, 'Fresh brewed iced tea', NULL),
('Spaghetti Carbonara', 4, 180.00, 'Creamy spaghetti with bacon and cheese', NULL),
('Chicken Alfredo', 4, 200.00, 'Fettuccine alfredo with grilled chicken', NULL);

-- Create admin user (password: admin123)
INSERT INTO users (name, email, password_hash, phone, address, role, loyalty_tier) VALUES
('Admin', 'admin@friedays.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '09123456789', 'Restaurant Address', 'admin', 'Platinum'),
('Staff', 'staff@friedays.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '09123456789', 'Restaurant Address', 'staff', 'Bronze');