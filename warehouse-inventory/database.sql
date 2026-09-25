-- Question 1: Warehouse Inventory Database

CREATE DATABASE IF NOT EXISTS Inventory
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE Inventory;

CREATE TABLE IF NOT EXISTS warehouse_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(150) NOT NULL,
    quantity INT NOT NULL,
    supplier VARCHAR(150) NOT NULL,
    category VARCHAR(80) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_item_supplier (item_name, supplier)
);

-- Sample records for testing
INSERT INTO warehouse_items (item_name, quantity, supplier, category) VALUES
('A4 Notepad 96 pages', 60, 'Waltons', 'Stationery'),
('A4 Notepad 192 pages', 50, 'Waltons', 'Stationery'),
('Desk phone', 250, 'ABC Digital', 'Electronics'),
('Printer Paper', 100, 'Stationery Depot', 'Stationery'),
('Toner Cartridge', 20, 'ABC Digital', 'Electronics');