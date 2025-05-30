# Database Schema

## Tables

### users
- id (INT, PK, AUTO_INCREMENT)
- username (VARCHAR(50), UNIQUE)
- email (VARCHAR(100), UNIQUE)
- password (VARCHAR(255))
- created_at (DATETIME)
- is_admin (TINYINT(1) DEFAULT 0)

### products
- id (INT, PK, AUTO_INCREMENT)
- name (VARCHAR(100))
- description (TEXT)
- price (DECIMAL(10,2))
- category (VARCHAR(50))
- image_path (VARCHAR(255))
- stock (INT)
- created_at (DATETIME)

### orders
- id (INT, PK, AUTO_INCREMENT)
- user_id (INT, FK to users.id)
- total_amount (DECIMAL(10,2))
- status (ENUM('pending', 'processing', 'shipped', 'delivered'))
- created_at (DATETIME)

### order_items
- id (INT, PK, AUTO_INCREMENT)
- order_id (INT, FK to orders.id)
- product_id (INT, FK to products.id)
- quantity (INT)
- price (DECIMAL(10,2))

### cart
- user_id (INT, FK to users.id)
- product_id (INT, FK to products.id)
- quantity (INT)
- Created_at (DATETIME)
- Primary Key (user_id, product_id)