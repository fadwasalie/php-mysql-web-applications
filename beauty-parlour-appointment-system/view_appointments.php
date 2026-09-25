<?php
/**
 * view_appointments.php — lists the logged-in client's appointments using a loop,
 * and lets them cancel one. Update is handled by update_appointment.php.
 */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

$error = '';
$appointments = [];

try {
    $pdo = getConnection();

    // Process a cancellation (sets status to Cancelled)
    if (isset($_GET['cancel']) && ctype_digit($_GET['cancel'])) {
        $cancel = $pdo->prepare(
            "UPDATE appointments SET status = 'Cancelled'
             WHERE appointment_id = :aid AND client_id = :cid"
        );
        $cancel->execute([
            ':aid' => (int)$_GET['cancel'],
            ':cid' => $_SESSION['client_id'],
        ]);
        header('Location: view_appointments.php');
        exit;
    }

    $stmt = $pdo->prepare(
        "SELECT appointment_id, appt_date, stylist, notes, status
         FROM appointments WHERE client_id = :cid ORDER BY appt_date"
    );
    $stmt->execute([':cid' => $_SESSION['client_id']]);
    $appointments = $stmt->fetchAll();
} catch (PDOException $ex) {
    $error = 'Database error: ' . $ex->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Your Appointments</title>
    <style>
        body{font-family:Arial,sans-serif;margin:30px;background:#f5f6fa;}
        table{border-collapse:collapse;background:#fff;margin-top:10px;}
        th,td{border:1px solid #ccc;padding:8px 12px;text-align:left;}
        th{background:#c2185b;color:#fff;}
        .cancelled{color:#999;text-decoration:line-through;}
        a{color:#c2185b;}
    </style>
</head>
<body>
    <h1>Your Appointments</h1>
    <?php if ($error): ?>
        <p style="color:#c0392b;"><?= e($error) ?></p>
    <?php elseif (count($appointments) === 0): ?>
        <p>No appointments found.</p>
    <?php else: ?>
        <table>
            <tr><th>Date</th><th>Stylist</th><th>Notes</th><th>Status</th><th>Actions</th></tr>
            <?php foreach ($appointments as $a): ?>
                <tr class="<?= $a['status'] === 'Cancelled' ? 'cancelled' : '' ?>">
                    <td><?= e($a['appt_date']) ?></td>
                    <td><?= e($a['stylist']) ?></td>
                    <td><?= e($a['notes']) ?></td>
                    <td><?= e($a['status']) ?></td>
                    <td>
                        <?php if ($a['status'] !== 'Cancelled'): ?>
                            <a href="update_appointment.php?id=<?= (int)$a['appointment_id'] ?>">Update</a> |
                            <a href="view_appointments.php?cancel=<?= (int)$a['appointment_id'] ?>"
                               onclick="return confirm('Cancel this appointment?');">Cancel</a>
                        <?php else: ?>
                            &mdash;
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    <p style="margin-top:20px;">
        <a href="add_appointment.php">Add Appointment</a> |
        <a href="dashboard.php">Dashboard</a>
    </p>
</body>
</html>
