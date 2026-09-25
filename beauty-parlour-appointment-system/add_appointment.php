<?php
/**
 * add_appointment.php — add a new appointment for the logged-in client.
 */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

$message = '';
$messageType = 'err';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date    = trim($_POST['appt_date'] ?? '');
    $stylist = trim($_POST['stylist'] ?? '');
    $notes   = trim($_POST['notes'] ?? '');

    if ($date === '' || $stylist === '') {
        $message = 'Date and stylist are required.';
    } else {
        try {
            $pdo = getConnection();
            $stmt = $pdo->prepare(
                "INSERT INTO appointments (client_id, appt_date, stylist, notes, status)
                 VALUES (:cid, :date, :stylist, :notes, 'Active')"
            );
            $stmt->execute([
                ':cid'     => $_SESSION['client_id'],
                ':date'    => $date,
                ':stylist' => $stylist,
                ':notes'   => $notes,
            ]);
            $messageType = 'ok';
            $message = 'Appointment added.';
        } catch (PDOException $ex) {
            $message = 'Database error: ' . $ex->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Add Appointment</title>
    <style>
        body{font-family:Arial,sans-serif;margin:30px;background:#f5f6fa;}
        .card{background:#fff;padding:20px 25px;max-width:380px;border-radius:8px;
              box-shadow:0 2px 6px rgba(0,0,0,.1);}
        label{display:block;margin-top:10px;font-weight:bold;font-size:14px;}
        input{width:100%;padding:8px;margin-top:4px;box-sizing:border-box;}
        button{margin-top:15px;padding:9px 18px;background:#c2185b;color:#fff;
               border:none;border-radius:4px;cursor:pointer;}
        .ok{color:#0a7d2c;font-weight:bold;} .err{color:#c0392b;font-weight:bold;}
        a{color:#c2185b;}
    </style>
</head>
<body>
    <div class="card">
        <h1>Add Appointment</h1>
        <?php if ($message): ?><p class="<?= $messageType ?>"><?= e($message) ?></p><?php endif; ?>
        <form method="post" action="add_appointment.php">
            <label>Date</label>
            <input type="date" name="appt_date" required>
            <label>Stylist</label>
            <input type="text" name="stylist" required>
            <label>Notes</label>
            <input type="text" name="notes">
            <button type="submit">Add Appointment</button>
        </form>
        <p><a href="view_appointments.php">View appointments</a> |
           <a href="dashboard.php">Dashboard</a></p>
    </div>
</body>
</html>
