<?php
/**
 * Database.php
 * OOP + PDO database connection class for the Inventory system.
 * Uses PDO with exception mode so connection/query errors are caught cleanly.
 */

class Database
{
    private string $host = 'localhost';
    private string $dbName = 'Inventory';
    private string $user = 'root';   // change to your MySQL username
    private string $pass = '';       // change to your MySQL password
    private ?PDO $pdo = null;

    /**
     * Opens (or reuses) a PDO connection.
     * @return PDO
     * @throws PDOException when the connection fails
     */
    public function connect(): PDO
    {
        if ($this->pdo === null) {
            $dsn = "mysql:host={$this->host};dbname={$this->dbName};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // throw on errors
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // return associative arrays
                PDO::ATTR_EMULATE_PREPARES   => false,                   // real prepared statements
            ];
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
        }
        return $this->pdo;
    }
}
