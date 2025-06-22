<?php
// session_start() is now handled in header.php
// Start session first, before any other logic
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to home page
header("Location: ../index.php");
exit();
?> 