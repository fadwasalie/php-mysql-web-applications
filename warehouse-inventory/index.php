<?php
/**
 * index.php  — Warehouse data capture form + insertion handler.
 */
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Inventory.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and trim input
    $itemName = trim($_POST['item_name'] ?? '');
    $quantity = trim($_POST['quantity'] ?? '');
    $supplier = trim($_POST['supplier'] ?? '');
    $category = trim($_POST['category'] ?? '');

    // ---- Validation / error handling ----
    $errors = [];
    if ($itemName === '')          { $errors[] = 'Item name is required.'; }
    if ($supplier === '')          { $errors[] = 'Supplier is required.'; }
    if ($category === '')          { $errors[] = 'Category is required.'; }
    if ($quantity === '' || !ctype_digit($quantity)) {
        $errors[] = 'Quantity must be a positive whole number.';
    }

    if (empty($errors)) {
        try {
            $inventory = new Inventory(new Database());
            $result = $inventory->addItem($itemName, (int)$quantity, $supplier, $category);
            $messageType = str_starts_with($result, 'Success') ? 'ok' : 'err';
            $message = $result;
        } catch (PDOException $e) {
            $messageType = 'err';
            $message = 'Could not connect to the database: ' . $e->getMessage();
        }
    } else {
        $messageType = 'err';
        $message = implode('<br>', $errors);
    }
}

// helper to escape output
function e(string $v): string { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warehouse Inventory</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background:#f5f6fa; }
        .card { background:#fff; padding:20px 25px; max-width:460px; border-radius:8px;
                box-shadow:0 2px 6px rgba(0,0,0,.1); margin-bottom:20px; }
        h1 { font-size:22px; }
        label { display:block; margin-top:10px; font-weight:bold; font-size:14px; }
        input, select { width:100%; padding:8px; margin-top:4px; box-sizing:border-box; }
        button { margin-top:15px; padding:9px 18px; background:#273c75; color:#fff;
                 border:none; border-radius:4px; cursor:pointer; }
        .ok  { color:#0a7d2c; font-weight:bold; }
        .err { color:#c0392b; font-weight:bold; }
        a { color:#273c75; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Warehouse Form</h1>
        <?php if ($message): ?>
            <p class="<?= $messageType === 'ok' ? 'ok' : 'err' ?>"><?= $message ?></p>
        <?php endif; ?>
        <form method="post" action="index.php">
            <label>Item</label>
            <input type="text" name="item_name" required>

            <label>Quantity</label>
            <input type="number" name="quantity" min="1" required>

            <label>Supplier</label>
            <input type="text" name="supplier" required>

            <label>Category</label>
            <select name="category" required>
                <option value="">-- Select --</option>
                <option value="Electronics">Electronics</option>
                <option value="Furniture">Furniture</option>
                <option value="Stationery">Stationery</option>
            </select>

            <button type="submit">Add Item</button>
        </form>
    </div>

    <div class="card">
        <h1>Actions</h1>
        <p><a href="search.php">Search by supplier</a></p>
        <p><a href="export.php">Export inventory to CSV</a></p>
    </div>
</body>
</html>
