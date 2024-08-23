<?php
session_start(); // Start the session
session_unset(); // Clear all session variables
session_destroy(); // Destroy the session
header('Location: prijava.php'); // Redirect to the login page
exit; // Ensure no further code is executed

