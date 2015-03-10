<?php
// Logs the user out
require 'includes/constants/sql_constants.php';
$message = urlencode("You have logged out successfully");
logout($message);