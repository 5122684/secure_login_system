<?php
require_once 'includes/config.php';

// Clear session
session_unset();
session_destroy();

// Redirect to login
redirect('index.php');
?>
