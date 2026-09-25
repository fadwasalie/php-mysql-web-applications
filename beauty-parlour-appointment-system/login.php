<?php
/**
 * login.php — login form + authentication logic.
 * Validates credentials against a hashed password using password_verify,
 * starts a session on success, and sets a Remember Me cookie if requested.
 */
require_once __DIR__ . '/db.php';
session_start();

// If already logged in, go straight to the dashboard.
if (isset($_SESSION['client_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    // ---- Selection logic for feedback ----
    if ($email === '' || $password === '') {
        $error = 'Please enter both email and password.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        try {
            $pdo = getConnection();
            $stmt = $pdo->prepare(
                "SELECT client_id, full_name, password FROM clients WHERE email = :email"
            );
            $stmt->execute([':email' => $email]);
            $client = $stmt->fetch();

            if ($client && password_verify($password, $client['password'])) {
                // Success: start session
                $_SESSION['client_id'] = $client['client_id'];
                $_SESSION['full_name'] = $client['full_name'];
                $_SESSION['email']     = $email;

                // Remember Me: store email in a cookie for 30 days
                if ($remember) {
                    setcookie('remember_email', $email, time() + (30 * 24 * 60 * 60), '/');
                } else {
                    // clear any old cookie
                    setcookie('remember_email', '', time() - 3600, '/');
                }

                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Invalid email or password.';
            }
        } catch (PDOException $ex) {
            $error = 'Database connection failed: ' . $ex->getMessage();
        }
    }
}

// Pre-fill email from the Remember Me cookie if present.
$rememberedEmail = $_COOKIE['remember_email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beauty Parlour Login</title>
    <style>
        body{font-family:Arial,sans-serif;margin:30px;background:#f5f6fa;}
        .card{background:#fff;padding:20px 25px;max-width:360px;border-radius:8px;
              box-shadow:0 2px 6px rgba(0,0,0,.1);}
        label{display:block;margin-top:10px;font-weight:bold;font-size:14px;}
        input[type=email],input[type=password]{width:100%;padding:8px;margin-top:4px;
              box-sizing:border-box;}
        button{margin-top:15px;padding:9px 18px;background:#c2185b;color:#fff;
               border:none;border-radius:4px;cursor:pointer;}
        .err{color:#c0392b;font-weight:bold;}
    </style>
</head>
<body>
    <div class="card">
        <h1>Beauty Parlour Login</h1>
        <?php if ($error): ?><p class="err"><?= e($error) ?></p><?php endif; ?>
        <form method="post" action="login.php">
            <label>Email</label>
            <input type="email" name="email" value="<?= e($rememberedEmail) ?>" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <label style="font-weight:normal;margin-top:12px;">
                <input type="checkbox" name="remember"
                    <?= $rememberedEmail !== '' ? 'checked' : '' ?>> Remember Me
            </label>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
