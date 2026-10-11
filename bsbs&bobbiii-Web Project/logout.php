<?php
// Check and start session if not active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if session is active before destroying it
if (session_status() === PHP_SESSION_ACTIVE) {
    // Unset all session variables
    $_SESSION = array();
    
    // Destroy the session
    session_destroy();
    
    // Remove the session cookie
    setcookie('PHPSESSID', '', time()-3600, '/', '', 0, 0);
}

// Redirect to login page
header("Location: adminlogin.php");
exit();
?> 