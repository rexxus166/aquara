<?php
session_start();

// Clear session
session_unset();
session_destroy();

// Redirect ke login
header('Location: login.php');
exit;
?>
