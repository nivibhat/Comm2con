<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US">
<head>
<script src="includes/js/jquery-1.10.2.js"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
<link rel="stylesheet" type="text/css" href="includes/styles/event_style.css" media="screen" />
<link rel="stylesheet" type="text/css" href="includes/styles/style.css" media="screen" />
<link rel="stylesheet" type="text/css" href="includes/styles/card_style.css" media="screen" />
<link rel="stylesheet" type="text/css" href="includes/styles/index_style.css" media="screen" />
<link rel="stylesheet" type="text/css" href="includes/styles/footer_header_style.css" media="screen" />
<script src="includes/js/jquery_custom_flip.js"></script>
<?php 
require_once 'includes/constants/sql_constants.php';
include_once 'includes/constants/card_print.php';

session_start();
?>

<script>
//function to get the city name, state based on the submitted zipcode. This utilizes the google geocoding.
function get_city_state(zipcode) {
	var zip = zipcode;
	var country = 'United States';
	var lat;
	var lng;
	var geocoder = new google.maps.Geocoder();
	alert(zip);
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

function getCityState(results,zipcode) {
	var a = results[0].address_components;
	var city, state;
	
	for(i = 0; i <  a.length; ++i) 	{
		var t = a[i].types;
		if(compIsType(t, 'administrative_area_level_1'))
			state = a[i].long_name; //store the state
		else if(compIsType(t, 'locality'))
			city = a[i].long_name; //store the city
	}
	
	var datastring = "zipcode= "+zipcode+ "&city= " +city+"&state= "+state;
	
	$.ajax({
		type: "POST",
		url:"<?php echo BASE; ?>/includes/ajax_functions/updateaddress.php?cmd=updatecitystate",
		data: datastring
		
	});
	return false;
}

function compIsType(t, s) { 
	for(z = 0; z < t.length; ++z) 
		if(t[z] == s)
			return true;
	return false;
} 
    
// sets the focus on the username field
$(document).ready(function(){
	$('#login_name').focus();
}); 

// function to refresh the public event
function refresh_content() {
	$('.card').fadeOut(700, function(){
		$(".public_event_refresh").load('public_event.php');
	});
	
}

// refreshes the public event periodically
setInterval(refresh_content, 7000 );

$(function(){
	var tooltips = $( "[title]" ).tooltip();
});
</script>
  
<?php

if($_SESSION){
	header("Location: " . BASE . "/home.php");
}

$meta_title = "Get Started";

$firstname = NULL;
$lastname = NULL;
$username = NULL;
$password = NULL;
$city= NULL;
$zipcode = NULL;
$email = NULL;
$pass2 = NULL;
$msg = NULL;
$err = array();
$e_loc_id = NULL;

//Check if the user signed up, add user to the user table.
if(isset($_POST['register'])) {
	$firstname = filter($_POST['firstname']);
	$lastname = filter($_POST['lastname']);
	$username = filter($_POST['username']);
	$password = filter($_POST['pass1']);
	$confirm_pass = filter($_POST['pass2']);
	$email = filter($_POST['email']);
	$zipcode = intval(filter($_POST['zipcode']));
	$date = date('Y-m-d');
	$user_ip = $_SERVER['REMOTE_ADDR'];
	$activation_code = rand(1000,9999);
	$community_type = 'Havyaka'; //$_POST['community_type'];

	$err = array();
	//defined in config.inc.php
	$err = add_user($firstname,$lastname,$username,$password,$confirm_pass,$email,$zipcode,$date,$user_ip,$activation_code,$community_type );

	if (empty($err)) {
		
		//if the registration is successful then get the city and state name using zipcode and update the table
		echo "<h3>Registration is successfull!. You may now <a href=\"".BASE."/users/activate.php\">Activate </a> your account.</h3>";

		$meta_title = "Registration successful!";
		echo "<script>
			get_city_state('{$zipcode}');
		</script>";
	}
}

return_meta($meta_title);
?>
</head>

<body>
	<div id="header">
	<?php
	include_once ('includes/header.inc.php');
	?>
		<div class="login_holder">
			<?php include_once 'login.php'; ?>
		</div>
	</div>
	
	<!-- Beginning of error display section -->
	<div class="login_event_section">
		
	</div>
	<!-- End of error display section -->
	
	<div class="page_content_holder">
		<!--- Begining of registration section -->
		<div class="registration_section">
			<?php
			//Show message if isset
			if(isset($msg)) {
					echo '<div class="success">'.$msg.'</div>';
			}
			//Show error message if isset

			if(!empty($err)) {
					echo '<div class="err">';

					foreach($err as $e) {
							echo $e.'<br />';
					}

					echo '</div>';
			}
			?>
			<h1>Register</h1>
			
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="register_form">
				<label for="firstname">First</label>
				<input class="account" type="text" name="firstname" title="Please input your first name" required="required">
				
				<label for="lastname">Last</label>
				<input class="account" type="text" name="lastname">

				<label> Zip Code:</label> 
				<input class="account" id ="zipcode" title="Your zipcode is used to pull the right events and chef details for you." type="number" name="zipcode">

				<label> Email: </label>
				<input class="account" type="email" name="email" title="Email is required to verify your account" required="required">

				<label> Username: </label> 
				<input class="account" id="username" type="text" name="username" required="required">

				<label> Password: </label> 
				<input class="account" type="password" name="pass1" required="required">

				<label>Confirm Password: </label> 
				<input class="account" type="password" name="pass2" required="required">
				
				<input class="account"  name="pass2" required="required">
				
				<br>
				<button type="submit" name="register">Create My Account</button>
			</form>
		</div> 
		<!--- End of registration section -->
		
		<!-- Beginning of information section -->
		<div class="information_section">
			<h1>Welcome!</h1>
			<!-- Need to select a picture to display on this section. Use class "event_image" -->
			<p>Welcome to the Community Connect site. On this site, you can find a chef who prepares traditional Havyaka foods in your area, participate in Havyaka community events, or sign up to prepare foods or create events.</p>
		</div>
		<!-- End of information section -->
	
		<!-- Beginning of public event display section -->
		<div class="public_event_section">
			<h1 style="margin-left:7em;">Public Events</h1>
			<div class="public_event_refresh">
				<?php include 'public_event.php';?>
			</div> 
		</div> 
		<!-- End of public event display section -->
		
	</div>
	<div>
		<?php include('includes/footer.inc.php'); ?>
	</div>

</body>
</html>