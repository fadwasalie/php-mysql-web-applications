<?php
/**
 * export.php — export all bookings as a downloadable CSV file.
 */
require_once __DIR__ . '/db.php';

try {
    $pdo = getConnection();
    $rows = $pdo->query(
        "SELECT id, passenger_name, destination, fare FROM bookings ORDER BY id"
    )->fetchAll();

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="bookings_export.csv"');

    $out = fopen('php://output', 'w');
    fputcsv($out, ['ID', 'PassengerName', 'Destination', 'Fare']);
    foreach ($rows as $r) {
        fputcsv($out, [$r['id'], $r['passenger_name'], $r['destination'], $r['fare']]);
    }
    fclose($out);
    exit;
} catch (PDOException $ex) {
    header('Content-Type: text/plain');
    echo 'Export failed: ' . $ex->getMessage();
}
