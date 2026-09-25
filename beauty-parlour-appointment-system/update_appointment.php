<?php
/**
 * update_appointment.php — edit an existing appointment belonging to the client.
 */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

$message = '';
$messageType = 'err';
$appointment = null;

$id = 0;
if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
    $id = (int)$_GET['id'];
} elseif (isset($_POST['appointment_id']) && ctype_digit($_POST['appointment_id'])) {
    $id = (int)$_POST['appointment_id'];
}

try {
    $pdo = getConnection();

    // Handle the update submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $date    = trim($_POST['appt_date'] ?? '');
        $stylist = trim($_POST['stylist'] ?? '');
        $notes   = trim($_POST['notes'] ?? '');

        if ($date === '' || $stylist === '') {
            $message = 'Date and stylist are required.';
        } else {
            $upd = $pdo->prepare(
                "UPDATE appointments SET appt_date = :date, stylist = :stylist, notes = :notes
                 WHERE appointment_id = :aid AND client_id = :cid"
            );
            $upd->execute([
                ':date'    => $date,
                ':stylist' => $stylist,
                ':notes'   => $notes,
                ':aid'     => $id,
                ':cid'     => $_SESSION['client_id'],
            ]);
            $messageType = 'ok';
            $message = 'Appointment updated.';
        }
    }

    // Load the current record (belonging to this client only)
    $stmt = $pdo->prepare(
        "SELECT appointment_id, appt_date, stylist, notes
         FROM appointments WHERE appointment_id = :aid AND client_id = :cid"
    );
    $stmt->execute([':aid' => $id, ':cid' => $_SESSION['client_id']]);
    $appointment = $stmt->fetch();

    if (!$appointment) {
        $message = 'Appointment not found.';
    }
} catch (PDOException $ex) {
    $message = 'Database error: ' . $ex->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Update Appointment</title>
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
        <h1>Update Appointment</h1>
        <?php if ($message): ?><p class="<?= $messageType ?>"><?= e($message) ?></p><?php endif; ?>
        <?php if ($appointment): ?>
            <form method="post" action="update_appointment.php">
                <input type="hidden" name="appointment_id" value="<?= (int)$appointment['appointment_id'] ?>">
                <label>Date</label>
                <input type="date" name="appt_date" value="<?= e($appointment['appt_date']) ?>" required>
                <label>Stylist</label>
                <input type="text" name="stylist" value="<?= e($appointment['stylist']) ?>" required>
                <label>Notes</label>
                <input type="text" name="notes" value="<?= e($appointment['notes']) ?>">
                <button type="submit">Save Changes</button>
            </form>
        <?php endif; ?>
        <p><a href="view_appointments.php">Back to appointments</a></p>
    </div>
</body>
</html>
