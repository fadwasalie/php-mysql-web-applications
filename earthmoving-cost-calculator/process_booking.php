<?php
/**
 * process_booking.php — validates input, fetches the customer securely,
 * calculates the full cost breakdown and displays it.
 *
 * Charging rules:
 *   Distance charge : R45 per km
 *   Labour cost     : 5% of the distance charge
 *   Work time       : 4.5 hours per km
 *   Hourly fee      : R1250 per hour
 *   VAT             : 15% added to the total
 */
require_once __DIR__ . '/db.php';

// Constants for the charging rules
const RATE_PER_KM    = 45;
const LABOUR_PERCENT = 0.05;
const HOURS_PER_KM   = 4.5;
const HOURLY_FEE     = 1250;
const VAT_RATE       = 0.15;

$error = '';
$data  = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerCode = trim($_POST['customer_code'] ?? '');
    $distanceRaw  = trim($_POST['distance'] ?? '');

    // ---- Input validation ----
    if (empty($customerCode)) {
        $error = 'Please select a customer.';
    } elseif (!is_numeric($distanceRaw) || (float)$distanceRaw <= 0) {
        $error = 'Distance must be a positive number.';
    } else {
        $distance = (float)$distanceRaw;

        try {
            $conn = getConnection();

            // ---- Secure query execution using a prepared statement ----
            $stmt = $conn->prepare(
                "SELECT customer_code, name, city FROM customers WHERE customer_code = ?"
            );
            $stmt->bind_param("s", $customerCode);
            $stmt->execute();
            $result = $stmt->get_result();
            $customer = $result->fetch_assoc();
            $stmt->close();
            $conn->close();

            if (!$customer) {
                $error = 'Customer not found.';
            } else {
                // ---- Cost calculation logic ----
                $distanceCharge = $distance * RATE_PER_KM;
                $labourCost     = $distanceCharge * LABOUR_PERCENT;
                $totalHours     = $distance * HOURS_PER_KM;
                $hourlyCost     = $totalHours * HOURLY_FEE;

                $totalBeforeVat = $distanceCharge + $labourCost + $hourlyCost;
                $vat            = $totalBeforeVat * VAT_RATE;
                $totalCost      = $totalBeforeVat + $vat;

                $data = [
                    'customer'       => $customer,
                    'distance'       => $distance,
                    'totalHours'     => $totalHours,
                    'distanceCharge' => $distanceCharge,
                    'labourCost'     => $labourCost,
                    'hourlyCost'     => $hourlyCost,
                    'totalBeforeVat' => $totalBeforeVat,
                    'vat'            => $vat,
                    'totalCost'      => $totalCost,
                ];
            }
        } catch (mysqli_sql_exception $ex) {
            $error = 'Database error: ' . $ex->getMessage();
        }
    }
}

function money($n): string { return 'R' . number_format((float)$n, 2); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Project Cost Breakdown</title>
    <style>
        body{font-family:Arial,sans-serif;margin:30px;background:#f5f6fa;}
        .card{background:#fff;padding:20px 25px;max-width:460px;border-radius:8px;
              box-shadow:0 2px 6px rgba(0,0,0,.1);}
        p{margin:6px 0;} .total{font-weight:bold;font-size:18px;border-top:2px solid #273c75;
              padding-top:8px;margin-top:12px;}
        .err{color:#c0392b;font-weight:bold;} a{color:#273c75;}
    </style>
</head>
<body>
    <div class="card">
        <h1>Project Cost Breakdown</h1>
        <?php if ($error): ?>
            <p class="err"><?= e($error) ?></p>
        <?php elseif ($data): ?>
            <p>Customer Code: <?= e($data['customer']['customer_code']) ?></p>
            <p>Customer Name: <?= e($data['customer']['name']) ?></p>
            <p>City: <?= e($data['customer']['city']) ?></p>
            <p>Distance: <?= e((string)$data['distance']) ?> km</p>
            <p>Total Work Time: <?= e((string)$data['totalHours']) ?> hours</p>
            <p>Distance Charge (R45/km): <?= money($data['distanceCharge']) ?></p>
            <p>Labour Cost (5%): <?= money($data['labourCost']) ?></p>
            <p>Hourly Cost (R1250/hr): <?= money($data['hourlyCost']) ?></p>
            <p>Total Cost Before 15% VAT: <?= money($data['totalBeforeVat']) ?></p>
            <p>VAT (15%): <?= money($data['vat']) ?></p>
            <p class="total">Total Project Cost: <?= money($data['totalCost']) ?></p>
        <?php else: ?>
            <p>No data submitted.</p>
        <?php endif; ?>
        <p><a href="earth_booking_form.php">&larr; New calculation</a></p>
    </div>
</body>
</html>
