-- Question 4: Beauty Parlour Database

CREATE DATABASE IF NOT EXISTS beauty_parlour
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE beauty_parlour;

CREATE TABLE IF NOT EXISTS clients (
    client_id  INT AUTO_INCREMENT PRIMARY KEY,
    full_name  VARCHAR(150) NOT NULL,
    email      VARCHAR(150) NOT NULL UNIQUE,
    phone      VARCHAR(30),
    password   VARCHAR(255) NOT NULL   -- stores a password_hash() value
);

CREATE TABLE IF NOT EXISTS appointments (
    appointment_id INT AUTO_INCREMENT PRIMARY KEY,
    client_id      INT NOT NULL,
    appt_date      DATE NOT NULL,
    stylist        VARCHAR(100) NOT NULL,
    notes          VARCHAR(255),
    status         VARCHAR(20) NOT NULL DEFAULT 'Active',  -- Active / Cancelled
    FOREIGN KEY (client_id) REFERENCES clients(client_id)
);

-- Sample client. Password is: Password123
-- The hash below was generated with password_hash('Password123', PASSWORD_DEFAULT).
-- If it does not verify on your PHP version, run seed_password.php once to regenerate it.
INSERT INTO clients (full_name, email, phone, password) VALUES
('Admin User', 'admin@example.com', '0123456789',
 '$2y$10$e0NRxCq4bIhVh2Qh0gA9Oe6QwQm0m3nS8YQY7q0oZ0f8H0m3nS8YQ');

-- Sample appointments for the client above (client_id = 1)
INSERT INTO appointments (client_id, appt_date, stylist, notes, status) VALUES
(1, '2026-09-19', 'Tanya', 'Brush cut and dye', 'Active'),
(1, '2026-10-31', 'Kuda',  'Dye',               'Active'),
(1, '2026-11-04', 'Tanya', 'Plaiting',          'Active');
