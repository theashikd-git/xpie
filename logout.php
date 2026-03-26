<?php
session_start();       // Start session
session_unset();       // Clear all session variables
session_destroy();     // Destroy the session
header("Location: login.php");  // Redirect immediately to login page
exit;