<?php
//Include the database
require_once 'includes/constants/sql_constants.php';
require_once 'chefProfile.php';
secure_page();
$user_id = $_SESSION['user_id'];
 
	
//Call the dbc function which retrieves the contents
 chef_profile_data($user_id); 
?>