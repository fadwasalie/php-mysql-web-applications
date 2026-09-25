<?php
/**
 * db.php — mysqli connection for the earthmoving system.
 * Uses mysqli so we can demonstrate prepared statements with bind_param.
 */

function getConnection(): mysqli
{
    $host = 'localhost';
    $user = 'root';        // change to your MySQL username
    $pass = '';            // change to your MySQL password
    $db   = 'earthmoving';

    // mysqli throws mysqli_sql_exception on error (PHP 8.1+ default)
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    return new mysqli($host, $user, $pass, $db);
}

function e(?string $v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}
