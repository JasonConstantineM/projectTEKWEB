-- =============================================
-- Database: marketplace_outfit
-- Market Place OutFit - E-commerce Fashion
-- =============================================

-- Buat database
CREATE DATABASE IF NOT EXISTS marketplace_outfit;
USE marketplace_outfit;

-- =============================================
-- Tabel: users
-- =============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    phone VARCHAR(20) NULL,
    address TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Tabel: categories
-- =============================================
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Tabel: products
-- =============================================
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    description TEXT NULL,
    price DECIMAL(12, 2) NOT NULL,
    stock INT DEFAULT 0,
    image VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Tabel: carts
-- =============================================
CREATE TABLE carts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_item (user_id, product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Tabel: orders
-- =============================================
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(12, 2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'completed', 'cancelled') DEFAULT 'pending',
    shipping_address TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Tabel: order_items
-- =============================================
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(12, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Sample Data: Users
-- Password: admin123 dan password123 (bcrypt hash)
-- =============================================
INSERT INTO users (name, email, password, role, phone, address) VALUES
('Administrator', 'admin@outfit.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '081234567890', 'Jl. Admin No. 1, Jakarta'),
('Budi Santoso', 'user1@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', '081234567891', 'Jl. Merdeka No. 10, Bandung'),
('Siti Rahayu', 'user2@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', '081234567892', 'Jl. Sudirman No. 25, Surabaya');

-- =============================================
-- Sample Data: Categories
-- =============================================
INSERT INTO categories (name, description) VALUES
('Kaos', 'Berbagai macam kaos casual dan formal untuk pria dan wanita'),
('Kemeja', 'Koleksi kemeja formal dan casual dengan berbagai motif'),
('Celana', 'Celana panjang, pendek, jeans, dan chinos'),
('Jaket', 'Jaket bomber, hoodie, sweater, dan outerwear lainnya'),
('Aksesoris', 'Topi, tas, ikat pinggang, dan aksesoris fashion lainnya');

-- =============================================
-- Sample Data: Products
-- =============================================
INSERT INTO products (category_id, name, description, price, stock, image) VALUES
-- Kaos (category_id = 1)
(1, 'Kaos Polos Hitam Premium', 'Kaos polos hitam bahan cotton combed 30s, nyaman dipakai sehari-hari', 89000.00, 50, 'kaos-hitam.jpg'),
(1, 'Kaos Oversize Putih', 'Kaos oversize warna putih, bahan tebal dan adem', 125000.00, 35, 'kaos-putih.jpg'),

-- Kemeja (category_id = 2)
(2, 'Kemeja Flanel Kotak Merah', 'Kemeja flanel motif kotak-kotak warna merah, cocok untuk casual', 175000.00, 25, 'kemeja-flanel.jpg'),
(2, 'Kemeja Formal Putih Slim Fit', 'Kemeja formal putih slim fit, cocok untuk kerja dan acara formal', 225000.00, 40, 'kemeja-formal.jpg'),

-- Celana (category_id = 3)
(3, 'Celana Jeans Biru Denim', 'Celana jeans biru denim original, slim fit modern', 299000.00, 30, 'celana-jeans.jpg'),
(3, 'Celana Chinos Khaki', 'Celana chinos warna khaki, bahan stretch nyaman', 245000.00, 20, 'celana-chinos.jpg'),

-- Jaket (category_id = 4)
(4, 'Jaket Bomber Hitam', 'Jaket bomber warna hitam, bahan parasut waterproof', 350000.00, 15, 'jaket-bomber.jpg'),
(4, 'Hoodie Abu-abu Polos', 'Hoodie abu-abu bahan fleece tebal, hangat dan nyaman', 275000.00, 25, 'hoodie-abu.jpg'),

-- Aksesoris (category_id = 5)
(5, 'Topi Baseball Hitam', 'Topi baseball warna hitam, bahan cotton twill', 85000.00, 45, 'topi-baseball.jpg'),
(5, 'Tas Selempang Kanvas', 'Tas selempang bahan kanvas, cocok untuk daily use', 165000.00, 30, 'tas-selempang.jpg');

-- =============================================
-- Sample Data: Orders (untuk testing)
-- =============================================
INSERT INTO orders (user_id, total_amount, status, shipping_address) VALUES
(2, 474000.00, 'completed', 'Jl. Merdeka No. 10, Bandung'),
(3, 350000.00, 'processing', 'Jl. Sudirman No. 25, Surabaya');

-- =============================================
-- Sample Data: Order Items
-- =============================================
INSERT INTO order_items (order_id, product_id, quantity, price) VALUES
(1, 1, 2, 89000.00),
(1, 3, 1, 175000.00),
(1, 9, 1, 85000.00),
(2, 7, 1, 350000.00);

-- =============================================
-- Indexes untuk optimasi query
-- =============================================
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_carts_user ON carts(user_id);
CREATE INDEX idx_orders_user ON orders(user_id);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_order_items_order ON order_items(order_id);
