<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US">
<head>
	<title>Community Resource</title>
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
	<meta name="home" content="index, follow" />
	<link rel="stylesheet" type="text/css" href ="includes/styles/style.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="includes/styles/chef_style.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="includes/styles/event_style.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="includes/styles/card_style.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="includes/styles/event_style.css" media="screen" />
	<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
	<script src="includes/js/jquery_custom_flip.js"></script>
</head>
<?php
	require_once 'includes/constants/sql_constants.php';
	require_once 'includes/constants/card_print.php';
	secure_page();

	if (isset($_SESSION['homepage']))
		$_SESSION['homepage']++;
	else
		$_SESSION['homepage'] = 1;

	$user_id =  $_SESSION['user_id'];
	$hash_pass= crypt($passsalt,'connectcommunity1');
?>
<input style="display:none" type="text" id="user_id" value="<?php echo $user_id ?>">

<script>
 
$(document).ready(function() {
	var t = setInterval(function() {
		$("#carousal ul").animate({marginLeft:-480},1000,function() {
			$(this).find("li:last").after($(this).find("li:first"));
			$(this).css({marginLeft:0});
		});
	},5000);
});

$(function(){
	$("#information_dialog").dialog({
		autoOpen: true,
		height: 500,
		width: 650,
		modal: true
	});

	$(".save_chef").click(function() {
		var chef_id = $(this).attr('rel');
		var user_id = $('#user_id').val();
		var datastring = "chef_id=" + chef_id + "&user_id=" + user_id;

		$.ajax({
			type: "POST",
			url: "<?php echo BASE; ?>/includes/ajax_functions/chef_interactions.php?cmd=save_chef",
			data: datastring,
			success: function(response) {
				var results = JSON.parse(response);

				$('.success').fadeIn(2000).show().html('Chef details are saved in your profile!').fadeOut(6000); //Show, then hide success msg
				$('.error').fadeOut(2000).hide(); //If showing error, fade out
			}
		});

		return false;
	});

	$(".save_event").click(function() {
		var event_id = $(this).attr('rel');
		var user_id = $('#user_id').val();
		// alert(event_id);
		var datastring = "event_id="+event_id + "&user_id=" + user_id;

		$.ajax({
			type: "POST",
			url: "<?php echo BASE; ?>/includes/ajax_functions/event_interactions.php?cmd=save_event",
			data: datastring,
			success: function(response) {
				console.log(response);
				var results = JSON.parse(response);

				$('.success').fadeIn(2000).show().html('Event details are saved in your profile!').fadeOut(6000); //Show, then hide success msg
				$('.error').fadeOut(2000).hide(); //If showing error, fade out
			}
		});

		return false;
	});

	$('.card').show('slide', {direction: "up"}, 700);

	$(".attending_radio").change(function() {
		var event_id = $(this).attr('rel');
		if(this.checked) {
			var datastring = "attending=true&event_id="+event_id;

			$.ajax({
				type: "POST",
				url: "<?php echo BASE; ?>/includes/ajax_functions/event_interactions.php?cmd=attending",
				data: datastring,
				success: function(response) {
					$('.success').fadeIn(2000).show().html('Your attendence is counted!').fadeOut(6000); //Show, then hide success msg
					$('.error').fadeOut(2000).hide(); //If showing error, fade out
				}
			});

			return false;
		}
		else {
			var datastring = "attending=false&event_id="+event_id;

			$.ajax({
				type: "POST",
				url: "<?php echo BASE; ?>/includes/ajax_functions/event_interactions.php?cmd=attending",
				data: datastring,
				success: function(response) {
					$('.success').fadeIn(2000).show().html('Your attendence is counted!').fadeOut(6000); //Show, then hide success msg
					$('.error').fadeOut(2000).hide(); //If showing error, fade out
				}
			});

			return false;
		}
	});
});
</script>
<body>

<?php
include_once ('includes/header.inc.php');
include('includes/navigation.inc.php'); ?>

<?php
//check if the user is logged in for the first time, if so, display the information dialog box
$query = "SELECT num_logins from " . USERS . " WHERE user_id =" . $user_id;
$q = mysqli_query($link, $query) or die(mysqli_error($link));


list($num_login) = mysqli_fetch_row($q);

//if the user is logged for first 2 times or he is landing in the home page for the first time, will display the information dialog
//($num_login <= 2) && : add this to below if statement in the future.
if(($_SESSION['homepage'] == 1) && ($num_login == 1)) {
?>

<!-- Begin modal welcome message window -->
<div id ="information_dialog" title = "Welcome to Community Connect!">
	<p>
		<h3 style="color: darkmagenta;font-style: italic;">Find Local Events, chef OR Add your own event, become a chef. </h3>
		All related to your community!
	</p>
	<p>
		<center>This website is built on card UI interface.</center>
		<div class="card flipper" style="width:85%;height: 50%; margin-top: 1em;">
			<div class="back">
				<h3 style="color: darkmagenta;font-style: italic;">This is the sample card. </h3>
				<button class="flip">flip</button><br>
				<p>If you click on the "flip" button above, the card flips and displays the contents in the back of the card.</p>
			</div>
			<div class="front">
				<h3 style="color: darkmagenta;font-style: italic;">This the back of the card.</h3>
				<button class="flip">flip</button><br>
				<p>If you click on the "flip" button above, the card flips back and displays front of the card.</p>
			</div>
		</div>
	</p>
	Enjoy your stay in <h3>Community Connect!</h3>
</div>
<!-- End modal welcome message window -->

<?php }

return_meta("Home");
$msg = NULL;

?>
<body>

<div class="content leftmenu">
	<div class="colright">
		<div class="col1">
			<!-- Left Column start -->
			<?php include('includes/left_column.inc.php'); ?>
			<!-- Left Column end -->
		</div>
		
		<div class="col2">
		<!-- Middle Column start -->
			<div id="carousal">
				<ul>
				<?php
				//Display the pictures in the carousal in the home page.
				//defined in sql_constants.php ; fetches randomly 8 images.
				$results = fetch_food_event_picture();
				foreach ($results as $r) {
					$food_image = $r['food_picture'];
					$food_image_loc = htmlspecialchars($food_image);
					$food_image_loc = PICTURE_LOCATION . $food_image_loc;
				?>
				 <li> <img src="<?php echo $food_image_loc?>"></img></li>

				<?php } ?>
				</ul>
			</div>

			<!-- Middle Column start -->
			<?php
			// This section gets all chefs for the user's location, then prints them into a card
			// functions below are defined in sql_constants
			$chefs_list = get_localchef_details($user_id,2);

			if(!empty($chefs_list)) {
				?>
				<!--prints a card for each chef associated with a food type    -->

				<div class ="chef_holder">
				<h1 class="home_section_header">Chefs in your area <a class="more_link" href="localChefs.php">More Chefs >></a></h1>

					<?php
					foreach ($chefs_list as $chef) {
						// gets the chef info and loads it into an array
						$chef_info_array = get_chef_info($chef['chef_id']);

						// uses the chef info array to print cards
						print_chef_card($chef_info_array);
					} ?>
				</div>
			<?php }
			else {?>
				<div class ="chef_holder" style="display:none;">	</div>
			<?php
			}
			?>

			<div id="event_holder">
				<?php
				$results = retrieve_future_event($user_id,2);
				if(!empty($results)) { ?>
					<div stlye="position:relative">
					<h1 class="home_section_header">Events in your area <a style="" class="more_link" href="localEvents.php">More Events >></a></h1>

					</div>

					<?php
						$i =0;
						foreach ($results as $r) {
							print_event_card($r);
						}
					?>

					<span class="success" style="display:none;"></span>
					<span class="error" style="display:none;">Please enter some text</span>
				<?php
				}

				if(empty($results) && empty($chefs_list)) {
				?>
					<h2>No Local events or Chef found!. add one <a href="userProfile.php">here</a>.</h2>
				<?php
				}
				?>
			</div>
		</div>
		<!-- Middle Column end -->
	</div>
		<!-- for future reference Right column start
		<div class="col3">

		</div>
		-->
</div>
<?php include('includes/footer.inc.php'); ?>

</body>
</html>
