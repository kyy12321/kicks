-- Create Database
CREATE DATABASE kicksf;
USE kicksf;

-- Create Categories table first
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL
);


CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL, -- Removed UNSIGNED to match the users table
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Pending', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Pending',
    total DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Create Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'cashier', 'superadmin', 'customer') NOT NULL
);

-- Create Suppliers table next
CREATE TABLE suppliers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    contact VARCHAR(100)
);

-- Products table structure
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);

-- Create Sales table after Users
CREATE TABLE sales (
    id INT PRIMARY KEY AUTO_INCREMENT,
    transaction_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10, 2) NOT NULL,
    cashier_id INT NOT NULL,
    FOREIGN KEY (cashier_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create Customers table (for optional loyalty program support)
CREATE TABLE customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    contact_info VARCHAR(100)
);

-- Create Sales Items table after Products and Sales
CREATE TABLE sales_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price_per_unit DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Create Stock table
CREATE TABLE stock (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    stock_in INT DEFAULT 0,
    stock_out INT DEFAULT 0,
    supplier_id INT DEFAULT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL
);

-- Create Payments table
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sale_id INT NOT NULL,
    method ENUM('cash', 'credit', 'debit', 'online') NOT NULL,
    status ENUM('completed', 'pending', 'failed') NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE
);

-- Create Shipping table (Fixed customer_id issue)
CREATE TABLE shipping (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    customer_id INT DEFAULT NULL, -- Fixed: Allows NULL for ON DELETE SET NULL
    shipping_address VARCHAR(255) NOT NULL,
    tracking_number VARCHAR(100),
    status ENUM('pending', 'shipped', 'delivered', 'canceled') NOT NULL,
    FOREIGN KEY (order_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
);

CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

ALTER TABLE products
ADD COLUMN barcode VARCHAR(255) UNIQUE NOT NULL;

ALTER TABLE products
ADD COLUMN discount_type VARCHAR(50) DEFAULT 'none',
ADD COLUMN discount_value DECIMAL(10, 2) DEFAULT 0;
