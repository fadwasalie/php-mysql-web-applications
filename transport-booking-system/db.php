<?php
/**
 * db.php — shared PDO connection for the bookings module.
 * Included by every other PHP file so we keep one source of truth.
 */

function getConnection(): PDO
{
    $host   = 'localhost';
    $dbName = 'transport_company';
    $user   = 'root';   // change to your MySQL username
    $pass   = '';       // change to your MySQL password

    $dsn = "mysql:host={$host};dbname={$dbName};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    return new PDO($dsn, $user, $pass, $options);
}

/** Escape helper for safe HTML output. */
function e(?string $v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}
