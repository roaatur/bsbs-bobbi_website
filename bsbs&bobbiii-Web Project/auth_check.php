<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is not logged in
if (!isset($_SESSION['admin_name'])) {
    header("Location: adminlogin.php");
    exit();
}
?>