<?php
/**
 * export.php — Streams the full inventory list to the browser as a CSV download.
 */
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Inventory.php';

try {
    $inventory = new Inventory(new Database());
    $items = $inventory->getAll();

    // Tell the browser this is a downloadable CSV file
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="inventory_export.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Item', 'Quantity', 'Supplier', 'Category']);

    foreach ($items as $row) {
        fputcsv($output, [
            $row['id'], $row['item_name'], $row['quantity'],
            $row['supplier'], $row['category'],
        ]);
    }
    fclose($output);
    exit;
} catch (PDOException $e) {
    header('Content-Type: text/plain');
    echo 'Export failed: ' . $e->getMessage();
}
