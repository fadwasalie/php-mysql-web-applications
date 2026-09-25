<?php
/**
 * update_profile.php — lets the logged-in client update their name, phone,
 * and optionally their password (re-hashed with password_hash).
 */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

$message = '';
$messageType = 'err';
$client = null;

try {
    $pdo = getConnection();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $fullName = trim($_POST['full_name'] ?? '');
        $phone    = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($fullName === '') {
            $message = 'Full name is required.';
        } else {
            if ($password !== '') {
                // Update including a new hashed password
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare(
                    "UPDATE clients SET full_name = :name, phone = :phone, password = :pass
                     WHERE client_id = :cid"
                );
                $stmt->execute([
                    ':name'  => $fullName,
                    ':phone' => $phone,
                    ':pass'  => $hash,
                    ':cid'   => $_SESSION['client_id'],
                ]);
            } else {
                // Update without changing the password
                $stmt = $pdo->prepare(
                    "UPDATE clients SET full_name = :name, phone = :phone WHERE client_id = :cid"
                );
                $stmt->execute([
                    ':name'  => $fullName,
                    ':phone' => $phone,
                    ':cid'   => $_SESSION['client_id'],
                ]);
            }
            $_SESSION['full_name'] = $fullName;
            $messageType = 'ok';
            $message = 'Profile updated.';
        }
    }

    // Load current profile
    $stmt = $pdo->prepare(
        "SELECT full_name, email, phone FROM clients WHERE client_id = :cid"
    );
    $stmt->execute([':cid' => $_SESSION['client_id']]);
    $client = $stmt->fetch();
} catch (PDOException $ex) {
    $message = 'Database error: ' . $ex->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Update Profile</title>
    <style>
        body{font-family:Arial,sans-serif;margin:30px;background:#f5f6fa;}
        .card{background:#fff;padding:20px 25px;max-width:380px;border-radius:8px;
              box-shadow:0 2px 6px rgba(0,0,0,.1);}
        label{display:block;margin-top:10px;font-weight:bold;font-size:14px;}
        input{width:100%;padding:8px;margin-top:4px;box-sizing:border-box;}
        button{margin-top:15px;padding:9px 18px;background:#c2185b;color:#fff;
               border:none;border-radius:4px;cursor:pointer;}
        .ok{color:#0a7d2c;font-weight:bold;} .err{color:#c0392b;font-weight:bold;}
        a{color:#c2185b;} small{color:#777;}
    </style>
</head>
<body>
    <div class="card">
        <h1>Update Profile</h1>
        <?php if ($message): ?><p class="<?= $messageType ?>"><?= e($message) ?></p><?php endif; ?>
        <?php if ($client): ?>
            <form method="post" action="update_profile.php">
                <label>Full Name</label>
                <input type="text" name="full_name" value="<?= e($client['full_name']) ?>" required>
                <label>Email (read-only)</label>
                <input type="email" value="<?= e($client['email']) ?>" readonly>
                <label>Phone</label>
                <input type="text" name="phone" value="<?= e($client['phone']) ?>">
                <label>New Password <small>(leave blank to keep current)</small></label>
                <input type="password" name="password">
                <button type="submit">Save Profile</button>
            </form>
        <?php endif; ?>
        <p><a href="dashboard.php">Back to dashboard</a></p>
    </div>
</body>
</html>
