-- Question 3: Earthmoving Company Database

CREATE DATABASE IF NOT EXISTS earthmoving
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE earthmoving;

CREATE TABLE IF NOT EXISTS customers (
    customer_id   INT AUTO_INCREMENT PRIMARY KEY,
    customer_code VARCHAR(20)  NOT NULL UNIQUE,
    name          VARCHAR(150) NOT NULL,
    city          VARCHAR(100) NOT NULL
);

-- Sample data for testing
INSERT INTO customers (customer_code, name, city) VALUES
('C001', 'Alpha Construction', 'Cape Town'),
('C002', 'Beta Builders',      'Johannesburg'),
('C003', 'Gamma Roadworks',    'Durban'),
('C004', 'Delta Earthworks',   'Pretoria');
