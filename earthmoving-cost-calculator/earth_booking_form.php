<?php
/**
 * earth_booking_form.php — Earthmoving booking form.
 * Loads the customer list from the DB into a dropdown, and asks for distance.
 */
require_once __DIR__ . '/db.php';

$customers = [];
$error = '';

try {
    $conn = getConnection();
    $result = $conn->query("SELECT customer_code, name FROM customers ORDER BY customer_code");
    while ($row = $result->fetch_assoc()) {
        $customers[] = $row;
    }
    $conn->close();
} catch (mysqli_sql_exception $ex) {
    $error = 'Could not load customers: ' . $ex->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Earthmoving Booking Form</title>
    <style>
        body{font-family:Arial,sans-serif;margin:30px;background:#f5f6fa;}
        .card{background:#fff;padding:20px 25px;max-width:420px;border-radius:8px;
              box-shadow:0 2px 6px rgba(0,0,0,.1);}
        label{display:block;margin-top:10px;font-weight:bold;font-size:14px;}
        select,input{width:100%;padding:8px;margin-top:4px;box-sizing:border-box;}
        button{margin-top:15px;padding:9px 18px;background:#273c75;color:#fff;
               border:none;border-radius:4px;cursor:pointer;}
        .err{color:#c0392b;}
    </style>
</head>
<body>
    <div class="card">
        <h1>Earthmoving Booking Form</h1>
        <?php if ($error): ?><p class="err"><?= e($error) ?></p><?php endif; ?>
        <form method="post" action="process_booking.php">
            <label>Customer</label>
            <select name="customer_code" required>
                <option value="">-- Select customer --</option>
                <?php foreach ($customers as $c): ?>
                    <option value="<?= e($c['customer_code']) ?>">
                        <?= e($c['customer_code']) ?> - <?= e($c['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Distance (km)</label>
            <input type="number" name="distance" min="1" step="0.1" required>

            <button type="submit">Calculate Project Cost</button>
        </form>
    </div>
</body>
</html>
