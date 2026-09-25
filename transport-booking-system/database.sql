-- Question 2: Transport Company Bookings Database

CREATE DATABASE IF NOT EXISTS transport_company
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE transport_company;

CREATE TABLE IF NOT EXISTS bookings (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    passenger_name VARCHAR(150)   NOT NULL,
    destination    VARCHAR(150)   NOT NULL,
    fare           DECIMAL(10,2)  NOT NULL,
    created_at     TIMESTAMP      DEFAULT CURRENT_TIMESTAMP
);

-- At least 5 sample records for testing
INSERT INTO bookings (passenger_name, destination, fare) VALUES
('Alice Kambo',           'Cape Town',      450.00),
('Tshelofelo Smith',      'Johannesburg',   680.00),
('Cynthia Neo Mosianane', 'Durban',         720.00),
('David Brown',           'Pretoria',       300.00),
('Emilia Davids',         'Port Elizabeth', 550.00),
('R Mnzivu',              'Cape Town',     1800.00);
