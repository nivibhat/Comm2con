<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US">
<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.9.1.js"></script>
<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script src="includes/js/jquery_custom_flip.js"></script>

<?php
require_once 'includes/constants/sql_constants.php';
secure_page();
?>

<script>
// function to delete a food from the food bucket
function delete_food(handler){
	var food_id = $(handler).attr('rel');
	var chef_id = $(handler).attr('rel1');
	var datastring = "food_id=" +food_id+ "&chef_id=" +chef_id;
	
	// ajax call to delete the food from the database and hide the deleted row from the table
	$.ajax({
		type: "POST",
		url: "<?php echo BASE; ?>/includes/ajax_functions/profile_interactions.php?cmd=delete_food",
		data: datastring,
		success:function(response) {
			var results = JSON.parse(response);
			
			$(handler).closest('tr').hide('slow');
			$('.success').fadeIn(2000).show().html('Deleted Successfully!').fadeOut(6000);
			$('.error').fadeOut(2000).hide();
		}
	});
	return false;
}

// this function is called from the update food button that is on each row of foods
function update_food(handler) {
	var food_id = $(handler).attr('rel');
	var chef_id = $(handler).attr('rel1');
	var food_to_update = $('#' + food_id);
	
	// gets the description of the food by finding the parent table cell for the button clicked, then finding the textarea within the parent that contains the description
	var food_description = $(handler).parent('td').find('textarea').val();
	
	// if the food description is null, an error displays
	if(food_description == '') {
		$('.error').fadeIn(400).show().html('Please enter the food description.');
	}
	// else calls AJAX function to update the food in the database
	else {
		var datastring = "food_description=" +food_description+ "&food_id=" +food_id+ "&chef_id="+chef_id;
		
		// AJAX444 call to update the details about the food and visually indicate the update was successful
		$.ajax({
			type: "POST",
			url: "<?php echo BASE; ?>/includes/ajax_functions/profile_interactions.php?cmd=update_food",
			data: datastring,
			success:function(response) {
				var results = JSON.parse(response);
				
				$('.success').fadeIn(2000).show().html('updated Successfully!').fadeOut(6000); //Show, then hide success msg
				$('.error').fadeOut(2000).hide();
				
				food_to_update.closest('table').effect('shake', { direction: "down", distance: "4", times: "2"});
			}
		});
	}
	return false;
}


// Makes it so that any delete food buttons that are clicked will call the delete_food function. 
// The ".on" function is used because delete_food buttons are generated dynamically after page load
$(document).on('click', '.delete_food', function() {delete_food(this)});
$(document).on('click', '.update_food', function() {update_food(this)});

$(function(){	
	// button that calls an Ajax request to save the profile information
	$('#save_profile_button').click(function() {
		var datastring = $('#update_profile_form').serialize();
		
		// checks to see if the public checkbox is checked or not
		if ($('#get_public').prop('checked')){
			datastring += "&public_info=yes";
		}
		else{
			datastring += "&public_info=no";
		}
		
		// AJAX call to update user profile in the database and shows success or error
		$.ajax({
			type: "POST",
			url: "<?php echo BASE; ?>/includes/ajax_functions/profile_interactions.php?cmd=update_user",
			data: datastring,
			success: function(response) {
				var results = JSON.parse(response);
				var status = results.success;
				var message = results.message;
				
				if(status === true) {
					$('.success').fadeIn(2000).show().html(message).fadeOut(6000); //Show, then hide success msg
					$('.error').fadeOut(2000).hide(); //If showing error, fade out
					$('#user_profile_div').effect('shake', { direction: "down", distance: "4", times: "2"})
				}
				else {
					$('.error').fadeIn(2000).show().html(message).fadeOut(6000); //Show, then hide success msg
					$('.success').fadeOut(2000).hide();
				}
			}
		});
	});
	
	// Save the chef updates when the save button is clicked
	$('#save_chef_updates').click(function(){
		var datastring = $('#chef_profile_form').serialize();
		
		// the series of if statements below set the values from checkboxes in the chef profile card
		if ($('#pickup').prop('checked')){
			datastring += "&pickup=yes";
		}
		else{
			datastring += "&pickup=no";
		}
		
		if ($('#offline').prop('checked')){
			datastring += "&offline=yes";
		}
		else{
			datastring += "&offline=no";
		}
		
		if ($('#delivery').prop('checked')){
			datastring += "&delivery=yes";
		}
		else{
			datastring += "&delivery=no";
		}
		
		// AJAX call to update the chef in the database and indicate success or failure
		$.ajax({
			type: "POST",
			url: "<?php echo BASE; ?>/includes/ajax_functions/profile_interactions.php?cmd=update_chef",
			data: datastring,
			success: function(response) {
				var results = JSON.parse(response);
				var status = results['success'];
				var message = results['message'];
				
				if(status === true) {
					$('.success').fadeIn(2000).show().html(message).fadeOut(6000); //Show, then hide success msg
					$('.error').fadeOut(2000).hide(); //If showing error, fade out
					$('#chef_profile').effect('shake', { direction: "down", distance: "4", times: "2"})
				}
				else {
					$('.error').fadeIn(2000).show().html(message).fadeOut(6000); //Show, then hide success msg
					$('.success').fadeOut(2000).hide();
				}
			}
		});
	});
	
	// button to open the new food dialog form
	$( "#add_new_food_form" ).dialog({
		autoOpen: false,
		height: 500,
		width: 650,
		modal: true,
		buttons: {
			"Add food": function() {
				var formData = new FormData($(this)[0]);
				add_new_food(formData);
				$(this).dialog( "close" );
			},
			Cancel: function() {
				$(this).dialog("close");
			}
		},
		close: function() {
			$(this).dialog("close");
		}
	});
	
	// opens the add new food dialog when the new food link is clicked
	$("#request_new_food_link").click(function() {
		$("#add_new_food_form").dialog("open");
	});
	
	// on the new food dialog, clicking to add the food calls an ajax request to add the selected food to the table
	$("#add_selected_food").click(function(){
		var e = document.getElementById("selected_food");
		var self = $(this);
		var food_id = e.options[e.selectedIndex].value;
		var chef_id = $(this).attr('rel1');
		var datastring = "food_id=" +food_id+ "&chef_id="+chef_id;
		var picture_folder = "<?php echo PICTURE_LOCATION; ?>";
		if (food_id == "default"){
			// show error
			console.log("food was left at default");
			return false;
		}
		
		// AJAX call to add a selected food to the user's foods, and add the food row to the table
		$.ajax({
			type: "POST",
			url: "<?php echo BASE; ?>/includes/ajax_functions/profile_interactions.php?cmd=add_selected_food",
			data: datastring,
			success:function (response) {
				var results = JSON.parse(response);
				$('#no_foods_message').hide();
				
				// on success, add the new food row to the table
				if (results.success){
					var new_html =	"<tr class='foods_table'>";
					new_html += "<td>";
					new_html += "<p>" + results.added_food.food_name + "</p>";
					new_html += "<img class='gridimg2' src='" + picture_folder + results.added_food.food_picture + "' />";
					new_html += "</td>";
					new_html += "<td class='foods_table'>";
					new_html += "<label for='food_description'>Food Description</label>";
					new_html += "<textarea style='width:20em; height: 5em;'  name='food_description'>" + results.added_food.food_description + "</textarea>";
					new_html += "<br>";
					new_html += "<button class='update_food' rel='" + results.added_food.food_id + "' rel1=" + results.added_food.chef_id + " id='update_food_" + results.added_food.food_id + "' >Update</button>";
					new_html += "<button type='button' name='delete_food' class ='delete_food' rel='" + results.added_food.food_id + "' rel1='" + results.added_food.chef_id + "'>Delete</button>";
					new_html += "</td>";
					new_html +=	"</tr>";
					
					// adds the new food at the top of the table
					$(new_html).hide().prependTo('#foods_table').fadeIn('slow');
					
					$('.success').fadeIn(2000).show().html('Added Successfully').fadeOut(6000); //Show, then hide success msg
					$('.error').fadeOut(2000).hide();
				}
				else {
					$('.error').fadeIn(2000).show().html('Failed to add food').fadeOut(6000); //Show, then hide success msg
					$('.success').fadeOut(2000).hide();
				}
			}
		});
		return false;
	});

	// function called by the add food dialog to insert the values into the database
	function add_new_food(formData) {
		$.ajax({
			url: "<?php echo BASE; ?>/includes/ajax_functions/profile_interactions.php?cmd=add_new_food",
			type: 'POST',
			data: formData,
			// do we need async?
			async: false,
			success: function (response) {
				var results = JSON.parse(response);
				
				$('.success').fadeIn(2000).show().html('Added Successfully!').fadeOut(6000); //Show, then hide success msg
				$('.error').fadeOut(2000).hide();
				
				// adds the new food to the dropdown selection
				$('#selected_food').append('<option value="' + results.new_food.food_id + '">' + results.new_food.food_name + '</option>');
				
				// selects the newly added food in the dropdown
				$('#selected_food').val(results.new_food.food_id);
			},
			cache: false,
			contentType: false,
			processData: false
		});
		return false;
	}

});
</script>

<?php
$user_id = $_SESSION['user_id'];
$msg = NULL;
$err=NULL;

$user_info = get_user_info($user_id);
$profile_pic = PICTURE_LOCATION . $user_info[0]['profile_picture'];
$profile_pic_loc = htmlspecialchars($profile_pic);

//Get the chef details of the logged in user if exists
$chef_info_ret = get_chef_details_logged_in_user($user_id);
$chef_info = array_filter($chef_info_ret);

if(!empty($chef_info)) {
	$chef_id =$chef_info[0]['chef_id'];
	$about_chef = $chef_info[0]['about_chef'];
	$contact_time_preference = $chef_info[0]['contact_time_preference'];
	$pickup_available = $chef_info[0]['pickup_available'];
	
	if($chef_id !=NULL){
		$food_chef = get_foods_of_chef($chef_id);
	}
}

// function to update the picture for events or foods
if($_POST and $_GET) {

	if ($_GET['cmd'] == 'add_picture'|| $_GET['cmd'] == 'add_food_picture'){
		$user_id = $user_id = $_SESSION['user_id'];
		
		if ($_FILES["file"]["error"] > 0) {
				echo "Error: " . $_FILES["file"]["error"] . "<br>";
		}
		else {
			$file_handler = $_FILES["file"];
			$picture = store_image($file_handler);
			$picture_loc = $picture;
			
			if($_GET['cmd'] == 'add_picture') {
				//call the update_user_info function defined in sql_constants.php
				$profile_update = update_user_info($user_id, NULL, NULL, NULL, NULL, $picture_loc);
				
				if($profile_update) {
					$msg="Picture updated successfully";
				} 
				else {
					$msg="Could not update this time, Please try again";
				}

			}
			elseif ($_GET['cmd'] == 'add_event_picture') {
				$event_id = $_POST['event_id'];
				//defined in sql_constants.php
				$event_update = update_event_picture($picture_loc,$event_id);
				
				if($event_update) {
					$msg="Food details updated successfully";
				} 
				else {
					$msg="Could not update this time, Please try again";
				}
			}
			elseif ($_GET['cmd'] == 'add_food_picture') {
				$food_id=$_POST['food_id'];
				$food_update = update_foods_of_chef(NULL,$food_id,NULL,NULL,$picture_loc);
				
				if($food_update) {
					$msg="Food details updated successfully";
				} 
				else {
					$msg="Could not update this time, Please try again";
				}
			}
		}
	}
}
?>

<head>
	<title>My Profile</title>
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
	<meta name="robots" content="index, follow" />
	<link rel="stylesheet" type="text/css" href="includes/styles/profile_styles.css"/>
	<link rel="stylesheet" type="text/css" href="includes/styles/style.css"/>
	<link rel="stylesheet" type="text/css" href="includes/styles/card_style.css"/>
</head>

<body>
<?php
include('includes/header.inc.php');
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
			<?php
			// success and error message sections
			if(isset($msg)) {
				echo '<div class="success" >'.$msg.'</div>';
			} 
			elseif (isset($err)) {
				echo '<div class="error">'.$err.'</div>';
			}
			?>
			<div class="dashboard_sub_section">
				<?php include('includes/subnavigation.inc.php'); ?>
			</div>
			
			<!-- Middle column start -->
			<span class="success" style="display:none;"></span>
			<span class="error" style="display:none;">Please enter some text</span>
			
			
			<!-- USER PROFILE START -->
			<div class="card flipper" id="user_profile_div">
				<div class="back">
					<div class="update_profile_left">
						<h2>Hello <?php echo $user_info[0]['first_name'];?>,</h2>
						<h3>Edit your profile here:</h3></br>
						<form id="update_profile_form" action="" method="post">
							<input style="display:none" type="text" name="user_id" value="<?php echo $user_id ?>">

							<label for="first_name">First Name:</label>
							<input type="text" id="get_first_name" name="first_name" value="<?php echo $user_info[0]['first_name'];?>">
							
							<label for="last_name">Last name:</label>
							<input type="text" id="get_last_name" name="last_name" value="<?php echo $user_info[0]['last_name'];?>">
							
							<label for="phone">Phone:</label>
							<input type="text" id="get_phone" name="phone" value="<?php echo $user_info[0]['phone'];?>">
							
							<label for="email">Email:</label>
							<input type="text" id="get_email" name="email" value="<?php echo $user_info[0]['email'];?>">
							
							<br>
							<input type="checkbox" id="get_public" name="public_info" value="public_info">Allow others to see my contact info
						</form>
					</div>
					
					<div class="update_profile_right">
						<p class="image_holder"><img class="card_image" src="<?php echo $profile_pic_loc;?>" /></p>
						<p>Upload a Picture</p>
						<form action="<?php echo $_SERVER['PHP_SELF']; ?>?cmd=add_picture" method="post" enctype="multipart/form-data">
							<input type="file" name="file" id="file" style="width: 13em;">
							<input type="submit" name="submit" value="Update picture">
						</form>
                                                <br><br></br><button type="button" id="save_profile_button">Save Changes</button>
					</div>
				</div>
			</div>
			<!-- USER PROFILE END -->
			
			
			<!-- CHEF CARD START -->
			<div class="card flipper" id="chef_profile">
			
				<!-- START OF CHEF PROFILE -->
				<div class="back">
					<div class="update_chef_top">
						<p class="card_name">Chef Profile</p>
						
						<form id="chef_profile_form" method="post">
						<input type='hidden' name='chef_id' value='<?php if(!empty($chef_info)) {echo $chef_info[0]['chef_id']; }?>' ></input>
						<input style="display:none" type="text" name="user_id" value="<?php echo $user_id ?>">
						<label for="about_chef">About yourself as a chef:</label> 
						<textarea style="width:400px; height: 100px;"  name="about_chef"><?php if(!empty($chef_info)) { echo $chef_info[0]['about_chef']; }?></textarea>
					</div>
						
					<div class="update_chef_bl">
						<label for="contact_hours">Contact Hours:</label> 
						<select name="contact_time_preference" id="contact_time_preference">
							<option value="morning" <?php if(!empty($chef_info)) { if($chef_info[0]['contact_time_preference'] == "morning") echo "selected";}?>>Morning</option>
							<option value="noon" <?php if(!empty($chef_info)) {if($chef_info[0]['contact_time_preference'] == "noon") echo "selected";}?>>Noon</option>
							<option value="evening" <?php if(!empty($chef_info)) {if($chef_info[0]['contact_time_preference'] == "evening") echo "selected";}?>>Evening</option>
							<option value="anytime" <?php if(!empty($chef_info)) {if($chef_info[0]['contact_time_preference'] == "evening") echo "selected";}?>>Any time</option>
						</select>
						
						<label for="contact_hours">Payments Accepted:</label> 
						<select name="payments_accepted" id="payments_accepted">
							<option value="cash" <?php if(!empty($chef_info)) { if($chef_info[0]['payments_accepted'] == "cash") echo "selected";}?>>Cash</option>
							<option value="check" <?php if(!empty($chef_info)) {if($chef_info[0]['payments_accepted'] == "Check") echo "selected";}?>>Check</option>
							<option value="cash or check" <?php if(!empty($chef_info)) { if($chef_info[0]['payments_accepted'] == "Cash or Check") echo "selected";}?>>Cash or Check</option>
							<option value="paypal" <?php if(!empty($chef_info)) {if($chef_info[0]['payments_accepted'] == "Paypal") echo "selected";}?>>Paypal</option>
							<option value="other" <?php if(!empty($chef_info)) { if($chef_info[0]['payments_accepted'] == "Other") echo "selected";}?>>Other</option>
						</select>
					</div>
					
					<div class="update_chef_br">
						<!-- marks these checkboxes as checked or unchecked based on what we find in the DB -->
						<input style="width:20px; height: 20px;" type="checkbox" id="pickup" <?php if(!empty($chef_info)) {if($chef_info[0]['pickup_available'] == "Yes") echo "checked"; else echo "unchecked";}?>>Offer pickup?</input><br>

						<input style="width:20px; height: 20px;" type="checkbox" id="offline" <?php if(!empty($chef_info)) {if($chef_info[0]['taking_offline_order'] == "Yes") echo "checked"; else echo "unchecked";}?>>Take offline orders?</input><br>

						<input style="width:20px; height: 20px;" type="checkbox" id="delivery" <?php if(!empty($chef_info)) { if($chef_info[0]['delivery_available'] == "Yes") echo "checked"; else echo "unchecked";}?>>Offer delivery?</input><br>
					</div>
						
						<button type="button" id="save_chef_updates" name="save_chef_updates" style="position: absolute; bottom: 1em; right: 10em;">Save</button>
						<button type="button" class="flip" style="position:absolute;bottom:1em;right:1em;">Food bucket</button>
					</form>
				</div>
				<!-- END OF CHEF PROFILE -->
				
				<?php
				//Get the chef details of the logged in user if exists
				$chef_info = get_chef_details_logged_in_user($user_id);
				$chef_info_filter = array_filter($chef_info);
				if(!empty($chef_info_filter)) {
					$chef_id =$chef_info[0]['chef_id'];

					//Get the foods that the chef is preparing.
					if($chef_id !=NULL){
						$food_chef = get_foods_of_chef($chef_id);
					}
				}

				$food_names = get_all_food_names();
				?>
				
				<!-- START OF FOOD BUCKET -->
				<div class="front">
				
					<!-- Div below is hidden if the chef has any foods prepared -->
					<div id="request_new_food_div" style="display:none;">
						<h3>Add a food to your profile. (There should be one, you started taking orders!)</h3>
						<form action="" id ="add_new_food_form" method="post" enctype="multipart/form-data">
							<fieldset>
								<input type="hidden" id="chef_id" name ="chef_id" value="<?php echo $chef_id;?>">
									Food Name: <input class="input_box" name="food_name" id="new_food_name" placeholder="Enter the food Name">
									Food description:<textarea name="food_description" id="new_food_description"></textarea>

									<h3> Add a colorful picture to your food!</h3>
									<input type="file" name="file" id="food_pic"><br>
							</fieldset>
						 </form>
					</div>
					
					<p>Food Bucket</p>
						<form action="" method="post">
							<div id="food_from_db">
								<select id ="selected_food" class="dropdown">
									<option selected value="default">Please select a food type</option>
									<?php
									foreach ($food_names as $current_food) {
									?>
										<option value="<?php echo $current_food['food_id'];?>" ><?php echo $current_food['food_name'];?></option>

									<?php 
									} 
									?>
								</select>
								<input type="button" name="add_selected_food" rel="<?php echo $current_food['food_id'];?>" rel1="<?php echo $chef_info[0]['chef_id'];?>" id="add_selected_food" value="Add this food to your bucket">
								<p id="request_new_food_link">Request a new food</p>
							</div>
						</form>
						
						<table id="foods_table" style="display: block;   width: 100%;   border-collapse: collapse;   border: solid 1px #D4D4D3;   max-height: 16em; overflow-y:auto;">
						<tbody>
						<?php 
						if(isset($food_chef)) { ?>
							<?php 
							// PHP code to print foods for the selected chef
							$foods_array = get_foods_by_chef($chef_id);
							
							if ($foods_array){
								foreach ($foods_array as $row_food) {
									
									$food_id = $row_food['food_id'];
									
									$food_picture = $row_food['food_picture'];
									$media_loc = htmlspecialchars($food_picture);
									$media_loc = PICTURE_LOCATION . $media_loc;
									?>
									
									<tr class="foods_table" style="border: solid 1px #D4D4D3;">
										<td>
											<p><?php echo $row_food['food_name']; ?></p>
											<img class="gridimg2" src="<?php echo $media_loc;?>" />
										</td>
										<td class="foods_table" id="food_<?php echo $row_food['food_id']; ?>">
											
											<label for="food_description">Food Description</label>
											<textarea style="width:20em; height: 5em;"  name="food_description"><?php echo $row_food['food_description']; ?></textarea>
											
											<br>
											<button class="update_food" rel="<?php echo $row_food['food_id'];?>" rel1=<?php echo $chef_info[0]['chef_id'];?> id="update_food_<?php echo $row_food['food_id'];?>" >Update</button>
											<button type="button" name="delete_food" class ="delete_food" rel="<?php echo $row_food['food_id'];?>" rel1="<?php echo $chef_info[0]['chef_id'];?>">Delete</button>
										</td>
										
										
									</tr>

							<?php
								}
							}
						}
						else { 
						?>
							<tr class="foods_table" id="no_foods_message">
								<td class="foods_table">You have not specified any foods.</td>
							</tr>
						<?php 
						}
						?>
						</tbody>
						</table>
					<button class="flip" style="position:absolute;bottom:1em;right:1em;">Back to Chef Profile</button>
				</div>
				<!-- END OF FOOD BUCKET -->
			</div>
		<!-- CHEF PROFILE END -->
		</div>
	<!-- Center column end -->
	</div>
</div>

<?php include('includes/footer.inc.php'); ?>

</body>
</html>