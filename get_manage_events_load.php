<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
<script src="includes/js/jquery_custom_flip.js"></script>
<?php
//Include the database
require_once 'includes/constants/sql_constants.php';
require_once 'includes/constants/card_print.php';

if(!isset($_SESSION)) {
	secure_page();
}
else {
	
}
$user_id = $_SESSION['user_id'];
 
	
//Call the dbc function which retrieves the contents
print_user_manage_events_card($user_id);
?>