-- Create database and user (already created by docker-compose)
USE sqli_demo;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample users
INSERT INTO users (username, password, email, role) VALUES
('admin', 'admin123', 'admin@example.com', 'admin'),
('user1', 'password1', 'user1@example.com', 'user'),
('user2', 'password2', 'user2@example.com', 'user'),
('john', 'secret123', 'john@example.com', 'user');

-- Create products table for search demo
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2),
    description TEXT
);

-- Insert sample products
INSERT INTO products (name, price, description) VALUES
('Laptop', 1500.00, 'High performance laptop'),
('Mouse', 25.00, 'Wireless mouse'),
('Keyboard', 75.00, 'Mechanical keyboard'),
('Monitor', 300.00, '27 inch monitor');

