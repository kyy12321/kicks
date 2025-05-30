CREATE TABLE IF NOT EXISTS sizes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    size DECIMAL(4,1) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS product_sizes (
    product_id INT NOT NULL,
    size_id INT NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    PRIMARY KEY (product_id, size_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (size_id) REFERENCES sizes(id) ON DELETE CASCADE
);

INSERT INTO sizes (size) VALUES 
(6.0), (6.5), (7.0), (7.5), (8.0), (8.5), (9.0), (9.5),
(10.0), (10.5), (11.0), (11.5), (12.0), (12.5), (13.0);