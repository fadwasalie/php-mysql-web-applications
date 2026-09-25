<?php
/**
 * search.php — filter bookings by destination (prepared statement).
 */
require_once __DIR__ . '/db.php';

$destination = trim($_GET['destination'] ?? '');
$results = [];
$error = '';

if ($destination !== '') {
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare(
            "SELECT id, passenger_name, destination, fare
             FROM bookings WHERE destination = :destination ORDER BY id"
        );
        $stmt->execute([':destination' => $destination]);
        $results = $stmt->fetchAll();
    } catch (PDOException $ex) {
        $error = 'Database error: ' . $ex->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Search by Destination</title>
    <style>
        body{font-family:Arial,sans-serif;margin:30px;background:#f5f6fa;}
        table{border-collapse:collapse;background:#fff;margin-top:10px;}
        th,td{border:1px solid #ccc;padding:8px 12px;text-align:left;}
        th{background:#273c75;color:#fff;}
        input{padding:8px;} button{padding:8px 16px;} a{color:#273c75;}
    </style>
</head>
<body>
    <form method="get" action="search.php">
        <label>Destination:</label>
        <input type="text" name="destination" value="<?= e($destination) ?>" required>
        <button type="submit">Search</button>
    </form>

    <?php if ($error): ?>
        <p style="color:#c0392b;"><?= e($error) ?></p>
    <?php elseif ($destination !== ''): ?>
        <h1>Search Results for <?= e($destination) ?></h1>
        <?php if (count($results) > 0): ?>
            <table>
                <tr><th>ID</th><th>Passenger Name</th><th>Destination</th><th>Fare</th></tr>
                <?php foreach ($results as $r): ?>
                    <tr>
                        <td><?= e((string)$r['id']) ?></td>
                        <td><?= e($r['passenger_name']) ?></td>
                        <td><?= e($r['destination']) ?></td>
                        <td><?= number_format((float)$r['fare'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No bookings found for that destination.</p>
        <?php endif; ?>
    <?php endif; ?>

    <p><a href="view_bookings.php">View all bookings</a></p>
</body>
</html>
