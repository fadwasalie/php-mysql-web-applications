<?php
/**
 * search.php — Search inventory items by supplier.
 */
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Inventory.php';

function e(string $v): string { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }

$results = [];
$supplier = trim($_GET['supplier'] ?? '');
$error = '';

if ($supplier !== '') {
    try {
        $inventory = new Inventory(new Database());
        $results = $inventory->searchBySupplier($supplier);
    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search by Supplier</title>
    <style>
        body { font-family: Arial, sans-serif; margin:30px; background:#f5f6fa; }
        table { border-collapse:collapse; margin-top:15px; background:#fff; }
        th, td { border:1px solid #ccc; padding:8px 12px; text-align:left; }
        th { background:#273c75; color:#fff; }
        form { margin-bottom:10px; }
        input { padding:8px; }
        button { padding:8px 16px; }
        .err { color:#c0392b; }
    </style>
</head>
<body>
    <h1>Search Inventory by Supplier</h1>
    <form method="get" action="search.php">
        <label>Supplier:</label>
        <input type="text" name="supplier" value="<?= e($supplier) ?>" required>
        <button type="submit">Search</button>
    </form>

    <?php if ($error): ?>
        <p class="err"><?= e($error) ?></p>
    <?php elseif ($supplier !== ''): ?>
        <h2>Search Results for Supplier: <?= e($supplier) ?></h2>
        <?php if (count($results) > 0): ?>
            <table>
                <tr><th>ID</th><th>Item</th><th>Quantity</th><th>Supplier</th><th>Category</th></tr>
                <?php foreach ($results as $row): ?>
                    <tr>
                        <td><?= e((string)$row['id']) ?></td>
                        <td><?= e($row['item_name']) ?></td>
                        <td><?= e((string)$row['quantity']) ?></td>
                        <td><?= e($row['supplier']) ?></td>
                        <td><?= e($row['category']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No records found for that supplier.</p>
        <?php endif; ?>
    <?php endif; ?>

    <p><a href="index.php">&larr; Back to form</a></p>
</body>
</html>
