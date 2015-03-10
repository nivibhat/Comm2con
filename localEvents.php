<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US">
<head>
	<script src="includes/js/jquery-1.10.2.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js"></script>
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCxfFRgFYNDht5u00x-8YzIRyHBU36QS-M&sensor=false"></script>
	<link rel="stylesheet" type="text/css" href="includes/styles/style.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="includes/styles/event_style.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="includes/styles/card_style.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="includes/styles/footer_header_style.css" media="screen" />
	<script src="includes/js/jquery_custom_flip.js"></script>
</head>

<body>
<?php
	require_once 'includes/constants/sql_constants.php';
	require_once 'includes/constants/card_print.php';
	//include 'google_map_api.php';
	secure_page();
	return_meta("Local Events!");
	$msg = NULL;
	$user_id = $_SESSION['user_id'];
?>
<script>
 
$(function(){
	// If the user clicks on the save button in the local events page, get the event id, userid and store the details into the table and display the details into the dashboard
	$(".save_event").click(function() {
		var event_id = $(this).attr('rel');
		var datastring = "event_id="+event_id;
		
		$.ajax({
			type: "POST",
			url: "<?php echo BASE; ?>/includes/ajax_functions/event_interactions.php?cmd=save_event",
			data: datastring,
			success: function(response){
				var results = JSON.parse(response);
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
			}
		});
		
		return false;
	});

	//Capture the attendance and update the table : this ajax request will send the data to event_interactions.php
	$(".attending_radio").change(function() {
		var event_id = $(this).attr('rel');
		
		// checks whether the attending checkbox is checked or not
		if(this.checked) {
			var datastring = "attending=true&event_id="+event_id;
		}
		else {
			var datastring = "attending=false&event_id="+event_id;
		}
		
		$.ajax({
			type: "POST",
			url: "<?php echo BASE; ?>/includes/ajax_functions/event_interactions.php?cmd=attending",
			data: datastring,
			success: function(response) {
				var results = JSON.parse(response);
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
			}
		});

		return false;
	});
});

//Function to display the google map based on the event location. This function makes use of google's geocode api.
function initialize() {
	var lat = '';
	var lng = '';
	var zip = $(".zipcode").attr('rel');
	var event_id = $(".event_id").attr('rel');
	var map_canvas = "map_canvas_"+event_id;
	
	var country = "USA";
	var geocoder = new google.maps.Geocoder();
	geocoder.geocode({ 'address':zip+ ','+country}, function(results, status) {
		if (status === google.maps.GeocoderStatus.OK) {
			lat = results[0].geometry.location.lat();
			lng = results[0].geometry.location.lng();
			
			var mapOptions = {
				zoom: 9,
				center: new google.maps.LatLng(lat,lng)
			};

			var map = new google.maps.Map(document.getElementById(map_canvas),mapOptions);

			map.setCenter(results[0].geometry.location);
			var center = map.getCenter();
			google.maps.event.trigger(map, 'resize');
			map.setCenter(center);
			var marker = new google.maps.Marker({
				map: map,
				position: results[0].geometry.location
			});

		}
		else {
			// alert("Geocode was not successful for the following reason: " + status);
		}
	});
}
</script>
<?php
include_once ('includes/header.inc.php');
include('includes/navigation.inc.php'); 
?>

<div class="content leftmenu">
	<div class="colright">
		<div class="col1">
			<!-- Left Column start -->
			<?php include('includes/left_column.inc.php'); ?>
			<!-- Left Column end -->
		</div>
		
		<div class="col2">
			<!-- Middle Column start -->
			<span class="success" style="display:none;"></span>
			<span class="error" style="display:none;">Please enter some text</span>
			
			<h1>Upcoming events in your area</h1>
			<?php
			// front of the card: call the retrieve_event function to retrive all event details based ont he user's location. defined in sql_constants.php
			$results = retrieve_future_event($user_id);
			// if future events are found for the user, print an event card for each event found
			if($results) {
				foreach ($results as $r) {
					// defined in includes/constants/card_print.php
					print_event_card($r);
				}
			}
			else {
			?>
				<div class="back">
					<h2>No upcoming events found! </h2>
					Add an event <a href="userProfile.php">here</a>
				</div>
			<?php 
			}
			?>
		
		</div>   <!-- end of col2-->
	</div>
</div>

<div id="footer">
	<?php include('includes/footer.inc.php'); ?>
</div>
</body>
</html>