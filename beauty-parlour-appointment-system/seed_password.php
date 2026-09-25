<?php
/**
 * seed_password.php — run ONCE to (re)set the admin client's password to a
 * proper hash generated on YOUR PHP version. Visit this page in the browser,
 * then delete it (or leave it, it only updates one row).
 *
 *   Email:    admin@example.com
 *   Password: Password123
 */
require_once __DIR__ . '/db.php';

try {
    $pdo = getConnection();
    $hash = password_hash('Password123', PASSWORD_DEFAULT);

    // Update if the admin exists, otherwise insert a fresh admin client.
    $stmt = $pdo->prepare("SELECT client_id FROM clients WHERE email = :email");
    $stmt->execute([':email' => 'admin@example.com']);

    if ($stmt->fetch()) {
        $upd = $pdo->prepare("UPDATE clients SET password = :p WHERE email = :email");
        $upd->execute([':p' => $hash, ':email' => 'admin@example.com']);
        echo "Admin password reset. You can now log in with admin@example.com / Password123";
    } else {
        $ins = $pdo->prepare(
            "INSERT INTO clients (full_name, email, phone, password)
             VALUES ('Admin User', 'admin@example.com', '0123456789', :p)"
        );
        $ins->execute([':p' => $hash]);
        echo "Admin client created. Log in with admin@example.com / Password123";
    }
} catch (PDOException $ex) {
    echo 'Error: ' . $ex->getMessage();
}
