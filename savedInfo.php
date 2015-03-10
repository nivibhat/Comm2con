<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US">
<head>
	<script type="text/javascript" src="includes/js/jquery-1.10.2.js"></script>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
	<!-- to make google map work, http://maps.google.com/maps/api/js?sensor=false should be added -->
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
	<title>Saved Information</title>

	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
	<meta name="robots" content="index, follow" />
	<link rel="stylesheet" type="text/css" href="includes/styles/profile_styles.css"/>
	<link rel="stylesheet" type="text/css" href="includes/styles/style.css"/>
	<link rel="stylesheet" type="text/css" href="includes/styles/card_style.css"/>
	<link rel="stylesheet" type="text/css" href="includes/styles/saved_info_style.css"/>
	<script src="includes/js/jquery_custom_flip.js"></script>
</head>

<script>
 
$(function(){
	// ajax request to delete the saved information from the user profile
	$(".delete_saved_data").click( function() {
		var delete_id = $(this).attr('rel');
		var delete_type = $(this).attr('rel1');
		var datastring = "delete_id=" +delete_id+ "&delete_type="+delete_type;
		
		$.ajax({
			type: "POST",
			url: "<?php echo $_SERVER['PHP_SELF']; ?>?cmd=Delete_saved_data",
			data: datastring,
			success:function () {
				// dynamically removes the row from the table
				var tr_id = "tr_"+delete_type+"_"+delete_id;
				$("#"+tr_id+"").remove();
				
				$('.success').fadeIn(2000).show().html('deleted Successfully!').fadeOut(6000); //Show, then hide success msg
				$('.error').fadeOut(2000).hide();
			}
		});
		
		return false;
	});
});

</script>

<?php
require_once 'includes/constants/sql_constants.php';
secure_page();
$user_id = $_SESSION['user_id'];

$msg = NULL;
$err=NULL;

if($_POST and $_GET){
	//to delete the saved data
	if($_GET['cmd'] == 'Delete_saved_data') {
		$delete_type = $_POST['delete_type'];
		$delete_id = $_POST['delete_id'];
		
		//Call the delete_saved_data function, defined in sql_constants.php
		$delete_data = delete_saved_data($delete_id,$delete_type,$user_id);
	}
}
//Get the user saved events and chef details
$saved_events = get_saved_events($user_id);
$saved_chef = get_saved_chef($user_id);

?>

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
			} elseif (isset($err)) {
				echo '<div class="error">'.$err.'</div>';
			}
			?>
			<div class="dashboard_sub_section">
				<?php include('includes/subnavigation.inc.php'); ?>
			</div>
			
			<span class="success" style="display:none;"></span>
			<span class="error" style="display:none;">Please enter some text</span>
			
			<?php
			// If the user is saved any event or chef details, display them on to user's dashboard.
			// Check if the user has  saved any event details, he data pulled from the function 'get_saved_events' defined in sql_constants.php
			if(!empty($saved_events)) {
				?>
				<div class="card" id='saved_event_div'>
					<div class="front">
						<h2 style="margin-top: 1px;">&nbsp;&nbsp;Your saved event details:</h2>
						<table>
						<th>Event Details</th>
						<th>Contact detail</th>
						<th>Your action</th>
						
						<?php
						//Display each of the event details in individual card.
						foreach($saved_events as $r) {
						?>
							<tr id="tr_event_<?php echo $r['event_id']; ?>">
								<input type="hidden" id="<?php echo $r['event_id']; ?>" value=""></input>

								<td><b> <center clss="chef_event_name"><h2><?php echo $r['event_name']; ?></h2></center></b><center class="desc">"<?php echo $r['event_desc']; ?>"</center>

								<b>Date:</b> <?php echo $r['event_date']; ?></br>

								<b>Venue:</b><center> <?php echo $r['venue_name']; ?><?php echo $r['venue_address']; ?></br><?php echo $r['city']; ?> ,&nbsp;<?php echo $r['state']; ?> -<?php echo $r['zipcode']; ?>  </center> </td>

								<td><?php echo $r['first_name']; ?> &nbsp;<?php echo $r['last_name']; ?><br><?php echo $r['email']; ?></br><?php echo $r['phone']; ?>&nbsp;&nbsp;</td>

								<td><br></br><button class="delete_saved_data" rel="<?php echo $r['event_id']; ?>" rel1='event' id="delete_saved_event_"<?php echo $r['event_id']; ?>>Delete</button></td>

							</tr>
						<?php 
						}
						?>
						</table>
					</div>
				</div>
			<?php 
			}
			
		//Check if the user has saved any chef details : the data pulled from the function 'get_saved_chef' defined in sql_constants.php
		if(!empty($saved_chef)) {
		?>
			<div class="card" id='saved_chef_div' style="height:auto">
				<div class="front">
					<h2 style="margin-top: 1px;">&nbsp;&nbsp;Your Favorite Chef's details:</h2>
					<table>
						<th>Chef details</th>
						<th>Food Names</th>
						<th>Your action</th>

						<?php
						foreach($saved_chef as $r) {
							$food_det =  get_foods_of_chef($r['chef_id']);
							?>
							<tr id="tr_chef_<?php echo $r['chef_id'];?>">

								<input type="hidden" id="<?php echo $r['chef_id'];?>" value=""></input>

								<td>
									<b> <center clss="chef_event_name"><h2><?php echo $r['first_name']; ?>&nbsp;<?php echo $r['last_name']; ?></h2></center></b>

									<center class="desc">"<?php echo $r['about_chef']; ?>"</center> <b>Contact:</b>&nbsp;<?php echo $r['email']; ?> &nbsp;OR &nbsp;<?php echo $r['phone']; ?>

									<br><b>Address:</b> <?php echo $r['city']; ?> ,&nbsp;<?php echo $r['state']; ?> -<?php echo $r['zipcode']; ?>

									<br><center class="desc">Other Info:</center>

									Pickup Available?:&nbsp;<?php echo $r['pickup_available']; ?><br>Payments accepted:<?php echo $r['payments_accepted']; ?></br>

									Delivery Available?:&nbsp; <?php echo $r['delivery_available']; ?>
									</br>
								</td>
								
								<td class="food_names">
									<?php
									foreach($food_det as $current_food) { ?>
										<?php echo $current_food['food_name']; ?><br><br>
							<?php	}	?>
								</td>
								<td>
									<br></br><button class="delete_saved_data" rel="<?php echo $r['chef_id']; ?>" rel1='chef' id="delete_saved_chef_"<?php echo $r['chef_id']; ?>>Delete</button>
								</td>
							</tr>
						
				<?php	}	?>
					</table>
				</div>
			</div>
		<?php 
		}

		if(empty($saved_chef) && empty($saved_events)) {
		?>
			<h2>No events or Chef details are saved in your profile.</h2>
			<?php
		}
		?>
		</div>
	</div>
</div>

<?php include('includes/footer.inc.php'); ?>

</body>

</html>
