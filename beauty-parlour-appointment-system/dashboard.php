<?php
/**
 * dashboard.php — landing page after login. Shows navigation menu.
 */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Dashboard</title>
    <style>
        body{font-family:Arial,sans-serif;margin:30px;background:#f5f6fa;}
        a{display:block;margin:8px 0;color:#c2185b;}
        .logout{color:#555;}
    </style>
</head>
<body>
    <h1>Welcome, <?= e($_SESSION['email']) ?></h1>
    <a href="add_appointment.php">Add Appointment</a>
    <a href="view_appointments.php">View Appointments</a>
    <a href="update_profile.php">Update Profile</a>
    <a class="logout" href="logout.php">Logout</a>
</body>
</html>
