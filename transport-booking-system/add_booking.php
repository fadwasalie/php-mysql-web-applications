<?php
/**
 * add_booking.php — validates and inserts a booking, then backs it up to CSV.
 */
require_once __DIR__ . '/db.php';

$message = '';
$messageType = 'err';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['passenger_name'] ?? '');
    $destination = trim($_POST['destination'] ?? '');
    $fare        = trim($_POST['fare'] ?? '');

    // ---- Validation ----
    $errors = [];
    if ($name === '')        { $errors[] = 'Passenger name is required.'; }
    if ($destination === '') { $errors[] = 'Destination is required.'; }
    if ($fare === '' || !is_numeric($fare) || (float)$fare < 0) {
        $errors[] = 'Fare must be a valid non-negative number.';
    }

    if (empty($errors)) {
        try {
            $pdo = getConnection();
            $stmt = $pdo->prepare(
                "INSERT INTO bookings (passenger_name, destination, fare)
                 VALUES (:name, :destination, :fare)"
            );
            $stmt->execute([
                ':name'        => $name,
                ':destination' => $destination,
                ':fare'        => (float)$fare,
            ]);

            // Backup to text/CSV file
            $backup = __DIR__ . '/backup.txt';
            $writeHeader = !file_exists($backup);
            $handle = fopen($backup, 'a');
            if ($writeHeader) {
                fputcsv($handle, ['PassengerName', 'Destination', 'Fare']);
            }
            fputcsv($handle, [$name, $destination, $fare]);
            fclose($handle);

            $messageType = 'ok';
            $message = 'Booking added successfully.';
        } catch (PDOException $ex) {
            $message = 'Database error: ' . $ex->getMessage();
        }
    } else {
        $message = implode('<br>', $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Add Booking</title>
    <style>
        body{font-family:Arial,sans-serif;margin:30px;}
        .ok{color:#0a7d2c;font-weight:bold;} .err{color:#c0392b;font-weight:bold;}
        a{color:#273c75;}
    </style>
</head>
<body>
    <p class="<?= $messageType ?>"><?= $message ?></p>
    <a href="booking_form.html">&larr; Back to form</a> |
    <a href="view_bookings.php">View all bookings</a>
</body>
</html>
