-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS user_store_db;
USE user_store_db;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create products table for the store (basic example)
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert some sample products
INSERT INTO products (name, description, price, image_url) VALUES
('Product 1', 'This is the first product description', 19.99, 'images/product1.jpg'),
('Product 2', 'This is the second product description', 29.99, 'images/product2.jpg'),
('Product 3', 'This is the third product description', 39.99, 'images/product3.jpg'),
('Product 4', 'This is the fourth product description', 49.99, 'images/product4.jpg');



-- Update users table to include admin flag
ALTER TABLE users ADD COLUMN is_admin BOOLEAN DEFAULT FALSE;

-- Create admin user (password is 'admin123')
INSERT INTO users (username, email, password, is_admin) VALUES 
('admin', 'admin@example.com', '$2y$10$rJf.NmHdqid.FCm7.khrNOkSGH7.hJh/QnD5HRwSjOxOBxHMq.VGe', TRUE);