<?php
/**
 * auth.php — session guard. Include at the top of every protected page.
 * Redirects to login if the user is not authenticated.
 */
session_start();

if (!isset($_SESSION['client_id'])) {
    header('Location: login.php');
    exit;
}
