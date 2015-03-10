<!-- This file is used to display public events on the index page -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script src="includes/js/jquery_custom_flip.js"></script>

<script>
 
$(function(){
	// show the public events card, but hide the elements for logged in users
	$('.flip').hide();
	$('.save_event').hide();
	$('.attending_radio').hide();
	$('#attending_label').hide();
});
</script>

<?php
require_once 'includes/constants/sql_constants.php';
require_once 'includes/constants/card_print.php';

//Pre-assign our variables to avoid undefined indexes
$username = NULL;
$pass2 = NULL;
$msg = NULL;
$err = array();
global $link;
$results = array();

//query the public events and display them randomly in the public_event section at the registration page
$q = "SELECT t1.event_id, t1.event_date, t1.event_desc, t1.event_id, t1.event_name, t5.first_name, AES_DECRYPT(t5.email, '$salt') as email, t5.phone, t5.last_name, t3.venue_address, t3.venue_name, t4.city, t4.zipcode as zipcode, t4.state
	FROM " . EVENT . " AS t1
	LEFT JOIN " . EVENT_TYPE . " AS t2 ON t1.e_type_id = t2.e_type_id
	LEFT JOIN " . VENUE . " AS t3 ON t1.venue_id = t3.venue_id
	LEFT JOIN " . LOCATION . " AS t4 ON t3.e_loc_id = t4.e_loc_id
	LEFT JOIN " . USERS . " AS t5 ON t1.user_id = t5.user_id
	WHERE t1.event_status=1 AND t1.event_scope = 'public' AND t1.event_date > CURDATE() ORDER BY RAND() LIMIT 1;";
	
if($event_query = mysqli_query($link,$q)) {
	$results = mysqli_fetch_assoc($event_query);
	mysqli_free_result($event_query);
}

print_event_card($results);
?>