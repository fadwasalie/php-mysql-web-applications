<?php
/**
 * Inventory.php
 * Business-logic class for warehouse items.
 * Handles: secure insertion (prepared statements + transaction),
 * duplicate prevention, CSV backup, search by supplier and full export.
 */

require_once __DIR__ . '/Database.php';

class Inventory
{
    private PDO $pdo;
    private string $backupFile;

    public function __construct(Database $database)
    {
        $this->pdo = $database->connect();
        $this->backupFile = __DIR__ . '/backup.csv';
    }

    /**
     * Checks whether an item + supplier combination already exists.
     */
    public function isDuplicate(string $itemName, string $supplier): bool
    {
        $sql = "SELECT COUNT(*) FROM warehouse_items WHERE item_name = :item AND supplier = :supplier";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':item' => $itemName, ':supplier' => $supplier]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Inserts a new item using a transaction so DB write + CSV backup stay consistent.
     * @return string success or error message
     */
    public function addItem(string $itemName, int $quantity, string $supplier, string $category): string
    {
        // Duplicate prevention (same item + supplier)
        if ($this->isDuplicate($itemName, $supplier)) {
            return "Error: '{$itemName}' from '{$supplier}' already exists.";
        }

        try {
            $this->pdo->beginTransaction();

            $sql = "INSERT INTO warehouse_items (item_name, quantity, supplier, category)
                    VALUES (:item, :quantity, :supplier, :category)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':item'     => $itemName,
                ':quantity' => $quantity,
                ':supplier' => $supplier,
                ':category' => $category,
            ]);

            // Backup to CSV for redundancy
            $this->backupToCsv([$itemName, $quantity, $supplier, $category]);

            $this->pdo->commit();
            return "Success: '{$itemName}' added to inventory.";
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return "Database error: " . $e->getMessage();
        }
    }

    /**
     * Appends a single record to backup.csv. Writes a header row once.
     */
    private function backupToCsv(array $row): void
    {
        $writeHeader = !file_exists($this->backupFile);
        $handle = fopen($this->backupFile, 'a'); // append mode
        if ($handle === false) {
            throw new PDOException("Could not open backup.csv for writing.");
        }
        if ($writeHeader) {
            fputcsv($handle, ['Item', 'Quantity', 'Supplier', 'Category']);
        }
        fputcsv($handle, $row);
        fclose($handle);
    }

    /**
     * Search items by supplier (prepared statement).
     */
    public function searchBySupplier(string $supplier): array
    {
        $sql = "SELECT id, item_name, quantity, supplier, category
                FROM warehouse_items WHERE supplier = :supplier ORDER BY id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':supplier' => $supplier]);
        return $stmt->fetchAll();
    }

    /**
     * Returns the full inventory list.
     */
    public function getAll(): array
    {
        return $this->pdo->query(
            "SELECT id, item_name, quantity, supplier, category FROM warehouse_items ORDER BY id"
        )->fetchAll();
    }
}
