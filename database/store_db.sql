CREATE DATABASE IF NOT EXISTS store_db;
USE store_db;

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    category VARCHAR(50) NOT NULL DEFAULT 'Umum',
    price DECIMAL(12,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO products (name, category, price, stock) VALUES
('BTS Official Lightstick Army Bomb Ver 4', 'Lightstick', 785000.00, 15),
('Album SEVENTEEN 10th Mini Album - FML', 'Album', 320000.00, 24),
('Photocard Stray Kids 5-STAR Special Edition', 'Photocard', 85000.00, 50),
('NCT DREAM The Dream Show Official Hoodie', 'Fashion', 650000.00, 10),
('NewJeans 2nd EP Get Up (Bunny Beach Bag Ver)', 'Album', 345000.00, 18),
('TWICE Candybong ∞ (Infinity) Lightstick', 'Lightstick', 750000.00, 8)
ON DUPLICATE KEY UPDATE id=id;