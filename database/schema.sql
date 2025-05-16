-- Create database if not exists
CREATE DATABASE IF NOT EXISTS kicks_ecommerce;
USE kicks_ecommerce;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    icon VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Brands table
CREATE TABLE IF NOT EXISTS brands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category_id INT,
    brand_id INT,
    rating DECIMAL(3, 1) DEFAULT 0,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (brand_id) REFERENCES brands(id)
);

-- Product sizes table
CREATE TABLE IF NOT EXISTS product_sizes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    size DECIMAL(3, 1) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_amount DECIMAL(10, 2) NOT NULL,
    shipping_address TEXT,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    size DECIMAL(3, 1),
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Reviews table
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    user_id INT,
    rating INT NOT NULL,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert sample data for categories
INSERT INTO categories (name, icon) VALUES 
('All Shoes', 'fas fa-shoe-prints'),
('Running', 'fas fa-running'),
('Basketball', 'fas fa-basketball-ball'),
('Soccer', 'fas fa-futbol'),
('Hiking', 'fas fa-hiking'),
('Casual', 'fas fa-tshirt');

-- Insert sample data for brands
INSERT INTO brands (name) VALUES 
('Nike'),
('Adidas'),
('Puma'),
('New Balance'),
('Reebok'),
('Salomon');

-- Insert sample products
INSERT INTO products (name, description, price, category_id, brand_id, rating, image) VALUES
('Nike Air Max 270', 'The Nike Air Max 270 delivers a plush ride with a super-soft, lightweight foam midsole and a Max Air unit in the heel for responsive cushioning.', 150.00, 2, 1, 4.5, 'https://static.nike.com/a/images/c_limit,w_592,f_auto/t_product_v1/i1-665455a5-45de-40fb-945f-c1852b82400d/air-max-270-mens-shoes-KkLcGR.png'),
('Adidas Ultraboost 21', 'The Adidas Ultraboost 21 features a Boost midsole for incredible energy return and a Primeknit+ upper that adapts to the shape of your foot for a comfortable fit.', 180.00, 2, 2, 4.8, 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/fbaf991a78bc4896a3e9ad7800abcec6_9366/Ultraboost_22_Shoes_Black_GZ0127_01_standard.jpg'),
('Puma RS-X³', 'The Puma RS-X³ features bold design elements and a chunky silhouette with RS technology for cushioning and support.', 110.00, 6, 3, 4.2, 'https://images.puma.com/image/upload/f_auto,q_auto,b_rgb:fafafa,w_600,h_600/global/373308/04/sv01/fnd/PNA/fmt/png/RS-X%C2%B3-Puzzle-Men\'s-Sneakers'),
('New Balance 990v5', 'The New Balance 990v5 combines premium materials with exceptional cushioning and stability for a comfortable running experience.', 175.00, 2, 4, 4.7, 'https://nb.scene7.com/is/image/NB/m990gl5_nb_02_i?$pdpflexf2$&wid=440&hei=440'),
('Adidas Predator Freak', 'The Adidas Predator Freak features Demonskin rubber spikes for enhanced ball control and a comfortable fit for maximum performance on the field.', 230.00, 4, 2, 4.9, 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/0e540bae86f24661b855ac8a00f45d3e_9366/Predator_Freak.1_Firm_Ground_Cleats_Black_FY1026_01_standard.jpg'),
('Salomon X Ultra 3', 'The Salomon X Ultra 3 is a lightweight hiking shoe with excellent traction and support for challenging terrain.', 150.00, 5, 6, 4.5, 'https://www.salomon.com/sites/default/files/products-images/L40467400_8db8f8d5d4.jpg'),
('Reebok Classic Leather', 'The Reebok Classic Leather features a soft leather upper and a die-cut EVA midsole for lightweight cushioning and comfort.', 80.00, 6, 5, 4.3, 'https://assets.reebok.com/images/h_840,f_auto,q_auto:sensitive,fl_lossy,c_fill,g_auto/e21e6ce4dc7c43c9a0bfac6800a9c0e6_9366/Classic_Leather_Shoes_White_49799_01_standard.jpg');

-- Insert product sizes
INSERT INTO product_sizes (product_id, size, stock) VALUES
(1, 7, 10), (1, 8, 15), (1, 9, 20), (1, 10, 15), (1, 11, 10),
(2, 8, 12), (2, 9, 18), (2, 10, 20), (2, 11, 15), (2, 12, 8),
(3, 7, 8), (3, 8, 12), (3, 9, 15), (3, 10, 10),
(4, 8, 10), (4, 9, 15), (4, 10, 18), (4, 11, 12), (4, 12, 8), (4, 13, 5),
(5, 7, 8), (5, 8, 12), (5, 9, 15), (5, 10, 12), (5, 11, 8),
(6, 7, 10), (6, 8, 15), (6, 9, 20), (6, 10, 15), (6, 11, 10), (6, 12, 5),
(7, 7, 12), (7, 8, 18), (7, 9, 22), (7, 10, 18), (7, 11, 12), (7, 12, 8);