<?php
/**
 * logout.php — destroys the session and clears the Remember Me cookie.
 */
session_start();
$_SESSION = [];
session_destroy();
setcookie('remember_email', '', time() - 3600, '/');
header('Location: login.php');
exit;
