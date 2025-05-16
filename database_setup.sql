-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS kicks_ecomm;

-- Use the database
USE kicks_ecomm;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'customer') NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255),
    category VARCHAR(50),
    brand VARCHAR(50),
    rating DECIMAL(3, 1) DEFAULT 0,
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create product_sizes table (for many-to-many relationship)
CREATE TABLE IF NOT EXISTS product_sizes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    size DECIMAL(4, 1) NOT NULL,
    stock INT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Create orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    shipping_address TEXT NOT NULL,
    order_status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Create order_items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    size DECIMAL(4, 1),
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Create cart table
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    size DECIMAL(4, 1),
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Create sample admin user (password: admin123)
INSERT INTO users (name, email, username, password, role) 
VALUES ('Admin User', 'admin@kicks.com', 'admin', '$2y$10$8KOO.JFMA/hkVOXLc5uOuRxAjZxGJI1aZO7YXWl4YgEUNNwUi/Oa', 'admin');

-- Insert sample products
INSERT INTO products (name, description, price, image, category, brand, rating, stock) VALUES
('Nike Air Max 270', 'The Nike Air Max 270 delivers a plush ride with a super-soft, lightweight foam midsole and a Max Air unit in the heel for responsive cushioning.', 150.00, 'https://static.nike.com/a/images/c_limit,w_592,f_auto/t_product_v1/i1-665455a5-45de-40fb-945f-c1852b82400d/air-max-270-mens-shoes-KkLcGR.png', 'Running', 'Nike', 4.5, 100),
('Adidas Ultraboost 21', 'The Adidas Ultraboost 21 features a Boost midsole for incredible energy return and a Primeknit+ upper that adapts to the shape of your foot for a comfortable fit.', 180.00, 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/fbaf991a78bc4896a3e9ad7800abcec6_9366/Ultraboost_22_Shoes_Black_GZ0127_01_standard.jpg', 'Running', 'Adidas', 4.8, 75),
('Puma RS-X³', 'The Puma RS-X³ features bold design elements and a chunky silhouette with RS technology for cushioning and support.', 110.00, 'https://images.puma.com/image/upload/f_auto,q_auto,b_rgb:fafafa,w_600,h_600/global/373308/04/sv01/fnd/PNA/fmt/png/RS-X%C2%B3-Puzzle-Men%27s-Sneakers', 'Casual', 'Puma', 4.2, 50),
('New Balance 990v5', 'The New Balance 990v5 combines premium materials with exceptional cushioning and stability for a comfortable running experience.', 175.00, 'https://nb.scene7.com/is/image/NB/m990gl5_nb_02_i?$pdpflexf2$&wid=440&hei=440', 'Running', 'New Balance', 4.7, 60),
('Adidas Predator Freak', 'The Adidas Predator Freak features Demonskin rubber spikes for enhanced ball control and a comfortable fit for maximum performance on the field.', 230.00, 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/0e540bae86f24661b855ac8a00f45d3e_9366/Predator_Freak.1_Firm_Ground_Cleats_Black_FY1026_01_standard.jpg', 'Soccer', 'Adidas', 4.9, 40),
('Salomon X Ultra 3', 'The Salomon X Ultra 3 is a lightweight hiking shoe with excellent traction and support for challenging terrain.', 150.00, 'https://www.salomon.com/sites/default/files/products-images/L40467400_8db8f8d5d4.jpg', 'Hiking', 'Salomon', 4.5, 30),
('Reebok Classic Leather', 'The Reebok Classic Leather features a soft leather upper and a die-cut EVA midsole for lightweight cushioning and comfort.', 80.00, 'https://assets.reebok.com/images/h_840,f_auto,q_auto:sensitive,fl_lossy,c_fill,g_auto/e21e6ce4dc7c43c9a0bfac6800a9c0e6_9366/Classic_Leather_Shoes_White_49799_01_standard.jpg', 'Casual', 'Reebok', 4.3, 90);

-- Insert product sizes
INSERT INTO product_sizes (product_id, size, stock) VALUES
(1, 7, 10), (1, 8, 15), (1, 9, 20), (1, 10, 15), (1, 11, 10),
(2, 8, 10), (2, 9, 15), (2, 10, 20), (2, 11, 15), (2, 12, 10),
(3, 7, 5), (3, 8, 10), (3, 9, 15), (3, 10, 10),
(4, 8, 10), (4, 9, 15), (4, 10, 15), (4, 11, 10), (4, 12, 5), (4, 13, 5),
(5, 7, 5), (5, 8, 10), (5, 9, 10), (5, 10, 10), (5, 11, 5),
(6, 7, 5), (6, 8, 5), (6, 9, 10), (6, 10, 5), (6, 11, 3), (6, 12, 2),
(7, 7, 10), (7, 8, 15), (7, 9, 20), (7, 10, 20), (7, 11, 15), (7, 12, 10);