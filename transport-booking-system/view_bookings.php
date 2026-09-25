<?php
/**
 * view_bookings.php — displays all bookings in a dynamic table,
 * calculates total/average/highest fares using arrays + loops,
 * applies conditional formatting for fares over the threshold,
 * and supports deleting a record.
 */
require_once __DIR__ . '/db.php';

const FARE_THRESHOLD = 500;   // fares above this are highlighted

$error = '';
$bookings = [];

try {
    $pdo = getConnection();

    // Handle delete (simple record processing)
    if (isset($_GET['delete']) && ctype_digit($_GET['delete'])) {
        $del = $pdo->prepare("DELETE FROM bookings WHERE id = :id");
        $del->execute([':id' => (int)$_GET['delete']]);
        header('Location: view_bookings.php');
        exit;
    }

    $bookings = $pdo->query(
        "SELECT id, passenger_name, destination, fare FROM bookings ORDER BY id"
    )->fetchAll();
} catch (PDOException $ex) {
    $error = 'Database error: ' . $ex->getMessage();
}

// ---- Summary calculations using arrays + loops ----
$fares = array_map(static fn($row) => (float)$row['fare'], $bookings);
$totalFare  = 0.0;
foreach ($fares as $f) { $totalFare += $f; }        // loop for total
$count       = count($fares);
$averageFare = $count > 0 ? $totalFare / $count : 0;
$highestFare = $count > 0 ? max($fares) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Booking Records</title>
    <style>
        body{font-family:Arial,sans-serif;margin:30px;background:#f5f6fa;}
        table{border-collapse:collapse;background:#fff;margin-top:10px;}
        th,td{border:1px solid #ccc;padding:8px 12px;text-align:left;}
        th{background:#273c75;color:#fff;}
        .high{color:#c0392b;font-weight:bold;}  /* conditional formatting */
        .summary{background:#fff;padding:15px 20px;margin-top:20px;max-width:300px;
                 border-radius:6px;box-shadow:0 2px 6px rgba(0,0,0,.1);}
        a{color:#273c75;}
    </style>
</head>
<body>
    <h1>Booking Records</h1>

    <?php if ($error): ?>
        <p style="color:#c0392b;"><?= e($error) ?></p>
    <?php elseif ($count === 0): ?>
        <p>No booking records found.</p>
    <?php else: ?>
        <table>
            <tr><th>ID</th><th>Passenger Name</th><th>Destination</th><th>Fare</th><th>Action</th></tr>
            <?php foreach ($bookings as $b): ?>
                <?php $isHigh = (float)$b['fare'] > FARE_THRESHOLD; ?>
                <tr>
                    <td><?= e((string)$b['id']) ?></td>
                    <td><?= e($b['passenger_name']) ?></td>
                    <td><?= e($b['destination']) ?></td>
                    <td class="<?= $isHigh ? 'high' : '' ?>">
                        <?= number_format((float)$b['fare'], 2) ?>
                    </td>
                    <td><a href="view_bookings.php?delete=<?= (int)$b['id'] ?>"
                           onclick="return confirm('Delete this booking?');">Delete</a></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <div class="summary">
            <h2>Summary Report</h2>
            <p>Total Fare: <?= number_format($totalFare, 2) ?></p>
            <p>Average Fare: <?= number_format($averageFare, 2) ?></p>
            <p>Highest Fare: <?= number_format($highestFare, 2) ?></p>
        </div>
    <?php endif; ?>

    <p style="margin-top:20px;">
        <a href="booking_form.html">Add Booking</a> |
        <a href="search.php">Search by Destination</a> |
        <a href="export.php">Export to CSV</a>
    </p>
</body>
</html>
