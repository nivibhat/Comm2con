<head>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US">
<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
<script src="includes/js/jquery_custom_flip.js"></script>
<?php
require_once 'includes/constants/sql_constants.php';
require_once 'includes/constants/card_print.php';
secure_page();
$user_id = $_SESSION['user_id'];
?>
<script>
// function to get the city and state from Google, then pass them to getCityState in order to update it in the DB
function get_city_state(zipcode) {
	var zip = zipcode;
	var country = 'United States';
	var lat;
	var lng;
	var geocoder = new google.maps.Geocoder();

	geocoder.geocode({ 'address': zipcode + ',' + country }, function (results, status) {
		if (status === google.maps.GeocoderStatus.OK) {
			geocoder.geocode({'latLng': results[0].geometry.location}, function(results, status) {
				if (status === google.maps.GeocoderStatus.OK) {
					if (results[1]) {
						var loc = getCityState(results,zipcode);
					}
				}
			});
		}
	});
}

// function to update the city and state for a zipcode in the DB
function getCityState(results,zipcode) {
	var a = results[0].address_components;
	var city, state;
	for(i = 0; i <  a.length; ++i) {
		var t = a[i].types;
		if(compIsType(t, 'administrative_area_level_1'))
			state = a[i].long_name; //store the state
		else if(compIsType(t, 'locality'))
			city = a[i].long_name; //store the city
	}


	var datastring = "zipcode="+zipcode+ "&city=" +city+"&state="+state;
	$.ajax({
		type: "POST",
		url: "<?php echo BASE; ?>/includes/ajax_functions/updateaddress.php?cmd=updatecitystate",
		data: datastring,
		success: function(response){
			// console.log(response);
		}
	});

	return false;
}

function compIsType(t, s) { 
	for(z = 0; z < t.length; ++z) 
		if(t[z] == s)
			return true;
	return false;
} 

$(function(){
	$("#create_event_div").hide();
	$("#change_food_pic_form").hide();

	// creates a jquery datepicker on all editable date areas
	$( '.datepicker').datepicker({dateFormat: "yy-mm-dd" });

	$('.manage_event').show('slide', {direction: "left"}, 400);

	$("#create_event_button").click(function() {
		$("#create_event_button").fadeOut(700);
		$('#create_event_div').show('slide', {direction: "up"}, 900);
	});

	$("#cancel_add_event").click(function() {
		$(this).closest('form').find("input[type=text], textarea").val("");
		$("#create_event_button").fadeIn(700);
		$('#create_event_div').hide('slide', {direction: "up"}, 900);
	});

	$("#change_food_pic").click(function() {
		$("#change_food_pic_form").show();
		$("#change_food_pic").hide();
	});

	$('.delete_event_button').click(function() {
		var card_id = $(this).closest('.flipper').attr('id');
		var datastring = $('#' + card_id).find('input:text[name=event_id]').val()

		datastring = "event_id=" + datastring;
		console.log(datastring);

		// ajax call to delete the event
		$.ajax({
			type: "POST",
			url: "<?php echo BASE; ?>/includes/ajax_functions/event_interactions.php?cmd=delete_event",
			data: datastring,
			success: function(response){
				// results will return with success=true or false
				// to be implemented later
				console.log(response);
				var results = JSON.parse(response);

				// Show a message at the top of the page that the event was deleted.
				$('.success').animate({opacity:1}, 2000).html("Event deleted! ").animate({opacity:0}, 6000); //Show, then hide success msg

				// a jquery effect to visually indicate that the card has been deleted
				$("#" + card_id).effect('shake', { direction: "down", distance: "15", times: "2"}, function(){
					$("#" + card_id).toggle('slide', 900);
				});

			}
		});
		return false;
	});

	// button for updating events
	$('.update_event_button').click(function() {
		// gets the closest event card in order to know which one to update
		var card_id = $(this).closest('.flipper').attr('id');

		// datastring for ajax call
		var datastring = $("#" + card_id).find(".update_event_form").serialize();
		console.log("datastring is: " + datastring);

		// The below function makes an asyncronous call to google to get the city and state associated with the provided zip code. It then updates the database when a match is found.
		get_city_state($("#" + card_id).find('.get_event_zipcode').val());

		// this call inserts the updated values in the DB
		$.ajax({
			type: "POST",
			url: "<?php echo BASE; ?>/includes/ajax_functions/event_interactions.php?cmd=update_event",
			data: datastring,
			success: function(response){

				// Show a message at the top of the page that the event was updated.
				$('.success').animate({opacity:1}, 2000).html("Event updated! ").animate({opacity:0}, 6000); //Show, then hide success msg

				// a jquery effect to visually indicate that the changes have been saved
				$("#" + card_id).effect('shake', { direction: "down", distance: "15", times: "2"}, function(){
					$("#" + card_id).toggleClass('flipped');
				});

				// parses JSON response
				console.log(response);
				updated_event = JSON.parse(response);

				// accesses the first/only array within the parsed JSON response and assigns it a variable
				updated_event = updated_event[0];
				// sets the read-only fields on the other side of the card to their updated values
 				$("#" + card_id).find('.event_name').html(updated_event.event_name);
				$("#" + card_id).find('.event_date').html("on: " + updated_event.event_date);
				$("#" + card_id).find('.venue_location').html(updated_event.venue_name + "<br>" + updated_event.venue_address + "<br>" + updated_event.city + ", " + updated_event.state + " " + updated_event.zipcode);
				$("#" + card_id).find('.event_type').html("Event Type: " + updated_event.event_type);
				$("#" + card_id).find('.event_scope').html("Event Scope: " + updated_event.event_scope);
				$("#" + card_id).find('.event_desc').html(updated_event.event_desc);

			}
		});

		return false;
	});

	$("#add_event").click(function() {
		var datastring = $("#create_event_form").serialize();
		console.log(datastring);

		get_city_state($("#create_event_div").find('.get_event_zipcode').val());

		$.ajax({
			type: "POST",
			url: "<?php echo BASE; ?>/includes/ajax_functions/event_interactions.php?cmd=add_event",
			data: datastring,
			success: function(response){

				var results = JSON.parse(response);
				console.log(results);
				var status = results['success'];
				var message = results['message'];

				if(status === 'true') {
					$('.success').fadeIn(2000).show().html(message).fadeOut(6000); //Show, then hide success msg
					$('.error').fadeOut(2000).hide(); //If showing error, fade out
				} 
				else {
					$('.error').fadeIn(2000).show().html(message).fadeOut(6000); //Show, then hide success msg
					$('.success').fadeOut(2000).hide();
				}

				$('#create_event_div').hide('slide', {direction: "up"}, 900);
				$("#create_event_button").fadeIn(700);
				$(':input','#create_event_form').not(':button, :submit, :reset, :hidden')
					.val('')
					.removeAttr('checked')
					.removeAttr('selected');
					$("#event_holder").load('get_manage_events_load.php');
			}
		});

		return false;
	});
})

</script>

<?php

$msg = NULL;
$err=NULL;

if($_POST and $_GET) {
	// if the user is adding a picture, add it to the file system and reference in user table

	if ($_GET['cmd'] == 'add_picture' || $_GET['cmd'] == 'add_event_picture'){

		if ($_FILES["file"]["error"] > 0) {
			echo "Error with the file. please use different file: " . $_FILES["file"]["error"] . "<br>";
		}
		else {
			$file_handler = $_FILES["file"];
			$picture = store_image($file_handler);
			if($_GET['cmd'] == 'add_picture') {
				// $user_info[0]['profile_picture'] = $profile_picture;
				update_user_info($user_id, NULL, NULL, NULL, NULL, $profile_picture_loc);

			}
			elseif ($_GET['cmd'] == 'add_event_picture') {
				$event_id = $_POST['event_id'];
				
				if(update_event_picture($picture,$event_id)) {
					$msg="Event picture added";
				}
				else {
					$err="Picture is not added. Try again";
				}
			}
		}
	}
}

$user_info = get_user_info($user_id);
$profile_pic = $user_info[0]['profile_picture'];

//get the event types
$event_types = get_event_types();

?>

<title>Manage Events</title>
<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
<meta name="robots" content="index, follow" />
<link rel="stylesheet" type="text/css" href="includes/styles/profile_styles.css"/>
<link rel="stylesheet" type="text/css" href="includes/styles/style.css"/>
<link rel="stylesheet" type="text/css" href="includes/styles/card_style.css"/>
</head>

<body>
<?php

include_once ('includes/header.inc.php');
include('includes/navigation.inc.php'); ?>


<div class="content leftmenu">
	<div class="colright">
		<div class="col1">
			<!-- Left Column start -->
			<?php include('includes/left_column.inc.php'); ?>
			<!-- Left Column end -->
		</div>
		<div class="col2">
			<?php
			if(isset($msg)) {
				echo '<div class="success" >'.$msg.'</div>';
			}
			elseif (isset($err)) {
				echo '<div class="error">'.$err.'</div>';
			}
			?>
			<span class="success" style="display:none;"></span>
			<span class="error" style="display:none;">Please enter some text</span>

			<div class="dashboard_sub_section">
				<?php include('includes/subnavigation.inc.php'); ?>
			</div>

			<div id="event_holder">

				<button name="create_event" id="create_event_button" style="display:block">Create an event</button>
				<!-- begin add event card -->
				<div class="card flipper" id="create_event_div" style="display:none">
					<div class="back">
						<form action="<?php echo basename($_SERVER['PHP_SELF']);?>?cmd=add_event" id="create_event_form" method="post">
							<div class="event_edit_left">
							<h3>Create a new event!</h3>
								<input style="display:none" type="text" name="user_id" value="<?php echo $user_id ?>">

								<label for="event_name">Event Name</label>
								<input type="text" name="event_name" class="get_event_name" value="">

								<label for="event_date">Event Date</label>
								<input type="text" class="datepicker" class="get_event_date" name="event_date" value="">

								<label for="venue_name">Venue Name</label>
								<input type="text" name="venue_name" class="get_venue_name" value="" >

								<label for="venue_address">Venue Street Address</label>
								<input type="text" name="venue_address" class="get_venue_address" value="">

								<label for="venue_city">Venue City</label>
								<input type="text" name="venue_city" class="get_venue_city" value="">

								<label for="venue_address">Venue State</label>
								<input type="text" name="venue_state" class="get_venue_state" value="">

								<label for="event_zipcode">Venue Zipcode</label>
								<input type="text" name="event_zipcode" class="get_event_zipcode" value="" >
							</div>
							
							<div class="event_edit_right">
								<label for="event_type">Event Type</label>
								<select name="event_type" class="get_event_type">
								<?php
									foreach($event_types as $row) {
									echo $row['event_type'];
									?>
									<option value="<?php echo $row['e_type_id']; ?>" ><?php echo $row['event_type']; ?></option>
								<?php } ?>
								</select>

								<label for="event_scope">Event Scope</label>
								<select name="event_scope" id="get_event_scope">
									<option value="public">Public</option>
									<option value="private">Private</option>
								</select>
								<label for="event_desc">Event Description</label>
								<textarea name="event_desc" class="get_event_desc" cols=20 rows=3></textarea>
							</div>
							
							<div class="event_edit_bottom">
								<button type="button" name="cancel_add" id="cancel_add_event">Cancel</button>
								<button type="button" name="add_event" id="add_event">Add Event</button>
							</div>
						</form>
					</div>
				</div>
			<!-- END Add Events Card -->

			<!-- BEGIN existing events cards -->
			<?php
				//Defined in includes/card_print.php
				print_user_manage_events_card($user_id);
			?>
			</div>
			<!-- END existing events cards -->
		</div>
		<!-- Center column end -->
	</div>
</div>

<?php include('includes/footer.inc.php'); ?>

</body>

</html>
