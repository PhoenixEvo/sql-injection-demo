-- Create database and user (already created by docker-compose)
USE sqli_demo;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    nickname VARCHAR(50),
    address TEXT,
    phone VARCHAR(20),
    salary DECIMAL(10, 2) DEFAULT 0.00,
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample users
-- Password is stored as SHA1 hash
INSERT INTO users (username, password, email, nickname, address, phone, salary, role) VALUES
('admin', SHA1('admin123'), 'admin@example.com', 'Administrator', '123 Admin St', '0123456789', 10000.00, 'admin'),
('alice', SHA1('seedalice'), 'alice@example.com', 'Alice', '456 Main St', '0987654321', 5000.00, 'user'),
('boby', SHA1('seedboby'), 'boby@example.com', 'Boby', '789 Oak Ave', '0111222333', 8000.00, 'user'),
('user1', SHA1('password1'), 'user1@example.com', 'User One', '321 First St', '0444555666', 3000.00, 'user'),
('user2', SHA1('password2'), 'user2@example.com', 'User Two', '654 Second St', '0777888999', 3500.00, 'user'),
('john', SHA1('secret123'), 'john@example.com', 'John', '987 Third St', '0222333444', 4000.00, 'user');

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

