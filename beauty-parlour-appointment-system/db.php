<?php
/**
 * db.php — PDO connection for the beauty parlour system.
 */

function getConnection(): PDO
{
    $host   = 'localhost';
    $dbName = 'beauty_parlour';
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

function e(?string $v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}
